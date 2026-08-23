# TASK-EPIC001-AUTORIZACION-PROFESOR-INTEGRAL-001

## Estado

APROBADA PARA REVISIÓN TÉCNICA PREVIA.

Implementación:
No iniciada.

VF:
Pendiente.

## Objetivo

Cerrar integralmente el objeto Profesor incluyendo:

- CRUD;
- autorización;
- ownership;
- cierre de IDOR;
- CREATE normal;
- Mi Perfil;
- UPDATE de terceros;
- DELETE seguro;
- roles administrativos acumulativos;
- lectura académica mínima para Estudiante;
- estado propio de Profesor;
- autoalta pública segura de Profesor;
- aceptación/rechazo institucional;
- acceso Docente condicionado a habilitación funcional;
- preservación de relaciones y antecedentes.

No dividir por operación CRUD.

## Política institucional

### Permiso y estado Profesor

El permiso 4 representa identidad Docente.

`profesor.estado_profesor` representa habilitación funcional y no reemplaza el
permiso 4.

Estados aprobados:

- 1 = Pendiente;
- 2 = Aceptado;
- 3 = Rechazado.

Representaciones válidas de permisos:

Profesor:

[4]

Profesor + Admin:

[4,1]

Profesor + Comité:

[4,2]

Prohibido:

[4,1,2]

Combinaciones funcionales:

- [4] + Pendiente: login y Mi Perfil; acceso académico normal bloqueado;
- [4] + Aceptado: acceso Docente normal;
- [4] + Rechazado: login y Mi Perfil; acceso académico normal bloqueado;
- [4,1] + Aceptado;
- [4,2] + Aceptado.

No permitir Pendiente/Rechazado con permiso 1 o 2.

### Admin

Puede:

- READ Profesor;
- CREATE Profesor;
- CREATE Alumno;
- UPDATE Profesor;
- DELETE Profesor según reglas;
- asignar Admin;
- asignar Comité;
- cambiar Admin ↔ Comité;
- retirar rol administrativo adicional;
- ejecutar Pendiente → Aceptado;
- ejecutar Pendiente → Rechazado;
- ejecutar Rechazado → Aceptado.

### Comité

Puede:

- READ Profesor;
- CREATE Profesor;
- CREATE Alumno;
- UPDATE Profesor;
- ejecutar Pendiente → Aceptado;
- ejecutar Pendiente → Rechazado;
- ejecutar Rechazado → Aceptado.

No puede:

- DELETE Profesor;
- modificar permisos 1/2.

### Profesor

Puede:

- iniciar sesión con permiso 4 en cualquiera de los tres estados;
- consultar y editar sus propios datos mediante Mi Perfil.

Sólo cuando `estado_profesor=2` puede acceder a la funcionalidad académica
Docente normal.

No puede:

- crear Profesor;
- eliminar Profesor;
- modificar permisos 1/2 propios o ajenos.

### Estudiante

Puede:

- consultar información académica básica necesaria de Profesor;
- identificar Profesor guía/director u otra relación académica;
- obtener nombre y datos institucionales mínimos necesarios.

No puede:

- acceder a ficha administrativa completa mediante read_prof_id;
- consultar antecedentes privados;
- consultar credenciales;
- consultar datos personales innecesarios;
- solicitar arbitrariamente cualquier Profesor mediante id_usu.

### Sin sesión

No puede ejecutar operaciones protegidas de Profesor.

Puede iniciar exclusivamente la autoalta pública de Profesor desde el formulario
que genere un contexto anónimo válido ligado a sesión PHP.

Backend es siempre la autoridad.

## READ

Revisar individualmente:

- read_prof;
- read-doc;
- read-filtrada;
- read_prof_id;
- read_prof_perfil;
- read_prof_prog.

No aplicar un guard global ciego.

Clasificar cada READ según propósito:

- selector académico;
- listado administrativo;
- ficha de terceros;
- Mi Perfil;
- dato auxiliar;
- lectura sensible.

### read_prof_id

Debe tratarse como lectura sensible.

No habilitarlo a Estudiante como mecanismo general de consulta.

Admin y Comité pueden utilizarlo cuando el contexto autorizado requiera realmente
datos completos de Profesor.

El objetivo recibido mediante id_usu debe validarse en backend.

Una petición con id_usu arbitrario sin actor y contexto autorizados debe ser
rechazada.

### Mi Perfil

El Profesor objetivo debe resolverse mediante la sesión backend.

No confiar en id_usu o id_login enviados por cliente.

### Estudiante

Los contextos de Estudiante deben consumir únicamente lectura académica mínima.

Preferencia técnica:

1. read_prof si es suficiente;
2. otra lectura mínima existente;
3. nueva operación mínima sólo si es imprescindible.

No exponer información administrativa completa.

### read_nom_prof

Reutilizar ajax/estudiante.php con op=read_nom_prof como lectura contextual mínima
del Profesor guía.

Contrato máximo permitido:

- id_usuario, sólo cuando el caller realmente lo necesite;
- nombres;
- ap_pat;
- ap_mat.

Mi Perfil Estudiante:

- resolver el Estudiante desde la sesión;
- derivar su Profesor guía en backend;
- no aceptar un Profesor objetivo enviado por cliente.

Admin/Comité viendo Estudiante:

- validar el actor;
- validar el Estudiante observado;
- obtener su prof_guia;
- derivar el Profesor relacionado.

La relación debe validarse mediante:

estudiante.usuario → estudiante.prof_guia → profesor.usuario.

No aceptar directamente un id de Profesor arbitrario como autoridad de la
consulta.

### read_prof

Mantener ajax/docente.php con op=read_prof como selector académico mínimo.

Debe devolver únicamente Profesores con `estado_profesor=2`.

Contrato permitido:

- id_usuario;
- nombres;
- ap_pat;
- ap_mat.

No incluir vinculo ni id_login cuando el caller actúe como selector académico.

La búsqueda y el listado administrativo de Docentes deben utilizar una operación
administrativa autorizada distinta.

Pendientes y Rechazados no pueden utilizarse en nuevas asignaciones de Profesor
guía, Curso, Tesis u otros callers del selector central.

Los READ contextuales de relaciones históricas deben seguir mostrando al
Profesor relacionado aunque posteriormente deje de estar Aceptado.

## Callers obligatorios

Revisar y adaptar cuando corresponda:

admin/scripts/ver.estudiante.js

form-doc/scripts/info.estudiante.js

form-doc/scripts/ficha.estudiante.js

Los tres callers deben utilizar read_nom_prof contextual y no read_prof_id.

Mantener sin cambios si continúan consumiendo únicamente el selector mínimo:

form-doc/scripts/estudiante.js

form-doc/scripts/curso.js

Modificar:

form-doc/scripts/tesis.js

Tesis debe consumir el read_prof académico centralizado de ajax/docente.php.

La operación duplicada ajax/tesis.php con op=read_prof debe eliminarse como bypass
funcional. No mantener dos contratos independientes para el mismo selector.

No modificar callers que ya sean seguros.

## CREATE Profesor desde cero

### Alta administrativa

Admin y Comité.

El guard debe ejecutarse antes de la primera escritura.

Comité:
permitido.

Profesor:
403.

Estudiante:
403.

Sin sesión:
403 en la ruta administrativa.

El resultado válido debe incluir:

- login;
- usuario;
- profesor;
- permiso 4;
- `estado_profesor=2`;
- relaciones obligatorias vigentes.

No utilizar permiso 3.

### Autoalta pública

Flujo objetivo:

Sin sesión
→ Registrarme
→ Docente
→ completar formulario
→ crear login/usuario/profesor
→ permiso 4
→ `estado_profesor=1`
→ éxito real
→ Login.

Debe reutilizar el patrón seguro de autoalta Estudiante:

- token criptográfico generado por backend;
- sesión PHP anónima;
- TTL;
- `hash_equals`;
- consumo anti-replay;
- backend como autoridad;
- transacción integral;
- `ok=true` sólo después de COMMIT.

El backend fuerza permiso 4 y estado Pendiente. No acepta desde el cliente:

- estado Profesor;
- permiso Admin;
- permiso Comité;
- ningún otro permiso.

Un POST sin contexto público válido debe rechazarse antes de la primera
escritura.

## CREATE Alumno

Admin y Comité pueden ejecutar el alta administrativa existente.

Profesor y Estudiante autenticado:
403.

No se modifican permisos, estados, identidad ni persistencia del alta Alumno.

La autoalta pública sin sesión se mantiene únicamente desde el formulario
público con contexto respaldado por sesión. El backend:

- valida y consume un token de contexto antes de la primera escritura;
- fuerza `tipo_est=1` (Postulante);
- fuerza el permiso histórico 5;
- no acepta permisos 1, 2 o 4 desde el payload;
- rechaza un POST sin contexto válido;
- redirige a Login después del alta exitosa.

El alta administrativa de Admin/Comité conserva el contrato vigente y puede
utilizar los estados permitidos por su formulario.

## Schema y migración Profesor

La implementación futura debe crear un script SQL versionado específico. No debe
incorporar ni versionar `c1441353_antr_db.sql`.

Campo objetivo:

`profesor.estado_profesor INT(11) NOT NULL`

Reglas:

- sin DEFAULT;
- sin catálogo adicional;
- CHECK limitado a 1, 2 y 3;
- todo Profesor existente migra a estado 2.

Orden obligatorio:

1. agregar columna nullable;
2. actualizar existentes a 2;
3. convertir a `NOT NULL`;
4. agregar CHECK.

SQL objetivo, todavía no ejecutado:

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

## Atomicidad CREATE

Las escrituras obligatorias del alta normal deben quedar coordinadas
transaccionalmente.

Debe evaluarse especialmente la creación contextual de Institución que actualmente
puede ejecutarse antes de insert-update.

No dejar escrituras huérfanas previas si falla el alta Profesor.

Ante fallo obligatorio:

rollback.

Esta atomicidad incluye la autoalta pública Profesor y la asignación explícita
de `estado_profesor`.

## Admin/Comité → Docencia

El flujo ya implementado en el objeto anterior debe preservarse.

No reimplementarlo.

Debe continuar utilizando:

- mismo id_login;
- usuario/profesor;
- permiso 4;
- permiso administrativo 1 o 2 preservado;
- `estado_profesor=2`;
- eliminación de admin sólo al completar correctamente la transición.

Agregar Docencia a una cuenta Admin o Comité no requiere aprobación posterior.

## UPDATE

Separar los contextos:

### Admin

Puede editar terceros.

### Comité

Puede editar terceros.

### Profesor

Sólo propio mediante Mi Perfil.

El backend debe validar el objetivo.

No confiar únicamente en identificadores enviados por cliente.

Los UPDATE de datos personales/programa no deben modificar permisos 1/2.

Password continúa siendo opcional donde corresponda.

### Mi Perfil por estado

Pendiente:
READ y UPDATE propios permitidos.

Aceptado:
READ y UPDATE propios permitidos.

Rechazado:
READ y UPDATE propios permitidos.

El estado no puede editarse desde Mi Perfil.

Las operaciones `*-perfil` y auxiliares estrictamente necesarios para completar
la ficha no deben bloquearse por no estar Aceptado. El ownership continúa
resolviéndose desde backend.

## Login y acceso Docente normal

El login conserva `$_SESSION['docente']` para los tres estados porque todos
mantienen permiso 4 e identidad Docente.

Debe obtener el estado Profesor desde backend y/o expresar la capacidad
`docente.habilitado` únicamente para `estado_profesor=2`.

No reutilizar `$_SESSION['aceptado']`, correspondiente al permiso histórico 3.

Acceso académico completo:

permiso 4 + `estado_profesor=2`.

Pendiente/Rechazado:

únicamente Mi Perfil y auxiliares necesarios.

Superficies inicialmente identificadas para revisión:

- `form-doc/header.php`;
- `form-doc/ver.curso.php`;
- `ajax/curso.php`;
- cualquier otra superficie realmente encontrada que dependa exclusivamente de
  permiso 4.

No ampliar indiscriminadamente sin inspección.

## Gestión de rol administrativo

Implementar una operación específica para Profesor.

Sólo Admin.

Estado objetivo limitado a:

- Ninguno;
- Administrador;
- Comité Académico.

No aceptar arrays arbitrarios de permisos.

El control debe aparecer únicamente en contexto administrativo de edición de
Profesor por Admin.

No mostrarlo en Mi Perfil.

Comité no debe poder operarlo.

Antes de agregar permiso 1 o 2 debe verificarse `estado_profesor=2`.

## Transiciones de rol administrativo

Ninguno → Admin:

[4] → [4,1]

Ninguno → Comité:

[4] → [4,2]

Admin → Comité:

[4,1] → [4,2]

Comité → Admin:

[4,2] → [4,1]

Admin → Ninguno:

[4,1] → [4]

Comité → Ninguno:

[4,2] → [4]

Toda transición debe ser atómica.

Preservar siempre:

- permiso 4;
- login;
- usuario;
- profesor;
- ficha;
- antecedentes;
- correo;
- password.

Nunca producir [4,1,2].

## Gestión de estado Profesor

Reutilizar:

- `admin/ver.docente.php`;
- `admin/scripts/ver.docente.js`.

La grilla debe diferenciar Pendiente, Aceptado y Rechazado.

Acciones disponibles para Admin y Comité:

- Pendiente: Aceptar o Rechazar;
- Rechazado: Aceptar;
- Aceptado: sin acción de rechazo en esta Task.

`ajax/docente.php` debe incorporar una operación específica de estado separada
de `update-rol-admin` y respaldada por un método específico de `Docente`.

Autorización del endpoint:

- Admin: permitido;
- Comité: permitido;
- Profesor: 403;
- Estudiante: 403;
- sin sesión: 403.

Transiciones permitidas:

- 1 → 2;
- 1 → 3;
- 3 → 2.

Transiciones no permitidas:

- 2 → 3;
- 2 → 1;
- 3 → 1;
- cualquier otra fuera de la matriz.

Una transición no permitida debe responder HTTP 409 y `ok=false`.

La persistencia modifica exclusivamente `profesor.estado_profesor`. No modifica:

- permisos 1, 2 o 4;
- login;
- usuario;
- ficha;
- antecedentes.

## Invariantes

Antes de la transición:

- Profesor debe existir;
- usuario/profesor deben representar una identidad válida;
- permiso 4 debe existir;
- como máximo puede existir uno entre permiso 1 y permiso 2.
- para agregar permiso 1 o 2, `estado_profesor` debe ser 2.

Si existen simultáneamente 1 y 2:

rechazar.

No normalizar automáticamente.

Después de la transición:

el estado debe ser exactamente uno de:

[4]
[4,1]
[4,2]

Toda identidad [4,1] o [4,2] debe permanecer en estado Aceptado.

No destruir otros permisos fuera del alcance sin decisión explícita.

## Autorrol

Un Admin+Docente no puede modificar su propio rol administrativo mediante una
petición directa.

La autofila excluida de la grilla no reemplaza este guard backend.

Comité tampoco puede modificar roles.

## DELETE

Mantener la contención vigente.

Sólo Admin.

Profesor simple con permisos compatibles exactamente con [4]:

eliminable si la identidad es inequívoca.

Profesor + Admin:

rechazo.

Profesor + Comité:

rechazo.

[4,1,2]:

rechazo.

Comité:

403.

Profesor:

rechazo.

Auto-DELETE:

rechazo.

Permiso 3:

no habilita DELETE Profesor.

No cambiar schema/cascadas en esta Task.

## Propia fila

Admin/Comité con Docencia:

no ve su propio registro en grilla Docentes.

Mi Perfil continúa siendo la vía de autoedición.

Otro actor autorizado sí puede visualizar/editar ese Profesor.

## Contrato HTTP/JSON

Éxito:

HTTP 2xx
ok=true

Error:

HTTP 4xx/5xx
ok=false

No presentar fallos funcionales o técnicos como éxito.

No filtrar detalles sensibles.

## Alcance de archivos

### Alcance confirmado previo

ajax/docente.php

src/Model/Docente.php

admin/scripts/ver.docente.js

form-doc/scripts/ficha.docente.js

form-doc/scripts/docente.js

form-doc/docente.php

form-doc/estudiante.php

form-doc/info.docente.php

admin/scripts/ver.estudiante.js

form-doc/scripts/info.estudiante.js

ajax/estudiante.php

src/Model/Estudiante.php

form-doc/scripts/ficha.estudiante.js

form-doc/scripts/tesis.js

ajax/tesis.php

Cantidad confirmada:

15 archivos.

`form-doc/estudiante.php` se incorpora al alcance por el hallazgo de VF sobre
CREATE Alumno para Comité.

### Impacto potencial de estado Profesor y autoalta

La revisión técnica final debe confirmar el conjunto mínimo antes de declarar
estos archivos modificables obligatorios.

Schema/migración:

- nuevo script SQL versionado específico.

Modelo:

- `src/Model/Docente.php`;
- `src/Model/Login.php`, sólo si resulta imprescindible para leer el estado.

Endpoints:

- `ajax/docente.php`;
- `ajax/login.php`;
- `ajax/curso.php`.

Autoalta:

- `form-doc/docente.php`;
- `form-doc/scripts/docente.js`.

Autorización/UI:

- `form-doc/header.php`;
- `form-doc/ver.curso.php`;
- `admin/perfil.php`;
- `admin/ver.docente.php`;
- `admin/scripts/ver.docente.js`.

Mi Perfil:

- preservar `form-doc/info.docente.php` y las operaciones `*-perfil`.

Selectores:

- `Docente::buscarProf()` debe devolver únicamente Aceptados.

No incluir `c1441353_antr_db.sql` como archivo versionable.

## Archivos revisados sin cambio esperado

No modificar salvo evidencia técnica nueva:

form-doc/scripts/info.docente.js

form-doc/datos.pers.php

form-doc/scripts/usuario.js

form-doc/scripts/estudiante.js

form-doc/scripts/curso.js

src/Model/Tesis.php

## Archivos protegidos

No modificar salvo bloqueo técnico explícito aprobado:

src/Model/Usuario.php

src/Security/Authorization.php

ajax/admin.php

src/Model/Admin.php

Schema:
únicamente mediante el futuro script versionado específico de
`profesor.estado_profesor`; no ejecutar ni modificar el schema durante esta
actualización documental.

Composer:
sin cambios.

## Criterios de aceptación READ / Estudiante

### read_prof_id

- permanece como READ sensible;
- Estudiante no puede invocarlo para obtener ficha Profesor;
- Admin/Comité conservan acceso autorizado a terceros;
- id_usu arbitrario sin actor/contexto válido es rechazado.

### read_nom_prof

- Mi Perfil Estudiante deriva objetivo desde sesión;
- Admin/Comité consultan Profesor guía a través del Estudiante observado;
- no acepta Profesor arbitrario como objetivo;
- devuelve únicamente contrato académico mínimo.

### read_prof

- mantiene contrato académico mínimo;
- no contiene id_login si no existe necesidad funcional;
- no contiene vinculo si no existe necesidad funcional;
- selectores Estudiante y Curso siguen funcionando;
- Tesis reutiliza el mismo selector.

### ajax/tesis.php

- no queda bypass alternativo de read_prof;
- no queda operación paralela sin guard para selector Profesor.

### Reglas generales

- Estudiante no obtiene ficha administrativa completa;
- no se exponen antecedentes privados;
- no se exponen credenciales;
- no se expone pass;
- Mi Perfil Profesor mantiene ownership por sesión.

## VF futura

VF será realizada exclusivamente por el usuario.

### Autoalta Profesor

- registro público funciona;
- obtiene permiso 4;
- obtiene estado Pendiente;
- `ok=true` sólo después de COMMIT;
- vuelve a Login;
- inicia sesión;
- sólo Mi Perfil.

### Estado Pendiente

- puede consultar y editar Mi Perfil;
- no accede a funciones académicas;
- no aparece en selectores.

### Gestión por Comité

- ve solicitudes Pendientes;
- acepta Pendiente;
- rechaza Pendiente;
- acepta Rechazado;
- una acción de estado no modifica permisos 1/2.

### Gestión por Admin

- ejecuta las mismas transiciones de estado que Comité;
- conserva su gestión administrativa previa;
- una acción de estado no modifica permisos 1/2.

### Estado Aceptado

- conserva login;
- conserva permiso 4;
- obtiene acceso Docente normal;
- aparece en selectores;
- no ofrece transición a Rechazado en esta Task.

### Estado Rechazado

- conserva login;
- conserva permiso 4;
- puede consultar y editar Mi Perfil;
- no accede a funciones académicas;
- no aparece en selectores.

### Altas administrativas y estado

- Admin crea Profesor Aceptado;
- Comité crea Profesor Aceptado;
- Agregar Docencia crea Profesor Aceptado.

### Estado y roles

- sólo Aceptado puede adquirir permiso Admin o Comité;
- Pendiente/Rechazado + permiso 1 o 2 son rechazados;
- [4,1] y [4,2] conservan estado Aceptado.

### CRUD Profesor

- CREATE por Admin;
- CREATE por Comité;
- READ Admin;
- READ Comité;
- UPDATE Admin;
- UPDATE Comité;
- Mi Perfil propio;
- DELETE Admin.

### Autorización

- Comité puede CREATE Profesor y Alumno;
- Comité no DELETE;
- Comité no gestiona permisos administrativos de Profesor;
- Profesor no CREATE;
- Profesor no DELETE;
- sin sesión rechazado;
- IDOR READ bloqueado;
- IDOR UPDATE bloqueado.

### Roles

- [4] → [4,1];
- [4] → [4,2];
- [4,1] → [4,2];
- [4,2] → [4,1];
- [4,1] → [4];
- [4,2] → [4];
- [4,1,2] rechazado;
- Comité no modifica roles;
- Profesor no modifica roles;
- autorrol rechazado.

### Preservación

En transición:

- mismo login;
- mismo usuario;
- mismo profesor;
- permiso 4;
- ficha;
- antecedentes.

### DELETE

- Profesor simple eliminable;
- Profesor+Admin bloqueado;
- Profesor+Comité bloqueado;
- auto-DELETE bloqueado.

### Estudiante

- visualiza Profesor guía/director relacionado;
- aparecen sólo datos académicos necesarios;
- manipular id_usu no entrega ficha administrativa completa;
- no obtiene antecedentes privados;
- no obtiene credenciales/pass.

### Ficha Estudiante — Admin

- muestra Profesor guía correctamente;
- utiliza relación del Estudiante observado.

### Ficha Estudiante — Comité

- muestra Profesor guía correctamente;
- no puede consultar Profesor arbitrario.

### Mi Perfil Estudiante

- muestra Profesor guía;
- objetivo Estudiante proviene de sesión;
- manipular identificadores cliente no cambia el Profesor consultado.

### read_prof_id

- Admin autorizado funciona;
- Comité autorizado funciona;
- Estudiante recibe rechazo;
- sin sesión recibe rechazo.

### Selectores

- Estudiante conserva selector Profesor;
- Curso conserva selector Profesor;
- Tesis conserva selector Profesor mediante ajax/docente.php;
- todos los selectores anteriores incluyen únicamente Profesores Aceptados;
- relaciones históricas con Pendientes/Rechazados conservan visualización;
- ruta duplicada de ajax/tesis.php no constituye bypass.

### Regresión

- selector Profesor en Estudiante;
- selector Profesor en Curso;
- selector Profesor en Tesis;
- visualización Profesor desde ficha Estudiante;
- Mi Perfil Estudiante con relaciones Profesor;
- Admin/Comité → Docencia sigue funcionando;
- login y ficha Profesor continúan funcionando.

## Riesgos

R1 — IDOR READ.

R2 — IDOR UPDATE.

R3 — alta con permiso 3.

R4 — alta parcial.

R5 — sobrescribir permiso 4.

R6 — generar [4,1,2].

R7 — Comité gestionando roles.

R8 — Profesor autogestionando roles.

R9 — DELETE destructivo con rol administrativo.

R10 — romper Mi Perfil.

R11 — exponer ficha completa a Estudiante.

R12 — romper transición Admin/Comité → Docencia.

R13 — tratar permiso 4 como habilitación funcional sin verificar estado 2.

R14 — aceptar estado o permisos administrativos desde la autoalta cliente.

R15 — permitir Pendiente/Rechazado en nuevos selectores académicos.

R16 — producir Pendiente/Rechazado con permiso 1 o 2.

R17 — alterar permisos, login o ficha durante una transición de estado.

## Deudas no bloqueantes existentes

H-4:
READ legacy sin `ok=true`.

H-5:
cambio hacia el mismo rol reescribe permiso.

Fallo de conexión inicial:
puede escapar del contrato JSON antes de obtener PDO.

Estas deudas permanecen registradas y no se mezclan con
`estado_profesor`.

## Condiciones para detener la implementación

Detener si:

1. una fuente aprobada no está disponible;
2. AT y Task se contradicen;
3. la migración requiere un cambio de schema distinto de
   `profesor.estado_profesor`;
4. ownership no puede verificarse en backend;
5. transición de roles requiere reconstruir todos los permisos;
6. permiso 4 no puede preservarse;
7. leer el estado exige una reescritura integral de Login en lugar de una
   adaptación mínima;
8. se necesita modificar Usuario.php;
9. se necesita modificar Authorization.php;
10. aparece una ruta activa de permisos fuera del alcance;
11. no puede ofrecerse una lectura académica mínima segura al Estudiante;
12. aparece una decisión institucional nueva;
13. Profesor deja de ser cerrable como una unidad integral.

## Gobierno

Decisiones institucionales pendientes:

Ninguna.

Objeto:

Profesor.

Unidad:

Task integral única.

AT adicional:

No requerido. El estado Profesor y la autoalta pública permanecen dentro del
mismo objeto y de esta Task integral.
