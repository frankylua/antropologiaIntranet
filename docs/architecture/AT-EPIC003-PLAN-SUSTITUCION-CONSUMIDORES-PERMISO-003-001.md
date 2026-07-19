# AT-EPIC003-PLAN-SUSTITUCION-CONSUMIDORES-PERMISO-003-001

## Plan de sustitución de consumidores del permiso histórico 3

### 1. Identificación

- **EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
- **ADR relacionado:** ADR-002 — Evolución del modelo de identidad y participación académica.
- **Tipo:** Análisis Técnico Arquitectónico.
- **Clasificación:** [ARQ] [TEC] [GOV] [MET].
- **Estado:** Aprobado.

### 2. Problema

El permiso histórico `3` mezcla responsabilidades diferentes:

- estado académico;
- acceso de estudiante;
- acceso docente;
- navegación;
- redirección;
- acceso a Cursos;
- acceso a Calendario;
- compatibilidad histórica mediante `$_SESSION['aceptado']`.

El modelo objetivo debe mantener separados:

```text
estado académico
≠ rol
≠ participación
≠ permiso
≠ capacidad
```

El permiso `3` no forma parte del modelo objetivo.

### 3. Evidencia confirmada

El inventario técnico confirmó:

1. El permiso `3` todavía se asigna en altas de estudiantes.
2. El permiso `3` todavía se asigna en altas de docentes.
3. `ajax/login.php` lo transforma en `$_SESSION['aceptado']`.
4. Existen consumidores en redirección, navegación, Cursos, Calendario y Reglamento.
5. Reglamento ya dispone de la capacidad `reglamento.ver`.
6. El cambio histórico de estado académico reconstruía permisos.
7. La base de datos utilizada es de prueba y sus datos antiguos pueden reinicializarse mediante una Task separada.

### 4. Decisión arquitectónica

Los consumidores del permiso `3` serán sustituidos mediante:

* estado académico persistido en `estudiante.tipo_est`;
* roles explícitos de estudiante, docente, Administrador y Comité;
* capacidades concretas derivadas durante el inicio de sesión;
* autorización backend mediante el componente centralizado de autorización.

No se crearán permisos equivalentes a cada estado académico.

No se reemplazará el permiso `3` por otra clave genérica de sesión.

No se autoriza eliminar inmediatamente el permiso `3` del catálogo.

### 5. Capacidades académicas mínimas

La matriz institucional requiere, como mínimo:

```text
perfil.ver
datos_academicos.ver
cursos.ver
calendario.ver
reglamento.ver
```

Reglas académicas vigentes:

| Estado          | Perfil | Datos académicos | Cursos | Calendario | Reglamento |
| --------------- | -----: | ---------------: | -----: | ---------: | ---------: |
| 1 — Postulante  |     Sí |               No |     No |         No |         Sí |
| 2 — Aceptado    |     Sí |               Sí |     Sí |         Sí |         Sí |
| 3 — Matriculado |     Sí |               Sí |     Sí |         Sí |         Sí |
| 4 — Graduado    |     Sí |               No |     No |         No |         Sí |
| 5 — Retirado    |     Sí |               No |     No |         No |         Sí |
| 6 — Eliminado   |     No |               No |     No |         No |         No |
| 7 — Reprobado   |     Sí |               No |     No |         No |         Sí |

La implementación de cada capacidad requiere una Task separada.

### 6. Productores y consumidores pendientes

Productores pendientes:

* alta de estudiante;
* alta de docente;
* otros productores que pudiera identificar una inspección posterior.

Transformación pendiente:

```text
permiso 3
→ $_SESSION['aceptado']
```

Consumidores pendientes:

* redirección posterior al login;
* navegación;
* Cursos;
* Calendario;
* fallbacks históricos restantes.

Reglamento dispone de sustitución funcional mediante:

```text
reglamento.ver
```

### 7. Cambio de estado académico

La operación de cambio de estado debe:

```text
validar el identificador de usuario
→ validar tipo_est
→ actualizar estudiante.tipo_est
→ preservar permiso_login
```

No debe:

* eliminar permisos;
* reconstruir permisos;
* asignar permiso `3`;
* asignar permiso `5`;
* sincronizar permisos con estados académicos.

Las capacidades correspondientes al nuevo estado se recalculan al iniciar una sesión nueva.

### 8. Permiso 5

La evidencia funcional permite clasificar provisionalmente el permiso `5` como:

```text
rol heredado de estudiante,
independiente de estudiante.tipo_est
```

Se observa que:

* se asigna en el alta de estudiante;
* genera `$_SESSION['estudiante']`;
* habilita perfil y navegación básica;
* no habilita por sí mismo Cursos o Calendario;
* debe preservarse durante cambios de estado;
* no debe utilizarse como sustituto del permiso `3`.

Su semántica definitiva requiere un análisis técnico independiente.

Referencia propuesta:

```text
AT-EPIC003-RESOLUCION-PERMISO-HISTORICO-005-001
```

La referencia no implica que dicho AT ya esté creado o aprobado.

### 9. Umbral para congelar el permiso 3

La asignación del permiso `3` solo podrá congelarse cuando:

* Reglamento funcione sin fallback histórico;
* Cursos tenga capacidades sustitutas;
* Calendario tenga capacidad sustituta;
* navegación y redirección funcionen sin `$_SESSION['aceptado']`;
* el acceso docente tenga rol o capacidades explícitas;
* las nuevas cuentas funcionen sin permiso `3`;
* el cambio de estado no reconstruya permisos;
* no existan productores adicionales no inventariados.

### 10. Secuencia aprobada

1. Resolver cambio de estado sin reconstrucción de permisos.
2. Resolver autorización docente.
3. Definir redirección basada en capacidades.
4. Resolver capacidades de Cursos.
5. Resolver `calendario.ver`.
6. Completar capacidades de perfil y datos académicos.
7. Migrar la navegación.
8. Validar cuentas nuevas sin permiso `3`.
9. Retirar fallbacks históricos.
10. Congelar la asignación del permiso `3`.
11. Retirar `$_SESSION['aceptado']`.
12. Reinicializar datos de prueba mediante una Task separada.
13. Evaluar la eliminación estructural del permiso `3`.

### 11. Progreso

#### Paso 1 — Cambio de estado sin reconstrucción

```text
Estado:
Completado.

Task:
TASK-EPIC003-CAMBIO-ESTADO-SIN-RECONSTRUIR-PERMISOS-001

Commit:
bc14bcd80170ef1c685c15a717c9323b36bc6b7d
```

Resultado:

* `id_usu` y `tipo_est` se validan antes de escribir;
* `tipo_est` admite exclusivamente valores `1–7`;
* la única escritura es `Estudiante::editarTipoEst()`;
* no se elimina ni reconstruye `permiso_login`;
* no se asignan permisos `3` ni `5`;
* los permisos independientes permanecen intactos;
* la validación funcional fue aprobada por el usuario.

### 12. Pendientes

* productores del permiso `3` en altas;
* `$_SESSION['aceptado']`;
* redirección;
* navegación;
* Cursos;
* Calendario;
* autorización docente;
* resolución definitiva del permiso `5`;
* refresco inmediato de capacidades;
* atomicidad global del alta;
* reinicialización de datos de prueba;
* eliminación estructural del permiso `3`.

### 13. Consecuencias

Consecuencias positivas:

* separación entre estado y permisos;
* eliminación de reconstrucción destructiva;
* preservación de roles independientes;
* avance hacia capacidades explícitas;
* posibilidad de crear datos nuevos coherentes.

Consecuencias transitorias:

* coexistencia con consumidores históricos;
* necesidad de Tasks por módulo;
* capacidades actualizadas en el próximo login;
* permiso `3` todavía no congelado.

### 14. Estado final del AT

```text
AT:
Aprobado y vigente.

Paso 1:
Completado.

Congelación del permiso 3:
No autorizada todavía.

Eliminación estructural:
Fuera de alcance.
```
