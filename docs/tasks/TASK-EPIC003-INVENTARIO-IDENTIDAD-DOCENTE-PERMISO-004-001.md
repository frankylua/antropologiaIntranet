# TASK-EPIC003-INVENTARIO-IDENTIDAD-DOCENTE-PERMISO-004-001

## Inventario de identidad docente y permiso histórico 4

**EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
**AT de origen:** AT-EPIC003-IDENTIDAD-ESTUDIANTE-DERIVADA-LOGIN-001.
**Tipo:** [TEC] [ARQ] [DAT] Inventario de solo lectura.
**Estado:** Ejecutado sobre la base local activa configurada para desarrollo.

## Alcance

Se compararon las relaciones `usuario → profesor` y el permiso histórico `4` en modo solo lectura. También se comprobó la cardinalidad de `usuario → profesor` y el solapamiento `estudiante + profesor`. No se modificaron datos, código, sesiones, permisos, redirección ni navegación; los resultados no exponen datos personales.

## Resultado

| Control | Resultado |
| --- | ---: |
| Filas `profesor` sin permiso 4 asociado | 1 |
| Permisos 4 sin fila `profesor` asociada | 0 |
| Usuarios con más de una fila `profesor` | 0 |
| Usuarios simultáneamente estudiante y profesor | 0 |
| Usuarios con más de una fila `estudiante` | 0 |

## Dictamen

Existe una contradicción activa: la especialización de dominio docente no coincide completamente con el permiso histórico 4. Por ello, el permiso 4 no puede tratarse como fuente de verdad de identidad docente. La inconsistencia debe ser observable como `PROFESOR_SIN_PERMISO_4` durante una futura derivación paralela, sin reconstruir el permiso ni conceder nuevas capacidades.

No se observan cardinalidades múltiples ni solapamiento estudiante-docente en la base inspeccionada. El esquema conserva índices no únicos para `usuario.login`, `estudiante.usuario` y `profesor.usuario`; por ello el resolver futuro deberá distinguir siempre cero, una y más de una fila.

## Siguiente paso autorizado

Ambos inventarios requeridos por el AT están disponibles. Puede prepararse un DFA técnico para un resolver de identidad de solo lectura, siempre que conserve las sesiones y consumidores históricos, no escriba permisos y no modifique la redirección o navegación.
