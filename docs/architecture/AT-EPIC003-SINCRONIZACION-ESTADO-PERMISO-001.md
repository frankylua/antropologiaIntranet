# AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001

## Análisis técnico documental: sincronización entre estado académico y permiso efectivo

## 1. Identificación y alcance

- **Clasificación:** [ARQ] evolución de identidad y autorización; [TEC] análisis de sincronización estado/permisos; [DOC] documento técnico oficial.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **ADR aplicable:** ADR-002 — Evolución del modelo de identidad y participación académica.
- **Estado:** análisis técnico documental. No autoriza implementación, cambio de reglas ni modificación de datos.

El análisis determina la relación actual entre `estudiante.tipo_est`, `permiso_login`, sesión y autorización observable. Distingue hechos del código legado de las reglas institucionales pendientes; no presupone que el cambio de estado deba sincronizar permisos de manera automática.

## 2. Conclusión ejecutiva

Existe una separación técnica confirmada entre el estado académico y el permiso efectivo de acceso:

```text
estudiante.tipo_est
  └─ cambio académico

permiso_login
  ↓
ajax/login.php
  ↓
$_SESSION
  ↓
autorización de cada módulo
```

El login no consulta `estudiante` ni `tipo_estudiante`; crea claves de sesión desde las filas de `permiso_login`. A su vez, `update-permiso-tipo-est` actualiza solo `estudiante.tipo_est`. Por tanto, no existe sincronización automática confirmada entre ambos flujos y un estudiante puede quedar, por ejemplo, como aceptado académicamente sin permiso 3 ni `$_SESSION['aceptado']` en una sesión nueva.

`$_SESSION['aceptado']` representa la materialización del permiso histórico 3, no una lectura directa ni una representación inequívoca del estado académico: el propio login lo describe como compartido por docente o estudiante aceptado.

## 3. Modelo actual

### 3.1 Persistencia y responsables técnicos

| Dimensión | Almacenamiento / flujo | Modificación observada | Alcance confirmado |
| --- | --- | --- | --- |
| Estado académico | `estudiante.tipo_est`, relacionado con `tipo_estudiante` | `Estudiante::editarTipoEst()` desde `update-permiso-tipo-est` | Modifica solo el estado académico del estudiante. |
| Permisos de acceso | Filas de `permiso_login` asociadas a `login` | Alta de estudiante y métodos heredados de permisos | Fuente operativa para el login; no se recalcula al cambiar `tipo_est` en el flujo revisado. |
| Sesión | `$_SESSION` | `ajax/login.php`, al iniciar sesión | Traduce permisos existentes a claves históricas de sesión. |
| Autorización efectiva | Condiciones de sesión de cada módulo; migración gradual a `Authorization` | Módulos consumidores | La Feature conserva inicialmente permisos y sesiones vigentes. |

Los actores institucionales habilitados para cambiar estados o administrar permisos no se pueden afirmar de manera concluyente con las fuentes autorizadas. Técnicamente, el formulario de estudiante muestra el selector de estado para sesión con `admin` o `comite`; sin embargo, el endpoint `ajax/estudiante.php` revisado no aplica una comprobación equivalente en el caso `update-permiso-tipo-est`. La autorización efectiva de esa llamada requiere revisión específica antes de atribuirla a un actor institucional.

### 3.2 Flujo observado

```text
Estudiante
  ↓
tipo_est / estado académico
  ↓
update-permiso-tipo-est
  ↓
UPDATE estudiante.tipo_est

[sin operación confirmada sobre permiso_login]

permiso_login
  ↓
ajax/login.php
  ↓
$_SESSION
  ↓
autorización efectiva
```

## 4. Flujo de login y significado de la sesión

`Login::validarPermiso()` consulta `login` unido a `permiso_login`; no incorpora el perfil `estudiante` ni `tipo_est`. `ajax/login.php` recorre las filas resultantes y materializa las siguientes claves:

| `id_permiso` | Clave creada |
| --- | --- |
| 1 | `$_SESSION['admin']` |
| 2 | `$_SESSION['comite']` |
| 3 | `$_SESSION['aceptado']` |
| 4 | `$_SESSION['docente']` |
| 5 | `$_SESSION['estudiante']` |

Una cuenta puede acumular claves porque el flujo procesa todas sus filas. En particular, `aceptado` significa técnicamente «la cuenta posee permiso 3»; no equivale por sí mismo al estado académico aceptado y tampoco identifica de forma exclusiva a un estudiante, dado el uso histórico compartido con docentes. La sesión se actualiza al inicio de sesión desde los permisos persistidos; no deriva permisos desde un cambio de `tipo_est`.

## 5. Flujo de cambio de estado

El caso `update-permiso-tipo-est` de `ajax/estudiante.php` invoca `Estudiante::editarTipoEst($id_usu, $tipo_est)`, cuyo efecto es:

```text
UPDATE estudiante SET tipo_est = ... WHERE usuario = ...
```

No se observan en ese caso llamadas a inserción, eliminación, conservación selectiva o reconstrucción de `permiso_login`. El nombre del caso incluye «permiso», pero su efecto confirmado es solo académico. La lógica de edición de permisos que aparece comentada en otro flujo no constituye comportamiento ejecutable ni evidencia de sincronización actual.

Esto deja posibles discrepancias persistentes en ambos sentidos: estado cambiado sin ajuste de acceso, o permiso previo conservado tras un cambio de estado. Si existía una sesión antes del cambio, sus claves tampoco son recalculadas por este endpoint.

## 6. Matriz conceptual: estado frente a permisos

La matriz no asigna reglas ausentes. EPIC-003 confirma los estados, mientras ADR-002 y la Feature establecen que estado académico, rol y permiso funcional deben mantenerse separados.

| Estado académico | Permiso esperado | Estado actual |
| --- | --- | --- |
| Postulante | No definido en las fuentes autorizadas. | No existe mapeo automático confirmado desde `tipo_est`. |
| Aceptado | Acceso funcional debe provenir de regla institucional; la equivalencia con permiso 3 no es unívoca. | Puede no recibir `aceptado` si falta permiso 3. |
| Matriculado | No definido en las fuentes autorizadas. | No existe mapeo automático confirmado desde `tipo_est`. |
| Graduado | No definido en las fuentes autorizadas. | No existe mapeo automático confirmado desde `tipo_est`. |
| Retirado | No definido en las fuentes autorizadas. | No existe mapeo automático confirmado desde `tipo_est`. |
| Eliminado | No definido en las fuentes autorizadas. | No existe mapeo automático confirmado desde `tipo_est`. |
| Reprobado | No definido en las fuentes autorizadas. | No existe mapeo automático confirmado desde `tipo_est`. |

## 7. Alternativas técnicas, sin decisión

### Alternativa A — Sincronización automática

```text
Cambio de estado académico
  ↓
Actualización de permisos
```

**Ventajas:** reduce la divergencia entre una transición definida y los permisos que esa política determine; permite que una sesión posterior refleje el resultado sin correcciones manuales separadas.

**Riesgos:** presupone una matriz institucional completa; puede borrar o sustituir permisos acumulativos independientes; el permiso 3 compartido vuelve riesgosa una equivalencia directa; exige tratar cambios compuestos y sesiones existentes con consistencia. La Feature vigente excluye automatizar estado → permiso en su etapa actual.

### Alternativa B — Mantener separación con asignación manual

```text
Estado académico      Permiso asignado manualmente
       \                 /
        \-- sin derivación automática --/
```

**Ventajas:** respeta de inmediato la separación de ADR-002; preserva compatibilidad con `permiso_login`, sesiones y roles acumulativos; evita codificar reglas institucionales no confirmadas.

**Riesgos:** requiere operación coordinada y trazable; mantiene la posibilidad de divergencias, pérdida de acceso legítimo o permisos conservados indebidamente; la corrección de una dimensión puede ocultar la inconsistencia de la otra.

### Alternativa C — Modelo futuro de autorización

```text
Estado / Rol / Participación
  ↓
Reglas de autorización
  ↓
Permisos funcionales efectivos
```

**Compatibilidad:** es coherente con ADR-002 y con la dirección de la Feature, que reconoce esas dimensiones sin confundirlas. La Feature actual solo centraliza el consumo de permisos vigentes; no calcula ni persiste permisos desde ellas.

**Complejidad:** requiere reglas institucionales explícitas por capacidad, tratamiento de acumulación y compatibilidad temporal con permisos/sesiones heredados, estrategia de transición y validación por módulo. No queda autorizado por este AT.

## 8. Impacto y riesgos

| Área | Impacto o riesgo |
| --- | --- |
| Login | Cambiar la fuente o la traducción de permisos afectaría todas las claves de sesión y módulos consumidores. |
| Permisos | Reinterpretar el permiso 3 puede mezclar acceso de docentes y estudiantes aceptados. |
| Estudiantes existentes | Hay riesgo de cuentas con estado y permiso no alineados; una migración masiva requeriría datos y reglas verificadas. |
| Módulos afectados | Todo módulo que consume `aceptado`, `estudiante`, `docente`, `admin` o `comite` puede observar cambios; Cursos ya evidencia la dependencia. |
| Compatibilidad histórica | Permisos acumulativos y sesiones existentes deben preservarse o migrarse de forma controlada. |

Riesgos principales: pérdida de acceso legítimo; asignación incorrecta o ampliación de permisos; automatización excesiva sin política aprobada; contradicción con reglas institucionales; y migración masiva que produzca cambios irreversibles o parciales.

## 9. Recomendación técnica y próximos pasos

1. Este hallazgo requiere una **Feature nueva o una extensión explícita de EPIC-003** antes de implementar sincronización o derivación de permisos, pues excede la centralización compatible con la fuente actual.
2. Debe obtenerse validación institucional para la matriz estado → reglas de acceso, la relación del permiso histórico 3 con estudiantes y docentes, y la preservación de roles acumulativos.
3. Luego corresponde un análisis de transición acotado: inventario no mutante de `tipo_est`, `permiso_login` y claves de sesión; definición de capacidades; y estrategia reversible para cuentas existentes.
4. No corresponde crear una TASK en este documento ni modificar login, sesiones, `permiso_login`, código o datos.

## 10. Hallazgos, hipótesis y fuentes

### Hallazgos confirmados

- El estado académico está almacenado en `estudiante.tipo_est` y los permisos operativos en `permiso_login`.
- El login crea sesión desde `permiso_login`, no desde el estado académico.
- El permiso 3 crea `$_SESSION['aceptado']` y su significado histórico es compartido entre docente y estudiante aceptado.
- `update-permiso-tipo-est` actualiza `tipo_est` sin sincronizar `permiso_login`.
- ADR-002 y la Feature vigente separan estado, rol, participación y permiso funcional; la automatización estado → permiso no está autorizada en la etapa actual.

### Hipótesis y validaciones pendientes

- La correlación real entre cada valor de estado y los permisos requeridos para cada capacidad.
- La composición de permisos y sesión de cada estudiante afectado en datos controlados.
- La política institucional para conservar, retirar o agregar permisos frente a cada transición.
- La autorización efectiva del endpoint de cambio de estado y los actores institucionales que deben ejecutarlo.

### Fuentes utilizadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md)
- [TASK-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.md](../tasks/TASK-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.md)
- [TASK-EPIC003-AUTORIZACION-CURSOS-CONSULTA-PILOTO-001.md](../tasks/TASK-EPIC003-AUTORIZACION-CURSOS-CONSULTA-PILOTO-001.md)
- [AT-EPIC003-CURSOS-SESION-ACEPTADO-001.md](AT-EPIC003-CURSOS-SESION-ACEPTADO-001.md)
- [AT-EPIC003-CURSOS-VALIDACION-ACCESO-ACEPTADO-001.md](AT-EPIC003-CURSOS-VALIDACION-ACCESO-ACEPTADO-001.md)
- Evidencia técnica de lectura: `ajax/login.php`, `src/Model/Login.php`, `ajax/estudiante.php`, `src/Model/Estudiante.php` y `form-doc/scripts/estudiante.js`.

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
Revisión técnica AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001
```
