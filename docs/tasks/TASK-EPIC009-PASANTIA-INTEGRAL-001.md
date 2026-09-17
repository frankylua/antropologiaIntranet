# TASK-EPIC009-PASANTIA-INTEGRAL-001

## Estado

**Cerrada. VF aprobada por el usuario.**

- EPIC principal: EPIC-009; coordinación EPIC-003 y EPIC-008.
- Fecha: 2026-09-16.
- Implementación: completada; validaciones técnicas correctas.
- VF: aprobada por el usuario.
- Blockers: ninguno.

## Cierre

- CRUD final: CREATE, READ, UPDATE y DELETE cerrados.
- Seguridad: ownership backend; CRUD propio de Estudiante/Profesor y global de Admin/Comité; acceso horizontal bloqueado; usuario inmutable; CSRF en CREATE/UPDATE/DELETE y anónimo rechazado.
- Persistencia: SQL parametrizado, `ejecutarEscritura`, `filasAfectadas` e `idInsertado` en CREATE; sin migración, cambio de esquema ni transacción multiobjeto.
- VF: render seguro, recarga sin duplicados, editor y DELETE funcionales, mensajes UX, collapse Pasantía/Beca independiente, `form_pasantia` sin anidación y cache busting de `pasantia.js` mediante `filemtime`.
- Validación: revisiones estáticas correctas; VF funcional aprobada por el usuario; sin blockers.

## Objetivo

Completar Pasantía como un único objeto funcional, corrigiendo CREATE/READ e
implementando UPDATE/DELETE, con propiedad inmutable, autorización por actor,
CSRF, validación backend, persistencia explícita y UI integrada en Ficha.
No dividir en Tasks CREATE, UPDATE o DELETE ni generar addendums.

El cierre de las cuatro operaciones queda sujeto a revisión técnica y VF
posterior aprobada por el usuario; esta Task no acredita ese cierre.

## Fuentes aprobadas

- Fuente primaria: [AT-EPIC009-PASANTIA-INTEGRAL-001](../architecture/AT-EPIC009-PASANTIA-INTEGRAL-001.md).
- La solicitud «EPIC-009 — CREACIÓN TASK INTEGRAL PASANTÍA» declara expresamente
  **APROBADO** ese AT y ratifica sus decisiones. El archivo conserva el rótulo
  anterior «LISTO PARA APROBACIÓN»; esta aprobación posterior del usuario es
  la autoridad vigente. No se modifica el AT en esta ejecución exclusiva.
- Inspección integral incorporada en el AT y decisiones institucionales de sus
  secciones 6–10; ADR-001 vigente para contratos de escritura.
- Grado, Postdoctorado y Beca: referentes de implementación y UX, sólo lectura.

## Estado inicial

```text
rama: refactor/fase-0-seguridad
HEAD: 427d281c2e1c588f332cf2375f30eafac05cbd57
último cierre integrado: TASK-EPIC009-BECA-INTEGRAL-001
staging: vacío
cambios tracked: ninguno
```

Antes de crear esta Task había tres archivos no rastreados: el AT fuente,
ADR-003 excluido y el PDF excluido. No existía el archivo de esta Task.

| Operación | Estado inicial | Objetivo después de implementación y VF |
| --- | --- | --- |
| CREATE | Defectuoso | Cerrado |
| READ | Defectuoso | Cerrado |
| UPDATE | Falta | Cerrado |
| DELETE | Falta | Cerrado |

CREATE usa `op=insert-update`, pero siempre inserta; recibe propietario del
cliente, interpola SQL y consume como booleano el array de `ejecutarEscritura`.
READ filtra por usuario cliente sin autorización. El endpoint carece de
protección propia y CSRF. Editar es inerte; el cierre del formulario no es
DELETE. El render puede ejecutar HTML y duplicar cards.

## Alcance

Autoridad: `pasantia`, PK `id_pasantia`, propietario `usuario`. Referencias:
`inst_pasant` a Institución y `pais_pasant` a País. Campos restantes:
`prof_patr`, `fondo`, `ciudad`, `fech_in`, `fech_ter`.

Objeto individual, sin documentos, tablas intermedias ni FKs entrantes según
la inspección aprobada. Ficha sólo consume/presenta. Sin nuevo ADR, migración,
cambio de esquema, constraints o cascadas. Sin transacción multiobjeto.

La futura intervención debe completar conjuntamente modelo, endpoint,
frontend, configuración local y corrección mínima de collapse. Conservar
consumers y catálogos; no modernizar Ficha integral.

## Archivos modificables

Máximo autorizado para la futura implementación:

| Archivo | Cambio delimitado |
| --- | --- |
| `src/Model/Pasantia.php` | CRUD parametrizado, detalle, comprobaciones de objetivo/referencias y escritura por PK/propietario. |
| `ajax/pasantia.php` | Actor, autorización, ownership, CSRF, validaciones y contrato JSON. |
| `form-doc/scripts/pasantia.js` | CRUD completo, formulario/editor, render seguro, mensajes y recarga. |
| `form-doc/agr.form.dat.acad.php` | Contexto/token y marcado exclusivos de Pasantía. |
| `form-doc/ficha.estudiante.php` | Exclusivamente identificadores/atributos de collapse propios de Pasantía. |
| `docs/tasks/TASK-EPIC009-PASANTIA-INTEGRAL-001.md` | Evidencias y estado real de ejecución; no atribuir VF a Codex. |

En esta ejecución documental sólo se crea el sexto archivo. Antes de una
futura implementación, verificar estado Git y cambios locales de cada objetivo.

## Archivos protegidos

Todo archivo fuera de la lista anterior queda protegido, expresamente:

- `src/Security/Authorization.php`, `src/Model/Login.php`, `ajax/login.php`,
  bootstrap/configuración de sesión y `src/Config/conexion.php`.
- `js/funcAjax.js`, `js/funcForm.js`, `js/funcValid.js`, `js/fechaCivil.js` y
  `form-doc/scripts/usuario.js`.
- `ajax/institucion.php`, `src/Model/Institucion.php`, `ajax/pais.php`,
  `src/Model/Pais.php` y reglas de ambos catálogos.
- Implementaciones cerradas de Grado, Postdoctorado y Beca, incluidos sus
  bloques en archivos compartidos; resto de Ficha, footer y callers existentes.
- AT fuente, ADR, Roadmap, migraciones y otros documentos.
- `docs/architecture/ADR-003-RECURSOS-ACADEMICOS-COMPARTIDOS-PARTICIPACION.md`.
- `files/prog_curso/curso_45afc744db0fb82f1db6f16d31ded67a.pdf`.

No modificar un caller externo aunque se detecte una necesidad: detener y
reportar si no se puede mantener su contrato desde los archivos autorizados.

## Contrato institucional

Estudiante y Profesor con perfil válido tienen CRUD exclusivamente propio;
Admin y Comité tienen CRUD global sobre objetivos válidos. Anónimo y terceros
sin rol global no acceden a recursos ajenos.

`pasantia.usuario` es autoridad persistida e inmutable. CREATE determina el
propietario autorizado; UPDATE no lo modifica. Al resolver especialización
del usuario objetivo se exige Profesor XOR Estudiante. No autorizar identidades
ambiguas ni redefinir roles, capacidades o estados de sesión.

DELETE es físico y unitario sobre `pasantia`: no elimina Usuario, Institución
o País ni cambia las cascadas desde ellos. No hay baja lógica.

## Matriz CRUD

| Actor | CREATE | READ | UPDATE | DELETE |
| --- | --- | --- | --- | --- |
| Estudiante propietario | Sí | Sí | Sí | Sí |
| Estudiante no propietario | No | No | No | No |
| Profesor propietario | Sí | Sí | Sí | Sí |
| Profesor no propietario | No | No | No | No |
| Admin | Global | Global | Global | Global |
| Comité | Global | Global | Global | Global |
| Anónimo | No | No | No | No |

«Sí» requiere perfil válido; para CREATE significa crear para sí mismo.
«Global» exige objetivo existente con especialización válida. Los actores que
acumulan un rol global válido operan conforme a Admin/Comité, como establece
el AT; las filas de no propietario corresponden a actores sin ese rol.

## Contrato backend

Mantener `ajax/pasantia.php` como único endpoint. Adoptar POST con operaciones
explícitas `create`, `read`, `detail`, `update`, `delete`. Actualizar sus
peticiones conjuntamente en `pasantia.js`; retirar `insert-update` y residuos
comentados. Operación desconocida: error controlado, nunca mutación implícita.

Campos canónicos de CREATE/UPDATE: `inst_pasant`, `pais_pasant`, `prof_patr`,
`fondo`, `ciudad`, `fech_in`, `fech_ter`. El frontend adapta los IDs HTML
existentes a estos nombres. `id_pasantia` identifica detalle/update/delete.

Resolver actor antes de entregar datos u operar: iniciar sesión con el
bootstrap vigente, comprobar login/rol coherentes y reutilizar
`Authorization::hasAny` / `hasCapability` sin modificarlas. Seguir el
precedente del AT para identidad única de sesión, Estudiante con `perfil.ver`
y Profesor con identidad `docente`, y validar especialización persistida.

| Operación | Resolución del objetivo y resultado |
| --- | --- |
| `create` | Propietario propio derivado de sesión o `usuario` explícito de Admin/Comité validado; siete campos y CSRF. Devolver ID creado. |
| `read` | Lista por usuario propio o objetivo global validado. Lista vacía devuelve `[]` con éxito. |
| `detail` | Obtener fila por ID, resolver propietario desde BD y autorizar antes de devolverla. |
| `update` | Resolver fila/propietario por ID, autorizar, validar campos/CSRF y escribir sin cambiar `usuario`. |
| `delete` | Resolver fila/propietario por ID, autorizar, validar CSRF y eliminar físicamente esa fila. |

Si un actor propio envía `usuario`, debe coincidir con su identidad efectiva;
si no coincide, rechazar. En detalle/update/delete, un usuario contextual
enviado también debe coincidir con el propietario persistido. El rol global
no permite transferir una fila. El modelo devuelve internamente `usuario`
para autorizar; no se publica como campo editable.

Proyección pública de lista/detalle: `id_pasantia`, `inst_pasant`, `inst`,
`pais_pasant`, `pais`, `prof_patr`, `fondo`, `ciudad`, `fech_in`, `fech_ter`.
Usar filas asociativas y proyección explícita, nunca `SELECT *`.

Respuestas JSON con Content-Type correcto y estructura uniforme:

```json
{"ok": true, "codigo": "PASANTIA_CREADA", "mensaje": "Pasantía registrada correctamente.", "datos": {"id_pasantia": 1}}
```

En errores, `ok=false` y `datos=null`; no entregar contenido de filas ajenas
ni SQL/excepciones internas. Contrato de códigos:

| HTTP | Código | Uso |
| --- | --- | --- |
| 200 | `LISTA_OBTENIDA` | `datos`: array de pasantías autorizadas. |
| 200 | `PASANTIA_OBTENIDA` | `datos`: fila autorizada para editor. |
| 201 | `PASANTIA_CREADA` | ID positivo y creación confirmada. |
| 200 | `PASANTIA_ACTUALIZADA` | ID y `cambios=true`, escritura confirmada. |
| 200 | `PASANTIA_SIN_CAMBIOS` | ID y `cambios=false`, comparación autorizada sin diferencias. |
| 200 | `PASANTIA_ELIMINADA` | ID cuya eliminación se confirmó. |
| 400 / 405 | `OPERACION_INVALIDA` / `METODO_NO_PERMITIDO` | Operación desconocida / método distinto de POST. |
| 401 | `NO_AUTENTICADO` | No existe actor autenticado válido. |
| 403 | `NO_AUTORIZADO` | Perfil/identidad sin autorización, incluida ambigüedad. |
| 403 | `OWNERSHIP_INVALIDO` | Usuario manipulado o recurso ajeno sin facultad global. |
| 403 | `CSRF_INVALIDO` | Token ausente o incorrecto en mutación. |
| 404 | `PASANTIA_NO_ENCONTRADA` | Fila inexistente, incluida eliminación repetida. |
| 422 | `VALIDACION_INVALIDA` | Campos, IDs, referencias u objetivo administrativo inválidos. |
| 409 | `CONFLICTO_PERSISTENCIA` | Integridad o resultado de escritura no confirmado. |
| 500 | `ERROR_PERSISTENCIA` / `ERROR_TECNICO` | Fallo interno, mensaje público sin detalles sensibles. |

No confundir ausencia de fila con denegación ni declarar éxito por recibir HTTP
200 sin verificar `ok`. La comparación sin cambios se efectúa tras autorizar
la fila y validar los datos.

## Contrato frontend

Conservar `cargarPasantia(usuario, destino)` para todos los callers existentes
del AT. `usuario` sirve de objetivo contextual, nunca de autoridad. El modo
propio deriva identidad backend; el global remite objetivo explícito validable.

1. Publicar contexto y token propios en `agr.form.dat.acad.php`, por ejemplo
   `#pasantia-app` con `data-contexto` y `data-csrf`; proteger los bloques de
   otros objetos. No modificar Login/session ni footer.
2. Mantener Agregar y completar editor de los siete campos. Cargar detalle
   persistido antes de editar y no presentar control para cambiar propietario.
   Usar contenedores locales existentes o editor local al destino en
   `pasantia.js`, sin modificar Ficha Profesor/callers.
3. Mostrar acciones según contexto autorizado; backend decide siempre. Asociar
   cada acción con ID, usuario objetivo y destino para no confundir fichas.
4. Reemplazar el contenido del destino al listar; manejar lista vacía y errores.
   Evitar que una respuesta de una selección anterior pinte otro usuario.
5. Renderizar valores y mensajes mediante `.text()`, `.val()` o escape seguro;
   no concatenarlos como HTML ejecutable. Identificadores DOM propios del objeto.
6. Deshabilitar envío mientras está pendiente y restablecerlo al terminar.
   Verificar `ok`, mostrar éxito temporal conforme al patrón vigente y errores
   legibles. Conservar datos del formulario ante fallo; recargar sólo tras éxito.
7. DELETE requiere confirmación; cancelar no emite petición. Confirmar envía
   ID y CSRF, verifica resultado, informa y recarga el contexto correcto.
8. Preservar «Otra Institución» mediante `crearInstitucionContextual` con
   contexto `pasantia`. Al cambiar selección, invalidar el ID contextual antiguo;
   un reintento no debe reutilizarlo para otra institución. El alta del catálogo
   permanece independiente y sin rollback cruzado.
9. Mantener cards, tabla, acciones, mensajes y responsive de los referentes
   cerrados; no introducir estilos globales ni rediseñar Ficha.

## Persistencia

- CREATE: INSERT parametrizado de propietario y siete campos; usar
  `ejecutarEscritura` solicitando `idInsertado`. Exigir `filasAfectadas=1` e ID
  positivo antes de responder `PASANTIA_CREADA`.
- UPDATE: SET exclusivo de los siete campos; WHERE parametrizado por
  `id_pasantia` y `usuario` autorizado. Comparar datos normalizados con la fila
  autorizada para devolver sin cambios; si hay modificación, exigir una fila.
  Cero filas no es éxito genérico: tratar como conflicto si no se ha confirmado
  un estado sin cambios autorizado.
- DELETE: `DELETE FROM pasantia WHERE id_pasantia=:id_pasantia AND usuario=:usuario`;
  `ejecutarEscritura`, exigir una fila. Una repetición no declara éxito falso.
- READ y verificaciones: preparar consultas con la conexión vigente,
  parametrizar todos los valores y obtener FETCH_ASSOC; no ampliar helpers.
- Capturar errores y traducir integridad/conflictos/fallos sin exponer SQL.
  Nunca evaluar el array de escritura como booleano de éxito.
- Sin migración, cambio de esquema, cascadas, nuevo ADR ni transacción
  multiobjeto. Operaciones unitarias; no introducir transacción conjunta con
  Institución ni compensar sus altas con borrados.

## Seguridad

Autenticar y autorizar todas las operaciones, incluidos lista y detalle.
Resolver ownership backend desde sesión y/o fila según operación. Rechazar
manipulación de `usuario` o `id_pasantia`; validar objetivos globales.

Token propio de sesión `csrf_pasantia`, generado siguiendo el precedente
existente (`bin2hex(random_bytes(32))`), publicado por el formulario y remitido
en `X-CSRF-Token`. Verificar tipo/formato y `hash_equals` en CREATE/UPDATE/DELETE.
READ/detail no requieren CSRF, pero mantienen todos los controles de acceso.
No usar la guardia de la página o visibilidad del botón como autorización.

Parametrización SQL y render seguro son obligatorios. La normalización de
texto no reemplaza ninguno de esos controles.

## Validaciones de datos

| Dato | Validación backend obligatoria |
| --- | --- |
| IDs | Entero positivo o representación decimal válida, dentro del rango admitido; rechazar arrays, objetos, fracciones, cero/negativos y cadenas parcialmente numéricas antes del cast. |
| Usuario | Identidad persistida válida y coherente; objetivo administrativo existente; especialización Profesor XOR Estudiante. |
| Institución/País | ID válido y referencia existente antes de escribir. |
| `prof_patr`, `fondo`, `ciudad` | String, trim, no vacío, máximo 45 caracteres; medir caracteres multibyte correctamente, no bytes. Sin regla nueva de minúsculas. |
| `fech_in`, `fech_ter` | Strings obligatorias YYYY-MM-DD, fechas civiles reales y representables en DATE; `fech_in <= fech_ter`. |

Para fechas, seguir el mecanismo backend existente en Postdoctorado:
`DateTimeImmutable::createFromFormat('!Y-m-d', ...)`, comprobación de errores
y comparación exacta del formato, con rango compatible con DATE. En frontend
consumir `fechaCivil` cuando corresponda; no modificarlo. Validar realidad
civil, no sólo expresión regular o normalización automática de fechas inválidas.

Replicar orientación de validación en UI sin depender de ella. No crear nuevas
restricciones de obligatoriedad, estados o aprobación del objeto.

## Corrección Ficha/collapse

En `form-doc/ficha.estudiante.php`, dar a Pasantía un ID exclusivo, por ejemplo
`collapsePasantiaEst`, y actualizar de forma coherente su `data-bs-target`,
`aria-controls` e `id`, manteniendo `data-bs-parent="#accordionEst"`.
Preservar `#pasant_est`, `#beca_est` y el bloque funcional de Beca. La corrección
autorizada no refactoriza el acordeón de Beca ni otras secciones de Ficha.

Comprobar que ningún selector activo adicional requiera cambios fuera de
alcance; de ser necesario, detener. Verificar después apertura independiente
de Pasantía/Beca durante VF.

## Criterios de aceptación

| Área | Criterio de cierre |
| --- | --- |
| CREATE | Propietario validado backend; tercero rechazado; Admin/Comité con objetivo válido; siete campos y referencias validados; CSRF; SQL parametrizado; fila e ID confirmados; JSON estructurado. |
| READ | Propio permitido, ajeno rechazado, global validado, anónimo rechazado; lista/detalle sin SELECT *, asociativos y estructurados; sin XSS ni duplicación. |
| UPDATE | Editor funcional con valores persistidos; ownership por ID; usuario inmutable; siete campos editables/validados; CSRF; SQL parametrizado; cambio/no cambio/conflicto distinguibles. |
| DELETE | Acción funcional y confirmación; cancelación sin petición; ownership por ID y rol global; tercero rechazado; CSRF; borrado físico con una fila afectada; repetición sin éxito falso y padres intactos. |
| UI | Cards/acciones coherentes, mensajes correctos, errores y lista vacía, responsive, render seguro y acordeones independientes. |
| Alcance | Sólo archivos autorizados; catálogos, helpers, objetos cerrados y cascadas preservados; sin pruebas con escrituras de BD. |
| Gobierno | Revisión técnica registrada y VF aprobada por el usuario antes de declarar CRUD cerrado. |

## Validaciones técnicas

Antes de implementar: leer AT/Task, comprobar rama/HEAD/status/staging y
preservar el estado inicial de los archivos objetivo. Revisar diferencias
nuevas sin sobrescribir trabajo local. Ejecutar la intervención completa sólo
cuando se autorice su implementación.

Después de implementar, sin ejecutar el endpoint ni pruebas que escriban BD:

- `php -l` sobre `src/Model/Pasantia.php`, `ajax/pasantia.php`,
  `form-doc/agr.form.dat.acad.php` y `form-doc/ficha.estudiante.php`.
- `node --check form-doc/scripts/pasantia.js`, si Node está disponible;
  documentar cualquier limitación sin presentar la revisión estática como VF.
- Revisar consultas, parámetros, proyección, predicados PK/propietario,
  inmutabilidad, CSRF y códigos de respuesta contra este contrato.
- Buscar callers de `cargarPasantia`, peticiones antiguas de Pasantía,
  IDs/collapse, render inseguro y acumulación de cards; comprobar que todos
  los contratos quedan resueltos sin modificar callers protegidos.
- Revisar el diff de los cinco archivos: cambios compartidos limitados a
  Pasantía y collapse autorizado; sin modificaciones oportunistas.
- `git diff --check`, `git status --short`, `git diff --name-only` y
  `git diff --cached --name-only`; distinguir cambios preexistentes y nuevos.

No ejecutar INSERT/UPDATE/DELETE de prueba, ni siquiera con rollback. No crear
fixtures persistentes, migraciones o archivos de test fuera del alcance.
Registrar resultados reales en esta Task y dejar VF pendiente.

Para esta creación documental ejecutar `git diff --check --
docs/tasks/TASK-EPIC009-PASANTIA-INTEGRAL-001.md`, status y listado de staging.
Como el archivo es no rastreado, complementar con comprobación de whitespace
de su contenido sin añadirlo al índice.

## VF

**Responsable: usuario. Estado: pendiente.** Codex no declara VF aprobada.

| Grupo | Casos mínimos y evidencia esperada |
| --- | --- |
| Estudiante | CREATE, READ lista/detalle, UPDATE y DELETE propios correctos. |
| Profesor | CREATE, READ lista/detalle, UPDATE y DELETE propios correctos. |
| Admin | CRUD completo para objetivo Estudiante y para objetivo Profesor válidos. |
| Comité | CRUD completo para objetivo Estudiante y para objetivo Profesor válidos. |
| Horizontal | Estudiante y Profesor contra registros ajenos: READ/UPDATE/DELETE rechazados; manipular `usuario` en CREATE y `id_pasantia` no autoriza. |
| Actor/CSRF | Anónimo, identidad inválida y ambigua rechazados; CSRF ausente/incorrecto rechaza cada mutación. |
| Referencias | Institución/País inválidos y IDs no escalares/no positivos rechazados. |
| Textos | Vacíos/espacios rechazados; trim; 45 caracteres admitidos y más de 45 rechazados, incluidos multibyte; apóstrofos correctos; HTML como texto inerte. |
| Fechas | Válidas e inicio=término admitidos; imposibles, vacías e inicio>término rechazados, incluidos bisiestos. |
| Edición | Carga persistida, siete campos editables, propietario preservado, manipulación rechazada y edición sin cambios informativa. |
| Eliminación | Cancelar no envía petición; confirmar elimina físicamente sólo Pasantía; repetición no informa éxito; padres permanecen. |
| Institución contextual | Alta independiente conservada; cambiar selección usa ID correcto; fallo posterior de Pasantía no revierte catálogo. |
| Presentación | Lista vacía, recargas sin duplicación, errores backend/red legibles, éxitos temporales, recuperación del formulario, contexto correcto y responsive. |
| Collapse | Pasantía y Beca abren sus secciones de forma independiente. |
| Regresión | Grado, Postdoctorado, Beca, otros antecedentes y catálogos Institución/País conservan funcionamiento. |

Registrar actor/contexto, caso, resultado y aprobación explícita del usuario;
no almacenar credenciales ni datos sensibles. No probar borrados de padres
para demostrar cascadas dentro de esta Task.

## Reversión

Antes de implementar registrar baseline exacto por archivo y conservar los
cambios locales permitidos. La reversión consiste en deshacer sólo los hunks
de esta Task; no usar un reset global ni restaurar indiscriminadamente HEAD.

| Archivo | Reversión delimitada |
| --- | --- |
| `src/Model/Pasantia.php` | Restaurar modelo previo a la intervención. |
| `ajax/pasantia.php` | Restaurar endpoint previo junto con el frontend compatible. |
| `form-doc/scripts/pasantia.js` | Restaurar script previo y su contrato de petición/respuesta en conjunto con endpoint/modelo. |
| `form-doc/agr.form.dat.acad.php` | Retirar sólo contexto/token/marcado introducidos para Pasantía, sin afectar otros bloques. |
| `form-doc/ficha.estudiante.php` | Revertir sólo los atributos del collapse modificados por esta Task. |
| Esta Task | Registrar reversión, causa y estado real; preservar decisiones y evidencia. |

Revertir el conjunto coherentemente para evitar frontend/backend incompatibles.
La reversión de código reintroduce los defectos del baseline y debe declararse.
No diseñar ni ejecutar restauración automática de datos. DELETE físico no es
reversible mediante rollback de código; tampoco se borran instituciones como
compensación de altas realizadas durante VF.

## Riesgos

- Las cascadas desde padres permanecen por decisión aprobada.
- Otra Institución puede quedar sin uso si falla Pasantía; independencia aprobada.
- Cambios concurrentes pueden producir cero filas: gestionar como resultado
  no confirmado, sin éxito falso ni transferencia de ownership.
- La modernización del JSON exige actualizar todas las peticiones internas de
  Pasantía en la misma intervención; los callers públicos deben conservarse.
- Esquema inspeccionado localmente; una divergencia incompatible en otro
  ambiente exige detener, no crear migración implícita.
- Este cierre no resuelve defectos de otros endpoints/Ficha; VF pendiente impide
  afirmar cierre funcional anticipado.

## Condiciones para detener la ejecución

Detener y reportar, sin reinterpretar decisiones ni ampliar alcance, si:

1. El AT aprobado no está disponible, no es legible o su contenido contradice
   la fuente aprobada.
2. Aparece contradicción AT/código que afecta reglas institucionales.
3. Se requiere modificar cualquier archivo fuera de los seis autorizados.
4. Se requiere modificar Authorization, Login o contratos de sesión.
5. Se requiere migración, cambio de esquema o nuevo ADR no previsto.
6. Se requiere cambiar cascadas/FKs.
7. Se requiere modificar reglas de Institución/País.
8. No se puede preservar ownership inmutable y autorización backend.
9. Se requiere redefinir la frontera de Pasantía o dividir el objeto para
   resolverlo fuera de la única intervención integral.
10. Existen cambios locales incompatibles en archivos objetivo o no puede
    preservarse el trabajo ajeno.

La creación de esta Task no inicia implementación. No staging, commit ni push
en esta ejecución; cualquier paso posterior requiere su autorización vigente.
