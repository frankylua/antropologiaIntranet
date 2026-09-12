# AT-EPIC009-GRADO-INTEGRAL-001 — Grado Académico integral

- **EPIC principal:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Coordinación:** EPIC-003 — Identidad y autorización; EPIC-008 — Persistencia.
- **Objeto:** Grado Académico.
- **Entidad autoridad:** `grado_academico`.
- **Consumer principal:** Ficha Académica de Estudiante y Profesor.
- **Clasificación:** [ARQ] [EPIC-009] [AUTH] [CRUD] [SEC] [PERSIST] [VF] [GOV].
- **Estado:** APROBADO / IMPLEMENTADO / CERRADO.
- **Implementación:** COMPLETADA.
- **Revisión técnica:** APROBADA.
- **VF:** APROBADA POR EL USUARIO.
- **Blockers:** NINGUNO.
- **Task asociada:** `TASK-EPIC009-GRADO-INTEGRAL-001`.
- **Fecha:** 2026-08-26.
- **Tipo de ejecución:** análisis estático y consolidación documental; no se ejecutaron operaciones HTTP, escrituras, VF ni cambios de datos.

## 1. Propósito y dictamen ejecutivo

Este AT consolida la inspección integral aprobada de Grado Académico y las
decisiones institucionales posteriores. Define la frontera funcional, matriz de
actores, ownership, autorización backend, seguridad, persistencia, integración
con Ficha Académica y alcance de una futura Task integral.

Los cuatro contratos CRUD existen, pero los cuatro son defectuosos. El objeto
puede cerrarse sin migración inicial de schema y sin reabrir Institución o
Título. La implementación futura debe corregir conjuntamente autorización,
ownership, IDOR, acceso anónimo, CSRF, SQL interpolado, validaciones, filas
afectadas y respuestas falsas de éxito.

No existe workflow de aprobación administrativa. CREATE y UPDATE quedan
vigentes inmediatamente. DELETE conserva el borrado físico de la fila
`grado_academico`.

**Dictamen: A. AT integral creado y listo para revisión.**

## 2. Estado Git y alcance de esta ejecución

Baseline inspeccionado:

```text
rama: refactor/fase-0-seguridad
HEAD: 2422b5ba331d23075d7436872afd8771d682146a
```

Al iniciar la inspección existía un archivo no versionado ajeno a este AT:

```text
files/prog_curso/curso_45afc744db0fb82f1db6f16d31ded67a.pdf
```

Ese archivo no fue leído, modificado, añadido a staging ni incorporado a este
alcance. Esta ejecución crea exclusivamente el presente documento. No modifica
código, base de datos, migraciones, Roadmap, ADR, addendum ni Tasks.

## 3. Fuentes y evidencia inspeccionada

Fuente institucional directa:

- resultado aprobado de la Inspección integral Grado Académico, incorporado en
  la solicitud de creación de este AT;
- decisiones institucionales aprobadas sobre actores, ownership, ausencia de
  aprobación, DELETE físico, dependencias y VF.

Evidencia técnica principal:

- `src/Model/Grado.php`;
- `ajax/grado.php`;
- `form-doc/scripts/grado.js`;
- `form-doc/agr.form.dat.acad.php`;
- `form-doc/ficha.estudiante.php` y `form-doc/ficha.docente.php`;
- `form-doc/info.estudiante.php` y `form-doc/info.docente.php`;
- scripts de Ficha de Estudiante, Profesor y vistas administrativas;
- `src/Security/Authorization.php`;
- `ajax/login.php` y `src/Model/Login.php`;
- `src/Config/conexion.php`;
- contratos vigentes de `ajax/institucion.php`, `ajax/titulo.php`,
  `src/Model/Institucion.php` y `src/Model/Titulo.php`;
- documentación vigente de EPIC-003, EPIC-008 y EPIC-009;
- migración cerrada `TASK-DB-MIGRATION-DELETE-RESTRICT-001`;
- historia Git de Grado, Institución, Título y los contratos relacionados.

No se encontró un AT integral equivalente de Grado Académico. Tampoco apareció
una fuente institucional contradictoria, un cambio obligatorio de schema, un
consumer adicional que altere materialmente el alcance ni una necesidad de
reabrir Institución o Título.

## 4. Frontera del objeto

Grado Académico es un objeto autoridad independiente. Su persistencia y regla
de propiedad pertenecen a:

```text
grado_academico
└── usuario → usuario.id_usuario
```

Campos funcionales actuales:

- `id_grado`: identidad persistente del Grado;
- `usuario`: propietario;
- `inst_grado`: referencia a Institución;
- `tit_grado`: referencia a Título académico;
- `fech_graduacion`: fecha civil de graduación.

Ficha Académica no es autoridad sobre estos datos: sólo consume y presenta los
Grados asociados a la persona objetivo. El cierre de Grado desbloquea
parcialmente la robustez de Ficha Académica, pero no cierra la Ficha ni los
demás objetos que la alimentan.

Institución y Título son objetos ya cerrados. Grado debe consumir sus contratos
vigentes; no puede redefinir sus modelos, endpoints, políticas referenciales ni
autorizaciones.

## 5. Consumers confirmados

| Consumer | Uso de Grado | Contrato que debe preservarse |
| --- | --- | --- |
| Ficha Estudiante propia | Listar y operar varios Grados del Estudiante autenticado | Propietario derivado de sesión y `perfil.ver` vigente |
| Ficha Profesor propia | Listar y operar varios Grados del Profesor autenticado | Identidad Docente y acceso vigente a Mi Perfil |
| Ficha Estudiante administrativa | Consultar y operar Grados del Estudiante seleccionado | Rol Admin o Comité y objetivo backend válido |
| Ficha Profesor administrativa | Consultar y operar Grados del Profesor seleccionado | Rol Admin o Comité y objetivo backend válido |
| Alta contextual de Institución | Crear una Institución durante edición de Grado | Contrato contextual vigente de Institución |
| Alta contextual de Título | Crear un Título durante edición de Grado | Contrato contextual vigente de Título y, cuando aplica, Grado propio |

Los scripts `form-doc/scripts/info.estudiante.js`,
`form-doc/scripts/info.docente.js`, `form-doc/scripts/ficha.estudiante.js`,
`form-doc/scripts/ficha.docente.js`, `admin/scripts/ver.estudiante.js` y
`admin/scripts/ver.docente.js` llaman a `cargarGrado(...)`. No se identificó
otro endpoint activo de Grado ni otro consumer que cambie la frontera.

La futura Task no debe modernizar integralmente Ficha Académica. El único
archivo adicional autorizado para conectar el contrato seguro del proveedor
Grado con sus consumers es `form-doc/agr.form.dat.acad.php`, dentro de los
límites definidos en la sección 16.

## 6. Estado CRUD actual

| Operación | Estado EPIC-009 | Flujo actual | Defecto principal |
| --- | --- | --- | --- |
| CREATE | Existe / defectuoso | `op=insert-update`, `id_grado=0` → `Grado::insertar()` | Acepta `usuario` cliente, no autoriza, no valida referencias/fecha, interpola SQL y puede informar éxito falso |
| READ lista | Existe / defectuoso | `op=read` → `Grado::mostrar($usuario)` | Acepta propietario cliente, permite consulta anónima y expone Grados ajenos por IDOR |
| READ detalle | Existe / defectuoso | `op=read_grado_id` → `Grado::mostrarGrado($id_grado)` | Consulta por ID sin autorización ni ownership |
| UPDATE | Existe / defectuoso | `op=insert-update`, `id_grado>0` → `Grado::editar()` | Confía en `id_grado`, no comprueba propietario, no valida datos, interpola SQL y no distingue cero filas |
| DELETE | Existe / defectuoso | `op=delete` → `Grado::eliminar()` | Confía en `id_grado`, no autoriza, no exige ownership/CSRF, interpola SQL, usa `ejecutarConsulta()` y puede informar éxito con cero filas |

El cast parcial de IDs realizado en el endpoint no constituye validación de
existencia, autorización ni ownership. La protección de una página tampoco
protege `ajax/grado.php`: hoy el endpoint puede invocarse directamente y no
contiene guard por operación.

## 7. Matriz institucional objetivo

| Actor efectivo | CREATE | READ | UPDATE | DELETE |
| --- | --- | --- | --- | --- |
| Profesor con acceso vigente a su perfil | Sólo propio | Sólo propios | Sólo propio | Sólo propio |
| Estudiante con acceso vigente a su perfil | Sólo propio | Sólo propios | Sólo propio | Sólo propio |
| Admin | Global sobre Estudiantes y Profesores | Global sobre Estudiantes y Profesores | Global sobre Estudiantes y Profesores | Global sobre Estudiantes y Profesores |
| Comité | Global sobre Estudiantes y Profesores | Global sobre Estudiantes y Profesores | Global sobre Estudiantes y Profesores | Global sobre Estudiantes y Profesores |
| Profesor que acumula Admin o Comité | Facultades globales del rol acumulado | Facultades globales del rol acumulado | Facultades globales del rol acumulado | Facultades globales del rol acumulado |
| Profesor/Estudiante sin acceso vigente a su perfil y sin Admin/Comité | Rechazado | Rechazado | Rechazado | Rechazado |
| Actor autenticado sin regla anterior | Rechazado | Rechazado | Rechazado | Rechazado |
| Anónimo | Rechazado | Rechazado | Rechazado | Rechazado |

No existe acceso de Profesor a Grados de estudiantes u otros profesores por el
solo hecho de ser Profesor. No existe acceso de Estudiante a Grados ajenos.
Ocultar botones o evitar navegación no cambia esta matriz.

## 8. Contrato mínimo de identidad y autorización

La autoridad se compone obligatoriamente de:

```text
identidad autenticada
+ acceso vigente al perfil, cuando actúa como propietario
+ ownership validado en backend
```

La precedencia es:

1. si la identidad autenticada acumula `admin` o `comite`, aplica la facultad
   global correspondiente;
2. en ausencia de esos roles, se evalúa el acceso vigente al perfil propio;
3. para operaciones propias, el backend obtiene `id_usuario` de la sesión
   validada y aplica ownership persistente;
4. cualquier otro caso se rechaza antes de consultar o escribir.

### 8.1 Admin y Comité

Se reutiliza `Authorization::hasAny(['admin', 'comite'])`. Estas claves son la
representación vigente de roles acumulativos producidos durante login. No se
crea una capability nueva de escritura.

El rol global no permite operar Grados de una identidad arbitraria fuera del
dominio aprobado. El backend debe verificar que el `usuario` objetivo exista y
posea especialización Estudiante o Profesor. Un objetivo inexistente, ambiguo o
ajeno a ambas especializaciones no es válido.

### 8.2 Estudiante propietario

El acceso vigente al perfil Estudiante se expresa mediante
`Authorization::hasCapability('perfil.ver')`, junto con sesión autenticada,
identidad Estudiante coherente y un único `id_usuario` positivo resuelto por el
backend.

`perfil.ver` continúa siendo el contrato público vigente aunque su productor
sea transitoriamente el permiso histórico 5. Grado no consulta ni interpreta el
permiso 5 directamente y no modifica su productor.

### 8.3 Profesor propietario

El contrato institucional vigente de Profesor permite Mi Perfil a Profesor
Pendiente, Aceptado o Rechazado. Por ello, operar Grados propios requiere sesión
autenticada, identidad `docente` coherente con `login`, contexto de Profesor
válido y un único `id_usuario` positivo resuelto por backend.

No se exige `docente.habilitado`, porque esa capability representa habilitación
académica plena y el contrato ya aprobado de Mi Perfil no depende del estado
Profesor. Tampoco se utiliza `aceptado` ni el permiso histórico 3 como autoridad.

### 8.4 Autoridades prohibidas

No son autoridad suficiente:

- `usuario` o `id_grado` enviados por el cliente;
- permiso 3 o la clave `aceptado`;
- permiso 5 consultado directamente;
- botones visibles u ocultos;
- acceso previo a una página protegida;
- que un ID tenga forma numérica;
- que una consulta o statement sea truthy.

No se requiere modificar `src/Security/Authorization.php` ni `ajax/login.php`
para expresar esta matriz. Si durante la revisión técnica se comprobara que el
contexto Docente o `perfil.ver` dejó de ser suficiente para representar acceso
vigente a Mi Perfil, deberá detenerse la Task antes de implementar y volver a
EPIC-003; este bloqueo no está presente en el baseline inspeccionado.

## 9. Ownership backend

El campo autoridad es exclusivamente:

```text
grado_academico.usuario
```

### 9.1 Operaciones propias

- CREATE ignora o rechaza cualquier `usuario` recibido como autoridad y utiliza
  el `id_usuario` único de la sesión.
- READ lista filtra por el `id_usuario` de sesión.
- READ detalle combina `id_grado` con el `id_usuario` de sesión.
- UPDATE combina `id_grado` con el `id_usuario` de sesión en validación y
  escritura.
- DELETE combina `id_grado` con el `id_usuario` de sesión en validación y
  escritura.

El patrón objetivo para UPDATE/DELETE propio debe conservar la protección en la
sentencia persistente, por ejemplo mediante `WHERE id_grado = :id_grado AND
usuario = :usuario`, además de la guard del endpoint. La consulta previa no
reemplaza esa condición.

La manipulación de `usuario` debe ser rechazada o ignorada de forma inequívoca,
sin permitir selección de propietario. La manipulación de un `id_grado` ajeno
debe finalizar sin lectura ni mutación del registro ajeno.

### 9.2 Operaciones globales

Admin y Comité pueden indicar un `usuario` objetivo para CREATE y para listar
sus Grados. El backend debe validar que el usuario exista y pertenezca a
Estudiante o Profesor.

En UPDATE y DELETE, la autoridad global permite resolver la fila por
`id_grado`; el propietario real se obtiene desde la fila persistida. Si el
cliente también envía `usuario`, sólo puede utilizarse como comprobación de
consistencia y debe coincidir. UPDATE no reasigna silenciosamente
`grado_academico.usuario`: cambiar de propietario no forma parte del contrato de
edición de Grado aprobado.

## 10. Contrato CRUD objetivo

### 10.1 CREATE

Debe:

1. exigir autenticación y resolver actor antes de escribir;
2. resolver propietario desde sesión para Profesor/Estudiante, o validar el
   objetivo indicado para Admin/Comité;
3. validar Institución, Título y fecha;
4. ejecutar INSERT parametrizado mediante `ejecutarEscritura(...)`;
5. exigir exactamente una fila afectada y un identificador insertado válido si
   el response lo necesita;
6. responder `ok=true` sólo cuando la fila esté confirmada.

El nuevo Grado queda vigente inmediatamente. No se crea estado de aprobación.
No se agregan a Grado los estados `pendiente`, `aprobado` o `rechazado`, ni una
etapa de revisión administrativa. Los estados homónimos ya existentes del
objeto Profesor sólo determinan su ciclo de vida propio y no constituyen un
workflow de Grado.

### 10.2 READ

Debe conservar dos necesidades:

- lista de múltiples Grados de la persona autorizada;
- detalle de un Grado para edición.

Profesor/Estudiante sólo puede listar o cargar detalle propio. Admin/Comité
puede hacerlo sobre cualquier Estudiante o Profesor válido. Todas las consultas
deben ser parametrizadas, seleccionar columnas explícitas y devolver arreglos
asociativos compatibles con la Ficha.

READ requiere autorización backend aunque sea una operación sin escritura. Un
ID ajeno no puede utilizarse para descubrir datos mediante
`read_grado_id`. Un recurso inexistente debe distinguirse de una lista propia
legítimamente vacía.

### 10.3 UPDATE

Debe:

1. exigir autenticación, actor y acceso aplicable;
2. validar `id_grado` positivo y existencia;
3. validar ownership para el propietario o autoridad global para Admin/Comité;
4. validar Institución, Título y fecha;
5. mantener el propietario persistido;
6. ejecutar UPDATE parametrizado mediante `ejecutarEscritura(...)`;
7. interpretar `filasAfectadas` explícitamente.

Si los valores válidos son idénticos a los persistidos, la respuesta debe ser
honesta, por ejemplo `ok=true`, `codigo=SIN_CAMBIOS` y `cambios=false`; no debe
afirmar que hubo modificación. Cero filas por inexistencia, pérdida de
ownership o conflicto no puede presentarse como éxito.

La modificación queda vigente inmediatamente. No se solicita revisión
administrativa.

### 10.4 DELETE

Debe:

1. mostrar confirmación explícita en frontend antes de solicitar el borrado;
2. exigir autenticación, autorización, ownership aplicable y CSRF;
3. validar `id_grado` y existencia;
4. ejecutar DELETE físico, parametrizado y acotado a la fila autorizada;
5. usar el contrato explícito `ejecutarEscritura(...)`, nunca
   `ejecutarConsulta()`;
6. exigir exactamente una fila afectada;
7. informar éxito sólo después de confirmar la eliminación;
8. refrescar la Ficha únicamente ante `ok=true`.

No se introduce borrado lógico, columna de estado, auditoría de aprobación ni
migración de schema. Un segundo DELETE sobre el mismo ID no puede informar un
nuevo éxito.

## 11. Validación de datos

La validación frontend sólo mejora UX; el endpoint y/o modelo deben repetir la
validación autoritativa.

| Campo | Contrato mínimo |
| --- | --- |
| `id_grado` | Entero positivo para detalle, UPDATE y DELETE; existencia comprobada |
| `usuario` | Derivado de sesión para propietario; para Admin/Comité, entero positivo, existente y Estudiante o Profesor |
| `inst_grado` | Entero positivo y fila existente en `institucion` |
| `tit_grado` | Entero positivo y fila existente en `titulo_grado` |
| `fech_graduacion` | String exacto `YYYY-MM-DD` y fecha civil real válida |

Este AT no introduce una regla no aprobada sobre fecha futura, antigüedad o
relación cronológica con otros hitos. La Task sólo debe asegurar formato,
validez calendárica y compatibilidad con la columna vigente.

La clasificación del Título proviene del contrato de `titulo_grado`; el backend
no debe confiar en etiquetas o categorías construidas por JavaScript como
identidad persistente.

## 12. Seguridad obligatoria

### 12.1 Acceso anónimo y directo

Todas las operaciones de `ajax/grado.php`, incluido READ, deben exigir una
identidad autorizada. La guard se ejecuta antes de consultar el modelo. Acceder
directamente al endpoint reproduce exactamente las mismas reglas que la UI.

### 12.2 IDOR

READ detalle, UPDATE y DELETE deben vincular `id_grado` con el actor autorizado.
READ lista debe derivar el usuario para propietarios. Ningún identificador del
cliente sustituye esa vinculación.

### 12.3 CSRF

CREATE, UPDATE y DELETE deben exigir un token CSRF de Grado ligado a la sesión,
generado con entropía criptográfica y comparado mediante `hash_equals`. El token
puede viajar en header AJAX o campo explícito; su ausencia o invalidez debe
responder rechazo antes de la escritura.

READ no requiere CSRF, pero sí autenticación y autorización. La emisión y el uso
del token en la interfaz quedan acotados exclusivamente a
`form-doc/agr.form.dat.acad.php` y `form-doc/scripts/grado.js`; no se autoriza
modificar implícitamente otra vista, formulario ni el diseño visual.

### 12.4 SQL y persistencia

Toda consulta de Grado debe utilizar placeholders y parámetros. Los casts del
endpoint no justifican interpolación. CREATE, UPDATE y DELETE consumen
`ejecutarEscritura(...)` y evalúan `filasAfectadas`; READ utiliza consultas
preparadas con resultados asociativos.

### 12.5 Errores y respuestas

El endpoint debe fijar `Content-Type: application/json; charset=utf-8` y devolver
objetos, nunca strings ambiguos. Contrato mínimo:

```json
{
  "ok": true,
  "codigo": "GRADO_CREADO",
  "mensaje": "Grado académico creado correctamente.",
  "datos": { "id_grado": 123 }
}
```

Errores mínimos:

| HTTP | Caso |
| --- | --- |
| 400 | Operación, payload o ID mal formado |
| 401 | Sesión no autenticada |
| 403 | Actor sin facultad, sin acceso vigente al perfil, ownership ajeno o CSRF inválido/ausente |
| 404 | Grado, usuario objetivo, Institución o Título inexistente, cuando su revelación sea autorizada |
| 422 | Fecha u otro valor con formato/semántica inválida |
| 409 | Resultado persistente incoherente o conflicto verificable |
| 500 | Fallo técnico controlado, sin falso éxito ni detalle sensible |

Para evitar enumeración, un propietario que solicita un `id_grado` ajeno puede
recibir un rechazo uniforme sin revelar si el ID existe. Admin/Comité sí pueden
recibir `NO_ENCONTRADO` después de superar su guard global.

## 13. Persistencia y schema

El schema actual de `grado_academico` es suficiente para el cierre inicial:

- `usuario` ya expresa ownership;
- las referencias a Institución y Título ya existen;
- la fecha ya tiene persistencia;
- el borrado físico ya corresponde a la política aprobada;
- no existe ni se requiere estado de aprobación.

Por tanto:

- no se crea migración;
- no se modifica BD/schema;
- no se añade estado lógico;
- no se añade tabla de workflow;
- no se reinterpreta ninguna FK.

La migración versionada cerrada mantiene
`grado_academico.tit_grado → titulo_grado.id_titulo` con `ON DELETE RESTRICT`.
Grado consume esa política y no la cambia.

La política FK vigente de Institución se conserva como dependencia conocida.
Según su contrato cerrado, Institución mantiene su resolución referencial
vigente. Cualquier cambio desde o hacia CASCADE/RESTRICT pertenece al objeto
Institución y queda fuera de este AT.

DELETE de Grado elimina solamente la fila `grado_academico` seleccionada. No
elimina Institución, Título, Usuario, Estudiante, Profesor ni otros antecedentes
de Ficha.

## 14. Dependencias cerradas

### 14.1 Institución

Se preservan `ajax/institucion.php` y `src/Model/Institucion.php`. Grado puede
seguir consumiendo lectura y alta contextual ya autorizada. No modifica su CRUD,
modelo, endpoint ni política FK vigente.

La futura Task debe validar que el `inst_grado` finalmente utilizado exista,
incluso cuando provenga de un alta contextual.

### 14.2 Título

Se preservan `ajax/titulo.php` y `src/Model/Titulo.php`. Grado puede seguir
consumiendo lectura y alta contextual ya autorizada. Título mantiene RESTRICT
cuando existen Grados asociados.

La futura Task debe validar que el `tit_grado` finalmente utilizado exista,
incluso cuando provenga de un alta contextual.

### 14.3 Deuda técnica existente: atomicidad multi-request

El flujo frontend actual puede ejecutar mediante solicitudes separadas:

1. el alta contextual de Institución;
2. el alta contextual de Título;
3. el alta o edición posterior de Grado.

Estas solicitudes no constituyen una transacción única. Si Institución o Título
se crea correctamente y la operación posterior de Grado falla, puede quedar un
registro de catálogo creado sin asociación al Grado que motivó el flujo.

Esta situación se clasifica como **DEUDA TÉCNICA EXISTENTE / LIMITACIÓN DE
ATOMICIDAD MULTI-REQUEST**. No bloquea el cierre integral de Grado porque su
resolución exigiría reabrir objetos ya cerrados y rediseñar la coordinación
entre endpoints.

La futura Task de Grado:

- no modifica Institución;
- no modifica Título;
- no introduce transacciones distribuidas;
- no fusiona endpoints;
- no elimina automáticamente registros de catálogo si Grado falla después;
- sí garantiza que cada operación propia de Grado sea consistente,
  parametrizada y produzca una respuesta honesta.

### 14.4 Ficha Académica

Ficha Estudiante y Ficha Profesor deben seguir mostrando múltiples Grados y
refrescando su sección después de mutaciones exitosas. La Task de Grado no puede
cerrar ni refactorizar integralmente Ficha Académica.

### 14.5 `perfil.ver`

Es dependencia explícita para el propietario Estudiante. Se consume mediante
`Authorization::hasCapability('perfil.ver')`; no se modifica su productor ni se
convierte en una capability general de escritura.

### 14.6 Permisos históricos 3 y 5

- permiso 3 y `aceptado` no autorizan Grado;
- permiso 5 no se consulta directamente;
- permiso 5 sólo participa indirectamente mientras sea productor transitorio de
  `perfil.ver` conforme al contrato EPIC-003 vigente;
- una futura sustitución del productor de `perfil.ver` no debe exigir cambios en
  el contrato público de Grado.

## 15. Frontend objetivo

`form-doc/scripts/grado.js` debe adaptarse sin rediseño visual para:

- dejar de enviar `usuario` como autoridad en operaciones propias;
- enviar el usuario objetivo sólo en contexto global Admin/Comité cuando el
  contrato lo requiera;
- incluir CSRF en CREATE, UPDATE y DELETE;
- usar respuestas HTTP/JSON estructuradas;
- mostrar mensajes reales de éxito, sin cambios y error;
- no recargar ni pintar éxito ante `ok=false`;
- confirmar DELETE antes de enviarlo;
- refrescar la sección correcta tras una mutación confirmada;
- tratar READ lista vacía como resultado válido;
- soportar múltiples Grados;
- retirar logs de diagnóstico que expongan payloads o respuestas si permanecen
  en las rutas intervenidas.

La visibilidad de Editar/Eliminar debe alinearse con el contexto efectivo para
evitar acciones inútiles, pero sigue siendo una ayuda visual. El endpoint repite
la autorización completa.

Las altas contextuales de Institución/Título se preservan. La Task de Grado sólo
ajusta la coordinación necesaria para que sus respuestas autorizadas se integren
con el nuevo contrato de Grado.

## 16. Alcance cerrado de la futura Task integral

### 16.1 Archivos modificables definitivos

- `src/Model/Grado.php`;
- `ajax/grado.php`;
- `form-doc/scripts/grado.js`;
- `form-doc/agr.form.dat.acad.php`.

`form-doc/agr.form.dat.acad.php` es el único archivo adicional de formulario
autorizado. Su intervención queda limitada a:

- emisión o uso del token CSRF requerido por Grado;
- transporte del contexto propio/global estrictamente necesario;
- visibilidad de acciones CRUD conforme al contexto autorizado;
- soporte mínimo del formulario compartido por Estudiante, Profesor y contexto
  administrativo.

No se autoriza implícitamente ningún otro archivo de vista.

### 16.2 Archivos y fronteras protegidos

- `src/Model/Institucion.php`;
- `ajax/institucion.php`;
- `src/Model/Titulo.php`;
- `ajax/titulo.php`;
- `ajax/login.php`;
- `src/Model/Login.php`;
- `src/Security/Authorization.php`;
- Ficha Estudiante como objeto integral;
- Ficha Docente como objeto integral;
- `form-doc/header.php`;
- `index.php`;
- `docs/roadmap/ROADMAP.md`;
- ADR vigentes;
- BD/schema;
- `migrations/`.

Si durante la implementación aparece la necesidad de modificar cualquier otro
archivo, la ejecución debe detenerse y volver a revisión de alcance. Lo mismo
aplica ante una necesidad de cambiar schema, reabrir Institución o Título, o
redefinir acceso a perfil.

### 16.3 Unidad de implementación

CREATE, READ, UPDATE y DELETE forman una única Task integral de Grado. No deben
dividirse automáticamente en micro-Tasks porque autorización, ownership,
respuestas, CSRF y frontend comparten el mismo contrato funcional.

## 17. Validación funcional futura

La VF es integral y será ejecutada exclusivamente por el usuario después de la
implementación y revisión técnica.

### 17.1 Profesor con acceso vigente a Mi Perfil

- lista todos sus Grados, incluidos múltiples registros;
- crea un Grado propio;
- carga y edita un Grado propio;
- confirma y elimina un Grado propio;
- no consulta lista o detalle de otro usuario;
- no actualiza ni elimina un `id_grado` ajeno;
- manipular `usuario` no cambia el propietario ni amplía el acceso;
- sin Admin/Comité no accede a Grados de Estudiantes u otros Profesores.

### 17.2 Estudiante con `perfil.ver`

- lista todos sus Grados, incluidos múltiples registros;
- crea, carga, edita y elimina sólo sus Grados;
- manipular `usuario` se rechaza o se ignora sin cambiar autoridad;
- manipular un `id_grado` ajeno no revela ni muta el registro.

### 17.3 Admin

- lista y carga Grados de Estudiantes y Profesores válidos;
- crea un Grado para un objetivo válido;
- actualiza cualquier Grado válido sin reasignar silenciosamente propietario;
- confirma y elimina cualquier Grado válido;
- un usuario objetivo inexistente o fuera del dominio aprobado se rechaza.

### 17.4 Comité

- ejecuta la misma matriz CRUD global aprobada para Grado;
- conserva facultades globales cuando también posee Docencia;
- los objetivos inválidos se rechazan antes de persistencia.

### 17.5 Bloqueos de acceso

- Profesor sin acceso vigente a Mi Perfil y sin Admin/Comité: bloqueado;
- Estudiante sin `perfil.ver` y sin Admin/Comité: bloqueado;
- actor autenticado no contemplado: bloqueado;
- anónimo: bloqueado en lista, detalle, CREATE, UPDATE y DELETE;
- permiso 3/`aceptado` aislado: bloqueado;
- permiso 5 sin consumir `perfil.ver`: no es autoridad directa.

### 17.6 Seguridad, validación y consistencia

- CSRF ausente en CREATE, UPDATE o DELETE: rechazo sin escritura;
- CSRF inválido: rechazo sin escritura;
- `id_grado` mal formado: rechazo;
- `id_grado` inexistente: respuesta inequívoca, sin falso éxito;
- Institución inexistente: rechazo sin escritura;
- Título inexistente: rechazo sin escritura;
- fecha con formato inválido o fecha civil imposible: rechazo;
- CREATE con cero o más de una fila afectada: fallo;
- UPDATE sin diferencias: respuesta honesta `SIN_CAMBIOS` o equivalente;
- UPDATE con cero filas por conflicto/inexistencia: fallo, no “Editado”;
- DELETE con cero o más de una fila afectada: fallo, no “Eliminado”;
- segundo DELETE: no informa éxito;
- SQL con caracteres especiales no altera la consulta ni permite inyección;
- invocación directa del endpoint respeta la misma matriz.

### 17.7 Regresión de consumers

- Ficha Estudiante propia;
- Ficha Profesor propia;
- Ficha Estudiante en contexto Admin/Comité;
- Ficha Profesor en contexto Admin/Comité;
- múltiples Grados se renderizan y refrescan correctamente;
- lista vacía se renderiza sin error;
- altas contextuales autorizadas de Institución y Título siguen funcionando;
- Institución y Título existentes siguen siendo seleccionables;
- DELETE de Grado no elimina catálogos ni otros antecedentes.

## 18. Condiciones de detención para la futura Task

Detener antes de implementar si:

1. aparece una fuente institucional contradictoria;
2. se descubre un AT integral equivalente que deba gobernar el mismo objeto;
3. surge necesidad obligatoria de cambiar schema;
4. el cierre exige reabrir Institución o Título;
5. el acceso vigente a perfil deja de poder expresarse con los contratos
   vigentes;
6. aparece otro consumer cuyo contrato cambie materialmente el alcance;
7. aparece la necesidad de modificar cualquier archivo fuera de la lista
   definitiva de la sección 16.

Ninguna de estas condiciones apareció durante la creación de este AT.

## 19. Gobierno y cierre documental

- Grado es un objeto autoridad independiente.
- Ficha Académica sólo consume Grado.
- El cierre de Grado desbloquea parcialmente Ficha Académica, pero no la cierra.
- Institución y Título permanecen cerrados y no se redefinen.
- No existe aprobación administrativa para Grado.
- CREATE y UPDATE tienen vigencia inmediata.
- DELETE es físico y acotado a `grado_academico`.
- No se requiere ADR: las decisiones usan los contratos vigentes de EPIC-003,
  EPIC-008, ADR-001 y EPIC-009.
- No se requiere addendum.
- El alcance exacto queda cerrado a los cuatro archivos de la sección 16.1.
- El formulario compartido autorizado queda cerrado exclusivamente a
  `form-doc/agr.form.dat.acad.php`.
- La limitación de atomicidad multi-request queda registrada como deuda técnica
  existente no bloqueante y no amplía la Task.
- No quedan decisiones institucionales pendientes para redactar la Task.
- La Task integral `TASK-EPIC009-GRADO-INTEGRAL-001` quedó CERRADA después de
  completarse la implementación, aprobarse la revisión técnica y aprobar el
  usuario la VF.

## 20. Resultado consolidado

```text
Entidad: grado_academico
Ownership: grado_academico.usuario
CRUD: CREATE/READ/UPDATE/DELETE implementados integralmente
Propietario: CRUD propio con acceso vigente a Mi Perfil
Admin: CRUD global sobre Grados de Estudiantes y Profesores
Comité: CRUD global sobre Grados de Estudiantes y Profesores
Aprobación: no existe
DELETE: físico
Schema/migraciones: sin cambios
Institución/Título: dependencias cerradas, no se reabren
Ficha Académica: consumer, no autoridad
Capability nueva: no requerida
perfil.ver: dependencia vigente para Estudiante
permiso 3/aceptado: no autorizado
permiso 5: sólo productor transitorio indirecto de perfil.ver
VF: APROBADA POR EL USUARIO
Archivos modificables: Grado.php, grado.php, grado.js y agr.form.dat.acad.php
Atomicidad multi-request: deuda técnica existente no bloqueante
Task integral: TASK-EPIC009-GRADO-INTEGRAL-001 — CERRADA
```
