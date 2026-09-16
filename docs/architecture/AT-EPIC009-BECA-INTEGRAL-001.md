# AT-EPIC009-BECA-INTEGRAL-001 — Beca integral

- **EPIC principal:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Coordinación:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia; EPIC-003 — Identidad y autorización.
- **Objeto:** Beca.
- **Entidad autoridad:** `beca`.
- **Catálogo gobernado:** `nombre_beca`.
- **Consumer principal:** Ficha Académica de Estudiante.
- **Clasificación:** [ARQ] antecedente individual + catálogo gobernado; [AUTH] ownership y matriz por actor; [DATA] evolución mínima de `nombre_beca`; [SEC] autorización, IDOR, CSRF y SQL; [GOV] autonomía del Estudiante con normalización institucional.
- **Estado:** APROBADO.
- **Arquitectura:** APROBADA.
- **Matriz institucional:** APROBADA.
- **Ownership:** APROBADO.
- **Modelo de catálogo:** APROBADO.
- **Evolución de datos:** APROBADA.
- **Alcance:** CERRADO.
- **Decisiones pendientes:** NINGUNA BLOQUEANTE.
- **Implementación:** NO INICIADA.
- **Revisión técnica:** PENDIENTE.
- **VF:** PENDIENTE.
- **Blockers:** NINGUNO.
- **Task asociada:** `TASK-EPIC009-BECA-INTEGRAL-001`.
- **Migración asociada:** NO CREADA.
- **Fecha:** 2026-09-12.
- **Tipo de ejecución:** consolidación arquitectónica y documental; no se modificaron código, base de datos, migraciones, Roadmap ni ADR.

## 1. Propósito y dictamen ejecutivo

Este AT consolida la inspección integral aprobada de Beca y las decisiones
institucionales posteriores. Define una única frontera para el antecedente
individual `beca`, su ownership, el CRUD objetivo, el gobierno del catálogo
compartido `nombre_beca`, la propuesta inmediata de nuevos nombres y la
evolución mínima del esquema necesaria para distinguir valores oficiales,
pendientes e inactivos.

Beca no es un recurso compartido con múltiples participantes. Cada fila de
`beca` pertenece a un único Estudiante mediante `beca.alumno`. El hecho de que
varias filas referencien un mismo nombre o Institución no transforma el
antecedente en un recurso compartido. ADR-003 no aplica.

El CRUD actual no constituye un contrato integral: CREATE y READ existen con
defectos críticos; UPDATE y DELETE del antecedente faltan. El endpoint permite
acceso directo anónimo, confía en el propietario enviado por el cliente,
interpola SQL y no exige CSRF. La implementación futura debe cerrar estos
defectos como una sola unidad coherente.

El catálogo requiere dos atributos nuevos y solamente dos:

```text
estado_catalogo
propuesto_por
```

La futura migración preservará todas las filas. Los valores históricos válidos
se inicializarán como `APROBADA`. La fila vacía con `tipo_beca=0` se conservará
sin corregir nombre ni tipo y se clasificará como `INACTIVA`, dejando registro
explícito de su excepcionalidad histórica.

**Dictamen: A. AT integral aprobado y habilitante de la Task asociada.**

## 2. Alcance de esta ejecución y baseline Git

Baseline inspeccionado:

```text
rama: refactor/fase-0-seguridad
HEAD: 1d04ee434b39b9117261295f225677ed28dacd82
origin/refactor/fase-0-seguridad: 1d04ee434b39b9117261295f225677ed28dacd82
```

Local y remoto estaban sincronizados al verificar el baseline. Staging y diffs
versionados estaban vacíos.

Existían dos archivos no versionados previos:

```text
docs/architecture/ADR-003-RECURSOS-ACADEMICOS-COMPARTIDOS-PARTICIPACION.md
files/prog_curso/curso_45afc744db0fb82f1db6f16d31ded67a.pdf
```

Ambos quedan protegidos. El primero fue consultado como fuente arquitectónica,
pero continúa sin seguimiento y en estado `Proposed`; este AT no lo modifica,
versiona ni convierte en decisión aplicable a Beca. El PDF es ajeno al objeto.

La ejecución original creó exclusivamente este documento. La aprobación formal
posterior actualizó su estado y creó la Task asociada; la migración continúa sin
crear. Ninguna de ambas ejecuciones implementa código, modifica datos o altera
staging.

## 3. Fuentes y evidencia consolidada

Fuente institucional directa:

- resultado aprobado de la Inspección Integral Beca;
- matriz institucional y evolución mínima de datos aprobadas en la solicitud
  de creación de este AT;
- reglas vigentes de EPIC-003 para identidad y acceso a perfil;
- contratos de persistencia de EPIC-008;
- objetivo de consolidación CRUD de EPIC-009.

Evidencia técnica principal:

- `src/Model/Beca.php`;
- `ajax/beca.php`;
- `form-doc/agr.form.beca.php`;
- `form-doc/scripts/beca.js`;
- `form-doc/ficha.estudiante.php`;
- `form-doc/info.estudiante.php` y `form-doc/scripts/info.estudiante.js`;
- `form-doc/scripts/ficha.estudiante.js`;
- `admin/ver.estudiante.php` y `admin/scripts/ver.estudiante.js`;
- `admin/act.list.php` y `admin/scripts/listas.js`;
- `js/funcAjax.js`;
- `form-doc/scripts/usuario.js`;
- `src/Config/conexion.php`;
- `src/Security/Authorization.php`;
- `ajax/institucion.php` y `src/Model/Institucion.php` como dependencia cerrada;
- AT y Tasks parciales EPIC-008 de Beca;
- AT integrales EPIC-009 de Grado Académico y Postdoctorado;
- esquema y datos reales consultados exclusivamente mediante lectura;
- historial Git relevante.

No se encontró una segunda tabla de propuestas, un estado equivalente ya
existente en `nombre_beca`, otra implementación del objeto ni una contradicción
con la matriz aprobada. No se activa ninguna condición de detención para crear
este AT.

## 4. Frontera funcional

La entidad autoridad del antecedente es:

```text
beca
├── id_beca       PK
├── nom_beca      FK → nombre_beca.id_nom_beca
├── inst_beca     FK → institucion.id_inst
├── fech_in
├── fech_ter
└── alumno        FK → usuario.id_usuario; ownership
```

El catálogo institucional es:

```text
nombre_beca
├── id_nom_beca   PK
├── beca          nombre
└── tipo_beca     1 Interna | 2 Externa
```

Después de la migración conservará la misma identidad y agregará el estado del
valor y la identidad interna que originó una propuesta.

La instancia `beca` contiene el antecedente individual. `nombre_beca` e
`institucion` son recursos de catálogo referenciados. Ficha Académica no es
autoridad sobre ninguno: solamente presenta y permite operar el proveedor Beca
de acuerdo con el actor efectivo.

No se crea relación Profesor-Beca, tabla de participantes, actor principal,
terceros ni múltiples beneficiarios. Una persona puede tener cero, una o varias
filas `beca`, pero cada fila tiene un único propietario Estudiante.

## 5. Estado técnico actual

| Operación | Estado EPIC-009 | Evidencia actual | Defecto principal |
| --- | --- | --- | --- |
| CREATE Beca | Existe / defectuoso | `op=insert-beca` → `Beca::insertar()` | Anónimo, propietario cliente, sin CSRF/validación y SQL interpolado |
| READ lista | Existe / defectuoso | `op=read` → `Beca::mostrar($usuario)` | IDOR, acceso anónimo y sin detalle autorizado |
| UPDATE Beca | Falta | No hay operación ni método sobre `beca` | No puede editarse el antecedente |
| DELETE Beca | Falta | No hay operación ni método sobre `beca` | No existe borrado funcional ni confirmación |
| CREATE catálogo | Existe / defectuoso | `insert`/`insert-update` | Cualquier actor; contrato de tipo incompatible y posible tipo 0 |
| READ catálogo | Existe / defectuoso | `read-nombre`/`read_lista` | Sin autorización, estado ni visibilidad contextual |
| UPDATE catálogo | Existe / defectuoso | `Beca::editar()` | Mutación global anónima, SQL interpolado y falso éxito posible |
| Gobierno catálogo | Falta | No hay aprobación, unificación o inactivación | No distingue oficial, propuesta ni rechazo |

Los AT parciales EPIC-008 corrigieron contratos aislados de lista, escritura y
caller de edición. Esos incrementos no prueban cierre integral y sus estados
documentales históricos no reflejan por completo el código ya incorporado.

## 6. Matriz institucional objetivo

| Actor efectivo | CREATE | READ | UPDATE | DELETE | Catálogo `nombre_beca` |
| --- | --- | --- | --- | --- | --- |
| Estudiante con perfil vigente | Sólo Beca propia | Sólo propias | Sólo propia | Sólo propia | Consulta aprobadas; propone; usa su pendiente |
| Profesor | Rechazado | Rechazado | Rechazado | Rechazado | Sin propuesta ni administración |
| Admin | Global sobre Estudiantes | Global sobre Estudiantes | Global sobre Estudiantes | Global sobre Estudiantes | Administración global |
| Comité | Global sobre Estudiantes | Global sobre Estudiantes | Global sobre Estudiantes | Global sobre Estudiantes | Administración global |
| Actor autenticado sin regla anterior | Rechazado | Rechazado | Rechazado | Rechazado | Rechazado |
| Anónimo | Rechazado | Rechazado | Rechazado | Rechazado | Rechazado |

Admin y Comité pueden acumular otras calidades; prevalece su facultad global.
Esa facultad sólo opera sobre objetivos que el backend valide como Estudiantes.
Ser Profesor, poseer permiso histórico 3 o conocer un identificador no concede
facultad sobre Beca.

## 7. Identidad, autorización y ownership

El único campo autoridad es:

```text
beca.alumno
```

### 7.1 Estudiante propietario

El backend debe exigir sesión autenticada, acceso vigente al perfil mediante el
contrato público aplicable y una identidad Estudiante coherente. Debe resolver
el `id_usuario` desde la sesión; `usuario`, `alumno` o cualquier equivalente
recibido del cliente se ignora o rechaza como intento de seleccionar
propietario.

El permiso histórico 5 no se consulta directamente: hoy participa en la
producción de `perfil.ver`, que es el contrato reutilizable. El permiso 3 o la
clave histórica `aceptado` no son autoridad suficiente.

### 7.2 Admin y Comité

Se reutiliza `Authorization::hasAny(['admin', 'comite'])`. Para CREATE o lista
global, el backend puede aceptar un usuario objetivo como dato de solicitud,
pero debe comprobar existencia y especialización Estudiante. Para detalle,
UPDATE y DELETE, el propietario real se obtiene desde la fila persistida; un
usuario adicional del cliente sólo puede comprobar consistencia.

### 7.3 Profesor y anónimo

El Profesor no posee CRUD propio de Beca ni capacidad de propuesta. El acceso
directo al endpoint debe devolver rechazo, aunque el Profesor tenga Mi Perfil o
conozca IDs válidos. Toda petición anónima se rechaza antes de consultar o
escribir.

### 7.4 Inmutabilidad

CREATE fija `alumno` desde backend. READ propio lo utiliza como filtro. UPDATE y
DELETE propios deben combinar `id_beca` y `alumno` en la condición persistente,
además de la guard del endpoint. UPDATE no puede modificar ni reasignar
`alumno`. La autoridad global tampoco convierte la reasignación en una edición
permitida.

No son autoridad suficiente:

- IDs enviados por cliente;
- visibilidad de botones;
- haber abierto una página protegida;
- casts numéricos;
- respuestas truthy del driver;
- permisos históricos interpretados directamente.

No se requiere cambiar Login ni `Authorization` para expresar esta matriz.

## 8. Contrato CRUD objetivo de Beca

### 8.1 CREATE

Debe:

1. exigir autenticación y resolver actor efectivo;
2. derivar propietario de sesión para Estudiante o validar objetivo Estudiante
   para Admin/Comité;
3. validar tipo, nombre de catálogo, Institución y fechas;
4. resolver si el nombre es aprobado, propuesta propia reutilizable o propuesta
   nueva;
5. ejecutar INSERT parametrizado mediante `ejecutarEscritura()`;
6. confirmar exactamente una fila afectada e ID insertado válido;
7. responder éxito sólo después de confirmar persistencia.

Cuando se crea una propuesta, la propuesta y la fila `beca` deben confirmarse
en una misma transacción. Una Institución creada mediante request contextual
separado no forma parte de esa transacción.

### 8.2 READ

Debe ofrecer:

- lista de cero, una o múltiples Becas autorizadas;
- detalle de una Beca para edición;
- lista de nombres aplicable al tipo y actor;
- vistas administrativas del catálogo aprobadas, pendientes e inactivas.

El Estudiante sólo puede listar o cargar detalle propio. Admin y Comité pueden
hacerlo sobre cualquier Estudiante válido. Las consultas deben seleccionar
columnas explícitas, parametrizar inputs y no exponer datos de terceros.

Una lista propia vacía es éxito con arreglo vacío. Un recurso inexistente o no
autorizado se trata según la política de no revelación del endpoint y nunca
devuelve el registro ajeno.

### 8.3 UPDATE

Debe crearse un UPDATE real de `beca` que permita modificar únicamente:

```text
nom_beca
inst_beca
fech_in
fech_ter
```

Debe validar ID positivo, existencia, actor, ownership o autoridad global,
catálogo permitido, Institución y fechas. `alumno` permanece inmutable. Para
operación propia, la escritura debe acotarse por `id_beca` y `alumno`.

El UPDATE será parametrizado, utilizará `ejecutarEscritura()` e interpretará
`filasAfectadas`. Un payload válido idéntico puede responder honestamente
`ok=true`, `codigo=SIN_CAMBIOS`, sin afirmar una modificación. Cero filas por
inexistencia, pérdida de ownership o conflicto no es éxito.

Si se edita la fila histórica con fechas invertidas, el resultado final debe
cumplir la regla vigente completa.

### 8.4 DELETE

DELETE elimina físicamente una única fila `beca`. Debe:

1. pedir confirmación explícita en frontend;
2. exigir autenticación, autorización, ownership aplicable y CSRF;
3. validar ID y existencia;
4. ejecutar DELETE parametrizado mediante `ejecutarEscritura()`;
5. acotar la sentencia a `id_beca` y, para propietario, `alumno`;
6. exigir exactamente una fila afectada;
7. refrescar el consumer únicamente ante `ok=true`.

Esta operación no elimina usuario, Institución ni `nombre_beca`. Los `CASCADE`
del esquema no constituyen el mecanismo funcional de DELETE Beca. Un segundo
DELETE del mismo ID no puede informar un nuevo éxito.

## 9. Catálogo institucional `nombre_beca`

El catálogo es compartido, pero la instancia de Beca continúa siendo
individual. Una modificación de nombre o tipo puede cambiar la proyección de
todas las Becas asociadas; la UI administrativa debe mostrar ese impacto antes
de confirmar.

Estados admitidos:

```text
APROBADA
PENDIENTE
INACTIVA
```

`INACTIVA` representa tanto rechazo como retiro de disponibilidad. No se crea
un cuarto estado `RECHAZADA`, porque no es necesario para el workflow mínimo.
La acción y el mensaje pueden indicar rechazo; la persistencia converge en
`INACTIVA`.

Transiciones mínimas:

```text
PENDIENTE → APROBADA
PENDIENTE → INACTIVA
APROBADA  → INACTIVA
INACTIVA  → APROBADA, sólo por Admin/Comité y con validación completa
```

No se introduce aprobación de la fila `beca`: el estado pertenece solamente al
valor de catálogo.

### 9.1 Visibilidad

Para crear o cambiar una Beca, un Estudiante puede seleccionar:

- entradas `APROBADA` del tipo escogido;
- una entrada `PENDIENTE` propuesta por él cuando ya la utiliza o la coincidencia
  corresponde a su propia propuesta.

No se muestran propuestas pendientes de terceros ni entradas inactivas como
opciones generales. Sin embargo, una entrada pendiente propia o inactiva ya
referenciada debe poder proyectarse en el antecedente existente para no perder
legibilidad histórica.

Admin y Comité pueden listar entradas por tipo y estado, incluida la fila
histórica excepcional.

### 9.2 Propuesta de nuevo nombre

El flujo “Agregar otra beca” es:

1. el Estudiante selecciona Interna o Externa;
2. ingresa el nombre;
3. el backend normaliza sólo para comparar —trim, espacios equivalentes y
   comparación compatible con la collation— sin alterar silenciosamente el
   texto persistido;
4. busca coincidencia por nombre y mismo tipo;
5. reutiliza una entrada `APROBADA` adecuada;
6. si no existe, reutiliza una propuesta `PENDIENTE` del mismo Estudiante;
7. una propuesta pendiente de tercero no se expone ni se reutiliza como valor
   disponible para el solicitante;
8. si no hay entrada utilizable, crea `PENDIENTE` con `propuesto_por` igual al
   Estudiante autenticado;
9. crea inmediatamente la fila `beca` asociada;
10. confirma ambas escrituras de forma atómica.

El Estudiante no espera aprobación para conservar su antecedente. No puede
editar, aprobar, unificar, inactivar ni eliminar entradas del catálogo.

### 9.3 Administración oficial

Admin y Comité pueden:

- crear directamente una entrada `APROBADA`;
- corregir nombre y tipo después de mostrar cantidad de Becas afectadas;
- aprobar una propuesta;
- inactivar una entrada;
- resolver propuestas duplicadas mediante unificación.

La edición de tipo debe validar solamente `1` o `2`. Una entrada aprobada queda
disponible en la lista general de su tipo.

### 9.4 Unificación

La unificación requiere origen y destino distintos. El destino debe ser una
entrada oficial `APROBADA`. En una transacción debe:

1. bloquear o estabilizar origen y destino durante la operación;
2. reasignar todas las filas `beca.nom_beca` del origen al destino;
3. comprobar filas afectadas y referencias restantes;
4. marcar el origen `INACTIVA`, sin borrarlo;
5. confirmar todo o revertir todo.

El frontend debe mostrar origen, destino y número de Becas afectadas y exigir
confirmación. No se permite unificar cambiando propietarios ni otras columnas
de `beca`.

### 9.5 Rechazo e inactivación

Una entrada referenciada no se borra. Rechazar una propuesta usada cambia su
estado a `INACTIVA` y preserva la Beca, el texto y la referencia histórica. La
entrada continúa visible dentro del antecedente que ya la referencia, pero no
puede seleccionarse para nuevas Becas.

`RECHAZADA` es la denominación operativa de la acción administrativa cuyo
estado persistente resultante es `INACTIVA`; no constituye un cuarto estado.
Una entrada denominada `INACTIVA` o `RECHAZADA` no está disponible para nuevas
selecciones, pero debe seguir siendo resoluble y visible para toda Beca
histórica que ya la referencie. Su inactivación no puede causar pérdida de
datos históricos ni ocultar antecedentes existentes.

Para reemplazarla por un valor oficial debe utilizarse unificación o UPDATE
autorizado de la Beca. Una entrada sin referencias tampoco se borra dentro de
este contrato: se inactiva para conservar trazabilidad y evitar semánticas
distintas según cardinalidad.

Toda unificación debe reasignar las referencias a la entrada destino antes de
inactivar el origen, comprobar que no quedan referencias destinadas a
reasignación y preservar la integridad de las FK. La operación no puede ocultar
antecedentes existentes, ni siquiera durante un fallo intermedio: ante error se
revierte completa.

## 10. Tipos de Beca

`tipo_beca` representa exclusivamente:

```text
1 = Interna
2 = Externa
```

No representa estado, aprobación, rol, ownership ni procedencia. Frontend y
backend deben usar un contrato único y rechazar `0`, strings desconocidos y
cualquier otro entero en nuevas escrituras.

No se agrega de inmediato un `CHECK tipo_beca IN (1,2)` porque la fila
histórica tipo 0 debe preservarse sin saneamiento. La prohibición de nuevos
valores inválidos se aplica en backend y en toda operación de catálogo. Una
restricción de base de datos sólo podría incorporarse en otro incremento que
resuelva explícitamente la excepción histórica.

## 11. Evolución mínima del modelo de datos

La futura migración modifica únicamente `nombre_beca` y agrega:

| Campo | Contrato propuesto |
| --- | --- |
| `estado_catalogo` | `VARCHAR(10) NOT NULL`; valores `APROBADA`, `PENDIENTE`, `INACTIVA` |
| `propuesto_por` | `INT NULL`; FK a `usuario.id_usuario`; identidad interna que originó la propuesta |

`propuesto_por` es nullable porque los valores oficiales históricos no poseen
proponente y porque una eliminación futura de la identidad no debe destruir el
catálogo. La FK debe usar `ON UPDATE CASCADE` y `ON DELETE SET NULL`, nunca
`CASCADE`. En operación normal, toda entrada nueva `PENDIENTE` debe tener
proponente no nulo; entradas administrativas `APROBADA` pueden tenerlo nulo.
Cuando una propuesta se aprueba, se conserva el proponente.

La migración debe agregar índices útiles para:

```text
(tipo_beca, estado_catalogo)
propuesto_por
```

Debe agregar una restricción de dominio para `estado_catalogo` si la versión y
el mecanismo vigentes de MariaDB la aplican efectivamente. La Task debe
verificarlo antes de fijar el DDL. No se agrega entidad Persona, tabla de
workflow, auditoría completa, timestamps, comentarios, revisor ni motivo de
rechazo.

### 11.1 Preservación de filas existentes

La migración debe ejecutarse de manera controlada:

1. agregar temporalmente `estado_catalogo` de forma compatible con backfill;
2. clasificar como `APROBADA` las filas existentes con nombre no vacío y
   `tipo_beca IN (1,2)`;
3. clasificar como `INACTIVA` la fila histórica vacía con `tipo_beca=0`;
4. mantener `propuesto_por=NULL` en todas las filas históricas;
5. no cambiar `id_nom_beca`, `beca` ni `tipo_beca`;
6. completar `NOT NULL` y constraints del estado;
7. verificar cardinalidad, valores y FK antes de finalizar.

Clasificar la excepción como inactiva no la sanea: la fila, ID, nombre vacío y
tipo 0 se preservan. No debe aparecer en opciones de Estudiante ni reutilizarse
para una nueva Beca.

### 11.2 Compatibilidad y reversión

La migración debe preservar las referencias actuales de `beca.nom_beca`. Su
aplicación requiere respaldo y consultas pre/post de cardinalidad. Un rollback
que elimine las columnas sólo es seguro antes de recibir propuestas nuevas; si
ya existe información de estado/proponente, la reversión exige preservarla
previamente y no puede consistir en un `DROP COLUMN` automático.

La migración no modifica `beca`, `institucion`, `usuario`, Ficha ni tablas de
Profesor. La nueva FK referencia `usuario` sin cambiar su modelo o endpoint.

## 12. Validación de datos

La validación frontend mejora UX, pero el backend conserva autoridad.

| Dato | Contrato mínimo |
| --- | --- |
| `id_beca` | Entero positivo para detalle, UPDATE y DELETE; existencia comprobada |
| `alumno` | Derivado de sesión o validado como objetivo Estudiante para Admin/Comité |
| `nom_beca` | ID positivo, existente y utilizable según actor/estado; no acepta la fila tipo 0 |
| `inst_beca` | ID positivo y fila existente en `institucion` |
| `fech_in` | String exacto `YYYY-MM-DD` y fecha civil real |
| `fech_ter` | String exacto `YYYY-MM-DD` y fecha civil real; mayor o igual a inicio |
| nombre propuesto | No vacío tras trim, dentro de 80 caracteres y validado para salida segura |
| `tipo_beca` | Entero estricto `1` o `2` |
| `estado_catalogo` | Transición permitida y actor Admin/Comité, salvo creación pendiente del Estudiante |

IDs y fechas deben rechazarse si contienen forma parcial o ambigua. Institución
se valida en backend antes de escribir. Todo texto se escapa al renderizar; el
escape de salida no reemplaza la parametrización SQL.

## 13. Fechas de Beca

Ambas fechas son obligatorias. La regla hacia adelante es:

```text
formato exacto: YYYY-MM-DD
fecha civil existente
fech_in <= fech_ter
```

La validación debe repetirse en backend para CREATE y UPDATE. No se infieren
reglas adicionales de vigencia, antigüedad, fecha actual o estado de programa.

Existe una fila histórica con inicio `2026-03-26` y término `2026-03-12`. Ni el
AT ni la futura migración de catálogo deben corregirla. READ debe seguir
proyectándola. Si esa Beca se edita, todos los valores finales, incluidas ambas
fechas, deben satisfacer la regla vigente antes de persistir.

## 14. Persistencia, SQL y retornos

Toda consulta que incorpore input externo debe usar placeholders y parámetros,
incluidos READ, búsquedas de coincidencia y conteos administrativos.

INSERT, UPDATE y DELETE deben utilizar:

```text
ejecutarEscritura()
```

El caller interpreta explícitamente:

```text
filasAfectadas
idInsertado
```

No se evalúa el arreglo de retorno como booleano genérico. INSERT exige una
fila e ID válido cuando corresponda; DELETE exige una fila; UPDATE distingue
cambio, sin cambios, inexistencia y conflicto de autorización.

Los métodos heredados de Beca no deben continuar interpolando nombres, fechas,
tipos ni IDs. Los SELECT deben retornar estructuras asociativas explícitas y no
`SELECT *`. Las excepciones se registran de forma segura y no se exponen al
cliente.

## 15. Transacciones

Son operaciones atómicas obligatorias:

- crear propuesta pendiente y crear la Beca que la usa;
- unificar catálogo, reasignar Becas e inactivar el origen;
- cualquier administración de catálogo que requiera más de una escritura para
  conservar invariantes.

El modelo puede utilizar la conexión PDO singleton vigente y
`ejecutarEscritura()` dentro de la misma transacción. Debe comenzar, confirmar
o revertir explícitamente y no ocultar un rollback fallido.

La creación contextual de Institución continúa como request separado y no se
incluye artificialmente en la transacción Beca. Si aquella request ya se
completó y la creación posterior falla, permanece la deuda multi-request
existente; este AT no la resuelve globalmente.

## 16. Institución como dependencia cerrada

`inst_beca` debe apuntar a una Institución existente. Beca consume el contrato
vigente de alta contextual y lectura de Institución.

Quedan expresamente protegidos:

```text
src/Model/Institucion.php
ajax/institucion.php
form-doc/scripts/usuario.js
```

No se cambia su autorización, SQL, FK ni política de creación. El `ON DELETE
CASCADE` histórico desde Institución hacia Beca no se utiliza como DELETE
funcional. Revisar globalmente esa política referencial pertenece a una unidad
de gobierno de datos distinta.

## 17. CSRF

Requieren token CSRF válido ligado a sesión:

- CREATE, UPDATE y DELETE Beca;
- creación de propuesta;
- CREATE/UPDATE de catálogo oficial;
- aprobación y normalización;
- unificación;
- rechazo o inactivación.

El token debe generarse con entropía criptográfica, exponerse únicamente a
vistas autorizadas y compararse con `hash_equals`. Puede enviarse mediante
`X-CSRF-Token`. Su ausencia o invalidez se rechaza antes de cualquier escritura.

READ no necesita CSRF, pero sí autenticación y autorización. El formulario de
Beca puede proveer el token a la UI de Ficha; `admin/act.list.php` debe proveerlo
a “Editar listas”. No se autoriza modificar un sistema CSRF global.

## 18. HTTP y JSON

`ajax/beca.php` debe fijar:

```text
Content-Type: application/json; charset=utf-8
```

Toda respuesta será un objeto. Contrato mínimo de éxito:

```json
{
  "ok": true,
  "codigo": "BECA_CREADA",
  "mensaje": "Beca creada correctamente.",
  "datos": { "id_beca": 123 }
}
```

Contrato mínimo de error:

```json
{
  "ok": false,
  "error": "DATOS_INVALIDOS",
  "mensaje": "Los datos de la beca no son válidos."
}
```

Códigos mínimos:

| HTTP | Caso |
| --- | --- |
| 400 | Operación, payload, ID, tipo o transición mal formados |
| 401 | Sesión no autenticada |
| 403 | Actor sin facultad, ownership ajeno o CSRF inválido |
| 404 | Beca, Estudiante objetivo, Institución o catálogo inexistente cuando pueda revelarse |
| 409 | Conflicto de estado, unificación o catálogo concurrente |
| 422 | Nombre, fecha, tipo o referencia semánticamente inválidos |
| 500 | Error interno no expuesto |

Listas exitosas usan `datos: []`. No se devuelven strings JSON ambiguos, SQL,
trazas ni excepciones crudas.

## 19. Frontend Estudiante objetivo

El flujo presenta:

```text
Tipo: Interna | Externa
Nombre: lista filtrada por tipo
Alternativa: Agregar otra beca
Institución
Fecha de inicio
Fecha de término
```

La lista de nombres muestra aprobadas y, cuando corresponda, la propuesta
pendiente propia necesaria para representar el antecedente. No muestra
pendientes de terceros ni inactivas como nuevas opciones.

La interfaz debe soportar cero, una y múltiples Becas, detalle, edición,
confirmación de borrado y refresh sin duplicar tarjetas. Debe eliminar
`async:false`, parseo manual, globals implícitas y logs de payloads. Debe manejar
errores HTTP/JSON y renderizar contenido como texto seguro.

El cliente no decide propietario. Un atributo DOM con usuario puede utilizarse
como contexto visual para Admin/Comité, pero no como autoridad.

## 20. Administración desde “Editar listas”

Admin y Comité continúan gobernando `nombre_beca` desde la pantalla existente.
No se traslada el workflow a Ficha Estudiante.

La sección debe permitir:

- separar o filtrar Internas y Externas;
- listar aprobadas, pendientes e inactivas;
- mostrar proponente cuando exista;
- crear valor oficial;
- editar y normalizar nombre/tipo;
- aprobar;
- iniciar unificación con destino oficial;
- rechazar/inactivar;
- mostrar impacto y confirmar operaciones globales.

Estas necesidades hacen modificable `admin/scripts/listas.js` y
`admin/act.list.php`. No requieren cambiar el helper genérico `js/funcAjax.js`:
la futura Task debe implementar el contrato específico de Beca sin alterar el
render de otros catálogos.

## 21. Seguridad obligatoria

### H1 — Crítico

- cerrar acceso directo anónimo antes de consultar el modelo;
- impedir IDOR en lista, detalle, UPDATE y DELETE;
- derivar ownership propio en backend;
- parametrizar todo SQL;
- impedir creación para otro usuario y mutación global no autorizada.

### H2 — Alto

- exigir CSRF en todas las mutaciones;
- aplicar matriz por operación y gobierno de catálogo;
- escapar nombres de Beca e Institución al renderizar;
- no exponer propuestas pendientes de terceros;
- no filtrar información mediante diferencias innecesarias de error.

### H3 — Integridad

- impedir nuevos tipos 0 y nombres vacíos;
- validar fechas y referencias;
- interpretar filas afectadas e IDs insertados;
- usar transacciones en propuesta y unificación;
- preservar referencias al inactivar;
- eliminar falsos éxitos y contratos JSON incompatibles.

### H4 — UX y deuda

- retirar requests síncronas, parseo manual, globals y logs;
- mostrar estado vacío y múltiples filas correctamente;
- evitar duplicados tras refresh;
- presentar confirmaciones e impacto de acciones globales;
- incorporar Beca en los consumers de Estudiante omitidos.

## 22. Alcance cerrado de la futura Task integral

### 22.1 Archivos modificables definitivos

```text
src/Model/Beca.php
ajax/beca.php
form-doc/scripts/beca.js
form-doc/agr.form.beca.php
form-doc/scripts/ficha.estudiante.js
admin/scripts/ver.estudiante.js
admin/scripts/listas.js
admin/act.list.php
migrations/TASK-EPIC009-BECA-CATALOGO-001.sql
```

Justificación:

- modelo y endpoint expresan CRUD, catálogo, transacciones y seguridad;
- formulario y JS expresan CSRF y CRUD de Ficha;
- los dos callers incorporan la proyección omitida;
- pantalla y script administrativos expresan el gobierno del catálogo;
- la migración agrega únicamente estado y proponente.

### 22.2 Archivos y fronteras protegidos

```text
src/Security/Authorization.php
ajax/login.php
src/Model/Login.php
src/Config/conexion.php
src/Model/Institucion.php
ajax/institucion.php
form-doc/scripts/usuario.js
js/funcAjax.js
form-doc/ficha.estudiante.php
form-doc/info.estudiante.php
form-doc/scripts/info.estudiante.js
form-doc/ficha.docente.php
form-doc/scripts/ficha.docente.js
admin/ver.docente.php
admin/scripts/ver.docente.js
```

También quedan protegidos Institución, Grado Académico, Postdoctorado, Ficha
como objeto integral, vistas/scripts de Profesor, reportes generales, ADR-003,
Roadmap y todos los archivos untracked preexistentes.

No se modifica `js/funcAjax.js`: su contrato genérico y sus demás consumers no
necesitan cambiar para implementar la administración específica de Beca.

### 22.3 Unidad de implementación

Código, migración y VF forman una única Task integral. La Task deberá crear el
archivo SQL con el nombre fijado, pero no ejecutarlo automáticamente. La
aplicación del esquema debe seguir el procedimiento de migraciones del
proyecto, con respaldo, verificación y autorización separada.

No se permite ampliar la Task para modernizar componentes compartidos ni
corregir deudas no necesarias para el contrato aprobado.

## 23. Validación funcional futura

### 23.1 Estudiante con perfil vigente

- lista vacía, una y múltiples Becas propias;
- CREATE propio con nombre aprobado Interna y Externa;
- READ lista y detalle propios;
- UPDATE de nombre, Institución y fechas sin cambiar propietario;
- DELETE propio con confirmación;
- intento ajeno bloqueado en cada operación;
- `usuario` manipulado ignorado o rechazado;
- propuesta nueva creada y utilizada inmediatamente;
- coincidencia aprobada reutilizada;
- propuesta propia pendiente reutilizada y visible en su antecedente;
- propuesta pendiente no visible ni utilizable por otro Estudiante.

### 23.2 Admin

- CRUD global sobre Becas de Estudiantes válidos;
- objetivo inexistente, Profesor o inválido rechazado;
- listas de catálogo por tipo y estado;
- creación oficial aprobada;
- edición/normalización con impacto visible;
- aprobación de propuesta;
- unificación transaccional;
- rechazo/inactivación referenciada y no referenciada;
- fila histórica excepcional visible administrativamente pero no seleccionable.

### 23.3 Comité

Debe repetir la matriz global de Admin para Becas y catálogo, sin adquirir
facultades ajenas a este objeto.

### 23.4 Negativos de actor y seguridad

- anónimo rechazado en READ y mutaciones;
- Profesor rechazado aun con perfil vigente;
- permiso 3 aislado sin acceso;
- actor autenticado sin regla rechazado;
- CSRF ausente, inválido y de otra sesión;
- IDs ajenos, inexistentes, cero, negativos y mal formados;
- payloads de SQL injection en todos los textos/filtros;
- payloads XSS almacenados y reflejados renderizados como texto;
- método u operación no soportados.

### 23.5 Datos y fechas

- tipo `1` y `2` aceptados; `0`, otros enteros y strings desconocidos rechazados;
- nombre vacío, sólo espacios, excesivo y coincidencia normalizada;
- Institución válida e inexistente;
- fechas vacías, formato incorrecto, calendario imposible, iguales, ordenadas e
  invertidas;
- fila histórica invertida legible y UPDATE rechazado mientras el resultado
  final permanezca inválido;
- entrada histórica tipo 0 preservada, inactiva y no reutilizable;
- propuesta duplicada y concurrencia sobre coincidencias.

### 23.6 Transacciones y catálogo

- rollback completo si falla la Beca después de insertar propuesta;
- rollback completo si falla la propuesta;
- unificación reasigna todas las Becas y después inactiva origen;
- fallo intermedio de unificación no deja referencias parciales;
- destino inválido, inactivo, idéntico al origen o inexistente rechazado;
- inactivación no elimina Becas ni nombres;
- cero filas y operaciones repetidas no informan falso éxito;
- `propuesto_por` se conserva tras aprobación.

### 23.7 Consumers y contratos

- Ficha Estudiante propia carga, crea, edita y elimina sin duplicados;
- vista Admin carga y opera Becas del Estudiante seleccionado;
- vista Comité repite el contrato autorizado;
- Ficha Docente no muestra ni opera Beca;
- “Editar listas” gobierna catálogo sin afectar otros catálogos;
- listas vacías y múltiples filas tienen representación estable;
- respuestas usan objetos JSON, content type y códigos HTTP coherentes;
- no aparecen SQL, excepciones crudas ni datos de terceros.

La VF no se ejecuta durante la creación de este AT.

## 24. Condiciones de detención para la futura Task

La futura Task debe detenerse antes de implementar si:

1. aparece otra tabla activa de propuestas o estado equivalente ya implantado;
2. la rama, baseline o esquema difieren de forma incompatible;
3. staging contiene cambios o un archivo modificable tiene cambios ajenos que
   no puedan preservarse;
4. la matriz exige modificar identidad o EPIC-003;
5. la transacción exige cambiar `src/Config/conexion.php`;
6. la administración real del catálogo requiere archivos fuera de la lista
   cerrada;
7. la migración necesita modificar `beca`, `usuario`, `institucion` u otro
   objeto no autorizado;
8. no puede preservarse la fila histórica tipo 0 o las referencias vigentes;
9. aparece una regla institucional contradictoria;
10. la solución requiere participantes, Profesor-Beca o ADR-003.

Una condición de detención obliga a volver a revisión arquitectónica; no
autoriza ampliar silenciosamente la Task.

## 25. Riesgos y controles

| Riesgo | Severidad | Control requerido |
| --- | --- | --- |
| Propietario controlado por cliente | Crítica | Derivar sesión y acotar SQL por ownership |
| Propuestas visibles a terceros | Alta | Filtro por estado y `propuesto_por` en backend |
| Duplicación concurrente de catálogo | Alta | Búsqueda normalizada y transacción controlada |
| Unificación parcial | Crítica | Una transacción para reasignar e inactivar |
| Edición global sorpresiva | Alta | Mostrar referencias afectadas y confirmar |
| Borrado de catálogo referenciado | Alta | Inactivar; no DELETE físico de catálogo |
| Migración clasifica mal legado | Alta | Conteos pre/post y reglas explícitas de backfill |
| Tipo 0 vuelve a utilizarse | Alta | Rechazo backend y exclusión de listas |
| Fecha histórica bloquea READ | Media | Compatibilidad de lectura; regla estricta sólo al editar |
| Regresión de otros catálogos | Alta | Proteger `js/funcAjax.js` y usar flujo Beca específico |

## 26. Gobierno y trazabilidad

Clasificación consolidada:

```text
[ARQ] antecedente individual + catálogo gobernado
[AUTH] ownership y matriz por actor
[DATA] evolución mínima de nombre_beca
[SEC] autorización, IDOR, CSRF y SQL
[GOV] autonomía del Estudiante con normalización institucional
```

No se crea ADR nuevo. ADR-003 no aplica porque no hay recurso académico global
con participaciones múltiples.

Las decisiones institucionales necesarias están resueltas en este AT:

- propietario exclusivo Estudiante;
- Estudiante con CRUD propio;
- Admin y Comité con CRUD global;
- Profesor y anónimo sin acceso;
- propietario inmutable;
- DELETE físico sólo de `beca`;
- catálogo con propuesta inmediata y gobierno institucional;
- tres estados simples;
- dos campos nuevos mínimos;
- fecha inicial menor o igual a término;
- preservación explícita del legado inválido.

No queda una decisión institucional bloqueante. Los detalles de nombres de
códigos JSON, presentación visual o implementación interna pueden cerrarse en
la Task sin alterar estos contratos.

## 27. Siguiente artefacto

Con la aprobación formal de este AT se creó una única Task integral EPIC-009
para Beca: `TASK-EPIC009-BECA-INTEGRAL-001`. Esa Task debe:

1. fijar el baseline técnico;
2. incorporar exactamente los nueve archivos modificables;
3. crear, pero no ejecutar automáticamente, la migración definida;
4. implementar seguridad, CRUD, propuesta y gobierno como una unidad;
5. ejecutar validaciones estáticas y técnicas;
6. entregar la matriz de VF al usuario;
7. no cerrar Beca hasta aprobar migración, implementación y VF.

La existencia de la Task no autoriza por sí sola staging, commit, push,
ejecución de migración o modificación de datos.

## 28. Resultado consolidado

```text
Objeto delimitado: Sí
Clasificación: Antecedente individual
Ownership: beca.alumno
Propietario válido: Estudiante
Profesor CRUD propio: No
Admin/Comité CRUD global: Sí
Anónimo: Sin acceso
CRUD Beca actual: Incompleto/defectuoso
CRUD Beca objetivo: Integral
Catálogo compartido: nombre_beca
Estados objetivo: APROBADA | PENDIENTE | INACTIVA
Campos nuevos: estado_catalogo | propuesto_por
Propuesta y Beca: Transacción obligatoria
Unificación: Transacción obligatoria
DELETE Beca: Físico, sin borrar dependencias
Tipo permitido: 1 Interna | 2 Externa
Fecha: fech_in <= fech_ter
Legado tipo 0: Preservado e INACTIVO
Legado fecha invertida: Preservado; debe corregirse si se edita
Institución: Dependencia cerrada
Ficha: Consumer, no autoridad
ADR-003: No aplica
ADR nuevo: No
Migración: Requerida, no creada ni ejecutada
Decisiones pendientes: Ninguna bloqueante
Task integral: Creada y aprobada para implementación
Código modificado: No
Base de datos modificada: No
Staging/commit/push: No
```
