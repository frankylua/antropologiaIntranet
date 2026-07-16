# Flujo oficial de modernización incremental

## 1. Propósito

Este documento define el flujo oficial de trabajo para modernizar incrementalmente la intranet PHP heredada. Su objetivo es asegurar que cada cambio parta de conocimiento validado, tenga un alcance controlado, conserve la compatibilidad funcional y quede documentado en el repositorio.

## 2. Principios obligatorios

- Comprender antes de modificar.
- No asumir reglas de negocio.
- Un objetivo por tarea.
- Un cambio lógico por commit.
- No modificar archivos fuera de alcance.
- No ejecutar migraciones destructivas sin autorización.
- No incluir secretos ni credenciales.
- Revisar siempre el diff.
- Mantener compatibilidad funcional.
- Aplicar cambios pequeños, reversibles y verificables.
- El repositorio es la fuente principal de verdad.
- ChatGPT valida, planifica y supervisa.
- Codex inspecciona y ejecuta tareas controladas.

## 3. Responsabilidades de ChatGPT

ChatGPT debe validar los hallazgos, separar hechos de supuestos, planificar el trabajo y supervisar el cumplimiento del alcance. También debe revisar los informes de descubrimiento, confirmar que las reglas de negocio estén validadas antes de usarlas y autorizar el paso entre las etapas definidas en este flujo.

## 4. Responsabilidades de Codex

Codex debe inspeccionar el repositorio y ejecutar tareas controladas. En descubrimiento, debe producir un informe sin cambios. En implementación, debe presentar un plan, esperar su aprobación, modificar solo los archivos autorizados, revisar el diff, ejecutar las pruebas correspondientes y comunicar los resultados sin ocultar incertidumbres ni cambios preexistentes.

## 5. Responsabilidad del repositorio

El repositorio es la fuente principal de verdad para el código, el comportamiento observable y la documentación oficial. Debe conservar la trazabilidad entre descubrimientos, reglas de negocio, tareas, decisiones ADR y commits. Ninguna conversación sustituye la actualización documental que corresponda dentro del repositorio.

## 6. Ciclo de descubrimiento

El ciclo oficial es:

Pregunta
→ chat nuevo de Codex `DISCOVERY-XXX`
→ informe sin cambios
→ revisión en Chat 01
→ validación
→ actualización documental
→ resumen al Manual Maestro
→ siguiente investigación.

Cada descubrimiento responde una pregunta delimitada. Codex inspecciona y registra evidencias sin modificar el repositorio. ChatGPT revisa el informe en Chat 01 y determina qué contenido queda validado. Solo entonces se actualizan los documentos oficiales y se incorpora un resumen al Manual Maestro.

## 7. Ciclo de implementación

El ciclo oficial es:

Problema confirmado
→ tarea en Chat 02
→ preparación en Chat 03
→ chat nuevo de Codex `TASK-XXX`
→ plan
→ aprobación
→ modificación
→ revisión del diff
→ pruebas
→ commit
→ actualización documental.

La implementación comienza solo con un problema confirmado. La tarea debe tener un objetivo, alcance, reglas de negocio y criterios de cierre definidos antes de que Codex presente el plan. Ninguna modificación se realiza antes de aprobar ese plan.

## 8. Nomenclatura

### Descubrimientos

Formato: `DISCOVERY-XXX: descripción breve`.

`XXX` es un número correlativo de tres dígitos. Ejemplo: `DISCOVERY-001: flujo de aprobación documental`.

### Tareas

Formato: `TASK-XXX: objetivo único`.

`XXX` es un número correlativo de tres dígitos. Ejemplo: `TASK-001: validar el formulario de congreso`.

### Decisiones ADR

Formato: `ADR-XXX: decisión`.

`XXX` es un número correlativo de tres dígitos. Ejemplo: `ADR-001: conservar compatibilidad con el flujo heredado`.

### Ramas Git

La rama debe conservar la trazabilidad del trabajo desde su identificador hasta la integración:

```text
EPIC
→ FEATURE
→ AT
→ TASK
→ rama
→ commit
→ integración
```

Las ramas de fase agrupan incrementos arquitectónicos relacionados y usan el formato `refactor/fase-XX-nombre`, donde `XX` es un número correlativo de dos dígitos. Ejemplos: `refactor/fase-01-seguridad`, `refactor/fase-02-modelo-dominio` y `refactor/fase-03-persistencia`.

El trabajo específico se realiza en una rama asociada a una tarea aprobada. Los segmentos descriptivos deben escribirse en minúsculas, sin tildes ni espacios, con palabras separadas por guiones; el identificador de tarea conserva su formato oficial. La rama debe seguir uno de estos formatos:

- `feature/TASK-ID-descripcion`: incorporación de funcionalidad.
- `fix/TASK-ID-modulo-descripcion`: corrección de comportamiento.
- `refactor/TASK-ID-descripcion`: refactorización específica que no constituye una fase.
- `docs/TASK-ID-tema`: actualización exclusivamente documental.

`TASK-ID` representa el identificador completo de la tarea, por ejemplo `TASK-001` o `TASK-PUB-005`, cuando este último haya sido asignado formalmente. La rama de fase no reemplaza la rama de tarea: actúa como destino de integración de los trabajos de esa fase. Si un trabajo no pertenece a una fase, su destino de integración debe definirse en la tarea antes de implementar.

No deben reutilizarse ramas para tareas distintas ni incluirse varios identificadores de tarea en un mismo nombre. Las ramas existentes no se renombran retroactivamente por la incorporación de esta convención.

### Commits

Formato: `TASK-XXX: descripción del cambio lógico`.

Cada commit debe corresponder a un solo cambio lógico y a una tarea. Ejemplo: `TASK-001: valida campos obligatorios del formulario`.

## 9. Estados de descubrimientos

- `PENDIENTE`: la pregunta está registrada y aún no se investiga.
- `EN INVESTIGACIÓN`: Codex está inspeccionando y reuniendo evidencias.
- `EN REVISIÓN`: el informe está terminado y espera revisión en Chat 01.
- `VALIDADO`: los hallazgos fueron revisados y aceptados.
- `DESCARTADO`: la investigación no se usará, dejando registrado el motivo.

## 10. Estados de reglas de negocio

- `NO CONFIRMADA`: la regla es una hipótesis o no tiene evidencia suficiente; no puede guiar una implementación.
- `EN VALIDACIÓN`: la regla tiene evidencia y está siendo revisada.
- `CONFIRMADA`: la regla fue validada y puede usarse para planificar e implementar.
- `DESCARTADA`: la regla fue rechazada, dejando registrado el motivo.

## 11. Estados de tareas

- `PENDIENTE`: la tarea está registrada, pero no preparada.
- `PREPARADA`: tiene objetivo, alcance, reglas confirmadas y criterios de cierre.
- `PLANIFICADA`: Codex presentó el plan y espera aprobación.
- `APROBADA`: el plan fue aprobado y puede implementarse.
- `EN IMPLEMENTACIÓN`: se está ejecutando el cambio autorizado.
- `EN REVISIÓN`: la modificación y su diff están siendo revisados y probados.
- `BLOQUEADA`: existe un impedimento registrado que no permite continuar.
- `CERRADA`: cumple todos los criterios de cierre.

## 12. Puertas de control antes de implementar

Antes de modificar código deben cumplirse todas estas condiciones:

- El problema está confirmado.
- La tarea tiene un identificador `TASK-XXX` y un solo objetivo.
- El alcance incluye los archivos permitidos y excluye los archivos fuera de alcance.
- Las reglas de negocio necesarias están `CONFIRMADAS`.
- Los criterios de cierre están definidos.
- Se identificó cómo preservar la compatibilidad funcional.
- Codex presentó un plan pequeño, reversible y verificable.
- El plan fue aprobado.
- Cualquier migración destructiva cuenta con autorización expresa.
- Se verificó que el cambio no requiera secretos ni credenciales en el repositorio.

Si alguna puerta no se cumple, la implementación no comienza.

## 13. Criterios para cerrar una tarea

Una tarea puede pasar a `CERRADA` cuando:

- Cumple su objetivo único y los criterios de cierre acordados.
- Solo modifica archivos incluidos en el alcance aprobado.
- Mantiene la compatibilidad funcional requerida.
- El diff completo fue revisado.
- Las pruebas previstas fueron ejecutadas y sus resultados registrados.
- No incorpora secretos ni credenciales.
- El cambio es pequeño, reversible y verificable.
- El commit contiene un solo cambio lógico y sigue la nomenclatura definida.
- La documentación oficial afectada fue actualizada sin duplicar contenido.

## 14. Documentos oficiales del repositorio

El catálogo documental oficial y la responsabilidad de cada fuente son:

- `WORKFLOW.md`: define el gobierno metodológico, las reglas del proceso, los estados y los controles para descubrir, analizar, implementar, validar, cerrar y regularizar cambios.
- `PROJECT_CONTEXT.md`: actúa como índice y resumen del estado del proyecto. Orienta hacia las fuentes especializadas y no reemplaza su evidencia ni su autoridad.
- `ROADMAP.md`: define la planificación estratégica, las prioridades, las dependencias y la organización en EPIC.
- Los documentos `FEATURE`: definen la estrategia, el objetivo, el alcance, las exclusiones y los criterios de aceptación de cada incremento funcional o técnico.
- Las decisiones `ADR-XXX`: registran las decisiones arquitectónicas aprobadas, su contexto, consecuencias y límites.
- Los análisis técnicos `AT`: registran el análisis técnico previo, las dependencias, los riesgos, la estrategia de cambio, la reversión y las validaciones previstas.
- Las tareas `TASK-XXX`: registran la implementación aprobada, incluido su objetivo, alcance, reglas confirmadas, plan, pruebas y cierre.
- Los informes `DISCOVERY-XXX` y su registro: conservan preguntas, evidencias, hallazgos y estado de validación de las investigaciones.
- Los registros históricos aprobados: conservan asociaciones retrospectivas entre fuentes documentales y cambios ya existentes, sin sustituir las fuentes especializadas ni crear evidencia ficticia.
- El Manual Maestro: resume el conocimiento validado y orienta la modernización.
- Git: constituye evidencia técnica de ramas, commits y contenido versionado, pero no acredita por sí solo aprobación, validación funcional ni cierre documental.

Las conversaciones de Chat 01, Chat 02, Chat 03 y los chats de Codex coordinan el trabajo, pero no reemplazan estos documentos.

### Jerarquía y precedencia documental

`WORKFLOW.md` gobierna la metodología y define la responsabilidad de cada tipo documental. Dentro de su ámbito, cada fuente especializada tiene autoridad sobre los documentos de resumen:

```text
ROADMAP / FEATURE / ADR / AT / TASK / DISCOVERY / registro histórico
    > PROJECT_CONTEXT / Manual Maestro
```

`PROJECT_CONTEXT.md` y el Manual Maestro deben resumir y referenciar las fuentes especializadas sin contradecirlas. Cuando exista una diferencia, debe registrarse y corregirse el documento de resumen; la discrepancia no autoriza a modificar por inferencia la fuente especializada.

## 15. Regularización histórica

La regularización histórica se aplica exclusivamente a cambios existentes que carecen de trazabilidad documental completa por haber sido implementados antes de la formalización del flujo vigente. No reemplaza el ciclo ordinario de implementación ni convierte retroactivamente una reconstrucción en una aprobación previa.

Cada asociación retrospectiva debe distinguir de forma explícita:

- el hecho observado en Git o en el comportamiento verificable;
- la evidencia contemporánea que pueda demostrarse que existía al momento del cambio;
- la evidencia reconstruida o producida durante la regularización;
- la información no encontrada;
- la validación posterior, cuando se ejecute y registre;
- la decisión de integración, cuando sea aprobada por la autoridad correspondiente.

No se permite presentar un análisis, una asociación, una validación, una Task o cualquier otra reconstrucción posterior como si hubiese existido antes del cambio. Los nombres de ramas y los mensajes de commit pueden aportar evidencia técnica, pero no sustituyen una Task, un AT, una aprobación ni un cierre documental.

Un registro histórico solo adquiere autoridad cuando su uso y ubicación han sido aprobados conforme a este flujo. Debe referenciar las fuentes especializadas aplicables, identificar sus incertidumbres y mantener separados los estados `IMPLEMENTADO`, `VALIDADO`, `CERRADO DOCUMENTALMENTE` y `PENDIENTE DE REGULARIZACIÓN`. La regularización no autoriza por sí misma la integración del cambio.

## 16. Regla para evitar duplicación documental

Cada dato debe tener un único documento oficial como fuente. Los demás documentos deben referenciarlo o resumirlo sin copiar su contenido completo. Los informes conservan la evidencia detallada; el Manual Maestro recibe solo el resumen validado; las tareas enlazan las reglas y decisiones aplicables; y los ADR contienen la decisión correspondiente. Cuando un dato cambia, se actualiza su fuente oficial y luego las referencias o resúmenes afectados.

## 17. Flujo resumido en un diagrama textual

```text
DESCUBRIMIENTO
Pregunta
  → Codex DISCOVERY-XXX
  → informe sin cambios
  → Chat 01: revisión y validación
  → actualización documental
  → resumen al Manual Maestro
  → siguiente investigación

IMPLEMENTACIÓN
Problema confirmado
  → Chat 02: tarea
  → Chat 03: preparación
  → Codex TASK-XXX
  → plan → aprobación
  → modificación
  → revisión del diff
  → pruebas
  → commit
  → actualización documental
```
