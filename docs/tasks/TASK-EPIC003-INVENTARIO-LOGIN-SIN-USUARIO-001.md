# TASK-EPIC003-INVENTARIO-LOGIN-SIN-USUARIO-001

## Inventario de cuentas autenticables sin perfil de usuario

**EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
**AT de origen:** AT-EPIC003-IDENTIDAD-ESTUDIANTE-DERIVADA-LOGIN-001.
**Tipo:** [TEC] [ARQ] [DAT] Inventario de solo lectura.
**Estado:** Ejecutado sobre la base local activa configurada para desarrollo.

## Alcance

Se ejecutaron consultas `SELECT` sobre `login`, `usuario`, `permiso_login` y `admin`. No se modificaron datos, código, sesiones, permisos, redirección ni navegación. La evidencia no incluye correos, nombres, contraseñas ni otros datos personales.

## Resultado

| Control | Resultado |
| --- | ---: |
| Logins sin fila `usuario` | 3 |
| Esas cuentas con rol administrativo persistido (`admin`) | 3 |
| Esas cuentas con al menos un permiso explícito | 3 |
| Asignaciones de permiso 1 entre esas cuentas | 1 |
| Asignaciones de permiso 2 entre esas cuentas | 2 |
| Logins con más de una fila `usuario` | 0 |

## Interpretación

Las tres cuentas autenticables sin perfil personal no son cuentas académicas incompletas según la evidencia disponible: todas poseen una relación administrativa y permisos explícitos. Por tanto, una futura resolución de identidad debe aceptar `id_usuario = null` y permitir que la resolución de roles administrativos continúe de manera independiente.

El flujo actual ya dirige cuentas con sesión `admin` o `comite` a `admin/inicio.php`; esta Task no cambia dicho comportamiento. Aún falta una decisión explícita para el caso futuro de una cuenta sin usuario que no posea un rol administrativo reconocido.

## Condición para el resolver posterior

El resolver podrá clasificar este resultado como `LOGIN_SIN_USUARIO`, sin consultar `estudiante` ni `profesor`, sin crear identidad ni capacidades académicas, y sin impedir por sí mismo los roles administrativos explícitos. Debe registrar el evento sin datos personales.
