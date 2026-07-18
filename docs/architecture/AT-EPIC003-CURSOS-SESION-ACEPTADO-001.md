# AT-EPIC003-CURSOS-SESION-ACEPTADO-001

## Diagnóstico técnico: estado aceptado, sesión y navegación de Cursos

## 1. Identificación y alcance

- **Clasificación:** [ARQ] diagnóstico de autorización heredada; [TEC] análisis de sesión y navegación de Cursos; [DOC] documento técnico oficial.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **TASK observada:** TASK-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.
- **Estado:** análisis técnico documental; no autoriza implementación.

El diagnóstico se limita a establecer la relación entre el estado académico almacenado, `permiso_login`, las claves de `$_SESSION` y la visibilidad de `Programa → Cursos`. No modifica código, sesiones, permisos, SQL, documentos de gobierno ni TASK existentes.

## 2. Conclusión ejecutiva

La causa técnica más probable y respaldada por el flujo revisado es una **desconexión heredada entre `estudiante.tipo_est` y `permiso_login`**.

El menú vigente ya muestra Cursos cuando existe `$_SESSION['aceptado']`:

```php
isset($_SESSION['aceptado'])
|| isset($_SESSION['admin'])
|| isset($_SESSION['comite'])
```

Por ello, si después de esa corrección el enlace continúa ausente, no hay una condición adicional de Cursos que explique el resultado para una sesión con `aceptado`. La evidencia dirige el diagnóstico a la ausencia de `$_SESSION['aceptado']` en la sesión real.

`$_SESSION['aceptado']` no se crea desde `tipo_estudiante`: se crea exclusivamente al iniciar sesión cuando la consulta a `permiso_login` devuelve `id_permiso = 3`. El cambio administrativo de estado revisado actualiza solamente `estudiante.tipo_est`; no inserta, conserva, elimina ni recalcula registros de `permiso_login`. Por tanto, un estudiante puede quedar con estado académico aceptado en base de datos y no recibir el permiso 3 ni la clave de sesión requerida por el menú.

```text
estado académico: estudiante.tipo_est = aceptado
  ── no existe traducción en login ──► permiso_login.id_permiso = 3
  ── ausente ──► $_SESSION['aceptado'] no existe
  ──► Programa → Cursos no se muestra
```

La resolución institucional sí establece el acceso de visualización para estudiante aceptado, pero declara expresamente que estado académico y permiso funcional son conceptos distintos y que su implementación técnica debe evaluarse en una TASK específica. El problema es, por tanto, un comportamiento legado/falta de mapeo estado → permiso efectivo, no una nueva regla de menú.

## 3. Flujo de login y sesión

### 3.1 Fuente de las claves de sesión

`ajax/login.php` llama a `Login::validarPermiso($correo, $pass)`. El método de `src/Model/Login.php` consulta exclusivamente `login` y `permiso_login`:

```sql
SELECT *
FROM login l JOIN permiso_login p
WHERE l.id_login = p.id_login
  AND l.correo = ? AND l.pass = ?
```

No consulta `estudiante`, `tipo_estudiante` ni `estudiante.tipo_est`.

El resultado se materializa así:

| `id_permiso` | Clave de sesión creada |
| --- | --- |
| 1 | `$_SESSION['admin']` |
| 2 | `$_SESSION['comite']` |
| 3 | `$_SESSION['aceptado']` |
| 4 | `$_SESSION['docente']` |
| 5 | `$_SESSION['estudiante']` |

El propio comentario del login registra que el permiso 3 es histórico y compartido por «docente o estudiante aceptado». La clave `aceptado` no es una lectura directa del estado académico y no distingue por sí sola el tipo de actor.

### 3.2 Relación con el estado del estudiante

La entidad `estudiante` conserva el estado mediante la columna `tipo_est`, relacionada con `tipo_estudiante`. La creación de estudiante desde `form-doc/scripts/estudiante.js` agrega permiso 5 y agrega permiso 3 cuando `tipo_est` vale 2 o 3. Esa es una asignación en el alta, no una derivación realizada en cada inicio de sesión.

El flujo administrativo posterior `update-permiso-tipo-est` de `ajax/estudiante.php` invoca únicamente:

```text
Estudiante::editarTipoEst(id_usuario, tipo_est)
  ↓
UPDATE estudiante SET tipo_est = ...
```

No hay, en ese caso, llamada a `agregarPermiso`, `insertarPermisos`, `eliminarPermiso` ni a una reconstrucción de `permiso_login`. El nombre histórico de la operación incluye «permiso», pero el código revisado modifica solo el estado académico.

## 4. Análisis del menú Programa

| Elemento | Evidencia |
| --- | --- |
| Archivo generador | `form-doc/header.php` |
| Menú `Programa` | Se muestra para alguna de `admin`, `comite`, `aceptado`, `estudiante` o `docente`. |
| Enlace Cursos actual | `aceptado OR admin OR comite`. |
| Otras restricciones en el menú | No se identificó otra condición para el `<li>` de Cursos. |

La condición anterior exigía `docente AND aceptado`, o bien Comité/administración. La corrección acotada ya la sustituyó por la condición actual. Por ello, el resultado posterior «Cursos no visible» es incompatible con una sesión que efectivamente contenga `aceptado`, `admin` o `comite`.

La página `form-doc/ver.curso.php` también permite entrada directa por `admin`, `comite`, `aceptado` o `docente`; esto no altera el diagnóstico de navegación, pero confirma que `estudiante` aislado no es una clave suficiente para Cursos.

## 5. Comparación de actores y materialización de sesión

| Actor/condición | Estado o fuente relevante | Claves que el login puede crear | Acceso visible a Cursos con menú actual |
| --- | --- | --- | --- |
| Estudiante aceptado | `estudiante.tipo_est`; permiso 3 debe existir separadamente | `estudiante` por permiso 5; `aceptado` solo por permiso 3 | Sí, solo si existe `aceptado` |
| Profesor | Permisos asignados en `permiso_login`; permiso 3 puede ser compartido | `docente` por permiso 4; `aceptado` por permiso 3 | Sí si tiene `aceptado`; no por `docente` aislado |
| Comité | Rol acumulativo en `permiso_login` | `comite` por permiso 2 | Sí |
| Administrador | Rol acumulativo en `permiso_login` | `admin` por permiso 1 | Sí |

La sesión puede acumular claves porque el login recorre todas las filas de `permiso_login`. No obstante, el flujo revisado no deriva esa acumulación desde cambios de `tipo_est`.

## 6. Estado académico versus sesión

```text
tipo_estudiante
  ↓
estudiante.tipo_est
  ↓
[no hay consulta ni traducción en Login::validarPermiso]
  ↓
permiso_login.id_permiso
  ↓
ajax/login.php
  ↓
$_SESSION['aceptado'] / ['estudiante'] / otras claves
  ↓
form-doc/header.php
```

### Hallazgos confirmados

- El login construye sesión desde `permiso_login`, no desde `tipo_estudiante`.
- `$_SESSION['aceptado']` se activa únicamente por `id_permiso = 3`.
- La modificación de estado `update-permiso-tipo-est` persiste `tipo_est` sin sincronizar `permiso_login`.
- El menú actual muestra Cursos si `aceptado` está presente; no hay otro requisito adicional para estudiante aceptado.
- Un usuario con solo permiso 5 obtiene `$_SESSION['estudiante']`, que permite abrir `Programa`, pero no muestra Cursos ni habilita la URL directa.

### Hipótesis que requiere contraste de datos controlado

- El estudiante validado fue aceptado mediante el flujo `update-permiso-tipo-est` o posee una cuenta sin permiso 3, por lo que su nueva sesión no contiene `aceptado`.
- Los valores 2 y 3 de `tipo_est` usados al crear estudiantes corresponden operativamente a los estados que deben recibir acceso a Cursos; la fuente de código no nombra esos valores y debe contrastarse con `tipo_estudiante` en un entorno autorizado.
- Una sesión preexistente podría conservar claves anteriores hasta un nuevo inicio de sesión; el login no recalcula estado, por lo que volver a iniciar sesión no resuelve la ausencia persistente de permiso 3.

## 7. Relación con EPIC-003 y clasificación

La discrepancia corresponde a **modelo legado y falta de mapeo técnico estado académico → permiso efectivo**. No es una contradicción con la resolución: esta exige que el estado determine reglas de acceso, pero conserva la separación estado ≠ permiso funcional y no autoriza materializar ese mapeo automáticamente.

La corrección de menú fue técnicamente pertinente y suficiente para eliminar la condición adicional de navegación. No puede resolver la falta de `aceptado` en sesión porque el menú no crea ni recalcula claves. Modificar `header.php` nuevamente no atendería la causa identificada.

## 8. Riesgos

- Asignar permiso 3 directamente sin validar su relación histórica compartida con docentes puede afectar permisos acumulativos.
- Implementar estado → permiso dentro del login puede cambiar el comportamiento de todos los módulos que consumen `aceptado`.
- Sin una política por capacidad, conceder `aceptado` sigue habilitando las capacidades actuales de Cursos más allá de la visualización institucional aprobada.
- Corregir solo datos o solo la sesión puede ocultar el problema de sincronización para futuros cambios de estado.

## 9. Recomendación

1. Realizar una validación no mutante de la cuenta reportada: comparar `estudiante.tipo_est`, filas de `permiso_login` y claves generadas tras un nuevo inicio de sesión.
2. Mantener la corrección actual del menú; no agregar condiciones ni permisos como solución local.
3. Requerir una definición técnica aprobada para sincronizar o derivar permisos efectivos desde transiciones de estado, preservando roles acumulativos y separando visualización de administración.
4. Esa definición excede la corrección de navegación y la TASK de separación de capacidades. Si se aprueba, requerirá una TASK posterior específica; este AT no crea ninguna.
5. No requiere actualizar ADR, Roadmap, Manual Maestro ni la resolución institucional. Puede requerir validación institucional solo si la política de convivencia del permiso histórico 3 con estudiantes y docentes no está clara.

## 10. Fuentes utilizadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md)
- [TASK-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.md](../tasks/TASK-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.md)
- [AT-EPIC003-CURSOS-VALIDACION-ACCESO-ACEPTADO-001.md](AT-EPIC003-CURSOS-VALIDACION-ACCESO-ACEPTADO-001.md)
- [AT-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.md](AT-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.md)
- Evidencia técnica: `ajax/login.php`, `src/Model/Login.php`, `ajax/estudiante.php`, `src/Model/Estudiante.php`, `form-doc/scripts/estudiante.js`, `form-doc/header.php` y `form-doc/ver.curso.php`.

## 11. Restricciones cumplidas y siguiente paso

```text
Sin cambios de código.
Sin implementación.
Sin cambios ADR.
Sin cambios Roadmap.
Sin cambios Manual Maestro.
Sin commit.
```

El único cambio de esta entrega es la creación de este AT. Siguiente paso:

```text
Revisión técnica AT-EPIC003-CURSOS-SESION-ACEPTADO-001
```
