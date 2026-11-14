# TASK-EPIC001-ESTUDIANTE-CRUD-INTEGRAL-001

## Estado y objetivo

INACTIVACIÓN IMPLEMENTADA CON AUTORIZACIÓN EXPRESA «autorizo» DEL 14/11/2026. REVIEW TÉCNICO — APROBADO PARA VF. El usuario informó «se ejecuto correctamente» y se verificaron los efectos de la migración en modo lectura: CHECK Docente (1,2,3,4), catálogo Estudiante 6 inactivo(a) y habilitación activa; conteos de cuentas/personas/perfiles sin cambios. VF de inactivación/reactivación Docente y Estudiante PASS comunicada por el usuario. Respaldo local previo verificado; staging y commit autorizados expresamente por el usuario. Publicación pendiente de autorización de push. Los PASS anteriores se conservan sin atribuirlos a la nueva regla.

Prioridad: crítica para la muestra interna del CRUD principal al 20/11/2026. EPIC-001 — Autorización y permisos; integridad transaccional como dependencia técnica. Objetivo: completar en una sola unidad el CRUD operativo de Estudiante, reutilizando la protección personal ya publicada y resolviendo los defectos de consulta, programa, estado, alta y eliminación que afectan su uso principal. No se crean Tasks por operación ni un AT duplicado; el análisis focalizado y los criterios se incorporan aquí.

## Fuentes y línea base

- Plan aprobado: [ROADMAP — entrega interna](../roadmap/ROADMAP.md#entrega-prioritaria-aprobada--muestra-interna-al-20112026).
- Protección personal ya validada: [TASKS — TASK-EPIC001-ESTUDIANTE-READ-UPDATE-SEGURO-001](../TASKS.md#task-epic001-estudiante-read-update-seguro-001), commit `4e002b7`.
- Preservación de permisos al cambiar estado: [TASK-EPIC003-CAMBIO-ESTADO-SIN-RECONSTRUIR-PERMISOS-001](TASK-EPIC003-CAMBIO-ESTADO-SIN-RECONSTRUIR-PERMISOS-001.md).
- Marco institucional: [matriz global de actores](../governance/ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001.md). Sus reglas generales no sustituyen la aprobación específica de la matriz propuesta abajo.
- Código: `ajax/estudiante.php`, `src/Model/Estudiante.php`, formularios y consumidores identificados en el alcance.
- Metadatos de la BD inspeccionados en modo lectura el 13/11/2026: relaciones de login/usuario/estudiante y motores InnoDB de login, usuario, estudiante y linea_usuario. No se leyeron registros personales ni se hicieron escrituras.

Repositorio: `C:\xampp\htdocs\antropologiaIntranet`. Rama: `refactor/fase-0-seguridad`. HEAD de preparación: `55f0aa3658d7b0ce2cc3c4200f99b4ecc27ef971`. Publicación de ese commit confirmada y aprobada por el usuario; no se solicita una verificación remota adicional.

Trabajo preexistente que debe preservarse: cambios documentales en `docs/TASKS.md` y `docs/roadmap/ROADMAP.md`; borrador `docs/governance/AVISO-DATOS-AUTOALTA.json`; ADR-003 untracked y PDF untracked ajenos a la unidad. Staging vacío al preparar esta Task.

## Hechos confirmados y correcciones del alcance

| Operación | Evidencia | Resultado objetivo |
| --- | --- | --- |
| Listado, filtros y búsqueda | `read-est`, `read-filtrada` y `read-est-filtro` carecen de gate específico en sus ramas. `mostrarEstTipo()` contiene un JOIN incompleto; filtros/búsqueda no conservan todos los alias del listado. El frontend distingue únicamente algunos estados al elegir operación. | Consulta administrativa autorizada, parametrizada y coherente para los siete estados y la búsqueda por nombre; conservar identificadores y alias de los consumidores. |
| Programa READ/UPDATE | `read_est_prog` y `update-inf-prog` aceptan objetivo del cliente sin resolver ownership/autorización. UPDATE combina líneas, estudiante e institución sin transacción; SQL interpolado. | Gate específico, objetivo resuelto en servidor, referencias válidas, actualización atómica y errores inequívocos. |
| Estado | `update-permiso-tipo-est` valida parámetros pero no comprueba actor autorizado en su rama. | Autorizar por operación; mantener el catálogo 1–7 y la actualización exclusiva de `estudiante.tipo_est`, conservando permisos independientes. |
| CREATE | Ya existen gate de contexto, autoalta forzada a Postulante/permiso 5 y transacción. Persisten escrituras heredadas interpoladas y aceptación de permisos administrativos desde la entrada de creación administrativa. | Reutilizar el flujo y las aprobaciones previas; parametrizar las escrituras del objeto y derivar exclusivamente en servidor los permisos de creación de Estudiante conforme al contrato vigente, sin permitir inyección de roles administrativos/docentes. |
| DELETE | `delete` invoca la eliminación heredada del login indicado por el cliente. La BD conserva CASCADE hacia persona y múltiples antecedentes académicos. | Gate y confirmación explícitos, identidad Estudiante unívoca y rechazo sin cambios ante antecedentes, otros roles o dependencias incompatibles. |

La evidencia anterior es estática y de metadatos. No acredita explotación, funcionamiento de endpoints ni VF de esta unidad.

## Matriz aprobada por el usuario el 13/11/2026

| Operación | Admin | Comité | Estudiante |
| --- | --- | --- | --- |
| Alta administrativa | Sí | Sí | No; conserva autoalta pública vigente |
| Listar, buscar y filtrar estudiantes | Sí | Sí | No |
| Leer programa | Cualquier Estudiante | Cualquier Estudiante | Solo propio, respetando la lectura de perfil vigente |
| Editar datos del programa, guía, fechas y líneas | Sí | Sí | No; conserva edición personal propia ya aprobada |
| Cambiar estado académico | Sí | Sí | No |
| Eliminación física controlada | Sí, solo Estudiante simple sin antecedentes académicos ni otros roles; nunca el propio login | No | No |

Para actores no administrativos sin identidad Estudiante autorizada, denegar estas operaciones. No derivar derechos desde permiso 3 ni reinterpretar su superposición. La lectura/edición personal y el correo propio conservan exactamente el contrato de la Task publicada.

Las tres decisiones institucionales aprobadas son: gestión de programa/estado por Admin y Comité; programa propio de Estudiante solo lectura en esta entrega; eliminación física exclusiva de Admin, limitada a registros simples sin antecedentes. La aprobación expresa del usuario acredita estas reglas; su presencia previa en la interfaz no se utiliza como autorización.

DELETE no se sustituye silenciosamente por estado Eliminado. El estado 6 mantiene su significado y las limitaciones históricas de acceso documentadas; esta Task no implanta una política global nueva de revocación de sesiones.

## Alcance técnico previsto

Seis archivos de aplicación ratificados por el preflight focalizado:

```text
ajax/estudiante.php
src/Model/Estudiante.php
admin/scripts/ver.estudiante.js
form-doc/scripts/ficha.estudiante.js
form-doc/ficha.estudiante.php
form-doc/scripts/estudiante.js
```

Este documento y las referencias de TASKS/ROADMAP son fuentes documentales de la unidad. No se modifican métodos compartidos de Usuario/Login ni otros módulos por inferencia; la persistencia específica se encapsula en Estudiante sobre la conexión existente. Si no puede mantenerse esa delimitación, presentar el ajuste antes de modificar código fuera de alcance.

Reglas técnicas:

- Validar sesión, rol efectivo, identidad unívoca y objetivo antes de leer/escribir; controles en backend y coherencia de botones frontend.
- Parametrizar las consultas y escrituras intervenidas; no exponer pass ni usar credenciales del cliente como prueba de autorización.
- Actualizar programa, institución y líneas con una sola transacción y rollback; guardar sin cambios es válido. Las líneas deben ser identificadores de catálogo y no mezclarse con el checkbox de acuerdos.
- Validar institución, fechas, promedio y profesor guía. Las nuevas asignaciones de Profesor deben respetar su contrato vigente; las relaciones históricas no se eliminan por el estado actual del guía.
- CREATE debe preservar la autoalta y val-mail aprobados, la transacción existente y sus respuestas legítimas. Corregir únicamente los defectos concretos de seguridad/persistencia dentro del mismo objeto.
- Mantener el cambio de estado independiente de permiso_login; no añadir/eliminar/reconstruir permisos ni inventar una máquina nueva de transiciones. La conversión de estados no es una migración de roles.
- Antes de implementar DELETE, cerrar el inventario de dependencias reales, incluyendo referencias usadas por código que puedan no tener FK. Las relaciones académicas deben impedir la eliminación, sin borrar antecedentes para hacerla posible. Bloquear también identidad compartida, cuenta administrativa/docente, otros roles incompatibles y eliminación propia. La eliminación autorizada y sus comprobaciones deben ser atómicas; si no puede garantizarse conservación/concurrencia con la conexión y el esquema actuales, detener ese diseño y elevar la alternativa.
- Conservar los contratos exitosos o adaptar juntos todos los consumidores del alcance. HTTP 4xx/5xx no debe presentarse como éxito; rollback sin detalles SQL al cliente.

Exclusiones: nuevos roles/capabilities, migraciones, reescritura de autenticación, cambio de contraseña, modificación del popup aprobado o del aviso legal pendiente, CRUD de otros objetos, rediseño visual y limpieza general.

## Validación y aceptación

Pruebas automatizadas aisladas para acceso ajeno/no autenticado, parámetros manipulados, SQL parametrizado, rollback, permisos intactos, CREATE sin roles inyectados y DELETE con dependencias/otros roles. Preparación SQL contra el esquema real en modo lectura cuando corresponda. Escrituras de prueba fuera de registros reales; no ejecutar migraciones ni borrados reales como validación automática.

VF manual en un único bloque de seis recorridos:

Preparación: utilizar cuentas y registros de prueba, con acceso a Admin, Comité y Estudiante. Para DELETE reservar un registro simple expresamente descartable y otro con antecedentes que deban conservarse. No reutilizar registros reales como descartables. La VF se ejecuta desde la interfaz: no exige inspeccionar código, SQL ni fabricar peticiones. Las manipulaciones de identificadores y solicitudes y los controles internos de permisos se verifican técnicamente con los fixtures aislados.

| Caso | Recorrido y resultado esperado | Resultado |
| --- | --- | --- |
| VF-CRUD-01 — Altas | Alta administrativa desde Admin y Comité y [autoalta Estudiante](http://localhost/antropologiaIntranet/form-doc/estudiante.php) sin sesión. Probar institución existente y «otra», guía seleccionado y líneas. En autoalta marcar la aceptación obligatoria. Guardar y acceder con la cuenta creada. La autoalta queda Postulante. La derivación de permisos se verifica con las pruebas técnicas. | Altas Admin y autoalta Estudiante aprobadas; variantes adicionales sin resultado específico |
| VF-CRUD-02 — Consulta | En [Estudiantes](http://localhost/antropologiaIntranet/admin/ver.estudiante.php), listar, buscar y combinar búsqueda con un estado. Recorrer los siete estados, incluyendo uno sin coincidencias. No aparecen datos residuales ni «undefined»; se conserva el filtro. | PASS — confirmado por el usuario («pass») el 14/11/2026 |
| VF-CRUD-03 — Programa | Admin/Comité abren una ficha y guardan sin cambios; luego cambian institución, trabajo Sí/No, guía, fechas y líneas. Reabrir esa ficha y otra: no se arrastran radios ni líneas. Estudiante consulta su propio programa sin botón de edición. La denegación de UPDATE propio y READ ajeno se verifica técnicamente, sin pedir al usuario construir solicitudes. | Admin PASS — confirmado por el usuario («programa pass») el 14/11/2026; otras variantes sin resultado manual comunicado |
| VF-CRUD-04 — Estado | Admin/Comité cambian el estado y comprueban el nuevo valor al consultar y filtrar. Estudiante no dispone del control de cambio de estado. La conservación de permisos y el rechazo de solicitudes no autorizadas se verifican técnicamente. | Admin PASS — confirmado por el usuario («estado pass») el 14/11/2026; otras variantes sin resultado manual comunicado |
| VF-CRUD-05 — Eliminación | Admin abre el modal del registro simple de prueba y cancela: el registro se conserva. Después confirma su eliminación: desaparece del listado. Intenta eliminar el registro de prueba con antecedentes: se rechaza y la ficha permanece. Comité no tiene botones de eliminación y Estudiante no accede al listado administrativo. Cuenta propia, identidad compartida y otros roles se comprueban técnicamente. | SUSTITUIDA por la nueva regla de inactivación; nueva VF pendiente |
| VF-CRUD-06 — Integración | Desde el perfil Estudiante ya cargado, cambiar una dirección de prueba y reabrir la ficha: se conserva y el acceso sigue disponible. En la autoalta Estudiante abrir/cerrar el popup de muestra: conserva los datos y el check y permite continuar. No repetir la VF de Profesor ni las matrices personales previamente aprobadas; sus dependencias no se modificaron en esta corrección. | PENDIENTE |

Registrar `VF-CRUD-01` a `VF-CRUD-06` como PASS/FAIL/BLOQUEADA, con las variantes de rol necesarias. Puede responderse «todos PASS» si se ejecutó el bloque completo; detallar únicamente fallos o bloqueos. No repetir las matrices completas de Tasks cerradas sin regresión concreta. No equiparar pruebas aisladas con VF manual sin acuerdo expreso.

Aceptación: CRUD principal operativo con los controles aprobados; ausencia de modificaciones ajenas; conservación de relaciones/roles y atomicidad; diff revisado; pruebas técnicas conformes; VF aprobada o excepciones expresas registradas. No declarar el objeto completo si una operación principal queda pendiente o restringida fuera del contrato aprobado.

## Preflight técnico cerrado el 13/11/2026

Resultado: viable dentro de los seis archivos de aplicación, con la conexión existente y sin migraciones. La matriz está aprobada. No se implementó código, no se ejecutaron escrituras ni se bloquearon registros reales durante este preflight.

**Evidencia de persistencia:** aislamiento actual `REPEATABLE-READ`; las 20 tablas con referencias directas al conjunto login/usuario/estudiante son InnoDB; 25 referencias FK identificadas; no se observaron triggers en el esquema. No hay hijos FK adicionales de `linea_usuario` o `permiso_login`. La búsqueda focalizada de campos y código no identificó referencias adicionales de identidad sin FK en los consumidores examinados. Los campos de nombres libres de cotutela/tesis/publicación no se convierten en identificadores por inferencia; Documento almacena archivos sin propietario usuario directo y sus vínculos académicos quedan protegidos por el bloqueo del antecedente padre.

**Gate DELETE:** dentro de una única transacción se bloquearán la cuenta objetivo, sus permisos, la persona y el estudiante mediante lecturas actuales `FOR UPDATE`. Se exige un único login→usuario→estudiante, ausencia de perfil Profesor, fila administrativa o permisos administrativos/docentes/incompatibles, y objetivo distinto del actor. El permiso 3 no se interpreta como identidad Docente ni como prueba de autorización. Se verificará el rol Admin del actor en backend.

Solo pueden desaparecer como dependencias estructurales del registro simple las cuatro relaciones verificadas: `usuario.login`, `permiso_login.id_login`, `estudiante.usuario` y `linea_usuario.usuario`. Cualquier otra referencia al objetivo bloquea el DELETE con conflicto y sin cambios. La lista de bloqueo identificada es:

```text
admin.id_login
beca.alumno
cotutela.id_prof_guia
curso.profesor
estudiante.prof_guia
grado_academico.usuario
motivo_retiro.usuario
nombre_beca.propuesto_por
otra_publicacion.usuario
participacion.id_aut
participacion.id_coaut
pasantia.usuario
postdoctorado.usuario
profesor.usuario
proyecto_investigacion.id_coinv
proyecto_investigacion.id_inv
publicacion.usuario
publicacion_participacion.usuario
tesis.id_est_tes
tesis.id_prof_coguia
tesis.id_prof_guia
```

Las consultas de esas 21 comprobaciones se prepararon nativamente contra MySQL, sin ejecutarlas. Se usarán consultas parametrizadas con lista fija de identificadores SQL. Los bloqueos de padres y las FK impiden introducir nuevas referencias a esos padres mientras se elimina. Se comprobará que motores, referencias y condiciones relevantes del esquema sigan correspondiendo al mapa admitido; ante deriva o ambigüedad se deniega la eliminación, sin borrar antecedentes ni ampliar cascadas. Se exige confirmar una única eliminación y rollback ante cualquier fallo.

**Contratos READ cerrados:** listado/filtrado conservan `id_usuario`, nombres/apellidos, `tipo`, `id_tipo` e `id_login`; los siete estados usan la misma proyección. Programa conserva `prof_guia`, `tipo_est`, `fech_ingr`, `fecha_egr`, `prom_notas`, `trabaja`, `orient_disc`, `sit_previa`, `id_usuario`, `linea_inv`, `id_login` e `inst_usuario`, sin pass. La relación de líneas podrá leerse mediante LEFT JOIN para no impedir corregir registros heredados sin líneas; el consumidor deberá tratar `linea_inv` nula. Ambas proyecciones se prepararon contra el esquema real sin devolver datos personales. Errores HTTP 4xx/5xx quedan diferenciados de los arreglos/cadenas JSON exitosos, adaptando los consumidores juntos.

**CREATE/UPDATE atómicos:** se reutiliza `Institucion::resolverId()`, existente y parametrizado, que requiere una transacción activa. La creación de una institución nueva durante el alta de Estudiante se trasladará desde la petición separada del frontend a la transacción del alta, mediante el dato `nueva_institucion`, sin modificar el CRUD general de Institución. Estudiante encapsulará las escrituras específicas que hoy hereda interpoladas, sin alterar Usuario/Login globalmente. Programa y líneas compartirán la transacción y conexión; se revalidará identidad y la elegibilidad de nuevas asignaciones de guía, conservando relaciones históricas cuando no cambia el guía.

**Consumidores y alcance ratificados:** las operaciones de programa/listado/estado aquí intervenidas se consumen en los archivos ya delimitados. La vista compartida y su script permiten ocultar la edición del programa en el perfil Estudiante manteniendo el READ existente, sin modificar `info.estudiante.js` ni conceder autoridad desde el navegador. Se corregirán dentro del mismo objeto la carga de radios de trabajo, el reinicio de líneas al cambiar de ficha y el tratamiento de filtros 4–7. Se conserva el popup publicado; su check queda fuera de los selectores de líneas.

La evidencia permite diseñar la implementación y sus pruebas; las preparaciones SQL no acreditan por sí solas ejecución funcional, concurrencia probada ni VF. La inspección obtenida no se repite salvo cambios o una carencia concreta.

## Implementación y verificación del 13/11/2026

Implementación limitada a los seis archivos de aplicación aprobados:

- `ajax/estudiante.php`: gate específico por operación con permisos actuales de BD y objetivo resuelto en servidor; validación de entrada; transacciones y errores HTTP/JSON. Autoalta fuerza Postulante/permiso 5 y consume su token solo después del commit. Las altas administrativas derivan permisos en servidor conforme al contrato. Se mantienen los handlers personales y val-mail publicados.
- `src/Model/Estudiante.php`: persistencia específica parametrizada para cuenta/persona/programa/líneas; institución integrada mediante el resolver existente; guardado sin cambios válido; filtros coherentes para siete estados; nuevas asignaciones de guía requieren Profesor aceptado y el guía histórico sin cambios se conserva. Estado modifica solamente `tipo_est`. DELETE exige transacción, Admin, confirmación, identidad simple y ausencia de las 21 referencias; verifica mapa FK, motores, triggers y aislamiento antes de eliminar un único login.
- Consumidores: JSON explícito y errores visibles; filtros y búsqueda combinables; lista vacía limpia; radios de trabajo sin alterar sus valores y líneas reiniciadas por ficha; institución nueva en la misma petición; checkbox de acuerdos excluido de líneas; edición de programa oculta y detenida en el perfil Estudiante y eliminación ofrecida solo a Admin. El backend aplica la matriz con independencia de esas señales visuales. Se retira el log del objeto de alta que contenía contraseña.

| Comprobación | Resultado y límite |
| --- | --- |
| Backend CRUD aislado | 123/123 PASS: matriz de acceso, ownership, siete estados, filtros, entradas manipuladas, altas administrativas/públicas, token, guía histórico/nuevo, atomicidad y rollback. DELETE cubre cada una de las 21 referencias, otros roles/identidad y deriva de metadatos. SQLite en memoria, cascadas estructurales activas y metadatos de esquema simulados; no representa concurrencia real de MySQL. |
| Consumidores CRUD JS | 14/14 PASS: callbacks reales en Node VM con adaptador jQuery, incluyendo composición de peticiones, filtros, radios/líneas, errores, roles visuales y ausencia del log de contraseña. No es VF de navegador. |
| Regresión personal publicada | 71/71 backend y 12/12 callbacks JS PASS; sin cambios en Usuario/Login ni en el contrato de edición personal. El adaptador de listado se ajustó al JSON explícito. |
| Esquema real | 59 consultas preparadas nativamente con PDO MySQL, sin ejecutar escrituras ni devolver registros personales. El verificador privado de esquema DELETE ejecutado solo sobre metadatos resultó conforme. Preparación SQL no acredita ejecución funcional ni concurrencia. |
| HTTP local sin sesión | 12/12 PASS: ocho operaciones protegidas devuelven 401; CREATE sin contexto y selector público sin token devuelven 403; selector retirado 410 y catálogo de tipos 200. Ninguna cuenta creada o eliminada. |
| Sintaxis y diff | PHP lint en tres archivos y Node --check en tres scripts conformes; diff revisado y `git diff --check` sin errores. |

Evidencia reproducible fuera del repositorio, en `C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81`: `test-estudiante-crud.py`, `estudiante-crud-fixture.php`, `test-estudiante-crud-frontend.cjs`, `prepare-estudiante-crud.php` y sus resultados `estudiante-crud-*.json`; regresiones `test-estudiante.py` y `test-estudiante-frontend.cjs`. Fixtures sin credenciales reales. No se agregan esos artefactos al commit del producto por inferencia.

No hubo migraciones, modificaciones de registros reales, staging, commit ni push en esta implementación. Se preservan los cambios documentales previos, el borrador de aviso, ADR-003 y el PDF ajenos a esta unidad. HEAD y rama conservados. No se modificaron los archivos del popup durante la implementación original del 13/11; la ampliación posterior del 14/11 se documenta abajo. La VF del bloque anterior permanece PENDIENTE; no existe aprobación funcional implícita por pasar las pruebas técnicas.

## Review Técnico y corrección del selector — 14/11/2026

**Secuencia y autorización:** el protocolo obligatorio aportado por el usuario exige Review con dictamen antes de VF. El primer Review quedó **OBSERVADO** por incompatibilidad de `read_tipo` con `ajaxSelect` heredado: `cadenaMay(undefined)` interrumpía el inicializador antes de pedir el listado. La llamada era preexistente, conservada por esta Task; no se atribuye como una regresión introducida por este incremento. Las 14 pruebas JS sustituían `ajaxSelect`, por lo que no cubrían esa integración. El usuario autorizó expresamente «Corregir la incompatibilidad del selector dentro de la Task activa»; esa instrucción habilita esta corrección y no concede commit/push.

**Cambio acotado:** `Estudiante::mostrarTipo()` selecciona explícitamente `id_tipo_est,tipo` y usa `PDO::FETCH_BOTH` exclusivamente para este catálogo: mantiene los campos nominales de los consumidores actuales y las posiciones 0/1 de los heredados. Se retira en `admin/scripts/ver.estudiante.js` la llamada a `#tipo_est_edit`, inexistente en la vista, quedando una sola carga del catálogo del listado. Esta corrección modifica dos de los seis archivos ya autorizados; no añade archivos de aplicación. No modifica `info.estudiante.js`, el auxiliar global, la conexión, capacidades, reglas institucionales ni escrituras.

**Evidencia ejecutada:** `node test-estudiante-selector.cjs` usa la respuesta del endpoint PHP real en el fixture SQLite y carga los scripts reales `funcForm.js`, `funcAjax.js` y los inicializadores de listado, perfil y alta. Solo se adaptan DOM/transporte. Antes de corregir: 1/5 PASS, con errores reproducidos en los consumidores heredados. Después: 5/5 PASS; se comprueba que el listado sí solicita `read-est`, que el perfil registra y ejecuta su carga en el orden de scripts real, y que el alta carga los siete estados. También cubre campos nominales/numéricos y el selector heredado con JSON recibido como texto. No equivale a VF de navegador.

Comandos de regresión reejecutados tras la corrección: `node test-estudiante-crud-frontend.cjs` (14/14 PASS), `node test-estudiante-frontend.cjs` (12/12 PASS), `C:/xampp/php/php.exe -l src/Model/Estudiante.php`, `node --check admin/scripts/ver.estudiante.js` y `git diff --check`, conformes. Todos los scripts están en la carpeta de evidencia ya identificada. Nuevos artefactos: `test-estudiante-selector.cjs`, `estudiante-selector-before-results.json` y `estudiante-selector-results.json`.

La auditoría anterior del 14/11 reejecutó 123/123 casos backend CRUD y 71/71 de regresión personal sobre las escrituras/controles, que no cambia esta corrección. No se presentan como nuevas ejecuciones posteriores al ajuste del catálogo. Se conserva la evidencia del 13/11 de 59 preparaciones nativas y 12 comprobaciones HTTP; el intento HTTP de la auditoría del 14/11 encontró conexión local rechazada, por lo que no se atribuye un nuevo PASS HTTP. La VF requiere el servidor local disponible; no se arrancaron servicios por inferencia.

| Criterio de Review | Dictamen y evidencia |
| --- | --- |
| Línea base y alcance | Rama `refactor/fase-0-seguridad`, HEAD `55f0aa3`; seis archivos de aplicación realmente modificados y revisados. Dos documentos versionados modificados y cuatro archivos sin seguimiento ya identificados: Task actual, borrador de aviso, ADR-003 y PDF. Staging vacío. Sin archivos de aplicación ajenos. |
| Trazabilidad | La Task integral complementa la protección personal publicada en `4e002b7`. Matriz y ejecución aprobadas por el usuario; corrección del selector aprobada el 14/11. No reemplaza la Task cerrada ni revoca sus aprobaciones. |
| Autorización, autenticación y ownership | Controles del backend y pruebas aisladas conformes al contrato; el catálogo público solo contiene identificador/tipo. La corrección no concede acceso ni altera gates. |
| READ, WRITE, SQL y atomicidad | Proyecciones de cuenta/programa sin contraseña; escrituras específicas parametrizadas, transacciones y rollback revisados; estado sin reconstrucción de roles y DELETE condicionado a identidad/dependencias. El nuevo SELECT del catálogo es estático y explícito. |
| Consumidores y CRUD dentro del alcance | Observación del inicializador resuelta y regresión dirigida conforme con auxiliares reales. Alta, listado, programa, estado y eliminación cubiertos por evidencia técnica; su aceptación de uso real permanece pendiente de VF. |
| Regresiones y límites | 5 pruebas nuevas y 26 JS de regresión conformes; evidencia backend previa conservada. SQLite/Node VM no prueban concurrencia MySQL ni navegador completo. Autenticación heredada y texto/aceptación institucional siguen fuera de esta corrección; no se aprueba publicación abierta. |
| Gobierno técnico | Diff contrastado con el alcance, sin cambios institucionales, arquitectura global, migraciones, modificaciones de registros reales, staging, commit ni push. VF no ejecutada. |

**DICTAMEN: REVIEW TÉCNICO — APROBADO PARA VF.** La observación P1 del selector queda resuelta. Esta aprobación permite ejecutar el bloque manual preparado; no declara la Task cerrada ni concede commit/push. Los casos manuales se ajustan al protocolo: acciones observables en la interfaz y resultados concretos, dejando código/SQL y solicitudes manipuladas a la validación técnica.

## Condiciones de detención y estimación

Detener sin ampliar ni modificar archivos fuera de alcance si faltan fuentes/decisiones indispensables, cambian rama/HEAD o archivos de forma incompatible, aparecen datos/relaciones contradictorios, se requiere una migración o reglas nuevas, o no se puede preservar seguridad, historial y compatibilidad. Conservar todo trabajo preexistente.

Estimación preliminar: 10–16 horas efectivas para implementación y validación focalizada, condicionada al contrato y a cerrar las dependencias de DELETE sin migración. Si ese gate exige una solución mayor, actualizar la estimación antes de ejecutarla. No constituye garantía de entrega.

Entregables: diff acotado, evidencia técnica, VF en bloque y cierre mínimo en fuentes vigentes. Mantener staging vacío durante implementación. Commit y push requieren autorizaciones expresas separadas; este documento no las concede.


## Ampliación autorizada — aceptación obligatoria en ambas autoaltas, 14/11/2026

El usuario informó que podía registrarse sin marcar acuerdos y autorizó expresamente «si debe exigir estar de acuerdo con los terminos y condiciones» ante la propuesta de exigir la casilla en Docente y Estudiante, validar formulario/backend antes de guardar datos, mantener el texto de prueba y excluir altas administrativas. Esta aprobación amplía el alcance provisional del popup y la Task activa; no crea una Task paralela ni concede commit/push. El Review anterior cubre la revisión previa al cambio; esta ampliación requiere un nuevo dictamen antes de VF.

Contrato: la casilla permanece inicialmente desmarcada y al final del formulario. En autoalta pública solo se acepta `acuerdos_muestra=1` (entero 1 o cadena «1» en backend); ausencia, cero, arrays, booleanos y otros valores se rechazan con HTTP 422 / `ACEPTACION_REQUERIDA`, sin escrituras ni consumir el token de autoalta. La aceptación no reemplaza sesión/token/autorización ni permite cambiar estados o roles. Altas administrativas y agregar Docencia conservan sus reglas y no exigen el check ausente. El contenido permanece como texto de prueba; no se registra evidencia persistente de consentimiento ni se implementa normativa pendiente o una migración.

Alcance específico de esta ampliación: `form-doc/acuerdos.autoalta.php`, `form-doc/scripts/estudiante.js`, `form-doc/scripts/docente.js`, `ajax/estudiante.php`, `ajax/docente.php`. Dos ya pertenecían a la unidad original y tres se incorporan por esta autorización: el total acumulado de archivos de aplicación de la Task pasa de seis a nueve. Los archivos Docente se intervienen exclusivamente en la aceptación del CREATE público, sin ampliar al CRUD Docente completo, permisos o calendario. Preservar todo trabajo preexistente. Implementación, validaciones y nuevo Review registrados a continuación.


### Implementación de la ampliación

- `form-doc/acuerdos.autoalta.php`: `required`, inicialmente desmarcada; etiqueta «He leído y acepto los Acuerdos y condiciones», misma posición/tipografía y popup de prueba.
- `form-doc/scripts/estudiante.js`: rechazo del envío desmarcado y envío explícito de `acuerdos_muestra=1` con el token público; mantiene el selector exclusivo `name=linea`.
- `form-doc/scripts/docente.js`: rechazo en submit y en el envío contextual público, y envío explícito de la aceptación. Altas administrativas y agregar Docencia conservan su recorrido sin casilla.
- `ajax/estudiante.php`: aceptación estricta solo para CREATE público con contexto válido, antes de conectar/escribir; error 422 específico y token conservado.
- `ajax/docente.php`: mismo rechazo público antes de validar el resto del alta, consumir token o invocar persistencia integral. No cambia el estado inicial ni los permisos.

El usuario aclaró que el caso Docente Aceptado mencionado durante la prueba procedía de un alta Admin y que la autoalta funcionaba correctamente; no se registra como un fallo de estado. La obligatoriedad de acuerdos constituye el nuevo requisito explícito, no una reinterpretación retroactiva del popup de muestra aprobado.

### Validaciones ejecutadas y Review de la ampliación

| Evidencia | Resultado |
| --- | --- |
| `python test-autoalta-acuerdos.py` | 36/36 PASS. Endpoints y modelos PHP reales con SQLite aislado: ausencia, cero, vacío, array, booleanos, valor distinto, `on`, null, float y espacios se rechazan; 1/«1» permiten alta. Se comprueba ausencia de escrituras ante rechazo, token conservado, roles/estados iniciales y altas Admin/Comité sin requisito. |
| `node test-autoalta-acuerdos-frontend.cjs` | 13/13 PASS. Handlers reales y render PHP de la casilla: desmarcada bloquea, marcada envía token/aceptación, desmarcar vuelve a bloquear, errores conservan datos, líneas no incluyen acuerdos, administración/agregar Docencia sin requisito. Adaptador DOM/transporte; no navegador completo. |
| HTTP local, `python -` | 8/8 PASS: GET 200 de ambas páginas con `required` y sin `checked`; POST público con token válido sin aceptación o con cero responde 422 / `ACEPTACION_REQUERIDA`; consultas posteriores confirman el mismo token. No se enviaron datos personales ni altas exitosas a la BD real. En esta ejecución el servidor local sí estuvo disponible. |
| Regresión CRUD Estudiante | `python test-estudiante-crud.py`: 123/123 PASS. Las altas públicas exitosas del fixture ahora incluyen aceptación conforme al nuevo contrato; el rechazo por falta de aceptación se cubre específicamente en la nueva suite. |
| Regresión de consumidores | `node test-estudiante-crud-frontend.cjs`: 14/14 PASS; `node test-estudiante-frontend.cjs`: 12/12 PASS; `node test-estudiante-selector.cjs`: 5/5 PASS. El adaptador representa la casilla ausente en altas administrativas y marcada en la autoalta exitosa. |
| Sintaxis | PHP lint en `ajax/estudiante.php`, `ajax/docente.php` y `form-doc/acuerdos.autoalta.php`; Node --check en ambos scripts de alta: conformes. `git diff --check` sin errores. |

Artefactos en la carpeta externa de evidencia ya identificada: `autoalta-acuerdos-fixture.php`, los dos nuevos scripts de prueba y `autoalta-acuerdos-backend-results.json`, `autoalta-acuerdos-frontend-results.json`, `autoalta-acuerdos-http-results.json`. El fixture extiende únicamente las columnas necesarias de Profesor en SQLite y utiliza los modelos reales. No ejecuta migraciones reales; no acredita concurrencia MySQL ni VF de navegador.

**Review:** diff real de los cinco archivos de esta ampliación contrastado con la aprobación; los otros cuatro archivos de aplicación de la unidad no recibieron cambios en esta ampliación. Se conservan autenticación/contexto/token antes del gate y autorización independiente de la casilla; la validación ocurre antes de las escrituras y no altera SQL, transacciones, estado, roles ni calendario. Mensajes HTTP y consumidores adaptados juntos. El alcance acumulado queda conciliado en nueve archivos de aplicación, dos documentos versionados modificados y los cuatro archivos sin seguimiento ya identificados; staging vacío, misma rama/HEAD `55f0aa3`, sin commit ni push. No hubo cambios ajenos ni decisiones institucionales adicionales.

Riesgos/límites: permanece el texto de prueba y no existe registro persistente/versionado de consentimiento; este control funcional no cierra el aviso institucional pendiente ni aprueba publicación abierta. Las pruebas aisladas y HTTP no sustituyen el uso completo del navegador. La matriz de estados/roles y el resto del CRUD Docente no forman parte de esta ampliación.

**DICTAMEN: REVIEW TÉCNICO — APROBADO PARA VF.** Criterios técnicos de obligatoriedad, rechazo sin escrituras, preservación de token y compatibilidad administrativa conformes. Criterio funcional manual pendiente. No se autoriza staging/commit/push ni se declara cierre.

### VF adicional de acuerdos — pendiente, solo interfaz

Usar datos ficticios y correos nuevos. Para las autoaltas, ventana privada sin sesión. Completar todos los demás campos válidos antes de intentar Guardar, para comprobar específicamente la aceptación; conservar las selecciones de líneas. La VF anterior de la Task sigue pendiente: esta ampliación añade únicamente los siguientes recorridos, sin repetir matrices técnicas ni exigir SQL/código.

| Caso | Acciones | Resultado esperado | Estado |
| --- | --- | --- | --- |
| VF-AC-01 — Docente | Completar la autoalta sin marcar acuerdos y pulsar Guardar. Abrir/cerrar el popup, marcar y desmarcar; intentar guardar desmarcada. Finalmente marcar y guardar. | Desmarcada no permite el alta, informa el requisito y conserva campos. El popup mantiene el texto de prueba; abrirlo no equivale a aceptar. Marcada permite continuar el registro y la nueva cuenta queda Pendiente. | PASS — confirmado por el usuario el 14/11/2026 |
| VF-AC-02 — Estudiante | Repetir el recorrido anterior en autoalta Estudiante, seleccionando guía y líneas válidas. | Mismo bloqueo y conservación de campos; marcada permite guardar y la cuenta queda Postulante. Las líneas elegidas se conservan y no incorporan acuerdos como una línea. | PASS — confirmado por el usuario («estudiante PASS») el 14/11/2026 |
| VF-AC-03 — Administración | Con Admin/Comité, abrir ambas altas administrativas y guardar registros de prueba válidos. | No aparece la casilla ni se exige aceptación; se mantienen los estados administrativos vigentes. | Admin PASS («pf pass», 14/11/2026); Comité sin resultado manual comunicado |

Responder PASS/FAIL/BLOQUEADA por caso. Detallar únicamente fallos y causa de bloqueo. No contabilizar la explicación previa del usuario sobre el estado Docente como PASS de esta funcionalidad nueva.


## Corrección de envío y aviso de aceptación — 14/11/2026

**Incidencia funcional:** el usuario mostró la casilla marcada junto al aviso `Debe aceptar los acuerdos y condiciones...` y confirmó que el aviso volvía después de pulsar Guardar. Su criterio explícito es que marcar la casilla basta para aceptar, sin abrir el popup. No se registra PASS ni cierre de esta incidencia por inferencia.

**Diagnóstico comprobado:** las dos páginas servían un único checkbox y el JS actual incluía `acuerdos_muestra`, pero los scripts de alta se cargaban con URL sin versión (`scripts/docente.js` y `scripts/estudiante.js`). Esto permitía reutilizar una versión anterior que no enviara el campo al backend actualizado. Además, el aviso de aceptación previo no se limpiaba al marcar. No se inspeccionó la caché ni la petición del navegador del usuario: no se atribuye como causa definitivamente confirmada de su rechazo. Las comprobaciones HTTP directas con aceptación marcada superan el gate sin abrir el popup.

**Corrección dentro de la funcionalidad solicitada:** las vistas `form-doc/docente.php` y `form-doc/estudiante.php` añaden `?v=filemtime(...)` al script de alta correspondiente para servir la versión actual. Ambos scripts limpian al marcar únicamente el aviso específico de aceptación; no ocultan otros errores ni guardan automáticamente. Se conservan el envío explícito de la aceptación, la validación backend y la obligatoriedad. No se requiere haber abierto o recorrido el modal.

Esta reparación de la entrega del mismo formulario incorpora dos vistas de entrada a la lista documental: el alcance acumulado de aplicación pasa de nueve a once archivos. La corrección actual modifica esas dos vistas y los dos scripts de alta existentes; no incorpora modelos, endpoints, estados, permisos ni nuevas reglas. No se considera una ampliación a CRUD Docente, calendario o consentimiento persistente.

**Validaciones ejecutadas:**

- `node test-autoalta-acuerdos-frontend.cjs`: 17/17 PASS; incluye marcar tras rechazo, limpiar solo el aviso de aceptación y reintentar enviando `1` sin abrir popup, en ambas altas. Se mantienen los casos de bloqueo desmarcado y administración.
- `python test-autoalta-acuerdos-http.py`: 12/12 PASS con HTML y assets reales: una casilla requerida/desmarcada por página, URL de script con mtime actual y contenido con envío de aceptación; desmarcada responde `ACEPTACION_REQUERIDA`; marcada con solo token/aceptación supera ese gate y se rechaza por los otros campos deliberadamente omitidos (`DATOS_INVALIDOS` / `IDENTIFICADOR_INVALIDO`), sin crear cuentas reales. El token permanece vigente tras esos rechazos. Esto no acredita un alta completa en el navegador del usuario.
- Regresión afectada: 14/14 de `test-estudiante-crud-frontend.cjs` y 5/5 de `test-estudiante-selector.cjs` PASS.
- PHP lint en ambas vistas, Node --check en ambos scripts y `git diff --check`: conformes.

Scripts y resultados en la carpeta de evidencia habitual; el nuevo script HTTP queda reproducible en `test-autoalta-acuerdos-http.py`. No se repiten pruebas de escrituras/modelos no intervenidos; permanece la evidencia anterior de 36 casos backend de aceptación y 123 del CRUD.

**Review Técnico:** revisados los cuatro diffs afectados y la conciliación Git; carga versionada limitada a los scripts de las propias vistas, sin script global ni invalidación indiscriminada. El cambio `change` no sustituye el guard backend ni cuenta abrir el popup como aceptación. Staging vacío, misma rama/HEAD `55f0aa3`; sin migraciones, cuentas reales creadas, commit ni push. Riesgos residuales: el navegador debe recargar el formulario para recibir la versión nueva; la causa exacta de la petición anterior del usuario no quedó capturada. El texto sigue siendo de prueba y no se persiste consentimiento.

**DICTAMEN: REVIEW TÉCNICO — APROBADO PARA REPETIR VF DEL CASO OBSERVADO.** La incidencia funcional continúa pendiente de confirmación del usuario. Recargar con Ctrl+F5 ambas páginas que se estén probando (conservar previamente los datos ficticios que se necesiten); comprobar desmarcada bloqueada, marcar sin abrir el modal y pulsar Guardar con el resto de datos válidos. El aviso específico debe desaparecer y la aceptación no debe impedir el registro. Si vuelve, registrar el formulario y el mensaje exacto, sin dar el caso por aprobado. No repetir todos los recorridos anteriores sin una nueva regresión concreta.


## VF Docente aprobada y aviso nativo de Estudiante — 14/11/2026

**Resultado funcional comunicado:** «vf completa para docente queda en pass» se registra como PASS de VF-AC-01 (autoalta/acuerdos Docente), sin extrapolar al cierre del CRUD Docente completo ni a VF-AC-03. El usuario informó que en Estudiante, intentar Guardar sin aceptar desplazaba la página sin aviso útil; ese recorrido queda OBSERVADO, no PASS.

**Diagnóstico y límite de los tests previos:** la casilla HTML `required` invalida el formulario y detiene el envío antes de disparar el evento `submit`; el handler JS de Estudiante solo mostraba su mensaje al recibir ese evento. En navegador real se comprobó que el control pertenecía a `form_usuario`, estaba inválido y el mensaje propio permanecía vacío mientras actuaba la validación nativa. Los tests que invocaban directamente el callback submit no cubrían este recorrido. No se añade un mensaje de consola como requisito: el error debe ser visible en la interfaz.

**Corrección:** únicamente `form-doc/scripts/estudiante.js`. Se captura el evento nativo `invalid` de la casilla, se cancela la presentación por defecto y se muestra el aviso propio, resaltado de control y `aria-invalid=true`; foco sin scroll automático y desplazamiento explícito a la casilla. La guardia submit reutiliza el mismo aviso para envíos programáticos. Marcar limpia aviso/resaltado/ARIA y basta para aceptar, sin abrir el popup. Se mantiene `required` y el rechazo backend. No se modifica Docente, el control compartido, endpoints, persistencia, estados o roles. El alcance acumulado sigue siendo once archivos de aplicación.

**Comprobación de navegador realizada por Work:** tab nuevo en localhost, datos ficticios, paso personal → académico. Tras recargar el script corregido, se activa Guardar mediante Enter con la casilla desmarcada: aviso visible, foco `acuerdos-autoalta-check`, `aria-invalid=true`, promedio y situación ocupacional conservados. Al marcar sin abrir el popup: casilla marcada, aviso oculto y vacío, atributo ARIA retirado y datos conservados. No se completó una cuenta: se dejaron incompletos los datos académicos y no se envió un alta válida. Se observó también que el footer fijo puede tapar Guardar en posiciones intermedias de scroll; no se amplía esta corrección a un rediseño del layout. La captura del aviso corregido se conserva en la carpeta de evidencia como `vf-estudiante-acuerdos-requeridos.png`.

**Validaciones:** `node test-autoalta-acuerdos-frontend.cjs` 19/19 PASS, con los dos casos nuevos de evento `invalid` y limpieza posterior; `node test-estudiante-crud-frontend.cjs` 14/14 PASS; `node --check form-doc/scripts/estudiante.js` y `git diff --check` conformes. Se adapta el fixture DOM para modelar foco, desplazamiento y retirada del atributo ARIA. No se repiten suites backend sin cambios. La prueba de navegador anterior es evidencia técnica de Work para el bloqueo y limpieza, no el PASS del usuario de un registro completo.

**Review Técnico:** diff limitado al script de Estudiante, contrastado con el fallo reportado; sin desactivar validación nativa ni gate backend y sin cambiar los flujos ya aprobados de Docente. Estados Git/HEAD `55f0aa3` y staging vacío preservados. Sin migración, creación de cuentas reales, commit o push. Riesgo residual: pendiente repetir el recorrido completo de autoalta Estudiante en el navegador del usuario, con el script actualizado.

**DICTAMEN: REVIEW TÉCNICO — APROBADO PARA REPETIR VF-AC-02.** Conservar PASS de VF-AC-01. Repetición: recargar Estudiante con Ctrl+F5, completar campos, intentar Guardar desmarcada (debe aparecer el aviso junto al control, conservar datos y no registrar); marcar sin abrir el popup (debe limpiar aviso) y guardar con datos válidos (debe crear Postulante). No repetir VF Docente ni declarar cerrada la Task por esta corrección.


### Confirmación funcional de Estudiante — 14/11/2026

El usuario confirmó «estudiante PASS» al repetir VF-AC-02 tras la corrección del aviso nativo. Se cierra esa observación funcional: VF-AC-01 Docente y VF-AC-02 Estudiante quedan PASS del usuario. No se reejecutan pruebas técnicas ni se exige repetir esos recorridos. VF-AC-03 Administración y los recorridos del CRUD integral que no tienen resultado comunicado conservan su estado pendiente; este PASS no constituye aprobación global de la Task ni autoriza staging, commit o push.


### Confirmación de altas Admin — 14/11/2026

El usuario respondió «pf pass» a la instrucción concreta de abrir y guardar altas Docente y Estudiante desde Admin, sin casilla ni exigencia de aceptación. Se registra Altas Admin PASS y ese resultado en VF-AC-03. No se extrapola a una ejecución manual con Comité ni a las variantes de institución/correo/login de VF-CRUD-01 no informadas; se conserva su cobertura técnica anterior, sin repetir las altas aprobadas. La siguiente comprobación funcional es VF-CRUD-02 (consulta y filtros). Sin cambios de código, pruebas repetidas, staging, commit ni push por esta confirmación.


### Confirmación de consulta y filtros — 14/11/2026

El usuario respondió «pass» al recorrido concreto de búsqueda por nombre, combinación con estado, los siete filtros y búsqueda sin coincidencias. Se registra VF-CRUD-02 PASS, sin extrapolar a programa, estados o eliminación. Siguiente recorrido: VF-CRUD-03. No se modifica código ni se repiten pruebas por registrar este resultado; sin staging, commit ni push.


### Confirmación de edición del programa — 14/11/2026

El usuario respondió «programa pass» al recorrido indicado con sesión Admin: guardado sin cambios, edición del programa, reapertura y comprobación de que otra ficha no arrastra trabajo/líneas. Se registra la variante Admin de VF-CRUD-03 PASS. No se atribuye ejecución manual a Comité o al perfil Estudiante; los gates correspondientes conservan la evidencia técnica anterior. Siguiente recorrido: VF-CRUD-04 (estado). Sin modificaciones de código, nuevas pruebas, staging, commit ni push por este registro.


### Confirmación de cambio de estado — 14/11/2026

El usuario confirmó «estado pass» para el recorrido Admin de cambio a Aceptado y Matriculado, consulta de ficha/listado/filtro y restauración del estado original. Se registra la variante Admin de VF-CRUD-04 PASS; la preservación interna de permisos conserva su evidencia técnica y no se atribuye al usuario inspección SQL ni ejecución de variantes no comunicadas. Siguiente: VF-CRUD-05 (eliminación controlada). Sin cambios de código, pruebas repetidas, staging, commit ni push por este registro.


## Cambio de contrato: inactivación y relaciones históricas — 14/11/2026

**Origen y precedencia:** el usuario reportó bloqueo al eliminar Docente con antecedentes sin mensaje visible y solicitó que Eliminar signifique inactivar, conservando la BD y ocultando el registro de la grilla. Posteriormente precisó, antes de continuar, que el estado se edita en ficha → Datos del programa por Admin y Comité y determina la presencia en la grilla. Esta regla sustituye el DELETE físico anterior para Docente y Estudiante; no se ejecutarán más pruebas de borrado físico para intentar cerrar el contrato sustituido. Los PASS de otras operaciones se conservan, sin atribuirles cobertura de la nueva regla.

Reglas funcionales expresas:

- Docente y Estudiante tienen estado de registro y pueden inactivarse sin destruir cuenta, persona, especialización ni antecedentes.
- Admin y Comité editan el estado desde Datos del programa. Los titulares no obtienen esa facultad por esta decisión.
- Eliminar cambia automáticamente a Inactivo; se permite aunque existan antecedentes académicos. No se elimina ni desvincula el historial para habilitar la operación.
- Inactivo se oculta de la grilla habitual y de nuevas selecciones operativas; se conserva un listado de inactivos para recuperación, aprobado por el usuario.
- Las relaciones donde el perfil participa en antecedentes de otros actores conservan sus identificadores y representación histórica (por ejemplo Profesor guía/coautor). No se sustituyen nombres ni se borran vínculos. Mantener el registro histórico no lo hace elegible para nuevas asignaciones.
- Decisión sobre acceso confirmada por el usuario: bloquear si la cuenta solo dispone del perfil inactivo y conservar los demás roles válidos. No borrar/reconstruir permisos para implementar inactivación ni bloquear indiscriminadamente las cuentas con otros perfiles válidos.
- Reactivación por Admin y Comité: el usuario respondió «Comité también puede reactivar», sustituyendo su elección inicial de exclusividad Admin. No queda pendiente esa decisión funcional.

**Hallazgos técnicos, solo lectura:** `profesor.estado_profesor` tiene dominio 1/2/3 (Pendiente/Aceptado/Rechazado), mientras Estudiante usa `tipo_est` 1–7 incluyendo 6 Eliminado. En metadatos reales no se encontró una columna de actividad en profesor/estudiante/login/usuario. Las cuatro tablas son InnoDB. No se equipara Rechazado a Inactivo por inferencia. Es necesario cerrar el diseño de representación, compatibilidad de estados existentes, sesión/acceso y eventual migración antes de aplicar cambios. No se preparó ni ejecutó una migración con esta inspección.

En Docentes, el callback de eliminación escribe en `#mnsj_elim` y `#eliminado`, ausentes de la vista y sus componentes inspeccionados; la grilla sí ofrece `#mensaje_estado_docente`. Esto explica un defecto concreto de presentación; además, el error del endpoint es genérico y no ofrece alternativa de conservación. La corrección debe ofrecer un mensaje visible de operación inactiva y explicar que se conserva el historial, sin sugerir borrar antecedentes.

**Alcance en replanteamiento:** no es una modificación cosmética del botón. Afecta modelos, listados/filtros, selectores, formularios de programa, endpoints y sesiones/autorización para que las URL directas y sesiones previas no eludan la inactividad. Se conservará la sesión de otros roles válidos. El diseño y la lista de archivos deben ratificarse antes de modificar código fuera del alcance anterior; las migraciones requieren autorización expresa. No se reabre el CRUD de antecedentes ni se migra su identidad por esta decisión.

Estado: decisiones funcionales registradas y reactivación confirmada para Admin/Comité. Diseño técnico propuesto a continuación; aplicación de código transversal y migración pendientes de autorización específica del alcance ampliado. Esta fase solo leyó fuentes/metadatos y actualizó documentación; no cambió código de aplicación, registros reales, staging, commit ni push. La Task no está cerrada.


### Diseño técnico propuesto para aprobación — inactivación

**Representación mínima:** reutilizar `estudiante.tipo_est=6` con etiqueta `Inactivo(a)` y ampliar `profesor.estado_profesor` con `4=Inactivo`. Se conservan los demás identificadores y estados. No se crea un rol/capability ni una columna de actividad paralela. Los registros Estudiante ya en estado 6 quedan inactivos bajo el nuevo contrato. La reactivación se realiza eligiendo explícitamente un estado válido distinto de Inactivo desde Datos del programa; no se inventa un estado anterior desconocido. No convertir Rechazado a Inactivo ni pasar masivamente inactivos a Aceptado.

**Matriz operativa propuesta:**

| Operación | Admin | Comité | Titular |
| --- | --- | --- | --- |
| Leer grilla normal (sin inactivos) y consultar historial | Sí | Sí | Conserva solo su acceso permitido vigente |
| Consultar filtro/listado Inactivos y abrir ficha | Sí | Sí | No obtiene acceso propio por estar inactivo |
| Cambiar estado, inactivar o reactivar desde Datos del programa | Sí | Sí | No |
| Acción Eliminar → Inactivar | Conserva la facultad actual de eliminación | No se amplía la acción de grilla; puede inactivar mediante estado | No |

Se mantiene la protección frente a la acción Eliminar sobre la cuenta propia hasta que exista una instrucción distinta; inactivar un perfil de otra cuenta no puede destruir los otros roles de esa cuenta. No eliminar filas de login, usuario, profesor, estudiante, permiso_login o antecedentes. Estado y datos del programa deben guardarse atómicamente; guardar solo estado de forma válida no debe exigir alterar otros datos. Cambiar a Inactivo devuelve un mensaje visible: se ocultó de la grilla habitual y se conserva historial; se indica el filtro Inactivos para reactivar. La confirmación del botón explica que no se borrará la información.

**Acceso y sesiones:** filtrar roles/capacidades efectivos de cada perfil inactivo en login y en peticiones de sesiones ya abiertas. Rechazar cuenta sin otro rol válido; conservar roles Admin, Comité u otro perfil académico activo. El permiso histórico 3 no puede mantener por sí solo el acceso del perfil inactivado. Los permisos almacenados no se borran ni reconstruyen. La capa de sesión debe funcionar con usuarios anónimos, perfiles múltiples válidos y estados/cardinalidades incompatibles, sin permitir una URL directa como alternativa al bloqueo. Reactivar no asigna nuevos permisos; se vuelve a derivar el acceso autorizado. No reescribir hashing, contraseñas ni el resto del sistema de autenticación.

**Relaciones y selectores:** la lectura de nombres/antecedentes históricos continúa resolviendo los mismos IDs aunque el perfil esté inactivo. La selección de un Profesor para nuevas asignaciones de guía mantiene la condición Aceptado, excluyendo Inactivo. Se conserva un guía histórico cuando la relación no cambia. No se moderniza el CRUD de Proyectos, Tesis u otros antecedentes ni se reasignan sus autores por esta Task.

**Migración preparada, no ejecutada:** `migrations/TASK-DB-MIGRATION-PERFILES-INACTIVOS-001.sql`. Verificación de metadatos reales: `chk_profesor_estado` permite actualmente (1,2,3), y el catálogo Estudiante 6 se llama `eliminado(a)`. El borrador amplía el CHECK a (1,2,3,4) y cambia solo esa etiqueta a `inactivo(a)`, sin modificar estados de personas, permisos o relaciones. DDL con confirmación implícita: requiere respaldo previo y despliegue coordinado, sin prometer transacción global. Reversión del CHECK condicionada a ausencia de estado 4; nunca reactivar en masa ni borrar para revertir.

Las dos sentencias mutadoras se prepararon nativamente mediante PDO, SIN execute: ambas conformes. Después se volvió a consultar la BD y continuaron el CHECK (1,2,3) y etiqueta `eliminado(a)`, acreditando que no se aplicó la migración. No hay pruebas de ejecución de esta nueva migración ni implementación de la regla.

**Alcance adicional de aplicación para ratificar:**

```text
src/Model/Docente.php
src/Model/Login.php
src/Security/AcademicSession.php (nuevo)
src/bootstrap/app.php
ajax/login.php
form-doc/header.php
form-doc/ficha.docente.php
form-doc/scripts/ficha.docente.js
admin/ver.docente.php
admin/scripts/ver.docente.js
admin/ver.estudiante.php
```

Junto a los once archivos de aplicación ya intervenidos, el máximo previsto pasa a veintidós, más la migración y documentación. La ampliación es transversal porque el usuario exige acceso bloqueado sin perder otros roles y conservación de historial, no solo ocultar filas. Los archivos previos se retocan únicamente donde sea necesario para los nuevos estados, UPDATE de programa, filtros/mensajes y sustitución del DELETE. El helper nuevo centraliza la derivación/actualización de sesión para evitar reglas divergentes; se integra después de iniciar sesión y antes de atender vistas/operaciones protegidas. No modificar módulos ajenos por inferencia; si el preflight de implementación detecta una frontera no cubierta, detener esa parte y comunicarla.

**Validación prevista:** fixtures aislados de inactivar/reactivar con antecedentes propios y relaciones en terceros (mismos IDs y conteos), rollback, búsqueda normal/inactivos, ambos roles de gestión, perfil propio sin facultad, rechazo de cuentas solo inactivas y conservación de otros roles, sesiones abiertas y URL directa, guía histórico/nuevas asignaciones, cambios de estado/datos atómicos y regresiones afectadas. Preparación nativa de SQL y esquema antes/después, review completo y dictamen antes de VF. No se requiere repetir las autoaltas/acuerdos aprobadas salvo regresión concreta.

**Punto de control:** se solicita autorizar la implementación de este alcance ampliado. La ejecución de la migración real se pedirá como paso final separado cuando existan código y pruebas preparados y respaldo verificable. No ejecutar el SQL por aprobar solamente la regla funcional o la implementación. No hay autorización de staging, commit o push.

## Implementación autorizada y Review Técnico de inactivación — 14/11/2026

La respuesta «autorizo» posterior al punto de control ratifica el alcance transversal de 22 archivos de aplicación, incluida la preparación del SQL. No autoriza ejecutarlo. Se implementó dentro de la Task activa, conservando cambios previos y archivos externos. El plan original de eliminación física queda sustituido, no pendiente de aprobación funcional.

### Archivos efectivamente modificados y revisados

- `admin/scripts/ver.docente.js`
- `admin/scripts/ver.estudiante.js`
- `admin/ver.docente.php`
- `admin/ver.estudiante.php`
- `ajax/docente.php`
- `ajax/estudiante.php`
- `ajax/login.php`
- `form-doc/acuerdos.autoalta.php`
- `form-doc/docente.php`
- `form-doc/estudiante.php`
- `form-doc/ficha.docente.php`
- `form-doc/ficha.estudiante.php`
- `form-doc/header.php`
- `form-doc/scripts/docente.js`
- `form-doc/scripts/estudiante.js`
- `form-doc/scripts/ficha.docente.js`
- `form-doc/scripts/ficha.estudiante.js`
- `src/Model/Docente.php`
- `src/Model/Estudiante.php`
- `src/Model/Login.php`
- `src/Security/AcademicSession.php` (nuevo)
- `src/bootstrap/app.php`

Son 21 archivos rastreados modificados y un helper nuevo: 16 PHP y seis JS. Además se actualizan esta Task, TASKS.md y ROADMAP.md, y se conserva el borrador de migración. El ADR-003 y PDF no rastreados, y el borrador de aviso institucional previo, se mantienen fuera de este incremento. El diff completo acumulado de los archivos rastreados se guarda como evidencia externa; el helper y SQL nuevos se inspeccionaron directamente.

### Contratos y controles revisados

- Eliminar realiza exclusivamente UPDATE del estado (Docente 4 / Estudiante 6); no borra login, persona, perfiles, permisos ni antecedentes. Las relaciones históricas conservan IDs. La comprobación de dependencias que bloqueaba el DELETE físico se sustituye por conservación.
- Grilla normal y búsquedas excluyen inactivos; filtro explícito de inactivos disponible para Admin/Comité. En Docentes este filtro admite encontrar el propio perfil inactivo de un gestor con otros roles válidos; el botón Eliminar sigue sin habilitar autoeliminación.
- Ambos gestores editan/inactivan/reactivan desde Datos del programa. Titular sin rol de gestión no puede enviar el estado. Reactivación exige elegir estado; para Docente con rol administrativo se conserva la regla vigente de Aceptado. No se inventa el estado previo ni se reconstruyen permisos.
- Programa y estado se guardan en una transacción, con identidad bloqueada, referencias válidas y SQL parametrizado. Guardado sin cambios es válido. Fallos de estado, referencias o líneas revierten todo. En Docente las nuevas escrituras del programa sustituyen en este recorrido los auxiliares heredados interpolados.
- Autorización administrativa contrastada con permisos actuales en BD, además de sesión coherente. IDs booleanos/arreglos y estado inválido rechazados. READ conserva proyecciones y consumidores; lista Docente utiliza JSON explícito y el catálogo Estudiante conserva FETCH_BOTH para auxiliares heredados.
- Login y sesiones abiertas filtran el acceso efectivo del perfil inactivo; si no quedan roles válidos se bloquea y se limpia la autenticación. El permiso histórico 3 aislado no rescata una cuenta inactiva. Otros roles/perfiles activos conservan sus capacidades. No se escriben permisos almacenados. Sesiones inválidas fallan con 401.
- Carga temprana con cookie cubre módulos que invocan app.php antes de session_start. Header revalida después de iniciar sesión, y las vistas propias redirigen si han perdido ese perfil. Las URLs/API se evalúan con el contexto filtrado. Un perfil reactivado recupera sus accesos tras iniciar sesión nuevamente, sin quitar los otros roles de una sesión vigente.
- Guía histórico inactivo se sigue leyendo y se permite conservarlo sin cambio; nuevas selecciones aceptan solo Docente Aceptado. No se reescriben relaciones de terceros ni los módulos de antecedentes.
- Aviso de inactivación visible en el alert real de Docentes; confirmación explica conservación y recuperación. Inactivos no ofrecen Eliminar otra vez. Controles de estado en programa y scripts administrativos versionados.
- Inactivación deshabilitada antes de verificar conjuntamente CHECK ampliado y catálogo 6 actualizado. Devuelve 409 con mensaje visible; la opción de estado nueva está deshabilitada en programa. La BD real todavía no está habilitada.

### Validaciones ejecutadas y evidencia

Artefactos en `C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/`.

| Comando (desde la carpeta de evidencia) | Resultado |
| --- | --- |
| `python test-perfiles-inactivos.py` | 111/111 PASS: endpoints/modelos reales, SQLite aislado y metadatos de migración simulados; historial, ambos gestores, rollback, roles coexistentes, login, sesiones abiertas y bootstrap con cookie |
| `node test-perfiles-inactivos-frontend.cjs` | 11/11 PASS: callbacks reales, carga/envío de estado, inactivos y aviso visible; adaptador DOM/transporte |
| `python test-estudiante-crud-activos.py` | 91/91 PASS: regresión de altas, consulta, programa y estados; excluye los 32 casos de DELETE físico sustituidos y comprueba 409 para estado 6 antes de migrar |
| `python test-estudiante.py` | 71/71 PASS: protección personal anterior y compatibilidad previa a migrar |
| `python test-autoalta-acuerdos.py` | 36/36 PASS: obligatoriedad, roles y alta aislada en ambos perfiles |
| `node test-autoalta-acuerdos-frontend.cjs` | 19/19 PASS |
| `node test-estudiante-crud-frontend.cjs` | 14/14 PASS |
| `node test-estudiante-frontend.cjs` | 12/12 PASS |
| `node test-estudiante-selector.cjs` | 5/5 PASS |
| `python test-autoalta-acuerdos-http.py` | 12/12 PASS HTTP real; HTML/assets y rechazos sin datos para impedir altas reales |
| `C:/xampp/php/php.exe prepare-perfiles-inactivos.php` | 63 consultas de aplicación y dos sentencias de migración preparadas nativamente; cero errores; NO execute de mutaciones |
| PHP lint / Node --check de los 22 archivos; `git diff --check` | 23 comprobaciones conformes |

Resultados JSON conservados junto a scripts/fixtures. Las ejecuciones exploratorias detectaron errores del fixture (setup vacío y aserción demasiado amplia que confundía UPDATE sin cambios con borrado); se corrigieron y los resultados finales anteriores corresponden a ejecuciones completas. La revisión detectó y corrigió orden de sesión, accesibilidad del propio perfil inactivo del gestor y validación de IDs booleanos. No se afirma VF por ausencia de errores, ni se reutiliza el PASS previo de eliminación.

La preparación nativa comprobó antes/después el CHECK `(1,2,3)`, catálogo 6 `eliminado(a)` y conteos idénticos. `inactivation_enabled=false`, `application_mutations_executed=0`. Las pruebas de estado 4/6 operativo son aisladas; no acreditan ejecución de migración ni concurrencia real de MySQL. Los PASS manuales de autoalta, altas Admin, consulta, programa y estado anteriores se conservan.

### Respaldo y paso de migración preparado

Respaldo completo generado con mysqldump local, single-transaction, rutinas/triggers/eventos y hex-blob, fuera del repositorio y del directorio público. Salida 0, marcador de finalización, archivo no vacío y 39 CREATE TABLE verificados. El archivo temporal de opciones se eliminó al terminar; no se imprimieron credenciales ni registros. Detalle, ubicación y SHA-256 en `perfiles-inactivos-backup-report.json`. No se ensayó una restauración; esto no equivale a validación de restore.

SQL pendiente: `migrations/TASK-DB-MIGRATION-PERFILES-INACTIVOS-001.sql`. Cambia únicamente CHECK Docente y etiqueta Estudiante 6. Los estudiantes históricos en código 6 pasarán a interpretarse como inactivos; conservarán sus datos, pero perderán el acceso de ese perfil. DDL confirma implícitamente y requiere ventana coordinada. Reversión condicionada a ausencia de Docentes 4; no convertirlos a Aceptado en masa. La autorización requerida es ejecutar exactamente este SQL en la BD local del proyecto, verificar metadatos, habilitación y conteos, y después presentar VF corta. No ejecutar por la aprobación actual de implementación.

### Dictamen, límites y estado Git

**REVIEW TÉCNICO — APROBADO PARA VF.** Implementación, contratos, alcance, SQL, rollback, consumidores y regresiones conformes con evidencia citada. La ejecución de VF queda pendiente de migración autorizada; no se da PASS ni cierre funcional a inactivación/reactivación todavía.

Límites materiales: pruebas SQLite no reproducen bloqueos/concurrencia InnoDB; SQL de migración preparado, no aplicado; la interfaz nueva requiere VF posterior a la migración; sesiones ya filtradas recuperan el perfil reactivado al iniciar sesión. Se mantienen los pendientes ya documentados para publicación en Internet (política institucional y alcance de seguridad previo), sin ampliar esta Task.

Rama `refactor/fase-0-seguridad`, HEAD `55f0aa3658d7b0ce2cc3c4200f99b4ecc27ef971`, staging vacío. Sin commit, push, migración ni cambios de registros reales. El respaldo y pruebas están fuera del repositorio. El usuario solo debe autorizar la ejecución de la migración como siguiente paso; no repetir autorización de implementación ni VF ya aprobadas.

## Migración reportada por el usuario y verificación posterior — 14/11/2026

El usuario informó «se ejecuto correctamente». Work no volvió a ejecutar el SQL ni hizo escrituras en registros. Se consultaron metadatos y conteos reales: `chk_profesor_estado` permite (1,2,3,4), el catálogo 6 se llama `inactivo(a)` y `AcademicSession::migrationReady()` devuelve true. Los conteos coinciden con los previos: 27 cuentas, 23 personas, 12 Docentes y 10 Estudiantes. Esto comprueba los efectos esperados y ausencia de variación de conteos; no constituye por sí solo VF ni verificación íntegra del historial. Evidencia: `perfiles-inactivos-postmigration-results.json` en la carpeta externa de pruebas ya identificada.

Se levanta el requisito pendiente de esquema para iniciar VF. Se mantiene REVIEW TÉCNICO — APROBADO PARA VF; no se repiten suites aisladas sin cambios de código ni se declara PASS funcional por el mensaje de ejecución del SQL.

VF nueva preparada, sin repetir acuerdos/altas aprobados:

- **VF-INACT-01 — Eliminar conserva e inactiva (Admin, ambos perfiles):** recargar Ctrl+F5; seleccionar registros de prueba con antecedentes y anotar su estado. Mantener una sesión del titular abierta en otro navegador/contexto. Pulsar Eliminar y confirmar. Resultado: aviso visible, registro fuera de la grilla normal, presente en filtro Inactivos y ficha/antecedentes conservados. Si Docente aparece como guía/coautor de otro actor, comprobar que esa relación mantiene su nombre.
- **VF-INACT-02 — Acceso del perfil inactivo:** durante el estado inactivo, recargar la sesión del titular y probar iniciar sesión de nuevo. Si solo dispone de ese perfil, no debe acceder ni mediante URL directa. Para cuenta con otros roles válidos, debe conservar esos roles y perder el perfil inactivo. Indicar BLOQUEADA para la variante multirrol si no se dispone de cuenta de prueba adecuada; no asumir PASS.
- **VF-INACT-03 — Reactivación por Comité (ambos perfiles):** entrar como Comité, filtrar Inactivos, abrir Información → Editar Datos Programa, elegir un estado vigente conocido (Docente Aceptado si conserva rol administrativo) y guardar. Resultado: vuelve a grilla habitual, conserva ficha y relaciones; el titular recupera el perfil al iniciar sesión otra vez.
- **VF-INACT-04 — Inactivar desde Datos del programa (Admin o Comité, ambos perfiles):** elegir Inactivo en ese formulario y guardar. Resultado: mismo ocultamiento y conservación que Eliminar. Reactivar y dejar los registros de prueba en su estado válido original. Este caso comprueba el recorrido de edición de estado incorporado, distinto del botón Eliminar.

Pendiente respuesta PASS/FAIL/BLOQUEADA por caso y perfil, o un único PASS si se ejecutaron todos los recorridos disponibles indicando cualquier variante pendiente. Ningún caso de VF nuevo está aprobado todavía. Sin autorización de commit o push.

## Resultado funcional del usuario y preparación de commit — 14/11/2026

El usuario respondió «docente estudiante pass» al bloque de ocho pasos entregado después de verificar la migración. Se registra PASS funcional de inactivación/reactivación para ambos perfiles: Eliminar, mensaje y ocultamiento, listado de inactivos, conservación de ficha/relaciones, acceso según roles vigentes, reactivación por Comité y edición de Inactivo desde Datos del programa. Es un resultado comunicado por el usuario, no una ejecución de navegador de Work. No se extrapola a módulos ajenos ni se exige repetir las VF previas aprobadas.

El incremento queda validado técnica y funcionalmente. Se conserva el dictamen técnico previo y la evidencia automatizada; no hay cambios nuevos de código ni motivo para repetir suites. Verificación de Git: HEAD 55f0aa3658d7b0ce2cc3c4200f99b4ecc27ef971, rama refactor/fase-0-seguridad y staging vacío. Se actualiza únicamente la trazabilidad documental de este PASS.

Siguiente paso preparado: solicitar autorización específica de staging y commit de los 22 archivos de aplicación, tres documentos de trazabilidad (esta Task, TASKS.md, ROADMAP.md) y el SQL de migración: 26 archivos. Mensaje propuesto: `TASK-EPIC001: completa CRUD e inactivacion de perfiles`. El manifiesto con rutas y hashes se conserva como `perfiles-inactivos-commit-manifest.json` fuera del repositorio. Se excluyen el ADR-003, el PDF académico, el borrador institucional previo y todos los respaldos/fixtures/evidencias externos. No se ejecuta staging, commit ni push por este PASS; el protocolo exige autorización separada para las operaciones Git. Cierre de publicación pendiente.

## Autorización de staging y commit — 14/11/2026

El usuario confirmó «Autorizo staging y commit». Antes de preparar Git se verificó que HEAD coincide con 55f0aa3658d7b0ce2cc3c4200f99b4ecc27ef971, la rama es refactor/fase-0-seguridad, el índice está vacío y los hashes de los 26 archivos coinciden con el manifiesto revisado. Ningún archivo de aplicación cambió después de las pruebas y VF; solo se incorpora este registro de autorización a los tres documentos de trazabilidad.

Este registro forma parte del commit autorizado, con mensaje `TASK-EPIC001: completa CRUD e inactivacion de perfiles`. La comprobación posterior del commit y su hash se conserva en la evidencia externa para evitar modificar de nuevo la documentación solo para incluir el hash del propio commit. El push requiere autorización posterior y no está autorizado por esta instrucción. Se mantienen excluidos ADR-003, PDF académico, aviso institucional previo y artefactos externos.
