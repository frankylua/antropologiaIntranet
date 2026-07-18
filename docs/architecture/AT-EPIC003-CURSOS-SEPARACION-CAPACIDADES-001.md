# AT-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001

## Análisis técnico: separación de capacidades del módulo Cursos

### 1. Identificación

- **Clasificación:** [ARQ] análisis de separación de capacidades; [TEC] evaluación de módulo heredado; [DOC] documento técnico oficial.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Feature asociada:** FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.
- **Antecedente:** TASK-EPIC003-AUTORIZACION-CURSOS-CONSULTA-PILOTO-001.
- **Alcance:** análisis documental y técnico; no autoriza ni contiene implementación.

## 2. Objetivo y conclusión

Se evalúa la viabilidad de separar las capacidades de **visualización**, **escritura**, **actualización** y **eliminación** del módulo Cursos para permitir que, en una etapa posterior, la consulta pueda usar `Authorization` sin habilitar acciones administrativas.

**Conclusión:** la separación es técnicamente necesaria antes de migrar con seguridad solo la visualización. El flujo activo agrupa lectura y administración en la misma pantalla, y `ajax/curso.php` atiende todas las operaciones sin una comprobación de sesión o permiso declarada. Por ello, sustituir únicamente la validación de entrada por `Authorization` conservaría la mezcla de capacidades y no constituiría una frontera verificable por operación.

## 3. Evidencia técnica observada

```text
form-doc/ver.curso.php
  ↓ incluye
form-doc/scripts/curso.js
  ↓ solicitudes POST
ajax/curso.php
```

| Capacidad | Evidencia en el flujo | Operación observada |
| --- | --- | --- |
| Visualización | `mostrarCurso()` y acción `verCurso` en `curso.js` | `read` lista cursos y `query_id` obtiene el detalle. La vista muestra tabla, detalle y descarga del programa. |
| Escritura | Formulario de alta y envío `insert-update` con `id_curso = 0` | El endpoint carga el archivo de programa y llama a `Curso::insertar(...)`. |
| Actualización | Acción `editar_curso` y envío `insert-update` con identificador existente | Se consulta el curso, se actualiza mediante `Curso::editar(...)` y, si se adjunta reemplazo, se reemplaza el archivo de programa. |
| Eliminación | Acción `eliminarCurso` y operación `delete` | El endpoint llama a `Curso::eliminar($id_curso)` y, si resulta exitoso, elimina el programa asociado con `unlink(...)`. La naturaleza lógica o física de la eliminación del registro de base de datos no puede confirmarse solo desde las fuentes autorizadas; la eliminación del archivo sí es física. |

La misma vista presenta el botón **Agregar Registro**, el control de edición en el detalle y la acción **Eliminar** junto a los elementos de consulta. No hay una separación de interfaz ni de autorización entre estos grupos de acciones.

## 4. Modelo actual de autorización

```text
Usuario
  ↓
Inicio de sesión: login + permiso_login
  ↓
Claves de sesión
  ↓
Validación directa en form-doc/ver.curso.php
  ↓
Pantalla Cursos y solicitudes a ajax/curso.php
```

La protección de entrada en `form-doc/ver.curso.php` permite el acceso si existe alguna de estas claves de sesión: `admin`, `comite`, `aceptado` o `docente`. Si ninguna existe, redirige a `index.php`.

| Actor o clave histórica | Comportamiento confirmado respecto de Cursos |
| --- | --- |
| `admin` | Acceso directo a la pantalla y enlace de navegación. |
| `comite` | Acceso directo a la pantalla y enlace de navegación. |
| `aceptado` | Acceso directo; la navegación lo habilita únicamente junto con `docente`. |
| `docente` | Acceso directo; la navegación lo habilita únicamente junto con `aceptado`. |
| `estudiante` | No habilita por sí sola acceso directo ni enlace. |

Las claves se materializan a partir de `permiso_login`, según el análisis previo. No son, por ese hecho, permisos funcionales consolidados ni una lectura directa del estado académico. Existe además una divergencia confirmada entre la condición de URL directa y la de navegación: ambas no aplican la misma combinación de claves.

El endpoint `ajax/curso.php` no declara una validación de sesión o permiso por cada operación. En consecuencia, la validación de la pantalla no debe tratarse como una frontera suficiente para lectura, escritura, actualización y eliminación.

## 5. Comparación con EPIC-003

El modelo aprobado separa conceptos:

```text
Estado / rol / participación
  ↓
Permiso funcional
  ↓
Acción permitida
```

El modelo de Cursos actualmente observado es:

```text
Clave de permiso histórica en sesión
  ↓
Acceso general a una pantalla
  ↓
Consulta y múltiples acciones administrativas
```

Esta diferencia impide afirmar que la regla institucional de «Cursos: solo vista» esté implementada por el acceso actual con `aceptado`, `docente`, `admin` o `comite`. La matriz aprobada concede Cursos solo vista a determinados estados de estudiantes y profesores, conserva la acumulación de roles autorizados y no permite inferir facultades administrativas desde la visualización.

## 6. Alternativas de separación

### Alternativa A — Separar validaciones dentro del módulo actual

Separar explícitamente las validaciones de **Consulta Cursos** y **Administración Cursos**, conservando por ahora las claves históricas que correspondan.

- **Ventajas:** cambio incremental; permite aislar consulta y hacerla candidata a consumo posterior desde `Authorization`; facilita pruebas por acción y mantiene reversibilidad.
- **Riesgos:** exige definir y aplicar controles también en el endpoint; si se escoge sin resolución una de las condiciones históricas divergentes, puede alterar accesos; las capacidades administrativas siguen sin un permiso funcional formal.
- **Impacto:** potencialmente en `ver.curso.php`, `curso.js` y `ajax/curso.php`; debe verificarse compatibilidad con navegación, sesiones y permisos acumulativos.

### Alternativa B — Crear permisos funcionales diferenciados

Modelo conceptual, no implementado:

```text
CURSOS_VER
CURSOS_CREAR
CURSOS_EDITAR
CURSOS_ELIMINAR
```

- **Ventajas:** expresa el modelo EPIC-003 con precisión; permite políticas auditables por acción; evita deducir administración desde la vista.
- **Riesgos:** requiere definir política institucional para las capacidades administrativas, mapeo y convivencia con permisos históricos, migración de `permiso_login` y pruebas de no ampliación de privilegios.
- **Compatibilidad:** no es compatible como cambio de piloto equivalente, porque la TASK antecedente excluye crear permisos, modificar `permiso_login`, sesiones y CRUD. Requeriría alcance aprobado adicional.

### Alternativa C — Mantener temporalmente el modelo actual

- **Ventajas:** preserva de inmediato el comportamiento heredado y no toca permisos ni sesiones.
- **Riesgos:** mantiene la administración implícita para quien logra abrir la pantalla, la falta de frontera declarada en el endpoint y la imposibilidad de migrar solo consulta con seguridad demostrable.
- **Impacto futuro:** posterga el piloto de Cursos y aumenta el costo de resolver la divergencia entre navegación y URL directa.

## 7. Compatibilidad futura con Authorization

Una vez que consulta y administración tengan fronteras independientes, la evolución posterior de consulta podría ser:

```text
Solicitud de consulta de Cursos
  ↓
Authorization
  ↓
Permiso funcional o política de consulta explícita
  ↓
Lectura permitida
```

Esa evolución debe preservar el acceso efectivo que se apruebe como equivalente y no debe convertir automáticamente un estado académico, una participación o una clave histórica en un permiso funcional. La separación por capacidad debe aplicarse igualmente a los controles de endpoint, no solo a la presentación.

## 8. Impacto técnico potencial

La siguiente lista es identificatoria y no autoriza cambios:

- `form-doc/ver.curso.php`: punto de entrada, visibilidad de controles y separación de vista frente a administración.
- `form-doc/scripts/curso.js`: llamadas `read`, `query_id`, `insert-update` y `delete` que tendrían que corresponder a capacidades diferenciadas.
- `ajax/curso.php`: frontera de operaciones que necesitaría autorización explícita por capacidad en una implementación futura.
- `form-doc/header.php`: verificación de la discrepancia de navegación respecto del acceso directo.
- `src/Security/Authorization.php`: consumidor potencial de la política de consulta; Cursos aún no lo utiliza.
- `ajax/login.php`, `src/Model/Login.php` y `permiso_login`: solo para verificar compatibilidad de la materialización de sesión; no deben modificarse por una migración de consulta equivalente.
- `src/Model/Curso.php` y el almacenamiento de programas: para definir garantías de las operaciones de escritura y eliminación en una iniciativa aprobada.

## 9. Riesgos

- Pérdida de acceso si se reemplaza una condición histórica en vez de preservar acumulativamente los permisos efectivos aprobados.
- Aumento accidental de privilegios si una capacidad de lectura hereda creación, edición o eliminación por compartir pantalla o endpoint.
- Duplicación o ambigüedad de permisos durante una convivencia entre claves históricas y permisos funcionales nuevos.
- Complejidad de migración por la divergencia entre navegación y URL directa, y por el mapeo no confirmado de claves históricas a estados institucionales.
- Cambio observable para usuarios si se ocultan o bloquean controles administrativos sin definir primero la política que los habilita.
- Operaciones críticas expuestas sin una política verificable por endpoint si la separación se limita a la interfaz.

## 10. Recomendación técnica

No es suficiente extender la TASK de consulta actual para separar capacidades: su alcance explícitamente excluye CRUD, permisos nuevos, `permiso_login`, sesiones y administración. Antes de cualquier implementación se requiere una **TASK técnica previa e independiente** para establecer la política de separación entre consulta y administración, incluidas las fronteras de endpoint y la equivalencia con las condiciones históricas.

La Feature vigente puede cubrir el análisis y una separación mínima que conserve permisos existentes, porque prevé centralización incremental por módulo. Si se pretende introducir permisos funcionales nuevos, modificar `permiso_login` o aplicar directamente la matriz institucional a las operaciones administrativas, se requiere ampliar la Feature o crear una nueva Feature según el alcance que se apruebe.

El siguiente paso incremental recomendado es documentar y validar: (1) la política histórica que debe prevalecer entre navegación y acceso directo, (2) qué actor puede administrar cada operación, y (3) la estrategia de protección por endpoint. No corresponde crear esa TASK ni implementar cambios en este análisis.

## 11. Estado de EPIC-003

```text
EPIC-003:

Arquitectura:
✅ Consolidada

Resolución permisos:
✅ Aprobada

Feature autorización:
🟢 Activa

Reglamento:
✅ Validado

Cursos:
🟡 Bloqueado por separación de capacidades

TASK consulta Cursos:
⏸ Detenida
```

## 12. Hallazgos y límites de certeza

### Confirmados

- El flujo activo reúne listado, detalle, creación, edición y eliminación.
- La entrada de Cursos autoriza directamente por `$_SESSION['admin']`, `$_SESSION['comite']`, `$_SESSION['aceptado']` o `$_SESSION['docente']`.
- `ajax/curso.php` expone `read`, `query_id`, `insert-update`, `read_cursos` y `delete` sin una comprobación declarada de sesión o permiso.
- La pantalla y la navegación aplican condiciones diferentes para Cursos.
- La eliminación del programa asociado se realiza físicamente mediante `unlink(...)` cuando la eliminación del curso informa éxito.
- La regla EPIC-003 distingue solo vista de capacidades administrativas y conserva roles acumulativos autorizados.

### Hipótesis o validaciones pendientes

- Correspondencia completa entre `aceptado`, `docente`, `estudiante` y los estados institucionales de estudiante o profesor.
- Política histórica que debe preservarse ante la divergencia entre enlace y URL directa.
- Capacidad funcional y actores autorizados para crear, editar y eliminar Cursos.
- Naturaleza lógica o física de la eliminación del registro `curso` dentro del modelo de persistencia.
- Vigencia de `ingr.curso.php` frente al flujo AJAX analizado.

### Fuentes utilizadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md)
- [TASK-EPIC003-AUTORIZACION-CURSOS-CONSULTA-PILOTO-001.md](../tasks/TASK-EPIC003-AUTORIZACION-CURSOS-CONSULTA-PILOTO-001.md)
- [AT-EPIC003-AUTORIZACION-CURSOS-001.md](AT-EPIC003-AUTORIZACION-CURSOS-001.md)
- [AT-EPIC003-AUTORIZACION-CENTRALIZACION-001.md](AT-EPIC003-AUTORIZACION-CENTRALIZACION-001.md)
- Evidencia técnica observada: `form-doc/ver.curso.php`, `form-doc/scripts/curso.js` y `ajax/curso.php`.

## 13. Restricciones cumplidas

- Sin cambios de código fuente.
- Sin cambios SQL.
- Sin cambios ADR, Roadmap, Manual Maestro, Feature ni TASK existentes.
- Sin implementación.
- Sin commit.
