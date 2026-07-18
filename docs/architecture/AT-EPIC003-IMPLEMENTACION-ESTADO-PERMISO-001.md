# AT-EPIC003-IMPLEMENTACION-ESTADO-PERMISO-001

## 1. Identificación y alcance

- **Clasificación:** [ARQ] Evolución modelo identidad/autorización; [TEC] Diseño de implementación estado → permisos; [DOC] Documento técnico oficial EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **ADR aplicable:** ADR-002 — Evolución del modelo de identidad y participación académica.
- **Estado:** análisis técnico documental. No autoriza implementación, cambios de datos ni modificación de las fuentes de permisos existentes.

El objetivo es analizar una futura materialización de la decisión institucional aprobada: **el estado académico determina permisos**, considerando la matriz de estudiantes y el modelo parcial conocido para profesores, comité y administrador.

## 2. Decisión institucional de partida

```text
Alternativa A:

Estado académico determina permisos.
```

El modelo objetivo institucional es:

```text
Estado académico / Rol / Participación
                 ↓
       Grupo de permisos funcionales
                 ↓
        Autorización efectiva
```

La derivación de permisos no elimina las separaciones de ADR-002: estado académico, rol institucional, participación y permiso funcional son dimensiones distintas. La regla aprobada determina qué permisos corresponden a un estado; no prescribe aún una tabla, algoritmo, servicio, sincronización ni modelo de sesión.

## 3. Matrices consideradas

### 3.1 Estudiantes

| Estado académico | Grupo de permisos funcionales aprobado |
|---|---|
| Postulante | Mi perfil, Reglamento |
| Aceptado | Mi perfil, Datos académicos, Cursos (vista), Calendario, Reglamento |
| Matriculado | Mi perfil, Datos académicos, Cursos (vista), Calendario, Reglamento |
| Graduado | Mi perfil, Reglamento |
| Reprobado | Mi perfil, Reglamento |
| Retirado | Mi perfil, Reglamento |
| Eliminado | Sin acceso a intranet |

Aceptado y Matriculado comparten grupo de permisos. Eliminado no tiene acceso a la intranet.

### 3.2 Profesores, comité y administrador

| Condición conocida | Grupo de permisos considerado |
|---|---|
| Profesor registrado | Mi perfil, Reglamento |
| Profesor aceptado | Cursos, Mi perfil, Reglamento, Calendario |
| Profesor inhabilitado | Sin acceso |
| Integrante de comité | Suma capacidades al permiso de profesor; detalle funcional pendiente |
| Administrador | Acceso administrativo completo |

El comité es una participación adicional de un profesor y solo el administrador gestiona sus integrantes. Permanecen pendientes el detalle de sus permisos funcionales y la separación del permiso histórico 3.

## 4. Modelo técnico actual confirmado

| Información | Dónde reside | Quién la modifica técnicamente | Uso actual confirmado |
|---|---|---|---|
| Estado académico de estudiante | `estudiante.tipo_est`, con catálogo `tipo_estudiante` | `Estudiante::editarTipoEst()` invocada por `ajax/estudiante.php` en `update-permiso-tipo-est` | Perfil y consultas de estudiantes; no es consultado por el login revisado. |
| Permiso operativo | Filas de `permiso_login` vinculadas a `login` | Alta de usuarios y métodos heredados de `Usuario`/`Admin` | Fuente de `Login::validarPermiso()`. |
| Sesión | `$_SESSION` | `ajax/login.php` al autenticar | Claves históricas consumidas por módulos: `admin`, `comite`, `aceptado`, `docente` y `estudiante`. |
| Consulta centralizada disponible | `src/Security/Authorization.php` | Módulos que llaman `Authorization::hasAny()` | Consulta las claves de sesión existentes; no deriva ni persiste permisos. |

El flujo observado es:

```text
Cambio de estado
       ↓
ajax/estudiante.php: update-permiso-tipo-est
       ↓
Estudiante::editarTipoEst()
       ↓
UPDATE estudiante.tipo_est

[sin modificación confirmada de permiso_login]

Inicio de sesión
       ↓
Login::validarPermiso()
       ↓
permiso_login
       ↓
ajax/login.php
       ↓
$_SESSION
       ↓
Autorización por módulo / Authorization
```

`Login::validarPermiso()` consulta `login` junto a `permiso_login`; no consulta `estudiante` ni `tipo_estudiante`. El login convierte los identificadores 1 a 5 en las claves de sesión anteriores. El permiso histórico 3 genera `$_SESSION['aceptado']`, pero también está asociado históricamente a docente; por ello no permite inferir de forma unívoca un estado académico.

Los cambios de estado tampoco recalculan sesiones ya abiertas. La autorización del endpoint de cambio de estado requiere revisión específica: en la lectura realizada no se observa una validación equivalente dentro de ese caso del endpoint.

## 5. Alternativas de implementación futura

Ninguna de las alternativas siguientes se adopta ni se implementa mediante este análisis.

### A1 — Sincronización al cambiar estado

```text
Cambio de estado académico
       ↓
Resolver grupo de permisos
       ↓
Actualizar permiso_login
       ↓
Próximo login genera sesión
```

**Ventajas:** reduce la divergencia persistente entre estado y permisos; mantiene al login con su fuente actual; permite una adopción localizada en los flujos de transición.

**Riesgos e impacto:** requiere definir reglas de agregación y retiro para no borrar permisos independientes (docencia, comité o administración); `permiso_login` no representa aún los permisos funcionales de la matriz; el permiso 3 es ambiguo; exige transacciones, auditoría, reversión y manejo de sesiones abiertas. Una actualización directa masiva aumentaría el riesgo de pérdida o ampliación indebida de acceso.

**Migración de existentes:** requiere inventario previo de estado, permisos actuales y perfiles; simulación no mutante del resultado; tratamiento de excepciones; y aplicación por lotes reversibles solo tras validación.

### A2 — Cálculo dinámico al login

```text
Login
       ↓
Consultar estado / rol / participación
       ↓
Resolver permisos efectivos
       ↓
Generar sesión
```

**Ventajas:** la sesión nueva refleja la regla vigente sin mutar `permiso_login`; reduce la necesidad de reconciliar permisos derivados persistidos; facilita comprobar la matriz antes de una migración física.

**Riesgos e impacto:** modifica un punto transversal y sensible (`ajax/login.php` y `src/Model/Login.php`); los permisos funcionales deben tener una representación de sesión compatible con módulos heredados; puede producir diferencias entre módulos no migrados; requiere definir precedencia entre estado, rol y comité, además de comportamiento ante datos incompletos.

**Compatibilidad:** solo sería segura con una capa de compatibilidad explícita que preserve las claves de sesión esperadas durante la transición. No debe reutilizar el permiso 3 como sinónimo del estado Aceptado.

### A3 — Capa de autorización derivada

```text
Estado / Rol / Participación
       ↓
Servicio o capa de autorización
       ↓
Permisos funcionales efectivos
       ↓
Módulos consumidores
```

**Alineación:** es la alternativa más coherente con ADR-002 y con la evolución prevista por `Authorization`: conserva las fuentes como dimensiones separadas y centraliza la decisión funcional.

**Complejidad:** exige catálogo de capacidades, matriz completa, reglas de composición, resolución de conflictos, trazabilidad, pruebas por módulo y coexistencia temporal con `$_SESSION` y `permiso_login`.

**Migración incremental:** puede iniciarse como evaluación derivada y no mutante, comparando el resultado con la autorización heredada. Después, los módulos se migrarían individualmente a la capa, con validación y reversión. La persistencia o retiro posterior de permisos heredados sería una etapa separada.

## 6. Impacto técnico a evaluar en una fase posterior

| Componente | Impacto potencial |
|---|---|
| `ajax/login.php` | Materializa las claves de sesión desde identificadores de permiso; cambiar su fuente afectaría todas las sesiones nuevas. |
| `src/Model/Login.php` | Su consulta actual depende de `permiso_login`; una derivación al login requeriría consultas y reglas adicionales. |
| `permiso_login` | Es la fuente heredada efectiva y contiene asociaciones históricas acumulables; no puede reinterpretarse como matriz funcional sin reconciliación. |
| `estudiante` / `tipo_estudiante` | Aporta estado académico, pero no contiene por sí solo roles, comité ni permisos funcionales. |
| Sesiones | No se actualizan al cambiar estado; una futura estrategia debe definir expiración, reautenticación o invalidación controlada. |
| `Authorization.php` | Hoy consume sesión; es el punto natural para evolucionar el consumo, pero no debe absorber reglas sin contrato funcional y pruebas. |
| Módulos existentes | Cualquier módulo que lea claves de sesión o use `Authorization` puede observar cambios de acceso; se requiere inventario y validación por módulo. |

## 7. Consideraciones de migración

Antes de cualquier cambio se requiere un inventario de solo lectura que correlacione, por cuenta:

- estado académico, profesor y participación de comité;
- filas vigentes en `permiso_login`;
- permisos derivados esperados según la matriz;
- módulos y claves de sesión de los que depende cada cuenta;
- excepciones institucionales aprobadas.

Casos críticos:

- **Usuarios existentes:** no asumir que los permisos históricos reflejan el estado vigente.
- **Permiso 3:** no convertirlo ni eliminarlo hasta definir su separación para estudiantes aceptados y docentes.
- **Estudiantes activos:** Aceptado y Matriculado requieren el mismo resultado funcional, sin deducir una equivalencia técnica con un identificador heredado.
- **Docentes y comité:** los permisos del estado no deben eliminar capacidades acumulativas de docencia, comité o administración.
- **Eliminados e inhabilitados:** el acceso debe bloquearse en la fuente efectiva y las sesiones activas deben tratarse de forma explícita; no basta modificar el estado almacenado.

## 8. Riesgos principales

| Riesgo | Manifestación | Mitigación futura requerida |
|---|---|---|
| Pérdida de acceso legítimo | Retiro de permisos acumulativos al aplicar una transición de estado | Reglas de composición, simulación y reversión. |
| Asignación incorrecta | Mapeo técnico equivocado entre estado y claves/sesiones | Catálogo funcional independiente de IDs históricos y pruebas por capacidad. |
| Duplicidad de permisos | Inserciones reiteradas al sincronizar transiciones | Restricciones, operación idempotente e inventario previo. |
| Inconsistencias históricas | `tipo_est` y `permiso_login` no coinciden | Reporte de diferencias, resolución de excepciones y migración gradual. |
| Cambio masivo | Aplicación simultánea a todas las cuentas | Piloto acotado, lotes reversibles y monitoreo. |
| Sesión obsoleta | Una sesión conserva acceso después de cambio a Eliminado/Inhabilitado | Política explícita de invalidación o reautenticación. |

## 9. Estrategia incremental recomendada

Se recomienda orientar una futura implementación hacia A3, usando A2 únicamente como posible mecanismo transitorio de compatibilidad. A1 no debe iniciarse hasta que la representación de permisos funcionales y las reglas de composición estén validadas.

```text
Fase 1 — Consolidar matriz funcional completa
Fase 2 — Definir capacidades, composición y tratamiento del permiso 3
Fase 3 — Construir evaluación derivada no mutante y comparar resultados
Fase 4 — Migrar módulos de forma incremental a la capa de autorización
Fase 5 — Decidir y ejecutar, con autorización separada, la transición de permiso_login
```

Cada fase requiere alcance aprobado, pruebas, criterios de reversión y trazabilidad. No corresponde crear una TASK ni ejecutar esas fases con este AT.

## 10. Hallazgos confirmados, pendientes y fuentes

### Hallazgos confirmados

- `estudiante.tipo_est` almacena el estado académico; `permiso_login` almacena los permisos que hoy consume el login.
- El cambio `update-permiso-tipo-est` actualiza solo `estudiante.tipo_est`.
- `ajax/login.php` genera las claves de sesión a partir de `permiso_login`.
- El permiso 3 produce la clave `aceptado` y posee asociación técnica histórica compartida con docente.
- `Authorization` consume claves de sesión y no calcula permisos derivados.

### Pendientes

- Matriz funcional detallada para comité.
- Separación y transición del permiso histórico 3.
- Reglas de composición entre estado, rol, comité y administrador.
- Política de sesiones ante cambios de estado y casos de eliminación/inhabilitación.
- Inventario de módulos y de cuentas afectadas antes de una migración.

### Fuentes utilizadas

- [Manual Maestro](../MANUAL_MAESTRO.md)
- [Roadmap](../roadmap/ROADMAP.md)
- [ADR-002](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md)
- [AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md)
- [ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](../governance/ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md)
- [ACTA-SOLICITUD-RESOLUCION-EPIC003-ESTADO-PERMISO-001](../governance/ACTA-SOLICITUD-RESOLUCION-EPIC003-ESTADO-PERMISO-001.md)
- Evidencia técnica: `ajax/login.php`, `src/Model/Login.php`, `ajax/estudiante.php`, `src/Model/Estudiante.php`, `src/Security/Authorization.php` y módulos consumidores revisados.

## 11. Restricciones cumplidas

```text
Sin cambios de código.
Sin implementación.
Sin cambios ADR.
Sin cambios Roadmap.
Sin cambios Manual Maestro.
Sin commit.
```

Siguiente paso posterior:

```text
Revisión técnica: AT-EPIC003-IMPLEMENTACION-ESTADO-PERMISO-001
```
