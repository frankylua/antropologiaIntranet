# ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001

## Consolidación de la matriz global de actores y permisos

## 1. Identificación

- **Clasificación:** [GOV] Consolidación reglas institucionales de autorización; [ARQ] Modelo estado, rol y participación; [DOC] Registro oficial EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Propósito:** consolidar la definición institucional de autorización para estudiantes, profesores, Comité académico y Administradores como fuente funcional de referencia.

Esta acta formaliza reglas institucionales. No autoriza implementación, cambios de código, creación de permisos técnicos, cambios de datos ni migración.

## 2. Modelo institucional aprobado

```text
Estado académico
Rol institucional
Participación
        ↓
Reglas de autorización
        ↓
Permisos funcionales
```

La autorización se interpreta como composición de capacidades, no como un único permiso excluyente. Estado académico, rol institucional y participación son dimensiones diferenciadas que aportan contexto a las reglas de autorización.

## 3. Matriz institucional — Estudiantes

| Estado académico | Permisos funcionales |
| --- | --- |
| Postulante | Mi perfil, Reglamento |
| Aceptado | Mi perfil, Datos académicos, Cursos (vista), Calendario, Reglamento |
| Matriculado | Mi perfil, Datos académicos, Cursos (vista), Calendario, Reglamento |
| Graduado | Mi perfil, Reglamento |
| Reprobado | Mi perfil, Reglamento |
| Retirado | Mi perfil, Reglamento |
| Eliminado | Sin acceso a intranet |

### Regla de estados equivalentes

```text
Aceptado
    =
Matriculado
```

Ambos estados comparten el mismo grupo de permisos funcionales.

## 4. Matriz institucional — Profesores

| Estado profesor | Permisos funcionales |
| --- | --- |
| Registrado | Mi perfil, Reglamento |
| Aceptado | Cursos, Mi perfil, Reglamento, Calendario, Tesis propias |
| Inhabilitado | Sin acceso |

### Regla de tesis

```text
Tesis corresponde al rol Profesor.

Profesor
    ↓
Visualiza únicamente sus tesis registradas.
```

La participación en Comité no modifica la propiedad académica de las tesis.

## 5. Comité académico

Regla aprobada:

```text
Profesor integrante Comité
        =
Permisos Profesor
        +
Permisos Comité
```

La pertenencia al Comité suma capacidades y no reemplaza el rol Profesor.

### Permisos del Comité

El Comité tiene acceso a todos los permisos funcionales del sistema, con las siguientes excepciones. El Comité no puede:

- agregar miembros del Comité;
- editar miembros del Comité;
- eliminar miembros del Comité;
- visualizar miembros del Comité;
- administrar Administradores.

## 6. Administración de Comité y Administradores

Solo el Administrador puede:

- agregar integrantes del Comité;
- modificar integrantes del Comité;
- eliminar integrantes del Comité;
- gestionar Administradores.

## 7. Administrador

```text
Administrador
    ↓
Acceso administrativo completo.
```

La definición general de acceso administrativo está aprobada. Permanece pendiente documentar el detalle fino de los permisos administrativos.

## 8. Permiso histórico 3

Se mantiene pendiente la resolución técnica de la asociación histórica:

```text
permiso 3
├── estudiante aceptado
└── docente
```

La matriz conceptual separa:

```text
Estado estudiante
      ≠
Rol docente
```

La estrategia de migración técnica del permiso histórico 3 será definida posteriormente. Esta acta no autoriza reinterpretarlo, modificarlo ni eliminarlo.

## 9. Decisiones pendientes

- Detalle técnico de permisos administrativos.
- Detalle interno de permisos del Comité.
- Resolución de la migración del permiso histórico 3.
- Mecanismo técnico de derivación de permisos.

## 10. Impacto arquitectónico

Esta matriz será utilizada como fuente funcional para [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md) y para futuros análisis técnicos.

No implica:

- cambios inmediatos en base de datos;
- cambios en login;
- migración automática;
- modificación de `permiso_login`.

## 11. Estado EPIC-003

```text
EPIC-003

Matriz estudiantes:
✅ Aprobada

Matriz profesores:
✅ Aprobada

Reglas Comité:
✅ Aprobadas

Reglas Administrador:
🟡 Definición general aprobada

Implementación:
Pendiente
```

## 12. Fuentes utilizadas

- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md).
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md).
- [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md).
- [AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](../architecture/AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md).
- [AT-EPIC003-IMPLEMENTACION-ESTADO-PERMISO-001](../architecture/AT-EPIC003-IMPLEMENTACION-ESTADO-PERMISO-001.md).
- [AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001](../architecture/AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001.md).
- [ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md).

## 13. Restricciones cumplidas

```text
Sin cambios de código.
Sin implementación.
Sin cambios ADR.
Sin cambios Roadmap.
Sin cambios Manual Maestro.
Sin TASK.
Sin commit.
```

## 14. Siguiente paso posterior

Revisión técnica: `ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001`.
