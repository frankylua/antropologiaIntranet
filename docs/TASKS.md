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

## Pendientes relacionados con el cierre EPIC-003

- [TEC] [ARQ] El alta de estudiante no cuenta aún con una transacción global. La validación previa de `tipo_est` evita el fallo parcial observado, pero errores posteriores pueden persistir datos parciales.
- El permiso histórico `3` permanece preservado; su congelación definitiva requiere capacidades sustitutas en los módulos correspondientes.
- El working tree conserva un cambio no incluido en `ajax/estudiante.php`, rama `update-permiso-tipo-est`; requiere revisión y Task independiente antes de versionarse.
- `reglamento.ver` es un piloto. La extensión a otros módulos requiere Tasks independientes derivadas de la matriz institucional y ADR-002.
- No se realizó limpieza del registro parcial detectado durante la investigación; cualquier limpieza de datos requiere autorización y procedimiento separado.
