# TASK-EPIC001 — Autorización integral de cuentas Admin/Comité/Docencia

## Estado

**REFORMULADA — implementación suspendida, parcialmente reutilizable y autorizada para reanudación.**

No está completada ni finalizada. Deriva de [AT-EPIC001-AUTORIZACION-CUENTAS-ADMIN-COMITE-INTEGRAL-001](../architecture/AT-EPIC001-AUTORIZACION-CUENTAS-ADMIN-COMITE-INTEGRAL-001.md). Después de esta actualización documental, la implementación suspendida queda autorizada para reanudarse y adaptarse al modelo institucional definitivo antes de una nueva VF.

## Objetivo

Implementar en una sola unidad coherente la gestión autorizada de cuentas Admin/Comité y su transición a Docencia, preservando un único login, los permisos acumulativos y un único perfil personal visible.

## Contrato institucional obligatorio

| Tipo de cuenta | Representación |
| --- | --- |
| Admin sin Docencia | `login` + permiso 1 + `admin`. |
| Comité sin Docencia | `login` + permiso 2 + `admin`. |
| Admin + Docente | mismo `login` + permisos 1 y 4 + `usuario` + `profesor`; sin `admin`. |
| Comité + Docente | mismo `login` + permisos 2 y 4 + `usuario` + `profesor`; sin `admin`. |

La pertenencia administrativa se determina por `permiso_login.id_permiso IN (1, 2)`. La fuente de datos personales es `admin` cuando no hay Docencia y `usuario`/`profesor` cuando existe Docencia.

## Impacto sobre la implementación suspendida

### Conservar

- guards backend con autoridad Admin;
- HTTP 403 para actores no autorizados;
- READ con columnas explícitas y sin `pass`;
- contraseña opcional en UPDATE, sin recuperar la vigente;
- CREATE transaccional;
- confirmación de DELETE;
- manejo frontend inequívoco de errores;
- protección contra `id_login` arbitrario;
- contención del DELETE general Docente cuando existe un rol administrativo.
- validación backend contra DELETE del propio login.

### Corregir

- el supuesto de CREATE exclusivamente Admin;
- restaurar el selector institucional Admin/Comité;
- mantener READ/listado de gestión exclusivamente para Admin;
- cualquier helper que considere `usuario`/`profesor` incompatible con un rol Admin o Comité;
- la asignación o retirada de Comité dependiente de que el objetivo sea Profesor;
- la lógica de selección y edición del perfil visible;
- la grilla administrativa construida únicamente desde `admin`;
- DELETE administrativo cuando la cuenta posee Docencia;
- cualquier reconstrucción o sobrescritura general de permisos.
- la grilla Docente para excluir únicamente la fila del actor Admin/Comité con Docencia.

### Implementar

- la transición “Agregar Docencia” sobre el mismo login;
- conservación de `id_login` y reutilización del correo;
- apertura o redirección al formulario Docente con correo prellenado;
- reingreso obligatorio de nombre y apellidos en campos separados;
- creación de `usuario` y `profesor` con los demás antecedentes requeridos;
- adición del permiso 4, conservando permiso 1 o 2;
- eliminación de `admin` sólo después del éxito completo;
- atomicidad y reversión integral de la transición ante fallos;
- perfil Docente como perfil visible y editable cuando existe Docencia;
- retiro exclusivo del permiso 1 o 2 cuando una cuenta con Docencia deja el rol administrativo.
- autogestión de datos personales desde **Mi Perfil** Docente;
- prohibición backend de auto-retiro de permiso 1/2 y de DELETE Docente propio.

## Alcance técnico autorizado

La implementación suspendida comprende 12 cambios de código preexistentes. La reanudación queda autorizada sobre un máximo de 13 archivos:

| Archivo | Impacto que debe revisarse |
| --- | --- |
| `admin/perfil.php` | Selección del perfil visible según exista Docencia. |
| `admin/scripts/admin.js` | Selector Admin/Comité, grilla, transición a Docencia, errores y confirmación de retiro/DELETE. |
| `admin/scripts/perfil.js` | Edición sin `pass` y derivación al perfil Docente cuando corresponda. |
| `admin/scripts/ver.docente.js` | Presentación y navegación asociadas a Docencia. |
| `admin/ver.admin.php` | Selector institucional, grilla de las cuatro representaciones y acción Agregar Docencia. |
| `ajax/admin.php` | Guards, READ, CREATE 1/2, UPDATE, DELETE/retiro y transición autorizada. |
| `ajax/docente.php` | Contención DELETE Docente y operaciones de Docencia sin perder roles administrativos. |
| `form-doc/agr.form.dat.prog.doc.php` | Formulario Docente reutilizado por la transición de la misma cuenta. |
| `form-doc/scripts/ficha.docente.js` | Datos de Profesor, permisos preservados y respuestas sin credenciales. |
| `form-doc/scripts/usuario.js` | Reutilización del login/correo y creación controlada de la identidad Docente. |
| `src/Model/Admin.php` | Consultas de grilla, CREATE/UPDATE y DELETE/retiro según representación. |
| `src/Model/Docente.php` | Creación Docente, perfil y contención de eliminación con permiso 1/2. |
| `form-doc/scripts/docente.js` | Coordinar el alta nueva y la transición Agregar Docencia, sin segundo login y con manejo de error. |

No se autoriza modificar archivos fuera de esta lista sin una nueva revisión de alcance. Permanecen protegidos `src/Model/Login.php`, `src/Model/Usuario.php` y `src/Security/Authorization.php`. No se autorizan cambios de esquema, migraciones ni modificaciones de hashing.

## Autorización backend

- Iniciar sesión conforme al bootstrap antes de evaluar permisos.
- Sólo Admin puede ejecutar READ/listado, CREATE, UPDATE, DELETE, Agregar Docencia o retirar Admin/Comité en esta gestión.
- Sin sesión, Comité u otro actor: HTTP 403 antes de consultar o escribir.
- La vista puede ocultar controles, pero el endpoint decide.
- El backend valida operación, objetivo y permiso; no confía en el estado enviado por el cliente.
- Los errores se responden en JSON con código estable y nunca se presentan como éxito.

## READ y grilla Admin/Comité

- Consultar la pertenencia desde `permiso_login.id_permiso IN (1, 2)`.
- Incluir Admin sin Docencia, Comité sin Docencia, Admin + Docente y Comité + Docente.
- Distinguir el rol mediante permiso 1 o 2.
- Obtener datos personales desde `admin` sin Docencia y desde `usuario`/`profesor` con Docencia.
- Cuando existe Docencia, mostrar y editar sólo el perfil Docente.
- Seleccionar columnas explícitas; no devolver `pass`, hashes ni credenciales en JSON o consola.

## Autogestión y grilla de Docentes

Para un actor Admin o Comité con Docencia, el listado de Docentes debe excluir únicamente la fila cuyo `id_login` coincida con el login autenticado. No debe excluir otros Docentes legítimos.

La edición de los datos personales propios se realiza desde **Mi Perfil**, que debe resolver y utilizar el perfil Docente. La exclusión de la fila evita autoedición y autoeliminación desde la grilla, pero no reemplaza las validaciones backend.

Antes de cualquier DELETE Docente, el backend debe comparar el objetivo con el login autenticado y rechazar la operación si coinciden, independientemente de la interfaz o de una petición directa.

## CREATE Admin/Comité

- Sólo Admin puede ejecutar CREATE.
- La interfaz presenta el selector **Admin / Comité**.
- El backend acepta exclusivamente `1 = Admin` o `2 = Comité`; cualquier otro permiso se rechaza.
- Validar datos, correo único y resultado de cada escritura.
- Crear atómicamente `login`, el permiso seleccionado y la fila `admin`.
- Ante fallo, revertir todas las escrituras parciales.
- Esta operación no crea `usuario`, `profesor` ni permiso 4.

## Agregar Docencia

La acción recibe una cuenta Admin/Comité existente y transforma esa misma cuenta:

1. validar el objetivo y conservar su `id_login`, correo y permiso 1 o 2;
2. abrir el formulario Docente con el correo prellenado y no editable como una identidad distinta;
3. solicitar nuevamente nombre y apellidos, porque Docente los almacena por separado;
4. completar los demás antecedentes requeridos;
5. crear `usuario` y `profesor` para el login existente;
6. agregar permiso 4 sin retirar el permiso 1 o 2;
7. eliminar la fila `admin` únicamente después de que todos los pasos anteriores hayan tenido éxito.

La operación completa debe ejecutarse como una unidad atómica o con compensación reversible equivalente. Nunca debe crear un segundo login, copiar automáticamente el nombre completo administrativo ni eliminar `admin` antes del éxito total.

## UPDATE Admin/Comité

- Para cuentas sin Docencia, actualizar los datos personales mediante `admin`.
- La contraseña es opcional; ausente o vacía conserva la vigente sin READ previo.
- Si se aporta una contraseña nueva, mantener el contrato histórico vigente sin modificar hashing.
- Para cuentas con Docencia, los datos personales pertenecen al perfil Docente y no se actualizan como `admin`.
- UPDATE no cambia roles ni reconstruye el conjunto de permisos administrativos o docentes.

## DELETE administrativo sin Docencia

- Sólo Admin puede ejecutarlo.
- Resolver y validar el objetivo; no aceptar un `id_login` arbitrario.
- Confirmar que la cuenta posee permiso 1 o 2, fila `admin` y no posee Docencia.
- Prohibir la autoeliminación Admin.
- Eliminar la identidad administrativa completa de acuerdo con el contrato autorizado y de forma consistente.
- No usar este flujo contra una cuenta con Docencia.

## Retirar Admin/Comité con Docencia

Cuando el objetivo posee permiso 4 y representación `usuario`/`profesor`:

- Admin + Docente: retirar exclusivamente permiso 1;
- Comité + Docente: retirar exclusivamente permiso 2;
- exigir que el objetivo sea distinto del login autenticado;
- conservar login, `usuario`, `profesor`, permiso 4 y todos los antecedentes docentes;
- conservar cualquier otro permiso ajeno a la operación;
- mantener íntegro y visible el perfil Docente.

Después de la operación, la cuenta continúa como Docente. No debe recrearse ni conservarse una fila `admin` para representar sus datos personales.

El propio Admin no puede retirar su permiso 1: sólo otro Admin puede hacerlo. El propio Comité no puede retirar su permiso 2: sólo Admin puede hacerlo. El backend debe imponer esta regla aunque el frontend oculte la acción.

## Contención DELETE Docente

El DELETE general Docente debe rechazar, antes de eliminar, todo objetivo que coincida con el login autenticado. También debe rechazar tanto Admin + Docente como Comité + Docente mientras exista permiso 1 o 2. La coexistencia es válida, pero esta superficie no puede eliminar el login completo ni los antecedentes docentes de una cuenta que todavía posee un rol administrativo.

Esta Task no redefine el ciclo de vida integral del objeto Profesor.

## Caso histórico validado

Una cuenta con permiso 2 + permiso 4 + `usuario` + `profesor` y sin `admin` cumple la representación canónica Comité + Docente. No requiere migración ni creación de una fila `admin`.

## Respuestas mínimas

| Caso | Resultado |
| --- | --- |
| No autorizado | HTTP 403 + JSON `NO_AUTORIZADO`. |
| Permiso CREATE distinto de 1/2 | HTTP 400 + código estable; sin escritura. |
| Objetivo inválido o representación incompatible con la operación | HTTP 400 o 409 + código estable; sin escritura. |
| Autoeliminación Admin | HTTP 409 + código específico; sin escritura. |
| DELETE Docente propio | HTTP 409 + código específico; sin escritura. |
| Auto-retiro de permiso 1/2 | HTTP 409 + código específico; sin escritura. |
| Fallo durante CREATE o Agregar Docencia | Reversión completa + error estable; sin filas parciales. |
| DELETE Docente con permiso 1/2 | Rechazo estable; sin eliminar login, usuario, profesor ni antecedentes. |
| Éxito | JSON sin credenciales y con estado inequívoco para actualizar la interfaz. |
| Error técnico | HTTP 500 genérico; detalle sólo en log servidor y nunca credenciales. |

## Restricciones

- No crear un segundo login al agregar Docencia.
- No copiar automáticamente el nombre completo de `admin` hacia los campos separados de Docente.
- No eliminar `admin` antes del éxito integral de la transformación.
- No eliminar Docencia al retirar Admin o Comité.
- No mantener visible un perfil administrativo cuando existe Docencia.
- No mostrar la propia fila en la grilla Docente cuando el actor Admin/Comité posee Docencia.
- No permitir DELETE Docente propio ni auto-retiro del permiso administrativo.
- No devolver, imprimir, almacenar ni reenviar `pass`.
- No modificar hashing, autenticación, esquema ni crear migraciones.
- No normalizar el caso histórico Comité + Docente mediante una fila `admin`.

## VF integral pendiente

La VF ejecutada previamente queda **SUPERADA por un cambio institucional posterior** y no es válida como cierre de esta Task reformulada.

Debe ejecutarse una nueva VF, exclusivamente después de adaptar y revisar la implementación contra el modelo definitivo. Como mínimo debe cubrir:

- autorización: Admin permitido; Comité, otros actores y ausencia de sesión reciben HTTP 403 en gestión;
- READ/grilla: aparecen las cuatro representaciones, se distingue permiso 1/2 y nunca se expone `pass`;
- CREATE: selector Admin/Comité, aceptación exclusiva de 1/2, atomicidad y ausencia de `usuario`/`profesor`;
- UPDATE: perfil administrativo sin Docencia, perfil Docente con Docencia, password opcional y permisos intactos;
- Agregar Docencia: mismo `id_login`, mismo correo, nombre/apellidos reingresados, permiso 4, conservación de 1/2 y eliminación tardía de `admin`;
- reversión de Agregar Docencia: ante fallo, no quedan filas parciales ni se pierde `admin`;
- DELETE sin Docencia: objetivo validado y autoeliminación Admin rechazada;
- retiro con Docencia: sólo desaparece permiso 1 o 2 y el perfil Docente permanece íntegro;
- DELETE Docente: Admin + Docente y Comité + Docente son rechazados;
- autogestión: la propia fila Docente queda oculta, **Mi Perfil** usa Docencia y un DELETE directo propio es rechazado;
- retiro: sólo otro Admin retira permiso 1 y Admin retira permiso 2; ningún actor puede auto-retirar su rol;
- regresión: el caso Comité + Docente sin `admin` permanece válido y funcional.

## Riesgos y controles

| Riesgo | Control requerido |
| --- | --- |
| Duplicar la cuenta al agregar Docencia | Reutilizar el `id_login` validado y el correo existente. |
| Perder la representación administrativa antes de completar Docencia | Transacción y eliminación de `admin` como último paso. |
| Omitir cuentas con Docencia en la grilla | Determinar pertenencia mediante permisos 1/2 y resolver la fuente personal según representación. |
| Mantener perfiles competidores | Hacer prevalecer exclusivamente el perfil Docente cuando existe permiso 4 y `usuario`/`profesor`. |
| Perder Docencia al retirar un rol | Eliminar sólo permiso 1 o 2; verificar que login, permiso 4, usuario, profesor y antecedentes permanezcan. |
| Eliminar login equivocado | Validar actor, objetivo, permisos y representación; rechazar IDs arbitrarios. |
| Exponer `pass` | Columnas explícitas, password opcional y revisión de JSON/consola. |
| Aceptar un permiso arbitrario | Lista backend cerrada a 1/2 en CREATE; operaciones tipadas para los demás cambios. |

## Condiciones para detener

Detener y reportar antes de continuar la implementación si:

1. no puede resolverse de forma fiable la cuenta por permisos 1/2 y su representación personal;
2. no puede preservarse el mismo login y correo durante Agregar Docencia;
3. la transformación no puede garantizar atomicidad o reversión sin cambio de esquema;
4. retirar 1/2 no puede conservar íntegramente Docencia y los demás permisos;
5. retirar `pass` de READ obliga a migrar hashing o reescribir Login;
6. no puede distinguirse de forma fiable el login autenticado del objetivo de DELETE o retiro;
7. aparece otra ruta activa de gestión Admin/Comité/Docencia no cubierta;
8. se requiere un cambio de esquema, migración o un archivo fuera de los 13 autorizados.

## Gobierno de reanudación

- Decisiones institucionales pendientes: ninguna.
- Bloqueantes documentales: ninguno.
- Implementación: suspendida, pero autorizada para reanudación después de esta actualización.
- VF anterior: superada y no válida como cierre.
- Nueva VF: pendiente después de la implementación definitiva.

## Criterio de cierre

La Task no está completada ni el objeto finalizado. Sólo podrá cerrarse después de adaptar y revisar la implementación suspendida, ejecutar una nueva VF integral válida y resolver cualquier hallazgo. Hasta entonces no autoriza staging, commit, push ni operaciones reales.
