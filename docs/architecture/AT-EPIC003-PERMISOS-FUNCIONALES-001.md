# AT-EPIC003-PERMISOS-FUNCIONALES-001

## Análisis técnico de permisos funcionales efectivos

## 1. Contexto

- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Feature asociada:** FEATURE-EPIC003-001.
- **Estado:** análisis técnico; no autoriza implementación.

Antecedentes documentales:

- ADR-002 — Evolución del modelo de identidad y participación académica.
- AT-FEATURE003-001-INVENTARIO-AUTORIZACION-001.
- TASK-EPIC003-PRESERVACION-PERMISOS-001.
- ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.

## 2. Objetivo y alcance

El análisis determina cómo se asignan y consumen actualmente los accesos, qué entidades intervienen, qué diferencias funcionales son observables y qué dependencias técnicas deben considerarse antes de cualquier incremento.

Se inspeccionó el flujo:

```text
Login
  ↓
permiso_login
  ↓
sesiones
  ↓
controles de acceso
  ↓
módulos protegidos
```

y su relación con `usuario`, `estudiante` y `tipo_estudiante`.

No se definen permisos futuros, reglas académicas, roles, cambios de modelo, migraciones ni implementación.

## 3. Resumen ejecutivo

La fuente efectiva de autorización en tiempo de ejecución son las variables de sesión creadas desde los registros de `permiso_login` al iniciar sesión. La tabla `permiso_login` es la fuente persistente inmediata de esos permisos; `login` identifica la cuenta autenticada y `permiso` no fue consultada directamente en el flujo de autenticación inspeccionado.

El estado académico reside en `estudiante.tipo_est`, con referencia a `tipo_estudiante`. En el estado actual del árbol de trabajo, la operación `update-permiso-tipo-est` actualiza ese campo sin escribir en `permiso_login`. Por ello, el análisis confirma una separación técnica entre la actualización de estado y la modificación de permisos en esa operación. No determina si tal separación satisface la regla institucional pendiente.

## 4. Flujo actual de autorización

```text
Credenciales
  ↓
Login::validarPermiso()
  ↓
login JOIN permiso_login
  ↓
ajax/login.php crea una variable $_SESSION por cada id_permiso
  ↓
controles isset($_SESSION[...]) o Authorization::hasAny(...)
  ↓
acceso, navegación inicial y visualización de módulos
```

### Evidencia confirmada

| Evidencia | Archivo y línea | Comportamiento observado |
| --- | --- | --- |
| Consulta de autenticación/autorización | `src/Model/Login.php:30` | Une `login` con `permiso_login` para las credenciales recibidas. |
| Materialización de sesión | `ajax/login.php:16-37` | Recorre los permisos resultantes y crea las sesiones `admin`, `comite`, `aceptado`, `docente` y/o `estudiante`. |
| Uso centralizado parcial | `src/Security/Authorization.php:7-15` | `hasAny()` comprueba si existe alguna clave de sesión indicada. |
| Redirección inicial | `index.php:6-18` | Prioriza `admin`/`comite`, luego `aceptado`, `docente` y `estudiante`. |

### Inventario de permisos observados

| `id_permiso` | Clave de sesión creada | Denominación observada en código |
| --- | --- | --- |
| 1 | `admin` | Administrador |
| 2 | `comite` | Comité académico |
| 3 | `aceptado` | Comentado como docente o estudiante aceptado |
| 4 | `docente` | Docente |
| 5 | `estudiante` | Estudiante |

La denominación de 1 y 2 aparece en `admin/scripts/admin.js:18`; la de 3, 4 y 5 corresponde a los nombres de sesión y comentarios de `ajax/login.php:23-35`. Este inventario describe la implementación, no una taxonomía institucional aprobada.

## 5. Relación actual entre estado académico y permiso

### Evidencia confirmada

| Evidencia | Archivo y línea | Comportamiento observado |
| --- | --- | --- |
| Persistencia de estado | `src/Model/Estudiante.php:87-89` | `editarTipoEst()` actualiza exclusivamente `estudiante.tipo_est`. |
| Lectura del catálogo | `src/Model/Estudiante.php:36-39` | Obtiene los registros de `tipo_estudiante`. |
| Asignación al crear estudiante | `form-doc/scripts/estudiante.js:123-127` | Construye `permisos=[5]` y agrega 3 si `tipo_est` es 2 o 3. |
| Persistencia de permisos de creación | `ajax/estudiante.php:71-78` | Inserta cada permiso recibido antes de insertar el registro de estudiante. |
| Actualización actual de estado | `ajax/estudiante.php:155-160` | La operación `update-permiso-tipo-est` llama a `editarTipoEst()` y no contiene escritura activa en `permiso_login`. |

El archivo de trabajo actual conserva como comentarios en `ajax/estudiante.php:95-119` lógica previa de edición de permisos y el diff local muestra que se retiró de la operación de actualización la eliminación y reconstrucción automática de permisos. Esto es consistente con el antecedente TASK-EPIC003-PRESERVACION-PERMISOS-001, pero no permite inferir por sí solo una regla futura de asignación.

## 6. Matriz estado/permisos actual

| Estado académico | Evidencia de permiso asociado | Acceso funcional observable | Límite del hallazgo |
| --- | --- | --- | --- |
| Postulante | La creación siempre agrega 5; no se inspeccionó un mapeo nominal de identificadores de `tipo_estudiante` a “postulante”. | Perfil de estudiante cuando existe sesión `estudiante`. | No puede afirmarse la correspondencia exacta de identificador/estado sin datos de la base. |
| Aceptado | La creación agrega 3 además de 5 para `tipo_est` 2 o 3. | Calendario y reglamento requieren `aceptado`, `admin` o `comite`; cursos admiten además `docente`. | No se concluye que 2 o 3 sean el estado institucional “aceptado”. |
| Matriculado | No se encontró una regla activa específica que modifique `permiso_login` al cambiar a este estado. | Depende de los permisos ya persistidos y de la sesión resultante. | No se determina diferencia funcional propia. |

## 7. Matriz módulo/permisos

| Módulo | Permiso/sesión requerida | Archivo/control |
| --- | --- | --- |
| Inicio administrativo | `admin` o `comite` | `admin/inicio.php:9`, `Authorization::hasAny()` |
| Cursos | `admin`, `comite`, `aceptado` o `docente` | `form-doc/ver.curso.php:6-8` |
| Calendario académico | `admin`, `comite` o `aceptado` | `form-doc/calend.acad.php:9-10` |
| Reglamento | `admin`, `comite` o `aceptado` | `form-doc/reglamento.php:9-10` |
| Ficha de estudiante | `admin`, `comite` o `estudiante` | `form-doc/info.estudiante.php:6-10` |
| Ficha de docente | `admin`, `comite` o `docente` | `form-doc/info.docente.php:6-10` |
| Navegación de cursos | `docente` **y** `aceptado`, o `admin`, o `comite` | `form-doc/header.php:92-95` |
| Navegación de calendario | `admin`, `comite` o `aceptado` | `form-doc/header.php:101-104` |

La protección directa de cursos admite a `aceptado` o `docente`, mientras el enlace de navegación de cursos exige simultáneamente `docente` y `aceptado` para ese caso. Esta diferencia está confirmada en el código y puede afectar visibilidad de menú frente a acceso directo; no se califica como regla correcta o incorrecta.

## 8. Permisos múltiples

El modelo persistente permite múltiples filas por `id_login`: `Usuario::insertarPermisos()` inserta una fila por permiso (`src/Model/Usuario.php:21-24`) y la creación de estudiantes itera el arreglo recibido (`ajax/estudiante.php:71-76`). Durante el inicio de sesión, cada fila puede originar una clave de sesión adicional (`ajax/login.php:15-37`).

Por tanto, la coexistencia de permisos está soportada por la implementación observada. Hay operaciones que pueden eliminar o actualizar permisos (`src/Model/Usuario.php:55-70`), y la corrección actual de preservación evita la eliminación automática en el cambio de `tipo_est`. No se identificó en el alcance inspeccionado una resolución explícita de conflictos entre permisos; los controles aplican comprobaciones de existencia y, en algunos casos, prioridades de redirección.

## 9. Hallazgos confirmados

1. La autorización de módulos depende de claves de sesión, no de una consulta directa de `tipo_estudiante` durante cada control.
2. Las claves de sesión se generan desde los registros de `permiso_login` retornados al autenticar.
3. `tipo_estudiante` y `permiso_login` son entidades persistentes separadas y, en la actualización actual de estado, no se escriben conjuntamente.
4. Existen múltiples permisos por login y pueden coexistir en una misma sesión.
5. El calendario académico requiere la clave `aceptado` (o administrativa), mientras la ficha de estudiante requiere `estudiante` (o administrativa); no son equivalentes a nivel de controles actuales.
6. Los controles están parcialmente centralizados en `Authorization::hasAny()`, pero también existen verificaciones directas con `isset($_SESSION[...])`.

## 10. Hipótesis pendientes

- La correspondencia institucional exacta entre los identificadores de `tipo_estudiante` y los nombres postulante, aceptado y matriculado requiere validación de datos y decisión institucional.
- La intención funcional de que un estado académico otorgue, conserve o revoque un permiso no se deduce del código.
- La diferencia entre la navegación de cursos y su control de acceso directo puede ser deliberada o una inconsistencia; requiere validación funcional, no una conclusión técnica unilateral.

## 11. Riesgos

- Una definición institucional incompleta puede mantener accesos insuficientes o inesperados después de un cambio de estado.
- Los controles distribuidos entre `Authorization::hasAny()` e inspecciones directas de sesión elevan el riesgo de divergencia entre módulos.
- Las rutas de creación y actualización no expresan una misma regla de vínculo estado/permisos, lo que puede producir resultados diferentes según el flujo utilizado.
- La prioridad fija de redirección inicial puede ocultar la experiencia de otros permisos coexistentes sin eliminar dichos permisos.

## 12. Recomendación de siguiente paso

**Escenario B: no existe definición institucional suficiente.**

El análisis técnico confirma el comportamiento actual y sus dependencias, pero no responde qué concepto debe determinar los permisos funcionales efectivos ni si postulante, aceptado y matriculado deben diferenciarse. Corresponde completar la validación institucional registrada en ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001 antes de crear una TASK incremental.

## Fuentes consultadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md](../governance/ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md](../governance/ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-OPERACION-001.md](../governance/ACTA-VALIDACION-INSTITUCIONAL-EPIC003-OPERACION-001.md)
- [ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md](../governance/ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md)
- [ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.md](../governance/ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.md)
- Código fuente actual inspeccionado, en particular `src/Model/Login.php`, `ajax/login.php`, `src/Security/Authorization.php`, `ajax/estudiante.php` y `form-doc/scripts/estudiante.js`.
