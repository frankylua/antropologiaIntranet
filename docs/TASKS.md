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

## TASK-UX-DELETE-CONFIRM-001

### Identificación

Nombre:
TASK-UX-DELETE-CONFIRM-001

Tipo:
FUNC — Experiencia de usuario

Componente:
Confirmación de eliminación CRUD

FEATURE asociada:
No registrada

EPIC asociada:
No registrada

Estado:
Cerrada

### Objetivo

Implementar un componente estándar de confirmación para operaciones de eliminación CRUD.

### Implementación

Archivos modificados:
`js/components/confirmDelete.js`
`js/funcAjax.js`
`form-doc/footer.php`

Cambio realizado:
Incorporación de un modal Bootstrap reutilizable que delega la operación de eliminación al callback consumidor, e integración inicial en la administración de listas.

### Validación

Validación técnica:
Aprobada

Validación funcional:
Aprobada

### Evidencia Git

Commit:
`0b8e29844dd1ad8edef61df87389e719132e232d`

Mensaje commit:
`feat(ux): introduce standard CRUD delete confirmation messages`

Fecha:
2026-07-16T19:29:45-04:00

### Observaciones

Sin observaciones adicionales registradas.

## TASK-UX-DELETE-CONFIRM-002

### Identificación

Nombre:
TASK-UX-DELETE-CONFIRM-002

Tipo:
GOV — Consistencia visual frontend

Componente:
Modal de confirmación de eliminación CRUD

FEATURE asociada:
No registrada

EPIC asociada:
No registrada

Estado:
Cerrada

### Objetivo

Alinear visualmente el modal de confirmación de eliminación con el estándar Bootstrap de la aplicación.

### Implementación

Archivos modificados:
`js/components/confirmDelete.js`

Cambio realizado:
Ajuste del título, contenido y botones del modal de confirmación de eliminación.

### Validación

Validación técnica:
Aprobada

Validación funcional:
Aprobada

### Evidencia Git

Commit:
`0b8e29844dd1ad8edef61df87389e719132e232d`

Mensaje commit:
`feat(ux): introduce standard CRUD delete confirmation messages`

Fecha:
2026-07-16T19:29:45-04:00

### Observaciones

Sin observaciones adicionales registradas.

## TASK-UX-MESSAGE-CRUD-001

### Identificación

Nombre:
TASK-UX-MESSAGE-CRUD-001

Tipo:
ARQ — Estandarización de componente frontend

Componente:
Mensajes CRUD

FEATURE asociada:
No registrada

EPIC asociada:
No registrada

Estado:
Cerrada

### Objetivo

Crear un helper estándar para mensajes CRUD Bootstrap y eliminar el uso de ventanas nativas del navegador en la eliminación de listas.

### Implementación

Archivos modificados:
`js/mensajesCrud.js`
`js/funcAjax.js`

Cambio realizado:
Incorporación de `mostrarMensajeCRUD()` para presentar mensajes `success` y `danger` con el patrón visual existente, e integración en el resultado AJAX de `eliminarLista()`.

### Validación

Validación técnica:
Aprobada

Validación funcional:
Aprobada

### Evidencia Git

Commit:
`0b8e29844dd1ad8edef61df87389e719132e232d`

Mensaje commit:
`feat(ux): introduce standard CRUD delete confirmation messages`

Fecha:
2026-07-16T19:29:45-04:00

### Observaciones

`admin/act.list.php` no se registra porque no forma parte del commit asociado.

## TASK-UX-DELETE-LIST-INTEGRATION-001

### Identificación

Nombre:
TASK-UX-DELETE-LIST-INTEGRATION-001

Tipo:
Regularización frontend incremental

Origen:
AT-UX-REGULARIZACION-001 — Inspección cambios UX residuales fuera de commit

Componente:
Administración de listas

FEATURE asociada:
No registrada

EPIC asociada:
No registrada

Estado:
Cerrada

### Objetivo

Regularizar los cambios frontend que quedaron fuera del commit `0b8e29844dd1ad8edef61df87389e719132e232d`, completando la integración del flujo UX de eliminación en la administración de listas.

### Implementación

Archivos modificados:
`admin/act.list.php`
`admin/scripts/listas.js`

Cambio realizado:
Incorporación del contenedor Bootstrap para mensajes CRUD y carga de su helper, entrega de la descripción visible del registro al flujo de confirmación y eliminación de la recarga inmediata previa a la confirmación.

### Validación

Validación técnica:
Aprobada

Validación funcional:
Aprobada

Cancelación:
Modal mostrado. Cancelar no ejecuta AJAX. Lista permanece sin cambios.

Confirmación exitosa:
Confirmación ejecuta AJAX. Mensaje CRUD Bootstrap visible. Lista se actualiza después del éxito.

Error funcional:
Backend rechaza eliminación. Mensaje Bootstrap de error visible. Lista no se actualiza.

### Evidencia Git

Commit:
`7d6fd409607915b7c0a655e406bdc6228de7d899`

Mensaje commit:
`fix(ux): complete admin list delete integration flow`

Fecha:
2026-07-16T19:58:58-04:00

### Observaciones

Regularización posterior al incremento UX de confirmación y mensajes CRUD estándar.

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

## TASK-FEATURE001-PERSIST-009

### Identificación

Nombre:
TASK-FEATURE001-PERSIST-009

FEATURE asociada:
FEATURE-001 — Evolución de los Contratos de Persistencia

EPIC asociada:
EPIC-008 — Gobierno del Modelo de Datos y Persistencia

Estado:
Cerrada

### Objetivo

Migrar `Publicacion::eliminarOtraPub()` a contrato explícito de escritura.

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
`d0ad69b64c734db91ca314c65a555d86758e88dd`

Fecha:
2026-07-16T17:32:13-04:00

### Observaciones

Sin observaciones adicionales registradas.

## TASK-FUNC-PUBLICACION-CONTEXT-001

### Identificación

Nombre:
TASK-FUNC-PUBLICACION-CONTEXT-001

Tipo:
FUNC — Corrección funcional

Componente:
Módulo Publicaciones

FEATURE asociada:
No registrada

EPIC asociada:
No registrada

Estado:
Cerrada

### Objetivo

Corregir el flujo de eliminación de publicaciones diferenciando el contexto estudiante/docente.

### Implementación

Archivos modificados:
`form-doc/scripts/publicacion.js`

Cambio realizado:
Selección dinámica de las referencias de scroll y mensajes para estudiante/docente en `.eliminarPub` y `.eliminarOtraPub`, corrigiendo el acceso a una posición inexistente.

### Validación

Validación técnica:
Aprobada

Validación funcional:
Aprobada

### Evidencia Git

Commit:
`2df42e82480d8044d1ed812c5db224f9b1631c55`

Fecha:
2026-07-16T17:32:30-04:00

### Observaciones

Sin observaciones adicionales registradas.

## TASK-DB-MIGRATION-DELETE-RESTRICT-001

### Identificación

Nombre:
TASK-DB-MIGRATION-DELETE-RESTRICT-001

Tipo:
TEC — Migración controlada de esquema de base de datos

Clasificación:
[TEC] Migración base de datos
[GOV] Integridad datos maestros
[ARQ] Política persistencia

AT asociado:
AT-DB-MIGRATION-DELETE-RESTRICT-001

AT origen:
AT-DB-FK-DELETE-CATALOGO-001

Diseño relacionado:
AT-DELETE-SECURE-DESIGN-001

Estado:
Cerrada

### Objetivo

Modificar la política referencial de eliminación de `ON DELETE CASCADE` a `ON DELETE RESTRICT` para `fk_pueblo` (`usuario.pueblo` → `pueblo.id_pueblo`) y `fk_titulo_grado` (`grado_academico.tit_grado` → `titulo_grado.id_titulo`).

### Implementación

Archivo incluido:
`migrations/TASK-DB-MIGRATION-DELETE-RESTRICT-001.sql`

Cambio realizado:
- FK `fk_pueblo` modificada a `ON DELETE RESTRICT`.
- FK `fk_titulo_grado` modificada a `ON DELETE RESTRICT`.
- `ON UPDATE CASCADE` conservado en ambas relaciones.
- No se realizaron cambios DML.
- Rollback documentado en el script de migración.

### Validación

Validación técnica:
Aprobada

Migración:
Aprobada

### Evidencia Git

Commit:
`4ae45a4fb132a499fd91f5971a53664d06636be5`

Mensaje commit:
`feat(db): enforce restrict delete on catalog foreign keys`

Fecha:
2026-07-16T20:38:29-04:00

### Observaciones

La implementación del CRUD seguro queda separada en TASK posteriores. No forman parte de esta TASK los modelos, endpoints AJAX, JavaScript ni frontend asociados a Pueblo y Título.

## TASK-PUEBLO-DELETE-SECURE-001

### Identificación

Nombre:
TASK-PUEBLO-DELETE-SECURE-001

Tipo:
Implementación eliminación CRUD segura de Pueblo

Clasificación:
[FUNC] Operación CRUD segura
[TEC] Persistencia backend
[GOV] Protección datos maestros

AT principal:
AT-DELETE-SECURE-DESIGN-001

Análisis asociados:
AT-DB-FK-DELETE-CATALOGO-001
AT-AUTORIZACION-DELETE-CATALOGO-001

Migración previa:
TASK-DB-MIGRATION-DELETE-RESTRICT-001

Estado:
Cerrada

### Objetivo

Implementar la eliminación segura de `pueblo`, autorizada para Administrador y Comité Académico, con validación previa de dependencias y respuesta JSON estándar integrada con los mensajes CRUD existentes.

### Implementación

Archivos modificados:
`ajax/pueblo.php`
`src/Model/Pueblo.php`

Cambio realizado:
- Validación de autorización para Administrador y Comité Académico.
- Validación de existencia del pueblo solicitado.
- Validación de la dependencia `usuario.pueblo` → `pueblo.id_pueblo`.
- Bloqueo con resultado `TIENE_DEPENDENCIAS` cuando existen usuarios asociados.
- Eliminación segura cuando el pueblo no tiene dependencias.
- Respuesta JSON estándar consumida por el mensaje CRUD existente.

Regla de integridad:
No se permite eliminar un pueblo cuando existen usuarios asociados.

### Validación

Validación técnica:
Aprobada

Validación funcional:
Aprobada

Validaciones realizadas:
- Eliminación de pueblo sin dependencias aprobada.
- Bloqueo de pueblo con usuarios asociados aprobado.
- Usuarios asociados permanecen intactos.
- Administrador autorizado.
- Comité autorizado.
- Usuarios sin permiso rechazados.
- Contrato JSON validado.
- Integración con mensaje CRUD validada.

### Evidencia Git

Commit:
`8e369122098a2a44bae2a6079b4be21451dc9c57`

Mensaje commit:
`feat(crud): secure pueblo deletion with dependency validation`

Fecha:
2026-07-16T20:50:53-04:00

### Observaciones

La implementación se limita al endpoint y al modelo de Pueblo. Los cambios correspondientes a Título y al volcado SQL pertenecen a otras unidades de trabajo.
