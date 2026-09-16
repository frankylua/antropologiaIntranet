# TASK-EPIC009-BECA-INTEGRAL-001

## Beca integral

## 1. Identificación y estado

- **Task:** TASK-EPIC009-BECA-INTEGRAL-001.
- **EPIC:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Coordinación:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia; EPIC-003 — Identidad y autorización.
- **AT fuente:** `AT-EPIC009-BECA-INTEGRAL-001`.
- **Objeto:** Beca.
- **Entidad persistente:** `beca`.
- **Catálogo gobernado:** `nombre_beca`.
- **Estado:** CERRADA / VF APROBADA.
- **Implementación:** COMPLETADA.
- **Revisión técnica:** COMPLETADA; validaciones estáticas correctas.
- **VF:** FUNCIONAL, VISUAL Y MENSAJES APROBADAS POR EL USUARIO.
- **Blockers actuales:** NINGUNO.
- **Fecha de creación:** 2026-09-12.

La implementación y sus correcciones de VF están cerradas. Los apartados de
planificación conservan el contrato original; el cierre de la sección 35
registra el estado final y las autorizaciones posteriores, incluido el ajuste
de cache busting en `form-doc/footer.php`. El AT se conserva como antecedente
aprobado, sin modificar su arquitectura.

## 2. Fuente arquitectónica obligatoria

La única fuente de decisiones de esta Task es:

- [AT-EPIC009-BECA-INTEGRAL-001](../architecture/AT-EPIC009-BECA-INTEGRAL-001.md).

El AT se encuentra formalmente `APROBADO`, con arquitectura, matriz
institucional, ownership, modelo de catálogo y evolución de datos aprobados;
alcance cerrado y ninguna decisión bloqueante.

Esta Task traduce ese contrato a una unidad implementable. No reconstruye
reglas desde el código heredado, no agrega decisiones institucionales y no
amplía la frontera.

## 3. Objetivo funcional

Implementar integralmente CREATE, READ, UPDATE y DELETE sobre `beca`, junto con
el gobierno mínimo de `nombre_beca`, incluyendo:

- autenticación en todas las operaciones;
- autorización backend por actor y operación;
- ownership derivado y comprobado en backend;
- protección frente a IDOR;
- CSRF en toda escritura;
- SQL parametrizado;
- validación del Estudiante objetivo, Institución, catálogo, tipo y fechas;
- UPDATE real del antecedente;
- DELETE físico exclusivo de la fila `beca`;
- propuesta inmediata de nuevos nombres;
- aprobación, normalización, unificación e inactivación del catálogo;
- transacciones en operaciones compuestas;
- respuestas HTTP/JSON coherentes;
- interpretación explícita de filas afectadas e ID insertado;
- compatibilidad con Ficha Estudiante propia, Admin y Comité;
- creación, pero no ejecución automática, de la migración aprobada.

Beca y catálogo forman una única Task porque comparten endpoint, modelo,
frontend, autorización, transacciones y evolución de datos.

## 4. Frontera funcional

Beca es un antecedente individual de Estudiante:

```text
Entidad: beca
PK: id_beca
Ownership: beca.alumno
Nombre/tipo: nombre_beca
Institución: dependencia cerrada
Ficha Académica: consumer
Profesor-Beca: no existe
Participaciones múltiples: no existen
ADR-003: no aplica
```

`nombre_beca` es un catálogo institucional compartido. Compartir un nombre no
transforma las filas `beca` en un recurso colectivo.

No forman parte de esta Task:

- relación Profesor-Beca;
- múltiples beneficiarios o participantes;
- aprobación de la fila `beca`;
- modernización integral de Ficha;
- cambios globales de identidad;
- cambios en Institución;
- saneamiento automático de fechas o catálogo históricos;
- cambios de Roadmap o ADR.

## 5. Matriz institucional obligatoria

| Actor efectivo | CREATE | READ | UPDATE | DELETE | Catálogo |
| --- | --- | --- | --- | --- | --- |
| Estudiante con perfil vigente | Sólo propio | Sólo propios | Sólo propio | Sólo propio | Consulta aprobadas y propone nombres |
| Profesor | Rechazado | Rechazado | Rechazado | Rechazado | Sin acceso funcional |
| Admin | Global sobre Estudiantes | Global sobre Estudiantes | Global sobre Estudiantes | Global sobre Estudiantes | Administración completa |
| Comité | Global sobre Estudiantes | Global sobre Estudiantes | Global sobre Estudiantes | Global sobre Estudiantes | Administración completa |
| Actor autenticado sin regla anterior | Rechazado | Rechazado | Rechazado | Rechazado | Rechazado |
| Anónimo | Rechazado | Rechazado | Rechazado | Rechazado | Rechazado |

El Estudiante no puede editar entradas existentes, aprobar, normalizar,
unificar, inactivar ni eliminar catálogo. Admin y Comité no pueden operar Beca
sobre un objetivo que el backend no valide como Estudiante.

El permiso histórico 3 no autoriza Beca. El permiso 5 no se consulta
directamente; se reutiliza el contrato público vigente de acceso al perfil.

## 6. Identidad y objetivo

Toda petición debe resolver primero una sesión autenticada y un actor efectivo.

Para operación propia:

```text
sesión autenticada
+ identidad Estudiante coherente
+ acceso vigente al perfil
→ id_usuario propietario derivado por backend
```

Para operación global:

```text
rol acumulativo admin o comite
+ usuario objetivo existente
+ especialización Estudiante válida
→ objetivo global autorizado
```

Se rechazan objetivos inexistentes, sin especialización Estudiante o
incompatibles con el dominio. La Task debe reutilizar los contratos vigentes de
sesión y `Authorization`; no puede modificar Login, Authorization ni crear una
capability nueva.

## 7. Ownership

La autoridad persistente exclusiva es:

```text
beca.alumno
```

CREATE propio deriva `alumno` desde sesión. READ lista propio filtra por ese
valor. READ detalle, UPDATE y DELETE propios deben combinar `id_beca` y
`alumno`, tanto en la autorización como en la consulta o escritura final.

El campo `usuario` enviado por cliente no constituye autoridad. Debe ignorarse
o rechazarse inequívocamente en operación propia. Para Admin/Comité puede
expresar un objetivo de CREATE/lista, sujeto a validación backend.

UPDATE no admite `alumno` entre sus campos editables. Admin y Comité tampoco
pueden reasignar una Beca mediante UPDATE. Si un payload global contiene
usuario, sólo puede comprobar coherencia con el propietario persistido.

## 8. Contrato CREATE de Beca

CREATE debe:

1. exigir autenticación y actor permitido;
2. resolver propietario propio o validar objetivo administrativo;
3. validar `nom_beca`, `inst_beca`, `fech_in` y `fech_ter`;
4. aceptar sólo catálogo utilizable por el actor;
5. procesar la alternativa de propuesta dentro del contrato seguro;
6. ejecutar INSERT parametrizado mediante `ejecutarEscritura()`;
7. comprobar `filasAfectadas === 1` e ID insertado positivo;
8. responder éxito sólo después de confirmar persistencia.

Una propuesta nueva y la Beca que la utiliza deben crearse dentro de una misma
transacción. No se crea una propuesta huérfana mediante un request independiente
del antecedente.

Admin y Comité pueden crear una Beca para un Estudiante objetivo válido usando
una entrada aprobada o el flujo administrativo autorizado. Profesor, anónimo y
Estudiante que intente elegir otro propietario son rechazados.

## 9. Contrato READ

Debe soportar:

- lista de cero, una o múltiples Becas;
- detalle por `id_beca` para edición;
- opciones de catálogo por tipo y visibilidad;
- listados administrativos por tipo y estado;
- información de impacto para normalización, unificación e inactivación.

El Estudiante sólo puede consultar filas propias. Admin y Comité pueden
consultar Becas de cualquier Estudiante válido. Profesor y anónimo no acceden.

Todas las consultas deben usar parámetros y columnas explícitas. Una lista
vacía devuelve éxito y `datos: []`. Un ID ajeno no puede revelar existencia,
propietario, catálogo, Institución o fechas.

Una entrada inactiva, o rechazada mediante la acción que desemboca en
`INACTIVA`, sigue siendo resoluble en los joins de una Beca histórica. Los
filtros de opciones nuevas no deben aplicarse a la proyección de antecedentes
ya persistidos.

## 10. Contrato UPDATE de Beca

Debe crearse UPDATE real sobre `beca`. Campos editables:

```text
nom_beca
inst_beca
fech_in
fech_ter
```

Campo inmutable:

```text
alumno
```

La operación valida ID, existencia, actor, ownership o facultad global,
catálogo utilizable, Institución y fechas. La sentencia propia se acota por
`id_beca` y `alumno`.

Debe usar `ejecutarEscritura()` y evaluar `filasAfectadas`. Si el payload válido
coincide con el estado persistido, puede responder `SIN_CAMBIOS`; no debe
afirmar una actualización inexistente. Cero filas por pérdida de ownership,
inexistencia o conflicto no es éxito.

Una Beca histórica con fechas invertidas sólo puede guardarse si el resultado
final completo cumple la regla vigente.

## 11. Contrato DELETE de Beca

DELETE es físico y elimina exclusivamente una fila `beca` autorizada. Debe:

1. exigir confirmación explícita en frontend;
2. exigir autenticación, autorización, ownership y CSRF;
3. validar `id_beca` y existencia;
4. acotar la escritura por propietario cuando corresponda;
5. ejecutar DELETE parametrizado con `ejecutarEscritura()`;
6. exigir exactamente una fila afectada;
7. refrescar el consumer sólo ante `ok=true`.

DELETE no elimina `usuario`, `institucion` ni `nombre_beca`. No utiliza los FK
`CASCADE` como operación funcional y no introduce soft delete. Un segundo
DELETE del mismo ID no puede informar un nuevo éxito.

## 12. Estados y visibilidad de `nombre_beca`

Estados persistentes únicos:

```text
APROBADA
PENDIENTE
INACTIVA
```

`RECHAZADA` es una acción/denominación operativa que finaliza en `INACTIVA`; no
es un cuarto valor persistente.

Reglas de selección:

- `APROBADA`: disponible generalmente según tipo;
- `PENDIENTE`: disponible sólo para su proponente cuando representa o crea su
  antecedente;
- `INACTIVA`: nunca seleccionable para una Beca nueva;
- `INACTIVA`/rechazada ya referenciada: siempre resoluble y visible en el
  antecedente histórico;
- ninguna transición puede causar pérdida de datos históricos.

Admin y Comité pueden listar los tres estados. El Estudiante no puede descubrir
propuestas pendientes de terceros.

## 13. Propuesta de nuevo nombre

El flujo “Agregar otra beca” debe:

1. recibir tipo Interna/Externa y nombre no vacío;
2. normalizar el valor para comparación sin reescribirlo silenciosamente;
3. buscar coincidencia por nombre y mismo tipo;
4. reutilizar una entrada `APROBADA` adecuada;
5. reutilizar una `PENDIENTE` del mismo Estudiante;
6. no exponer ni reutilizar como opción una pendiente de tercero;
7. crear `PENDIENTE` con `propuesto_por` igual al Estudiante autenticado cuando
   no existe entrada utilizable;
8. crear inmediatamente la Beca asociada;
9. confirmar ambas escrituras o revertir ambas.

La propuesta queda visible para Admin/Comité y utilizable por su creador sin
esperar aprobación administrativa. La Beca no recibe estado de aprobación.

## 14. Administración del catálogo

### 14.1 Crear valor oficial

Admin/Comité pueden crear una entrada `APROBADA`. Deben validar nombre, tipo y
coincidencias antes de insertar. El Estudiante no usa esta operación.

### 14.2 Aprobar

La transición autorizada es:

```text
PENDIENTE → APROBADA
```

Después de aprobar, la entrada aparece en la lista general de su tipo. Se
conserva `propuesto_por`.

### 14.3 Normalizar

Admin/Comité pueden corregir nombre y cambiar entre Interna/Externa. Antes de
confirmar, la UI debe mostrar la cantidad de Becas referenciadas. La operación
valida coincidencias y no crea tipos desconocidos.

### 14.4 Unificar

La operación exige origen y destino distintos y destino `APROBADA`. En una
transacción debe:

1. estabilizar las entradas involucradas;
2. reasignar `beca.nom_beca` desde origen hacia destino;
3. interpretar las filas afectadas;
4. comprobar que no quedan referencias que debían reasignarse;
5. marcar origen `INACTIVA`;
6. confirmar la transacción.

Ante cualquier error debe ejecutar rollback. La reasignación sucede antes de
inactivar el origen. No se permiten FK rotas, antecedentes ocultos ni DELETE
físico del origen.

### 14.5 Rechazar o inactivar

La operación persiste `INACTIVA`. Una entrada inactiva no aparece en nuevas
selecciones, pero si está referenciada permanece resoluble y visible dentro de
la Beca histórica. No se elimina aunque tenga cero referencias; se conserva la
trazabilidad con una semántica uniforme.

## 15. Tipo de Beca

`tipo_beca` representa únicamente:

```text
1 = Interna
2 = Externa
```

Backend y frontend deben rechazar `0`, enteros distintos y strings desconocidos
en nuevas operaciones. `tipo_beca` no representa estado, aprobación, rol ni
ownership.

La fila histórica vacía con tipo 0 se conserva como `INACTIVA`, no aparece en
opciones y no puede reutilizarse. Esta Task no corrige su nombre o tipo.

## 16. Migración obligatoria

La implementación debe crear exactamente:

```text
migrations/TASK-EPIC009-BECA-CATALOGO-001.sql
```

No debe ejecutarla automáticamente. Su aplicación requiere autorización
posterior y separada.

Estado final de `nombre_beca`:

| Campo nuevo | Contrato |
| --- | --- |
| `estado_catalogo` | `VARCHAR(10) NOT NULL`; `APROBADA`, `PENDIENTE` o `INACTIVA` |
| `propuesto_por` | `INT NULL`; FK a `usuario.id_usuario`, `ON UPDATE CASCADE`, `ON DELETE SET NULL` |

La migración debe:

1. realizar preflight sobre columnas, filas y referencias esperadas;
2. agregar `estado_catalogo` de forma compatible con backfill;
3. agregar `propuesto_por` nullable;
4. inicializar como `APROBADA` las filas históricas con nombre no vacío y tipo
   1/2;
5. inicializar como `INACTIVA` la fila histórica vacía tipo 0;
6. dejar `propuesto_por=NULL` para filas históricas;
7. preservar todos los IDs, nombres, tipos y referencias existentes;
8. completar `NOT NULL` y el dominio del estado;
9. crear índice `(tipo_beca, estado_catalogo)` e índice/FK de `propuesto_por`;
10. verificar cardinalidad y ausencia de referencias rotas.

No debe modificar `beca`, corregir la fecha histórica invertida, borrar la fila
tipo 0 ni convertir tipos existentes. No debe suponer atomicidad transaccional
de DDL si MariaDB no la garantiza; debe ordenar pasos y validaciones para una
aplicación controlada.

No se agrega `CHECK` global sobre `tipo_beca` mientras exista la excepción tipo
0. La prohibición de nuevos valores inválidos se aplica en el backend.

## 17. Fechas

`fech_in` y `fech_ter` son obligatorias. CREATE y UPDATE deben comprobar en
backend:

```text
formato exacto YYYY-MM-DD
fecha civil existente
fech_in <= fech_ter
```

El frontend repite la regla para UX, pero no constituye autoridad.

La fila histórica con inicio `2026-03-26` y término `2026-03-12` se preserva y
continúa visible. La migración no la modifica. Si se edita, el resultado final
debe quedar cronológicamente válido.

## 18. Institución

`inst_beca` debe ser entero positivo y referenciar una fila existente de
`institucion`. La validación se realiza antes de escribir.

Institución es una dependencia cerrada. Quedan protegidos:

```text
src/Model/Institucion.php
ajax/institucion.php
form-doc/scripts/usuario.js
```

La creación contextual puede continuar como request separado y no se incorpora
a la transacción de propuesta+Beca. Su deuda de atomicidad no se resuelve desde
esta Task.

## 19. Modelo

`src/Model/Beca.php` debe concentrar persistencia parametrizada y exponer
contratos explícitos para:

- crear Beca;
- listar por propietario autorizado;
- obtener detalle;
- actualizar sin modificar `alumno`;
- eliminar físicamente;
- listar opciones de catálogo por actor/tipo/estado;
- buscar coincidencias normalizadas;
- crear propuesta y Beca transaccionalmente;
- crear/editar/aprobar/inactivar catálogo;
- contar referencias e impacto;
- unificar y reasignar transaccionalmente.

No se exige conservar firmas defectuosas si sus callers autorizados se actualizan
en esta misma Task. No puede quedar ningún camino activo con SQL interpolado.

Toda escritura usa `ejecutarEscritura()` e interpreta su arreglo. Las
transacciones deben usar la misma conexión PDO singleton. Los métodos retornan
datos suficientes para que el endpoint diferencie éxito, sin cambios,
inexistencia y conflicto, sin exponer objetos `PDOStatement` como contrato HTTP.

## 20. Endpoint

`ajax/beca.php` debe:

1. iniciar/reutilizar la sesión mediante el bootstrap vigente;
2. fijar JSON como content type;
3. validar método y operación;
4. autenticar antes de consultar el modelo;
5. resolver actor y objetivo;
6. aplicar ownership o facultad global por operación;
7. exigir CSRF en escrituras;
8. validar y tipar el payload;
9. invocar contratos parametrizados;
10. mapear resultados a HTTP/JSON;
11. capturar errores sin revelar SQL o excepciones.

Operaciones funcionales mínimas:

```text
Beca: create | list | detail | update | delete
Catálogo público autorizado: options
Catálogo administrativo: catalog-list | catalog-create | catalog-update
                         | catalog-approve | catalog-unify | catalog-inactivate
```

La propuesta forma parte de `create`/`update` cuando el payload indica un nuevo
nombre; no debe quedar una operación anónima o aislada que cree propuestas
huérfanas.

Los aliases legacy `insert`, `insert-beca`, `insert-update`, `read`,
`read-nombre` y `read_lista` deben retirarse o pasar por las mismas guards y
contratos. No puede sobrevivir una ruta legacy sin autorización.

## 21. CSRF

Requieren `X-CSRF-Token` válido ligado a sesión:

- CREATE, UPDATE y DELETE Beca;
- propuesta de catálogo;
- creación y edición oficial;
- aprobación;
- normalización;
- unificación;
- inactivación/rechazo.

El token debe tener entropía criptográfica y compararse mediante `hash_equals`.
La ausencia o invalidez responde rechazo antes de toda escritura.

`form-doc/agr.form.beca.php` proporciona el token a la UI de Ficha y
`admin/act.list.php` a la administración del catálogo. READ requiere
autenticación/autorización, pero no CSRF.

## 22. HTTP y JSON

Content type obligatorio:

```text
application/json; charset=utf-8
```

Éxito mínimo:

```json
{
  "ok": true,
  "codigo": "BECA_ACTUALIZADA",
  "mensaje": "Beca actualizada correctamente.",
  "datos": {}
}
```

Error mínimo:

```json
{
  "ok": false,
  "error": "DATOS_INVALIDOS",
  "mensaje": "Los datos de la beca no son válidos."
}
```

| HTTP | Uso mínimo |
| --- | --- |
| 200/201 | Lectura o escritura confirmada |
| 400 | Operación, payload, ID o transición mal formados |
| 401 | Sesión no autenticada |
| 403 | Actor, ownership o CSRF rechazado |
| 404 | Recurso autorizado inexistente |
| 409 | Conflicto de estado, coincidencia o unificación |
| 422 | Nombre, tipo, Institución o fecha inválidos |
| 500 | Error interno no expuesto |

No se devuelven strings ambiguos, SQL, stack traces ni excepciones crudas. Las
listas exitosas devuelven `datos: []` cuando estén vacías.

## 23. Frontend Estudiante

`form-doc/agr.form.beca.php` y `form-doc/scripts/beca.js` deben proporcionar:

- token CSRF de Beca;
- tipo Interna/Externa;
- lista segura de nombres filtrada por tipo;
- alternativa “Agregar otra beca”;
- propuesta con uso inmediato;
- Institución y fechas;
- CREATE, detalle, UPDATE y confirmación DELETE;
- mensajes derivados del contrato JSON;
- estado vacío y múltiples tarjetas;
- refresh sin duplicados;
- escape de salida para catálogo e Institución.

Debe eliminarse el uso Beca de `async:false`, `JSON.parse` manual, globals
implícitas y logs de payloads/respuestas. Los callbacks deben manejar respuestas
2xx y errores 4xx/5xx.

El usuario presente en DOM no constituye autoridad. La UI administrativa puede
enviar contexto de objetivo, pero el endpoint lo valida.

## 24. Administración “Editar listas”

`admin/act.list.php` y `admin/scripts/listas.js` deben implementar el flujo
específico de Beca dentro de la sección existente:

- emitir/usar CSRF;
- listar por Interna/Externa y estado;
- mostrar proponente e impacto;
- crear valor aprobado;
- editar/normalizar;
- aprobar;
- seleccionar destino y unificar;
- rechazar/inactivar;
- confirmar acciones globales;
- mostrar errores HTTP/JSON.

No debe trasladarse esta administración a Ficha Estudiante. No se modifica
`js/funcAjax.js`; los otros catálogos deben conservar su comportamiento.

## 25. Consumers

`form-doc/scripts/ficha.estudiante.js` debe cargar Beca al entrar al bloque
académico propio sin duplicar resultados.

`admin/scripts/ver.estudiante.js` debe cargar y operar Beca del Estudiante
seleccionado para Admin/Comité, conservando la validación backend del objetivo.

La proyección existente en `form-doc/scripts/info.estudiante.js` debe conservarse
compatible sin modificar ese archivo. Ficha Docente no debe incorporar Beca.

Ficha continúa siendo consumer y no se moderniza como objeto integral dentro de
esta Task.

## 26. Seguridad obligatoria

### H1

- acceso anónimo cerrado;
- IDOR cerrado en lista, detalle, UPDATE y DELETE;
- propietario derivado en backend;
- mutación ajena bloqueada;
- SQL parametrizado en todo camino activo.

### H2

- CSRF en toda escritura;
- autorización por actor y operación;
- pendientes de terceros no expuestas;
- salida escapada frente a XSS;
- errores sin filtración de datos.

### H3

- tipo 0 bloqueado para nuevas operaciones;
- fechas, nombres y referencias validados;
- filas afectadas/ID interpretados;
- propuesta y unificación transaccionales;
- inactivación sin pérdida histórica;
- contratos JSON uniformes.

### H4

- sin requests síncronas, parseo manual, globals ni logs;
- estado vacío y múltiples registros correctos;
- refresh sin duplicados;
- confirmaciones e impacto visibles;
- consumers administrativos completos.

## 27. Archivos modificables definitivos

Únicamente:

```text
1. src/Model/Beca.php
2. ajax/beca.php
3. form-doc/scripts/beca.js
4. form-doc/agr.form.beca.php
5. form-doc/scripts/ficha.estudiante.js
6. admin/scripts/ver.estudiante.js
7. admin/scripts/listas.js
8. admin/act.list.php
9. migrations/TASK-EPIC009-BECA-CATALOGO-001.sql
```

Si la implementación necesita un décimo archivo, debe detenerse antes de
modificarlo. El archivo 9 debe crearse durante la implementación, pero no
ejecutarse sin autorización posterior.

## 28. Archivos y áreas protegidos

Quedan protegidos, entre otros:

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

También se protegen Login, Authorization, Institución, Grado, Postdoctorado,
Ficha como objeto integral, Docente, ADR-003, Roadmap, reportes generales,
Tasks/AT ajenos y cambios locales preexistentes.

## 29. Secuencia de implementación autorizada

1. verificar rama, HEAD, estado, staging y ausencia de conflictos locales;
2. confirmar AT aprobado y los nueve paths autorizados;
3. inspeccionar el esquema en modo lectura y validar precondiciones de migración;
4. crear el archivo SQL sin ejecutarlo;
5. implementar persistencia parametrizada y transacciones en el modelo;
6. asegurar sesión, autorización, ownership, CSRF y HTTP/JSON en el endpoint;
7. implementar CRUD y propuesta en frontend Estudiante;
8. incorporar Beca en los dos callers autorizados;
9. implementar gobierno de catálogo en “Editar listas”;
10. ejecutar revisión estática y técnica sin aplicar la migración;
11. solicitar autorización separada para aplicar la migración;
12. con schema compatible, completar pruebas técnicas y entregar VF al usuario;
13. cerrar sólo después de revisión técnica y VF aprobadas.

No se autoriza usar el código dependiente de columnas nuevas en producción
antes de aplicar la migración mediante el procedimiento aprobado.

## 30. Validación técnica posterior a la implementación

Como mínimo:

```text
php -l src/Model/Beca.php
php -l ajax/beca.php
php -l form-doc/agr.form.beca.php
php -l admin/act.list.php
node --check form-doc/scripts/beca.js
node --check form-doc/scripts/ficha.estudiante.js
node --check admin/scripts/ver.estudiante.js
node --check admin/scripts/listas.js
git diff --check
git diff --cached --name-only
git status --short
```

Si Node no está disponible, debe registrarse esa limitación y efectuarse una
revisión sintáctica equivalente sin instalar dependencias no autorizadas.

La revisión debe demostrar:

- exactamente nueve archivos funcionales/migración como máximo;
- ningún archivo protegido modificado;
- ningún SQL externo interpolado;
- ninguna operación sin guard;
- CSRF en toda escritura;
- ownership presente en backend y SQL propio;
- transacciones con rollback;
- migration creada pero no ejecutada;
- datos y fecha histórica intactos;
- staging vacío antes de autorización posterior.

## 31. Validación funcional del usuario

Responsable exclusivo de la VF: **USUARIO**.

### 31.1 Estudiante

- CRUD propio con perfil vigente;
- CRUD ajeno bloqueado;
- Interna y Externa;
- cero, una y múltiples Becas;
- propuesta nueva y uso inmediato;
- coincidencia aprobada reutilizada;
- propuesta pendiente propia visible;
- propuesta pendiente ajena no visible;
- inactiva histórica visible sólo en antecedente ya relacionado;
- propietario manipulado rechazado o ignorado.

### 31.2 Admin y Comité

- CRUD global sobre Estudiantes válidos;
- Profesor/objetivo inválido rechazado;
- crear y editar catálogo;
- aprobar propuesta;
- normalizar nombre/tipo mostrando impacto;
- unificar y verificar reasignación total;
- rechazar/inactivar sin ocultar antecedentes;
- consultar aprobadas, pendientes e inactivas.

### 31.3 Negativos

- anónimo;
- Profesor;
- permiso 3 aislado;
- usuario/ID manipulado;
- CSRF ausente, inválido o de otra sesión;
- SQL injection y XSS;
- tipo 0 o desconocido;
- nombre vacío/excesivo;
- Institución inexistente;
- fechas mal formadas, imposibles o invertidas;
- operación o método inválido.

### 31.4 Datos y transacciones

- propuesta duplicada;
- propuesta ya referenciada;
- rollback de propuesta+Beca;
- rollback de unificación;
- destino de unificación inválido;
- fila histórica invertida preservada y corregida sólo al editar;
- fila tipo 0 preservada como inactiva;
- filas afectadas cero y operación repetida sin falso éxito.

### 31.5 Consumers y respuestas

- Ficha Estudiante propia;
- vista Admin;
- vista Comité;
- ausencia en Ficha Docente;
- “Editar listas” sin regresión de otros catálogos;
- estado vacío y refresh sin duplicados;
- códigos HTTP, content type y objetos JSON coherentes;
- errores sin SQL, trazas ni datos de terceros.

## 32. Criterios de aceptación

La implementación sólo puede declararse técnicamente completada cuando:

1. los cuatro contratos CRUD de `beca` operen según matriz;
2. no exista acceso anónimo ni IDOR;
3. `alumno` se derive/valide en backend y sea inmutable;
4. Profesor y permiso 3 queden bloqueados;
5. todo SQL activo esté parametrizado;
6. toda escritura exija CSRF;
7. propuesta+Beca y unificación sean atómicas;
8. el catálogo respete estados y visibilidad;
9. una inactiva/rechazada referenciada siga visible históricamente;
10. unificación reasigne antes de inactivar y preserve FK;
11. tipos nuevos se limiten a 1/2;
12. fechas cumplan el contrato hacia adelante;
13. la migración preserve filas e IDs y no cambie `beca`;
14. los consumers autorizados funcionen con cero/uno/múltiples registros;
15. HTTP/JSON y filas afectadas sean honestos;
16. no se modifique un décimo archivo ni superficie protegida;
17. revisión técnica resulte aprobada;
18. VF del usuario resulte aprobada.

La implementación y la VF aprobadas quedan registradas en el cierre de la
sección 35.

## 33. Reversión

Los cambios de aplicación deben ser reversibles dentro de los ocho archivos de
código/vista autorizados.

La reversión de schema requiere tratamiento especial:

- antes de existir propuestas nuevas, puede diseñarse rollback controlado de
  columnas/FK/índices;
- después de almacenar estados o proponentes, no se permite eliminar columnas
  sin preservar previamente esa información;
- ninguna reversión puede borrar o romper Becas históricas;
- la migración no se ejecuta automáticamente durante la Task.

La estrategia exacta de aplicación y reversión debe revisarse antes de autorizar
la ejecución SQL.

## 34. Condiciones obligatorias de detención

La implementación debe detenerse sin ampliar alcance si:

1. el AT deja de estar aprobado;
2. la migración requiere más campos que `estado_catalogo` y `propuesto_por`;
3. se necesita un décimo archivo;
4. se necesita cambiar Login o Authorization;
5. se requiere modificar Institución;
6. se requiere modificar Docente;
7. `js/funcAjax.js` resulta imprescindible;
8. la unificación no puede implementarse transaccionalmente;
9. el esquema actual contradice la migración aprobada;
10. aparecen cambios locales ajenos incompatibles.

También debe detenerse si no pueden preservarse IDs, FK o filas históricas. La
detención obliga a volver al AT; no autoriza una solución alternativa implícita.

## 35. Gobierno y cierre

```text
[ARQ] antecedente individual + catálogo gobernado
[AUTH] ownership y matriz por actor
[DATA] evolución mínima de nombre_beca
[SEC] autorización, IDOR, CSRF y SQL
[GOV] autonomía del Estudiante con normalización institucional
```

ADR nuevo: no.

ADR-003: no aplica.

### Estado

Cerrada / VF aprobada. Cierre técnico/documental autorizado por el usuario.

### Implementación final

- CRUD propio de Estudiante y CRUD global Admin/Comité.
- Catálogo `nombre_beca` con estados APROBADA/PENDIENTE/INACTIVA y propuestas
  de estudiantes; propuesta y Beca se crean transaccionalmente.
- Ownership comprobado en backend, `alumno` inmutable y fechas civiles
  validadas con inicio menor o igual a término.
- Administración del catálogo con aprobación, normalización, unificación
  transaccional e inactivación que preserva antecedentes históricos.
- Migración ejecutada y validada.

### Correcciones derivadas de VF

- Cache busting de `beca.js` mediante `filemtime` en `form-doc/footer.php`.
- Eliminación del formulario anidado: `form_beca` conserva su ID como `div`.
- Cards alineadas con el patrón de Grado/Postdoctorado.
- Éxitos de create/update temporales según Grado: 3 segundos y
  `fadeOut(1500)`, con cancelación de timers anteriores.

### Migración

`migrations/TASK-EPIC009-BECA-CATALOGO-001.sql`: ejecutada y validada según
confirmación de cierre del usuario. Resultado post-migración conocido:
9 filas, 8 APROBADA, 1 INACTIVA y 0 proponentes históricos.
No se ejecutó SQL durante este cierre.

### Validación

- Validaciones estáticas correctas: PHP sin errores de sintaxis, compilación
  sintáctica de los cuatro JavaScript sin ejecutarlos y `git diff --check`.
  Node no disponible; se utilizó el motor JavaScript de las herramientas.
- VF funcional, VF visual y VF de mensajes aprobadas por el usuario.
- Revisión final sin diagnósticos ni logs de depuración agregados.
- Blockers: ninguno.
- Staging autorizado sólo para los doce archivos de Beca indicados en el
  cierre; ADR-003 y PDF local excluidos. Commit y push no autorizados.
