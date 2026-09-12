# AT-EPIC009-POSTDOCTORADO-INTEGRAL-001 — Postdoctorado integral

- **EPIC principal:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Coordinación:** EPIC-003 — Identidad y autorización; EPIC-008 — Persistencia.
- **Objeto:** Postdoctorado.
- **Entidad autoridad:** `postdoctorado`.
- **Consumer principal:** Ficha Académica de Estudiante y Profesor.
- **Clasificación:** [ARQ] [EPIC-009] [AUTH] [CRUD] [SEC] [PERSIST] [VF] [GOV].
- **Estado:** APROBADO — IMPLEMENTADO — VF APROBADA — CERRADO.
- **Arquitectura:** APROBADA.
- **Matriz institucional:** APROBADA.
- **Ownership:** APROBADO.
- **Alcance:** CERRADO.
- **Decisiones institucionales pendientes:** NINGUNA.
- **Implementación:** COMPLETADA.
- **Revisión técnica:** APROBADA.
- **VF:** APROBADA POR EL USUARIO.
- **Blockers:** NINGUNO.
- **Task integral:** CERRADA.
- **Fecha:** 2026-09-12.
- **Tipo de ejecución:** análisis estático y consolidación documental; no se ejecutaron operaciones HTTP, escrituras, VF ni cambios de datos.

## 1. Propósito y dictamen ejecutivo

Este AT consolida la inspección integral aprobada de Postdoctorado, la matriz
institucional aprobada, las reglas de ownership, los defectos técnicos vigentes
y la frontera exacta de una futura Task integral.

Postdoctorado es una entidad persistente independiente. Ficha Académica sólo la
consume y presenta. La autoridad de propiedad es `postdoctorado.usuario` y la
matriz funcional es la misma aprobada para Grado Académico: Profesor y
Estudiante con acceso vigente operan sólo sus propios registros; Admin y Comité
poseen CRUD global sobre registros de Profesores y Estudiantes válidos.

Los cuatro contratos CRUD existen y los cuatro son defectuosos. El cierre
integral puede realizarse sin cambio inicial de schema, sin migraciones, sin
reabrir Institución y sin crear un workflow de aprobación. DELETE permanece
físico. Las fechas nuevas o editadas deben cumplir formato, realidad civil y
orden temporal; la fila histórica ya detectada con fechas invertidas se
preserva mientras no sea editada expresamente.

La futura unidad de implementación queda cerrada a cuatro archivos
funcionales. No se identificó contradicción institucional o técnica, un AT
integral equivalente, una Task integral previa, otro objeto precedente ni un
consumer que amplíe materialmente el alcance.

**Dictamen: A. AT sincronizado y formalmente aprobado; puede crearse la Task
integral.**

## 2. Estado Git y alcance de esta ejecución

Baseline inspeccionado:

```text
rama: refactor/fase-0-seguridad
HEAD: 1a89fdb86fbea210a217dc6eef6841bab8246957
```

Al iniciar la inspección existían dos archivos no versionados y ajenos a este
AT:

```text
docs/architecture/ADR-003-RECURSOS-ACADEMICOS-COMPARTIDOS-PARTICIPACION.md
files/prog_curso/curso_45afc744db0fb82f1db6f16d31ded67a.pdf
```

Ambos se preservan intactos. El PDF histórico fue excluido de la inspección. El
ADR no versionado no fue usado como autoridad ni reabierto; la frontera de
ADR-003 aplicable a este AT proviene de la fuente aprobada suministrada y de la
documentación versionada vigente.

Esta ejecución crea exclusivamente el presente documento. No modifica código,
base de datos, schema, migraciones, Roadmap, ADR, addendum, Tasks ni staging.

## 3. Fuentes y evidencia inspeccionada

Fuente institucional directa:

- resultado aprobado de la Inspección integral de Postdoctorado, incorporado
  como fuente exacta en la solicitud de este AT;
- matriz institucional aprobada de Profesor, Estudiante, Admin, Comité y
  Anónimo;
- decisiones aprobadas sobre identidad XOR, ownership, ausencia de aprobación,
  DELETE físico, fechas, registro histórico, dependencias, alcance y VF.

Evidencia técnica principal:

- `src/Model/Postdoctorado.php`;
- `ajax/postdoctorado.php`;
- `form-doc/scripts/postdoctorado.js`;
- `form-doc/agr.form.dat.acad.php`;
- `form-doc/ficha.estudiante.php` y `form-doc/ficha.docente.php`;
- `form-doc/info.estudiante.php` y `form-doc/info.docente.php`;
- scripts de Ficha de Estudiante, Profesor y vistas administrativas que llaman
  a `cargarPostdoc(...)`;
- `src/Model/Grado.php`, `ajax/grado.php` y el AT integral de Grado como patrón
  consolidado compatible;
- `src/Security/Authorization.php`;
- contratos vigentes de sesión, identidad y `perfil.ver` de EPIC-003;
- `src/Config/conexion.php` y `src/Config/ConnectionAuthority.php`;
- `src/Model/Institucion.php` y `ajax/institucion.php` sólo para comprobar el
  contrato cerrado que Postdoctorado consume;
- documentación vigente de EPIC-003, EPIC-008 y EPIC-009;
- referencias e historial Git de Postdoctorado y sus consumers.

La búsqueda versionada no encontró un AT integral ni una Task integral de
Postdoctorado. Tampoco apareció una fuente contradictoria, un cambio obligatorio
de schema, una necesidad de reabrir Institución o un quinto archivo funcional
necesario para expresar el contrato previsto.

## 4. Frontera del objeto

Postdoctorado es un antecedente académico individual y una entidad autoridad
independiente:

```text
postdoctorado
├── id_postdoc      → identidad persistente
├── usuario         → propietario; FK a usuario
├── inst_postdoc    → FK a institucion.id_inst
├── prof            → Profesor/a patrocinante
├── fecha_inicio    → fecha civil de inicio
└── fecha_termino   → fecha civil de término
```

La PK vigente es `id_postdoc`. La propiedad no se deriva desde la pantalla, la
URL, un botón ni un valor remitido por el cliente: pertenece a
`postdoctorado.usuario`.

Ficha Académica no es autoridad sobre Postdoctorado. Sólo consume sus datos y
presenta las operaciones disponibles en el contexto autorizado. Cerrar
Postdoctorado mejora parcialmente la robustez de Ficha, pero no cierra ni
refactoriza Ficha Académica como objeto integral.

Postdoctorado no requiere la consolidación previa de otro objeto. Institución
es una dependencia cerrada y protegida; Postdoctorado consume su lista, alta
contextual y FK vigentes sin redefinirlos.

ADR-003 sobre recursos académicos compartidos no aplica a Postdoctorado. No se
introducen participantes, Autor/Coautor, ownership colectivo ni relaciones de
copropiedad.

## 5. Consumers confirmados

| Consumer | Uso de Postdoctorado | Contrato que debe preservarse |
| --- | --- | --- |
| Ficha Estudiante propia | Listar y operar varios Postdoctorados del Estudiante autenticado | Propietario derivado de sesión y `perfil.ver` vigente |
| Ficha Profesor propia | Listar y operar varios Postdoctorados del Profesor autenticado | Identidad Docente y acceso vigente a Mi Perfil |
| Ficha Estudiante administrativa | Consultar y operar Postdoctorados del Estudiante seleccionado | Rol Admin o Comité y objetivo backend válido |
| Ficha Profesor administrativa | Consultar y operar Postdoctorados del Profesor seleccionado | Rol Admin o Comité y objetivo backend válido |
| Alta contextual de Institución | Crear una Institución durante edición de Postdoctorado | Contrato contextual vigente de Institución |

Los scripts `form-doc/scripts/info.estudiante.js`,
`form-doc/scripts/info.docente.js`, `form-doc/scripts/ficha.estudiante.js`,
`form-doc/scripts/ficha.docente.js`, `admin/scripts/ver.estudiante.js` y
`admin/scripts/ver.docente.js` llaman a `cargarPostdoc(...)`. Todos pertenecen
a los contexts de Ficha ya contemplados y no requieren modificar sus contratos
si la función conserva su firma pública.

`form-doc/footer.php` carga el script de Postdoctorado, pero no necesita cambios
para el contrato previsto y queda protegido. No se identificó otro endpoint
activo o consumer que materialmente cambie la frontera.

## 6. Estado CRUD actual

| Operación | Estado EPIC-009 | Flujo vigente | Defecto principal |
| --- | --- | --- | --- |
| CREATE | Existe / defectuoso | `op=insert` → `Postdoctorado::insertar()` | Acepta `usuario` cliente, no autentica ni autoriza, no valida referencias/fechas, interpola SQL y trata como booleano el resultado estructurado de `ejecutarEscritura()` |
| READ lista | Existe / defectuoso | `op=read` → `Postdoctorado::mostrar($usuario)` | Acepta propietario cliente, permite consulta directa sin guard y expone registros ajenos por IDOR |
| READ detalle | Existe / defectuoso | `op=read_postdoc_id` → `Postdoctorado::mostrarPostdoc($id_postdoc)` | Consulta sólo por ID, sin autenticación, autorización ni ownership |
| UPDATE | Existe / defectuoso | `op=update` → `Postdoctorado::editarPostdoc()` | Confía en `id_postdoc`, no comprueba propietario, no valida referencias/fechas, interpola SQL y no interpreta `filasAfectadas` |
| DELETE | Existe / defectuoso | `op=delete` → `Postdoctorado::eliminar()` | Confía en `id_postdoc`, no exige autorización/ownership/CSRF, interpola SQL, usa `ejecutarConsulta()` y no distingue cero filas |

El cast parcial de IDs en `ajax/postdoctorado.php` no valida el formato original,
la existencia, la autorización ni el ownership. La inclusión del formulario
desde una página protegida tampoco protege el endpoint, que puede invocarse
directamente.

En el frontend vigente:

- `usuario` se obtiene del DOM y se remite como si fuera autoridad;
- todas las respuestas se parsean manualmente;
- no se distingue éxito HTTP de error funcional;
- CREATE, UPDATE y DELETE carecen de CSRF;
- DELETE se ejecuta sin confirmación;
- los callbacks pintan mensajes de éxito incluso cuando el texto recibido
  declara un fallo;
- los refresh mezclan identificadores de contexto y pueden duplicar o refrescar
  incorrectamente la sección;
- READ detalle supone siempre `postdoc[0]`;
- no existe manejo explícito de listas vacías ni errores JSON/HTTP.

El modelo ya llama `ejecutarEscritura()` para INSERT y UPDATE por incrementos
anteriores de EPIC-008, pero conserva SQL interpolado y el endpoint consume los
arrays resultantes como booleanos genéricos. Esa migración parcial no satisface
el contrato integral.

## 7. Matriz institucional objetivo

| Actor efectivo | CREATE | READ | UPDATE | DELETE |
| --- | --- | --- | --- | --- |
| Profesor con acceso vigente a su perfil | Sólo propio | Sólo propios | Sólo propio | Sólo propio |
| Estudiante con acceso vigente a su perfil | Sólo propio | Sólo propios | Sólo propio | Sólo propio |
| Admin | Global sobre Profesores y Estudiantes | Global sobre Profesores y Estudiantes | Global sobre Profesores y Estudiantes | Global sobre Profesores y Estudiantes |
| Comité | Global sobre Profesores y Estudiantes | Global sobre Profesores y Estudiantes | Global sobre Profesores y Estudiantes | Global sobre Profesores y Estudiantes |
| Profesor/Estudiante que acumula Admin o Comité | Facultades globales del rol acumulado | Facultades globales del rol acumulado | Facultades globales del rol acumulado | Facultades globales del rol acumulado |
| Actor sin acceso vigente y sin Admin/Comité | Rechazado | Rechazado | Rechazado | Rechazado |
| Anónimo | Rechazado | Rechazado | Rechazado | Rechazado |

Un Profesor sin Admin/Comité no puede consultar ni modificar Postdoctorados de
Estudiantes, de otros Profesores ni de cualquier otro usuario. Un Estudiante no
puede consultar ni modificar Postdoctorados ajenos. La visibilidad frontend no
sustituye la matriz backend.

## 8. Identidad objetivo y autorización

### 8.1 Regla XOR obligatoria

Todo usuario objetivo, tanto propio como global, debe existir y poseer
exactamente una especialización:

```text
usuario válido
→ Profesor XOR Estudiante
```

Se rechaza:

- usuario simultáneamente Profesor y Estudiante;
- usuario sin ninguna de esas especializaciones;
- usuario inexistente.

La futura Task debe reutilizar la resolución vigente demostrada por Grado, sin
modificar `ajax/login.php`, `src/Model/Login.php` ni
`src/Security/Authorization.php`.

### 8.2 Precedencia de actores

La autoridad se resuelve en backend con esta precedencia:

1. si la identidad autenticada acumula `admin` o `comite`, aplica la facultad
   global correspondiente;
2. de lo contrario, se evalúa el acceso vigente al perfil propio;
3. para operación propia, el backend obtiene un único `id_usuario` positivo
   desde la sesión y comprueba la especialización XOR;
4. cualquier otro caso se rechaza antes de leer o escribir Postdoctorado.

### 8.3 Estudiante propietario

El contrato estudiantil utiliza `Authorization::hasCapability('perfil.ver')`,
sesión autenticada, identidad Estudiante coherente y un único `id_usuario`
positivo resuelto por backend.

El permiso histórico 5 no se consulta directamente como autoridad. Sólo puede
participar indirectamente mientras sea el productor transitorio vigente de
`perfil.ver`.

### 8.4 Profesor propietario

El contrato aprobado de Profesor exige sesión autenticada, identidad Docente
coherente con el login, acceso vigente a su perfil y un único `id_usuario`
positivo resuelto por backend.

El permiso histórico 3 o la clave `aceptado` aislada no autorizan el CRUD de
Postdoctorado.

### 8.5 Admin y Comité

Se reutiliza el rol acumulativo vigente `admin` o `comite`. La facultad global
no autoriza objetivos arbitrarios fuera del dominio: el usuario objetivo debe
existir y satisfacer Profesor XOR Estudiante.

### 8.6 Autoridades prohibidas

No son autoridad suficiente:

- `usuario` o `id_postdoc` enviados por el cliente;
- permiso 3 o `aceptado`;
- permiso 5 consultado directamente;
- acceso previo a una página protegida;
- botones visibles u ocultos;
- un valor obtenido desde atributos DOM;
- que un ID pueda convertirse a entero;
- que un statement o array sea truthy.

## 9. Ownership backend

La única autoridad persistente de ownership es:

```text
postdoctorado.usuario
```

### 9.1 Operaciones propias

- CREATE deriva el propietario desde la sesión. El `usuario` cliente debe ser
  rechazado o ignorado inequívocamente como autoridad.
- READ lista filtra por el `id_usuario` de sesión.
- READ detalle combina `id_postdoc` con el propietario de sesión o comprueba la
  fila y su ownership antes de responder.
- UPDATE resuelve `id_postdoc`, comprueba propietario, mantiene el propietario
  persistido y no permite reasignación por request.
- DELETE resuelve `id_postdoc`, comprueba propietario y elimina únicamente la
  fila propia.

La sentencia persistente de UPDATE y DELETE propios debe quedar acotada por ID
y propietario, además de la guard del endpoint, por ejemplo:

```text
WHERE id_postdoc = :id_postdoc AND usuario = :usuario
```

Una consulta previa por sí sola no protege frente a pérdida de ownership o
cambios concurrentes.

### 9.2 Operaciones globales

Admin y Comité pueden indicar un usuario objetivo para CREATE y READ lista. El
backend valida existencia y especialización XOR antes de operar.

Para READ detalle, UPDATE y DELETE global, la fila se resuelve por
`id_postdoc`; su propietario real se obtiene desde persistencia y también debe
ser un objetivo Profesor XOR Estudiante válido. Si el request incluye
`usuario`, sólo puede actuar como comprobación de consistencia y debe coincidir
con la fila.

UPDATE nunca reasigna `postdoctorado.usuario`. El cambio de propietario no forma
parte del contrato aprobado.

## 10. Contrato CRUD objetivo

### 10.1 CREATE

Debe:

1. exigir autenticación y resolver actor antes de escribir;
2. resolver propietario desde sesión para Profesor/Estudiante, o validar el
   objetivo explícito para Admin/Comité;
3. validar el usuario objetivo, Institución, campos funcionales y fechas;
4. ejecutar INSERT parametrizado mediante `ejecutarEscritura(...)`;
5. interpretar explícitamente `filasAfectadas` e `idInsertado`;
6. exigir exactamente una fila afectada y un identificador insertado válido;
7. responder `ok=true` sólo después de confirmar la inserción.

El Postdoctorado creado queda vigente inmediatamente. No existe revisión ni
aprobación administrativa posterior.

### 10.2 READ

Debe preservar dos contratos:

- lista de cero, uno o múltiples Postdoctorados del usuario autorizado;
- detalle de un Postdoctorado para edición.

Profesor/Estudiante sólo puede listar o cargar detalle propio. Admin/Comité
puede operar sobre cualquier objetivo válido. Las consultas deben estar
parametrizadas, seleccionar campos compatibles con la Ficha y devolver arrays
asociativos.

Una lista vacía es un éxito válido con `datos: []`. Un ID inexistente no es una
lista vacía. Un ID ajeno se rechaza sin revelar el contenido del registro.

### 10.3 UPDATE

Debe:

1. exigir autenticación, actor y acceso aplicable;
2. validar `id_postdoc` positivo y resolver su existencia;
3. comprobar ownership propio o facultad global;
4. validar Institución, campos funcionales y ambas fechas;
5. mantener el propietario persistido;
6. ejecutar UPDATE parametrizado mediante `ejecutarEscritura(...)`;
7. interpretar `filasAfectadas` y distinguir sin cambios, inexistencia,
   conflicto y error técnico.

Si el payload válido coincide con la fila persistida, la respuesta debe ser
honesta, por ejemplo `ok=true`, `codigo=POSTDOCTORADO_SIN_CAMBIOS` y
`cambios=false`. No debe afirmar que hubo una modificación. Cero filas por
inexistencia, pérdida de ownership o conflicto no equivale a éxito.

Si la fila histórica con fechas invertidas es editada expresamente, el estado
final completo debe cumplir las reglas temporales vigentes antes de persistir.
No se permite editar sólo otro campo y conservar el intervalo inválido.

### 10.4 DELETE

Debe:

1. mostrar confirmación explícita en frontend antes de enviar la solicitud;
2. no enviar la solicitud si el usuario cancela;
3. exigir autenticación, autorización, ownership aplicable y CSRF;
4. validar `id_postdoc` y existencia;
5. ejecutar DELETE físico, parametrizado y acotado a la fila autorizada;
6. utilizar `ejecutarEscritura(...)`, nunca `ejecutarConsulta()`;
7. exigir exactamente una fila afectada;
8. informar éxito y refrescar la Ficha sólo después de confirmarlo.

Un segundo DELETE sobre el mismo ID no puede informar un nuevo éxito. DELETE de
Postdoctorado no elimina Institución, Usuario, Estudiante, Profesor ni otros
antecedentes.

## 11. Validación de datos

La validación frontend mejora la experiencia, pero no es autoridad. El endpoint
y/o modelo deben repetir toda validación necesaria antes de persistir.

| Campo | Contrato mínimo |
| --- | --- |
| `id_postdoc` | Entero positivo para detalle, UPDATE y DELETE; existencia comprobada |
| `usuario` | Derivado de sesión para operación propia; para Admin/Comité, entero positivo, existente y Profesor XOR Estudiante |
| `inst` / `inst_postdoc` | Entero positivo y fila existente en `institucion` |
| `prof` | Dato textual requerido por el contrato actual; debe llegar como string válido y no utilizarse nunca como identidad u ownership |
| `fech_in` / `fecha_inicio` | Obligatoria, formato exacto `YYYY-MM-DD` y fecha civil real |
| `fech_ter` / `fecha_termino` | Obligatoria, formato exacto `YYYY-MM-DD` y fecha civil real |
| Intervalo | `fecha_inicio <= fecha_termino`; se acepta igualdad |

Para ambas fechas se rechaza:

- ausencia o cadena vacía;
- texto fuera del formato exacto;
- fecha imposible;
- payload SQL;
- inicio posterior a término.

La validación estricta debe comprobar que el parseo y la serialización resultan
exactamente en el mismo valor recibido; una normalización silenciosa de fechas
imposibles no es válida.

Los campos textuales no deben interpolarse en SQL. La futura Task preservará el
significado actual de `prof` y sólo aplicará la validación mínima compatible con
el schema vigente; no redefine el modelo funcional de Profesor/a patrocinante.

## 12. Registro histórico con fecha invertida

La inspección aprobada detectó una fila persistente preexistente con:

```text
fecha_inicio > fecha_termino
```

Se registra como **DEUDA / DATO HISTÓRICO PREEXISTENTE**.

La futura Task:

- no corrige automáticamente esa fila;
- no ejecuta una migración de datos;
- no la modifica al listar o consultar;
- no bloquea el READ autorizado por esta anomalía;
- no realiza escrituras oportunistas sobre ella;
- exige un intervalo válido completo si el usuario decide editarla.

La regla temporal se aplica hacia adelante a todo CREATE y UPDATE. Preservar el
dato histórico no constituye una excepción para nuevas escrituras.

## 13. Respuestas HTTP/JSON

`ajax/postdoctorado.php` debe convertirse en la autoridad operacional del CRUD
y emitir siempre JSON estructurado con `Content-Type` apropiado.

Forma mínima de éxito:

```json
{
  "ok": true,
  "datos": {},
  "mensaje": "Operación completada correctamente."
}
```

Forma mínima de error:

```json
{
  "ok": false,
  "error": "CODIGO_ESTABLE",
  "mensaje": "Descripción segura para el usuario."
}
```

Códigos HTTP objetivo:

| Condición | Código orientativo |
| --- | --- |
| operación, ID o payload mal formado | 400 |
| no autenticado | 401 |
| no autorizado, ajeno o CSRF inválido | 403 |
| recurso u objetivo válido pero inexistente | 404 |
| conflicto, integridad o escritura no confirmada | 409 |
| campo o fecha semánticamente inválida | 422 |
| error interno o persistencia no clasificable | 500 |

El endpoint no expone SQL, parámetros internos, trazas, credenciales ni mensajes
de PDO. El frontend debe usar el status HTTP y `ok`, no inferir éxito desde la
mera llegada al callback ni desde un string.

Para reducir IDOR observable, la futura Task puede unificar de forma coherente
la respuesta a recursos ajenos e inexistentes para actores propios. Esa elección
no puede revelar contenido ni alterar la matriz. Admin/Comité sí pueden recibir
un no encontrado después de superar la guard global.

## 14. CSRF

CSRF es obligatorio en:

- CREATE;
- UPDATE;
- DELETE.

READ lista y READ detalle no requieren CSRF, pero sí autenticación,
autorización y ownership.

Se reutilizará el patrón local ya consolidado en Grado si continúa compatible:
token generado en sesión activa, transporte desde el formulario y comparación
segura en el endpoint. El token ausente, mal formado o inválido produce rechazo
antes de cualquier escritura.

No se introduce middleware, helper global, arquitectura transversal ni cambio a
Login/Authorization para resolver CSRF de Postdoctorado.

## 15. Persistencia y schema

El schema vigente se conserva íntegramente:

- tabla `postdoctorado`;
- PK `id_postdoc`;
- ownership `postdoctorado.usuario`;
- FK a Usuario e Institución;
- columnas y nulabilidad actuales;
- índices actuales;
- reglas CASCADE/RESTRICT vigentes;
- charset vigente.

No se crea migración ni se modifica la base de datos. No se añade una columna
de aprobación, borrado lógico o estado.

Contrato por operación:

- INSERT utiliza `ejecutarEscritura()` y comprueba `filasAfectadas` e
  `idInsertado`;
- UPDATE utiliza `ejecutarEscritura()` y comprueba `filasAfectadas` sin tratar
  el array como booleano;
- DELETE migra desde `ejecutarConsulta()` a `ejecutarEscritura()` y exige una
  fila afectada;
- READ utiliza consultas parametrizadas y resultados asociativos.

Llamar `prepare()` sobre una cadena que ya contiene input interpolado no es
parametrizar. Todo input externo debe utilizar placeholders y parámetros
separados.

El modelo futuro debe revisar exactamente:

- `insertar()`;
- `mostrar()`;
- `mostrarPostdoc()`;
- `editarPostdoc()`;
- `eliminar()`;
- helpers estrictamente internos que sean necesarios dentro del mismo archivo.

No se introduce un repositorio nuevo, un helper global ni un quinto archivo
funcional.

## 16. Institución y atomicidad multi-request

Institución está cerrada y protegida. Se preservan:

- lista asociativa `id_inst` / `inst`;
- alta contextual vigente para `postdoctorado`;
- FK actual;
- `src/Model/Institucion.php`;
- `ajax/institucion.php`.

La futura Task valida que la Institución finalmente utilizada exista, incluso
si su ID proviene de un alta contextual.

El flujo vigente puede ejecutar en solicitudes separadas:

1. alta contextual de Institución;
2. CREATE o UPDATE posterior de Postdoctorado.

No forman una transacción única. Si la Institución se crea y Postdoctorado
falla, puede quedar un registro de catálogo sin la asociación que motivó su
alta. Se registra como **DEUDA TÉCNICA EXISTENTE / LIMITACIÓN DE ATOMICIDAD
MULTI-REQUEST**.

Esta limitación no bloquea Postdoctorado. La futura Task no fusiona endpoints,
no introduce transacciones distribuidas, no elimina Instituciones como
compensación y no modifica el contrato cerrado de Institución.

## 17. Ausencia de aprobación y política DELETE

Postdoctorado no posee workflow de aprobación administrativa.

No se introducen:

- `pendiente`;
- `aprobado`;
- `rechazado`;
- `validado`;
- `estado_postdoctorado`;
- cola o pantalla administrativa de revisión.

CREATE y UPDATE exitosos quedan vigentes inmediatamente.

DELETE permanece físico y elimina exclusivamente la fila autorizada de
`postdoctorado`. No se introduce soft delete, fecha de eliminación, estado
eliminado, auditoría nueva ni migración.

## 18. Seguridad obligatoria

La futura Task debe corregir dentro de Postdoctorado:

### H1

- CRUD anónimo;
- IDOR en lista y detalle;
- CREATE para usuario arbitrario;
- UPDATE arbitrario;
- DELETE arbitrario;
- SQL injection potencial por interpolación.

### H2

- ausencia de CSRF en escrituras;
- ausencia de autorización backend;
- ausencia de ownership;
- exposición de datos ajenos.

### H3

- fechas vacías, imposibles o invertidas;
- referencias inválidas;
- falsos éxitos;
- errores no normalizados;
- arrays de escritura tratados como booleanos;
- filas afectadas ignoradas.

### H4

- ausencia de confirmación DELETE;
- refresh defectuoso relacionado con Postdoctorado;
- listas vacías y múltiples registros;
- manejo frontend de errores necesario para el CRUD.

Este cierre no declara resuelta la seguridad de Ficha ni de otros objetos. No
autoriza un refactor general.

## 19. Frontend objetivo

`form-doc/scripts/postdoctorado.js` debe adaptarse sin rediseño visual para:

- consumir el contrato HTTP/JSON normalizado;
- incluir CSRF en CREATE, UPDATE y DELETE;
- dejar de utilizar `usuario` cliente como autoridad en contexto propio;
- enviar el objetivo sólo cuando sea necesario para contexto global;
- mostrar mensajes reales según status y `ok`;
- no pintar éxito ni refrescar ante `ok=false` o JSON inválido;
- confirmar DELETE y respetar la cancelación;
- refrescar la sección correcta sólo tras éxito confirmado;
- reemplazar el contenido correspondiente para evitar duplicados;
- aceptar listas vacías;
- preservar y renderizar múltiples Postdoctorados;
- manejar detalle inexistente o no autorizado sin asumir `datos[0]`;
- retirar parseo manual frágil sólo donde interfiera con el nuevo contrato;
- retirar logs diagnósticos de payload/response en las rutas intervenidas.

Las funciones públicas consumidas por Ficha, en especial
`cargarPostdoc(usuario, id)`, deben conservar una interfaz compatible para no
obligar cambios en sus callers protegidos. El valor `usuario` puede seguir
sirviendo como objetivo solicitado en contexto global, pero nunca como
autoridad suficiente.

La visibilidad de Agregar/Editar/Eliminar debe alinearse con el contexto
efectivo para evitar acciones inoperantes. Sigue siendo sólo UX: el endpoint
repite todas las comprobaciones.

## 20. Formulario compartido

`form-doc/agr.form.dat.acad.php` es el único archivo compartido autorizable y
sólo puede modificarse como wiring mínimo para Postdoctorado:

- emitir o transportar su token CSRF;
- indicar contexto propio/global/sin acceso;
- ajustar visibilidad UX de acciones;
- aportar el transporte mínimo requerido por el contrato seguro.

El wiring ya existente para Grado puede servir como patrón, pero Postdoctorado
debe mantener su propio contrato claramente delimitado. No se delega
autorización al HTML ni al JavaScript.

No se autoriza intervenir desde esta Task los archivos de Ficha, info, admin,
footer, helpers globales ni `fechaCivil`.

## 21. Alcance cerrado de la futura Task integral

### 21.1 Archivos modificables definitivos

1. `src/Model/Postdoctorado.php`;
2. `ajax/postdoctorado.php`;
3. `form-doc/scripts/postdoctorado.js`;
4. `form-doc/agr.form.dat.acad.php`.

CREATE, READ, UPDATE y DELETE forman una única Task integral porque comparten
actor, ownership, CSRF, validación, respuestas y refresh.

### 21.2 Archivos y fronteras protegidos

- `ajax/login.php`;
- `src/Model/Login.php`;
- `src/Security/Authorization.php`;
- `src/Model/Institucion.php`;
- `ajax/institucion.php`;
- Ficha Académica como objeto integral;
- callers de Ficha Estudiante, Ficha Docente, Admin y Comité;
- `form-doc/footer.php`;
- helpers globales;
- `fechaCivil`;
- Grado Académico;
- BD/schema;
- `migrations/`;
- `docs/roadmap/ROADMAP.md`;
- ADR existentes.

La futura Task no puede introducir un quinto archivo funcional. Si aparece esa
necesidad, debe detenerse antes del cambio y volver a revisión de alcance.

## 22. Validación funcional futura

La VF será integral y ejecutada exclusivamente por el usuario después de la
implementación y la revisión técnica.

### 22.1 Profesor propietario

- lista cero, uno y múltiples Postdoctorados propios;
- crea un Postdoctorado propio;
- carga y edita uno propio;
- confirma y elimina uno propio;
- cancela DELETE sin emitir la solicitud ni alterar datos;
- no lista ni carga detalle ajeno;
- no actualiza ni elimina un `id_postdoc` ajeno;
- manipular `usuario` no cambia propietario ni amplía acceso;
- sin Admin/Comité no accede a registros de Estudiantes u otros Profesores.

### 22.2 Estudiante propietario

- con `perfil.ver`, lista cero, uno y múltiples registros propios;
- crea, carga, edita y elimina sólo sus Postdoctorados;
- manipular `usuario` se rechaza o ignora sin cambiar autoridad;
- manipular un ID ajeno no revela ni muta la fila.

### 22.3 Admin

- CRUD global de Postdoctorados de Profesores válidos;
- CRUD global de Postdoctorados de Estudiantes válidos;
- objetivo inexistente, ambiguo o sin especialización: rechazo;
- UPDATE no reasigna silenciosamente propietario.

### 22.4 Comité

- misma matriz CRUD global aprobada para Admin;
- conserva facultades globales cuando también posee Docencia;
- objetivos inválidos se rechazan antes de persistencia.

### 22.5 Negativos de identidad y acceso

- anónimo: bloqueado en todas las operaciones;
- permiso 3 aislado: bloqueado;
- permiso 5 sin contrato `perfil.ver` vigente: bloqueado;
- actor autenticado no contemplado: bloqueado;
- usuario ajeno: bloqueado;
- usuario objetivo Profesor + Estudiante: bloqueado;
- usuario objetivo sin especialización: bloqueado;
- usuario inexistente: bloqueado;
- `id_postdoc` inexistente: respuesta inequívoca, sin falso éxito;
- CSRF ausente o inválido: rechazo sin escritura.

### 22.6 Datos y persistencia

- Institución válida: aceptada;
- Institución inexistente: rechazo sin escritura;
- ambas fechas válidas: aceptadas;
- fechas vacías: rechazo;
- fechas imposibles: rechazo;
- formato distinto de `YYYY-MM-DD`: rechazo;
- inicio posterior a término: rechazo;
- inicio igual a término: aceptado;
- payload SQL no altera consultas;
- múltiples Postdoctorados se conservan y muestran;
- lista vacía se representa correctamente;
- UPDATE idéntico informa sin cambios;
- INSERT, UPDATE y DELETE interpretan filas afectadas;
- segundo DELETE no informa éxito;
- la fila histórica invertida se lee sin mutación automática;
- editar esa fila exige que el resultado final tenga intervalo válido.

### 22.7 Consumers y UX

- Ficha Estudiante propia;
- Ficha Docente propia;
- contexto Admin sobre Estudiante y Profesor;
- contexto Comité sobre Estudiante y Profesor;
- confirmación DELETE;
- cancelación DELETE;
- refresh correcto sin duplicados ni falsos mensajes;
- alta contextual vigente de Institución;
- DELETE de Postdoctorado no elimina Institución ni Usuario.

## 23. Condiciones de detención para la futura Task

Detener antes de implementar si:

1. aparece contradicción con la matriz institucional aprobada;
2. se descubre un AT integral equivalente que gobierne Postdoctorado;
3. surge necesidad obligatoria de cambiar schema;
4. el cierre exige reabrir Institución;
5. la identidad propia/global no puede resolverse con los contratos vigentes;
6. aparece otro consumer cuyo contrato cambie materialmente el alcance;
7. aparece necesidad de modificar un quinto archivo funcional;
8. la evidencia técnica no permite sustentar una afirmación necesaria.

Ninguna de estas condiciones apareció durante la creación de este AT.

## 24. Gobierno y siguiente paso

- Postdoctorado es un antecedente individual y una entidad autoridad
  independiente.
- Ficha Académica sólo consume Postdoctorado.
- Aplica la misma matriz institucional que Grado Académico.
- El ownership reside en `postdoctorado.usuario`.
- Usuario objetivo válido significa Profesor XOR Estudiante.
- No existe aprobación administrativa.
- CREATE y UPDATE tienen vigencia inmediata.
- DELETE es físico.
- Las fechas se validan estrictamente hacia adelante.
- La fila histórica invertida se preserva hasta edición expresa.
- Institución permanece cerrada y protegida.
- La atomicidad multi-request queda registrada como deuda existente no
  bloqueante.
- ADR-003 permanece fuera de este objeto y no se reabre.
- No se requiere ADR nuevo, addendum ni migración.
- La implementación integral y su revisión técnica fueron completadas y
  aprobadas.
- La VF general y la validación dirigida posterior fueron aprobadas por el
  usuario.
- El cache-busting de `postdoctorado.js` se incorporó durante VF como corrección
  operativa no arquitectónica mediante el quinto archivo autorizado
  `form-doc/footer.php`.
- No hubo cambios de BD/schema ni migraciones.

El AT y la Task integral quedan cerrados sin alterar las decisiones
arquitectónicas aprobadas.

## 25. Resultado consolidado

```text
Entidad: postdoctorado
PK: id_postdoc
Ownership: postdoctorado.usuario
CRUD actual: CREATE/READ/UPDATE/DELETE existentes y defectuosos
Propietario objetivo: CRUD propio con acceso vigente a Mi Perfil
Admin: CRUD global sobre Postdoctorados de Profesores y Estudiantes
Comité: CRUD global sobre Postdoctorados de Profesores y Estudiantes
Usuario válido: Profesor XOR Estudiante
Aprobación administrativa: no existe
DELETE: físico
Fechas: YYYY-MM-DD, civiles reales, inicio <= término
Fila histórica invertida: preservada; deuda preexistente
Schema/migraciones: sin cambios
Institución: dependencia cerrada; no se reabre
Ficha Académica: consumer, no autoridad
ADR-003: fuera de alcance; no aplica ownership compartido
perfil.ver: dependencia vigente para Estudiante
permiso 3: no autorizado
permiso 5: sólo productor transitorio indirecto de perfil.ver
Archivos funcionales implementados: Postdoctorado.php, postdoctorado.php,
postdoctorado.js, agr.form.dat.acad.php y footer.php
Cache-busting: corrección operativa incorporada y validada
Implementación/revisión: completada / aprobada
VF: general y dirigida aprobadas por el usuario
Task integral: cerrada
```
