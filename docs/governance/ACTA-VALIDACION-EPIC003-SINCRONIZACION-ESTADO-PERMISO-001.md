# ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001

## 1. Identificación

- **Clasificación:** [GOV] Decisión institucional; [ARQ] Modelo estado académico → permisos; [DOC] Actualización registro oficial EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **ADR relacionado:** ADR-002 — Evolución del modelo de identidad y participación académica.
- **Análisis técnico base:** AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.
- **Propósito:** registrar la resolución institucional consolidada sobre la relación entre estado académico y permisos efectivos.

Esta acta formaliza una decisión institucional. No autoriza cambios técnicos ni implementación de sincronización sobre el modelo heredado.

## 2. Decisión institucional aprobada

### 2.1 Alternativa seleccionada

```text
Alternativa A:

El estado académico determina automáticamente permisos.
```

La relación conceptual aprobada es:

```text
Estado académico
       ↓
Grupo de permisos funcionales
       ↓
Autorización efectiva
```

Esta decisión define la política institucional de acceso para estudiantes. La forma en que dicha política pueda materializarse técnicamente se mantiene fuera del alcance de esta acta.

## 3. Matriz institucional aprobada — Estudiantes

| Estado académico | Grupo de permisos |
|---|---|
| Postulante | Mi perfil, Reglamento |
| Aceptado | Mi perfil, Datos académicos, Cursos (vista), Calendario, Reglamento |
| Matriculado | Mi perfil, Datos académicos, Cursos (vista), Calendario, Reglamento |
| Graduado | Mi perfil, Reglamento |
| Reprobado | Mi perfil, Reglamento |
| Retirado | Mi perfil, Reglamento |
| Eliminado | Sin acceso a intranet |

## 4. Reglas institucionales explícitas

### 4.1 Agrupación de estados

```text
Aceptado

=

Matriculado
```

Los estados **Aceptado** y **Matriculado** comparten el mismo grupo de permisos funcionales.

### 4.2 Acceso para estado eliminado

```text
Estado eliminado
       ↓
Sin acceso a intranet
```

El estado **Eliminado** no posee grupo de permisos funcionales de intranet.

## 5. Alcance de la decisión

La decisión aprobada define exclusivamente la relación conceptual:

```text
Estado académico
       ↓
Permisos
```

No define todavía:

- mecanismo técnico de sincronización;
- estructura final de permisos;
- migración de `permiso_login`;
- cambios en login;
- automatización de transición.

En consecuencia, esta acta no ordena ni habilita implementar sincronización, modificar sesiones, cambiar autorizaciones ni alterar el modelo técnico actual.

## 6. Pendientes mantenidos

### 6.1 Profesores

Pendiente consolidación formal de los estados:

- registrado;
- aceptado;
- inhabilitado.

### 6.2 Comité

Se mantiene que:

- un profesor puede pertenecer al comité;
- el comité agrega capacidades adicionales;
- solo administrador gestiona integrantes del comité.

### 6.3 Permiso histórico 3

Se mantiene pendiente la situación histórica:

```text
permiso 3
├── estudiante aceptado
└── docente
```

La presente resolución no aprueba mantener esta asociación técnica ni define su futura modificación.

## 7. Estado actual de EPIC-003

```text
EPIC-003

Arquitectura:
✅ Consolidada

Resolución de permisos funcionales:
✅ Aprobada

Relación estado académico/permisos para estudiantes:
✅ Aprobada mediante Alternativa A

Implementación de sincronización:
No corresponde
```

## 8. Fuentes utilizadas

- [Manual Maestro](../MANUAL_MAESTRO.md)
- [Roadmap](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md)
- [AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](../architecture/AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md)
- [ACTA-SOLICITUD-RESOLUCION-EPIC003-ESTADO-PERMISO-001](ACTA-SOLICITUD-RESOLUCION-EPIC003-ESTADO-PERMISO-001.md)
- Resoluciones institucionales EPIC-003 recibidas.

## 9. Restricciones cumplidas

```text
Sin cambios de código.
Sin implementación.
Sin cambios ADR.
Sin cambios Roadmap.
Sin cambios Manual Maestro.
Sin commit.
```

Siguiente paso:

```text
Revisión documental del acta actualizada antes de commit.
```
