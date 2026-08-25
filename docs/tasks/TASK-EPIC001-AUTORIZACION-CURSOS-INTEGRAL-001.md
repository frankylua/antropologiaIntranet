# TASK-EPIC001-AUTORIZACION-CURSOS-INTEGRAL-001

## 1. Estado

~~~text
Task: CERRADA
Implementación: COMPLETADA
Revisión técnica: APROBADA
Validación funcional: APROBADA POR EL USUARIO
~~~

### 1.1. Evidencia mínima de cierre

- CRUD y autorización aplicados: Admin CRUD global; Comité CRU global sin
  DELETE; Profesor Accepted CREATE propio, READ global y UPDATE propio sin
  DELETE; Estudiante Aceptado, Estudiante Matriculado y permiso histórico 3
  con READ; Profesor Pending, Profesor Rejected y anónimo sin acceso.
- Ownership aplicado: la identidad Profesor se deriva en backend para CREATE,
  UPDATE se limita a Cursos propios, la manipulación del Profesor en request se
  rechaza y Admin/Comité sólo pueden seleccionar un Profesor Accepted válido.
- Seguridad validada: `cursos.ver`, CSRF, ownership, descarga por `id_curso`,
  READ independiente de ownership y protección legacy mediante `.htaccess`.
- Persistencia Documento integrada: nuevas escrituras guardan el PDF en BD sin
  filesystem; UPDATE sin PDF conserva Documento; UPDATE con PDF actualiza o
  crea la asociación; DELETE Admin elimina Curso/Documento transaccionalmente;
  fallback legacy sólo con FK Documento nula.
- UI corregida: los mensajes success se auto-ocultan a los 3000 ms, el timer
  anterior se cancela y los errores permanecen visibles.
- Validación funcional integral: **APROBADA POR EL USUARIO**.

- **Objeto:** Curso / Cursos.
- **EPIC principales:** EPIC-001 y EPIC-009.
- **Coordinación:** EPIC-003, EPIC-004 y EPIC-008.
- **ADR:** ADR-001 — Contrato explícito para operaciones de escritura.
- **AT vigente:** AT-EPIC003-AUTORIZACION-CURSOS-001.
- **Clasificación:** [TASK] [CRUD] [AUTH] [OWNERSHIP] [SEC] [PERSIST] [PDF] [HTTP] [FRONT] [VF] [REV].

Esta Task es una única unidad funcional. No se divide en Tasks de CREATE, READ,
UPDATE, DELETE, seguridad, frontend, PDF o UX.

## 2. Objetivo único

Implementar el objeto Cursos de extremo a extremo conforme a la matriz
institucional aprobada, cerrando en una sola unidad:

~~~text
UI
→ JavaScript
→ HTTP/AJAX
→ autorización
→ ownership
→ modelo
→ persistencia
→ programa PDF
→ mensajes
→ rutas legacy
→ VF integral
~~~

El resultado debe conservar el schema actual, eliminar los defectos técnicos
confirmados y producir un CRUD coherente por actor.

## 3. Baseline documental y técnico

Baseline inspeccionado:

~~~text
Rama: refactor/fase-0-seguridad
HEAD: 109d6c2603176f3c0cd96e80bd3c1a2ab0dccbf5
~~~

Flujo activo:

~~~text
form-doc/ver.curso.php
→ form-doc/scripts/curso.js
→ ajax/curso.php
→ src/Model/Curso.php
→ curso / nombre_curso
→ files/prog_curso
~~~

Selector Profesor:

~~~text
curso.js
→ ajax/docente.php, read_prof
→ Docente::buscarProf()
→ sólo estado_profesor = 2
~~~

## 4. Matriz CRUD obligatoria

| Actor | CREATE | READ | UPDATE | DELETE |
| --- | --- | --- | --- | --- |
| Admin | Global | Global | Global | Global, físico |
| Comité | Global | Global | Global | No |
| Profesor Aceptado | Propio | Global | Propio | No |
| Profesor Pendiente | No | No | No | No |
| Profesor Rechazado | No | No | No | No |
| Estudiante Aceptado | No | Sí | No | No |
| Estudiante Matriculado | No | Sí | No | No |
| Otros estados Estudiante | No | No | No | No |
| Permiso histórico 3 | No | READ de compatibilidad | No | No |
| Anónimo | No | No | No | No |

No se considera completa la Task mientras cualquiera de estas operaciones
permanezca defectuosa, desconectada o incompatible con la matriz.

## 5. Reglas de autorización

### Admin

Puede crear, leer, actualizar y eliminar cualquier Curso.

### Comité

Puede crear, leer y actualizar cualquier Curso. DELETE debe responder rechazo
backend aunque el control no sea visible.

### Profesor Aceptado

Requiere:

- identidad Docente válida;
- permiso 4 preservado;
- profesor.estado_profesor = 2;
- capacidad docente.habilitado;
- ownership backend para CREATE y UPDATE.

Puede leer globalmente. No puede eliminar Cursos.

### Profesor Pendiente y Rechazado

Mantienen identidad Docente y Mi Perfil conforme al objeto Profesor, pero no
acceden a Cursos. La mera existencia de permiso 4 no habilita el objeto.

### Estudiante

Aceptado y Matriculado pueden leer. Cualquier mutación debe rechazarse. Otros
estados no acceden salvo una regla vigente explícita distinta; esta Task no crea
ninguna.

### Permiso histórico 3

aceptado no puede seguir operando como shortcut de CRUD global. Si se conserva
durante la transición, sólo habilita READ.

### Anónimo

No accede a página, endpoint ni entrega del programa.

## 6. Capacidad cursos.ver

La implementación debe crear o usar:

~~~text
cursos.ver
~~~

como capacidad de READ, sin conceder mutaciones.

Durante revisión técnica debe confirmarse la adaptación mínima de ajax/login.php
para producirla desde el estado Estudiante ya consultado:

| tipo_est | Estado | cursos.ver |
| ---: | --- | --- |
| 2 | Aceptado | Sí |
| 3 | Matriculado | Sí |
| Otro | Otro estado | No |

docente.habilitado continúa habilitando al Profesor Aceptado, pero CREATE y
UPDATE propios requieren además ownership.

src/Security/Authorization.php no debe redefinirse. Si la capacidad no puede
materializarse con el contrato vigente sin una ampliación global, detener la
implementación y volver a revisión.

## 7. Autoridad backend y actor

El endpoint debe resolver el actor desde contexto de sesión validado. Ninguno de
estos valores cliente determina actor u ownership:

- id_usuario;
- id_profesor;
- profesor;
- docente;
- id_curso;
- arch_actual;
- ruta o nombre de archivo.

id_curso sólo identifica el recurso solicitado. Antes de operar deben validarse
autorización, existencia y, cuando aplique, ownership.

## 8. Ownership Profesor

Regla:

~~~text
curso.profesor
=
usuario.id_usuario del Profesor autenticado
~~~

### CREATE

Para Profesor Aceptado:

1. resolver id_usuario desde sesión;
2. comprobar identidad Profesor única y estado 2;
3. ignorar cualquier Profesor enviado por cliente;
4. persistir el id_usuario de sesión en curso.profesor.

### UPDATE

Para Profesor Aceptado:

1. cargar el Curso por id_curso;
2. resolver id_usuario desde sesión;
3. comparar curso.profesor con el id_usuario del actor;
4. rechazar si no coinciden;
5. preservar el Profesor asociado;
6. ignorar o rechazar cualquier intento de transferencia.

No basta ocultar Cursos ajenos o controles en la UI.

## 9. Profesor asociado en operaciones administrativas

Admin y Comité pueden seleccionar Profesor durante CREATE y UPDATE. El endpoint
debe verificar:

- id de usuario válido;
- usuario existente;
- fila Profesor inequívoca;
- estado_profesor = 2;
- relación compatible con curso.profesor.

El selector read_prof ya filtra Aceptados y debe conservarse. Su resultado no
reemplaza la validación del endpoint.

## 10. CREATE integral

Payload funcional:

- nombre de Curso válido;
- créditos;
- carácter;
- periodo;
- año;
- carga horaria;
- programa PDF;
- Profesor, sólo para Admin/Comité;
- operación.

El backend debe:

1. autorizar antes de archivos o persistencia;
2. validar obligatoriedad, tipos, rangos y relaciones;
3. derivar Profesor propio cuando el actor sea Profesor Aceptado;
4. validar Profesor seleccionado cuando el actor sea Admin/Comité;
5. validar PDF real;
6. generar nombre servidor;
7. preparar una estrategia coherente de archivo;
8. ejecutar INSERT parametrizado;
9. exigir exactamente una fila afectada;
10. obtener idInsertado;
11. responder ok=true sólo al completar el resultado integral.

Un fallo no puede dejar archivo huérfano, registro incompleto ni mensaje de
éxito.

La revisión técnica debe establecer si existe una regla de duplicado demostrable
con los campos actuales. No debe inventar UNIQUE ni modificar schema.

## 11. READ integral

Operaciones incluidas:

| Operación | Propósito |
| --- | --- |
| read | Listado general |
| query_id | Detalle de un Curso |
| read_cursos | Catálogo nombre_curso |
| descarga | Programa PDF |

READ debe:

- exigir cursos.ver o rol con READ aprobado;
- validar id_curso como entero positivo;
- responder 404 para Curso inexistente;
- usar consultas preparadas;
- devolver campos asociativos explícitos;
- eliminar SELECT *;
- excluir datos personales innecesarios del Profesor;
- devolver sólo identificador y nombre institucional mínimo del Profesor;
- evitar dependencia del frontend en FETCH_BOTH e índices numéricos;
- proteger la descarga conforme a la decisión técnica de almacenamiento.

query_id no debe transformarse en bypass de acceso, archivo o datos personales.

## 12. UPDATE integral

El backend debe:

1. autorizar al actor;
2. validar id_curso;
3. cargar el Curso existente;
4. aplicar ownership si el actor es Profesor;
5. validar el Profesor nuevo si el actor es Admin/Comité;
6. impedir transferencia cliente para Profesor;
7. validar todos los campos modificables;
8. preparar de forma segura un PDF nuevo, cuando exista;
9. ejecutar UPDATE parametrizado;
10. validar filas afectadas y distinguir sin cambios de inexistente/conflicto;
11. conservar el archivo anterior hasta garantizar reemplazo;
12. responder con contrato inequívoco.

La actualización parcial no debe reconstruirse mediante una llamada AJAX
síncrona que mezcle etiquetas con identificadores. El backend debe definir el
contrato de campos requeridos y el frontend debe enviarlo explícitamente.

## 13. DELETE integral

Regla definitiva:

~~~text
DELETE físico
Sólo Admin
~~~

El endpoint debe:

- autorizar antes de consultar o eliminar;
- validar id_curso;
- responder 404 si no existe;
- obtener el archivo vigente desde base de datos;
- no usar arch_actual cliente;
- confinar cualquier operación de archivo al directorio aprobado;
- ejecutar DELETE parametrizado;
- exigir exactamente una fila afectada;
- coordinar eliminación de registro y PDF con estrategia compensable;
- responder éxito sólo cuando el resultado integral sea coherente.

Comité, Profesor, Estudiante, permiso 3 y anónimo deben ser rechazados mediante
invocación directa. Un segundo DELETE no puede informar un nuevo éxito.

## 14. Lifecycle seguro del programa PDF

### Validación obligatoria

- archivo recibido mediante upload válido;
- MIME real comprobado con finfo;
- política exclusiva PDF;
- extensión controlada;
- tamaño máximo explícito;
- nombre generado por servidor;
- ausencia de segmentos de ruta cliente;
- ruta canónica contenida en ubicación autorizada.

No se acepta como autoridad:

- nombre original;
- MIME enviado por navegador;
- arch_actual;
- ruta enviada por cliente.

### CREATE

Archivo e INSERT deben confirmarse como una sola operación observable. La
estrategia puede usar staging temporal seguro y compensación, pero debe quedar
definida exactamente durante revisión técnica.

### UPDATE

El PDF anterior no se elimina antes de asegurar la validez y disponibilidad del
nuevo. Si la escritura BD falla, el estado anterior debe poder preservarse o
restaurarse.

### DELETE

El archivo debe obtenerse desde el Curso persistido. Si falla BD o filesystem,
la operación debe fallar de forma explícita y aplicar la compensación definida.

### Ubicación

La revisión técnica evaluará files/prog_curso y elegirá la mínima solución que
impida ejecución arbitraria y descarga no autorizada. Mover archivos fuera del
webroot no se presume. Si exige infraestructura no acotada, registrar bloqueo y
detener antes de improvisar.

## 15. SQL y consultas preparadas

src/Model/Curso.php debe eliminar toda concatenación de variables en:

- INSERT;
- UPDATE;
- DELETE;
- query_id;
- búsqueda por archivo;
- búsqueda de archivo por id;
- cualquier lectura con parámetros.

Los parámetros se tipan y validan antes de execute. La corrección queda limitada
al objeto Curso y no autoriza una parametrización masiva de otros modelos.

## 16. ADR-001

Las escrituras deben usar ejecutarEscritura() según su necesidad:

| Operación | Resultado requerido |
| --- | --- |
| CREATE | filasAfectadas = 1 e idInsertado válido |
| UPDATE | resultado explícito y filasAfectadas |
| DELETE | filasAfectadas = 1 |

No se interpreta un PDOStatement truthy como éxito funcional. Las excepciones
se propagan al endpoint y se traducen a un contrato HTTP/JSON controlado.

La coordinación BD/filesystem puede requerir transacción y compensación; esa
decisión no modifica ADR-001.

## 17. Contrato HTTP/JSON

### Éxito

~~~json
{
  "ok": true,
  "datos": {},
  "mensaje": "Curso creado correctamente."
}
~~~

datos se incluye cuando corresponde, por ejemplo id_curso después de CREATE o
el recurso solicitado en READ.

### Error

~~~json
{
  "ok": false,
  "error": "CODIGO_ESTABLE",
  "mensaje": "No fue posible completar la operación."
}
~~~

### Estados

| HTTP | Condición |
| --- | --- |
| 200 | READ o escritura completada |
| 400 | Payload o identificador inválido |
| 403 | Actor no autorizado |
| 404 | Curso, Profesor o relación inexistente |
| 409 | Ownership, estado o conflicto de persistencia |
| 422 | Validación funcional, incluido PDF inválido |
| 500 | Error técnico BD/archivo |

El endpoint debe emitir Content-Type JSON. No debe mezclar warnings, HTML o
texto con la respuesta.

## 18. IDOR

Casos obligatorios:

- query_id sin READ;
- id_curso inexistente;
- Profesor actualizando Curso ajeno;
- Profesor intentando cambiar curso.profesor;
- Comité intentando DELETE;
- Estudiante intentando mutación;
- permiso 3 intentando mutación;
- Admin operando id_curso manipulado inexistente;
- descarga directa sin autorización.

Los guards deben preceder a la consulta sensible o mutación. La respuesta no
debe revelar datos personales ni rutas internas.

## 19. Frontend

form-doc/scripts/curso.js debe:

- consumir objetos JSON y no arrays duales;
- comprobar ok === true antes de mostrar éxito;
- manejar callbacks de error HTTP;
- mostrar mensajes de error sin redirigir ni limpiar el formulario;
- impedir doble submit;
- confirmar DELETE mediante el componente vigente;
- impedir doble DELETE;
- renderizar datos mediante APIs seguras;
- evitar concatenación de HTML con datos no escapados;
- corregir #docente por el control real;
- mostrar apellido materno en lugar de repetir apellido paterno;
- eliminar solicitud síncrona async:false;
- transportar IDs de manera explícita;
- no usar atributos name como autoridad de rutas o ownership.

form-doc/ver.curso.php debe:

- eliminar el ID form_curso duplicado;
- exponer controles según capacidad para usabilidad;
- mantener el backend como autoridad;
- separar visualmente READ de mutaciones;
- conservar el formulario único del objeto;
- usar una declaración de aceptación PDF coherente.

No se crea Task UX separada.

## 20. Mensajes

Mensajes mínimos:

- Curso creado correctamente;
- Curso actualizado correctamente;
- Curso eliminado correctamente;
- datos inválidos;
- Profesor asociado inválido o no Aceptado;
- Curso inexistente;
- operación no autorizada;
- Curso ajeno;
- PDF inválido;
- error de persistencia;
- error de archivo.

Un mensaje negativo nunca utiliza estilo de éxito. La UI sólo actualiza listado
y limpia formulario después de ok=true.

## 21. Navegación

form-doc/header.php debe:

- mostrar Cursos a actores con READ;
- reconocer cursos.ver;
- conservar acceso Admin, Comité y Profesor Aceptado;
- no usar aceptado como autoridad administrativa;
- no mostrar acceso a Profesor Pendiente/Rechazado u otros estados Estudiante;
- alinear navegación con la página y el endpoint.

La visibilidad del enlace no concede mutaciones.

## 22. Rutas legacy

### ingr.curso.php

Evidencia:

- sin caller;
- requiere functions.php inexistente;
- referencia views/ingr.curso.view.php inexistente;
- no participa en el flujo activo.

### ajax/nombre.curso.php

Evidencia:

- vacío;
- sin callers;
- no participa en read_cursos.

Durante revisión técnica se debe elegir, con evidencia final, una de estas
acciones para cada ruta:

- retirar;
- responder 410;
- conservar inactiva.

No se reconstruye un flujo legacy sin caller. La decisión debe quedar dentro de
esta Task y no originar otro documento o Task.

## 23. Alcance de archivos

### Archivos iniciales

| Archivo | Rol futuro |
| --- | --- |
| ajax/curso.php | Guards, validación, contrato, PDF y coordinación |
| src/Model/Curso.php | SQL preparado y contratos de persistencia |
| form-doc/scripts/curso.js | Cliente, mensajes, seguridad de render y CRUD |
| form-doc/ver.curso.php | Página, formulario y controles por capacidad |
| form-doc/header.php | Navegación READ coherente |

### Condicionado a revisión técnica

| Archivo | Condición |
| --- | --- |
| ajax/login.php | Sólo para derivar cursos.ver desde estado Estudiante |

### Legacy sujeto a decisión

| Archivo | Estado |
| --- | --- |
| ingr.curso.php | Sin caller y roto |
| ajax/nombre.curso.php | Vacío y sin callers |

Los archivos condicionados no son modificables definitivos hasta que la revisión
técnica confirme la transformación exacta.

## 24. Archivos y áreas protegidas

No modificar sin bloqueo explícito y nueva revisión:

- src/Security/Authorization.php;
- schema o migraciones;
- c1441353_antr_db.sql;
- modelos de Profesor, Estudiante o Login;
- endpoints ajenos a Curso y al productor condicionado de cursos.ver;
- permisos del catálogo permiso_login;
- otros objetos académicos.

La reutilización de ajax/docente.php::read_prof y Docente::buscarProf() no
autoriza cambios porque ya satisfacen el selector de Aceptados.

## 25. Schema

~~~text
Schema change: No
Migración: No
Tabla nueva: No
Columna nueva: No
DELETE lógico: No
~~~

La Task usa curso.profesor como ownership y mantiene DELETE físico.

## 26. Criterios de aceptación funcionales

### CREATE

- Admin crea Curso para cualquier Profesor Aceptado.
- Comité crea Curso para cualquier Profesor Aceptado.
- Profesor Aceptado crea Curso asociado obligatoriamente a sí mismo.
- Profesor no puede forzar otro Profesor.
- Profesor Pendiente/Rechazado y Estudiante no crean.
- PDF y registro quedan coherentes.

### READ

- Admin, Comité y Profesor Aceptado leen globalmente.
- Estudiante Aceptado y Matriculado leen mediante cursos.ver.
- permiso 3 sólo conserva READ de compatibilidad.
- otros estados y anónimo no acceden.
- detalle no expone datos personales innecesarios.
- PDF no se ejecuta ni se entrega sin autorización.

### UPDATE

- Admin y Comité actualizan cualquier Curso.
- Profesor Aceptado actualiza sólo Curso propio.
- Profesor no actualiza Curso ajeno ni transfiere ownership.
- Profesor seleccionado administrativamente debe estar Aceptado.
- archivo anterior se preserva ante fallo.

### DELETE

- sólo Admin elimina físicamente;
- Comité, Profesor, Estudiante, permiso 3 y anónimo son bloqueados;
- registro y PDF terminan coherentes;
- segundo DELETE no informa éxito.

## 27. Criterios de aceptación técnicos

- todas las operaciones tienen guard backend;
- cursos.ver no habilita mutaciones;
- ownership se deriva de sesión;
- no existe SQL concatenado en Curso;
- no existe SELECT * del usuario asociado;
- escrituras cumplen ADR-001;
- PDF se valida con finfo, tamaño y nombre servidor;
- arch_actual cliente no se utiliza como autoridad;
- no existe unlink sobre ruta cliente;
- JSON y HTTP son inequívocos;
- frontend sólo muestra éxito con ok=true;
- no existe doble envío;
- no existe renderizado inseguro en el flujo Curso;
- código y schema ajenos permanecen intactos.

## 28. VF integral futura

La VF corresponde exclusivamente al usuario y no se ejecuta al crear esta Task.

### Admin

- CREATE global con Profesor Aceptado;
- READ lista, detalle y PDF;
- UPDATE global con y sin reemplazo PDF;
- DELETE físico;
- fallos BD y filesystem;
- id inexistente y payload inválido.

### Comité

- CREATE global;
- READ global;
- UPDATE global;
- DELETE oculto y bloqueado por endpoint directo.

### Profesor Aceptado

- CREATE propio;
- READ global;
- UPDATE propio;
- UPDATE ajeno bloqueado;
- intento de forzar Profesor ajeno bloqueado;
- DELETE bloqueado.

### Profesor Pendiente

- navegación, página y endpoint bloqueados.

### Profesor Rechazado

- navegación, página y endpoint bloqueados.

### Estudiante Aceptado

- READ lista, detalle y PDF;
- CREATE, UPDATE y DELETE bloqueados.

### Estudiante Matriculado

- READ lista, detalle y PDF;
- CREATE, UPDATE y DELETE bloqueados.

### Otros estados Estudiante

- sin acceso a Cursos.

### Permiso 3

- compatibilidad READ, si permanece;
- ninguna mutación;
- no constituye identidad Profesor.

### Seguridad

- id_curso manipulado;
- profesor manipulado;
- endpoint directo;
- acceso anónimo;
- MIME declarado PDF con contenido falso;
- archivo no PDF;
- archivo excedido;
- nombre con traversal;
- arch_actual manipulado;
- doble submit;
- doble DELETE;
- error INSERT después de preparar archivo;
- error UPDATE durante reemplazo;
- error DELETE BD;
- error al eliminar/restaurar archivo;
- contenido almacenado presentado de forma segura.

## 29. Validaciones técnicas futuras

Antes de solicitar VF:

- ejecutar lint PHP sobre archivos modificados;
- validar sintaxis JavaScript mediante la herramienta disponible;
- ejecutar git diff --check;
- confirmar que sólo estén modificados los archivos aprobados;
- inspeccionar que no haya SQL concatenado;
- inspeccionar que no haya rutas cliente en filesystem;
- inspeccionar que no exista SELECT * sensible;
- verificar códigos HTTP mediante pruebas no mutantes;
- verificar permisos con dobles o fixtures autorizados;
- no usar datos personales reales en evidencia.

Las escrituras funcionales reales permanecen reservadas a la VF del usuario.

## 30. Revisión técnica previa obligatoria

La revisión debe definir exactamente:

1. funciones de guard por operación;
2. resolución del actor y del id_usuario Profesor;
3. consultas preparadas y firmas de Curso;
4. estrategia de filas afectadas e idInsertado;
5. contrato JSON por operación;
6. derivación mínima de cursos.ver;
7. estrategia PDF para CREATE, UPDATE y DELETE;
8. protección de entrega del PDF;
9. comportamiento ante fallos compensables;
10. decisión final sobre rutas legacy;
11. archivos definitivos y reversión.

No comenzar implementación mientras alguno de estos puntos requiera una decisión
arquitectónica fuera del alcance aprobado.

## 31. Reversión

La futura implementación debe poder revertirse por archivos del objeto sin:

- modificar schema;
- reconstruir permisos;
- eliminar datos de otros objetos;
- cambiar estados Profesor o Estudiante;
- redefinir Authorization;
- perder programas preexistentes.

La revisión debe registrar cómo revertir capacidad, guards, contratos, SQL,
frontend y manejo PDF. Cualquier movimiento de archivos debe contar con
estrategia de retorno verificable.

## 32. Riesgos

- permiso 3 conservando mutaciones por fallback;
- ownership inferido desde cliente;
- Profesor asociando un usuario no Aceptado;
- IDOR de modificación;
- exposición de datos personales;
- SQL injection;
- upload ejecutable;
- path traversal y unlink arbitrario;
- pérdida del PDF anterior;
- archivo huérfano;
- DELETE parcial;
- respuesta 200 ante fallo;
- UI mostrando éxito falso;
- expansión del cambio hacia login o autorización global;
- retiro de rutas legacy con caller no inventariado.

## 33. Condiciones para detener la implementación

Detener y volver a revisión si:

1. el worktree contiene cambios ajenos que se solapan;
2. cursos.ver exige redefinir Authorization.php;
3. se requiere modificar schema;
4. aparece una relación entrante a Curso que cambia DELETE;
5. ownership no puede derivarse inequívocamente desde sesión;
6. el almacenamiento PDF seguro exige infraestructura no acotada;
7. no puede definirse compensación BD/archivo;
8. aparece un caller legacy activo no inventariado;
9. se requiere alterar la matriz institucional;
10. el objeto necesita dividirse por una dependencia arquitectónica real;
11. la implementación requiere modificar archivos protegidos.

## 34. Fuera de alcance

- cambios de schema;
- DELETE lógico;
- nueva tabla o columna de Curso;
- reescritura general de PHP o jQuery;
- refactorización global de Authorization;
- migración global del permiso 3;
- cambios generales de Login;
- modernización de otros objetos;
- nueva arquitectura general de archivos;
- Task UX separada;
- Task de seguridad separada;
- ejecución de VF;
- staging, commit o push.

## 35. Gobierno

Documentación suficiente:

~~~text
AT vigente actualizado
+
una Task integral
~~~

No se requiere addendum, nuevo AT, nueva Feature, nueva EPIC ni Tasks por
operación.

## 36. Dictamen

Task integral creada y lista para revisión técnica previa a implementación.
La matriz institucional, ownership, DELETE físico, schema sin cambios y alcance
de seguridad quedan definidos. La Task no autoriza todavía modificar código.
