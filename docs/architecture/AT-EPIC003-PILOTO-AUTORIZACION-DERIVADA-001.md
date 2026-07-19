# AT-EPIC003-PILOTO-AUTORIZACION-DERIVADA-001

## Análisis del piloto de autorización derivada para Reglamento

## 1. Identificación y alcance

- **Clasificación:** [ARQ] Piloto capa autorización derivada; [TEC] Migración incremental autorización; [DOC] Documento técnico EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Feature asociada:** [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md).
- **Estado:** Piloto incremental ejecutado y validado. No constituye una solución global de autorización.

El objetivo es analizar cómo un módulo existente puede consumir una resolución de permisos derivada sin modificar inmediatamente el modelo legado. Este documento no define código, cambios de sesión, permisos técnicos ni una TASK.

## 2. Decisión institucional considerada

```text
Estado académico
Rol institucional
Participación
        ↓
Reglas de autorización
        ↓
Permisos funcionales
```

La resolución debe componer capacidades desde el contexto institucional, sin convertir una clave histórica de sesión en sinónimo de estado o rol.

## 3. Piloto seleccionado: Reglamento

Se recomienda Reglamento como primer piloto de autorización derivada por las siguientes razones:

- ya cuenta con una migración previa al punto de consulta `Authorization`;
- es un módulo de solo lectura;
- presenta bajo riesgo funcional;
- no ejecuta operaciones críticas;
- permite validar la coexistencia entre legado y resultado derivado sin modificar reglas de negocio.

El piloto es válido como etapa de evaluación de arquitectura. No autoriza modificar el módulo ni su control actual.

## 4. Estado actual de Reglamento

El módulo `form-doc/reglamento.php` consume `Authorization.php` y solicita las claves históricas `admin`, `comite` o `aceptado`. `Authorization::hasAny()` comprueba la presencia de esas claves en `$_SESSION`.

Flujo actual:

```text
permiso_login
        ↓
ajax/login.php
        ↓
$_SESSION: admin / comite / aceptado
        ↓
Authorization::hasAny()
        ↓
Reglamento
```

La fuente operativa sigue siendo `permiso_login`, materializada por `ajax/login.php` como sesión. Reglamento es compatible con `Authorization` existente, pero aún no consume un permiso funcional explícito de Reglamento ni una regla derivada.

## 5. Diseño conceptual del piloto

```text
Usuario autenticado
        ↓
Contexto usuario
  - estado académico
  - rol institucional
  - participación
        ↓
Resolución de permisos derivados
        ↓
Authorization
        ↓
Reglamento
```

La evaluación derivada propuesta para el piloto es de lectura: obtiene el contexto disponible, resuelve de forma conceptual el permiso funcional Reglamento y permite contrastarlo con el resultado heredado. No modifica `permiso_login`, `$_SESSION`, login, `Authorization.php` ni el módulo.

## 6. Alcance de evaluación funcional

La matriz aprobada permite esperar permiso Reglamento para estudiantes Postulante, Aceptado, Matriculado, Graduado, Reprobado y Retirado; no para Eliminado. Para profesores, el estado Registrado y Aceptado incluye Reglamento; Inhabilitado no tiene acceso. La composición de Comité suma sus capacidades a las del profesor.

| Actor o contexto | Resultado derivado esperado para Reglamento |
| --- | --- |
| Estudiante aceptado | Acceso Reglamento |
| Estudiante matriculado | Acceso Reglamento |
| Estudiante eliminado | Sin acceso |
| Profesor aceptado | Acceso Reglamento |
| Profesor + Comité | Acceso Reglamento |
| Usuario sin permisos | Sin acceso |

Los casos deben verificar tanto la decisión derivada esperada como su equivalencia o diferencia respecto del resultado de sesión vigente. Una diferencia no autoriza una corrección automática: debe registrarse para análisis institucional y técnico posterior.

## 7. Coexistencia con el legado

Durante la fase piloto se mantienen:

```text
permiso_login
        +
Authorization actual
        +
reglas derivadas de lectura
```

La estrategia es comparar y validar, no reemplazar. El comportamiento observable de Reglamento se mantiene mientras las fuentes históricas continúen siendo la fuente operativa para módulos existentes.

La coexistencia requiere identificar qué resultado utilizó la evaluación: derivado, histórico o diferencia pendiente. No se debe aplicar fallback silencioso que oculte inconsistencias entre estado, roles, participación y permisos históricos.

## 8. Validaciones y rollback conceptual

### Validaciones requeridas

- Casos funcionales de la matriz del piloto.
- Usuarios existentes con estados, roles y permisos históricos conocidos.
- Equivalencia entre acceso histórico y resultado derivado cuando corresponda.
- Registro de diferencias, en especial para cuentas asociadas al permiso histórico 3.
- Verificación de que el módulo permanezca solo de lectura y sin cambio de navegación o comportamiento observable.

### Rollback conceptual

El piloto debe ser reversible por diseño: si la evaluación derivada produce un resultado no validado o una divergencia relevante, Reglamento continúa sujeto a `Authorization` y a la fuente histórica vigente. La evaluación derivada se retira de la comparación sin alterar datos, login, sesión ni permisos existentes.

## 9. Riesgos

| Riesgo | Consideración del piloto |
| --- | --- |
| Diferencia entre permisos históricos y derivados | Puede revelar cuentas con estado o rol no alineado a `permiso_login`; se debe registrar, no corregir automáticamente. |
| Dependencia actual de sesión | `Authorization` aún consume claves de `$_SESSION`; el piloto no debe asumir que estas representan permisos funcionales. |
| Usuarios inconsistentes | Cuentas existentes pueden no reflejar la matriz aprobada. |
| Duplicidad temporal de reglas | La regla derivada y el control histórico pueden producir decisiones distintas durante la coexistencia. |
| Migración incompleta | Otros módulos pueden conservar controles directos aunque Reglamento evolucione primero. |

## 10. Recomendación técnica

Reglamento es válido como primer piloto porque ya utiliza el punto central `Authorization`, es de solo lectura y posee una regla de acceso acotada. El éxito del piloto se mide por:

- resolución derivada documentada para todos los casos definidos;
- ausencia de cambio observable en Reglamento;
- identificación trazable de diferencias con el legado;
- reversibilidad sin cambios de datos, sesión o permisos;
- confirmación de que el patrón puede expresarse sin exponer claves históricas al módulo futuro.

La migración del siguiente módulo solo debe evaluarse cuando el piloto demuestre equivalencia controlada, las diferencias detectadas tengan tratamiento definido y exista un módulo candidato con riesgo y dependencias acotados. Cursos no es un candidato inmediato mientras mantenga mayor impacto funcional y dependencias de autorización más amplias.

## 11. Exclusiones

Este análisis no modifica ni autoriza modificar:

- `Authorization.php`;
- `ajax/login.php`;
- `permiso_login`;
- sesiones;
- SQL;
- módulos;
- código fuente.

No implementa cambios, no crea TASK y no realiza commit.

## 12. Fuentes utilizadas

- [ADR-002](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md).
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md).
- [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md).
- [AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001](AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001.md).
- [ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001](../governance/ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001.md).
- [ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](../governance/ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md).
- Evidencia técnica de lectura: `form-doc/reglamento.php`, `src/Security/Authorization.php` y `ajax/login.php`.

## 13. Restricciones cumplidas

```text
Sin cambios de código.
Sin implementación.
Sin cambios ADR.
Sin cambios Roadmap.
Sin cambios Manual Maestro.
Sin TASK.
Sin commit.
```

## 14. Siguiente paso posterior

Revisión técnica: `AT-EPIC003-PILOTO-AUTORIZACION-DERIVADA-001`.

## 15. Ejecución y cierre del piloto

### Resultado registrado

El piloto se implementó de forma acotada para la capacidad `reglamento.ver`.

- Fuente: estado académico persistido en `estudiante.tipo_est`.
- Derivación: durante el inicio de sesión.
- Representación temporal: `$_SESSION['capacidades']`.
- Estados que conceden la capacidad: `1`, `2`, `3`, `4`, `5` y `7`.
- Estado que no concede la capacidad: `6` — Eliminado.
- Compatibilidad transitoria: `admin`, `comite` y `aceptado` continúan siendo reconocidos.
- Permiso histórico `3`: preservado; no es la única fuente de la capacidad nueva.

La implementación mantiene el alcance limitado a Reglamento. No autoriza extender la derivación a Cursos, Datos académicos, Mi perfil, Calendario u otros módulos.

### Evidencia técnica y funcional

| Tipo | Resultado | Responsable |
| --- | --- | --- |
| Validación técnica | Aprobada; revisión de cambios y comprobaciones estáticas registradas en las Tasks asociadas. | Codex |
| Validación funcional integrada | Aprobada. | Usuario |

La evidencia funcional confirma el alta de estudiante sin sesión de Administrador o Comité, la asignación de `tipo_est = 1` cuando el selector no está disponible, el cambio de estado por Administrador o Comité y el acceso a Reglamento tras una nueva sesión conforme al estado académico.

### Decisión institucional aplicada

Cuando un estudiante se crea desde un flujo en que el actor no puede seleccionar el estado académico, el sistema asigna `tipo_est = 1` — Postulante. La regla sólo aplica sin selector; Administrador y Comité conservan la selección explícita. No redefine el catálogo de estados, no convierte el permiso histórico `3` en estado académico y no autoriza nuevas cuentas con ese permiso.

### Commits relacionados

- `777323573c7aef6fabbfc15b28c3bcc6b68c9cfc` — `feat(auth): derive reglamento access from academic state`.
- `0f38ade2cd8c7821e451af04c25e678d4a8a0b3a` — `fix(student): repair dropdowns and student creation flow`.
- `cc3855dc7efd7b06cea8ce8cfee18e81b3a010c1` — `fix(student): correct academic state admin UI`.

### Riesgos y trabajo pendiente

- [TEC] [ARQ] El alta de estudiante aún no dispone de una transacción global; la validación previa de `tipo_est` evita el fallo observado, pero no garantiza atomicidad ante errores posteriores.
- La congelación definitiva del permiso histórico `3` continúa pendiente hasta contar con capacidades sustitutas en los módulos correspondientes.
- El cambio local de `ajax/estudiante.php`, rama `update-permiso-tipo-est`, no forma parte de este incremento y requiere revisión y Task independiente.
- No se realizó limpieza del registro parcial detectado durante la investigación; cualquier intervención de datos requiere autorización y procedimiento separado.
