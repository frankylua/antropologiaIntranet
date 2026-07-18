# ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001

---

## 1. Identificación

- **Nombre del documento:** ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Propósito:** Registrar las resoluciones institucionales aprobadas sobre las reglas de permisos funcionales.
- **Antecedentes:** ADR-002; AT-FEATURE003-001-INVENTARIO-AUTORIZACION-001; TASK-EPIC003-PRESERVACION-PERMISOS-001; AT-EPIC003-PERMISOS-FUNCIONALES-001; ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-001; ACTA-SOLICITUD-RESOLUCION-EPIC003-REGLAS-PERMISOS-001.

Esta acta consolida la resolución institucional del EPIC-003. Define reglas funcionales para estudiantes y profesores, sin autorizar cambios de código, base de datos ni comportamiento técnico automático.

## 2. Fuente de permisos

Se aprueba la siguiente resolución:

```text
El estado académico/institucional define las reglas de acceso
que determinan los permisos funcionales efectivos.
```

Se mantiene la separación conceptual:

```text
Estado

≠

Permiso funcional
```

El estado determina reglas de acceso; no reemplaza el concepto de permiso funcional.

## 3. Matriz aprobada para estudiantes

| Estado | Perfil/Mi perfil (incluye datos académicos) | Reglamento | Calendario | Cursos |
| --- | --- | --- | --- | --- |
| Postulante | Sí | Sí | No | No |
| Aceptado | Sí | Sí | Sí | Solo vista |
| Matriculado | Sí | Sí | Sí | Solo vista |
| Graduado | Sí | Sí | No | No |
| Reprobado | Sí | Sí | No | No |
| Retirado | Sin acceso intranet | Sin acceso | Sin acceso | Sin acceso |
| Eliminado | Sin acceso intranet | Sin acceso | Sin acceso | Sin acceso |

## 4. Definición de Perfil/Mi perfil

```text
Perfil/Mi perfil incluye:

* datos personales;
* datos académicos propios del usuario.
```

Perfil/Mi perfil no representa acceso automático a todos los módulos académicos.

## 5. Matriz aprobada para profesores

Los estados institucionales aprobados para profesor son: Registrado, Aceptado e Inhabilitado.

| Estado | Permisos funcionales |
| --- | --- |
| Registrado | Perfil; Reglamento |
| Aceptado | Perfil; Reglamento; Cursos; Calendario |
| Inhabilitado | Sin acceso a intranet |

## 6. Concepto de autorización aprobado

```text
Estado académico/institucional

↓

Reglas institucionales de acceso

↓

Permisos funcionales efectivos
```

La implementación técnica de estas reglas debe evaluarse posteriormente mediante una TASK específica.

## 7. Profesor, Comité académico y administración

Un profesor puede pertenecer al Comité académico. Los permisos asociados al Comité académico se suman a los permisos propios del profesor.

La asignación y remoción de integrantes del Comité académico corresponde exclusivamente al Administrador.

Una persona puede acumular roles y participaciones. Los permisos resultantes se acumulan según las reglas institucionales, incluyendo las siguientes combinaciones autorizadas:

```text
Profesor + Comité académico

Profesor + Administrador

Administrador + otras participaciones autorizadas
```

## 8. Actores pendientes de validación futura

No existen definiciones aprobadas en esta resolución para los siguientes actores o condiciones. Permanecen pendientes de validación futura:

- Participantes externos.
- Personas sin Login.

No se asignan ni infieren reglas para estos actores.

## 9. Impacto arquitectónico

ADR-002 mantiene vigencia.

La presente resolución confirma la separación conceptual:

```text
Estado académico ≠ permiso funcional
```

## 10. Impacto en Roadmap

```text
EPIC-003 mantiene prioridad y dependencias.

No requiere modificación inmediata del Roadmap.
```

## 11. Impacto en implementación

```text
La resolución habilita futuros análisis técnicos.

No autoriza modificaciones automáticas de permisos.

No modifica TASK-EPIC003-PRESERVACION-PERMISOS-001.
```

## 12. Estado posterior de EPIC-003

```text
Arquitectura:
✅ Consolidada

Inventario autorización:
✅ Cerrado

Preservación permisos:
✅ Implementada

Reglas permisos funcionales:
✅ Resueltas para estudiantes, profesores y Comité académico

Actores adicionales:
🟡 Pendientes

Implementación derivada:
Pendiente análisis técnico
```

## 13. Fuentes utilizadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- Actas EPIC-003 autorizadas.
- [AT-EPIC003-PERMISOS-FUNCIONALES-001.md](../architecture/AT-EPIC003-PERMISOS-FUNCIONALES-001.md)
- Respuestas institucionales consolidadas.

## 14. Alcance y cierre

Esta resolución es documental. No modifica código fuente, SQL, ADR, Roadmap, Manual Maestro ni actas existentes; no crea TASK, no implementa cambios y no constituye autorización para modificar permisos automáticamente.

Siguiente paso:

```text
Revisión documental ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md antes de commit.
```
