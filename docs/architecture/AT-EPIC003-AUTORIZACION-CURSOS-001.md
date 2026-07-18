# AT-EPIC003-AUTORIZACION-CURSOS-001

## Análisis técnico documental: Cursos como segundo piloto de autorización centralizada

## 1. Identificación y alcance

- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Feature asociada:** FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.
- **Clasificación:** [ARQ] evolución de autorización; [TEC] impacto en Cursos; [DOC] documento técnico oficial.
- **Estado:** análisis técnico documental. No autoriza implementación.

Este documento evalúa si el módulo Cursos puede ser el segundo piloto de centralización del consumo de autorizaciones. Identifica evidencia, riesgos y alternativas para una futura decisión. No modifica código, SQL, modelo de datos, ADR, Roadmap, Manual Maestro, Feature, actas ni TASK.

## 2. Marco aplicable

ADR-002 y la resolución de EPIC-003 mantienen separadas identidad, estado académico, rol institucional, participación académica y permiso funcional. La regla institucional aprobada es:

```text
Estado académico/institucional
  ↓
Reglas institucionales de acceso
  ↓
Permisos funcionales efectivos
```

La Feature vigente no implementa todavía esa derivación. Su alcance inicial es centralizar el consumo de las autorizaciones existentes, conservando `login`, `permiso_login` y las claves de sesión. El piloto Reglamento fue validado funcionalmente con ese límite.

## 3. Inventario actual del módulo Cursos

### 3.1 Archivos y responsabilidades identificadas

| Área | Archivo | Responsabilidad actual |
| --- | --- | --- |
| Punto de entrada y pantalla | `form-doc/ver.curso.php` | Protege la entrada, presenta listado, detalle y controles de agregar, editar y eliminar. |
| Navegación | `form-doc/header.php` | Decide si muestra el enlace Cursos dentro de Programa. |
| Cliente | `form-doc/scripts/curso.js` | Solicita listado, detalle, altas/ediciones y eliminación al endpoint de Cursos. |
| Endpoint | `ajax/curso.php` | Atiende operaciones de lectura, consulta por identificador, inserción/edición y eliminación. |
| Persistencia | `src/Model/Curso.php` | Ejecuta las operaciones sobre la entidad `curso`. |
| Sesión y permisos | `ajax/login.php`, `src/Model/Login.php` | Materializan permisos de `permiso_login` como claves de `$_SESSION`. |
| Componente central existente | `src/Security/Authorization.php` | Ofrece `Authorization::hasAny()` sobre claves de sesión; Cursos aún no lo utiliza. |

También existe `ingr.curso.php`, con un control de sesión histórico orientado a Comité. Su relación con el flujo activo debe verificarse antes de una eventual TASK, pues la vista actual opera mediante `form-doc/ver.curso.php` y `ajax/curso.php`.

### 3.2 Flujo efectivo observado

```text
Usuario
  ↓
Inicio de sesión: login + permiso_login
  ↓
$_SESSION[admin|comite|aceptado|docente|estudiante]
  ↓
Validación directa en form-doc/ver.curso.php
  ↓
Módulo Cursos y solicitudes AJAX a ajax/curso.php
```

El punto de entrada de Cursos admite directamente `admin`, `comite`, `aceptado` o `docente`. La vista incluye, sin una separación técnica de autorización, tanto la consulta como los controles que desencadenan escrituras. El endpoint AJAX no declara una comprobación propia de sesión o permiso; por ello, el control de la pantalla no constituye por sí solo una frontera verificable de cada operación.

## 4. Relación con permisos actuales

| Clave actual | Relación con Cursos |
| --- | --- |
| `admin` | Accede directamente a la pantalla; el enlace de navegación también se muestra. |
| `comite` | Accede directamente a la pantalla; el enlace de navegación también se muestra. |
| `aceptado` | Accede directamente a la pantalla; la navegación solo lo habilita junto con `docente`. |
| `docente` | Accede directamente a la pantalla; la navegación solo lo habilita junto con `aceptado`. |
| `estudiante` | No habilita el acceso directo ni el enlace Cursos por sí solo. |

Cursos depende directamente de `$_SESSION`; no consume `Authorization`. Las claves son el resultado de permisos asociados al login, no una consulta directa del estado académico en cada acceso. En consecuencia, no debe asumirse que `aceptado`, `docente` o `estudiante` sean por sí mismos estados institucionales o permisos funcionales consolidados.

La divergencia confirmada es relevante: la pantalla permite una disyunción de cuatro claves, mientras la navegación ofrece Cursos a administración o Comité, o a la combinación simultánea `docente` + `aceptado`. Un usuario puede, por tanto, tener comportamiento distinto al navegar que al llegar por URL directa.

## 5. Relación con la resolución EPIC-003

La matriz aprobada concede Cursos en solo vista a estudiantes aceptados y matriculados; no lo concede a postulantes, graduados o reprobados, y niega todo acceso a retirados y eliminados. Para profesores, Cursos corresponde al estado institucional Aceptado, pero no al Registrado ni al Inhabilitado. Administrador y Comité son roles acumulativos, y una persona puede coexistir como profesor + Comité, profesor + administrador u otras participaciones autorizadas.

La implementación actual no expresa esas capacidades de forma funcional: consume claves históricas de sesión y ofrece una pantalla que combina lectura y administración. Por ello, la equivalencia técnica entre la regla institucional «Cursos: solo vista» y el acceso actual con `aceptado` no está confirmada. Tampoco está validado el mapeo completo entre los estados técnicos de estudiante o profesor y las claves de sesión usadas por Cursos.

Una futura centralización de consumo puede conservar inicialmente la política efectiva vigente, pero no debe afirmar que con ello implementa la matriz institucional ni automatizar estado → permiso.

## 6. Riesgos específicos de Cursos

- **Navegación versus acceso directo:** las condiciones actuales no son equivalentes, con riesgo de experiencias contradictorias o de ampliar/restringir acceso al intentar unificarlas.
- **Consulta versus administración:** la misma pantalla y el mismo endpoint reúnen lectura, altas, edición y eliminación. La matriz aprobada habla de «solo vista» para estudiantes y no autoriza inferir facultades administrativas desde esa capacidad.
- **Estados académicos:** `aceptado` es una clave histórica; no confirma por sí sola si representa estudiante aceptado, matriculado, profesor aceptado o una capacidad funcional.
- **Permisos históricos y acumulación:** una persona puede mantener `admin`, `comite`, `docente` y otras claves. Una migración que reemplace en vez de consultar acumulativamente puede alterar capacidades legítimas.
- **Controles de endpoint:** la ausencia de una validación declarada en `ajax/curso.php` eleva el riesgo de que una migración limitada a la vista deje operaciones críticas sin una política equivalente verificable.
- **Reversibilidad:** cambiar solamente la consulta de entrada es fácilmente reversible; separar o restringir operaciones de escritura afecta flujos académicos y requiere evidencia funcional adicional.

## 7. Factibilidad como segundo piloto

**Cursos es candidato condicionado, no candidato inmediato para una migración integral.** Es adecuado como segundo piloto únicamente si el alcance se limita explícitamente a la evaluación centralizada y equivalente del acceso de consulta, sin cambiar permisos, estados, sesión ni operaciones de administración.

| Aspecto | Reglamento validado | Cursos |
| --- | --- | --- |
| Naturaleza | Solo lectura | Consulta y administración reunidas |
| Riesgo | Bajo | Medio/alto por impacto académico potencial |
| Regla actual | Combinación simple validada | Acceso y navegación divergentes |
| Reversibilidad | Alta | Alta solo para consulta; menor para escritura |

Sus ventajas son que ya cuenta con un control de entrada identificable, expone una divergencia de alto valor para validar el patrón y permite comprobar compatibilidad con permisos acumulativos. Su complejidad es mayor que Reglamento: antes de migrar debe fijarse qué política histórica se preserva y qué alcance se excluye.

## 8. Alternativas técnicas a evaluar

No se selecciona ni implementa alternativa.

### Alternativa A — Migrar solo la validación de acceso de consulta

Sustituir de forma equivalente el control de entrada de la vista por una consulta a `Authorization`, manteniendo las claves de sesión y sin cambiar las operaciones ni su disponibilidad observable. Tiene el menor alcance y máxima reversibilidad, pero no resuelve la divergencia de navegación ni diferencia escritura de consulta.

### Alternativa B — Separar visualización y administración

Definir controles diferenciados para lectura y operaciones de alta, edición y eliminación. Se alinea mejor con la matriz de «solo vista», pero exige aclarar capacidades administrativas y controlar también los endpoints; excede un piloto de consumo equivalente.

### Alternativa C — Esperar evolución de reglas académicas

Posponer Cursos hasta validar el mapeo entre estados técnicos, estados institucionales y capacidades funcionales, además de la política administrativa. Reduce el riesgo de consolidar nombres históricos, aunque retrasa la evidencia de un segundo módulo.

## 9. Archivos potencialmente afectados en una iniciativa futura

La siguiente lista es únicamente identificatoria:

- `form-doc/ver.curso.php` y `form-doc/header.php`.
- `form-doc/scripts/curso.js`.
- `ajax/curso.php` y, si se confirma su vigencia funcional, `ingr.curso.php`.
- `src/Model/Curso.php`.
- `src/Security/Authorization.php`.
- `ajax/login.php`, `src/Model/Login.php` e `index.php`, solo para verificación de compatibilidad de sesión y redirección.

## 10. Recomendación técnica

Se recomienda aprobar Cursos como **segundo piloto condicionado a una futura TASK independiente y acotada a Alternativa A**, después de aclarar documentalmente la política de equivalencia entre acceso directo y navegación. Esa TASK deberá excluir de manera expresa altas, edición, eliminación, cambios de estado y cambios a `Authorization.php` salvo que su alcance aprobado los contemple.

No se requiere una Feature adicional para analizar o para un piloto equivalente: la Feature vigente prevé migraciones progresivas por módulo. Sí requiere TASK antes de cualquier modificación. Si la revisión pretende separar lectura de administración o aplicar la matriz institucional de Cursos, será necesaria una aclaración funcional previa y posiblemente una ampliación o nueva Feature según el alcance aprobado. No corresponde crear ninguna de ellas en este documento.

## 11. Estado EPIC-003

```text
Arquitectura:
✅ Consolidada

Resolución permisos:
✅ Aprobada

Piloto Reglamento:
✅ Validado

Piloto Cursos:
🟡 En análisis

TASK:
No creada
```

## 12. Hallazgos y límites de certeza

### Confirmados

- La fuente operativa actual de las claves de sesión es `permiso_login`, materializada en el inicio de sesión.
- Cursos utiliza controles directos de `$_SESSION`; no utiliza el componente `Authorization`.
- La protección directa de Cursos y su enlace de navegación no aplican la misma combinación de claves.
- La pantalla activa concentra lectura y acciones de administración de Cursos.
- La resolución vigente autoriza Cursos solo vista para determinados estados de estudiantes y profesores, y mantiene la acumulación de roles autorizados.

### Hipótesis o validaciones pendientes

- La correspondencia completa entre `aceptado`, `docente`, `estudiante` y los estados institucionales de estudiante o profesor.
- La política histórica exacta que debe conservarse cuando navegación y URL directa divergen.
- Qué capacidad funcional habilita administrar Cursos y qué controles deben aplicarse en cada endpoint.
- La vigencia del flujo histórico de `ingr.curso.php` frente al flujo AJAX actual.

### Riesgos principales

- Ampliar o restringir acceso por elegir una de las dos condiciones actuales sin resolución funcional.
- Interpretar una clave histórica de sesión como estado o permiso funcional.
- Conceder administración cuando la regla institucional aprobada solamente concede vista.
- Preservar una validación de pantalla sin controlar las operaciones de escritura asociadas.

## 13. Fuentes utilizadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md)
- [AT-EPIC003-AUTORIZACION-CENTRALIZACION-001.md](AT-EPIC003-AUTORIZACION-CENTRALIZACION-001.md)
- [AT-EPIC003-IMPLEMENTACION-REGLAS-PERMISOS-001.md](AT-EPIC003-IMPLEMENTACION-REGLAS-PERMISOS-001.md)
- [ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md](../governance/ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md)

La identificación del inventario se contrastó con los archivos vigentes del módulo, sin modificar ninguno.

## 14. Cierre

No se creó TASK ni se implementaron cambios. El siguiente paso aplicable es la revisión técnica de `AT-EPIC003-AUTORIZACION-CURSOS-001.md`.
