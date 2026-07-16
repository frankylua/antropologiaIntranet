# Registro de TASK

## TASK-FEATURE001-PERSIST-006

### Identificación

Nombre:
TASK-FEATURE001-PERSIST-006

FEATURE asociada:
FEATURE-001 — Evolución de los Contratos de Persistencia

EPIC asociada:
EPIC-008 — Gobierno del Modelo de Datos y Persistencia

Estado:
Cerrada

### Objetivo

Migrar `Publicacion::editarArtRev()` a contrato explícito de escritura.

### Implementación

Archivos modificados:
`src/Model/Publicacion.php`

Cambio realizado:
Sustitución de `ejecutarConsulta($sql)` por `ejecutarEscritura($sql)`.

### Validación

Validación técnica:
Aprobada

Validación funcional:
Aprobada

### Evidencia Git

Commit:
`0863b17b46c64070a2a808bd5b6562d0a7d80a1a`

Fecha:
2026-07-16T13:56:33-04:00

### Observaciones

Sin observaciones adicionales registradas.

## TASK-FEATURE001-PERSIST-007

### Identificación

Nombre:
TASK-FEATURE001-PERSIST-007

FEATURE asociada:
FEATURE-001 — Evolución de los Contratos de Persistencia

Estado:
Cerrada

### Objetivo

Migrar `Publicacion::editarLibro()` a contrato explícito de escritura.

### Implementación

Archivos modificados:
`src/Model/Publicacion.php`

Cambio realizado:
Sustitución de `ejecutarConsulta($sql)` por `ejecutarEscritura($sql)`.

### Validación

Validación técnica:
Aprobada

Validación funcional:
Aprobada

### Evidencia Git

Commit:
`39d85a6904d41c41ec26b1f6745aa6cfb717730a`

Fecha:
2026-07-16T16:47:34-04:00

### Observaciones

Sin observaciones adicionales registradas.

## TASK-FUNC-PUBLICACION-EDIT-001

### Identificación

Nombre:
TASK-FUNC-PUBLICACION-EDIT-001

FEATURE asociada:
No registrada

EPIC asociada:
No registrada

Estado:
Cerrada

### Objetivo

Corregir las referencias de estudiante y docente en la edición de publicaciones.

### Implementación

Archivos modificados:
`form-doc/scripts/publicacion.js`

Cambio realizado:
Selección contextual entre `editAcadDoc()` y `editAcadEst()`, y entre `reiniciarInfoDoc()` y `reiniciarInfoEst()`.

### Validación

Validación técnica:
No registrada

Validación funcional:
Flujos de estudiante y docente aprobados

### Evidencia Git

Commit:
`956dbee8f72931f9d602770e6319cbbeb945996d`

Fecha:
2026-07-16T13:57:34-04:00

### Observaciones

Sin observaciones adicionales registradas.

## TASK-FEATURE001-PERSIST-008

### Identificación

Nombre:
TASK-FEATURE001-PERSIST-008

FEATURE asociada:
FEATURE-001 — Evolución de los Contratos de Persistencia

EPIC asociada:
EPIC-008 — Gobierno del Modelo de Datos y Persistencia

Estado:
Cerrada

### Objetivo

Migrar `Titulo::editar()` a contrato explícito de escritura.

### Implementación

Archivos modificados:
`src/Model/Titulo.php`

Cambio realizado:
Sustitución de `ejecutarConsulta($sql)` por `ejecutarEscritura($sql)`.

### Validación

Validación técnica:
Aprobada

Validación funcional:
Aprobada

### Evidencia Git

Commit:
`dd0830e299c4552a82c99f3a41c8c6207d411eae`

Fecha:
2026-07-16T17:11:14-04:00

### Observaciones

Sin observaciones adicionales registradas.

## TASK-FUNC-TITULO-LIST-001

### Identificación

Nombre:
TASK-FUNC-TITULO-LIST-001

Tipo:
FUNC — Corrección funcional

Componente:
Carga administrativa de listas de títulos

FEATURE asociada:
No registrada

EPIC asociada:
No registrada

Estado:
Cerrada

### Objetivo

Corregir el consumo posicional incompatible con el contrato asociativo en la carga administrativa de títulos.

### Implementación

Archivos modificados:
`js/funcAjax.js`

Cambio realizado:
Incorporación de las propiedades explícitas `id_titulo` y `tit_grado` en la llamada a `ajaxListas()`.

### Validación

Validación técnica:
Aprobada

Validación funcional:
Aprobada

### Evidencia Git

Commit:
`9eb3f5895ae9f7e2ec06caa5f936f612a56cd38b`

Fecha:
2026-07-16T17:11:30-04:00

### Observaciones

Sin observaciones adicionales registradas.
