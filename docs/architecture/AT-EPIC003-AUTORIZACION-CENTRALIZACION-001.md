# AT-EPIC003-AUTORIZACION-CENTRALIZACION-001

## Análisis técnico documental de centralización incremental de autorización

## 1. Identificación y alcance

- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Clasificación:** [ARQ] Evolución de arquitectura de autorización; [TEC] análisis de impacto de implementación; [DOC] documento técnico oficial.
- **Estado:** en análisis; no autoriza implementación.

Este documento evalúa la viabilidad de una primera evolución incremental: centralizar la evaluación de autorizaciones de los módulos manteniendo inicialmente `login`, `permiso_login` y el modelo de sesión PHP actual. No modifica código, SQL, modelo de datos, ADR, Roadmap, Manual Maestro, actas ni TASK.

## 2. Principios y objetivo de la evolución

ADR-002 y la resolución institucional preservan las separaciones conceptuales:

```text
Estado / Rol / Participación
  ≠
Permiso funcional
```

La centralización propuesta no pretende todavía calcular permisos desde estados, roles o participaciones. Su objetivo inmediato es interponer un punto común de evaluación entre los módulos y la fuente actual:

```text
Módulo
  ↓
Authorization
  ↓
claves de sesión construidas desde permiso_login
```

De este modo, la fuente persistente y la construcción de sesión continúan siendo compatibles, mientras el consumo de autorizaciones se hace más uniforme y preparado para una derivación futura de permisos funcionales.

## 3. Inventario de autorización actual documentado

### 3.1 Flujo vigente

```text
Credenciales
  ↓
Login::validarPermiso()
  ↓
login JOIN permiso_login
  ↓
ajax/login.php
  ↓
$_SESSION por id_permiso
  ↓
controles de acceso y navegación
```

Según el análisis técnico antecedente, `Login::validarPermiso()` obtiene los permisos asociados a la cuenta mediante `login` y `permiso_login`. `ajax/login.php` materializa los resultados como las claves de sesión `admin`, `comite`, `aceptado`, `docente` y `estudiante`.

La autorización efectiva se determina al comprobar la existencia de dichas claves. La implementación admite múltiples permisos por login y, por tanto, múltiples claves en una sesión.

### 3.2 Puntos de validación documentados

| Área | Consumo documentado | Observación |
| --- | --- | --- |
| Inicio administrativo | `Authorization::hasAny()` | Uso de componente central existente. |
| Cursos | `admin`, `comite`, `aceptado` o `docente` | Control directo del módulo documentado. |
| Calendario | `admin`, `comite` o `aceptado` | Control directo del módulo documentado. |
| Reglamento | `admin`, `comite` o `aceptado` | Control directo del módulo documentado. |
| Ficha estudiante | `admin`, `comite` o `estudiante` | Control directo del módulo documentado. |
| Ficha docente | `admin`, `comite` o `docente` | Control directo del módulo documentado. |
| Navegación | Combinaciones de sesiones en `header.php` | Puede diferir de la protección de acceso directo. |
| Redirección inicial | Prioridad entre claves de sesión | Puede condicionar la experiencia de permisos coexistentes. |

Los módulos consumen directamente sesión allí donde aplican `isset($_SESSION[...])`; el antecedente documenta una centralización parcial, no una única política de evaluación.

## 4. Estado actual de `Authorization`

El componente `src/Security/Authorization.php` posee una responsabilidad acotada: `hasAny()` comprueba si existe alguna de las claves de sesión solicitadas. Representa un punto central parcial porque ya abstrae una parte de la lectura de sesión para al menos un módulo.

Sus limitaciones documentadas son:

- No constituye el único camino de autorización: coexiste con inspecciones directas de `$_SESSION`.
- Evalúa nombres históricos de sesión, no capacidades funcionales institucionales como Perfil, Reglamento, Calendario o Cursos.
- No expresa procedencia, acumulación, vigencia ni reglas institucionales de un permiso.
- No resuelve la divergencia documentada entre navegación y control directo de Cursos.

Puede evolucionar como punto central, siempre que la primera etapa mantenga su semántica actual: preguntar por capacidades existentes en sesión sin modificar cómo se obtienen ni persistir permisos nuevos.

## 5. Problemas actuales

- **Controles distribuidos:** cada módulo puede repetir combinaciones de claves, con riesgo de que cambios futuros se apliquen de modo desigual.
- **Dependencia directa de sesión:** los módulos quedan acoplados a la estructura PHP y a la existencia de claves concretas.
- **Nombres históricos:** `aceptado`, `docente` y `estudiante` son nombres técnicos de sesión cuya equivalencia completa con estado, rol o capacidad aprobada no está validada.
- **Dificultad para aplicar la resolución:** la matriz institucional habla de permisos funcionales, mientras los módulos consultan claves heredadas; sin centralización, cualquier adaptación exigiría localizar y coordinar cada control.
- **Coexistencia de permisos:** las combinaciones autorizadas de profesor, Comité y administrador requieren una evaluación acumulativa consistente, que no debe depender de copias de lógica por módulo.

## 6. Evolución propuesta y viabilidad

La primera evolución es viable como un cambio incremental de consumo, no de fuente:

```text
Módulo protegido
  ↓
Authorization evalúa una solicitud de acceso
  ↓
claves $_SESSION existentes
  ↓
permisos ya cargados desde permiso_login
```

En esta fase, los módulos comenzarían a delegar comprobaciones equivalentes a `Authorization`; `ajax/login.php`, `src/Model/Login.php`, `login`, `permiso_login` y la estructura de sesión se conservarían sin cambios funcionales. La evolución no implementaría todavía el encadenamiento institucional estado/rol/participación → permiso funcional.

### Ventajas

- Reduce duplicación de comprobaciones sin cambiar la fuente actual de permisos.
- Permite verificar por módulo que el comportamiento previo se conserva.
- Establece un límite claro para que una futura capa traduzca permisos funcionales sin modificar todos los módulos simultáneamente.
- Facilita identificar divergencias y cubrirlas con pruebas de regresión antes de cambiar reglas institucionales.

### Riesgos

- Una abstracción incompleta puede reproducir las mismas combinaciones históricas sin resolver su significado.
- Migrar un módulo sin equivalencia exacta puede ampliar o restringir acceso accidentalmente.
- Centralizar solo controles de acceso, sin navegación y redirección, puede conservar experiencias inconsistentes.
- Tratar una clave histórica como permiso funcional consolidaría el acoplamiento que EPIC-003 busca evitar.

### Compatibilidad y esfuerzo

La propuesta es compatible con el inicio de sesión y permisos múltiples documentados, siempre que el componente siga consultando las claves existentes. El esfuerzo inicial es **medio y acotable por módulo**: requiere inventario de reglas por módulo, pruebas de acceso autorizado/denegado y migración gradual de cada control, pero no exige cambios de datos ni sesión en la primera etapa.

## 7. Compatibilidad futura con la resolución EPIC-003

Centralizar el punto de consumo prepara, pero no implementa, el modelo aprobado:

```text
Estado académico/institucional
  + Rol institucional
  + Participación autorizada
  ↓
Reglas institucionales de acceso
  ↓
Permisos funcionales efectivos
  ↓
Authorization
  ↓
Módulo
```

En una fase posterior, `Authorization` podría recibir capacidades funcionales —por ejemplo, acceso a Reglamento, Calendario o Cursos— y resolverlas contra la fuente que sea aprobada. Mientras tanto, la fase inicial debe conservar una adaptación explícita entre esas solicitudes y las claves de sesión actuales, sin declarar que una clave sea un estado o rol institucional.

Esta separación es especialmente relevante para permisos acumulativos: el componente futuro debe poder combinar las capacidades provenientes de un estado y de roles como Comité o administrador sin reconstruir ni eliminar permisos independientes.

## 8. Evaluación de módulos piloto

| Candidato | Riesgo | Evidencia actual | Valor para piloto | Evaluación |
| --- | --- | --- | --- | --- |
| Reglamento | Bajo; módulo de solo lectura. | Control documentado: `admin`, `comite` o `aceptado`. | Permite validar delegación al componente con una combinación simple y observar compatibilidad. | **Mejor candidato inicial.** |
| Cursos | Mayor impacto funcional. | Acceso directo admite `admin`, `comite`, `aceptado` o `docente`; su navegación exige una combinación distinta. | Expone pronto la relación con estados y la divergencia navegación/acceso. | Candidato posterior, una vez validado el patrón. |

Se recomienda Reglamento como piloto porque acota el riesgo y no requiere resolver todavía la discrepancia documentada de Cursos. El piloto no debe reinterpretar `aceptado` ni alterar la matriz institucional; debe preservar exactamente la política existente y aportar evidencia para la siguiente migración.

## 9. Archivos potencialmente afectados

Se identifican exclusivamente para una eventual implementación:

| Área | Archivos potencialmente afectados | Finalidad de una futura revisión |
| --- | --- | --- |
| Componente central | `src/Security/Authorization.php` | Extender evaluación equivalente de claves actuales. |
| Autenticación y sesión | `ajax/login.php`, `src/Model/Login.php`, `index.php` | Verificar compatibilidad; no requieren cambio en la primera hipótesis. |
| Piloto y controles de módulos | `form-doc/reglamento.php`, `form-doc/ver.curso.php`, `form-doc/calend.acad.php`, `form-doc/info.estudiante.php`, `form-doc/info.docente.php`, `admin/inicio.php`, `form-doc/header.php` | Sustituir gradualmente controles directos y alinear navegación. |
| Fuente y estado | `ajax/estudiante.php`, `src/Model/Estudiante.php`, `src/Model/Usuario.php`, `form-doc/scripts/estudiante.js` | Fuera del piloto inicial; relevantes si se implementan reglas de derivación o ciclo de vida de permisos. |

## 10. Alternativas técnicas

### Alternativa A — Extender `Authorization` existente

- **Ventajas:** reutiliza el punto central parcial, mantiene bajo el alcance y ofrece una transición compatible con las sesiones actuales.
- **Riesgos:** puede crecer sin una interfaz clara si incorpora directamente toda la lógica institucional futura.
- **Compatibilidad:** alta para una primera migración; debe recibir solamente claves/capacidades documentadas y conservar resultados existentes.

### Alternativa B — Crear un servicio o capa nueva de autorización

- **Ventajas:** separa explícitamente el adaptador de sesiones heredadas de una futura evaluación de permisos funcionales.
- **Riesgos:** duplica conceptos con `Authorization` durante la transición y aumenta el alcance antes de validar un piloto.
- **Compatibilidad:** posible mediante adaptador hacia sesión, pero requiere mayor diseño, pruebas y justificación arquitectónica.

### Alternativa C — Migración gradual módulo por módulo

- **Ventajas:** controla el riesgo, permite pruebas de regresión focalizadas y revela divergencias sin afectar la totalidad de la intranet.
- **Riesgos:** coexistencia temporal de controles centralizados y directos; requiere un inventario y trazabilidad rigurosos.
- **Compatibilidad:** alta si cada migración conserva la política efectiva anterior antes de introducir cambios de reglas.

Las alternativas A y C son complementarias: extender de manera mínima el componente existente y adoptar una migración gradual constituye la ruta incremental de menor impacto. Esto no selecciona una implementación ni autoriza cambios.

## 11. Recomendación técnica y siguiente paso

Corresponde completar la revisión técnica de este AT. Si se aprueba, la evolución requiere una **Feature incremental** que delimite la centralización de evaluación, su módulo piloto, equivalencias de acceso y pruebas de regresión. Solo después correspondería crear una **TASK acotada** para el piloto Reglamento; esta TASK no se crea aquí.

No se requiere ADR adicional para la primera centralización compatible, porque ADR-002 ya permite evolución incremental y no se cambia modelo persistente ni se decide una nueva fuente de permisos. Una ADR deberá reevaluarse si una fase futura introduce una capa derivada permanente, una nueva fuente de verdad o una estrategia de coexistencia arquitectónica que exceda estos principios.

## 12. Estado EPIC-003

```text
Arquitectura:
✅ Consolidada

Resolución institucional:
✅ Aprobada

Centralización autorización:
🟡 En análisis

TASK:
No creada
```

## 13. Hallazgos y límites de certeza

### Confirmados

- `permiso_login` es la fuente persistente inmediata de los permisos materializados en sesión al iniciar sesión.
- La autorización está distribuida entre `Authorization::hasAny()` y controles directos de `$_SESSION`.
- La implementación documentada permite múltiples permisos y claves de sesión por login.
- Reglamento y Cursos tienen controles documentados; Cursos presenta además una divergencia entre navegación y acceso directo.
- La resolución institucional aprobó permisos funcionales por estado y acumulación de roles/participaciones autorizadas, sin autorizar su implementación automática.

### Hipótesis pendientes

- La interfaz concreta que debe ofrecer `Authorization` para representar capacidades funcionales sin exponer claves históricas.
- La equivalencia completa entre permisos funcionales aprobados y sesiones actuales.
- La correspondencia técnica completa de estados de estudiante y profesor con sus catálogos institucionales.
- La cobertura efectiva de todos los controles directos de sesión del repositorio.

### Riesgos principales

- Desalinear el resultado autorizado al reemplazar un control directo por una regla central no equivalente.
- Confundir claves de sesión con estado, rol o permiso funcional.
- Mantener divergencia entre navegación y acceso directo durante una migración parcial.
- Reemplazar permisos acumulativos al incorporar futuras reglas de estado.

## 14. Fuentes utilizadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [AT-EPIC003-IMPLEMENTACION-REGLAS-PERMISOS-001.md](AT-EPIC003-IMPLEMENTACION-REGLAS-PERMISOS-001.md)
- [AT-EPIC003-PERMISOS-FUNCIONALES-001.md](AT-EPIC003-PERMISOS-FUNCIONALES-001.md)
- [ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md](../governance/ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md)

## 15. Cierre

No se creó TASK ni se implementaron cambios. El siguiente paso aplicable es la revisión técnica de este documento.
