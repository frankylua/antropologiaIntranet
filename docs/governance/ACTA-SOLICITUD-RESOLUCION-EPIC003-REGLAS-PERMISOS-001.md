# ACTA-SOLICITUD-RESOLUCION-EPIC003-REGLAS-PERMISOS-001

---

## 1. Identificación

- **Nombre del documento:** ACTA-SOLICITUD-RESOLUCION-EPIC003-REGLAS-PERMISOS-001.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Antecedentes:** ADR-002; AT-FEATURE003-001-INVENTARIO-AUTORIZACION-001; TASK-EPIC003-PRESERVACION-PERMISOS-001; AT-EPIC003-PERMISOS-FUNCIONALES-001; ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-001.
- **Propósito:** Solicitar formalmente las definiciones institucionales necesarias para cerrar las reglas de permisos funcionales del EPIC-003.

Esta solicitud da continuidad documental al EPIC-003. No resuelve reglas, no asigna responsables y no modifica la arquitectura vigente.

## 2. Estado actual

La arquitectura y la corrección técnica inicial están cerradas.

La definición de reglas funcionales permanece pendiente.

```text
Arquitectura:
Consolidada

Separación estado/permisos:
Validada

Preservación permisos:
Implementada

Modelo autorización actual:
Documentado

Reglas permisos funcionales:
Pendientes de resolución institucional
```

## 3. Solicitud institucional principal

Se solicita resolver institucionalmente la siguiente pregunta:

```text
¿Qué concepto define los permisos funcionales efectivos?
```

La evidencia técnica actual registra flujos separados:

```text
permiso_login
↓
sesiones
↓
autorización efectiva
```

```text
tipo_estudiante
↓
estado académico
```

La implementación vigente evitó que un cambio de estado académico elimine permisos existentes. Esta preservación no define qué concepto debe determinar los permisos funcionales efectivos.

Para la resolución institucional, se solicita responder las siguientes preguntas, sin considerar ninguna de ellas una decisión contenida en esta acta:

- ¿El estado académico define los permisos funcionales efectivos?
- ¿Un permiso funcional independiente define los permisos funcionales efectivos?
- ¿Una combinación de criterios define los permisos funcionales efectivos?

## 4. Solicitud sobre estados académicos

Se solicita definir, para cada estado académico, el acceso permitido, las restricciones aplicables y las diferencias funcionales que correspondan:

| Estado |
| --- |
| Postulante |
| Aceptado |
| Matriculado |
| Graduado |
| Retirado |
| Eliminado |
| Reprobado |

La solicitud no presume que los estados deban otorgar, conservar o revocar permisos. Tales efectos requieren definición institucional explícita.

## 5. Solicitud sobre módulos

Se solicita establecer el criterio institucional aplicable a los siguientes módulos:

- Cursos.
- Calendario académico.
- Ficha académica.
- Tesis.
- Publicaciones.

Para cada módulo, se solicita responder:

```text
¿Qué condición habilita el acceso?
```

## 6. Solicitud sobre actores

Se solicita definir el criterio de acceso aplicable a los siguientes actores o condiciones de participación:

- Profesor.
- Comité académico.
- Administrador.
- Participantes externos.
- Personas sin Login.

La presente solicitud no atribuye permisos ni responsabilidades a estos actores.

## 7. Restricciones

Esta solicitud no requiere ni autoriza:

- modificar código;
- modificar base de datos;
- cambiar ADR-002;
- cambiar ROADMAP.md;
- crear TASK.

## 8. Impacto esperado

Una vez obtenidas las definiciones institucionales, la continuidad esperada será:

```text
Solicitud de resolución
↓
Acta de resolución institucional
↓
Evaluación técnica
↓
TASK si corresponde
```

La evaluación técnica posterior deberá determinar el impacto y no se encuentra autorizada por esta acta.

## 9. Estado EPIC-003

```text
Arquitectura:
Consolidada

Implementación preservación permisos:
Cerrada

Reglas permisos funcionales:
Pendientes

Resolución institucional:
Pendiente
```

## Fuentes utilizadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-RESOLUCIONES-001.md)
- [ACTA-VALIDACION-INSTITUCIONAL-EPIC003-OPERACION-001.md](ACTA-VALIDACION-INSTITUCIONAL-EPIC003-OPERACION-001.md)
- [ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md](ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md)
- [ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.md](ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.md)
- [AT-EPIC003-PERMISOS-FUNCIONALES-001.md](../architecture/AT-EPIC003-PERMISOS-FUNCIONALES-001.md)
