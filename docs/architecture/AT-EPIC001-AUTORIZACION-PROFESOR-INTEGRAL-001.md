# AT-EPIC001-AUTORIZACION-PROFESOR-INTEGRAL-001

## Estado

APROBADO — ampliado con estado Profesor y autoalta pública.

## Objeto

Profesor / Docente.

## Problema

La implementación actual de Profesor presenta deuda integral de autorización y
persistencia:

- guards insuficientes en READ, UPDATE y CREATE;
- posible IDOR mediante identificadores recibidos del cliente;
- CREATE regular sin política institucional correcta;
- alta heredada capaz de asignar permiso 3 en lugar de permiso 4;
- falta de atomicidad integral en el alta normal;
- roles administrativos del Profesor sin una operación dedicada;
- riesgo de sobrescritura o coexistencia inválida de permisos;
- DELETE potencialmente destructivo cuando existen roles administrativos;
- ausencia de un estado propio que separe identidad Docente de habilitación
  funcional;
- ruta visual de registro Docente sin una autoalta pública backend operativa;
- permiso 4 interpretado en algunas superficies como acceso Docente pleno.

## Matriz CRUD objetivo

### READ

Admin:
permitido.

Comité:
permitido.

Profesor:
lectura propia cuando corresponda a Mi Perfil.

Estudiante:
únicamente lectura académica mínima necesaria para representar relaciones con
Profesor.

Sin sesión:
rechazo en operaciones protegidas.

No exponer pass.

Los READ sensibles deben aplicar guard por actor y contexto.

### CREATE

Admin y Comité pueden ejecutar el alta administrativa.

Una persona sin sesión puede ejecutar exclusivamente la autoalta pública de
Profesor cuando el formulario haya generado un contexto anónimo válido ligado a
su sesión PHP.

El alta normal de Profesor debe producir una representación válida con:

- login;
- usuario;
- profesor;
- permiso 4;
- relaciones obligatorias vigentes.

No debe asignar permiso 3.

La operación debe quedar protegida antes de la primera escritura y ejecutarse
de forma transaccional para las escrituras obligatorias.

Que Comité cree un Profesor no concede permiso Comité: el nuevo Profesor nace
con permiso 4 y `estado_profesor=2`.

Admin crea Profesor con `estado_profesor=2`.

Comité crea Profesor con `estado_profesor=2`.

La autoalta pública crea Profesor con permiso 4 y `estado_profesor=1`; el backend
no acepta el estado ni permisos administrativos desde el cliente.

Admin y Comité pueden ejecutar el alta administrativa existente de Alumno, sin
alterar sus permisos, estados ni reglas funcionales.

La autoalta pública de Alumno se mantiene para una persona sin sesión que haya
abierto el formulario público. El backend valida un contexto ligado a sesión,
fuerza `tipo_est=1` (Postulante), asigna únicamente el permiso histórico 5 y
finaliza redirigiendo a Login. Un flag cliente aislado no autoriza el alta ni
permite asignar permisos 1, 2 o 4.

La autoalta pública de Profesor debe reutilizar el mismo patrón de seguridad:

- token criptográfico generado por backend;
- sesión PHP anónima;
- TTL;
- comparación mediante `hash_equals`;
- consumo anti-replay;
- backend como autoridad;
- transacción integral;
- `ok=true` sólo después de COMMIT;
- redirección a Login únicamente después del éxito real.

### UPDATE

Admin:
puede editar datos de Profesor.

Comité:
puede editar datos de Profesor.

Profesor:
puede editar exclusivamente sus propios datos mediante Mi Perfil.

El ownership debe validarse en backend.

Los permisos administrativos 1/2 deben permanecer separados de las operaciones
de datos personales y académicos.

### DELETE

Exclusivamente Admin.

Profesor simple [4]:
puede seguir el flujo de eliminación autorizado cuando la identidad sea
inequívoca.

Profesor + Admin [4,1]:
DELETE Profesor rechazado.

Profesor + Comité [4,2]:
DELETE Profesor rechazado.

Auto-DELETE:
prohibido.

Comité:
no puede eliminar Profesor.

Identidades incompatibles:
rechazo.

## Modelo de permisos y estado

El permiso 4 representa identidad Docente.

`profesor.estado_profesor` representa habilitación funcional y no reemplaza ni
elimina el permiso 4.

Representaciones válidas de permisos:

Profesor base:

[4]

Profesor + Admin:

[4,1]

Profesor + Comité:

[4,2]

Estado prohibido:

[4,1,2]

Estados Profesor aprobados:

1 = Pendiente.

2 = Aceptado.

3 = Rechazado.

Combinaciones funcionales:

[4] + Pendiente:
login y Mi Perfil permitidos; acceso académico normal bloqueado.

[4] + Aceptado:
acceso Docente normal.

[4] + Rechazado:
login y Mi Perfil permitidos; acceso académico normal bloqueado.

Las únicas combinaciones válidas con rol administrativo son:

- [4,1] + Aceptado;
- [4,2] + Aceptado.

No se permite Pendiente o Rechazado con permiso 1 o 2.

Sólo Admin puede modificar permisos administrativos 1/2 de un Profesor.

Comité no gestiona permisos administrativos.

Profesor no autogestiona permisos administrativos.

Antes de agregar permiso 1 o 2, la gestión de rol administrativo debe verificar
`estado_profesor=2`.

## Persistencia de estado Profesor

La implementación futura incorporará `profesor.estado_profesor` con:

- `INT(11)`;
- `NOT NULL` en estado final;
- sin DEFAULT;
- sin catálogo adicional;
- CHECK limitado a 1, 2 y 3.

Todo Profesor existente se migra a `estado_profesor=2`.

La migración debe residir en un script SQL versionado específico y seguir este
orden:

1. agregar la columna nullable;
2. poblar registros existentes con 2;
3. convertir la columna a `NOT NULL`;
4. agregar el CHECK.

SQL objetivo, no ejecutado por esta actualización documental:

```sql
ALTER TABLE `profesor`
  ADD COLUMN `estado_profesor` INT(11) NULL AFTER `anio_ingreso`;

UPDATE `profesor`
SET `estado_profesor` = 2
WHERE `estado_profesor` IS NULL;

ALTER TABLE `profesor`
  MODIFY COLUMN `estado_profesor` INT(11) NOT NULL,
  ADD CONSTRAINT `chk_profesor_estado`
    CHECK (`estado_profesor` IN (1, 2, 3));
```

`c1441353_antr_db.sql` no forma parte de la implementación versionable de esta
ampliación.

## Transiciones autorizadas

[4] → [4,1]

[4] → [4,2]

[4,1] → [4,2]

[4,2] → [4,1]

[4,1] → [4]

[4,2] → [4]

Las transiciones deben preservar:

- login;
- usuario;
- profesor;
- permiso 4;
- ficha;
- antecedentes;
- correo;
- password.

Nunca producir [4,1,2].

Si el estado inicial ya contiene simultáneamente permisos 1 y 2:

- rechazar;
- no normalizar automáticamente.

## Transiciones de estado Profesor

Admin y Comité pueden ejecutar las mismas transiciones autorizadas:

- Pendiente → Aceptado;
- Pendiente → Rechazado;
- Rechazado → Aceptado.

No se implementa en esta Task Aceptado → Rechazado.

También se rechazan:

- Aceptado → Pendiente;
- Rechazado → Pendiente;
- cualquier transición fuera de la matriz aprobada.

Una transición inválida debe responder HTTP 409 y `ok=false`.

La operación de estado debe ser específica, estar separada de la gestión de rol
administrativo y modificar exclusivamente `profesor.estado_profesor`. Debe
preservar:

- permisos 1, 2 y 4 existentes;
- login;
- usuario;
- ficha;
- antecedentes.

El endpoint específico autoriza Admin y Comité, y rechaza con 403 a Profesor,
Estudiante y actores sin sesión. No debe utilizar un editor genérico.

La grilla vigente `admin/ver.docente.php` y
`admin/scripts/ver.docente.js` se reutiliza para mostrar Pendiente, Aceptado y
Rechazado. Admin y Comité disponen de Aceptar/Rechazar para Pendiente y Aceptar
para Rechazado; Aceptado no ofrece rechazo en esta Task.

## Política Estudiante

El Estudiante puede:

- consultar información académica básica de Profesores necesaria para su ficha;
- identificar Profesor guía/director u otros Profesores relacionados;
- obtener nombre y datos institucionales necesarios para mostrar esa relación.

El Estudiante no puede:

- acceder mediante read_prof_id a la ficha completa administrativa del Profesor;
- consultar antecedentes privados;
- consultar credenciales;
- consultar datos personales que no sean necesarios para la relación académica;
- solicitar arbitrariamente cualquier Profesor mediante id_usu.

## read_prof_id

read_prof_id se considera READ sensible.

No debe convertirse en un mecanismo general de consulta académica para
Estudiante.

Admin y Comité pueden conservar acceso cuando el contexto autorizado realmente
requiera la ficha de terceros.

Los callers de Estudiante deben utilizar únicamente una lectura académica mínima.

## Lectura académica mínima

La implementación debe preferir:

1. reutilizar read_prof si su contrato es suficiente y seguro;
2. reutilizar otra lectura mínima existente;
3. crear una operación mínima específica sólo si no existe alternativa segura.

El contrato deberá devolver únicamente los campos realmente necesarios.

Como máximo según necesidad funcional:

- identificador técnico requerido por la relación;
- nombre del Profesor;
- información institucional necesaria para mostrar la relación académica.

No ampliar automáticamente el contrato.

El selector central `ajax/docente.php`, `op=read_prof`, debe incluir únicamente
Profesores con `estado_profesor=2`. Esta regla aplica a nuevas asignaciones de
Profesor guía, Curso, Tesis y cualquier otro caller que reutilice el selector.

Pendientes y Rechazados no son seleccionables. Las relaciones históricas deben
seguir visibles mediante sus READ contextuales aunque el Profesor deje de estar
habilitado para nuevas asignaciones.

## Login, sesión y acceso funcional

`$_SESSION['docente']` se conserva para Pendiente, Aceptado y Rechazado porque
los tres mantienen identidad Docente y permiso 4.

El login debe obtener el estado Profesor desde backend y disponer de él en un
contexto validado y/o expresar la capacidad equivalente `docente.habilitado`
exclusivamente cuando `estado_profesor=2`.

No debe reutilizar `$_SESSION['aceptado']`, porque esa clave representa el
permiso histórico 3.

El acceso Docente completo requiere simultáneamente:

- permiso 4;
- `estado_profesor=2`.

Pendiente y Rechazado conservan únicamente Mi Perfil y los auxiliares
estrictamente necesarios para completar su ficha. Las superficies inicialmente
identificadas para exigir habilitación completa son:

- `form-doc/header.php`;
- `form-doc/ver.curso.php`;
- `ajax/curso.php`;
- cualquier otra superficie que durante la inspección final demuestre depender
  exclusivamente del permiso 4.

No se amplía el cambio indiscriminadamente a superficies sin evidencia.

## Mi Perfil por estado

Pendiente, Aceptado y Rechazado pueden ejecutar READ y UPDATE propios conforme a
la política vigente.

El estado no es editable desde Mi Perfil.

Las operaciones `*-perfil` y los endpoints auxiliares estrictamente necesarios
para la ficha no deben exigir `estado_profesor=2`. El Profesor objetivo continúa
resolviéndose desde la sesión backend.

## Seguridad

Debe quedar garantizado:

- guard backend por operación;
- ownership backend;
- no confiar en id_usu/id_login cliente para Mi Perfil;
- Profesor propio resuelto mediante sesión;
- validación de Profesor objetivo para operaciones de terceros;
- ausencia de pass;
- ausencia de editor arbitrario de permisos;
- Comité sin autoridad sobre permisos 1/2;
- Profesor sin autoridad sobre permisos 1/2;
- permiso 3 no representa Docente;
- permiso 4 por sí solo no representa habilitación académica plena;
- estado Profesor resuelto por backend;
- estado y permisos administrativos no aceptados desde cliente;
- selector académico limitado a Profesores Aceptados;
- [4,1,2] rechazado;
- contrato HTTP/JSON consistente.

## Propia fila

Admin/Comité con Docencia no debe ver su propia fila en la grilla Docentes.

Sus datos propios se administran mediante Mi Perfil.

Esta protección frontend no reemplaza los guards backend.

## Fuera de alcance

- hashing de password;
- cambios de schema distintos del script versionado específico para
  `profesor.estado_profesor`;
- migración global de permisos;
- reescritura integral de Login; se admite la lectura mínima del estado Profesor
  si resulta imprescindible;
- redefinición integral de Usuario;
- cambios generales de Authorization;
- incorporación de `c1441353_antr_db.sql`;
- transición Aceptado → Rechazado;
- redefinición integral de otros objetos.

## Deudas no bloqueantes existentes

Se mantienen registradas, sin mezclarlas con `estado_profesor`:

- H-4: READ legacy sin `ok=true`;
- H-5: cambio hacia el mismo rol reescribe permiso;
- fallo de conexión inicial: puede escapar del contrato JSON antes de obtener
  PDO.

## Gobierno

Decisiones institucionales pendientes:

Ninguna.

No se requiere AT adicional. Esta ampliación permanece dentro del mismo objeto
Profesor y de la Task integral vigente.

## Dictamen

Profesor puede cerrarse mediante una única Task integral.
