# ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-001

---

## 1. Identificación

- **Nombre del documento:** ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-001.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Análisis técnico de origen:** [AT-EPIC003-PERMISOS-FUNCIONALES-001.md](../architecture/AT-EPIC003-PERMISOS-FUNCIONALES-001.md).
- **Propósito:** Registrar las decisiones institucionales pendientes sobre las reglas que determinan los permisos funcionales efectivos del sistema.

Esta acta complementa la documentación vigente de EPIC-003. No resuelve reglas, no asume comportamiento futuro, no diseña implementación y no reemplaza ni modifica ADR-002, ROADMAP.md, el Manual Maestro ni las actas existentes.

## 2. Estado actual del proyecto

```text
Arquitectura:
✅ Consolidada

Separación estado/permisos:
✅ Validada

Inventario autorización:
✅ Cerrado

Preservación permisos:
✅ Implementada

Reglas permisos funcionales:
🟡 Pendientes de validación institucional
```

## 3. Principio arquitectónico

```text
Estado académico

≠

Permiso funcional
```

Este principio no determina por sí solo cómo se asignan, conservan, modifican o revocan permisos funcionales.

## 4. Situación funcional observada

Caso observado:

```text
postulante

↓

aceptado
```

Resultado observado:

- El estado cambia correctamente.
- Los permisos existentes se conservan.
- No aparecen diferencias suficientes de acceso académico.

Se registra como hallazgo funcional. Esta acta no determina si el comportamiento observado es correcto o incorrecto ni atribuye una causa definitiva.

```text
Acceso académico observado

≠

Modelo definitivo de permisos funcionales
```

La ausencia observada de acceso esperado a cursos o calendario para el caso aceptado no demuestra, por sí sola, cuál debe ser la regla institucional futura.

## 5. Decisión institucional principal

Se requiere validar:

```text
¿Qué concepto define los permisos funcionales efectivos?
```

### Opción A

El estado académico define parcialmente permisos.

Debe definir:

- Estados involucrados.
- Permisos asociados.
- Condiciones.
- Responsables.

### Opción B

Los permisos son capacidades independientes del estado.

Debe definir:

- Cómo se asignan.
- Quién los administra.
- Cuándo cambian.

### Opción C

Existe un modelo combinado:

```text
Estado académico

*

Rol académico/institucional

*

Permiso funcional
```

Debe definir la responsabilidad de cada concepto.

## 6. Diferencias entre estados académicos

Se requiere validar las diferencias funcionales que correspondan a cada estado.

### Postulante

¿Qué acceso corresponde?

### Aceptado

¿Qué diferencia funcional debe existir?

### Matriculado

¿Qué capacidades adicionales posee?

### Otros estados

Se requiere validar el tratamiento funcional de:

- Graduado.
- Retirado.
- Eliminado.
- Reprobado.

## 7. Módulos académicos

Se requiere validar qué concepto habilita acceso a:

- Cursos.
- Calendario académico.
- Ficha académica.
- Tesis.
- Publicaciones.
- Otros módulos.

Alternativas:

- Estado académico.
- Permiso funcional.
- Rol académico.
- Combinación de criterios.

## 8. Permisos actuales

El comportamiento técnico actual es:

```text
login

↓

permiso_login

↓

múltiples permisos
```

Este comportamiento no define automáticamente el modelo futuro de permisos funcionales.

## 9. Responsables requeridos

| Decisión | Responsable requerido | Estado |
| --- | --- | --- |
| Reglas acceso académico | Por definir | Pendiente |
| Diferencias entre estados | Por definir | Pendiente |
| Gestión permisos funcionales | Por definir | Pendiente |

No se asignan autoridades definitivas sin fuente institucional que las determine.

## 10. Impacto arquitectura y planificación

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
✅ Consolidada

Modelo conceptual:
✅ Validado

Preservación permisos:
✅ Implementada

Reglas permisos:
🟡 Pendientes

Nuevas TASK:
No crear hasta resolución institucional
```

## Fuentes consultadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-OPERACION-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-OPERACION-001.md)
- [ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md](ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md)
- [ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.md](ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.md)
- [AT-EPIC003-PERMISOS-FUNCIONALES-001.md](../architecture/AT-EPIC003-PERMISOS-FUNCIONALES-001.md)
