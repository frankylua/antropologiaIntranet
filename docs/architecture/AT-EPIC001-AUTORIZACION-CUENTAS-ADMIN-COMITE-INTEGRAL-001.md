# AT-EPIC001 — Autorización integral de cuentas Admin/Comité/Docencia

## Estado

**APROBADO — Política institucional definitiva.**

Este AT reemplaza el modelo anterior de Comité/Docencia. La implementación existente debe revisarse contra esta definición antes de considerarse cerrada.

## Problema

La gestión histórica mezcló identidad, permiso y perfil visible:

- asumió que Comité debía representarse siempre mediante Profesor;
- trató la coexistencia Admin/Comité + Docente como incompatible;
- construyó parte de la gestión administrativa únicamente desde `admin`;
- permitió que operaciones generales sobre login o permisos pudieran sobrescribir roles acumulativos;
- expuso o reutilizó `pass` en flujos de lectura y actualización;
- no distinguió entre eliminar una cuenta administrativa sin Docencia y retirar un rol administrativo de una cuenta que conserva Docencia.

La política definitiva separa la pertenencia administrativa, determinada por permisos, de la representación de los datos personales, determinada por la existencia de Docencia.

## Política canónica

| Tipo de cuenta | Representación obligatoria |
| --- | --- |
| Admin sin Docencia | Mismo `login` + permiso 1 + fila `admin`. |
| Comité sin Docencia | Mismo `login` + permiso 2 + fila `admin`. |
| Admin + Docente | Mismo `login` + permisos 1 y 4 + `usuario` + `profesor`; **sin** fila `admin`. |
| Comité + Docente | Mismo `login` + permisos 2 y 4 + `usuario` + `profesor`; **sin** fila `admin`. |

Reglas derivadas:

- Admin y Comité son roles administrativos distintos, identificados exclusivamente mediante `permiso_login.id_permiso` 1 y 2.
- Docencia se identifica mediante permiso 4 y su representación `usuario`/`profesor`.
- La coexistencia de un rol administrativo con Docencia es válida.
- Una cuenta que adquiere Docencia deja de usar `admin` como representación personal.
- No deben existir dos perfiles personales competidores para el mismo login.

## Agregar Docencia

“Agregar Docencia” es una transición de la cuenta Admin o Comité existente, no la creación de otra cuenta:

```text
cuenta Admin/Comité existente
→ conservar id_login y correo
→ abrir el formulario Docente con el correo prellenado
→ volver a ingresar nombre y apellidos por separado
→ completar los demás antecedentes docentes requeridos
→ crear usuario y profesor
→ agregar permiso 4
→ conservar permiso 1 o 2
→ eliminar admin sólo después del éxito total
```

El nombre completo administrativo no se divide ni copia automáticamente: el contrato Docente exige que nombre y apellidos vuelvan a ingresarse en sus campos propios.

La transición completa debe ser atómica. Ante cualquier fallo debe revertirse sin dejar una representación parcial, sin perder la fila `admin` vigente y sin cambiar el login, correo o permiso administrativo. La fila `admin` no puede eliminarse anticipadamente.

## Perfil visible y editable

| Estado | Perfil visible y editable |
| --- | --- |
| Admin/Comité sin Docencia | Perfil administrativo obtenido desde `admin`. |
| Admin/Comité con Docencia | Perfil Docente obtenido desde `usuario`/`profesor`. |

Cuando existe Docencia, el perfil Docente prevalece. No se mantiene ni presenta un perfil administrativo paralelo.

## Autogestión de Admin/Comité con Docencia

Cuando una cuenta Admin o Comité posee Docencia:

- su propio registro se excluye de la grilla de Docentes;
- sus datos personales se consultan y editan exclusivamente desde **Mi Perfil**;
- **Mi Perfil** utiliza el perfil Docente;
- el propio registro Docente no puede eliminarse;
- el backend rechaza un DELETE directo cuando el objetivo coincide con el login autenticado, aunque el frontend haya ocultado la fila o la acción.

La exclusión afecta únicamente a la propia fila (`id_login == login autenticado`) y no debe ocultar otros Docentes legítimos.

## Grilla Admin/Comité

La pertenencia a la grilla se determina desde `permiso_login.id_permiso IN (1, 2)`, no únicamente desde la tabla `admin`.

La fuente de datos personales depende de la representación:

- sin Docencia: `admin`;
- con Docencia: `usuario`/`profesor`.

La grilla debe incluir Admin sin Docencia, Comité sin Docencia, Admin + Docente y Comité + Docente, y distinguir Admin de Comité mediante el permiso 1 o 2. Ninguna consulta o respuesta puede devolver `pass` ni un hash de contraseña.

## Matriz de operaciones

| Operación | Autoridad y resultado requerido |
| --- | --- |
| READ/listado administrativo | Sólo Admin. Consulta pertenencia por permisos 1/2, resuelve datos desde `admin` o `usuario`/`profesor` y nunca devuelve `pass`. Sin autorización: HTTP 403. |
| CREATE Admin/Comité | Sólo Admin. Acepta exclusivamente 1 = Admin o 2 = Comité y crea atómicamente `login` + permiso seleccionado + fila `admin`. No crea `usuario` ni `profesor`. |
| UPDATE sin Docencia | Sólo Admin. Actualiza datos mediante la identidad `admin`; la contraseña es opcional y no se recupera previamente. No reconstruye ni sobrescribe permisos acumulativos. |
| UPDATE con Docencia | Sólo Admin en esta superficie. Los datos personales pertenecen al perfil Docente y no se actualizan como perfil `admin`; los permisos administrativos se conservan. |
| DELETE sin Docencia | Sólo Admin. Puede eliminar la identidad administrativa completa según el contrato autorizado, tras validar el objetivo. Prohíbe autoeliminación y rechaza un `id_login` arbitrario. |
| RETIRAR Admin con Docencia | Sólo otro Admin puede retirar permiso 1. El propio Admin no puede auto-retirarlo. Conserva login, usuario, profesor, permiso 4 y todos los antecedentes docentes. |
| RETIRAR Comité con Docencia | Sólo Admin puede retirar permiso 2. El propio Comité no puede auto-retirarlo. Conserva login, usuario, profesor, permiso 4 y todos los antecedentes docentes. |
| DELETE general Docente con rol administrativo | Rechazado tanto para Admin + Docente como para Comité + Docente. También se rechaza siempre cuando el objetivo coincide con el login autenticado. |

La retirada del permiso administrativo deja la cuenta funcionando como Docente. Este AT no redefine todavía el ciclo de vida integral del objeto Profesor.

## CREATE Admin/Comité

El selector institucional **Admin / Comité** forma parte del contrato válido. Sólo Admin puede ejecutar CREATE y el backend acepta únicamente:

- `1`: Admin;
- `2`: Comité.

Cualquier otro valor se rechaza. La operación debe validar los datos y la unicidad requerida, y crear transaccionalmente `login`, el permiso seleccionado y `admin`. No crea Docencia ni admite un editor general de permisos.

## UPDATE Admin/Comité

Para una cuenta sin Docencia, los datos administrativos se actualizan mediante su identidad `admin`. La contraseña continúa siendo opcional: si no se aporta una nueva, se conserva la vigente sin leerla ni devolverla al cliente.

Para una cuenta con Docencia, los datos personales se editan mediante el perfil Docente. El flujo administrativo no puede recrear `admin`, competir con el perfil Docente ni reconstruir o sobrescribir los permisos existentes.

## Eliminación y retiro de rol

Antes de actuar, el backend debe resolver el objetivo autorizado y comprobar su representación; no acepta un `id_login` arbitrario.

- Sin Docencia, DELETE administrativo puede eliminar la identidad administrativa completa según el contrato autorizado. La autoeliminación Admin permanece prohibida.
- Con Docencia, la operación no es un DELETE de cuenta: otro Admin puede retirar exclusivamente el permiso 1 de Admin + Docente, o un Admin puede retirar exclusivamente el permiso 2 de Comité + Docente.
- Admin no puede retirar su propio permiso 1 y Comité no puede retirar su propio permiso 2.
- Nunca se eliminan por esa retirada el login, `usuario`, `profesor`, permiso 4 ni antecedentes docentes.
- El DELETE general Docente debe rechazar cuentas con permiso 1 o 2 y debe rechazar previamente todo objetivo que coincida con el login autenticado.

## Caso histórico Comité + Docente sin `admin`

El caso anonimizado con permiso 2 + permiso 4 + `usuario` + `profesor` y sin fila `admin` es compatible con la representación canónica. No requiere migración ni normalización para crear `admin`.

## Seguridad y consistencia

- El backend es la autoridad; ocultar controles en vistas o JavaScript no autoriza una operación.
- La ocultación de la propia fila Docente y de acciones de auto-retiro es sólo una medida de interfaz; el backend debe repetir ambas validaciones.
- Toda superficie de gestión Admin/Comité exige guard Admin y responde HTTP 403 ante actores no autorizados.
- READ usa columnas explícitas y no devuelve, imprime, almacena ni reenvía `pass`.
- CREATE, la transición a Docencia y toda mutación multitabla deben ser transaccionales.
- UPDATE y retiro de rol mutan únicamente los datos o permisos propios de la operación.
- Las respuestas de error deben ser inequívocas para impedir que el frontend presente un rechazo como éxito.

## Fuera de alcance

- Modificar hashing, passwords existentes o el algoritmo de autenticación.
- Cambiar el esquema o crear migraciones.
- Crear un segundo login al agregar Docencia.
- Copiar automáticamente el nombre completo de `admin` a los campos separados de Docente.
- Redefinir el ciclo de vida integral de Profesor.
- Migrar el caso histórico compatible Comité + Docente para agregar una fila `admin`.

## Dictamen

**A. AT actualizado según la política institucional definitiva; la implementación puede reanudarse dentro del alcance autorizado.**
