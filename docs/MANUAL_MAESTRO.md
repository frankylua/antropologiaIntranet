# Manual Maestro — Modernización Intranet Doctorado de Antropología

## 1. Objetivo del proyecto

La modernización de la Intranet del Doctorado de Antropología es incremental. Su propósito es realizar cambios basados en conocimiento validado, con alcance controlado, compatibilidad funcional y trazabilidad documental.

El sistema heredado se preserva mientras se lo evoluciona: cada cambio debe ser pequeño, reversible, verificable y compatible con el comportamiento funcional existente. Este Manual resume fuentes validadas; no sustituye las fuentes especializadas.

## 2. Principios del proyecto

- Evolución incremental y alcance controlado.
- Trazabilidad entre descubrimientos, decisiones, tareas, validaciones y evidencia Git.
- Cambios pequeños, reversibles y verificables.
- Separación entre las decisiones de arquitectura y su ejecución.
- Validación del conocimiento antes de implementar.

La metodología, los controles y los estados aplicables se rigen por [WORKFLOW.md](WORKFLOW.md).

## 3. Estado general del proyecto

El proyecto mantiene una estrategia de modernización incremental. Según [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md), el descubrimiento y la documentación están en progreso; la planificación, implementación y frontend figuran como pendientes en el estado estratégico global.

Existen tareas e incrementos ejecutados y registrados en [TASKS.md](TASKS.md), sin que esto implique que la modernización completa del sistema se encuentre finalizada.

El [ROADMAP.md](roadmap/ROADMAP.md) registra las épicas EPIC-001 a EPIC-008: autorización y permisos, identidades y cuentas, estados académicos y roles, integridad transaccional, backend PHP, ficha académica, interfaz web y gobierno del modelo de datos y persistencia.

EPIC-003 cuenta con validación arquitectónica documentada en ADR-002, un acta inicial que establece su marco de validación institucional y resoluciones institucionales iniciales registradas. La implementación permanece pendiente de definición técnica y no está autorizada directamente por dichas resoluciones.

Los documentos principales del proyecto son este Manual, PROJECT_CONTEXT, WORKFLOW, ROADMAP, ADR, arquitectura, descubrimientos y TASKS.

## 4. Arquitectura consolidada

Las decisiones arquitectónicas vigentes se registran exclusivamente en [docs/adr/](adr/). Este Manual no las reemplaza ni agrega decisiones nuevas.

- ADR-001 establece un contrato explícito para operaciones de escritura y está aprobada.
- ADR-002 establece la evolución del modelo de identidad y participación académica; está aprobada arquitectónicamente y pendiente de implementación.
- Los ADR de regularización A2 y A3 disponibles en el repositorio figuran como aprobados en sus documentos de origen.

La separación entre identidad, acceso, autorización y dominio debe interpretarse según ADR-002 y el modelo de dominio; no se definen aquí soluciones físicas ni reglas adicionales.

## 5. Modelo de dominio

El modelo de dominio funcional actual se encuentra en [DOMAIN_MODEL.md](architecture/DOMAIN_MODEL.md). Esta fuente describe los conceptos, actores, módulos, procesos, estados, contradicciones y preguntas pendientes del sistema actual.

El registro [DISCOVERY_LOG.md](discovery/DISCOVERY_LOG.md) mantiene la trazabilidad de los hallazgos. El descubrimiento del modelo de dominio (DISCOVERY-004) figura como validado; otros descubrimientos conservan el estado indicado en su fuente.

## 6. Gobierno documental

La relación documental y de ejecución se rige por WORKFLOW:

```text
EPIC
 ↓
FEATURE
 ↓
AT
 ↓
TASK
 ↓
Implementación
 ↓
Validación
 ↓
Commit
```

ADR: decisión arquitectónica aplicable cuando corresponde. Define restricciones y criterios de evolución; no constituye una etapa obligatoria del flujo.

Para EPIC-003, la relación documental aplicable es:

```text
EPIC-003
 ↓
ADR-002
 ↓
ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001
 ↓
ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001
```

Cada fuente especializada conserva autoridad sobre este Manual. Si existe una diferencia, debe corregirse el documento de resumen sin inferir cambios en la fuente especializada.

## 7. Documentos oficiales relacionados

| Documento o conjunto | Función | Ubicación |
| --- | --- | --- |
| PROJECT_CONTEXT | Contexto inicial y resumen del estado | [docs/PROJECT_CONTEXT.md](PROJECT_CONTEXT.md) |
| WORKFLOW | Gobierno metodológico y controles | [docs/WORKFLOW.md](WORKFLOW.md) |
| ROADMAP | Planificación estratégica por EPIC | [docs/roadmap/ROADMAP.md](roadmap/ROADMAP.md) |
| ADR | Decisiones arquitectónicas aprobadas | [docs/adr/](adr/) |
| GOVERNANCE | Marco de validación institucional de EPIC-003 | [docs/governance/ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md](governance/ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md) |
| GOVERNANCE | Documento de resoluciones institucionales asociadas a EPIC-003 | [docs/governance/ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md](governance/ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md) |
| ARCHITECTURE | Modelo de dominio y análisis arquitectónico disponible | [docs/architecture/](architecture/) |
| DISCOVERY | Hallazgos y trazabilidad de descubrimiento | [docs/discovery/](discovery/) |
| TASKS | Registro de tareas | [docs/TASKS.md](TASKS.md) |

## 8. Decisiones y estados relevantes

| Fecha | Decisión o estado | Fuente |
| ----- | ----------------- | ------ |
| No registrada en la fuente | ADR-001 aprobada: contrato explícito para operaciones de escritura. | [ADR-001](adr/ADR-001-contrato-explicito-operaciones-escritura.md) |
| No registrada en la fuente | ADR-002 aprobada arquitectónicamente; su implementación está pendiente. | [ADR-002](adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md) |
| No registrada en la fuente | El acta inicial de EPIC-003 establece el marco de validación institucional. | [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001](governance/ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md) |
| 2026-07-17 | Las resoluciones institucionales de EPIC-003 complementan ADR-002 sin modificar sus decisiones arquitectónicas; la implementación permanece pendiente. | [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001](governance/ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md) |
| 2026-07-11 | El modelo de dominio funcional fue validado en DISCOVERY-004. | [DISCOVERY_LOG](discovery/DISCOVERY_LOG.md) |

## 9. Pendientes del proyecto

### Aprobado

- El modelo de dominio funcional actual, conforme a DISCOVERY-004.
- Las decisiones ADR con estado aprobado en sus fuentes.

### Pendiente

- Completar la revisión de la arquitectura inicial, del modelo de datos y del alcance efectivo de roles y permisos.
- Resolver las reglas institucionales abiertas mediante la Matriz de Negocio.
- La implementación de ADR-002.
- Los estados de planificación, implementación y frontend indicados en PROJECT_CONTEXT.

### No decidido

- La selección del módulo piloto de modernización, cuya información requerida se mantiene en ROADMAP.

## 10. Historial del Manual Maestro

```text
Fecha de creación: 2026-07-17
Motivo: Formalizar el documento rector transversal previamente referenciado por WORKFLOW.md y ausente del repositorio.
Fuente: PROJECT_CONTEXT.md, WORKFLOW.md, ROADMAP.md, docs/adr/, docs/architecture/, docs/discovery/ y TASKS.md.

Fecha de actualización: 2026-07-17
Motivo: Incorporación del acta de resoluciones institucionales de EPIC-003.
Fuente: ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md.
```

Pendiente de revisión de Dirección Técnica antes de cualquier integración documental.
