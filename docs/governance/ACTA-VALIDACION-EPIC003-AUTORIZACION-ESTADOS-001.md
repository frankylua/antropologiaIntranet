# ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001

## 1. Identificación

- **Nombre del documento:** ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Relación con análisis técnico:** se origina en AT-FEATURE003-001-INVENTARIO-AUTORIZACION-001, que confirmó acoplamiento técnico entre actualización de estado académico y reconstrucción de permisos.
- **Propósito:** Documento destinado a validar reglas institucionales sobre efectos permitidos de estados académicos sobre autorización.

Este documento complementa las actas vigentes de EPIC-003 y ADR-002. No las reemplaza, no define solución técnica ni autoriza implementación.

El modelo actual relaciona las cuentas de acceso mediante `login` y `permiso_login`, permitiendo que una cuenta pueda tener múltiples permisos registrados. Esta relación representa el comportamiento técnico actual y no define por sí misma la relación futura entre roles, estados académicos y permisos funcionales.

```text
Login ≠ Identidad académica
```

## 2. Estado actual

```text
Separación conceptual:
Validada

Acoplamiento técnico actual:
Confirmado

Reglas institucionales:
Pendientes

Implementación:
No autorizada
```

## 3. Principio arquitectónico involucrado

```text
Estado académico ≠ Permiso funcional
```

Este principio pertenece al ámbito arquitectónico y no define por sí mismo las reglas operativas institucionales, los efectos de cada estado ni los criterios para modificar autorizaciones.

## 4. Validación de estados académicos

### Catálogo de estados académicos

```text
postulante
aceptado
matriculado
graduado
retirado
eliminado
reprobado
```

El catálogo de estados académicos fue validado como referencia institucional en [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md).

El presente documento no reabre la definición del catálogo, sino que valida exclusivamente los efectos permitidos que dichos estados puedan tener sobre autorización y permisos funcionales.

```text
Catálogo de estados:
Validado

Efectos sobre autorización:
Pendientes de validación
```

## 5. Relación estado académico → autorización

### Pregunta principal

¿Un cambio de estado académico puede modificar permisos de acceso?

- A) No. Estado académico nunca modifica permisos.
- B) Sí, pero únicamente mediante reglas institucionales explícitas.
- C) Sí, automáticamente según catálogo aprobado.
- D) Otra definición institucional.

La respuesta debe distinguir entre estado académico, autorización y permiso funcional, sin diseñar su implementación.

## 6. Permisos derivados de estado

Se requiere validar: ¿existen permisos que dependen directamente del estado académico?

- A) No existen.
- B) Existen permisos académicos específicos definidos institucionalmente.
- C) Deben definirse formalmente.

Este documento no define permisos concretos.

## 7. Conservación de permisos existentes

Cuando cambia el estado académico, se requiere validar qué ocurre con permisos adicionales existentes:

- A) Deben conservarse salvo revocación explícita.
- B) Deben revisarse según reglas institucionales.
- C) Deben eliminarse siempre.
- D) Otro criterio.

## 8. Permiso 3 — discrepancia postulante/aceptado

### Situación técnica registrada

```text
BD:
postulante

Sesión:
aceptado
```

Se requiere validar cuál es el significado institucional correcto:

- A) Representa postulante.
- B) Representa aceptado.
- C) Representa una capacidad de acceso independiente del estado.
- D) Debe redefinirse.

La discrepancia se registra como materia institucional pendiente; este documento no la resuelve técnicamente.

## 9. Roles institucionales y permisos

Se mantiene la separación conceptual:

```text
Rol institucional

≠

Permiso funcional
```

Se requiere validar: ¿los roles institucionales deben generar permisos automáticamente?

- A) Sí.
- B) No.
- C) Depende del rol.

## 10. Responsables institucionales

| Decisión | Responsable requerido | Estado |
| --- | --- | --- |
| Estados académicos | Por definir | Pendiente |
| Permisos asociados | Por definir | Pendiente |
| Cambios de acceso | Por definir | Pendiente |

## 11. Impacto esperado

```text
ADR-002:
Mantiene vigencia.

Roadmap:
No requiere modificación.

Implementación:
Pendiente hasta aprobación de reglas.
```

Esta acta no modifica ADR-002, ROADMAP.md ni la planificación vigente. Las respuestas institucionales requeridas deberán validarse antes de una futura definición implementable.

## 12. Estado EPIC-003

```text
Arquitectura:
Cerrada

Inventario técnico:
Cerrado

Reglas autorización/estado:
Pendientes

TASK implementación:
No creada
```

## Fuentes consultadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-OPERACION-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-OPERACION-001.md)
- AT-FEATURE003-001-INVENTARIO-AUTORIZACION-001
