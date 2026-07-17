# ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001

## 1. Identificación

- **Nombre del documento:** ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Relación con acta inicial:** complementa el [Acta de validación institucional EPIC-003-001](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md); no la reemplaza.
- **Propósito:** Documento de consolidación de decisiones institucionales. Registra las resoluciones institucionales obtenidas para EPIC-003, sin definir implementación.

## 2. Estado de validación

```text
Validación institucional:
Parcialmente resuelta mediante resoluciones registradas.

Implementación:
No autorizada todavía.
```

## 3. Decisiones institucionales aprobadas

### Identidad Persona

Se aprueba que:

```text
Persona representa toda identidad humana relacionada
con actividad académica o institucional.
```

Incluye participantes internos y participantes externos.

### Personas externas

Se aprueba que:

```text
Una persona externa puede existir sin Usuario institucional
ni Login.
```

### Usuario institucional

Se aprueba que:

```text
Usuario institucional se crea cuando una Persona requiere
interacción operativa con la institución.
```

Se mantiene la separación:

```text
Persona ≠ Usuario institucional
```

### Vigencia de acceso

Se aprueba que:

```text
Al finalizar vínculo institucional se desactiva acceso,
pero se conserva identidad e historial.
```

### Login

Se aprueba que:

```text
Todo Login humano pertenece a una Persona.
```

Asimismo, se registra como decisión institucional que no se autorizan cuentas técnicas.

### Estados académicos

Se aprueba que el catálogo actual representa los estados académicos oficiales:

```text
postulante
aceptado
matriculado
graduado
retirado
eliminado
reprobado
```

Se aprueba que:

```text
Los estados requieren transición controlada.
```

Esta acta no define las transiciones.

### Roles

Se aprueba que Administrador y comité académico son roles institucionales, separados de permisos.

Se aprueba que Autor, guía, investigador y evaluador son roles académicos contextuales.

### Permisos

Se aprueba que:

```text
Los permisos representan capacidades funcionales.
```

Los permisos se mantienen separados de estados, perfiles y roles académicos.

### Historial

Se aprueba que:

```text
Debe conservarse historial de cambios académicos.
```

## 4. Decisiones pendientes

### Estados académicos

Queda pendiente definir:

- Transiciones válidas.
- Responsables.
- Excepciones.
- Efectos.

### Roles

Queda pendiente definir:

- Asignación.
- Revocación.
- Vigencia.
- Aprobación.

### Historial

Queda pendiente definir:

- Eventos auditables.
- Retención.
- Acceso.
- Responsables.

### Identidad

Queda pendiente definir:

- Criterios de identificación.
- Tratamiento de homónimos.
- Consolidación de registros.

## 5. Decisiones rechazadas

```text
No se autorizan cuentas técnicas.
```

## 6. Impacto sobre ADR-002

```text
ADR-002 mantiene vigencia.

Las decisiones institucionales confirman sus principios.

No requiere modificación.
```

No se modifica [ADR-002](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md).

## 7. Impacto sobre Roadmap

```text
ROADMAP.md no requiere modificación.

EPIC-003 mantiene prioridad y dependencias.
```

No se modifica [ROADMAP.md](../roadmap/ROADMAP.md).

## 8. Impacto sobre implementación

```text
Las resoluciones institucionales permiten avanzar hacia
una futura definición implementable.

No autorizan implementación directa.

Requieren:
- Feature definida;
- análisis técnico;
- TASK aprobada.
```

## 9. Estado consolidado EPIC-003

```text
Análisis arquitectónico:
Cerrado

Validación arquitectónica:
Cerrada

Validación institucional:
Parcialmente resuelta

Implementación:
Pendiente
```

## 10. Historial documental

| Fecha | Origen de las decisiones | Relación documental |
| --- | --- | --- |
| 2026-07-17 | Respuestas institucionales consolidadas: P1.A, P2.A, P3.A, P4.A, P5.A, P6.B, P7.A, P8.A, P9.A, P10.A, P11.A y P12.A. | Complementa el Acta de validación institucional EPIC-003-001; no la reemplaza. |

## Fuentes consultadas

- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [WORKFLOW.md](../WORKFLOW.md)
- [PROJECT_CONTEXT.md](../PROJECT_CONTEXT.md)
- [TASKS.md](../TASKS.md)
