# ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001

---

## 1. Identificación

- **Nombre del documento:** ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Feature asociada:** FEATURE-EPIC003-001.
- **TASK relacionada:** TASK-EPIC003-PRESERVACION-PERMISOS-001.
- **Propósito:** Registrar las decisiones institucionales pendientes sobre los conceptos que determinan los permisos funcionales efectivos dentro del sistema.

Esta acta complementa la documentación vigente de EPIC-003. No reemplaza ni modifica ADR-002, ROADMAP.md, el Manual Maestro ni las actas existentes; tampoco define implementación.

La TASK-EPIC003-PRESERVACION-PERMISOS-001 es antecedente del hallazgo funcional: corrigió el comportamiento destructivo de reconstrucción automática de permisos ante cambios de estado académico. A partir de esa preservación, esta acta valida la regla institucional pendiente que deberá determinar la asignación futura de permisos funcionales, sin modificar la TASK.

Esta acta no valida la implementación de permisos ni autoriza cambios técnicos; valida exclusivamente la regla institucional que determinará su asignación.

## 2. Estado actual

```text
Arquitectura:
Consolidada

Separación estado/permisos:
Validada

Inventario técnico:
Cerrado

Preservación permisos:
Implementada

Reglas permisos funcionales:
Pendientes
```

## 3. Principio arquitectónico involucrado

```text
Estado académico

≠

Permiso funcional
```

Este principio valida la separación conceptual entre ambos conceptos. No determina automáticamente cómo se asignan, conservan, modifican o revocan los permisos funcionales.

## 4. Situación funcional observada

Caso observado:

```text
postulante

→

aceptado
```

Resultado observado:

```text
No se observan cambios suficientes de acceso.
```

En particular, el usuario conserva permisos equivalentes y no visualiza cursos ni calendario académico. Se registra como hallazgo funcional. Esta acta no concluye una causa definitiva ni atribuye el resultado a una regla técnica o institucional específica.

## 5. Pregunta institucional principal

Se requiere validar:

```text
¿Qué concepto define los permisos funcionales efectivos?
```

### Opción A

El estado académico define parcialmente permisos.

Ejemplo:

```text
Aceptado

→

acceso académico ampliado
```

Debe definir:

- Estados afectados.
- Permisos.
- Responsables.

### Opción B

El estado académico no define permisos. Los permisos son capacidades independientes.

Debe definir:

- Quién asigna.
- Cuándo.
- Bajo qué reglas.

### Opción C

Modelo combinado:

```text
Estado académico

Rol académico/institucional

Permiso funcional
```

Debe definir la responsabilidad de cada concepto.

## 6. Validación de diferencias postulante / aceptado

Se requiere resolver si debe existir diferencia funcional entre:

```text
postulante

y

aceptado
```

Alternativas:

- A) Sí, tienen accesos diferentes.
- B) No, tienen el mismo acceso.
- C) Depende de otros criterios.

## 7. Cursos y calendario académico

Se requiere validar qué condición habilita acceso a:

- Cursos.
- Calendario académico.
- Módulos académicos.

Alternativas:

- A) Estado académico.
- B) Permiso funcional.
- C) Rol académico.
- D) Combinación de criterios.

## 8. Permisos actuales

El sistema actual posee:

```text
login

→

permiso_login

→

múltiples permisos
```

Esto representa el comportamiento técnico actual. No define automáticamente el modelo futuro de permisos funcionales.

## 9. Responsables

| Decisión | Responsable requerido | Estado |
| --- | --- | --- |
| Acceso académico | Por definir | Pendiente |
| Diferencia postulante/aceptado | Por definir | Pendiente |
| Permisos funcionales | Por definir | Pendiente |

## 10. Impacto

```text
ADR-002:
Mantiene vigencia.

Roadmap:
No requiere modificación.

Implementación:
Pendiente hasta definición institucional.
```

## 11. Estado EPIC-003

```text
Arquitectura:
Cerrada

Separación conceptual:
Validada

Preservación permisos:
Implementada

Permisos funcionales:
Pendientes

TASK adicionales:
No crear hasta resolver reglas
```

## Fuentes consultadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-OPERACION-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-OPERACION-001.md)
- [ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md](ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md)
- AT-FEATURE003-001-INVENTARIO-AUTORIZACION-001.
- TASK-EPIC003-PRESERVACION-PERMISOS-001.
