# AT-EPIC003-CURSOS-VALIDACION-ACCESO-ACEPTADO-001

## Análisis técnico documental: validación de acceso a Cursos para estudiante aceptado

## 1. Identificación y alcance

- **Clasificación:** [ARQ] análisis de divergencia de autorización; [TEC] investigación de regresión funcional de Cursos; [DOC] documento técnico oficial.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Feature relacionada:** FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.
- **TASK en ejecución:** TASK-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.
- **Estado:** análisis técnico documental. No autoriza implementación ni cambios de reglas.

Este documento registra el hallazgo informado durante la validación: un estudiante que se identifica institucionalmente como **aceptado** no visualiza `Programa → Cursos`, aunque el antecedente institucional disponible indica Cursos en modalidad **solo vista**. Se revisan las condiciones actuales de sesión, navegación, acceso directo y endpoint; no se modifica código, SQL, datos ni documentación de gobierno.

## 2. Antecedente institucional y comportamiento técnico

El único antecedente institucional disponible establece:

| Estado institucional | Perfil y datos académicos | Reglamento | Calendario | Cursos |
| --- | --- | --- | --- | --- |
| Aceptado | Sí | Sí | Sí | Solo vista |

El siguiente esquema conceptual se conserva como antecedente, no como matriz operativa aprobada:

```text
Estado académico
  ↓
Reglas institucionales de acceso
  ↓
Permisos funcionales efectivos
  ↓
Acción permitida
```

La implementación heredada no materializa ese flujo. En el inicio de sesión, `permiso_login` produce claves de `$_SESSION`; para Cursos se consumen directamente `admin`, `comite`, `aceptado` y `docente`. En particular, `ajax/login.php` documenta que la clave histórica `aceptado` procede del permiso 3, compartido por «docente o estudiante aceptado».

Por tanto, hay evidencia de una discrepancia de navegación para una sesión que posee solo `aceptado`, pero no evidencia suficiente para afirmar que toda cuenta con esa clave represente de manera unívoca el estado institucional «estudiante aceptado». Estado académico y clave histórica de sesión no son equivalentes confirmados, conforme a ADR-002 y a la Feature vigente.

## 3. Flujo de navegación observado

### 3.1 Menú Programa

`form-doc/header.php` muestra el menú `Programa` si existe alguna de las claves `admin`, `comite`, `aceptado`, `estudiante` o `docente`. Sin embargo, el elemento `Cursos` tiene una condición más restrictiva:

```text
(docente AND aceptado) OR admin OR comite
```

Una sesión con solamente `aceptado` visualiza `Programa`, Calendario y Reglamento, pero no el enlace Cursos. Esto confirma el resultado observado bajo esa composición de sesión.

### 3.2 Acceso directo

`form-doc/ver.curso.php` controla la entrada de la pantalla con una disyunción:

```text
admin OR comite OR aceptado OR docente
```

En consecuencia, si la sesión contiene `aceptado`, la URL directa `form-doc/ver.curso.php` permite entrar. No existe, en esa validación de entrada, el requisito adicional de `docente`.

La diferencia queda representada así:

```text
Sesión con solo aceptado
  ├─ Programa → Cursos: bloqueado por ausencia de docente
  └─ URL directa ver.curso.php: permitido
```

La hipótesis «ambos bloquean» queda descartada para una sesión en la que `aceptado` está materializado. La validación funcional no incluyó una sesión real ni consulta de datos, por lo que no confirma si el caso reportado tenía exclusivamente esa clave o si su sesión no la recibió.

### 3.3 Endpoint y controles actuales

`form-doc/scripts/curso.js` usa `read`, `query_id`, `read_cursos`, `create`, `update` y `delete` contra `ajax/curso.php`. En el estado de trabajo inspeccionado, dicho endpoint ya aplica una frontera por capacidad: visualización para `read`, `query_id` y `read_cursos`; creación, actualización y eliminación para sus respectivas operaciones.

No obstante, las cuatro capacidades conservan actualmente la misma disyunción histórica:

```text
admin OR comite OR aceptado OR docente
```

Esto protege el endpoint respecto de una sesión sin esas claves, pero todavía no transforma «solo vista» en una política efectiva: una sesión que puede visualizar también satisface las condiciones actuales de creación, actualización y eliminación. Los atributos `data-capacidad` incorporados en vista y cliente son identificatorios; no restringen por sí solos la interfaz.

## 4. Comparación de actores y claves efectivas

| Actor o condición | Regla institucional relevante | Clave/sesión técnica observada | Menú Cursos | URL y endpoint actual |
| --- | --- | --- | --- | --- |
| Estudiante aceptado | Cursos solo vista | No hay mapeo unívoco confirmado; puede involucrar `aceptado` | Solo aparece si además existe `docente` | `aceptado` basta técnicamente para acceso y todas las capacidades actuales |
| Profesor aceptado | Pendiente | No hay mapeo institucional completo confirmado; se consumen `docente` y/o `aceptado` | Requiere ambas claves según la condición histórica | Cada una de `docente` o `aceptado` basta técnicamente |
| Comité académico | Pendiente | `comite` | Permitido técnicamente | Permitido técnicamente |
| Administrador | Pendiente | `admin` | Permitido técnicamente | Permitido técnicamente |
| `estudiante` sin otra clave | No equivale por sí solo a estado institucional en el código revisado | `estudiante` | No permitido | No permitido |

La tabla registra condiciones técnicas, no concede ni redefine permisos. Las facultades del Comité, de sus integrantes, de administración y de los roles acumulativos permanecen pendientes.

## 5. Relación con las TASK de Cursos

El hallazgo es directamente relevante para `TASK-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001`: esa TASK ya exige documentar la política efectiva de las condiciones históricas y la divergencia entre navegación y URL directa antes de cualquier implementación. La separación recién incorporada en el endpoint no resuelve la divergencia del menú ni la diferencia entre solo vista y administración, porque las capacidades mantienen idéntica matriz histórica.

También afecta a `TASK-EPIC003-AUTORIZACION-CURSOS-CONSULTA-PILOTO-001`, pero no puede corregirse mediante ella sin cambiar su objetivo. Esa TASK debe conservar el comportamiento observable y reutilizar las condiciones existentes; no autoriza derivar acceso desde estado académico ni crear un permiso funcional nuevo. Centralizar la condición actual sin resolver la divergencia conservaría una política ambigua.

## 6. Clasificación del hallazgo

**Clasificación principal: comportamiento legado con decisión pendiente de autorización.**

- **Confirmado:** existe una inconsistencia técnica entre el menú y el acceso directo para las mismas claves históricas. Para `aceptado` aislado, el menú bloquea y la URL permite.
- **No confirmado:** que la clave `aceptado` de la sesión del caso reportado equivalga inequívocamente al estado institucional «estudiante aceptado»; el login declara que esa clave también representa al docente o estudiante aceptado.
- **No clasificado como defecto respecto de una matriz definitiva:** no existe una matriz operativa aprobada ni un mapeo institucional estado → permiso efectivo para Cursos.
- **Regla faltante:** falta la decisión institucional que determine actores, operaciones, roles acumulativos y el alcance de «solo vista».

No se detecta evidencia de una regresión causada por la separación de capacidades en sí: la condición del menú sigue siendo distinta de la entrada directa y la nueva separación de endpoint mantiene las mismas claves para todas las capacidades. La validación debe tratar el hallazgo como preexistente hasta contrastarlo con una versión y un caso de sesión controlado.

## 7. Recomendación

1. **TASK actual:** registrar y validar el caso como condición de detención/revisión de `TASK-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001`; no corregir el menú dentro de esa TASK sin política aprobada de equivalencia y de administración.
2. **TASK de consulta piloto:** mantenerla detenida hasta fijar la equivalencia entre menú, URL directa y permisos de consulta. No debe usarse para aplicar directamente estado académico → acceso.
3. **Nueva TASK:** si la validación institucional confirma que el estado aceptado debe habilitar técnicamente solo visualización y que la condición histórica debe cambiar, se requerirá una TASK posterior y explícita para alinear navegación, acceso directo, interfaz y endpoint. Este documento no la crea.
4. **Actualización documental:** incorporar este AT como evidencia de la divergencia; no corresponde cambiar ADR, Roadmap, Manual Maestro, Feature ni resolución.
5. **Validación institucional:** solicitar confirmación del mapeo operativo entre estado institucional de estudiante aceptado, permiso 3/clave `aceptado`, y las capacidades administrativas de Cursos. Esa confirmación es necesaria antes de elegir entre preservar o cambiar la condición histórica.

## 8. Hallazgos, hipótesis y riesgos

### Confirmados

- El antecedente institucional disponible indica Cursos «solo vista» para estudiante aceptado.
- El enlace `Programa → Cursos` exige `docente` y `aceptado` simultáneamente, excepto para `admin` o `comite`.
- La URL directa de Cursos permite cualquiera de `admin`, `comite`, `aceptado` o `docente`.
- Para una sesión con solo `aceptado`, el menú bloquea y el acceso directo permite.
- El endpoint revisado tiene comprobación por operación, pero utiliza la misma combinación de claves históricas para visualización y acciones administrativas.

### Hipótesis o validaciones pendientes

- La sesión del usuario reportado contaba con `aceptado` aislado y no con una clave ausente por materialización o inicio de sesión.
- El permiso 3/clave `aceptado` representa operacionalmente el estado institucional de estudiante aceptado en ese caso concreto.
- La condición `docente AND aceptado` del menú corresponde a una restricción histórica intencional y no a un error de composición.
- La política institucional para creación, actualización y eliminación de Cursos.

### Riesgos

- Cambiar solo el menú puede ampliar acceso visible sin alinear URL ni endpoint, o producir el efecto inverso.
- Tratar `aceptado` como estado inequívoco puede mezclar estudiantes y docentes u otras condiciones históricas.
- Presentar Cursos a estudiantes aceptados sin separar las capacidades administrativas puede contrariar la regla de «solo vista».
- Centralizar la consulta preservando condiciones divergentes puede consolidar una experiencia inconsistente.

## 9. Validación funcional de la TASK

### 9.1 Evidencia ejecutada sin mutación

| Prueba | Resultado | Estado |
| --- | --- | --- |
| Sintaxis de `ajax/curso.php` | `php -l` sin errores | Confirmada |
| Sintaxis de `form-doc/ver.curso.php` | `php -l` sin errores | Confirmada |
| `POST /ajax/curso.php` con `op=read` sin sesión autorizada | HTTP 403 | Confirmada |
| `GET /form-doc/ver.curso.php` sin sesión autorizada | HTTP 302 a `../index.php` | Confirmada |

Las pruebas verifican que la frontera de endpoint bloquea una solicitud no autorizada y que la entrada de la pantalla conserva el bloqueo sin sesión. No se ejecutaron solicitudes de creación, actualización o eliminación para no alterar cursos, archivos asociados ni datos de prueba.

### 9.2 Matriz efectiva trazada desde la implementación

| Actor | Estado | Visualización | Crear | Actualizar | Eliminar |
| --- | --- | --- | --- | --- | --- |
| Estudiante | Aceptado | Permitida por URL/endpoints si la sesión tiene `aceptado`; menú no visible si no posee además `docente` | La condición actual del endpoint la permite | La condición actual del endpoint la permite | La condición actual del endpoint la permite |
| Profesor | — | Permitida por URL/endpoints con `docente`; menú solo si posee además `aceptado` | La condición actual del endpoint la permite | La condición actual del endpoint la permite | La condición actual del endpoint la permite |
| Comité | — | Permitida | Permitida por la condición actual | Permitida por la condición actual | Permitida por la condición actual |
| Administrador | — | Permitida | Permitida por la condición actual | Permitida por la condición actual | Permitida por la condición actual |

«Permitida por la condición actual» significa que la verificación de sesión del endpoint la autoriza; no acredita la ejecución completa de una operación sobre datos reales ni establece una regla institucional. Para estudiante aceptado, la observación confirma que el requisito de solo vista todavía no está materializado en las condiciones de las capacidades administrativas.

### 9.3 Estado de validación

```text
Separación técnica por operación:     Confirmada por inspección y bloqueo sin sesión.
Capacidades identificadas:            Confirmadas.
Sin regresiones por actor real:        Pendiente de ejecución con cuentas de prueba autorizadas.
Hallazgo estudiante aceptado:          Documentado como pendiente de decisión/autorización.
```

La validación funcional completa no puede declararse cerrada con la evidencia disponible: faltan sesiones de prueba controladas para estudiante aceptado, profesor, Comité y administrador, y pruebas no destructivas o reversibles de sus operaciones permitidas. Este límite no autoriza corrección de menú, cambios de permisos ni reanudación de la centralización.

## 10. Fuentes utilizadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md)
- [TASK-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.md](../tasks/TASK-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.md)
- [TASK-EPIC003-AUTORIZACION-CURSOS-CONSULTA-PILOTO-001.md](../tasks/TASK-EPIC003-AUTORIZACION-CURSOS-CONSULTA-PILOTO-001.md)
- [AT-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.md](AT-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.md)
- [AT-EPIC003-AUTORIZACION-CURSOS-001.md](AT-EPIC003-AUTORIZACION-CURSOS-001.md)
- Evidencia técnica inspeccionada: `form-doc/header.php`, `form-doc/ver.curso.php`, `form-doc/scripts/curso.js`, `ajax/curso.php` y `ajax/login.php`.

## 11. Restricciones cumplidas y siguiente paso

```text
Sin cambios de código.
Sin implementación.
Sin cambios ADR.
Sin cambios Roadmap.
Sin cambios Manual Maestro.
Sin commit.
```

El diagnóstico técnico permanece preservado. Su aplicación operativa queda
sujeta a la actualización institucional posterior.

## 12. Actualización institucional posterior

Fuente: `Derivación institucional formal — Matriz operativa de Cursos`.

El resultado técnico y las pruebas no mutantes se preservan como diagnóstico.
No legitiman el parche local ni acreditan una política institucional.

```text
Autoridad institucional: No identificada
Matriz operativa: No aprobada
Documento Fuente Aprobado: No existe
Protección provisional P-B: Propuesta, no aprobada
AT de materialización: No autorizado
Task técnica: No autorizada
Implementación local: Parcial, no publicable y preservada
Eliminación física: No autorizada
```

Las cuatro operaciones locales continúan usando la misma política histórica
`admin OR comite OR aceptado OR docente`. Los atributos `data-capacidad` no
tienen consumidor y no son capacidades centralizadas. El bloqueo se retira
únicamente con un Documento Fuente Aprobado emitido por autoridad competente.
