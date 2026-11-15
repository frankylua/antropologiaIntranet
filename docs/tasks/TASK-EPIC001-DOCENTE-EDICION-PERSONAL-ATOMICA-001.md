# TASK-EPIC001-DOCENTE-EDICION-PERSONAL-ATOMICA-001

## Estado, autorización y objetivo

CERRADA: implementada, Review Técnico aprobado y VF-DP-01 a VF-DP-04 PASS del usuario. Commit `f482ed898bafa23abca1877f14dafd570a05781c`; push ejecutado informado por el usuario («push ejecutado»). Sin migración. La publicación pendiente de apartados anteriores corresponde al momento en que se redactaron. Preparación de muestra vigente: [TASK-ENTREGA-MUESTRA-INTERNA-001](TASK-ENTREGA-MUESTRA-INTERNA-001.md).

Autorización original: «Autorizo la Task de edición Docente». Objetivo cumplido: guardar datos personales y credenciales juntos, o revertirlos ante un error. Prioridad del CRUD principal de la muestra interna al 20/11/2026.

Línea base: rama `refactor/fase-0-seguridad`, HEAD `498134eb9a22d48f3e94a624f6cb289d7b6d95a8`. El usuario confirmó push ejecutado y aprobado de ese commit; no se repite verificación remota. Inactivación/reactivación anterior con PASS de Docente y Estudiante. Índice vacío, sin modificaciones rastreadas al inicio; se preservan ADR-003, borrador institucional JSON y PDF académico no rastreados.

## Problema acreditado y alcance

El endpoint ejecutaba UPDATE usuario y después UPDATE login sin transacción conjunta. Reproducción previa con endpoint/modelos reales y SQLite aislado: fallo controlado en UPDATE login → HTTP 500, `unchanged=false`, cambio persistente en usuario. Evidencia `docente-edicion-personal/baseline-partial-write.json`, en la carpeta de evidencia externa del proyecto. No se modificaron registros reales.

Metadatos reales consultados en lectura: login, usuario, profesor y permiso_login son InnoDB; login.correo tiene índice único correo_UNIQUE; aislamiento REPEATABLE-READ. No se necesita migración.

Archivos de aplicación previstos y autorizados para esta reparación:

- `ajax/docente.php`: handler exclusivo de update-inf-pers / update-inf-pers-perfil, contrato JSON existente, validación de forma/identificadores, mensajes y errores.
- `src/Model/Docente.php`: operación personal transaccional con identidad y permisos actuales bloqueados; reutiliza las escrituras parametrizadas existentes.

Documentación: esta Task, docs/TASKS.md y docs/roadmap/ROADMAP.md. El JS actual ya presenta responseJSON.mensaje y conserva el formulario al fallar; no se prevé modificarlo. No tocar el modelo Usuario compartido ni handlers de programa, estados, autoalta o inactivación por esta Task. No crear capabilities, cambiar reglas institucionales de edición, reescribir normalización de contraseñas, aplicar migraciones ni hacer staging/commit/push sin las autorizaciones correspondientes.

## Contrato y criterios de aceptación

1. Docente edita sus datos por modo perfil, resuelto desde login en el servidor. Los IDs del cliente no redirigen una edición propia a otra persona. Perfil propio requiere rol Docente vigente y estado 1/2/3; los otros roles de una cuenta siguen las reglas ya aprobadas de actividad.
2. Admin y Comité editan un Docente inequívoco por modo administrativo, con sesión coherente y permisos actuales en BD. No se concede este acceso por enviar IDs o banderas de cliente. Se conserva la posibilidad de editar el registro inactivo desde gestión.
3. Se bloquean login, persona, perfil y permisos relevantes durante la operación. Las escrituras personal y de credenciales usan la misma conexión/transacción. Error de cualquier escritura o commit revierte la operación; no se deja una transacción abierta.
4. Guardado sin cambios responde éxito; no se exige rowCount=1 para una actualización idéntica. Contrato de éxito permanece `{ok:true,codigo:DOCENTE_ACTUALIZADO,mensaje:...}`.
5. Correo duplicado responde HTTP 409, código CORREO_EN_USO y aviso comprensible; dirección, correo y contraseña quedan como antes. No exponer SQL, credenciales ni detalles del driver.
6. Contraseña vacía conserva la actual. Cambio explícito conserva la regla/normalización existente y no se introduce una migración de hashes. Estados, permisos, programa, cuentas e historial no se alteran por la edición personal.
7. IDs/valores con forma inválida se rechazan de manera controlada. Ante errores, el JS actual muestra el aviso y conserva los datos para corregir/reintentar.

## Validaciones y secuencia

Fixtures aislados del endpoint y modelos: propio/Admin/Comité, IDOR, permisos revocados/falsos, identidad ambigua, inactivo, guardado sin cambios, cambio de correo/contraseña, conflicto real de correo con rollback y fallos controlados de usuario/login/commit. Callback JS real para mensaje 409/conservación/reintento. Preparación nativa del SQL sin execute de mutaciones, sintaxis, diff y regresiones afectadas. Resultados y comandos se incorporarán al Review.

Secuencia: implementación → pruebas → Review Técnico con dictamen → VF → autorización de commit → commit → autorización de push. No iniciar VF antes del dictamen. Task nueva complementa el CRUD anterior; no reabre sus PASS ni lo reemplaza.

## Implementación final y Review Técnico

### Archivos reales y conciliación

Se modificaron exactamente dos archivos de aplicación: `ajax/docente.php` y `src/Model/Docente.php`. Además se actualizaron `docs/TASKS.md`, `docs/roadmap/ROADMAP.md` y se creó esta Task: cinco archivos dentro del incremento. No hay cambios en JavaScript, modelo Usuario compartido, esquema, permisos, programa, estados, autoalta o inactivación. El ADR-003, aviso institucional JSON y PDF no rastreados permanecen preservados y excluidos. Los scripts/fixtures/resultados se conservan fuera del repositorio.

### Contratos, integridad y seguridad revisados

- El endpoint desvía únicamente update-inf-pers y update-inf-pers-perfil a un handler propio antes de los casts/normalización genéricos; se elimina el handler personal antiguo para evitar dos implementaciones. Los demás handlers mantienen su código.
- Contrato WRITE de formulario completo conservado: respuesta de éxito ok/codigo/mensaje, normalización heredada de textos y credenciales, contraseña vacía sin cambio y cambio explícito permitido conforme al flujo vigente. Validación de forma, identificadores y correo en servidor; error de correo ocupado ahora devuelve 409 CORREO_EN_USO con mensaje comprensible.
- Edición propia resuelta desde login y usuario inequívoco en BD, aunque los IDs enviados o cacheados en sesión indiquen otra persona. Requiere rol Docente vigente; admite estados 1/2/3 y deniega Inactivo. Admin/Comité gestionan identidades indicadas e inequívocas, incluido registro inactivo, con permisos actuales y banderas de sesión coherentes. POST no concede roles ni cambia estados por incluir campos extra.
- El modelo posee la transacción y bloquea cuenta del actor, sus permisos, cuenta/persona/perfil del objetivo. No mezcla transacciones de propietarios distintos. Se reutilizan las escrituras existentes parametrizadas mediante la misma conexión PDO singleton; un fallo de la segunda escritura, identidad, begin o commit revierte lo abierto por esta operación. Guardado sin cambios no depende de rowCount.
- La restricción real correo_UNIQUE garantiza unicidad incluso ante conflicto concurrente. Únicamente el error de driver 1062/SQLSTATE 23000 durante la escritura de credenciales se traduce a CORREO_EN_USO; otros errores de integridad conservan respuesta técnica genérica, sin SQL ni secretos. El adaptador SQLite de prueba convierte su violación UNIQUE real al código MariaDB; no se incorpora lógica de SQLite a la aplicación.
- No se reconstruyen permisos, reasignan identidades ni actualizan estados/programa/historial. Los rechazos de login y actividad se mantienen en AcademicSession sin modificaciones. Roles adicionales válidos siguen la política ya implementada.
- El consumidor JS existente usa responseJSON.mensaje, conserva controles al fallar y permite reintentar con el correo corregido. Se ejecutaron sus callbacks reales; no se cambia ni se versiona nuevamente el asset por este incremento. READ y selector no se modifican.

### Validaciones ejecutadas

Carpeta de evidencia: `C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/docente-edicion-personal/`.

| Comando / comprobación | Resultado |
| --- | --- |
| `python test-backend.py` desde la carpeta anterior | 91/91 PASS: propio/Admin/Comité; guardado idéntico; dirección/correo; contraseña vacía/explícita; correo ocupado con rollback; errores de persona/login/commit/begin; verificación fallida de credenciales; IDOR, permisos revocados/falsos, identidad ambigua, estados y formas inválidas |
| `node test-frontend.cjs` | 8/8 PASS: callbacks JS reales en Node VM, mensaje 409/500, conservación de datos, corrección y reintento; confirmación de contraseña existente y rechazo si falla READ |
| `python ../test-perfiles-inactivos.py` | 111/111 PASS de regresión de programa, estado, inactivación/reactivación y acceso |
| `python ../test-autoalta-acuerdos.py` | 36/36 PASS de regresión de altas/acuerdos y reglas administrativas |
| `C:/xampp/php/php.exe prepare-native.php` | 12 consultas distintas preparadas nativamente, cero errores, sin execute de mutaciones; metadatos y conteos idénticos antes/después |
| HTTP real a ambas operaciones, sin sesión ni datos personales | 2/2 rechazos JSON 401 conformes; ninguna escritura válida posible |
| PHP lint de ambos archivos; `git diff --check` | Conformes |

Resultados completos: backend-results.json, frontend-results.json, native-results.json y http-results.json, junto a fixtures y queries.json. La reproducción previa baseline-partial-write.json demuestra el fallo anterior con un cambio persistente en usuario y HTTP 500; los casos nuevos equivalentes exigen snapshot sin cambios y transacción cerrada. Los snapshots también comprueban preservación de las demás tablas y contraseñas; no se imprimen contraseñas en las respuestas de prueba.

### Criterios, límites y riesgos residuales

Criterios técnicos 1–7 conformes con ejecución y revisión de diff. VF de navegador pendiente del usuario: no se declara PASS funcional a partir de los tests. SQLite no reproduce bloqueos/concurrencia de InnoDB; la preparación nativa valida SQL y esquema pero no ejecuta las escrituras reales. La variante 1062 se cubre con violación UNIQUE aislada adaptada y error de driver controlado. No se ha ensayado pérdida de conexión durante la confirmación ni un despliegue de producción. Se conserva la normalización heredada de credenciales; no se moderniza almacenamiento de contraseñas en esta Task.

No se repiten VF de autoalta, programa, estados o inactivación ya aprobadas. El alcance se mantuvo en los dos archivos backend anunciados; no fue necesaria ampliación de permisos/arquitectura ni migración. HEAD permanece `498134eb9a22d48f3e94a624f6cb289d7b6d95a8`, rama refactor/fase-0-seguridad e índice vacío. Sin modificación de registros reales, staging, commit o push por Work.

**DICTAMEN: REVIEW TÉCNICO — APROBADO PARA VF.** Se puede iniciar el recorrido manual siguiente; cierre y operaciones Git permanecen pendientes.

## VF preparada — pendiente del usuario

Usar un Docente de prueba y anotar su dirección y correo guardados. No es necesario inspeccionar SQL ni provocar una falla técnica del servidor.

1. **VF-DP-01 — Conflicto y reversión (Admin):** ficha Docente → Editar Datos Personales. Cambiar la dirección y poner como correo el de otra cuenta existente. Guardar. Debe aparecer el aviso de correo registrado y el formulario conservar el borrador. Salir del editor sin guardar otra vez y reabrir: dirección y correo guardados deben seguir siendo los originales; no debe quedar una edición parcial.
2. **VF-DP-02 — Corrección y guardado sin cambios (Admin):** cambiar dirección y correo a uno libre, dejando contraseña vacía. Guardar y reabrir: ambos valores nuevos deben estar guardados. Guardar nuevamente sin modificaciones: éxito. El titular debe poder iniciar sesión con el correo nuevo y la misma contraseña.
3. **VF-DP-03 — Comité:** abrir la ficha del mismo Docente y guardar un cambio simple de dirección; reabrir y comprobar persistencia.
4. **VF-DP-04 — Titular:** con la cuenta del Docente, Mi perfil → Editar Datos Personales, guardar un cambio simple y reabrir. Debe persistir en su propia ficha.

Respuesta esperada: «VF Docente PASS» si se ejecutaron los cuatro recorridos, o identificar el caso FAIL/BLOQUEADA y mensaje observado. No se cuenta como ejecutada ninguna VF hasta recibir el resultado. Esta prueba no autoriza staging, commit ni push.

## Resultado funcional y preparación de commit — 14/11/2026

El usuario confirmó «VF Docente PASS» al bloque de cuatro recorridos. Se registra PASS comunicado del conflicto de correo con reversión, corrección/guardado sin cambios/login con contraseña conservada, edición por Comité y edición propia del Docente (VF-DP-01 a VF-DP-04). Es evidencia funcional del usuario; no se atribuye ejecución de navegador a Work ni se extrapola a módulos ajenos.

El incremento queda validado técnica y funcionalmente. Antes de registrar este resultado se verificó que los cinco archivos coinciden con los hashes del Review, HEAD sigue en 498134eb9a22d48f3e94a624f6cb289d7b6d95a8 y el índice está vacío. No cambió código de aplicación y no se repiten suites ni VF aprobadas para registrar un PASS.

Siguiente paso: autorización específica de staging y commit de cinco archivos: ajax/docente.php, src/Model/Docente.php, docs/TASKS.md, docs/roadmap/ROADMAP.md y esta Task. Mensaje preparado: `TASK-EPIC001: hace atomica la edicion personal de Docente`. Manifiesto externo: docente-edicion-personal/commit-manifest.json. Se excluyen ADR-003, aviso institucional JSON, PDF académico y todos los artefactos externos. Este PASS no autoriza staging, commit ni push. Publicación y cierre final pendientes.

## Autorización de staging y commit — 14/11/2026

El usuario confirmó «Autorizo staging y commit». Preflight: los cinco archivos coinciden con el manifiesto posterior a VF; HEAD 498134eb9a22d48f3e94a624f6cb289d7b6d95a8, rama refactor/fase-0-seguridad e índice vacío. Se incorpora únicamente este registro de autorización a la documentación; el código revisado y validado no cambia.

Este registro forma parte del commit autorizado `TASK-EPIC001: hace atomica la edicion personal de Docente`. Hash y verificación posterior se conservarán en el artefacto externo docente-edicion-personal/commit-result.json, evitando modificar nuevamente el documento para incluir el hash de su propio commit. Push pendiente de autorización específica. Mantener excluidos todos los archivos ajenos y artefactos externos.

## Cierre conciliado — 14/11/2026

El usuario informó «push ejecutado» para f482ed8, cuyo HEAD se comprobó localmente. Se registró el resultado en docente-edicion-personal/commit-result.json sin repetir la verificación remota. Esta conciliación únicamente actualiza documentación de cierre como parte de TASK-ENTREGA-MUESTRA-INTERNA-001; el código validado no cambió y no se repiten pruebas por este registro.
