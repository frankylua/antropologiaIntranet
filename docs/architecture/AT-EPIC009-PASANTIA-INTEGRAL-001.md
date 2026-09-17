# AT-EPIC009-PASANTIA-INTEGRAL-001

- **Objeto:** Pasantía; entidad autoridad `pasantia`.
- **EPIC principal:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Coordinación:** EPIC-003 — Identidad y autorización; EPIC-008 — Persistencia.
- **Nivel:** L3 — Documentación arquitectónica/técnica previa a Task.
- **Estado del AT:** LISTO PARA APROBACIÓN.
- **Decisiones institucionales:** APROBADAS por el usuario en la solicitud de creación de este AT.
- **Implementación:** NO INICIADA; **VF:** PENDIENTE, a cargo del usuario.
- **Task prevista:** `TASK-EPIC009-PASANTIA-INTEGRAL-001`, NO CREADA.
- **Blockers:** NINGUNO identificado para el alcance aprobado.
- **Fecha:** 2026-09-16.

## 1. Objetivo

Consolidar la inspección integral aprobada de Pasantía y las decisiones
institucionales posteriores, y delimitar una única Task que complete CREATE,
READ, UPDATE y DELETE con autorización, ownership, validación, persistencia y
presentación coherentes. Este documento no implementa ni autoriza por sí mismo
la implementación.

## 2. Contexto

Baseline comprobado al crear el AT:

```text
rama: refactor/fase-0-seguridad
HEAD: 427d281c2e1c588f332cf2375f30eafac05cbd57
staging: vacío
cambios tracked: ninguno
```

El último objeto integrado es `TASK-EPIC009-BECA-INTEGRAL-001`. Al iniciar sólo
existían los dos archivos no rastreados excluidos en la sección 13. El archivo
objetivo de este AT no existía.

Fuentes de autoridad y evidencia:

- Inspección integral de Pasantía de esta conversación, aprobada expresamente
  como fuente técnica por el usuario. Incluye consultas de sólo lectura al
  esquema y datos agregados de la BD local; no acredita otros ambientes.
- Solicitud «EPIC-009 — CREACIÓN AT INTEGRAL PASANTÍA»: fuente directa de las
  decisiones institucionales de la sección 6 y de las exclusiones.
- `src/Model/Pasantia.php`, `ajax/pasantia.php`,
  `form-doc/scripts/pasantia.js`, formulario compartido, fichas y callers
  identificados en la sección 11.
- `src/Security/Authorization.php`, contratos actuales de sesión y precedente
  implementado en `ajax/postdoctorado.php` y `src/Model/Postdoctorado.php`.
- Grado, Postdoctorado y Beca como referentes existentes; ADR-001 en
  `docs/adr/ADR-001-contrato-explicito-operaciones-escritura.md` y reglas de
  EPIC-009 en `docs/roadmap/ROADMAP.md`.

La revisión de creación confirma el mismo HEAD, los flujos CREATE/READ y la
colisión de collapse. No se repitió la inspección de BD ni se ejecutaron VF,
peticiones de mutación o cambios de datos. No surgió evidencia incompatible
con las decisiones aprobadas.

## 3. Frontera del objeto

Pasantía es un antecedente académico individual, no un recurso compartido.
Ficha consume y presenta el objeto; no es autoridad de propiedad.

| Propiedad | Contrato persistido |
| --- | --- |
| Tabla | `pasantia`, InnoDB |
| PK | `id_pasantia`, entero autoincremental |
| Propietario | `usuario` → `usuario.id_usuario` |
| Institución | `inst_pasant` → `institucion.id_inst` |
| País | `pais_pasant` → `pais.id_pais` |
| Inicio / término | `fech_in`, `fech_ter`: `DATE NOT NULL` |
| Descriptivos | `prof_patr`, `fondo`, `ciudad`: `VARCHAR(45) NOT NULL` |

Los nombres de Institución y País pertenecen a sus catálogos. Pasantía sólo
almacena sus referencias. Patrocinante y fondo son textos, no relaciones a
Profesor, Financiamiento o Beca.

No se identificaron FKs entrantes, tablas intermedias, documentos, archivos ni
triggers asociados. Las tres FKs salientes tienen `ON DELETE CASCADE` y
`ON UPDATE CASCADE`. Borrar una Pasantía no elimina sus padres; borrar un padre
puede eliminar Pasantías. Estas reglas de FK permanecen intactas.

La inspección local registró dos filas, sin fechas invertidas/cero, referencias
huérfanas ni descriptivos vacíos en las comprobaciones agregadas realizadas.
No se propone limpieza automática de datos históricos.

## 4. Estado actual CRUD

| Operación | Estado EPIC-009 | Flujo y evidencia |
| --- | --- | --- |
| CREATE | Defectuoso | `form_pasantia` → `op=insert-update` → `Pasantia::insertar()` → INSERT. `$id_pasant=0` hace que siempre inserte. |
| READ | Defectuoso | `cargarPasantia(usuario, destino)` → `op=read` → `mostrar($usuario)` → SELECT con INNER JOIN a Institución/País y filtro por usuario cliente. |
| UPDATE | Falta | Botón «Editar Pasantía» y contenedores de edición, sin handler, rama ejecutable ni UPDATE de Pasantía. |
| DELETE | Falta | `.borrar_pasantia` sólo cierra el formulario de alta. No hay eliminación persistida. |

CREATE recibe `usuario`, `prof`, `inst`, `fondo`, `ciudad`, `pais`, `fecha_in`
y `fecha_ter`. El usuario viene de `#id_usuario[name]`; el backend sólo aplica
casts parciales, sin contrastarlo con sesión. Devuelve un string JSON de
mensaje, tratando el array de escritura como booleano e ignorando filas e ID.

READ devuelve un array asociativo compatible con FETCH_ASSOC. El render usa
`id_pasantia`, `prof_patr`, `inst`, `fech_in`, `fech_ter`, `fondo`, `ciudad` y
`pais`. No existe lectura individual por ID. `SELECT *` expone columnas
adicionales sin necesidad de presentación.

El endpoint no autentica, autoriza ni exige CSRF. Las guardias de las páginas
no protegen su invocación directa. Los comentarios de edición/eliminación
copiados de Pueblo/Institución no constituyen operaciones del objeto.

## 5. Riesgos confirmados

- Acceso horizontal y anónimo: READ acepta cualquier usuario solicitado;
  CREATE permite indicar un propietario ajeno que satisfaga la FK.
- SQL interpolado: riesgo de inyección y fallo con apóstrofos; falta de
  validaciones backend de referencias, tipos, textos y fechas.
- CREATE sin CSRF; los futuros UPDATE/DELETE deben nacer protegidos.
- Render sin escape suficiente: riesgo de XSS almacenado.
- Recarga mediante `append` sin limpieza: cards duplicadas; mensajes de éxito
  sin comprobación funcional, acciones inertes y errores no gestionados.
- Colisión `collapsePasant` entre Pasantía y Beca en Ficha Estudiante.
- Borrado por cascada desde padres y alta de Institución independiente del
  éxito posterior de Pasantía.

La edición/eliminación ajena no se clasifica como una ruta existente
explotable: UPDATE y DELETE faltan. Su protección es parte del contrato objetivo.

## 6. Decisiones institucionales aprobadas

Las siguientes reglas proceden de la solicitud del usuario; no son inferencias
a partir de los objetos precedentes:

1. Pasantía es individual y `pasantia.usuario` es autoridad de ownership.
2. Estudiante y Profesor con perfil válido tienen CRUD exclusivamente propio.
   Un tercero sin facultades globales no puede leer ni mutar el recurso ajeno.
3. Admin y Comité tienen CRUD global sujeto a un usuario objetivo válido.
   Anónimo no tiene acceso al endpoint.
4. La identidad propia debe ser válida y coherente. Al resolver especialización
   se aplica Profesor XOR Estudiante; una identidad ambigua no obtiene
   autorización implícita.
5. UPDATE puede cambiar institución, país, patrocinante, fondo, ciudad y ambas
   fechas. El propietario `usuario` es inmutable.
6. DELETE elimina físicamente la fila; no hay estado funcional ni baja lógica.
   No elimina Usuario, Institución o País ni modifica sus cascadas existentes.
7. Ambas fechas son obligatorias, civiles válidas, coherentes con DATE y deben
   cumplir inicio <= término. Se rechazan fechas imposibles.
8. `prof_patr`, `fondo` y `ciudad` son obligatorios: trim, no vacíos y máximo
   45 caracteres conforme al esquema. No se agrega una regla de minúsculas.
9. Institución y País deben existir; sus nombres siguen siendo de los catálogos.
10. Se conserva «Otra Institución» como alta contextual independiente. No hay
    transacción conjunta ni rollback cruzado si luego falla Pasantía.
11. CREATE, UPDATE y DELETE requieren CSRF válido. READ exige autenticación,
    autorización y ownership, sin CSRF como protección principal.
12. Las escrituras usan `ejecutarEscritura`, SQL parametrizado, filas afectadas
    y el ID cuando sea necesario para confirmar CREATE. No cambian helpers.
13. Se completan acciones, mensajes, render seguro, recarga y acordeones, con
    Grado/Postdoctorado/Beca como referencias visuales, sin rediseñar Ficha.

No quedan decisiones institucionales bloqueantes para este alcance.

## 7. Matriz de autorización

| Actor | CREATE | READ | UPDATE | DELETE |
| --- | --- | --- | --- | --- |
| Estudiante propietario | Sí | Sí | Sí | Sí |
| Estudiante no propietario | No | No | No | No |
| Profesor propietario | Sí | Sí | Sí | Sí |
| Profesor no propietario | No | No | No | No |
| Admin | Global | Global | Global | Global |
| Comité | Global | Global | Global | Global |
| Anónimo | No | No | No | No |

«Sí» exige perfil válido y operación propia; en CREATE significa crear para
la propia identidad. «Global» exige objetivo de usuario válido, con
especialización Profesor XOR Estudiante. Las filas de no propietario describen
actores sin facultades Admin/Comité; si existe un rol global válido se aplica
la fila correspondiente. La UI no sustituye estas comprobaciones backend.

## 8. Ownership y resolución del actor

Reutilizar los contratos de sesión y las APIs `Authorization::hasAny()` y
`Authorization::hasCapability()` en lectura, sin redefinirlos. El precedente
vigente verifica login y rol coherentes, identidad persistida única y acceso
propio; para Estudiante utiliza la capacidad existente `perfil.ver`, y para
Profesor la identidad `docente`. No se crean roles, capacidades ni estados.

Para CREATE y READ lista propios, derivar el usuario de la identidad efectiva
de sesión. Si el cliente también envía `usuario`, validarlo y rechazar una
discrepancia; nunca usarlo como sustituto de esa identidad. Admin/Comité deben
indicar un objetivo válido para crear o listar.

Para READ detalle, UPDATE y DELETE, cargar la fila por `id_pasantia` y obtener
su `usuario` persistido. Autorizar contra el actor efectivo o sus facultades
globales, validando la identidad objetivo. Un usuario contextual remitido por
el cliente debe ser coherente con la fila; no puede transferir propiedad.

UPDATE y DELETE deben condicionar la escritura tanto por PK como por el
propietario autorizado. UPDATE no incluirá `usuario` en SET. Validar IDs
positivos y entradas escalares antes de acceder a persistencia. No entregar
datos de una fila ajena al resolver una solicitud denegada.

## 9. Contrato CRUD objetivo

El único endpoint sigue siendo `ajax/pasantia.php`. La futura Task concretará
los nombres de operaciones y funciones internas con sus callers dentro del
alcance; `insert-update` no podrá seguir aparentando una actualización que
no implementa. Se preserva `cargarPasantia(usuario, destino)` como entrada
pública para los consumers existentes.

| Operación | Entrada y controles | Resultado verificable |
| --- | --- | --- |
| CREATE | Actor/objetivo autorizado, siete campos editables, referencias existentes, fechas/textos válidos y CSRF | INSERT de una fila; `filasAfectadas=1` e `idInsertado` positivo; devolver `id_pasantia`. |
| READ lista | Actor y usuario objetivo autorizado | Filas asociativas con proyección explícita; lista vacía válida. |
| READ detalle | ID positivo, fila existente y ownership autorizado | Fila autorizada con IDs de referencias y campos necesarios para editar. |
| UPDATE | ID, fila/propietario autorizados, campos válidos y CSRF | Actualizar sólo los siete campos; distinguir cambio confirmado, ausencia de cambios y conflicto. |
| DELETE | ID, fila/propietario autorizados y CSRF | DELETE físico de una fila; no tocar padres; no informar éxito de borrado si no se eliminó una fila. |

La validación backend debe ser independiente del frontend: tipos escalares,
IDs, existencia de referencias, trim/no vacío/longitud de descriptivos y fechas
civiles reales en formato DATE, con inicio <= término. No basta el cast a
entero ni un input HTML de fecha. Las validaciones UI reproducen estas reglas
para orientar al usuario, sin convertirse en autoridad.

Usar respuestas JSON estructuradas con éxito explícito, mensaje y datos o
código de error, compatibles con el patrón de objetos cerrados. Distinguir
sesión ausente, falta de autorización, entrada inválida, recurso inexistente,
conflicto y fallo técnico mediante estado HTTP y mensaje coherentes. No exponer
SQL ni detalles internos. La lectura usa propiedades asociativas explícitas,
sin depender de índices numéricos ni publicar campos innecesarios.

La edición sin cambios puede devolver éxito informativo sin afirmar que hubo
una modificación. La eliminación repetida debe informar que el recurso ya no
existe o que no se confirmó una eliminación; no fingir otra fila eliminada.

## 10. Persistencia

Aplicar ADR-001 dentro de la unidad funcional EPIC-009, con coordinación
EPIC-008. CREATE ya usa el helper correcto, pero debe consumir su resultado
explícito y solicitar el ID generado. UPDATE y DELETE usarán el mismo contrato
con comprobación de filas afectadas; no se evalúa el array como booleano.

Parametrizar lecturas y escrituras del objeto. Los contratos actuales de PDO
y `ejecutarEscritura($sql, $parametros, $obtenerIdInsertado, $tipos)` permiten
resolverlo sin modificar infraestructura. Las lecturas parametrizadas pueden
usar la conexión vigente y FETCH_ASSOC; no se ampliarán los helpers globales
para adaptar `ejecutarConsultaResultados`.

Una operación unitaria afecta una sola fila de Pasantía. No requiere
transacción explícita ni transacción multiobjeto. La verificación de ownership
y los predicados de escritura deben conservar coherencia ante cambios
concurrentes y gestionar cero filas sin declarar éxito falso.

| Decisión arquitectónica | Requerimiento |
| --- | --- |
| Nuevo ADR | NO |
| Migración BD | NO |
| Cambio de esquema | NO |
| Transacción multiobjeto | NO |
| Modificación de helpers globales | NO |
| Modificación de Authorization/Login/session | NO |

## 11. UI e integración con Ficha

Consumers activos que deben conservarse:

| Archivo | Destino de Pasantía |
| --- | --- |
| `form-doc/scripts/info.estudiante.js` | `#pasant_est` |
| `form-doc/scripts/ficha.estudiante.js` | `#pasantia_card` |
| `admin/scripts/ver.estudiante.js` | `#pasant_est` |
| `form-doc/scripts/info.docente.js` | `#ficha_pasantia` |
| `form-doc/scripts/ficha.docente.js` | `#pasantia_card`, `#ficha_pasantia` |
| `admin/scripts/ver.docente.js` | `#ficha_pasantia` |

`form-doc/footer.php` ya carga el script. Las fichas Estudiante/Profesor incluyen
el formulario académico compartido. No se requiere alterar esos callers ni
la ficha de Profesor para mantener la entrada pública y sus destinos.

El frontend de Pasantía debe:

- Implementar alta, carga de detalle, edición, cancelación y eliminación con
  confirmación explícita; retirar acciones inertes y residuos del objeto.
- Recibir contexto y token CSRF propios desde `agr.form.dat.acad.php`, usando
  el contrato de sesión vigente, sin modificar configuraciones globales.
- Reemplazar el contenido del destino al recargar, con identificadores propios
  del objeto y sin duplicar cards o confundir destinos administrativos/propios.
- Escapar contenido o insertar nodos de texto; no interpolar valores del
  servidor sin protección ni usar mensajes backend como HTML ejecutable.
- Mostrar éxito únicamente con resultado confirmado; gestionar lista vacía,
  errores de backend/red y envío pendiente, conservando datos ante un fallo.
- Mantener «Otra Institución» mediante `crearInstitucionContextual` y el
  contexto `pasantia`; corregir localmente el estado del ID seleccionado tras
  cambios/reintentos. No alterar el helper ni el endpoint del catálogo.
- Alinear cards, botones, mensajes y estructura responsive con los referentes
  cerrados, sin cambios globales de estilos ni rediseño de Ficha.

La colisión continúa en `form-doc/ficha.estudiante.php`: Pasantía y Beca usan
`collapsePasant`, y el segundo bloque apunta a `#accordionExample` aunque el
contenedor es `#accordionEst`. Corregir exclusivamente identificadores y
atributos de enlace/parent necesarios para separar ambos acordeones,
preservando `#pasant_est`, `#beca_est` y la implementación de Beca. Esta
corrección de marcado es la única excepción localizada en Ficha Estudiante;
no reabre Beca ni cierra Ficha integral.

## 12. Alcance técnico probable

Una única Task integral, limitada a estos cinco archivos funcionales:

| Archivo | Responsabilidad prevista |
| --- | --- |
| `src/Model/Pasantia.php` | CRUD parametrizado, detalle, referencias/objetivo válido, ownership persistido y contrato de escritura. |
| `ajax/pasantia.php` | Resolución del actor, autorización, ownership, CSRF, validaciones y respuestas CRUD. |
| `form-doc/scripts/pasantia.js` | Flujos completos, render seguro, mensajes, recarga y contexto de cada consumer. |
| `form-doc/agr.form.dat.acad.php` | Configuración y marcado exclusivos de Pasantía; preservar bloques de otros objetos. |
| `form-doc/ficha.estudiante.php` | Únicamente corrección localizada de collapse Pasantía/Beca. |

El documento de Task futuro deberá concretar contratos internos, cambios,
validación técnica, reversión y condiciones de detención dentro de esa
frontera. No se divide automáticamente el objeto en cuatro Tasks CRUD.

## 13. Exclusiones

No planificar modificaciones de:

- `src/Security/Authorization.php`, `src/Model/Login.php`, `ajax/login.php` y
  contratos/bootstrap de sesión.
- `src/Config/conexion.php`, conexión y configuración global.
- `js/funcAjax.js`, `js/funcForm.js`, `js/funcValid.js`, `js/fechaCivil.js` y
  auxiliares compartidos, incluido `form-doc/scripts/usuario.js`.
- Implementaciones cerradas de Grado, Postdoctorado y Beca; sus bloques en el
  formulario compartido permanecen protegidos.
- Modelos/endpoints y gobierno de catálogos Institución y País, incluidas FKs.
- Resto de Ficha integral, callers enumerados, footer, reportes y estilos
  globales. La etiqueta residual de Pasantía en `admin/filtrar.informe.php` no
  incorpora un nuevo consumer CRUD al alcance.
- ADR, Roadmap, esquema, migraciones y datos históricos.

Exclusión expresa, sin lectura como autoridad ni modificación:

```text
docs/architecture/ADR-003-RECURSOS-ACADEMICOS-COMPARTIDOS-PARTICIPACION.md
files/prog_curso/curso_45afc744db0fb82f1db6f16d31ded67a.pdf
```

Pasantía no requiere participantes ni ownership colectivo; no necesita un
nuevo ADR. Esta ejecución crea exclusivamente el presente AT, sin Task,
implementación, staging, commit o push.

## 14. VF propuesta

La ejecutará el usuario después de la futura implementación. No se considera
realizada por la inspección estática ni por este AT.

| Grupo | Casos y resultado esperado |
| --- | --- |
| CRUD propio | Estudiante y Profesor con perfil válido: crear, listar, ver detalle, editar y eliminar registros propios en su Ficha. |
| CRUD global | Admin y Comité: CRUD de objetivos válidos Estudiante/Profesor, conservando el contexto seleccionado. |
| Acceso horizontal | READ lista/detalle, UPDATE y DELETE ajenos por Estudiante/Profesor: rechazo sin exposición ni cambios. CREATE manipulando `usuario`: rechazo, sin fila ajena. |
| Actor | Anónimo sin acceso; identidad inexistente, inválida o ambigua sin autorización implícita. Comprobar acceso vigente conforme a contratos existentes. |
| CSRF | CREATE/UPDATE/DELETE con token ausente o incorrecto: rechazo sin escritura; token válido permite continuar los demás controles. |
| Referencias e IDs | Institución/País inexistentes, ID inválido o entrada no escalar: rechazo controlado. |
| Textos | Vacíos y sólo espacios rechazados; trim; 45 caracteres admitidos y 46 rechazados, incluidos caracteres multibyte; apóstrofos persistidos correctamente. |
| HTML | Caracteres y cadenas HTML se muestran como texto, sin ejecución ni alteración de la estructura. |
| Fechas | Fechas civiles válidas y fechas iguales admitidas; invertidas e imposibles rechazadas; comprobar años bisiestos y valores vacíos. |
| Ownership en UPDATE | Cambiar campos permitidos conserva `usuario`; manipular propietario no transfiere la fila. |
| Resultado UPDATE | Edición con cambios confirmada; edición sin cambios informativa y sin éxito ficticio de filas modificadas. |
| DELETE | Confirmación/cancelación; borrado físico de la fila, padres intactos; repetición sin informar una segunda eliminación exitosa. |
| Otra Institución | Alta contextual y uso de la FK; si falla Pasantía, Institución permanece y no se ejecuta rollback cruzado. Cambiar selección/reintentar usa el ID correcto. |
| Render y recarga | Crear/editar/eliminar y recargar reiteradamente sin duplicados; lista vacía explícita; destinos propios/administrativos correctos. |
| Fallos | Error backend/red con mensaje de error, sin falso éxito; conservar formulario y permitir recuperación. |
| Ficha | Acordeones Pasantía/Beca independientes en Estudiante; presentación en Profesor y administración; revisión responsive. |
| Regresión | Grado, Postdoctorado, Beca y demás antecedentes siguen funcionando; catálogos y flujo contextual conservan su contrato. |

La Task deberá acompañar la VF con revisión técnica de sintaxis PHP/JS,
SQL parametrizado, controles de autorización/CSRF y diff limitado a su alcance.
Las pruebas destructivas o de cascadas de catálogos no forman parte de esta VF.

## 15. Riesgos residuales

- Las cascadas desde Usuario/Institución/País permanecen y pueden borrar
  Pasantías al eliminar un padre; es una exclusión aprobada, no un defecto
  ocultamente corregido en este objeto.
- Una Institución creada puede quedar sin uso si falla Pasantía. Se conserva
  por decisión expresa la independencia de ambas operaciones.
- La evidencia de esquema corresponde a BD local. Si otro ambiente presenta
  dependencias o restricciones incompatibles, se detiene la implementación
  afectada para revisión; no se crea una migración por cuenta propia.
- El cierre del objeto no corrige vulnerabilidades de otros endpoints ni
  moderniza Ficha integral o catálogos compartidos.
- La VF pendiente debe confirmar interacción real, errores y regresiones; la
  viabilidad técnica documentada no equivale a validación funcional aprobada.

Detener y elevar cualquier contradicción con decisiones institucionales,
necesidad de ADR/migración no prevista, cambio obligatorio de archivos
protegidos o ampliación que impida el objeto integral único. No reinterpretar
las reglas ni ampliar silenciosamente la futura Task.

## 16. Dictamen

**A. AT listo para aprobación y generación de Task integral.**

La frontera es clara, las decisiones institucionales están aprobadas y no se
detectaron contradicciones nuevas ni bloqueantes técnicos. El CRUD integral
es viable mediante los cinco archivos delimitados, sin nuevo ADR, migración,
cambio de esquema, transacción multiobjeto o cambios Authorization/Login.

Este dictamen no marca el AT como aprobado ni la implementación/VF como
completadas. La aprobación del AT es el siguiente hito de gobierno.

## 17. Siguiente paso

Tras la aprobación explícita de este AT, crear una única
`TASK-EPIC009-PASANTIA-INTEGRAL-001` con el contrato técnico, alcance,
validaciones, reversión y condiciones de detención aquí delimitados.

En esta ejecución no se crea esa Task, no se implementa código, no se modifica
BD y no se realiza staging, commit ni push.
