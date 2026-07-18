# FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001

## Evolución hacia un modelo de autorización derivada

## 1. Identificación

- **Clasificación:** [ARQ] Evolución arquitectura autorización; [GOV] Gobierno modelo permisos; [DOC] Actualización Feature EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Estado:** ✅ Diseño arquitectónico aprobado.
- **Implementación:** Pendiente.
- **Dependencias pendientes:** definición de Comité, Administrador y permiso histórico 3.
- **Propósito:** registrar el alcance institucional y la dirección arquitectónica aprobada para evolucionar hacia un modelo de autorización derivada, sin autorizar su implementación.

## 2. Decisión y principios

La decisión institucional incorpora como antecedente la Alternativa A: para estudiantes, el estado académico determina permisos. La evolución aprobada mantiene separadas las dimensiones de estado académico, rol institucional, participación y permiso funcional, conforme a ADR-002.

Principios aplicables:

1. Evolución incremental.
2. Compatibilidad con el sistema legado.
3. Separación entre estado, rol y participación.
4. Permisos funcionales explícitos.
5. Migración progresiva por módulos.

## 3. Arquitectura de autorización derivada

Modelo objetivo:

```text
Estado académico
Rol institucional
Participación
        ↓
Reglas de autorización
        ↓
Permisos funcionales
        ↓
Módulos
```

Las reglas de autorización resuelven permisos funcionales efectivos desde el contexto institucional del usuario. Estado académico, rol institucional y participación no son equivalentes entre sí ni equivalen por sí solos a un permiso.

La arquitectura aprobada conceptualmente considera una fuente de identidad, una fuente de estado académico, una fuente de roles y participaciones, un motor de reglas y consumidores funcionales. Su diseño detallado consta en [AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001](../architecture/AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001.md).

## 4. Arquitectura actual y coexistencia histórica

```text
tipo_est                 permiso_login
    \                       /
             ajax/login.php
                    ↓
                $_SESSION
                    ↓
                 módulos
```

`tipo_est`, `permiso_login`, `ajax/login.php` y `$_SESSION` son fuentes y mecanismos históricos existentes. No se eliminan inmediatamente. La migración hacia permisos funcionales derivados será incremental y deberá preservar el comportamiento observable mientras existan módulos dependientes del modelo heredado.

`Authorization.php` existente se mantiene como punto de evolución para centralizar el consumo de autorizaciones. Esta Feature no modifica su comportamiento ni le incorpora reglas derivadas.

## 5. Matrices aprobadas

### Estudiantes

| Estado | Permisos |
| --- | --- |
| Postulante | Mi perfil, Reglamento |
| Aceptado | Mi perfil, Datos académicos, Cursos vista, Calendario, Reglamento |
| Matriculado | Igual a Aceptado |
| Graduado | Mi perfil, Reglamento |
| Reprobado | Mi perfil, Reglamento |
| Retirado | Mi perfil, Reglamento |
| Eliminado | Sin acceso |

### Profesores

| Estado | Permisos |
| --- | --- |
| Registrado | Mi perfil, Reglamento |
| Aceptado | Cursos, Mi perfil, Reglamento, Calendario |
| Inhabilitado | Sin acceso |

## 6. Decisiones pendientes

### Comité

- Un profesor puede pertenecer al Comité.
- El Comité suma capacidades adicionales.
- Solo el Administrador agrega o remueve integrantes.
- Pendientes: permisos funcionales específicos y módulos afectados.

### Administrador

- Acceso administrativo completo.
- Pendiente: alcance administrativo formal.

### Permiso histórico 3

Pendiente de resolución:

```text
permiso 3
├── estudiante aceptado
└── docente
```

No se decide dentro de esta Feature. No debe reinterpretarse ni eliminarse durante la transición sin una definición formal posterior.

## 7. Estrategia de transición

### Fase 1 — Capa derivada de lectura

Objetivo: crear una resolución de permisos derivada sin modificar el comportamiento existente.

Características:

- lectura del contexto usuario;
- cálculo de permisos efectivos;
- coexistencia con autorización histórica.

### Fase 2 — Migración progresiva de módulos

Los módulos migrarán progresivamente desde controles directos hacia:

```text
Authorization
        ↓
Reglas derivadas
```

La migración mantendrá el comportamiento observable y deberá contar con validación y reversibilidad por módulo.

### Fase 3 — Resolución legado

Pendientes de esta fase:

- permiso histórico 3;
- permisos duplicados;
- dependencias directas de sesión.

El retiro de dependencias históricas solo podrá evaluarse después de resolver estos pendientes y con autorización separada.

## 8. Riesgos

- Duplicidad temporal de reglas.
- Diferencias entre permisos históricos y derivados.
- Pérdida de acceso durante migración.
- Usuarios existentes con estados inconsistentes.
- Módulos con autorización distribuida.
- Dependencia actual de claves de sesión.

Estos riesgos requieren reglas explícitas, trazabilidad, validación de equivalencia, coexistencia controlada y migración gradual por módulos.

## 9. Dependencias

- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md).
- [ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](../governance/ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md).
- [ACTA-SOLICITUD-RESOLUCION-EPIC003-ESTADO-PERMISO-001](../governance/ACTA-SOLICITUD-RESOLUCION-EPIC003-ESTADO-PERMISO-001.md).
- [AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](../architecture/AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md).
- [AT-EPIC003-IMPLEMENTACION-ESTADO-PERMISO-001](../architecture/AT-EPIC003-IMPLEMENTACION-ESTADO-PERMISO-001.md).
- [AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001](../architecture/AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001.md).
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001](FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md).
- `Authorization.php` existente y resoluciones EPIC-003.

## 10. Exclusiones

La Feature NO incluye:

- implementación del motor de políticas;
- cambios en `login`;
- cambios en `permiso_login`;
- migración masiva;
- creación inmediata de permisos nuevos;
- modificación de base de datos;
- cambios de código;
- creación de TASK.

## 11. Restricciones cumplidas

```text
Sin cambios de código.
Sin implementación.
Sin cambios ADR.
Sin cambios Roadmap.
Sin cambios Manual Maestro.
Sin TASK.
Sin commit.
```

## 12. Fuentes utilizadas

- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md).
- [ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](../governance/ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md).
- [ACTA-SOLICITUD-RESOLUCION-EPIC003-ESTADO-PERMISO-001](../governance/ACTA-SOLICITUD-RESOLUCION-EPIC003-ESTADO-PERMISO-001.md).
- [AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](../architecture/AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md).
- [AT-EPIC003-IMPLEMENTACION-ESTADO-PERMISO-001](../architecture/AT-EPIC003-IMPLEMENTACION-ESTADO-PERMISO-001.md).
- [AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001](../architecture/AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001.md).
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001](FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md).
- Resoluciones institucionales EPIC-003.

## 13. Siguiente paso posterior

Revisión técnica: `FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001` actualizada.
