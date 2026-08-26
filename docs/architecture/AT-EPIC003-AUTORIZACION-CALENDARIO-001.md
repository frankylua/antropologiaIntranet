# AT-EPIC003-AUTORIZACION-CALENDARIO-001

## Autorización integral del objeto Calendario Académico

## 1. Identificación y estado

- **Objeto:** Calendario Académico.
- **EPIC principales:** EPIC-001 — Modernización del modelo de autorización y permisos; EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Coordinación:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Paso del plan EPIC-003:** Paso 5 — resolver `calendario.ver`.
- **Clasificación:** [ARQ] [AUTH] [EPIC-009] [GOV].
- **Estado:** APROBADO.

Este AT consolida la frontera funcional, la matriz CRUD, la autorización y el
alcance técnico futuro del objeto Calendario Académico. Sus decisiones se
sustentan en la inspección integral aprobada, en la matriz institucional vigente
y en el plan aprobado de sustitución de consumidores del permiso histórico `3`.

Este documento no implementa código, no modifica base de datos, no crea una
Task, no modifica el Roadmap o un ADR y no autoriza staging, commit, push ni
ejecución de validación funcional.

## 2. Contexto arquitectónico

Calendario corresponde al paso 5 de la secuencia aprobada para sustituir los
consumidores del permiso histórico `3`. Profesor y Cursos ya fueron cerrados y
proporcionan el contexto técnico necesario para distinguir identidad Docente de
habilitación funcional y para producir capacidades derivadas durante el login.

La autorización local vigente de Calendario continúa expresada como:

~~~text
admin OR comite OR aceptado
~~~

`aceptado` se materializa históricamente desde el permiso `3`. No representa un
estado académico ni un rol institucional y no es autoridad válida en el modelo
objetivo. La capability `calendario.ver` todavía no existe en el contexto de
sesión.

## 3. Fuentes vigentes

Este AT aplica las decisiones contenidas en:

- [AT-EPIC003-PLAN-SUSTITUCION-CONSUMIDORES-PERMISO-003-001](AT-EPIC003-PLAN-SUSTITUCION-CONSUMIDORES-PERMISO-003-001.md);
- [ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001](../governance/ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md);
- [ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001](../governance/ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001.md);
- [ROADMAP](../roadmap/ROADMAP.md), especialmente las reglas de EPIC-009;
- inspección integral aprobada del objeto Calendario Académico sobre el baseline
  `476667b901c2355007f91dda1ec671431d15ead2`.

Las reglas institucionales se toman de las actas oficiales. El código heredado
se utiliza como evidencia del comportamiento actual, no como fuente para
redefinir la matriz aprobada.

## 4. Frontera funcional del objeto

Calendario Académico se define como un **objeto funcional local de lectura**.

La responsabilidad de la intranet es:

~~~text
autenticar
→ derivar autorización
→ permitir navegación
→ renderizar una vista local
→ embeber Google Calendar
~~~

La intranet no:

- almacena eventos;
- crea eventos;
- actualiza eventos;
- elimina eventos;
- sincroniza eventos;
- replica eventos;
- administra la cuenta o la ACL del calendario externo.

La fuente de verdad de los eventos es **Google Calendar**. La vista PHP local y
su navegación son parte del objeto; el ciclo de vida de los eventos y su
administración permanecen en el proveedor externo.

## 5. Superficie técnica confirmada

| Área | Archivo | Responsabilidad |
| --- | --- | --- |
| Productor de sesión | `ajax/login.php` | Autentica, obtiene permisos y estados y materializa `$_SESSION['capacidades']`. |
| Contexto de estado | `src/Model/Login.php` | Proporciona los estados Estudiante y Profesor ya consumidos por el login. |
| Helper | `src/Security/Authorization.php` | Evalúa capabilities y señales de sesión; no requiere modificación. |
| Página | `form-doc/calend.acad.php` | Aplica el guard backend y renderiza el iframe externo. |
| Navegación | `form-doc/header.php` | Muestra el enlace Calendario Académico. |
| Dashboard | `admin/inicio.php` | Muestra a Admin/Comité el botón de Calendario. |
| Navegación cliente | `js/inicio.js` | Convierte el botón del dashboard en navegación hacia la página. |
| Destino inicial | `index.php` | Conserva prioridades históricas de redirección por actor/señal. |
| Carga de scripts | `form-doc/footer.php` | Carga globalmente `js/inicio.js`; no decide autorización. |

No se encontró otra página, endpoint, modelo, formulario o integración activa
del objeto.

## 6. Persistencia y relación con EPIC-008

Para Calendario Académico no existe:

- tabla local de Calendario;
- modelo PHP de Calendario;
- endpoint CRUD de eventos;
- integración con API de Google;
- OAuth;
- token o credencial local;
- filesystem propio del objeto;
- `Documento` asociado;
- migración de base de datos.

La persistencia de los eventos es totalmente externa. EPIC-008 no requiere
intervención para este objeto y la futura Task no debe crear schema, migraciones,
Documento o almacenamiento local.

## 7. Matriz CRUD EPIC-009

| Operación | Estado | Capa local | Justificación |
| --- | --- | --- | --- |
| CREATE | NO APLICA AL CRUD LOCAL | Inexistente | La creación de eventos pertenece al ciclo de vida externo de Google Calendar. |
| READ | EXISTE / DEFECTUOSO | Sesión, guard, navegación, vista e iframe | El embed existe, pero la autorización local no coincide con la matriz institucional. |
| UPDATE | NO APLICA AL CRUD LOCAL | Inexistente | La modificación de eventos se administra fuera de la intranet. |
| DELETE | NO APLICA AL CRUD LOCAL | Inexistente | La eliminación de eventos se administra fuera de la intranet. |

CREATE, UPDATE y DELETE no son operaciones faltantes de la intranet. Su ausencia
está justificada por la frontera del objeto y no habilita la creación artificial
de formularios, endpoints o persistencia local.

READ es defectuoso exclusivamente por la autorización y sus consumidores
locales. La carga externa del calendario ya existe.

## 8. Capability objetivo

Se adopta:

~~~text
calendario.ver
~~~

como capability funcional única para acceder localmente al Calendario
Académico.

`calendario.ver`:

- se deriva durante el login;
- se materializa en `$_SESSION['capacidades']`;
- no es un rol;
- no es un estado académico o institucional;
- no es un permiso histórico;
- no implica ownership sobre eventos;
- no concede administración de Google Calendar;
- puede acumularse con otras capacidades válidas de una misma cuenta.

`src/Security/Authorization.php` no debe modificarse. Su método
`Authorization::hasCapability()` ya implementa el contrato de consumo requerido.

## 9. Matriz institucional de autorización

| Actor o estado | Condición institucional/técnica | `calendario.ver` |
| --- | --- | --- |
| Admin | rol Admin | Sí |
| Comité | rol Comité | Sí |
| Profesor Accepted | `estado_profesor = 2` | Sí |
| Profesor Pending | `estado_profesor = 1` | No |
| Profesor Rejected | `estado_profesor = 3` | No |
| Estudiante Postulante | `tipo_est = 1` | No |
| Estudiante Aceptado | `tipo_est = 2` | Sí |
| Estudiante Matriculado | `tipo_est = 3` | Sí |
| Estudiante Graduado | `tipo_est = 4` | No |
| Estudiante Retirado | `tipo_est = 5` | No |
| Estudiante Eliminado | `tipo_est = 6` | No |
| Estudiante Reprobado | `tipo_est = 7` | No |
| Anónimo | sin sesión autenticada | No |

La autorización es acumulativa. Una cuenta con Admin o Comité obtiene la
capability por ese rol aunque además posea otra identidad autorizada. Una
identidad o permiso adicional no debe retirar una capability válida ni convertir
una señal histórica en autoridad.

## 10. Permiso histórico 3

El permiso `3` y su traducción de sesión `$_SESSION['aceptado']` no son autoridad
válida para Calendario en el modelo objetivo.

Decisión:

~~~text
Calendario deja de consumir aceptado.
~~~

Una cuenta que conserve únicamente permiso `3` no obtiene `calendario.ver` si
su estado o rol institucional vigente no satisface la matriz aprobada.

Durante la futura Task:

- `aceptado` puede continuar materializándose temporalmente para otros
  consumidores heredados;
- no se elimina el permiso `3`;
- no se elimina globalmente la clave `aceptado`;
- no se modifican consumidores ajenos a Calendario;
- no se reinterpreta el permiso `3` como estado académico;
- no se amplía su compatibilidad dentro de Calendario.

La congelación y eliminación estructural del permiso `3`, así como el retiro
global de `aceptado`, permanecen en pasos posteriores del plan EPIC-003.

## 11. Productor de `calendario.ver`

El productor será `ajax/login.php`. Debe reutilizar el contexto ya cargado por
el flujo vigente y conceder la capability cuando se cumpla al menos una de estas
condiciones:

~~~text
Admin
OR Comité
OR tipo_est IN (2, 3)
OR estado_profesor = 2
~~~

El productor debe:

- incorporar `calendario.ver` al catálogo validado de capabilities;
- mantener el arreglo como lista deduplicada y con el contrato vigente;
- validar que su presencia coincida exactamente con la regla derivada;
- reutilizar `$studentStatus`, `$professorState` y los roles ya disponibles;
- no agregar consultas redundantes;
- no conceder la capability por `aceptado` o permiso `3` aislado;
- no alterar la derivación de capabilities de otros objetos.

## 12. Guard backend

El consumidor principal será `form-doc/calend.acad.php`.

La autoridad objetivo es:

~~~php
Authorization::hasCapability('calendario.ver')
~~~

El guard debe dejar de usar `admin`, `comite` y `aceptado` como autoridades
directas, porque los actores válidos quedan representados por la capability
derivada. El acceso por URL directa debe aplicar exactamente esta misma regla.

Cuando el acceso sea rechazado, la respuesta debe redirigir al destino local
vigente y finalizar correctamente la ejecución. La ocultación del enlace nunca
reemplaza este guard backend.

## 13. Navegación de cabecera

`form-doc/header.php` debe mostrar el enlace **Calendario Académico** únicamente
cuando:

~~~php
Authorization::hasCapability('calendario.ver')
~~~

El enlace deja de depender de `aceptado`. La política del menú y la política del
guard backend deben ser equivalentes.

El wrapper general **Programa** conserva su composición vigente. Los actores que
obtienen `calendario.ver` ya disponen de una señal válida para visualizar ese
wrapper mediante sus roles o capacidades existentes; no se autoriza una
reescritura general del menú.

## 14. Dashboard Admin/Comité

`admin/inicio.php` y `js/inicio.js` permanecen sin modificación funcional:

- el dashboard ya está limitado por backend a Admin y Comité;
- ambos actores reciben `calendario.ver`;
- el botón únicamente inicia navegación;
- el JavaScript no constituye autoridad de seguridad.

Ambos archivos son consumers de regresión de la futura Task. Sólo podrán
modificarse si la revisión técnica previa descubre evidencia nueva que invalide
estas condiciones.

## 15. Decisión de navegación para `index.php`

La prioridad histórica vigente es:

~~~text
Admin/Comité → dashboard
aceptado → Calendario
Docente → perfil docente
perfil.ver → perfil estudiante
~~~

No debe reemplazarse mecánicamente `aceptado` por `calendario.ver`. Esa
transformación haría que Profesor Accepted cumpliera la condición anterior a
Docente y cambiara accidentalmente su destino inicial desde el perfil docente
hacia Calendario.

La decisión de este AT es:

- `calendario.ver` gobierna el acceso al objeto;
- `calendario.ver` gobierna la visibilidad del enlace;
- no gobierna de forma genérica el destino inicial de todos sus titulares;
- el destino inicial vigente de Profesor Accepted se conserva;
- `index.php` queda protegido por defecto en la Task de Calendario.

La sustitución global de la redirección asociada a `aceptado` corresponde a una
etapa posterior de saneamiento de sesión y navegación dentro de EPIC-003. La
futura Task sólo podrá modificar `index.php` si una inspección técnica previa
demuestra que es estrictamente necesario para evitar una regresión y dispone de
una transformación mínima que preserve exactamente la prioridad por actor.

## 16. Integración con Google Calendar

La integración confirmada posee estas características:

- proveedor: Google Calendar;
- mecanismo: iframe HTML;
- transporte: HTTPS;
- URL: estática y definida directamente en la vista;
- identificador: calendario de grupo incluido en el parámetro público del embed;
- zona horaria: `America/Santiago`;
- administración de eventos: externa;
- API local: inexistente;
- credenciales locales: inexistentes.

Quedan fuera de alcance:

- crear, modificar o borrar eventos;
- cambiar el calendario externo;
- cambiar su ACL o carácter público/privado;
- cambiar la cuenta propietaria o administradora;
- configurar OAuth, tokens o API de Google;
- introducir sincronización o réplica local.

## 17. ACL y propiedad externa

El repositorio no permite determinar:

- propietario institucional del calendario;
- cuenta administradora;
- política ACL;
- carácter público o privado real del recurso;
- necesidad efectiva de autenticación externa para todos sus usuarios.

Esto se registra como **observación operativa no bloqueante**. No impide definir
ni implementar la autorización local, pero constituye una dependencia externa
pendiente de gobierno institucional. Este AT no atribuye la propiedad o
administración a Admin, Comité ni a otro actor local.

## 18. Seguridad y atributos del iframe

La evidencia local confirma:

- URL HTTPS;
- URL estática;
- ausencia de parámetros procedentes del request;
- ausencia de API key, token o secreto local;
- ausencia de construcción dinámica con datos del usuario;
- ausencia de contenido mixto en el embed.

Se registran como mejoras opcionales y no bloqueantes la evaluación de `title`,
`referrerpolicy` y atributos de carga o accesibilidad.

No se adopta `sandbox` automáticamente. Un sandbox restrictivo podría impedir
el funcionamiento esperado de Google Calendar y requiere una validación
funcional específica. La futura Task no debe ampliarse hacia hardening general
del iframe salvo que la revisión técnica descubra un hallazgo bloqueante.

## 19. Alcance de la futura Task integral

### 19.1. Candidatos modificables

- `ajax/login.php`: producir y validar `calendario.ver`;
- `form-doc/calend.acad.php`: consumir la capability en el guard backend y
  finalizar correctamente el rechazo;
- `form-doc/header.php`: consumir la capability para mostrar el enlace.

### 19.2. Protegido por defecto

- `index.php`: conservar el destino inicial de Profesor Accepted y no ejecutar
  una sustitución mecánica de `aceptado` por `calendario.ver`.

Sólo la revisión técnica previa puede habilitar una modificación mínima de
`index.php` bajo el contrato definido en la sección 15.

### 19.3. Read-only y regresión

- `src/Security/Authorization.php`;
- `src/Model/Login.php`;
- `admin/inicio.php`;
- `js/inicio.js`;
- `form-doc/footer.php`.

### 19.4. Fuera de alcance

- base de datos;
- `migrations/*`;
- modelos, endpoints o formularios CRUD de eventos;
- Documento o filesystem local;
- Google Calendar, sus eventos, configuración, ACL y cuentas;
- consumidores de `aceptado` pertenecientes a otros objetos;
- Roadmap, ADR y arquitectura transversal.

## 20. Contrato de compatibilidad

Durante la futura Task:

- Calendario deja de consumir permiso `3` y `aceptado`;
- otros módulos pueden continuar consumiendo `aceptado` temporalmente;
- la clave de sesión no se elimina;
- no cambia la semántica de otros objetos;
- no se concede `calendario.ver` a estados no autorizados;
- la capability no decide administración externa;
- Admin, Comité, Profesor Accepted y Estudiante Aceptado/Matriculado obtienen la
  misma capability funcional por reglas independientes y verificables;
- una cuenta con permiso `3` aislado no obtiene compatibilidad de acceso a
  Calendario.

## 21. Validación funcional futura

La VF será integral, posterior a la implementación y ejecutada exclusivamente
por el usuario.

| Caso | Capability | Enlace | URL directa | Resultado |
| --- | --- | --- | --- | --- |
| Admin | Sí | Visible | Permitida | Iframe visible y calendario cargado. |
| Comité | Sí | Visible | Permitida | Iframe visible y calendario cargado. |
| Profesor Accepted | Sí | Visible | Permitida | Calendario accesible; destino inicial continúa en perfil docente. |
| Profesor Pending | No | Oculto | Bloqueada | Sin iframe. |
| Profesor Rejected | No | Oculto | Bloqueada | Sin iframe. |
| Estudiante Postulante | No | Oculto | Bloqueada | Sin iframe. |
| Estudiante Aceptado | Sí | Visible | Permitida | Iframe visible y calendario cargado. |
| Estudiante Matriculado | Sí | Visible | Permitida | Iframe visible y calendario cargado. |
| Estudiante Graduado | No | Oculto | Bloqueada | Sin iframe. |
| Estudiante Retirado | No | Oculto | Bloqueada | Sin iframe. |
| Estudiante Eliminado | No | Oculto | Bloqueada | Sin iframe y sin acceso indebido. |
| Estudiante Reprobado | No | Oculto | Bloqueada | Sin iframe. |
| Sólo permiso histórico 3 | No, salvo otro estado/rol válido | Oculto | Bloqueada | `aceptado` puede coexistir, pero no autoriza Calendario. |
| Anónimo | No | No disponible | Bloqueada | Redirección local sin iframe. |

La VF también debe confirmar:

- igualdad entre política de menú y guard backend;
- acceso directo independiente de la visibilidad del menú;
- carga real del iframe HTTPS para actores autorizados;
- ausencia de capacidades adicionales no aprobadas;
- sesión deduplicada y validada;
- ausencia de CREATE, UPDATE y DELETE locales;
- ausencia de cambios accidentales en Admin/Comité, Cursos, Reglamento, perfiles
  y destinos iniciales;
- conservación temporal de `aceptado` para consumidores ajenos a Calendario.

No corresponde probar CREATE, UPDATE o DELETE sobre Google Calendar.

## 22. Criterios de aceptación arquitectónica

El AT se considera completo cuando quedan aprobadas inequívocamente estas
decisiones:

1. Calendario es un objeto funcional local de lectura.
2. Google Calendar es la fuente de verdad externa.
3. CREATE, UPDATE y DELETE no aplican al CRUD local.
4. READ existe y es defectuoso por autorización local.
5. `calendario.ver` es la capability funcional única.
6. La matriz por actor y estado es completa.
7. Permiso `3` y `aceptado` dejan de ser autoridad de Calendario.
8. `ajax/login.php` produce la capability desde el contexto ya disponible.
9. `form-doc/calend.acad.php` aplica el guard backend por capability.
10. `form-doc/header.php` muestra el enlace por la misma capability.
11. `index.php` no recibe una sustitución mecánica que cambie el destino de
    Profesor Accepted.
12. No se crean persistencia, Documento ni migraciones.
13. La futura Task queda delimitada a los archivos sustentados por evidencia.
14. La VF integral queda definida y será ejecutada por el usuario.
15. La propiedad y ACL externas quedan como observación operativa no bloqueante.

## 23. Condiciones de detención para la futura Task

La ejecución deberá detenerse si la revisión técnica descubre que:

1. existe otro CRUD local de Calendario no inventariado;
2. la integración usa una API, credencial o sincronización no detectada;
3. implementar la capability exige nueva persistencia o migración;
4. la matriz institucional resulta contradictoria;
5. se requiere redefinir el destino institucional de un actor;
6. `index.php` sólo puede modificarse alterando la prioridad vigente de Profesor;
7. se necesita modificar un ADR o el Roadmap;
8. el alcance exige intervenir Google Calendar, su cuenta o su ACL;
9. se requiere modificar un archivo protegido sin evidencia y aprobación previa.

## 24. Gobierno documental

Este AT se clasifica como:

- **[ARQ]:** frontera funcional, integración y capability;
- **[AUTH]:** matriz y sustitución del permiso histórico;
- **[EPIC-009]:** clasificación CRUD integral y operaciones No aplica;
- **[GOV]:** dependencia operativa externa de Google Calendar.

No se requiere ADR porque las decisiones permanecen dentro de la arquitectura
ya aprobada para capacidades derivadas, autorización centralizada, separación de
estado/rol/permiso y consolidación integral por objeto.

No se requiere addendum. Este documento constituye el AT integral específico de
Calendario Académico.

## 25. Dictamen

La frontera funcional, la matriz CRUD, la autorización, la compatibilidad
histórica, la navegación, la ausencia de persistencia local, la dependencia
externa y la VF futura están determinadas con evidencia suficiente.

No existe una decisión institucional pendiente que bloquee la autorización
local. La propiedad y ACL de Google Calendar permanecen como observación
operativa no bloqueante.

Después de su revisión y aprobación documental, este AT habilita la creación de
una única Task integral del objeto Calendario Académico.
