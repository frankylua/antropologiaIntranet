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

## TASK-TITULO-DELETE-SECURE-001

### Identificación

Nombre:
TASK-TITULO-DELETE-SECURE-001

Tipo:
Implementación eliminación CRUD segura de Títulos académicos

Clasificación:
[FUNC] Operación CRUD segura
[TEC] Persistencia backend
[GOV] Protección datos maestros

AT principal:
AT-TITULO-DELETE-SECURE-001

Diseño general:
AT-DELETE-SECURE-DESIGN-001

Migración previa:
TASK-DB-MIGRATION-DELETE-RESTRICT-001

Patrón aprobado:
TASK-PUEBLO-DELETE-SECURE-001

Estado:
Cerrada

### Objetivo

Implementar la eliminación segura de `titulo_grado`, autorizada para Administrador y Comité Académico, impidiendo eliminar un título académico cuando existen registros asociados mediante la dependencia `grado_academico.tit_grado` → `titulo_grado.id_titulo`.

### Implementación

Archivos modificados:
`src/Model/Titulo.php`
`ajax/titulo.php`

Cambio realizado:
- Validación de autorización mediante el mecanismo existente para Administrador y Comité Académico, sin crear nuevos permisos.
- Validación de existencia del título académico solicitado.
- Validación de la dependencia `grado_academico.tit_grado` → `titulo_grado.id_titulo`.
- Bloqueo con resultado `TIENE_DEPENDENCIAS` cuando existen grados académicos asociados.
- Eliminación segura cuando el título no tiene dependencias.
- Traducción de la restricción referencial a la respuesta JSON estándar.
- Contrato JSON alineado con `TASK-PUEBLO-DELETE-SECURE-001` e integrado con la UX existente.

Identidad del registro:
La eliminación utiliza exclusivamente `titulo_grado.id_titulo`. La columna `tipo_grado` corresponde a clasificación y contexto visual, y no se utiliza como identidad persistente ni como criterio de eliminación.

Regla de integridad:
No se permite eliminar un título académico cuando existen grados académicos asociados. La operación elimina únicamente el registro de `titulo_grado`; los registros de `grado_academico` permanecen intactos.

Contrato JSON:
- Éxito: `{"ok":true,"codigo":"ELIMINADO","mensaje":"Registro eliminado correctamente"}`.
- Dependencias: `{"ok":false,"codigo":"TIENE_DEPENDENCIAS","mensaje":"No se puede eliminar porque existen registros asociados"}`.
- Sin permisos: `{"ok":false,"codigo":"NO_AUTORIZADO","mensaje":"Usuario sin permisos para eliminar"}`.

### Validación

Validación técnica:
Aprobada

Validación funcional:
Aprobada

Validaciones realizadas:
- Firma `Titulo::eliminar($id)` restaurada sin dependencia de `tipo_grado`.
- Consumidores revisados.
- Administrador autorizado.
- Comité Académico autorizado.
- Usuarios sin permisos rechazados.
- Títulos con dependencias bloqueados.
- Grados académicos asociados permanecen intactos.
- Contrato JSON validado.
- Integración UX existente preservada.

### Evidencia Git

Commit:
`939a59100f37094aa6a4d7af3732fa80b5b93dd3`

Mensaje commit:
`feat(crud): secure titulo deletion with dependency validation`

Fecha:
2026-07-16T21:08:48-04:00

### Observaciones

La implementación se limita al endpoint y al modelo de Título. No se modificaron la base de datos, la migración `TASK-DB-MIGRATION-DELETE-RESTRICT-001`, los componentes JavaScript ni la UX existente.

## TASK-REG-A3-GRADO-INSERT-001

### Identificación

Nombre:
TASK-REG-A3-GRADO-INSERT-001

Tipo:
Implementación backend incremental supervisada

FEATURE asociada:
FEATURE-001 — Evolución de los Contratos de Persistencia

EPIC asociada:
EPIC-008 — Gobierno del Modelo de Datos y Persistencia

AT asociado:
AT-REG-A3-003

ADR asociado:
ADR-001 — Contrato explícito para operaciones de escritura

Estado:
Cerrada

### Objetivo

Migrar exclusivamente `Grado::insertar()` desde el contrato heredado `ejecutarConsulta()` hacia el contrato explícito `ejecutarEscritura()`, manteniendo el comportamiento observable existente.

### Implementación

Archivo modificado:
`src/Model/Grado.php`

Cambio realizado:
Sustitución exclusiva de `ejecutarConsulta($sql)` por `ejecutarEscritura($sql)` dentro de `Grado::insertar()`.

Contrato de persistencia:
La operación `INSERT` de Grado utiliza el contrato explícito de escritura definido por FEATURE-001.

Se preservaron sin cambios:
- Firma `insertar($usuario,$instituto,$titulo,$fecha)`.
- SQL `INSERT` existente.
- Tablas y valores utilizados.
- Reglas de negocio.
- Flujo AJAX.
- Endpoint y mensajes existentes.

No se realizaron cambios en:
- `ajax/grado.php`.
- `form-doc/scripts/grado.js`.
- `src/Config/conexion.php`.
- `ConnectionAuthority`.
- Otros modelos.
- Base de datos.
- Frontend.

### Validación

Validación técnica:
Aprobada

Validación funcional:
Aprobada

Validaciones realizadas:
- Sintaxis PHP validada.
- Diff revisado.
- Firma del método sin cambios.
- SQL sin cambios.
- Consumidor revisado.
- Endpoint preservado.
- Mensajes funcionales preservados.
- Sin cambios de implementación fuera del alcance autorizado.

### Evidencia Git

Commit:
`b28d7421d3e7fba43d8d9661fbc359b12aa63955`

Mensaje commit:
`refactor(persistence): migrate grado insert to explicit write contract`

Fecha:
2026-07-16T21:23:54-04:00

### Observaciones

La implementación se limita a la migración del helper utilizado por `Grado::insertar()`. No se incorporaron parametrización SQL, transacciones, nuevas excepciones, DAO, Repository ni cambios de infraestructura.

## TASK-REG-A4-PASANTIA-INSERT-001

### Identificación

Nombre:
TASK-REG-A4-PASANTIA-INSERT-001

Tipo:
Implementación backend incremental supervisada

FEATURE asociada:
FEATURE-001 — Evolución de los Contratos de Persistencia

EPIC asociada:
EPIC-008 — Gobierno del Modelo de Datos y Persistencia

AT asociado:
AT-REG-A4-PASANTIA-INSERT-001

ADR asociado:
ADR-001 — Contrato explícito para operaciones de escritura

Estado:
Cerrada

### Objetivo

Migrar exclusivamente `Pasantia::insertar()` desde el contrato heredado `ejecutarConsulta()` hacia el contrato explícito `ejecutarEscritura()`, manteniendo el comportamiento observable existente.

### Implementación

Archivo modificado:
`src/Model/Pasantia.php`

Cambio realizado:
Sustitución exclusiva de `ejecutarConsulta($sql)` por `ejecutarEscritura($sql)` dentro de `Pasantia::insertar()`.

Contrato de persistencia:
La operación `INSERT` de Pasantia utiliza el contrato explícito de escritura definido por FEATURE-001.

Se preservaron sin cambios:
- Firma `insertar($usuario,$prof,$inst,$fech_in,$fech_ter,$ciudad,$fondo,$pais)`.
- SQL `INSERT` existente.
- Parámetros, tabla, columnas y valores utilizados.
- Flujo AJAX.
- Respuesta observable y mensajes funcionales.

No se realizaron cambios en:
- `ajax/pasantia.php`.
- `form-doc/scripts/pasantia.js`.
- `src/Config/conexion.php`.
- `src/Config/ConnectionAuthority.php`.
- Otros modelos.
- Otros métodos de Pasantia.
- Base de datos.
- Frontend.

### Validación

Validación técnica:
Aprobada

Validación funcional:
Aprobada

Validaciones realizadas:
- Sintaxis PHP validada.
- Diff revisado.
- Consumidores revisados.
- Único consumidor confirmado: `ajax/pasantia.php`.
- Retorno utilizado únicamente como condición booleana.
- Sin dependencia de `PDOStatement`.
- Firma y SQL preservados.
- Endpoint y frontend sin cambios.
- Mensaje funcional preservado.
- Cambio reversible restaurando `ejecutarConsulta($sql)`.

### Evidencia Git

Commit:
`f5589f8b5f617da955148db98516d852865155c4`

Mensaje commit:
`refactor(persistence): migrate pasantia insert to explicit write contract`

Fecha:
2026-07-16T21:39:34-04:00

### Observaciones

La implementación se limita a la migración del helper utilizado por `Pasantia::insertar()`. No se incorporaron parametrización SQL, transacciones, nuevas excepciones, DAO, Repository ni cambios de infraestructura.

## TASK-REG-A6-PROYECTO-EDITARINV-001

### Identificación

Nombre:
TASK-REG-A6-PROYECTO-EDITARINV-001

Tipo:
Implementación backend incremental supervisada

FEATURE asociada:
FEATURE-001 — Evolución de los Contratos de Persistencia

EPIC asociada:
EPIC-008 — Gobierno del Modelo de Datos y Persistencia

AT asociado:
AT-REG-A6-PROYECTO-EDITARINV-001

ADR asociado:
ADR-001 — Contrato explícito para operaciones de escritura

Estado:
Cerrada

### Objetivo

Migrar exclusivamente `Proyecto::editarInv()` desde el contrato heredado `ejecutarConsulta()` hacia el contrato explícito `ejecutarEscritura()`, manteniendo el comportamiento observable existente.

### Implementación

Archivo modificado:
`src/Model/Proyecto.php`

Método:
`Proyecto::editarInv($inv,$id_proy)`

Cambio realizado:
Sustitución exclusiva de `ejecutarConsulta($sql)` por `ejecutarEscritura($sql)` dentro de `Proyecto::editarInv()`.

Contrato de persistencia:
La operación `UPDATE` de `proyecto_investigacion` utiliza el contrato explícito de escritura definido por FEATURE-001.

Se preservaron sin cambios:
- Firma `editarInv($inv,$id_proy)`.
- SQL `UPDATE` existente.
- Parámetros, tabla, columnas y condición utilizados.
- Endpoint `ajax/proyecto.php`.
- Frontend.
- Mensajes observables y comportamiento funcional esperado.

No se realizaron cambios en:
- `ajax/proyecto.php`.
- `form-doc/scripts/proyecto.js`.
- Otros métodos de Proyecto.
- Otros modelos.
- Base de datos.
- SQL.
- Frontend.

### Validación

Validación técnica:
Aprobada

Validaciones realizadas:
- `php -l src/Model/Proyecto.php`: correcto.
- `git diff --check`: correcto.
- Diff revisado: un único cambio funcional en `Proyecto::editarInv()`.

Validación funcional:
No ejecutada

Motivo:
No existe fixture controlado ni proyecto identificado como seguro para modificar.

No se encontraron:
- Datos semilla descartables.
- Ambiente de prueba autorizado.
- Identificadores aprobados.

Compatibilidad funcional validada estáticamente mediante:
- Revisión del flujo AJAX `update-inv`.
- Revisión del endpoint `ajax/proyecto.php`.
- Revisión del contrato de respuesta.
- Confirmación de ausencia de dependencia de `PDOStatement`, `fetch()`, `rowCount()` o propiedades del retorno.
- Confirmación de que el retorno se utiliza únicamente como condición booleana.

### Evidencia Git

Commit:
`e3d3ce78566ecce9351e607758ee0757f425775a`

Mensaje commit:
`refactor(persistence): migrate proyecto editarInv to explicit write contract`

Fecha:
2026-07-16T22:32:20-04:00

### Observaciones

La validación funcional no fue ejecutada por ausencia de datos controlados. La implementación mantiene reversibilidad restaurando `ejecutarConsulta($sql)`.

## TASK-REG-A7-PROYECTO-EDITARCOINV-001

### Identificación

Nombre:
TASK-REG-A7-PROYECTO-EDITARCOINV-001

Tipo:
Implementación backend incremental supervisada

FEATURE asociada:
FEATURE-001 — Evolución de los Contratos de Persistencia

EPIC asociada:
EPIC-008 — Gobierno del Modelo de Datos y Persistencia

AT asociado:
AT-REG-A7-PROYECTO-EDITARCOINV-001

ADR asociado:
ADR-001 — Contrato explícito para operaciones de escritura

Estado:
Cerrada

### Objetivo

Migrar exclusivamente `Proyecto::editarCoinv()` desde el contrato heredado `ejecutarConsulta()` hacia el contrato explícito `ejecutarEscritura()`, manteniendo el comportamiento observable existente.

### Implementación

Archivo modificado:
`src/Model/Proyecto.php`

Método:
`Proyecto::editarCoinv($coinv,$id_proy,$inst)`

Cambio realizado:
Sustitución exclusiva de `ejecutarConsulta($sql)` por `ejecutarEscritura($sql)` dentro de `Proyecto::editarCoinv()`.

Contrato de persistencia:
La operación `UPDATE` de `proyecto_investigacion` utiliza el contrato explícito de escritura definido por FEATURE-001.

Se preservaron sin cambios:
- Firma `editarCoinv($coinv,$id_proy,$inst)`.
- Parámetros `$coinv`, `$id_proy` y `$inst`.
- SQL `UPDATE` existente.
- Tabla, columnas y condición utilizadas.
- Endpoint `ajax/proyecto.php`.
- Operación AJAX `update-coinv`.
- Flujo frontend `form-doc/scripts/proyecto.js`.
- Mensajes JSON y comportamiento observable.

Quedaron fuera de alcance:
- CRUD completo de Proyecto.
- `Proyecto::editar()`.
- `Proyecto::editarInv()`.
- `Proyecto::insertar()`.
- Cambios SQL.
- Cambios frontend.
- Cambios de esquema.
- Nuevas transacciones.
- Correcciones funcionales.
- Refactor general.

### Validación

Validación técnica:
Aprobada

Validaciones realizadas:
- `php -l src/Model/Proyecto.php`: correcto.
- `git diff --check`: correcto.
- Consumidor confirmado: `ajax/proyecto.php`.
- Operación confirmada: `update-coinv`.
- Frontend confirmado: `form-doc/scripts/proyecto.js`.
- El retorno se consume únicamente como condición booleana.
- Firma, parámetros, SQL, endpoint, frontend y contrato JSON preservados.

Validación funcional:
No ejecutada

Motivo:
No existe fixture controlado ni proyecto autorizado para modificar.

No se crearon datos artificiales ni se realizaron operaciones sobre la base de datos.

La compatibilidad fue validada estáticamente mediante:
- Revisión del flujo AJAX.
- Revisión del endpoint.
- Revisión del contrato de respuesta.
- Confirmación de ausencia de dependencia de `PDOStatement`.

### Evidencia Git

Commit:
`14b94e9bcf0b73b0bb0321ad80837173d660cc0e`

Mensaje commit:
`refactor(persistence): migrate proyecto editarCoinv to explicit write contract`

Fecha:
2026-07-16T22:45:56-04:00

### Observaciones

La validación funcional quedó pendiente por ausencia de datos controlados. La implementación mantiene reversibilidad mediante la restauración de `ejecutarConsulta()`.

## TASK-REG-A8-TESIS-EDITARGUIA-001

### Identificación

Nombre:
TASK-REG-A8-TESIS-EDITARGUIA-001

Tipo:
Implementación backend incremental supervisada

FEATURE asociada:
FEATURE-001 — Evolución de los Contratos de Persistencia

EPIC asociada:
EPIC-008 — Gobierno del Modelo de Datos y Persistencia

AT asociado:
AT-REG-A8-TESIS-EDITARGUIA-001

ADR asociado:
ADR-001 — Contrato explícito para operaciones de escritura

Estado:
Cerrada

### Objetivo

Migrar exclusivamente `Tesis::editarGuia()` desde el contrato heredado `ejecutarConsulta()` hacia el contrato explícito `ejecutarEscritura()`, manteniendo el comportamiento observable existente.

### Implementación

Archivo modificado:
`src/Model/Tesis.php`

Método:
`Tesis::editarGuia($guia,$id_tesis)`

Cambio realizado:
Sustitución exclusiva de `ejecutarConsulta($sql)` por `ejecutarEscritura($sql)` dentro de `Tesis::editarGuia()`.

Contrato de persistencia:
La operación `UPDATE` de `tesis` utiliza el contrato explícito de escritura definido por FEATURE-001.

Se preservaron sin cambios:
- Firma `editarGuia($guia,$id_tesis)`.
- SQL `UPDATE` existente.
- Parámetros `$guia` y `$id_tesis`.
- Endpoint `ajax/tesis.php`.
- Operación AJAX `update-guia`.
- Flujo frontend `form-doc/scripts/tesis.js`.
- Mensajes JSON y comportamiento observable.

Quedaron fuera de alcance:
- `Tesis::editarCoguia()`.
- `Tesis::insertarTesis()`.
- `Tesis::insertarCotutela()`.
- CRUD general de Tesis.
- Cambios SQL.
- Cambios frontend.
- Nuevas transacciones.
- Correcciones funcionales.
- Refactor general.

### Validación

Validación técnica:
Aprobada

Validaciones realizadas:
- `php -l src/Model/Tesis.php`: correcto.
- `git diff --check`: correcto.
- Único archivo de implementación modificado: `src/Model/Tesis.php`.
- Consumidor confirmado: `ajax/tesis.php`.
- Operación confirmada: `update-guia`.
- Frontend confirmado: `form-doc/scripts/tesis.js`.
- El retorno se consume únicamente como condición booleana.
- No existe dependencia de `PDOStatement`, `fetch()`, `rowCount()` ni propiedades del retorno.
- Firma, parámetros, SQL, endpoint, frontend y mensajes preservados.
- `ejecutarEscritura($sql)` se utiliza sin solicitar identificador insertado.

Validación funcional:
Pendiente por falta de fixture controlado

Motivo:
No existe fixture controlado ni tesis/profesor autorizado para modificar.

No se crearon datos artificiales ni se realizaron operaciones sobre la base de datos.

### Evidencia Git

Commit:
`0337e3df8a9047be773b8a873a17549200f975a4`

Mensaje commit:
`refactor(persist): migrate tesis guia update to explicit write contract`

Fecha:
2026-07-16T23:00:24-04:00

### Observaciones

La validación funcional quedó pendiente por ausencia de datos controlados. La implementación mantiene reversibilidad mediante la restauración de `ejecutarConsulta($sql)`.

## TASK-TEC-CONGRESO-SELECT-CONTRATO-001

### Identificación

Nombre:
TASK-TEC-CONGRESO-SELECT-CONTRATO-001

Tipo:
[Tec] Corrección frontend

Origen:
AT-TEC-CONGRESO-SELECT-CADENAMAY-001

Estado:
Cerrada

### Objetivo

Corregir el consumidor frontend del buscador de Congreso para utilizar el contrato asociativo de la respuesta JSON.

### Causa

El consumidor frontend utilizaba índices posicionales (`list[0]`, `list[1]`) sobre una respuesta JSON asociativa.

Respuesta real:

```text
{
 id_congreso,
 nombre
}
```

### Implementación

Archivo modificado:
`form-doc/scripts/congreso.js`

Cambio realizado:

Antes:

```text
list[0]
list[1]
```

Después:

```text
list.id_congreso
list.nombre
```

### Validación

Validaciones realizadas:
- `git diff --check`: correcto.
- Validación funcional `VF-TEC-01`: aprobada.
- Lista de congresos cargada correctamente.
- Error `cadenaMay(undefined)` resuelto.

### Archivos afectados

- `form-doc/scripts/congreso.js`.

### Exclusiones

- Backend sin cambios.
- Modelo sin cambios.
- SQL sin cambios.
- Persistencia sin cambios.

### Reversibilidad

Restaurar el consumo posicional anterior.

### Observaciones

Estado final:
Cerrada

## TASK-EPIC003-AUTORIZACION-REGLAMENTO-ESTADO-001

### Identificación

Tipo:
[ARQ] [TEC] [DOC] [GOV] [MET] Piloto incremental de capacidades derivadas.

Estado:
Cerrada

Fecha de cierre:
2026-07-18

### Resultado

- La capacidad `reglamento.ver` se deriva durante el inicio de sesión desde `estudiante.tipo_est` y se mantiene temporalmente en `$_SESSION['capacidades']`.
- Los estados `1`, `2`, `3`, `4`, `5` y `7` conceden la capacidad; el estado `6` — Eliminado no la concede.
- Se mantiene el manejo explícito de cardinalidad ambigua y la coexistencia con `admin`, `comite` y `aceptado`.
- El permiso histórico `3` se preserva y no se usa como única fuente de la nueva capacidad.

### Validación

Validación técnica:
Aprobada.

Validación funcional:
Aprobada por el usuario. Codex realizó implementación, inspección y validación técnica estática; no ejecutó la validación funcional.

### Evidencia Git

Commit:
`777323573c7aef6fabbfc15b28c3bcc6b68c9cfc`

Push:
Completado.

Archivos:
- `src/Model/Login.php`.
- `ajax/login.php`.
- `src/Security/Authorization.php`.
- `form-doc/reglamento.php`.

## TASK-EPIC003-FIX-DROPDOWNS-PERSONAS-001

### Identificación

Tipo:
[TEC] [DOC] Corrección de contrato asociativo.

Estado:
Cerrada

Fecha de cierre:
2026-07-18

### Resultado y validación

Los dropdowns de instituciones, países y tipo de estudiante consumen propiedades asociativas explícitas.

Validación técnica:
Aprobada.

Validación funcional:
Aprobada por el usuario.

### Evidencia Git

Commit:
`0f38ade2cd8c7821e451af04c25e678d4a8a0b3a`

Push:
Completado.

## TASK-EPIC003-FIX-PROFESOR-GUIA-ALTA-001

### Identificación

Tipo:
[TEC] [DOC] Adaptación asociativa de Profesor guía.

Estado:
Cerrada

Fecha de cierre:
2026-07-18

### Resultado y validación

La lista de Profesor guía consume `id_usuario`, `nombres`, `ap_pat` y `ap_mat`; el texto visible deja de resolver como `undefined`.

Validación técnica:
Aprobada.

Validación funcional:
Aprobada por el usuario.

### Evidencia Git

Commit:
`0f38ade2cd8c7821e451af04c25e678d4a8a0b3a`

Push:
Completado.

## TASK-EPIC003-FIX-TIPO-EST-POSTULANTE-DEFECTO-001

### Identificación

Tipo:
[TEC] [DOC] Regla de alta de estudiante.

Estado:
Cerrada

Fecha de cierre:
2026-07-18

### Resultado y validación

Cuando el selector de estado no está disponible, el frontend envía `tipo_est = 1` — Postulante. Administrador y Comité mantienen la selección explícita. El backend valida `tipo_est` antes de `insertarLogin()` y antes de cualquier escritura. La regla no redefine el catálogo ni autoriza nuevas cuentas con permiso histórico `3`.

Validación técnica:
Aprobada.

Validación funcional:
Aprobada por el usuario.

### Evidencia Git

Commit:
`0f38ade2cd8c7821e451af04c25e678d4a8a0b3a`

Push:
Completado.

Nota de alcance:
El commit incorporó exclusivamente la validación previa de `tipo_est` en `ajax/estudiante.php`; no incluyó el cambio local histórico de permisos.

## TASK-EPIC003-FIX-UI-CAMBIO-ESTADO-ESTUDIANTE-001

### Identificación

Tipo:
[TEC] [DOC] Corrección de interfaz de estado académico.

Estado:
Cerrada

Fecha de cierre:
2026-07-18

### Resultado y validación

El modal Cambiar estado utiliza identificadores independientes del modal Eliminar y el filtro superior consume `id_tipo_est`.

Validación técnica:
Aprobada.

Validación funcional:
Aprobada por el usuario.

### Evidencia Git

Commit:
`cc3855dc7efd7b06cea8ce8cfee18e81b3a010c1`

Push:
Completado.

## TASK-EPIC003-CAMBIO-ESTADO-SIN-RECONSTRUIR-PERMISOS-001

### Identificación

Tipo:
[TEC] [ARQ] [GOV] [MET] Cambio de estado académico no destructivo.

Estado:
Cerrada

Fecha de cierre:
2026-07-19

### Objetivo

Actualizar `estudiante.tipo_est` sin eliminar ni reconstruir permisos.

### Resultado

- Valida `id_usu`.
- Valida `tipo_est` entre 1 y 7.
- Preserva `permiso_login`.
- No asigna permisos 3 ni 5.
- Las capacidades se recalculan en una nueva sesión.

### Validación

Validación técnica:
Aprobada.

Validación funcional:
Aprobada por el usuario.

### Evidencia Git

Commit:
`bc14bcd80170ef1c685c15a717c9323b36bc6b7d`

Push:
Completado.

## TASK-EPIC003-RESOLVER-IDENTIDAD-LOGIN-001

### Identificación

Tipo:
[TEC] [ARQ] [GOV] [MET] Resolver de identidad de solo lectura.

Estado:
Cerrada

Fecha de cierre:
2026-07-19

### Objetivo

Introducir un resolver de identidad de solo lectura para login, usuario, estudiante, profesor y cardinalidades.

### Resultado

- Distingue login inexistente y login sin usuario.
- Distingue `NONE`, `SINGLE` y `MULTIPLE`.
- Resuelve estudiante desde `estudiante.usuario`.
- Resuelve profesor desde `profesor.usuario`.
- Obtiene `tipo_est` exclusivamente desde `estudiante`.
- Detecta multiplicidades e inconsistencias.
- No consulta permisos como fuente de identidad.
- No modifica sesión ni datos.
- Todavía no está integrado en `ajax/login.php`.

### Validación

Validación técnica:
Aprobada con observaciones no bloqueantes.

Aprobación:
Aprobada por el usuario.

Validación funcional:
No aplica todavía; el componente no tiene consumidor productivo.

### Evidencia Git

Commit:
`814ecb2d703d8df1638a53e660a068c5f29e3b06`

Push:
Completado.

## TASK-EPIC003-INTEGRAR-IDENTIDAD-DERIVADA-LOGIN-001

### Identificación

Tipo:
[TEC] [ARQ] [SEC] [GOV] [MET] Integración paralela de identidad derivada.

Estado:
Cerrada

Fecha de cierre:
2026-07-19

### Objetivo

Integrar `IdentityResolver` en paralelo durante el login sin sustituir permisos, sesiones históricas, capacidades, respuesta, navegación ni redirección.

### Resultado

- Se ejecuta después de autenticar.
- Utiliza el `id_login` real y validado como entero positivo.
- Reutiliza la instancia PDO entregada por `conexion()`.
- Mantiene `IdentityResolution` local a la solicitud.
- Compara estudiante derivado con permiso histórico 5.
- Compara profesor derivado con permiso histórico 4.
- Registra códigos técnicos seguros sin datos personales.
- No sincroniza permisos ni modifica sesiones históricas.
- No altera capacidades ni comportamiento observable.

### Validación

Revisión técnica:
Aprobada después del addendum correctivo.

Validación funcional:
Aprobada por el usuario. Codex no ejecutó la validación funcional.

### Evidencia Git

Commit:
`2c94d414c72cf3196d46ed766576881742c66e7c`

Mensaje commit:
`feat(identity): integrate derived identity into login`

Push:
Completado.

## TASK-EPIC003-CAPACIDAD-PERFIL-VER-001

### Identificación

Tipo:
[ARQ] [TEC] [SEC] [GOV] [MET] Capacidad transitoria para lectura del perfil propio.

Estado:
Cerrada

Fecha de cierre:
2026-07-19

### Objetivo

Introducir `perfil.ver` como capacidad transitoria basada en el permiso histórico 5 y proteger `read_est_perfil` mediante autorización backend centralizada.

### Resultado

- El permiso 5 produce `perfil.ver` con deduplicación estricta.
- La capacidad no se deriva desde estado académico ni identidad derivada.
- `read_est_perfil` exige `perfil.ver`, `admin` o `comite`.
- La denegación ocurre antes de consultar el modelo y devuelve HTTP 403 con JSON seguro.
- El titular se obtiene desde la sesión; no se aceptan identificadores desde el cliente.
- La respuesta exitosa permanece intacta.
- Las sesiones históricas se preservan.
- Los permisos y la base de datos permanecen sin cambios.
- La navegación y la redirección permanecen sin cambios.
- El permiso 5 continúa vigente como productor transitorio y el estado 6 conserva acceso temporal cuando mantiene ese permiso.

### Validación

Revisión técnica:
Aprobada con observaciones no bloqueantes.

Validación funcional:
Aprobada por el usuario. Codex no ejecutó la validación funcional.

### Evidencia Git

Commit:
`58d7745684aa1156e1feec0e1c80900cb1dce998`

Mensaje commit:
`feat(auth): introduce perfil.ver capability`

Push:
Completado.

## TASK-EPIC003-MIGRAR-GUARDIA-PERFIL-ESTUDIANTE-001

### Identificación

Tipo:
[ARQ] [TEC] [SEC] [GOV] [MET] Migración de la guardia backend del perfil estudiantil.

Estado:
Cerrada

Fecha de cierre:
2026-07-19

### Objetivo

Migrar la guardia backend de `form-doc/info.estudiante.php` desde señales
históricas directas hacia `perfil.ver`, conservando `admin` y `comite` como
fallback histórico.

### Resultado

- `Authorization` se carga antes de la guardia.
- La regla es `perfil.ver OR admin OR comite`.
- No existe fallback directo por `estudiante`.
- La redirección continúa siendo `../index.php`.
- La guardia ocurre antes de emitir HTML.
- El contenido histórico permanece intacto.
- No se modificaron formularios, scripts, endpoints, navegación, permisos ni capacidades ejecutables.
- El estado 6 conserva acceso temporal cuando mantiene permiso 5.
- La identidad derivada no concede acceso estudiantil.

### Validación

Revisión técnica:
Aprobada con observaciones no bloqueantes.

Validación funcional:
Aprobada por el usuario. Codex no ejecutó la validación funcional.

### Evidencia Git

Commit:
`018a3679b191afbfbe6fe277b3ad60e5184a5964`

Mensaje commit:
`refactor(auth): migrate student profile guard`

Push:
Completado.

## TASK-EPIC003-MIGRAR-REDIRECCION-PERFIL-ESTUDIANTE-001

### Identificación

Tipo:
[ARQ] [TEC] [SEC] [MET] Migración del destino estudiantil en la redirección central.

Estado:
Cerrada

Fecha de cierre:
2026-07-20

### Objetivo

Migrar en `index.php` la selección del destino estudiantil desde la señal
histórica `estudiante` hacia `perfil.ver`, preservando la prioridad y los
destinos existentes.

### Resultado

- Se importó `Authorization`.
- El bootstrap se carga antes de evaluar la redirección.
- La condición estudiantil utiliza `perfil.ver`.
- No existe fallback directo por `estudiante`.
- Se preservó la prioridad `admin/comite > aceptado > docente > perfil.ver > login`.
- Todos los destinos permanecen intactos.
- La navegación permanece sin cambios.
- Se eliminó para esta ruta el ciclo específico de una sesión con `estudiante`
  presente y `perfil.ver` ausente.
- La limpieza general de señales residuales de sesión permanece pendiente.

### Validación

Revisión técnica:
Aprobada con observaciones no bloqueantes.

Validación funcional:
Aprobada por el usuario con observaciones no bloqueantes. Codex no ejecutó la
validación funcional.

### Evidencia Git

Commit:
`2c2f4d906e6154e804154bd12a47eab7bd608c74`

Mensaje commit:
`refactor(auth): migrate student profile redirect`

Push:
Completado.

## TASK-EPIC003-MIGRAR-NAVEGACION-MI-PERFIL-ESTUDIANTE-001

### Identificación

Tipo:
[ARQ] [TEC] [SEC] [MET] Migración de la selección estudiantil del enlace Mi Perfil.

Estado:
Cerrada

Fecha de cierre:
2026-07-20

### Objetivo

Migrar en `form-doc/header.php` la selección estudiantil del enlace Mi Perfil
desde la señal histórica `estudiante` hacia `perfil.ver`, preservando la
prioridad docente y el fallback administrativo.

### Resultado

- Se importó `Authorization`.
- La condición estudiantil de `$miperfil` utiliza `perfil.ver`.
- Se preservó la prioridad `docente > perfil.ver > fallback administrativo`.
- No existe fallback directo por `estudiante` dentro de `$miperfil`.
- Las URL permanecen intactas.
- Se reutilizó el autoload existente, sin añadir otro bootstrap.
- Programa, Cursos y Calendario Académico permanecieron fuera del alcance.
- El cambio local de Cursos y el EOF fueron excluidos del commit mediante
  staging selectivo.

### Validación

Revisión técnica:
Aprobada con observaciones no bloqueantes y aislable.

Validación funcional:
Aprobada por el usuario con observaciones no bloqueantes. Codex no ejecutó la
validación funcional.

### Evidencia Git

Commit:
`4d3d4c805ef7b00c42ecb0d629c29940266b70f3`

Mensaje commit:
`refactor(auth): migrate student profile navigation`

Push:
Completado.

## TASK-EPIC003-MIGRAR-VISIBILIDAD-PROGRAMA-ESTUDIANTE-001

### Identificación

Tipo:
[ARQ] [TEC] [SEC] [MET] Migración de la visibilidad estudiantil del menú Programa.

Estado:
Cerrada con corrección aditiva posterior

Fecha de cierre:
2026-07-20

### Objetivo

Migrar en `form-doc/header.php` la señal estudiantil del wrapper Programa desde
`estudiante` hacia `perfil.ver`.

### Resultado acumulado

```text
admin OR comite OR aceptado OR perfil.ver OR docente
```

- Se realizó una sustitución funcional exclusivamente dentro del wrapper Programa.
- `$miperfil`, los enlaces hijos y las guardias backend permanecieron intactos.
- No se crearon capacidades ni se modificó el login.

### Incidencia y resolución

El commit funcional original incluyó accidentalmente una modificación local de
Cursos y el EOF del archivo. Ambos cambios ajenos fueron restaurados mediante
el commit correctivo aditivo, sin reescribir la historia.

### Validación

Revisión técnica:
Aprobada con observaciones no bloqueantes.

Validación funcional:
Aprobada por el usuario. Codex no ejecutó la validación funcional.

### Evidencia Git

Commit funcional original:
`eda073e2f61793a197bd8f0812883c2862144c4f`

Mensaje:
`refactor(auth): migrate program menu visibility`

Commit correctivo:
`3874da2b1e2d20c012f5dd5375c1e15678463afa`

Mensaje:
`fix(auth): remove unrelated changes from program menu migration`

Estado remoto:
Ambos commits publicados.

## TASK-EPIC003-CORREGIR-PUBLICACION-VISIBILIDAD-PROGRAMA-001

### Identificación

Tipo:
[TEC] [GOV] [MET] [DOC] Corrección aditiva de publicación.

Estado:
Cerrada

Fecha de cierre:
2026-07-20

### Objetivo

Restaurar exclusivamente Cursos y EOF incluidos accidentalmente en `eda073e2`,
preservando la migración autorizada del wrapper Programa.

### Resultado

- Cursos fue restaurado literalmente desde el padre de `eda073e2`.
- EOF fue restaurado literalmente desde el padre de `eda073e2`.
- El wrapper con `perfil.ver` fue preservado.
- El diff acumulado quedó reducido a la sustitución autorizada.
- La historia publicada no fue reescrita y no se utilizó force push.

### Validación

Revisión técnica:
Aprobada con observaciones no bloqueantes.

Validación funcional:
Aprobada por el usuario. Codex no ejecutó la validación funcional.

### Evidencia Git

Commit correctivo:
`3874da2b1e2d20c012f5dd5375c1e15678463afa`

Mensaje:
`fix(auth): remove unrelated changes from program menu migration`

Push:
Completado.

## TASK-EPIC003-ALINEAR-VISIBILIDAD-REGLAMENTO-001

### Identificación

Tipo:
[TEC] [SEC] [MET] Alineación de la visibilidad frontend de Reglamento.

Estado:
Cerrada

Fecha de cierre:
2026-07-20

### Objetivo

Alinear la visibilidad frontend del enlace Reglamento con su guardia backend
vigente.

Archivo:
`form-doc/header.php`

Regla implementada:

```text
reglamento.ver OR admin OR comite OR aceptado
```

### Resultado

- El enlace se muestra sólo para sesiones autorizadas por la guardia vigente.
- El acceso directo continúa gobernado por `form-doc/reglamento.php`.
- El wrapper Programa permanece intacto.
- `$miperfil`, Cursos y Calendario Académico permanecen intactos.
- No se crearon capacidades.
- No se modificaron login, sesiones, permisos ni estados productores.
- La URL, el texto, la clase y la posición del enlace permanecen intactos.

### Validación

Revisión técnica:
Aprobada con observaciones no bloqueantes y aislable.

Validación funcional:
Aprobada por el usuario. Codex no ejecutó la validación funcional.

### Evidencia Git

Commit:
`3d551a0e9aba88f3fb50644f612edd62c88a80cd`

Mensaje commit:
`refactor(auth): align regulation menu visibility`

Push:
Completado.

### Limitación

Una sesión con `reglamento.ver` aislada puede continuar autorizada por backend
sin mostrar Programa. La Task no modifica el wrapper ni declara paridad
bidireccional completa.

### Siguiente recomendación

Resolver el trabajo local pendiente de Cursos antes de intervenir nuevamente su
navegación o sus contratos de autorización. Esta recomendación no crea una Task
ni prioriza automáticamente una capacidad nueva.

## TASK-EPIC003-CENTRALIZAR-CONFIGURACION-SESION-APP-001

### Identificación

Tipo:
[ARQ] [SEC] [APP] [DEPLOY] [DOC] [GOV] Centralización de política de sesión.

Estado:
Cerrada

### Resultado

Política de sesión centralizada mediante un bootstrap dedicado. El alcance APP
aplica y verifica seis directivas antes de todos los `session_start()`
publicados y preserva las directivas dependientes del despliegue.

Cobertura publicada:
`33/33` callers.

### Validación

Revisión técnica:
Aprobada con observaciones no bloqueantes.

Validación funcional:
Aprobada por el usuario.

### Evidencia Git

Commit:
`7bd947661d443228ab0dccdb133cd117c9806104`

Mensaje:
`refactor(session): centralize application session policy`

Push:
Completado.

### Pendiente

`session.cookie_secure` y el despliegue HTTPS productivo requieren una
inspección posterior con evidencia externa de infraestructura. No se creó una
Task de despliegue.

## Pendientes relacionados con el cierre EPIC-003

- [TEC] [ARQ] El alta de estudiante no cuenta aún con una transacción global. La validación previa de `tipo_est` evita el fallo parcial observado, pero errores posteriores pueden persistir datos parciales.
- El permiso histórico `3` permanece preservado; su congelación definitiva requiere capacidades sustitutas en los módulos correspondientes.
- El working tree conserva un cambio no incluido en `ajax/estudiante.php`, rama `update-permiso-tipo-est`; requiere revisión y Task independiente antes de versionarse.
- `reglamento.ver` es un piloto. La extensión a otros módulos requiere Tasks independientes derivadas de la matriz institucional y ADR-002.
- No se realizó limpieza del registro parcial detectado durante la investigación; cualquier limpieza de datos requiere autorización y procedimiento separado.

## Bloqueo institucional del módulo Cursos

### Identificación

Fuente:
`Derivación institucional formal — Matriz operativa de Cursos`

Resultado:

```text
F. No se identificó autoridad competente.
```

### Estado

```text
Módulo:
Cursos

Matriz operativa:
No aprobada

Autoridad institucional:
No identificada

Documento Fuente Aprobado:
No existe

Protección provisional:
No aprobada

AT de materialización:
No autorizado

Task técnica:
No autorizada

Implementación local:
Parcial, no publicable y preservada

Eliminación física:
No autorizada
```

Las Tasks de consulta piloto y separación de capacidades de Cursos están
bloqueadas, no cerradas. El único antecedente institucional disponible indica
«solo vista» para estudiantes aceptados y matriculados. De él sólo puede
concluirse que no deben crear, actualizar, eliminar ni administrar profesores;
el alcance de lectura continúa pendiente.

La protección provisional P-B fue propuesta, pero no aprobada.

### Hipótesis institucional

La coincidencia pública plausible con el Doctorado en Antropología de la Pontificia Universidad
Católica de Chile no vincula formalmente esta intranet con esa institución y no
se utiliza para identificar autoridades ni adoptar decisiones.

### Decisiones institucionales pendientes

- Alcance de «solo vista».
- Lectura, detalle y descargas.
- Facultades de profesores.
- Responsable institucional de cada curso.
- Facultades del Comité como órgano.
- Facultades de integrantes individuales del Comité.
- Facultades administrativas.
- Separación entre administrador técnico y autoridad académica.
- Creación, aprobación y publicación.
- Actualización y reemplazo de archivos.
- Administración de profesores.
- Desactivación, restauración e históricos.
- Roles acumulativos.
- Navegación.
- Protección provisional.

### Condición de desbloqueo

Cursos sólo puede continuar cuando una autoridad competente emita un Documento
Fuente Aprobado que contenga institución y unidad propietaria, autoridad
emisora, fundamento de competencia, fecha y vigencia, actores, matriz por
operación, alcance de «solo vista», facultades de profesores, Comité y
administración, roles acumulativos, históricos, desactivación, protección
provisional cuando corresponda y aprobación verificable.

El documento debe declarar expresamente si autoriza elaborar AT técnico, diseñar
una protección provisional y crear una Task de implementación.

```text
AUTORIZA ELABORAR AT TÉCNICO:
Sí / No

AUTORIZA DISEÑAR PROTECCIÓN PROVISIONAL:
Sí / No

AUTORIZA CREAR TASK DE IMPLEMENTACIÓN:
Sí / No
```

### Continuidad

El bloqueo afecta únicamente la autorización operativa de Cursos. El proyecto
puede continuar con incrementos independientes que no modifiquen
`ajax/curso.php`, `form-doc/scripts/curso.js`, `form-doc/ver.curso.php`, la
navegación, las capacidades ni las reglas institucionales de Cursos. Esta
anotación no selecciona el siguiente incremento.
