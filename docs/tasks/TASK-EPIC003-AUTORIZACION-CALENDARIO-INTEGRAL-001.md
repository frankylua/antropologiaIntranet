# TASK-EPIC003-AUTORIZACION-CALENDARIO-INTEGRAL-001

## Autorización integral del Calendario Académico

## 1. Estado

~~~text
Task: CERRADA
Implementación: COMPLETADA
Revisión técnica: APROBADA
Validación funcional: APROBADA POR EL USUARIO
~~~

Esta Task cerró la implementación integral y supervisada del READ local del
objeto Calendario Académico. El commit y el push permanecen pendientes de una
autorización posterior.

## 2. Objetivo

Corregir la autorización local de Calendario Académico mediante la capability
funcional única:

~~~text
calendario.ver
~~~

La implementación debe alinear:

~~~text
productor en login
→ capability de sesión
→ guard backend
→ visibilidad del enlace
~~~

No debe ampliar el objeto con operaciones de escritura, persistencia local,
administración externa o saneamiento general de navegación.

## 3. Fuente aprobada

La fuente arquitectónica exacta y obligatoria es:

- [AT-EPIC003-AUTORIZACION-CALENDARIO-001](../architecture/AT-EPIC003-AUTORIZACION-CALENDARIO-001.md).

Fuentes complementarias vigentes:

- [AT-EPIC003-PLAN-SUSTITUCION-CONSUMIDORES-PERMISO-003-001](../architecture/AT-EPIC003-PLAN-SUSTITUCION-CONSUMIDORES-PERMISO-003-001.md);
- [ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001](../governance/ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md);
- [ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001](../governance/ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001.md).

La ejecución no debe reconstruir ni reinterpretar reglas institucionales desde
el código heredado.

## 4. Alcance funcional y CRUD EPIC-009

| Operación | Estado | Tratamiento en esta Task |
| --- | --- | --- |
| CREATE | NO APLICA AL CRUD LOCAL | No crear formularios, endpoints ni persistencia. |
| READ | EXISTE / DEFECTUOSO | Corregir productor, guard backend y visibilidad del enlace. |
| UPDATE | NO APLICA AL CRUD LOCAL | No implementar. |
| DELETE | NO APLICA AL CRUD LOCAL | No implementar. |

CREATE, UPDATE y DELETE pertenecen al ciclo de vida externo de Google Calendar
y no constituyen operaciones faltantes de la intranet.

## 5. Resultado objetivo y matriz

| Actor o estado | Condición | Resultado |
| --- | --- | --- |
| Admin | rol Admin | Obtiene `calendario.ver`. |
| Comité | rol Comité | Obtiene `calendario.ver`. |
| Profesor Accepted | `estado_profesor = 2` | Obtiene `calendario.ver`. |
| Profesor Pending | `estado_profesor = 1` | No obtiene `calendario.ver`. |
| Profesor Rejected | `estado_profesor = 3` | No obtiene `calendario.ver`. |
| Estudiante Postulante | `tipo_est = 1` | No obtiene `calendario.ver`. |
| Estudiante Aceptado | `tipo_est = 2` | Obtiene `calendario.ver`. |
| Estudiante Matriculado | `tipo_est = 3` | Obtiene `calendario.ver`. |
| Estudiante Graduado | `tipo_est = 4` | No obtiene `calendario.ver`. |
| Estudiante Retirado | `tipo_est = 5` | No obtiene `calendario.ver`. |
| Estudiante Eliminado | `tipo_est = 6` | No obtiene `calendario.ver`. |
| Estudiante Reprobado | `tipo_est = 7` | No obtiene `calendario.ver`. |
| Permiso histórico 3 aislado | `aceptado` sin estado/rol válido | No obtiene `calendario.ver`. |
| Anónimo | sin autenticación | No obtiene acceso. |

Las capacidades válidas son acumulativas. Un rol Admin o Comité concede la
capability aunque la cuenta tenga además otra identidad. El permiso histórico
`3` no constituye una condición de concesión.

## 6. Archivos modificables

La implementación puede modificar únicamente:

1. `ajax/login.php`;
2. `form-doc/calend.acad.php`;
3. `form-doc/header.php`.

Si la solución requiere cualquier otro archivo, la ejecución debe detenerse y
reportar la causa antes de modificarlo.

## 7. Transformación de `ajax/login.php`

`ajax/login.php` debe agregar `calendario.ver` al catálogo y al contrato vigente
de capabilities.

Debe concederla cuando se cumpla al menos una condición válida:

~~~text
Admin
OR Comité
OR tipo_est IN (2, 3)
OR estado_profesor = 2
~~~

La transformación debe:

- reutilizar los roles, `$studentStatus` y `$professorState` ya disponibles;
- evitar consultas redundantes;
- conservar la construcción atómica y la validación del contexto de sesión;
- mantener la lista de capabilities deduplicada;
- validar que `calendario.ver` coincida exactamente con la regla derivada;
- no concederla por permiso `3` o `$_SESSION['aceptado']` aislado;
- no retirar ni modificar `$_SESSION['aceptado']`;
- no alterar `perfil.ver`, `reglamento.ver`, `docente.habilitado` o
  `cursos.ver`.

## 8. Transformación de `form-doc/calend.acad.php`

La autoridad histórica:

~~~text
admin OR comite OR aceptado
~~~

debe sustituirse por:

~~~php
Authorization::hasCapability('calendario.ver')
~~~

La capability debe ser el guard backend efectivo tanto para navegación normal
como para URL directa.

Cuando el acceso sea rechazado, la página debe:

- redirigir según el patrón local vigente;
- finalizar correctamente la ejecución;
- no procesar ni renderizar el iframe o contenido posterior.

No se debe cambiar la URL de Google Calendar, el iframe funcional, sus estilos,
su contenido ni el proveedor externo.

## 9. Transformación de `form-doc/header.php`

El enlace **Calendario Académico** debe mostrarse únicamente mediante:

~~~php
Authorization::hasCapability('calendario.ver')
~~~

El enlace deja de consumir `admin`, `comite` y `aceptado` como autoridades
directas. La condición de visibilidad debe coincidir con el guard backend.

No se deben alterar otros enlaces, wrappers, condiciones o la organización del
header. La ocultación del enlace no reemplaza la autorización backend.

## 10. Archivos y superficies protegidos

No modificar:

- `index.php`;
- `src/Security/Authorization.php`;
- `src/Model/Login.php`;
- `admin/inicio.php`;
- `js/inicio.js`;
- `form-doc/footer.php`;
- `migrations/*`;
- schema o datos de base de datos;
- Google Calendar, su URL, eventos, cuenta, ACL o configuración;
- `docs/architecture/*`;
- `docs/roadmap/*`;
- `docs/governance/*`.

`admin/inicio.php`, `js/inicio.js` y `form-doc/footer.php` son consumers de
regresión, no autoridades ni objetivos de implementación.

## 11. Contrato de compatibilidad del permiso histórico 3

Calendario debe dejar completamente de consumir `$_SESSION['aceptado']` como
autoridad.

La Task debe conservar:

- la materialización temporal de `aceptado` en `ajax/login.php`;
- los consumidores heredados ajenos a Calendario;
- `permiso_login` sin escrituras ni reinterpretaciones;
- las capacidades y reglas de otros objetos.

La Task no debe:

- eliminar permiso `3`;
- retirar globalmente `aceptado`;
- migrar Reglamento u otros consumers;
- interpretar permiso `3` como estado Aceptado;
- otorgar compatibilidad READ de Calendario por permiso `3` aislado.

## 12. Protección de `index.php`

`index.php` no debe modificarse.

No se debe reemplazar:

~~~text
aceptado → Calendario
~~~

por:

~~~text
calendario.ver → Calendario
~~~

`calendario.ver` es compartida por actores con destinos iniciales diferentes.
En particular, Profesor Accepted debe conservar su destino inicial vigente de
Profesor. La navegación global y el consumer histórico de `aceptado` en
`index.php` serán resueltos en otra etapa de EPIC-003.

## 13. Persistencia e integración externa

Persistencia local: **NO APLICA**.

No crear:

- tabla;
- modelo;
- migración;
- Documento;
- filesystem;
- endpoint CRUD;
- integración API u OAuth.

Google Calendar, sus eventos, CREATE, UPDATE, DELETE, URL embed, ACL, propietario
y cuenta administradora permanecen fuera de alcance. Las observaciones externas
no deben resolverse dentro de esta Task.

## 14. Validación estática obligatoria

Después de implementar, Codex debe ejecutar:

~~~text
php -l ajax/login.php
php -l form-doc/calend.acad.php
php -l form-doc/header.php
git diff --check
git status --short
git diff --cached --name-only
~~~

Staging debe permanecer vacío hasta autorización posterior. La revisión del diff
debe confirmar que sólo cambiaron los tres archivos autorizados.

## 15. Regresión estática obligatoria

La revisión debe comprobar que:

- `perfil.ver` permanece intacta;
- `reglamento.ver` permanece intacta;
- `docente.habilitado` permanece intacta;
- `cursos.ver` permanece intacta;
- `aceptado` continúa disponible para otros consumers;
- `src/Security/Authorization.php` no cambia;
- `src/Model/Login.php` no cambia;
- `index.php` no cambia;
- el dashboard Admin/Comité no cambia;
- `js/inicio.js` y `form-doc/footer.php` no cambian;
- la URL y parámetros del calendario externo no cambian;
- no aparecen modelos, endpoints, tablas, migraciones o archivos de eventos.

## 16. Criterios de aceptación

La implementación queda técnicamente aprobable cuando:

1. `calendario.ver` forma parte del catálogo validado de capabilities.
2. La capability se deriva sin consultas adicionales.
3. Sólo la reciben los actores institucionalmente autorizados.
4. Permiso `3` o `aceptado` aislado no la producen.
5. `form-doc/calend.acad.php` usa exclusivamente la capability como autoridad.
6. El rechazo redirige, finaliza y no renderiza el iframe.
7. `form-doc/header.php` usa la misma capability para mostrar el enlace.
8. Admin y Comité conservan acceso.
9. Profesor Accepted obtiene acceso.
10. Profesor Pending y Rejected permanecen bloqueados.
11. Estudiantes con `tipo_est` 2/3 obtienen acceso.
12. Estudiantes con `tipo_est` 1/4/5/6/7 permanecen bloqueados.
13. El acceso directo aplica la misma matriz que el menú.
14. Otros consumers de `aceptado` no cambian.
15. `index.php` permanece sin cambios.
16. No se introduce persistencia o CRUD local nuevo.
17. La URL y comportamiento funcional del iframe permanecen intactos.

## 17. Validación funcional futura

La VF será ejecutada exclusivamente por el usuario después de la implementación
y revisión técnica.

### VF-01 — Admin

- posee `calendario.ver`;
- enlace visible;
- URL directa permitida;
- iframe y calendario cargan.

### VF-02 — Comité

- mismo acceso que Admin para READ de Calendario.

### VF-03 — Profesor Accepted

- `calendario.ver` presente;
- enlace visible;
- URL directa permitida;
- iframe y calendario cargan;
- el destino inicial continúa siendo el vigente para Profesor.

### VF-04 — Profesor Pending

- sin capability;
- enlace oculto;
- URL directa bloqueada;
- sin iframe.

### VF-05 — Profesor Rejected

- sin capability;
- enlace oculto;
- URL directa bloqueada.

### VF-06 — Estudiante Aceptado

- `tipo_est = 2`;
- capability, enlace, URL directa e iframe permitidos.

### VF-07 — Estudiante Matriculado

- `tipo_est = 3`;
- capability, enlace, URL directa e iframe permitidos.

### VF-08 — Estudiante Postulante

- `tipo_est = 1`;
- sin capability, enlace o acceso directo.

### VF-09 — Estudiante Graduado

- `tipo_est = 4`;
- bloqueado.

### VF-10 — Estudiante Retirado

- `tipo_est = 5`;
- bloqueado.

### VF-11 — Estudiante Eliminado

- `tipo_est = 6`;
- bloqueado.

### VF-12 — Estudiante Reprobado

- `tipo_est = 7`;
- bloqueado.

### VF-13 — Permiso histórico 3 aislado

- `aceptado` puede continuar presente;
- no obtiene `calendario.ver` sin estado o rol válido;
- enlace oculto y URL directa bloqueada.

### VF-14 — Anónimo

- sin capability;
- URL directa bloqueada;
- redirección local sin iframe.

No corresponde ejecutar VF de CREATE, UPDATE o DELETE externos.

## 18. Reversión

La reversión técnica consiste únicamente en revertir los cambios realizados en:

- `ajax/login.php`;
- `form-doc/calend.acad.php`;
- `form-doc/header.php`.

No requiere reversión de datos, schema, migraciones, archivos o configuración
externa.

## 19. Condiciones para detener la implementación

Codex debe detenerse sin modificar archivos si:

1. el AT fuente no está disponible;
2. las fuentes institucionales resultan contradictorias;
3. alguno de los tres archivos autorizados cambió de forma incompatible;
4. la solución requiere modificar `index.php`;
5. la solución requiere modificar `src/Security/Authorization.php`;
6. se necesita una consulta adicional de base de datos para producir la
   capability;
7. se requiere persistencia, schema o migración nueva;
8. se necesita modificar Google Calendar, su URL, cuenta o ACL;
9. no puede conservarse la compatibilidad de otros consumers de `aceptado`;
10. aparece un cambio arquitectónico o institucional no previsto;
11. se necesita modificar cualquier archivo fuera de los tres autorizados.

No se permiten ampliaciones improvisadas. Cualquier condición de detención debe
registrarse y elevarse antes de continuar.

## 20. Gobierno y cierre futuro

- **Unidad funcional:** Calendario Académico.
- **Unidad de ejecución:** una Task integral.
- **Decisiones institucionales pendientes:** ninguna para la autorización local.
- **Persistencia:** no aplica.
- **ADR/addendum:** no requeridos.
- **VF:** aprobada por el usuario.

Resultado final:

- `ajax/login.php` produce y valida `calendario.ver`;
- `form-doc/calend.acad.php` consume la capability como guard backend y finaliza
  la ejecución después de rechazar el acceso;
- `form-doc/header.php` consume la capability para mostrar el enlace;
- permiso `3` y `aceptado` dejaron de autorizar Calendario;
- `index.php` permaneció sin cambios;
- no hubo cambios de base de datos, schema o migraciones;
- no se introdujeron CREATE, UPDATE o DELETE locales;
- la revisión técnica post-implementación fue aprobada;
- la VF integral fue aprobada por el usuario.

La Task queda cerrada. La integración Git permanece pendiente de commit y push
bajo autorización posterior.
