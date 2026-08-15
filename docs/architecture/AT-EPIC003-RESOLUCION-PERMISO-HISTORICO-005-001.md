# AT-EPIC003-RESOLUCION-PERMISO-HISTORICO-005-001

## Resolución arquitectónica del permiso histórico 5 como rol de estudiante

**EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
**Tipo:** [ARQ] [TEC] [GOV] [MET].
**Estado:** Propuesto para revisión arquitectónica.

## Decisión propuesta

La especialización de estudiante tendrá como fuente de verdad objetivo la relación:

```text
login → usuario → estudiante
```

El permiso `5` (`estudiante` en el SQL local de prueba) se clasifica como **rol heredado de compatibilidad en retiro**. No será requisito permanente para identificar a un estudiante ni concederá por sí mismo capacidades académicas.

Mientras se realiza la transición, el permiso 5 se conserva en cuentas existentes y durante cambios de `estudiante.tipo_est`. No se crearán nuevos consumidores, no se usará como sustituto del permiso 3 y no se retirará de las altas hasta cumplir el umbral definido en este documento.

## Separación de conceptos

| Concepto | Fuente objetivo |
| --- | --- |
| Identidad personal | `login → usuario` |
| Especialización de estudiante | `usuario → estudiante` |
| Estado académico | `estudiante.tipo_est` |
| Capacidades | Estado, roles independientes y participaciones aplicables |
| Autenticación | Cuenta `login` |

El permiso 5 no representa un estado académico. Las capacidades deben ser explícitas —por ejemplo, `perfil.ver`, `reglamento.ver`, `cursos.ver` y `calendario.ver`— y su autorización backend no puede depender de la visibilidad de menús.

## Tratamiento del estado Eliminado

`tipo_est = 6` preserva la identidad histórica de estudiante cuando la fila exista, pero no concede capacidades académicas. No debe interpretarse como inexistencia retrospectiva de la especialización.

## Consecuencias de transición

Actualmente la compatibilidad depende de `permiso 5 → $_SESSION['estudiante']`; los consumidores incluyen login, carga de `id_usuario`, redirección inicial, perfil individual y navegación. Por ello, la eliminación inmediata se rechaza: rompería flujos vigentes.

El modelo futuro deberá resolver la identidad y las especializaciones después de autenticar, derivar capacidades y seleccionar el destino considerando roles múltiples. La prioridad exacta de redirección requiere la decisión posterior `AT-EPIC003-REDIRECCION-POR-CAPACIDADES-001`.

## Umbral para congelar la asignación en altas

Solo se puede dejar de asignar permiso 5 cuando se compruebe que:

- login obtiene `id_usuario` sin permiso 5 y detecta la relación estudiante;
- `perfil.ver` protege el perfil y `info.estudiante.php` no depende de `$_SESSION['estudiante']`;
- enlace Mi perfil, navegación y redirección usan capacidades y roles;
- estudiantes Eliminados no obtienen capacidades;
- usuarios con roles múltiples funcionan correctamente;
- existe una prueba de alta nueva sin permiso 5 y una reversión documentada.

## Secuencia aprobable

1. Inventariar inconsistencias entre permiso 5 y la relación estudiante, sin modificar datos.
2. Definir identidad derivada durante login y contrastarla con el legado.
3. Implementar `perfil.ver` y migrar perfil, navegación y redirección.
4. Validar todos los estados y roles múltiples.
5. Crear y validar una cuenta nueva sin permiso 5; entonces congelar su asignación.
6. Retirar consumidores de sesión, y solo después evaluar la eliminación estructural del catálogo.

## Exclusiones

Este AT no autoriza cambios de código, datos, sesiones, permisos, base de datos ni catálogo. La eliminación estructural de permiso 5 queda fuera de alcance y exige una decisión posterior.

## Próximas piezas de trabajo

- [TASK-EPIC003-INVENTARIO-CONSISTENCIA-ROL-ESTUDIANTE-005-001](../tasks/TASK-EPIC003-INVENTARIO-CONSISTENCIA-ROL-ESTUDIANTE-005-001.md).
- `AT-EPIC003-IDENTIDAD-ESTUDIANTE-DERIVADA-LOGIN-001`.
- `TASK-EPIC003-CAPACIDAD-PERFIL-VER-001`.
- `AT-EPIC003-REDIRECCION-POR-CAPACIDADES-001`.
