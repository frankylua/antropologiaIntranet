# AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001

## Diseño conceptual de la capa de autorización derivada

## 1. Identificación y alcance

- **Clasificación:** [ARQ] Diseño capa autorización derivada; [TEC] Arquitectura incremental autorización; [DOC] Documento técnico oficial EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Feature asociada:** [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md).
- **Estado:** Análisis técnico de diseño. No autoriza implementación.

Este análisis diseña conceptualmente una futura capa de autorización derivada. No define código, esquemas de base de datos, permisos técnicos, cambios de sesión ni una migración ejecutable.

## 2. Decisión de partida y objetivo

Para estudiantes, la decisión institucional aprobada establece que el estado académico determina el grupo de permisos funcionales. El modelo objetivo general conserva, a la vez, la separación de dimensiones definida en ADR-002:

```text
Estado académico + Rol institucional + Participación
                         ↓
              Reglas de autorización
                         ↓
             Permisos funcionales efectivos
                         ↓
                     Módulos
```

La derivación no convierte estado, rol o participación en sinónimos de permiso. Las reglas de autorización son las que resuelven el resultado funcional a partir del contexto institucional de una identidad.

## 3. Arquitectura actual confirmada

```text
Base de datos
    ↓
estudiante.tipo_est              permiso_login
    ↓                                  ↓
estado académico                 permisos históricos
    \                                /
     \                              /
              ajax/login.php
                    ↓
                $_SESSION
                    ↓
                 módulos
```

| Elemento | Responsabilidad actual | Acoplamiento o limitación |
| --- | --- | --- |
| `estudiante.tipo_est` | Mantiene el estado académico del estudiante. | El cambio de estado observado actualiza este dato, sin sincronización confirmada de permisos. |
| `permiso_login` | Fuente operativa de permisos durante la autenticación. | Sus identificadores históricos no representan un catálogo de permisos funcionales. |
| `ajax/login.php` | Traduce identificadores de `permiso_login` a claves de `$_SESSION`. | Concentra una traducción transversal basada en IDs históricos. |
| `$_SESSION` | Expone claves como `admin`, `comite`, `aceptado`, `docente` y `estudiante`. | Mezcla capacidades, condición institucional y compatibilidad heredada. |
| Módulos | Consumen directamente claves de sesión o `Authorization`. | La autorización queda distribuida y depende del contrato histórico de sesión. |
| `Authorization.php` | Evalúa si existe alguna clave de sesión solicitada por el consumidor. | Centraliza parte del consumo, pero no resuelve reglas ni permisos derivados. |

Problemas principales: dos fuentes independientes para estado y permisos; ausencia de una regla funcional central; dependencia de sesiones; y ambigüedad del permiso histórico 3.

## 4. Arquitectura objetivo conceptual

```text
Identity Context
    ↓
Academic State Provider ───────┐
                               │
Role / Participation Provider ─┼──→ Authorization Policy Engine
                               │              ↓
                               │     Functional Permissions
                               │              ↓
                               └──────────── Modules
```

La arquitectura objetivo propone una evaluación de solo lectura del contexto de identidad. La capa deriva permisos funcionales para los consumidores, sin obligar por sí misma a persistirlos ni a modificar la fuente heredada.

### 4.1 Identity Context

Responsable de identificar el usuario autenticado y de aportar el contexto mínimo de sesión y autenticación. No determina estados académicos, roles ni permisos funcionales.

### 4.2 Academic State Provider

Responsable de obtener el estado académico vigente de un estudiante y de exponerlo como un dato institucional diferenciado. Las transiciones académicas siguen siendo responsabilidad de sus procesos de dominio; este proveedor no las ejecuta.

### 4.3 Role / Participation Provider

Responsable de aportar los roles institucionales y participaciones aplicables: condición de profesor, participación en Comité y condición de Administrador. Debe permitir que una participación agregue capacidades sin reemplazar las capacidades derivadas de otras dimensiones.

### 4.4 Authorization Policy Engine

Responsable de transformar el contexto de identidad, estado, roles y participaciones en permisos funcionales efectivos mediante reglas explícitas. Debe concentrar:

- matriz estado académico → permisos de estudiantes;
- matriz de estados de profesores → permisos;
- composición de capacidades adicionales del Comité;
- precedencia y acumulación con Administrador;
- decisión de acceso o ausencia de permisos para estados Eliminado e Inhabilitado.

No es responsable de autenticar, actualizar datos académicos, gestionar integrantes de Comité ni modificar `permiso_login`.

### 4.5 Functional Permissions

Representan capacidades consumibles por módulos —por ejemplo, Mi perfil, Reglamento, Datos académicos, Cursos (vista), Cursos y Calendario— independientes de IDs históricos y claves de sesión. El catálogo técnico concreto y su representación quedan pendientes de definición posterior.

### 4.6 Consumidores

Los consumidores serán `Authorization.php`, módulos de navegación y endpoints protegidos. La evolución deseada es que consulten capacidades funcionales, sin consultar directamente estado académico, IDs de permisos históricos ni claves de sesión específicas.

## 5. Reglas funcionales consideradas

### Estudiantes

| Estado | Permisos funcionales |
| --- | --- |
| Postulante | Mi perfil, Reglamento |
| Aceptado | Mi perfil, Datos académicos, Cursos (vista), Calendario, Reglamento |
| Matriculado | Mi perfil, Datos académicos, Cursos (vista), Calendario, Reglamento |
| Graduado | Mi perfil, Reglamento |
| Reprobado | Mi perfil, Reglamento |
| Retirado | Mi perfil, Reglamento |
| Eliminado | Sin acceso |

### Profesores, Comité y Administrador

| Condición | Permisos o regla conocida |
| --- | --- |
| Profesor registrado | Mi perfil, Reglamento |
| Profesor aceptado | Cursos, Mi perfil, Reglamento, Calendario |
| Profesor inhabilitado | Sin acceso |
| Integrante de Comité | Agrega capacidades adicionales a las del profesor; detalle pendiente. |
| Administrador | Acceso administrativo completo; alcance detallado pendiente. |

El motor no debe inventar los permisos pendientes de Comité ni el alcance detallado de Administrador. Debe disponer de una política explícita antes de resolver esos casos.

## 6. Estrategias de coexistencia con el legado

### Estrategia A — Fuente temporal heredada con evaluación derivada

```text
Autorización derivada
        +
fallback de permiso_login / sesión heredada
```

Permite evaluar o introducir capacidades derivadas mientras `permiso_login` y `$_SESSION` preservan el comportamiento de módulos aún no migrados. Riesgo: coexistencia de dos decisiones de autorización y posible divergencia; requiere trazabilidad de cuál fuente atendió cada consumidor.

### Estrategia B — Reemplazo progresivo del consumo directo

```text
Módulo
  ↓
Authorization
  ↓
Reglas derivadas
```

Cada módulo deja de consultar directamente sesiones o permisos históricos y consume una interfaz central de capacidades funcionales. Es la estrategia recomendada para la migración incremental porque acota el impacto por módulo. Requiere pruebas de equivalencia, reversibilidad y una política de fallback mientras coexistan módulos heredados.

### Estrategia C — Migración completa

Elimina la dependencia operativa de `permiso_login` y de claves históricas de sesión. Solo es viable después de completar las matrices, reglas de composición, inventario de consumidores, tratamiento de usuarios vigentes y una autorización separada. Su principal riesgo es una pérdida o ampliación masiva de acceso.

## 7. Migración incremental propuesta

### Fase 1 — Capa de lectura sin cambio de comportamiento

Definir el contrato conceptual de contexto y permisos funcionales, y evaluar de forma no mutante los resultados esperados frente al comportamiento heredado. No modificar login, sesiones ni permisos históricos.

### Fase 2 — Módulos piloto

Seleccionar módulos acotados y migrarlos al consumo centralizado de capacidades, con validación funcional y mecanismo de reversión por módulo.

### Fase 3 — Resolución de permisos históricos

Completar el catálogo funcional, definir las reglas de composición y resolver el tratamiento del permiso histórico 3, de usuarios vigentes y de excepciones institucionales.

### Fase 4 — Retiro gradual de dependencias heredadas

Tras validación acumulada, evaluar el retiro progresivo de dependencias directas de sesión y permisos históricos. Requiere decisión, alcance y autorización independiente.

## 8. Caso del permiso histórico 3

Situación confirmada:

```text
permiso 3
├── estudiante aceptado
└── docente
```

**Riesgo:** el mismo identificador técnico no distingue un estado académico de un rol institucional; usarlo como regla derivada produciría asignaciones incorrectas o pérdida de capacidades.

**Alternativas a evaluar, sin decisión en este AT:** mantenerlo como compatibilidad temporal; separar la representación de estudiante aceptado y docente; o reemplazar su consumo por permisos funcionales derivados.

**Impacto de migración:** requiere identificar cuentas afectadas, verificar sus condiciones reales y preservar capacidades acumulativas. No debe eliminarse, reinterpretarse ni sincronizarse automáticamente durante la transición.

## 9. Riesgos arquitectónicos

| Riesgo | Manifestación | Tratamiento requerido |
| --- | --- | --- |
| Duplicidad de reglas | Módulos, sesión y motor aplican criterios distintos. | Centralizar reglas gradualmente y contrastar resultados. |
| Inconsistencias | Estado, rol, participación y permisos heredados no coinciden. | Inventario de solo lectura, tratamiento de excepciones y trazabilidad. |
| Pérdida de acceso | Una regla o transición retira una capacidad legítima. | Reversibilidad, pruebas por capacidad y coexistencia controlada. |
| Migración de usuarios actuales | Las cuentas existentes no reflejan la matriz objetivo. | Evaluación previa individual o por lotes validados, sin migración masiva inicial. |
| Autorización directa en módulos | Un módulo evita la capa central y conserva una decisión distinta. | Inventario y migración progresiva de consumidores. |
| Dependencia de sesión | Sesiones vigentes no reflejan cambios de contexto o reglas. | Política posterior de vigencia, reautenticación o invalidación controlada. |

## 10. Recomendación técnica

Se recomienda la arquitectura objetivo basada en proveedores de contexto institucional, un motor de políticas de autorización y permisos funcionales consumidos centralmente. La transición recomendada combina la Estrategia A como compatibilidad temporal y la Estrategia B como mecanismo de migración por módulos.

Dependencias principales: la Feature de modelo derivado, `Authorization.php` existente como punto de consumo evolutivo, las matrices institucionales aprobadas y las fuentes heredadas que hoy alimentan estado, permisos y sesión.

Decisiones pendientes antes de implementar: catálogo técnico de permisos funcionales; reglas de composición y precedencia; capacidades específicas de Comité; alcance formal de Administrador; tratamiento de sesiones; y resolución del permiso histórico 3. Este AT no toma dichas decisiones ni crea una TASK.

## 11. Fuentes utilizadas

- [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md).
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md).
- [ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](../governance/ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md).
- [ACTA-SOLICITUD-RESOLUCION-EPIC003-ESTADO-PERMISO-001](../governance/ACTA-SOLICITUD-RESOLUCION-EPIC003-ESTADO-PERMISO-001.md).
- [AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md).
- [AT-EPIC003-IMPLEMENTACION-ESTADO-PERMISO-001](AT-EPIC003-IMPLEMENTACION-ESTADO-PERMISO-001.md).
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md).
- Evidencia técnica de lectura: `ajax/login.php`, `src/Model/Login.php`, `src/Model/Estudiante.php`, `ajax/estudiante.php` y `src/Security/Authorization.php`.

## 12. Restricciones cumplidas

```text
Sin cambios de código.
Sin implementación.
Sin cambios ADR.
Sin cambios Roadmap.
Sin cambios Manual Maestro.
Sin commit.
```

## 13. Siguiente paso posterior

Revisión técnica: `AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001`.
