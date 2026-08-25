# AT-EPIC003-AUTORIZACION-CURSOS-001

## Autorización integral del objeto Cursos

## 1. Identificación y estado

- **Objeto:** Curso / Cursos.
- **EPIC principales:** EPIC-001 — Modernización del modelo de autorización y permisos; EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Coordinación:** EPIC-003 — Separación segura entre estados académicos y roles de acceso; EPIC-004 — Integridad transaccional; EPIC-008 — Gobierno del Modelo de Datos y Persistencia.
- **ADR vigente:** ADR-001 — Contrato explícito para operaciones de escritura.
- **Clasificación:** [ARQ] [GOV] [CRUD] [AUTH] [PERSIST] [VF].
- **Estado:** APROBADO documentalmente; matriz institucional consolidada; pendiente de revisión técnica previa a implementación.

Este AT es la fuente técnica vigente para la autorización integral de Cursos. La
matriz institucional incorporada resuelve el bloqueo registrado anteriormente
por ausencia de definición por operación. Los AT previos de diagnóstico y
separación de capacidades se conservan como antecedentes técnicos; sus
conclusiones de bloqueo no sustituyen la matriz vigente de este documento.

Este documento no implementa código, no modifica datos o schema y no autoriza
staging, commit, push ni ejecución de VF.

## 2. Objetivo

Consolidar la regla institucional aplicable al objeto Cursos y habilitar la
creación de una única Task integral que abarque:

~~~text
CREATE
→ READ
→ UPDATE
→ DELETE
→ autorización
→ ownership
→ persistencia
→ programa PDF
→ contratos HTTP/JSON
→ frontend
→ rutas legacy
→ VF integral
~~~

La implementación permanece sujeta a revisión técnica. No se divide el objeto
en Tasks por operación.

## 3. Estado técnico confirmado

La superficie activa se compone de:

| Área | Archivo | Responsabilidad |
| --- | --- | --- |
| Página | form-doc/ver.curso.php | Entrada, listado, detalle, formulario y controles CRUD. |
| Navegación | form-doc/header.php | Enlace Programa → Cursos. |
| Cliente | form-doc/scripts/curso.js | Payloads, listado, detalle, CREATE, UPDATE, DELETE y selector Profesor. |
| Endpoint | ajax/curso.php | Operaciones create, read, read_cursos, query_id, update y delete. |
| Modelo | src/Model/Curso.php | SQL y persistencia de curso. |
| Selector | ajax/docente.php y src/Model/Docente.php | read_prof y selección de Profesores Aceptados. |
| Sesión | ajax/login.php y src/Model/Login.php | Roles históricos, estado Estudiante y capacidad docente.habilitado. |
| Archivo | files/prog_curso | Almacenamiento actual de programas. |

Después del cierre integral de Profesor:

- permiso 4 representa identidad Docente;
- profesor.estado_profesor = 2 representa Profesor Aceptado;
- docente.habilitado se produce sólo para Profesor Aceptado;
- Profesor Pendiente y Rechazado no obtienen acceso académico normal;
- read_prof devuelve únicamente Profesores con estado_profesor = 2;
- navegación, página y endpoint de Cursos ya reconocen docente.habilitado.

Ese cierre resuelve la habilitación del actor Profesor, pero el código de Cursos
todavía aplica la misma condición histórica a todas las operaciones:

~~~text
admin OR comite OR aceptado OR docente.habilitado
~~~

La implementación actual no expresa la matriz aprobada, no valida ownership y
continúa utilizando aceptado como autoridad genérica para mutaciones.

## 4. Matriz institucional aprobada

| Actor | CREATE | READ | UPDATE | DELETE |
| --- | --- | --- | --- | --- |
| Admin | Sí, global | Sí, global | Sí, global | Sí, global |
| Comité | Sí, global | Sí, global | Sí, global | No |
| Profesor Aceptado | Sí, propio | Sí, global | Sí, propio | No |
| Profesor Pendiente | No | No | No | No |
| Profesor Rechazado | No | No | No | No |
| Estudiante Aceptado | No | Sí | No | No |
| Estudiante Matriculado | No | Sí | No | No |
| Otros estados Estudiante | No | No | No | No |
| Permiso histórico 3 | No | Sí, compatibilidad | No | No |
| Anónimo | No | No | No | No |

Las capacidades acumulativas conservan la suma de autorizaciones válidas. Una
cuenta con más de un rol obtiene la unión de las capacidades aprobadas, sin
convertir permiso 3, estado Estudiante o identidad Docente en autoridad
administrativa.

## 5. Ownership Profesor

La regla aprobada es:

~~~text
curso.profesor
=
usuario.id_usuario del Profesor autenticado
~~~

### CREATE propio

Cuando un Profesor Aceptado crea un Curso:

- el backend obtiene su usuario desde la sesión validada;
- ignora cualquier id_usuario, id_profesor, profesor u otro identificador
  enviado por el cliente para determinar ownership;
- fuerza curso.profesor al usuario del Profesor autenticado;
- rechaza la operación si la identidad de sesión no representa exactamente un
  Profesor con estado_profesor = 2.

### UPDATE propio

Cuando un Profesor Aceptado actualiza un Curso:

- el backend carga el Curso por id_curso;
- compara curso.profesor con el usuario de sesión;
- permite la actualización sólo si coinciden;
- no permite transferir el Curso a otro Profesor mediante un identificador
  cliente;
- rechaza el UPDATE de un Curso ajeno.

La visibilidad o el filtrado de la UI no reemplazan esta validación.

## 6. Administración y Profesor asociado

Admin y Comité pueden crear y actualizar Cursos globalmente. En esas operaciones
pueden seleccionar Profesor, pero el backend debe comprobar antes de escribir:

1. que el usuario indicado existe;
2. que representa inequívocamente una identidad Profesor;
3. que profesor.estado_profesor = 2;
4. que el identificador corresponde al usuario persistido en curso.profesor.

El selector central:

~~~text
form-doc/scripts/curso.js
→ ajax/docente.php, op=read_prof
→ Docente::buscarProf()
→ estado_profesor = 2
~~~

ya satisface el filtrado de la UI y debe conservarse. El endpoint de Cursos debe
repetir la validación porque el payload cliente es manipulable.

## 7. Capacidad de lectura

La evolución mínima aprobada incorpora:

~~~text
cursos.ver
~~~

Debe representar READ de Cursos y no implica CREATE, UPDATE o DELETE.

La futura revisión técnica determinará la adaptación mínima de ajax/login.php
para derivar cursos.ver desde el estado Estudiante ya disponible:

| Estado Estudiante | cursos.ver |
| --- | --- |
| Aceptado | Sí |
| Matriculado | Sí |
| Otros estados | No |

docente.habilitado continúa representando Profesor Aceptado. Para CREATE y
UPDATE propios debe combinarse con ownership backend.

No se redefine src/Security/Authorization.php en esta fase. La Task debe usar su
contrato vigente y limitar cualquier cambio de producción de capacidad al
archivo de login que la revisión técnica confirme.

## 8. Permiso histórico 3

El permiso 3 puede conservarse temporalmente como compatibilidad de READ, pero:

- no es autoridad suficiente para CREATE;
- no es autoridad suficiente para UPDATE;
- no es autoridad suficiente para DELETE;
- no identifica por sí solo un rol Profesor;
- no debe reutilizarse como shortcut de CRUD global;
- la clave de sesión aceptado, si continúa durante la transición, debe mapearse
  exclusivamente a visualización de Cursos.

La implementación debe evitar que el fallback histórico amplíe capacidades a
actores o estados no autorizados.

## 9. CREATE

CREATE debe:

- autorizar Admin, Comité o Profesor Aceptado;
- para Admin/Comité, validar el Profesor Aceptado seleccionado;
- para Profesor, derivar curso.profesor desde sesión;
- validar campos obligatorios, tipos, rangos y relaciones;
- validar el programa PDF en backend;
- usar SQL parametrizado;
- aplicar el contrato explícito de escritura;
- informar éxito sólo después de confirmar INSERT y archivo coherentes;
- impedir duplicación accidental por doble envío.

La ausencia de una restricción UNIQUE en schema no autoriza a inventar una regla
institucional de unicidad. La revisión técnica debe definir validaciones
funcionales sustentadas por el formulario y detectar únicamente conflictos que
puedan demostrarse sin cambio de schema.

## 10. READ

READ comprende:

- read: listado general;
- query_id: detalle por id_curso;
- read_cursos: catálogo nombre_curso para selector;
- descarga del programa asociado.

Cada operación debe:

- exigir capacidad o actor con READ;
- validar identificadores;
- responder 404 cuando el recurso no exista;
- devolver únicamente los campos requeridos;
- eliminar SELECT * sobre usuario;
- no exponer documento, fecha de nacimiento, dirección, teléfonos, contactos u
  otros antecedentes personales del Profesor;
- usar una estructura asociativa estable y no depender innecesariamente de
  índices numéricos PDO;
- proteger la entrega del PDF conforme a la solución mínima que confirme la
  revisión técnica.

Profesor Aceptado, Admin, Comité, Estudiante Aceptado y Estudiante Matriculado
poseen READ global.

## 11. UPDATE

UPDATE debe:

- autorizar Admin y Comité globalmente;
- autorizar Profesor Aceptado sólo sobre Curso propio;
- bloquear Estudiante, permiso 3 como autoridad aislada y cualquier otro actor;
- validar existencia de id_curso;
- validar ownership antes de mutar;
- para Admin/Comité, validar todo nuevo Profesor asociado;
- para Profesor, preservar su asociación y no aceptar transferencia cliente;
- parametrizar SQL y comprobar filas afectadas;
- coordinar reemplazo de programa sin destruir anticipadamente el archivo
  vigente;
- devolver un resultado inequívoco.

## 12. DELETE

La decisión institucional definitiva es:

~~~text
DELETE Curso = físico
Autoridad = sólo Admin
~~~

| Actor | DELETE |
| --- | --- |
| Admin | Sí, global |
| Comité | No |
| Profesor | No |
| Estudiante | No |
| Permiso 3 | No |
| Anónimo | No |

No se introduce DELETE lógico, columna de estado ni migración. La implementación
debe coordinar el registro de base de datos y el PDF mediante una estrategia
segura y compensable. Un fallo de archivo no puede presentarse como eliminación
íntegra, y un fallo de base de datos no puede destruir definitivamente el
archivo vigente.

## 13. Seguridad de archivos y lifecycle PDF

El programa de Curso mantiene política PDF. La Task integral debe resolver:

- validación MIME real mediante finfo;
- extensión y tamaño permitidos;
- nombre generado y controlado por servidor;
- rechazo de path traversal;
- ausencia de confianza en arch_actual o rutas cliente;
- consulta del archivo vigente desde base de datos;
- contención de unlink dentro del directorio autorizado;
- almacenamiento o entrega que no permita ejecución arbitraria;
- compensación ante fallos de archivo o persistencia.

### CREATE

Archivo válido e INSERT deben producir un único resultado coherente. Ante fallo,
no debe quedar registro incompleto ni archivo huérfano.

### UPDATE

El reemplazo debe prepararse y validarse antes de alterar el registro. El archivo
anterior no se elimina definitivamente antes de garantizar un reemplazo seguro.

### DELETE

Sólo Admin puede iniciar la operación. Registro y archivo deben eliminarse
físicamente mediante una secuencia reversible o compensable durante el límite
de la operación.

La revisión técnica debe evaluar files/prog_curso. Si mover archivos fuera del
webroot exige infraestructura no acotada, deberá registrar el bloqueo y elegir
la mínima protección viable sin improvisar una ampliación arquitectónica.

## 14. Persistencia y ADR-001

src/Model/Curso.php debe dejar de concatenar parámetros y utilizar consultas
preparadas para todas las entradas variables.

CREATE, UPDATE y DELETE deben adoptar ejecutarEscritura() de manera específica:

- CREATE requiere éxito, filas afectadas e idInsertado;
- UPDATE requiere resultado explícito y filas afectadas;
- DELETE requiere resultado explícito y filas afectadas;
- ninguna operación considera verdadero un PDOStatement sólo por existir;
- las excepciones deben propagarse al endpoint para producir error HTTP/JSON.

La coordinación BD/archivo requiere una estrategia transaccional y de
compensación definida durante revisión técnica. ADR-001 no se modifica ni se
aplica mecánicamente fuera de Curso.

## 15. Contrato HTTP/JSON

Las escrituras deben responder con un objeto inequívoco:

~~~json
{
  "ok": true,
  "datos": {},
  "mensaje": "Operación completada."
}
~~~

Los errores deben utilizar, según corresponda:

| HTTP | Uso |
| --- | --- |
| 400 | Payload, identificador o tipo inválido |
| 403 | Actor sin autorización |
| 404 | Curso o relación inexistente |
| 409 | Conflicto de ownership, estado o resultado incompatible |
| 422 | Validación funcional |
| 500 | Error técnico de persistencia o coordinación de archivo |

Las respuestas de error incluyen ok=false, error y mensaje. El frontend no debe
mostrar éxito ni volver al listado cuando ok no sea true.

## 16. Frontend

La Task integral debe corregir dentro del mismo objeto:

- manejo explícito de errores HTTP;
- exigencia de ok=true antes del mensaje de éxito;
- confirmación DELETE;
- bloqueo de doble envío;
- renderizado seguro sin concatenar datos no escapados;
- selector incorrecto #docente;
- apellido materno mostrado incorrectamente;
- ID form_curso duplicado;
- dependencia innecesaria de índices numéricos;
- mensajes coherentes para CREATE, UPDATE, DELETE y errores.

No se crea una Task UX separada.

## 17. IDOR y autorización directa

Los guards deben ejecutarse antes de acceder a datos o archivos. Deben
protegerse:

- query_id;
- UPDATE;
- DELETE;
- selección o cambio de Profesor;
- descarga del programa;
- cualquier operación alternativa por id_curso.

El acceso directo a ajax/curso.php debe producir la misma decisión que la UI.
Los identificadores cliente seleccionan un recurso, pero nunca determinan el
actor ni el ownership.

## 18. Rutas legacy

Se registran para revisión técnica:

| Ruta | Evidencia | Decisión durante revisión |
| --- | --- | --- |
| ingr.curso.php | Sin caller; requiere functions.php y vista inexistentes | Retirar, responder 410 o conservar inactiva |
| ajax/nombre.curso.php | Archivo vacío; sin callers | Retirar, responder 410 o conservar inactivo |

No se adopta todavía una acción técnica sobre estas rutas.

## 19. Archivos potenciales de implementación

### Alcance técnico inicial

- ajax/curso.php
- src/Model/Curso.php
- form-doc/scripts/curso.js
- form-doc/ver.curso.php
- form-doc/header.php

### Condicionado a revisión técnica

- ajax/login.php, exclusivamente para producir cursos.ver desde estado
  Estudiante.

### Legacy sujeto a decisión técnica

- ingr.curso.php
- ajax/nombre.curso.php

src/Security/Authorization.php no se redefine. Otros archivos no ingresan al
alcance sin evidencia y revisión.

## 20. Schema

~~~text
Schema change requerido: No
Migración: No
Nuevas tablas o columnas: No
~~~

La implementación debe utilizar curso.profesor y las relaciones existentes.

## 21. Task integral autorizada

Se autoriza crear:

~~~text
TASK-EPIC001-AUTORIZACION-CURSOS-INTEGRAL-001
~~~

La Task constituye una única unidad bajo EPIC-001 y EPIC-009. Debe quedar lista
para revisión técnica previa a implementación y no autoriza cambios de código
por sí sola.

## 22. VF futura

La VF será integral y ejecutada exclusivamente por el usuario después de una
implementación autorizada. Debe cubrir:

- Admin: CREATE, READ, UPDATE y DELETE físico global; lifecycle PDF;
- Comité: CREATE, READ y UPDATE global; DELETE bloqueado;
- Profesor Aceptado: CREATE propio, READ global, UPDATE propio, UPDATE ajeno y
  DELETE bloqueados, imposibilidad de forzar otro Profesor;
- Profesor Pendiente y Rechazado: sin acceso;
- Estudiante Aceptado y Matriculado: READ solamente;
- otros estados Estudiante: sin acceso;
- permiso 3: READ de compatibilidad y ninguna mutación;
- anónimo: página, endpoint y archivos bloqueados;
- id_curso y Profesor manipulados;
- MIME falso, archivo no PDF, tamaño inválido y ruta manipulada;
- arch_actual manipulado;
- doble envío y doble DELETE;
- fallo de base de datos y fallo de archivo;
- endpoint directo y contratos HTTP/JSON.

No se ejecuta VF durante esta consolidación documental.

## 23. Riesgos y condiciones de revisión

Riesgos principales:

- ampliar permiso 3 más allá de READ;
- confiar en ownership de UI;
- perder o dejar huérfanos programas PDF;
- ejecutar archivos cargados desde el webroot;
- exponer datos personales mediante SELECT *;
- reportar éxito ante escritura parcial;
- extender cambios de login o Authorization fuera del objeto.

La revisión técnica debe detenerse si:

1. cursos.ver no puede derivarse sin redefinir globalmente autorización;
2. la solución PDF exige infraestructura no acotada;
3. la coordinación BD/archivo no dispone de estrategia compensable;
4. aparece una relación persistente no inventariada que cambie DELETE;
5. se requiere cambiar schema;
6. el alcance deja de ser una única unidad funcional Curso;
7. una implementación exige alterar la matriz institucional.

## 24. Gobierno y cierre

La decisión institucional pendiente registrada por versiones anteriores queda
resuelta por la matriz de este documento.

No se requiere:

- nuevo AT;
- addendum;
- nueva EPIC;
- nueva Feature;
- Task separada de seguridad;
- Task separada de frontend;
- Task por operación;
- cambio de schema.

Próximo paso:

~~~text
Revisión técnica de
TASK-EPIC001-AUTORIZACION-CURSOS-INTEGRAL-001
~~~

## 25. Fuentes

- ROADMAP.md.
- ADR-001 — Contrato explícito para operaciones de escritura.
- ADR-002 — Evolución del modelo de identidad y participación académica.
- AT-EPIC001-AUTORIZACION-PROFESOR-INTEGRAL-001.
- AT-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.
- AT-EPIC003-CURSOS-SESION-ACEPTADO-001.
- AT-EPIC003-CURSOS-VALIDACION-ACCESO-ACEPTADO-001.
- Inspección integral del objeto Cursos sobre HEAD
  109d6c2603176f3c0cd96e80bd3c1a2ab0dccbf5.

## 26. Dictamen

Matriz institucional aprobada e incorporada. El bloqueo documental de Cursos se
retira. Corresponde una única Task integral, pendiente de revisión técnica antes
de cualquier implementación.
