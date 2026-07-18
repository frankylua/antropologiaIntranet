# ACTA-VALIDACION-INSTITUCIONAL-EPIC003-OPERACION-001

## 1. Identificación

- **Nombre del documento:** ACTA-VALIDACION-INSTITUCIONAL-EPIC003-OPERACION-001.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Relación documental:** complementa el [Acta de validación institucional EPIC-003-001](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md) y el [Acta de resoluciones institucionales EPIC-003](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md); no las reemplaza ni modifica ADR-002.
- **Propósito:** Documento destinado a validar reglas operativas institucionales necesarias antes de una futura implementación.

## 2. Estado actual

```text
Modelo conceptual:
Validado

Decisiones institucionales iniciales:
Registradas

Reglas operativas:
Pendientes de validación

Implementación:
No autorizada
```

## 3. Validación estados académicos

### Catálogo

Se requiere validar institucionalmente si el catálogo actual es oficial:

```text
postulante
aceptado
matriculado
graduado
retirado
eliminado
reprobado
```

Alternativas de validación:

- A) Sí, todos son estados oficiales.
- B) Algunos deben modificarse.
- C) Debe definirse un catálogo nuevo.

### Transiciones

Se requiere validar:

- Estados de origen.
- Estados de destino.
- Responsables.
- Condiciones.

Esta acta no propone una secuencia de transiciones.

### Efectos

Se requiere validar si un cambio de estado:

- A) Solo modifica la situación académica.
- B) Puede modificar accesos mediante reglas explícitas.
- C) Otro.

Se mantiene el principio arquitectónico vigente:

```text
Estado académico ≠ permiso funcional.
```

## 4. Validación roles institucionales

### Administrador

Se requiere definir institucionalmente:

- Responsable de asignación.
- Vigencia.
- Revocación.

### Comité académico

Se requiere definir institucionalmente:

- Integrantes.
- Duración.
- Renovación.
- Autoridad de designación.

Esta acta no define respuestas para estas materias.

## 5. Validación roles académicos

Los roles sujetos a validación son:

- Autor.
- Guía.
- Coguía.
- Investigador.
- Evaluador.

Autor, Guía, Coguía, Investigador y Evaluador corresponden a roles académicos contextuales asociados a una participación respecto de un objeto académico.

No representan permisos funcionales ni roles institucionales.

```text
Rol académico ≠ Permiso funcional
Rol académico ≠ Rol institucional
```

Se requiere validar para cada rol:

- Vigencia.
- Cambios durante la actividad académica.
- Responsables.

No se define un modelo físico.

## 6. Validación Usuario institucional y Login

La relación conceptual validada es:

```text
Persona

↓

Usuario institucional

↓

Login
```

Esta relación representa la separación conceptual validada.

Una Persona puede existir sin Usuario institucional ni Login, especialmente en casos de participantes externos.

### Usuario institucional

Se requiere validar:

- Quién crea el usuario institucional.
- Quién lo suspende.
- Cuándo pierde vigencia.

### Login

Se requiere validar:

- Administración.
- Recuperación.
- Revocación.

Se mantiene la decisión institucional registrada:

```text
Login humano asociado a Persona.
```

## 7. Validación historial

```text
Historial académico

≠

Auditoría administrativa
```

### Historial académico

Se requiere validar:

- Qué eventos académicos deben conservarse.
- Qué cambios requieren trazabilidad.
- Cuál es el período de retención.
- Quién puede consultar.

### Auditoría administrativa

Se requiere validar:

- Qué acciones administrativas son registrables.
- Responsables.
- Acceso.
- Retención.

## 8. Matriz de decisiones pendientes

| Área | Pregunta | Responsable requerido | Estado |
| --- | --- | --- | --- |
| Estados académicos | ¿El catálogo actual es oficial? | Responsable institucional por definir | Pendiente de validación |
| Estados académicos | ¿Cuáles son las transiciones, condiciones y responsables? | Responsable institucional por definir | Pendiente de validación |
| Estados académicos | ¿Qué efectos operativos produce un cambio de estado? | Responsable institucional por definir | Pendiente de validación |
| Roles institucionales | ¿Cómo se asignan, mantienen y revocan Administrador y Comité académico? | Responsable institucional por definir | Pendiente de validación |
| Roles académicos | ¿Cuál es la vigencia y responsable de cada rol académico contextual? | Responsable institucional por definir | Pendiente de validación |
| Usuario institucional | ¿Quién crea, suspende y determina la pérdida de vigencia? | Responsable institucional por definir | Pendiente de validación |
| Login | ¿Cómo se administran la recuperación y revocación? | Responsable institucional por definir | Pendiente de validación |
| Historial y auditoría | ¿Qué eventos, acciones, responsables, accesos y períodos de retención deben conservarse? | Responsable institucional por definir | Pendiente de validación |

## 9. Impacto esperado

```text
ADR-002:
Mantiene vigencia.

Roadmap:
No requiere modificación.

EPIC-003 mantiene la prioridad y dependencias definidas en ROADMAP.md.

La validación operativa no modifica la planificación oficial.

Implementación:
Pendiente hasta cierre de reglas operativas.
```

## 10. Estado consolidado EPIC-003

```text
Arquitectura:
Cerrada

Validación conceptual:
Cerrada

Validación operativa:
Pendiente

Feature implementable:
No creada
```

## Fuentes consultadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md)
