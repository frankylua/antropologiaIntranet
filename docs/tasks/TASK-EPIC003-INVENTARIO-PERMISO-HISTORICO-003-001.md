# TASK-EPIC003-INVENTARIO-PERMISO-HISTORICO-003-001

## Inventario de productores y consumidores del permiso histórico 3

## 1. Identificación

- **Clasificación:** [TEC] [ARQ] [DOC].
- **EPIC asociado:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
- **AT asociado:** [AT-EPIC003-RESOLUCION-PERMISO-HISTORICO-003-001](../architecture/AT-EPIC003-RESOLUCION-PERMISO-HISTORICO-003-001.md).
- **Estado:** Inventario realizado; pendiente de revisión técnica.
- **Alcance:** inspección estática del repositorio. No se modificaron datos, SQL operativo ni código funcional.

## 2. Objetivo

Localizar las escrituras, lecturas, traducciones de sesión y consumidores funcionales del permiso histórico `3` y de `$_SESSION['aceptado']`, como prerrequisito para congelar nuevas asignaciones.

## 3. Resultado ejecutivo

El permiso `3` sigue teniendo uso funcional activo y no puede eliminarse todavía:

- se asigna a estudiantes nuevos con estado `2` o `3`;
- se asigna a docentes nuevos cuando no se selecciona la condición docente;
- se traduce durante el login a `$_SESSION['aceptado']`;
- esa clave controla siete comprobaciones de redirección, navegación y acceso a Cursos, Calendario y Reglamento.

El inventario no encontró una consulta SQL directa adicional de `permiso_login.id_permiso = 3` fuera del flujo de autenticación. Las comprobaciones de autorización se realizan desde la clave de sesión materializada por `ajax/login.php`.

## 4. Catálogo y datos de referencia

| Evidencia | Hallazgo | Implicación |
| --- | --- | --- |
| `c1441353_antr_db.sql:859-864` | El catálogo identifica `3` como `postulante`. | Difiere del uso de sesión `aceptado`. |
| `c1441353_antr_db.sql:882-887` | El volcado contiene una asignación de permiso `3` a un login de prueba. | Dato heredado candidato a reinicialización futura; no autoriza borrado. |
| `c1441353_antr_db.sql:1072-1079` | Los estados académicos se mantienen en `tipo_estudiante`, con `2` aceptado y `3` matriculado. | Confirma que estado académico y permiso `3` son conceptos distintos. |

## 5. Productores activos

| Archivo | Ubicación | Comportamiento observado | Acción posterior |
| --- | --- | --- | --- |
| `form-doc/scripts/estudiante.js` | 123-127 | El alta inicial añade `5`; si `tipo_est` es `2` o `3`, agrega además `3`. | Retirar esta asignación en la Task de congelación y validar altas sin permiso `3`. |
| `ajax/estudiante.php` | 72-91 | Recibe el arreglo de permisos y los persiste mediante `insertarPermisos()` tras validar `tipo_est`. | El endpoint no filtra `3`; requiere defensa de backend en la Task de congelación. |
| `ajax/docente.php` | 49-56 | En una rama de alta asigna `[3]`; en la alternativa asigna `[4]`. | Precisar la regla docente y retirar `3` sin sustituirlo por una equivalencia no aprobada. |
| `ajax/docente.php` | 107-115 | En actualización consulta si el login ya posee `3` y lo inserta si falta. | Es productor adicional que debe quedar prohibido. |

`src/Model/Usuario.php:61` y `src/Model/Admin.php:11` son primitivas genéricas de persistencia de `permiso_login`; no asignan `3` por sí mismas, pero permiten que un productor lo persista.

## 6. Traducción de sesión

| Archivo | Ubicación | Comportamiento observado | Riesgo |
| --- | --- | --- | --- |
| `ajax/login.php` | 14-34 | Recorre permisos del login; cuando `id_permiso == 3`, crea `$_SESSION['aceptado']`. | Convierte el permiso ambiguo en una señal transversal de autorización. |
| `ajax/login.php` | 37-49 | Deriva temporalmente `reglamento.ver` desde `estudiante.tipo_est` para estados 1, 2, 3, 4, 5 y 7; excluye 6. | Esta es una capacidad sustituta parcial, independiente del permiso `3`. |

## 7. Consumidores funcionales de `$_SESSION['aceptado']`

| Archivo | Ubicación | Uso observado | Sustitución requerida |
| --- | --- | --- | --- |
| `index.php` | 6-12 | Redirige una sesión `aceptado` a Calendario. | Definir capacidad o ruta inicial derivada por estado/rol. |
| `form-doc/header.php` | 77 | Incluye `aceptado` para mostrar el menú de ingreso. | Revisar contra capacidades de navegación. |
| `form-doc/header.php` | 92-94 | Muestra el enlace Cursos. | Sustituir tras definir y validar la capacidad de consulta de Cursos. |
| `form-doc/header.php` | 101-104 | Muestra el enlace Calendario Académico. | Sustituir por capacidad de Calendario. |
| `form-doc/ver.curso.php` | 6-8 | Permite abrir Cursos junto con admin, comité o docente. | Migrar en la Task de separación de capacidades de Cursos. |
| `form-doc/calend.acad.php` | 9 | Autoriza Calendario mediante `Authorization::hasAny(['admin', 'comite', 'aceptado'])`. | Definir e implementar capacidad de Calendario. |
| `form-doc/reglamento.php` | 9 | Conserva `aceptado` como fallback junto con `reglamento.ver`, admin y comité. | Retirar solo después de validar `reglamento.ver` para la matriz indicada en el AT. |

No se clasifican como consumidores funcionales los comentarios heredados en `ajax/estudiante.php` y `form-doc/scripts/ficha.estudiante.js`; deben limpiarse solo al realizar las Tasks de código correspondientes.

## 8. Cambio de estado académico

El endpoint `ajax/estudiante.php`, operación `update-permiso-tipo-est`, actualmente solo invoca `Estudiante::editarTipoEst()`. El diff local pendiente eliminó las llamadas históricas a `eliminarAcceso()` y `agregarPermiso(3|5)`.

Este hallazgo concuerda con la dirección arquitectónica, pero no valida el cambio: es una modificación local ajena a esta Task, sin pruebas ni commit independiente. La Task `TASK-EPIC003-CAMBIO-ESTADO-SIN-RECONSTRUIR-PERMISOS-001` debe revisarla y comprobar autorización, validación de transición y efectos de sesión.

## 9. Orden de continuidad

1. Revisar y aprobar este inventario.
2. Crear `TASK-EPIC003-CONGELAR-ASIGNACION-PERMISO-003-001` para retirar y bloquear los cuatro productores identificados, con validación de backend.
3. Resolver por separado el cambio de estado no destructivo.
4. Sustituir cada consumidor por módulo, comenzando por Reglamento, luego Cursos, Calendario, navegación y redirección.
5. Solo cuando no queden consumidores, retirar la creación de `$_SESSION['aceptado']` en login.

## 10. Validaciones realizadas

- Búsqueda estática de `id_permiso == 3`, arreglos que contienen `3`, `buscarPermiso(3)` y `$_SESSION['aceptado']` en código de aplicación.
- Inspección del catálogo, las asignaciones de prueba y `tipo_estudiante` en `c1441353_antr_db.sql`.
- Inspección de `ajax/login.php`, `ajax/estudiante.php`, `ajax/docente.php` y los puntos de entrada de los módulos consumidores.

No se ejecutaron operaciones contra la base de datos ni se alteró el código funcional.
