# TASK-EPIC009-POSTDOCTORADO-INTEGRAL-001

## Postdoctorado integral

## 1. Identificación y estado

- **Task:** TASK-EPIC009-POSTDOCTORADO-INTEGRAL-001.
- **EPIC:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **AT fuente:** `AT-EPIC009-POSTDOCTORADO-INTEGRAL-001`.
- **Objeto:** Postdoctorado.
- **Entidad persistente:** `postdoctorado`.
- **Estado:** CERRADA.
- **Implementación:** COMPLETADA.
- **Revisión técnica:** APROBADA.
- **VF:** GENERAL Y VALIDACIÓN DIRIGIDA DE CACHE-BUSTING APROBADAS POR EL USUARIO.
- **Blockers pendientes:** NINGUNO.
- **Fecha de creación:** 2026-09-12.

Esta Task está implementada, revisada y cerrada. La VF general y la validación
dirigida mediante recarga normal del cache-busting incorporado después del
bloqueo `CSRF_INVALIDO` fueron aprobadas por el usuario.

## 2. Fuente arquitectónica obligatoria

La única fuente de decisiones de esta Task es:

- [AT-EPIC009-POSTDOCTORADO-INTEGRAL-001](../architecture/AT-EPIC009-POSTDOCTORADO-INTEGRAL-001.md).

El AT queda en estado `APROBADO`, `IMPLEMENTADO`, `VF APROBADA` y `CERRADO`, con
arquitectura y matriz institucional aprobadas, ownership aprobado, alcance
cerrado y ninguna decisión institucional pendiente.

Esta Task traduce ese contrato arquitectónico a una unidad de implementación.
No reconstruye reglas desde el comportamiento heredado, no agrega decisiones
institucionales y no amplía su frontera.

### 2.1 Ampliación operativa autorizada durante VF

Durante VF, el navegador conservó una versión legacy de
`form-doc/scripts/postdoctorado.js` que no enviaba `X-CSRF-Token`; el endpoint
respondió correctamente HTTP 403 con `CSRF_INVALIDO`. La VF general fue
aprobada por el usuario después de cargar el JavaScript vigente.

Para impedir la recurrencia se autorizó y modificó un quinto archivo funcional:

5. `form-doc/footer.php`.

La solución aplicada incorpora cache-busting automático mediante `filemtime()`
exclusivamente a la URL de `postdoctorado.js`. Es una corrección operativa de
despliegue, no arquitectónica: no modifica CSRF, autorización, ownership,
consumers, BD ni schema.

Esta excepción deja sin efecto únicamente las referencias posteriores al
límite de cuatro archivos, a la prohibición de un quinto archivo y a
`form-doc/footer.php` como archivo protegido, sólo para este cache-busting. No
autoriza ningún sexto archivo ni otra modificación del footer.

El versionado fue comprobado mediante recarga normal, sin `Ctrl+F5` ni caché
deshabilitada, y una escritura válida sin `CSRF_INVALIDO`. La validación
dirigida fue aprobada por el usuario.

## 3. Objetivo funcional

Cerrar integralmente CREATE, READ, UPDATE y DELETE del objeto persistente
`postdoctorado`, incluyendo:

- autenticación en todas las operaciones;
- autorización backend por actor y operación;
- ownership persistente;
- protección frente a IDOR;
- CSRF en CREATE, UPDATE y DELETE;
- SQL completamente parametrizado;
- validación del usuario objetivo;
- validación de Institución;
- validación temporal estricta;
- respuestas HTTP/JSON coherentes;
- interpretación explícita de filas afectadas e ID insertado;
- DELETE físico;
- compatibilidad con Ficha Estudiante y Ficha Docente;
- operación global de Admin y Comité.

Los cuatro contratos CRUD forman una sola unidad porque comparten identidad,
autorización, ownership, persistencia, frontend y consumers.

## 4. Frontera funcional

Postdoctorado es un antecedente académico individual y una entidad autoridad
independiente.

```text
Entidad: postdoctorado
PK: id_postdoc
Ownership: postdoctorado.usuario
Institución: dependencia cerrada
Ficha Académica: consumer
ADR-003: no aplicable funcionalmente
```

No existe workflow de aprobación administrativa. CREATE y UPDATE exitosos
quedan vigentes inmediatamente. DELETE elimina físicamente sólo la fila de
Postdoctorado autorizada.

No forman parte de esta Task:

- una modernización integral de Ficha Académica;
- cambios de identidad global, Login o Authorization;
- cambios en Institución;
- participación compartida o ownership colectivo;
- saneamiento masivo de datos históricos;
- cambios de schema o migraciones.

## 5. Matriz institucional obligatoria

| Actor efectivo | CREATE | READ | UPDATE | DELETE |
| --- | --- | --- | --- | --- |
| Profesor con acceso vigente a su perfil | Sólo propio | Sólo propios | Sólo propio | Sólo propio |
| Estudiante con acceso vigente a su perfil | Sólo propio | Sólo propios | Sólo propio | Sólo propio |
| Admin | Global sobre Profesores y Estudiantes | Global sobre Profesores y Estudiantes | Global sobre Profesores y Estudiantes | Global sobre Profesores y Estudiantes |
| Comité | Global sobre Profesores y Estudiantes | Global sobre Profesores y Estudiantes | Global sobre Profesores y Estudiantes | Global sobre Profesores y Estudiantes |
| Profesor/Estudiante que acumula Admin o Comité | Prevalecen las facultades globales | Prevalecen las facultades globales | Prevalecen las facultades globales | Prevalecen las facultades globales |
| Anónimo | Rechazado | Rechazado | Rechazado | Rechazado |

Sin Admin o Comité, Profesor y Estudiante no pueden consultar ni mutar
Postdoctorados ajenos. La visibilidad de botones no sustituye la autorización
backend.

Para Estudiante se utiliza el contrato vigente `perfil.ver` cuando corresponda.
El permiso histórico 5 no puede consultarse directamente como autoridad.

El permiso histórico 3 o `aceptado` aislado tampoco autorizan operaciones de
Postdoctorado.

## 6. Identidad objetivo

Todo usuario objetivo debe:

1. ser un entero positivo cuando sea indicado explícitamente;
2. existir;
3. poseer exactamente una especialización válida.

```text
usuario válido
→ Profesor XOR Estudiante
```

Se rechaza:

- Profesor y Estudiante simultáneamente;
- usuario sin ninguna especialización;
- usuario inexistente.

La regla aplica a objetivos globales de Admin/Comité y a la coherencia de la
identidad propietaria. Debe resolverse con los contratos vigentes, sin modificar
`ajax/login.php`, `src/Model/Login.php` ni
`src/Security/Authorization.php` y sin crear una capability nueva.

## 7. Ownership

La autoridad persistente exclusiva es:

```text
postdoctorado.usuario
```

### 7.1 Operación del propietario

CREATE:

- deriva el propietario desde la sesión;
- no admite `usuario` del cliente como autoridad;
- no permite crear un Postdoctorado para otra persona.

READ lista:

- filtra por el usuario autenticado y autorizado;
- devuelve únicamente registros propios.

READ por ID:

- valida `id_postdoc`;
- resuelve existencia;
- comprueba ownership antes de responder;
- no revela datos de una fila ajena.

UPDATE:

```text
id_postdoc
→ existencia
→ ownership
→ validaciones
→ UPDATE autorizado
```

- conserva `postdoctorado.usuario`;
- no permite reasignación desde el request;
- acota la escritura por ID y propietario.

DELETE:

```text
id_postdoc
→ existencia
→ ownership
→ DELETE físico autorizado
```

- elimina únicamente una fila propia;
- acota la sentencia por ID y propietario.

### 7.2 Operación global

Admin y Comité pueden indicar un usuario objetivo explícito para CREATE y READ
lista. El backend valida entero positivo, existencia y Profesor XOR Estudiante.

Para detalle, UPDATE y DELETE, el propietario real se obtiene desde la fila
persistida. Si también se recibe `usuario`, sólo puede comprobar consistencia;
no reemplaza al ownership ni permite reasignar propietario.

## 8. Contrato CREATE

CREATE debe:

1. autenticar al actor;
2. autorizar la operación;
3. resolver propietario propio o usuario objetivo global válido;
4. validar Institución, `prof`, `fecha_inicio` y `fecha_termino`;
5. ejecutar INSERT parametrizado mediante `ejecutarEscritura()`;
6. interpretar `filasAfectadas` e `idInsertado`;
7. exigir exactamente una fila afectada y un ID insertado válido;
8. responder HTTP 201 y `ok=true` sólo con inserción confirmada.

El registro creado queda vigente inmediatamente. No se genera estado de
aprobación.

## 9. Contrato READ

READ debe preservar:

- lista de cero, uno o múltiples Postdoctorados;
- detalle de un Postdoctorado para edición;
- resultados asociativos compatibles con los consumers.

Campos mínimos del contrato:

- `id_postdoc`;
- `id_inst`;
- `prof`;
- `inst`;
- `fecha_inicio`;
- `fecha_termino`;
- cualquier otro campo que el contrato aprobado requiera realmente sin exponer
  ownership innecesariamente.

Las consultas deben ser parametrizadas y retornar datos bajo
`PDO::FETCH_ASSOC`. Una lista vacía es un resultado válido con `datos: []`. Un
detalle inexistente o ajeno se resuelve de forma segura antes de entregar
contenido.

READ no requiere CSRF, pero siempre requiere autenticación, autorización y
ownership u objetivo global válido.

## 10. Contrato UPDATE

UPDATE debe:

1. autenticar y autorizar al actor;
2. validar `id_postdoc` como entero positivo;
3. resolver existencia y propietario;
4. aplicar ownership o facultad global;
5. validar Institución, `prof` y ambas fechas;
6. conservar el propietario persistido;
7. ejecutar SQL parametrizado mediante `ejecutarEscritura()`;
8. interpretar `filasAfectadas` explícitamente;
9. distinguir inexistente, ajeno, sin cambios, conflicto y error.

Un payload válido idéntico a la fila puede responder éxito honesto con código
`POSTDOCTORADO_SIN_CAMBIOS` o equivalente y `cambios=false`. No debe afirmar que
la fila fue modificada.

Cero filas por inexistencia, pérdida de ownership o conflicto no puede tratarse
como actualización exitosa.

## 11. Contrato DELETE

DELETE permanece físico y debe:

1. solicitar confirmación explícita en frontend;
2. no emitir request si el usuario cancela;
3. autenticar y autorizar al actor;
4. exigir CSRF válido;
5. validar `id_postdoc`, existencia y ownership aplicable;
6. ejecutar DELETE parametrizado y acotado;
7. utilizar `ejecutarEscritura()`, no `ejecutarConsulta()`;
8. exigir exactamente una fila afectada;
9. responder éxito sólo tras confirmar la eliminación;
10. refrescar la sección sólo ante `ok=true`.

Cero filas no equivale a DELETE exitoso. Un segundo DELETE sobre el mismo ID no
puede volver a informar éxito.

DELETE no elimina Institución, Usuario, Estudiante, Profesor ni otros
antecedentes de Ficha. No introduce soft delete ni modifica FK/CASCADE.

## 12. Validación temporal

Campos obligatorios:

- `fecha_inicio`;
- `fecha_termino`.

Para CREATE y UPDATE, cada fecha debe:

- tener formato exacto `YYYY-MM-DD`;
- representar una fecha civil real;
- conservar exactamente el valor validado.

El intervalo debe cumplir:

```text
fecha_inicio <= fecha_termino
```

Inicio igual a término es válido.

Se rechazan valores vacíos, texto fuera del formato, fechas imposibles, payloads
SQL e inicio posterior a término. La validación frontend no sustituye la
validación autoritativa del endpoint y/o modelo.

## 13. Fila histórica invertida

Existe una fila histórica preexistente con:

```text
fecha_inicio > fecha_termino
```

La Task no debe:

- modificarla automáticamente;
- crear una migración;
- sanearla durante READ;
- realizar una escritura oportunista sobre ella.

Si esa fila se edita expresamente, el estado final completo debe cumplir el
contrato temporal antes de persistirse. Editar otro campo no permite conservar
el intervalo inválido.

La anomalía permanece registrada como deuda/dato histórico preexistente y no
bloquea el CRUD.

## 14. Validación de Institución

`inst_postdoc` debe ser un entero positivo y referenciar una Institución
existente. Un ID inexistente genera una respuesta controlada sin escritura.

Se preservan:

- `id_inst`;
- `inst`;
- lista asociativa vigente;
- alta contextual vigente;
- FK actual.

No se modifican `src/Model/Institucion.php` ni `ajax/institucion.php`.

## 15. Deuda multi-request

La secuencia:

```text
crear Institución
→ crear o editar Postdoctorado
```

puede abarcar múltiples requests y no constituye una transacción única. Puede
quedar una Institución creada si luego falla Postdoctorado.

Clasificación:

```text
DEUDA TÉCNICA EXISTENTE / NO BLOCKER
```

La Task no fusiona endpoints, no implementa una transacción distribuida, no
crea compensaciones automáticas y no reabre Institución.

## 16. Modelo

Archivo:

```text
src/Model/Postdoctorado.php
```

Revisar integralmente:

- `insertar()`;
- `mostrar()`;
- `mostrarPostdoc()`;
- `editarPostdoc()`;
- `eliminar()`;
- sólo helpers internos estrictamente necesarios dentro del mismo archivo.

Todas las consultas con input externo deben parametrizarse. No se permite
interpolar `usuario`, `id_postdoc`, `inst_postdoc`, `prof`, `fecha_inicio` o
`fecha_termino` antes de llamar `prepare()`.

INSERT, UPDATE y DELETE utilizan `ejecutarEscritura()`. Sus arrays estructurados
no se convierten directamente en booleanos de éxito.

## 17. Endpoint

Archivo:

```text
ajax/postdoctorado.php
```

Debe convertirse en autoridad operacional real. Antes de cada operación
resuelve:

- sesión y autenticación;
- actor efectivo;
- autorización;
- usuario objetivo cuando corresponda;
- ownership;
- IDs y demás inputs;
- Institución;
- fechas;
- CSRF para escrituras.

No depende de la página desde la cual se invoca.

Se preservan las operaciones públicas legacy:

- `insert`;
- `update`;
- `read_postdoc_id`;
- `read`;
- `delete`.

Puede adaptarse internamente su contrato de respuesta al formato normalizado. No
se crea un segundo endpoint. Si fuera indispensable hacerlo, la implementación
se detiene.

## 18. HTTP y JSON

Respuesta conceptual de éxito:

```json
{
  "ok": true,
  "datos": {},
  "mensaje": "Operación completada correctamente."
}
```

Respuesta conceptual de error:

```json
{
  "ok": false,
  "error": "CODIGO_ESTABLE",
  "mensaje": "Descripción segura para el usuario."
}
```

Usar status coherentes según el caso:

- 200 para lectura, actualización, eliminación o resultado sin cambios
  exitosos;
- 201 para creación confirmada;
- 400 para operación, ID o payload mal formado;
- 401 para actor no autenticado;
- 403 para actor no autorizado, recurso ajeno o CSRF inválido;
- 404 para recurso u objetivo no encontrado;
- 409 para conflicto, integridad o escritura no confirmada;
- 422 para validación semántica, incluida fecha inválida;
- 500 para error interno no clasificable.

No exponer SQL, stack, rutas, credenciales, parámetros internos ni detalles de
excepciones/PDO.

## 19. CSRF

CSRF es obligatorio en CREATE, UPDATE y DELETE. READ lista y detalle no lo
requieren.

El wiring permitido se limita a:

- emisión/contexto: `form-doc/agr.form.dat.acad.php`;
- transporte frontend: `form-doc/scripts/postdoctorado.js`;
- validación backend: `ajax/postdoctorado.php`.

Se reutiliza el patrón consolidado en Grado dentro de los cuatro archivos del
alcance funcional base. Token ausente, mal formado o inválido se rechaza antes
de cualquier escritura.

La ampliación posterior a `form-doc/footer.php` no altera CSRF: únicamente
garantiza que el navegador solicite el JavaScript vigente que transporta el
token.

## 20. Frontend

Archivo:

```text
form-doc/scripts/postdoctorado.js
```

Adaptar CREATE, READ, UPDATE y DELETE para:

- consumir HTTP/JSON normalizado;
- enviar CSRF en escrituras;
- no usar `usuario` del DOM como autoridad propia;
- conservar el usuario solicitado sólo como objetivo global no autoritativo;
- manejar cero, uno y múltiples registros;
- mostrar errores reales;
- evitar falsos éxitos;
- confirmar DELETE;
- cancelar DELETE sin request;
- refrescar la sección correcta sólo después de éxito confirmado;
- reemplazar contenido donde corresponda para evitar tarjetas duplicadas;
- manejar detalle inexistente sin asumir `postdoc[0]`;
- preservar el contrato asociativo;
- retirar logs relacionados con Postdoctorado en las rutas intervenidas.

Corregir dentro de este alcance los defectos confirmados de mensajes tratados
como éxito, refresh posterior a CREATE, refresh Estudiante basado en
`#info_doc`, duplicación de tarjetas, detalle inexistente y logs. No rediseñar la
interfaz ni refactorizar Ficha.

## 21. Formulario compartido

Archivo:

```text
form-doc/agr.form.dat.acad.php
```

Sólo admite wiring mínimo para:

- token CSRF de Postdoctorado;
- contexto propio/global/sin acceso;
- visibilidad UX de acciones;
- datos mínimos requeridos por el transporte seguro.

El frontend no es autoridad. La autorización completa permanece en el endpoint.

## 22. Consumers y compatibilidad

Preservar sin modificar sus callers:

- Ficha Estudiante propia;
- Ficha Docente propia;
- vista Admin sobre Estudiantes y Profesores;
- vista Comité sobre Estudiantes y Profesores.

La interfaz pública consumida, incluida `cargarPostdoc(usuario, id)`, debe seguir
siendo compatible. El argumento `usuario` no constituye autoridad y sólo puede
servir como objetivo solicitado dentro del contexto global permitido.

Se deben preservar:

- listas vacías;
- múltiples Postdoctorados;
- campos asociativos requeridos;
- refresh de la sección correcta;
- alta contextual de Institución.

Si preservar un consumer exige modificar un caller protegido, la implementación
se detiene.

## 23. Seguridad obligatoria

### H1

- bloquear CRUD anónimo;
- eliminar IDOR;
- impedir usuario manipulable como autoridad;
- impedir UPDATE/DELETE arbitrarios;
- eliminar SQL injection potencial por interpolación.

### H2

- implementar autorización backend;
- implementar ownership efectivo;
- aplicar CSRF a escrituras;
- impedir exposición de datos ajenos.

### H3

- rechazar fechas inválidas e intervalos invertidos nuevos;
- rechazar Institución inválida;
- eliminar falsos éxitos;
- normalizar errores;
- interpretar filas afectadas.

### H4

- confirmar DELETE;
- corregir refresh estrictamente relacionado;
- mostrar mensajes reales.

El cierre sólo cubre Postdoctorado y no declara resuelta la seguridad de otros
objetos o de Ficha integral.

## 24. Archivos modificables definitivos

Únicamente:

1. `src/Model/Postdoctorado.php`;
2. `ajax/postdoctorado.php`;
3. `form-doc/scripts/postdoctorado.js`;
4. `form-doc/agr.form.dat.acad.php`;
5. `form-doc/footer.php`, exclusivamente para cache-busting automático de
   `postdoctorado.js`.

La lista ampliada es cerrada. No se autoriza un sexto archivo funcional.

## 25. Archivos y áreas protegidos

No modificar:

- `ajax/login.php`;
- `src/Model/Login.php`;
- `src/Security/Authorization.php`;
- `src/Model/Institucion.php`;
- `ajax/institucion.php`;
- Ficha Académica como objeto integral;
- callers de Ficha;
- `form-doc/footer.php`, salvo la única modificación de cache-busting descrita
  en la sección 2.1;
- helpers globales;
- `fechaCivil`;
- Grado Académico;
- `docs/roadmap/ROADMAP.md`;
- ADR existentes, incluido ADR-003;
- BD/schema;
- `migrations/`.

No realizar `ALTER`, crear índices, `CHECK`, `UNIQUE`, soft delete, estados,
cambios FK o cambios CASCADE.

## 26. Secuencia de implementación autorizada

1. Confirmar nuevamente que el AT continúa aprobado, que no existe una segunda
   implementación y que no hay conflicto con cambios locales ajenos.
2. Adaptar `src/Model/Postdoctorado.php` a SQL parametrizado, resultados
   asociativos y contrato explícito de escrituras.
3. Convertir `ajax/postdoctorado.php` en autoridad de identidad, autorización,
   ownership, validación, CSRF y HTTP/JSON.
4. Añadir en `form-doc/agr.form.dat.acad.php` únicamente el wiring mínimo de
   contexto, token y visibilidad.
5. Adaptar `form-doc/scripts/postdoctorado.js` al contrato seguro, preservando
   callers y corrigiendo los defectos frontend incluidos.
6. Aplicar en `form-doc/footer.php` el cache-busting autorizado después del
   bloqueo detectado durante VF.
7. Revisar el diff completo y confirmar que sólo existen los cinco archivos
   funcionales autorizados.
8. Ejecutar validaciones estáticas y documentar resultados reales.
9. Entregar la validación dirigida de recarga normal sin declarar su
   aprobación antes de la confirmación del usuario.

Cada paso debe detenerse si aparece una condición de la sección 31.

## 27. Validación técnica posterior a la implementación

Ejecutar como mínimo:

```text
php -l src/Model/Postdoctorado.php
php -l ajax/postdoctorado.php
php -l form-doc/agr.form.dat.acad.php
php -l form-doc/footer.php
git diff --check
git status --short
git diff --name-only
git diff --cached --name-only
```

Confirmar:

- sintaxis PHP válida;
- staging vacío;
- sólo cinco archivos funcionales modificados;
- ningún cambio en BD/schema o migraciones;
- ningún archivo protegido modificado fuera de la excepción de la sección 2.1;
- AT fuente intacto;
- Task y resultados documentales coherentes con el estado real.

Las pruebas no realizadas deben declararse pendientes. No se simula ni presume
una VF.

## 28. Validación funcional posterior

Responsable exclusivo: **USUARIO**.

### 28.1 Profesor propietario

- READ de cero, uno y múltiples propios;
- CREATE propio;
- UPDATE propio;
- DELETE propio;
- lista, detalle, UPDATE y DELETE ajenos bloqueados;
- manipulación de `usuario` sin ampliación de autoridad.

### 28.2 Estudiante propietario

- acceso mediante contrato `perfil.ver` vigente;
- READ de cero, uno y múltiples propios;
- CREATE, UPDATE y DELETE propios;
- registros ajenos bloqueados;
- permiso 5 sin contrato vigente no autoriza.

### 28.3 Admin

- CRUD global sobre Profesor válido;
- CRUD global sobre Estudiante válido;
- usuario inexistente, ambiguo o sin especialización rechazado;
- propietario inmutable en UPDATE.

### 28.4 Comité

- CRUD global sobre Profesor válido;
- CRUD global sobre Estudiante válido;
- usuario inexistente, ambiguo o sin especialización rechazado;
- facultad global preservada si acumula Docencia.

### 28.5 Negativos

- anónimo;
- permiso 3 aislado;
- permiso 5 sin `perfil.ver` vigente;
- usuario inexistente;
- usuario Profesor + Estudiante;
- usuario sin especialización;
- `id_postdoc` inexistente;
- `id_postdoc` ajeno;
- CSRF ausente;
- CSRF inválido;
- operación o ID mal formado.

### 28.6 Datos

- Institución válida;
- Institución inexistente;
- ambas fechas válidas;
- fecha vacía;
- fecha imposible;
- formato distinto de `YYYY-MM-DD`;
- inicio posterior a término;
- inicio igual a término;
- múltiples Postdoctorados;
- lista vacía;
- fila histórica invertida leída sin saneamiento;
- edición expresa de fila histórica con resultado final válido.

### 28.7 UI y consumers

- confirmación DELETE;
- cancelación DELETE sin request;
- refresh correcto sin duplicaciones;
- mensajes de éxito, sin cambios y error;
- Ficha Estudiante;
- Ficha Docente;
- contexto Admin;
- contexto Comité;
- alta contextual de Institución.

La matriz general de VF y la validación dirigida de recarga normal del
cache-busting fueron ejecutadas y aprobadas por el usuario.

## 29. Criterios de aceptación

- [x] 1. El CRUD anónimo está bloqueado.
- [x] 2. El ownership backend es efectivo.
- [x] 3. El IDOR está eliminado dentro de Postdoctorado.
- [x] 4. Profesor y Estudiante operan sólo registros propios sin rol global.
- [x] 5. Admin y Comité ejecutan CRUD global sobre objetivos válidos.
- [x] 6. La identidad Profesor XOR Estudiante está validada.
- [x] 7. CREATE, UPDATE y DELETE exigen CSRF.
- [x] 8. Todo SQL con input externo está parametrizado.
- [x] 9. Ambas fechas y su orden están validados.
- [x] 10. Institución está validada sin modificar su objeto.
- [x] 11. DELETE utiliza `ejecutarEscritura()`.
- [x] 12. Filas afectadas e ID insertado se interpretan según operación.
- [x] 13. HTTP/JSON es coherente y no expone detalles internos.
- [x] 14. Los consumers de Ficha Estudiante, Ficha Docente, Admin y Comité están preservados.
- [x] 15. No existen cambios de BD/schema ni migraciones.
- [x] 16. La implementación modifica exactamente los cinco archivos autorizados.
- [x] 17. Las validaciones estáticas posteriores resultan PASS.
- [x] 18. La VF general fue aprobada y la validación dirigida de cache-busting
      fue confirmada expresamente por el usuario.

La Task sólo puede considerarse técnicamente completada cuando se cumplan los
criterios 1 a 17 con evidencia de revisión. No puede marcarse `CERRADA` antes de
la VF aprobada por el usuario y el cierre documental correspondiente.

## 30. Reversión

La reversión se limita a cambios Git de los cinco archivos autorizados:

1. `src/Model/Postdoctorado.php`;
2. `ajax/postdoctorado.php`;
3. `form-doc/scripts/postdoctorado.js`;
4. `form-doc/agr.form.dat.acad.php`;
5. `form-doc/footer.php`.

No existen cambios de BD que revertir. No ejecutar rollback automáticamente ni
alterar cambios ajenos. La acción concreta de reversión requiere revisar el
diff y la situación Git de la ejecución de implementación.

## 31. Condiciones obligatorias de detención

Detener sin modificar más archivos si:

1. el AT no existe o deja de estar `APROBADO`;
2. el AT contradice la Task;
3. se necesita un sexto archivo o uno distinto de los cinco autorizados;
4. CSRF exige otro archivo fuera del alcance ampliado;
5. la identidad propia/global no puede resolverse con contratos vigentes;
6. Profesor XOR Estudiante exige cambiar Login o Authorization;
7. debe reabrirse Institución;
8. debe modificarse un caller de Ficha;
9. se necesita un cambio de schema;
10. aparece una segunda implementación de Postdoctorado;
11. existe conflicto con cambios locales ajenos;
12. aparece una contradicción institucional;
13. se requiere una nueva capability;
14. no puede preservarse un consumer;
15. la fuente aprobada deja de estar disponible.

Ante una detención se conserva el trabajo seguro ya realizado, se documenta el
impedimento y se vuelve a revisión de alcance. No se resuelve ampliando
silenciosamente la Task.

## 32. Gobierno y cierre

Estado de cierre después de la ampliación operativa:

```text
Task: CERRADA
AT: APROBADO; IMPLEMENTADO; VF APROBADA; CERRADO
Implementación: COMPLETADA EN CINCO ARCHIVOS AUTORIZADOS
Revisión técnica: APROBADA
VF GENERAL: APROBADA POR EL USUARIO
VALIDACIÓN DIRIGIDA CACHE-BUSTING: APROBADA POR EL USUARIO
Blockers: NINGUNO
```

No se crea ADR, addendum, migración ni actualización de Roadmap. No quedan
decisiones institucionales pendientes para iniciar una ejecución separada de
implementación.

Esta Task se marca `CERRADA` después de completar, en orden:

1. implementación dentro de los cinco archivos autorizados;
2. validación estática y revisión técnica;
3. VF general ejecutada y aprobada por el usuario;
4. validación dirigida de cache-busting confirmada por el usuario;
5. cierre documental coherente con la evidencia real.

El cierre se sustenta en la implementación completada, la revisión técnica
aprobada y la aprobación expresa del usuario para la VF general y dirigida.
