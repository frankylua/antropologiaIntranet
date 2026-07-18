# AT-EPIC003-PILOTO-AUTORIZACION-DERIVADA-001

## Análisis del piloto de autorización derivada para Reglamento

## 1. Identificación y alcance

- **Clasificación:** [ARQ] Piloto capa autorización derivada; [TEC] Migración incremental autorización; [DOC] Documento técnico EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Feature asociada:** [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md).
- **Estado:** Análisis técnico documental. No autoriza implementación.

El objetivo es analizar cómo un módulo existente puede consumir una resolución de permisos derivada sin modificar inmediatamente el modelo legado. Este documento no define código, cambios de sesión, permisos técnicos ni una TASK.

## 2. Decisión institucional considerada

```text
Estado académico
Rol institucional
Participación
        ↓
Reglas de autorización
        ↓
Permisos funcionales
```

La resolución debe componer capacidades desde el contexto institucional, sin convertir una clave histórica de sesión en sinónimo de estado o rol.

## 3. Piloto seleccionado: Reglamento

Se recomienda Reglamento como primer piloto de autorización derivada por las siguientes razones:

- ya cuenta con una migración previa al punto de consulta `Authorization`;
- es un módulo de solo lectura;
- presenta bajo riesgo funcional;
- no ejecuta operaciones críticas;
- permite validar la coexistencia entre legado y resultado derivado sin modificar reglas de negocio.

El piloto es válido como etapa de evaluación de arquitectura. No autoriza modificar el módulo ni su control actual.

## 4. Estado actual de Reglamento

El módulo `form-doc/reglamento.php` consume `Authorization.php` y solicita las claves históricas `admin`, `comite` o `aceptado`. `Authorization::hasAny()` comprueba la presencia de esas claves en `$_SESSION`.

Flujo actual:

```text
permiso_login
        ↓
ajax/login.php
        ↓
$_SESSION: admin / comite / aceptado
        ↓
Authorization::hasAny()
        ↓
Reglamento
```

La fuente operativa sigue siendo `permiso_login`, materializada por `ajax/login.php` como sesión. Reglamento es compatible con `Authorization` existente, pero aún no consume un permiso funcional explícito de Reglamento ni una regla derivada.

## 5. Diseño conceptual del piloto

```text
Usuario autenticado
        ↓
Contexto usuario
  - estado académico
  - rol institucional
  - participación
        ↓
Resolución de permisos derivados
        ↓
Authorization
        ↓
Reglamento
```

La evaluación derivada propuesta para el piloto es de lectura: obtiene el contexto disponible, resuelve de forma conceptual el permiso funcional Reglamento y permite contrastarlo con el resultado heredado. No modifica `permiso_login`, `$_SESSION`, login, `Authorization.php` ni el módulo.

## 6. Alcance de evaluación funcional

La matriz aprobada permite esperar permiso Reglamento para estudiantes Postulante, Aceptado, Matriculado, Graduado, Reprobado y Retirado; no para Eliminado. Para profesores, el estado Registrado y Aceptado incluye Reglamento; Inhabilitado no tiene acceso. La composición de Comité suma sus capacidades a las del profesor.

| Actor o contexto | Resultado derivado esperado para Reglamento |
| --- | --- |
| Estudiante aceptado | Acceso Reglamento |
| Estudiante matriculado | Acceso Reglamento |
| Estudiante eliminado | Sin acceso |
| Profesor aceptado | Acceso Reglamento |
| Profesor + Comité | Acceso Reglamento |
| Usuario sin permisos | Sin acceso |

Los casos deben verificar tanto la decisión derivada esperada como su equivalencia o diferencia respecto del resultado de sesión vigente. Una diferencia no autoriza una corrección automática: debe registrarse para análisis institucional y técnico posterior.

## 7. Coexistencia con el legado

Durante la fase piloto se mantienen:

```text
permiso_login
        +
Authorization actual
        +
reglas derivadas de lectura
```

La estrategia es comparar y validar, no reemplazar. El comportamiento observable de Reglamento se mantiene mientras las fuentes históricas continúen siendo la fuente operativa para módulos existentes.

La coexistencia requiere identificar qué resultado utilizó la evaluación: derivado, histórico o diferencia pendiente. No se debe aplicar fallback silencioso que oculte inconsistencias entre estado, roles, participación y permisos históricos.

## 8. Validaciones y rollback conceptual

### Validaciones requeridas

- Casos funcionales de la matriz del piloto.
- Usuarios existentes con estados, roles y permisos históricos conocidos.
- Equivalencia entre acceso histórico y resultado derivado cuando corresponda.
- Registro de diferencias, en especial para cuentas asociadas al permiso histórico 3.
- Verificación de que el módulo permanezca solo de lectura y sin cambio de navegación o comportamiento observable.

### Rollback conceptual

El piloto debe ser reversible por diseño: si la evaluación derivada produce un resultado no validado o una divergencia relevante, Reglamento continúa sujeto a `Authorization` y a la fuente histórica vigente. La evaluación derivada se retira de la comparación sin alterar datos, login, sesión ni permisos existentes.

## 9. Riesgos

| Riesgo | Consideración del piloto |
| --- | --- |
| Diferencia entre permisos históricos y derivados | Puede revelar cuentas con estado o rol no alineado a `permiso_login`; se debe registrar, no corregir automáticamente. |
| Dependencia actual de sesión | `Authorization` aún consume claves de `$_SESSION`; el piloto no debe asumir que estas representan permisos funcionales. |
| Usuarios inconsistentes | Cuentas existentes pueden no reflejar la matriz aprobada. |
| Duplicidad temporal de reglas | La regla derivada y el control histórico pueden producir decisiones distintas durante la coexistencia. |
| Migración incompleta | Otros módulos pueden conservar controles directos aunque Reglamento evolucione primero. |

## 10. Recomendación técnica

Reglamento es válido como primer piloto porque ya utiliza el punto central `Authorization`, es de solo lectura y posee una regla de acceso acotada. El éxito del piloto se mide por:

- resolución derivada documentada para todos los casos definidos;
- ausencia de cambio observable en Reglamento;
- identificación trazable de diferencias con el legado;
- reversibilidad sin cambios de datos, sesión o permisos;
- confirmación de que el patrón puede expresarse sin exponer claves históricas al módulo futuro.

La migración del siguiente módulo solo debe evaluarse cuando el piloto demuestre equivalencia controlada, las diferencias detectadas tengan tratamiento definido y exista un módulo candidato con riesgo y dependencias acotados. Cursos no es un candidato inmediato mientras mantenga mayor impacto funcional y dependencias de autorización más amplias.

## 11. Exclusiones

Este análisis no modifica ni autoriza modificar:

- `Authorization.php`;
- `ajax/login.php`;
- `permiso_login`;
- sesiones;
- SQL;
- módulos;
- código fuente.

No implementa cambios, no crea TASK y no realiza commit.

## 12. Fuentes utilizadas

- [ADR-002](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md).
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md).
- [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md).
- [AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001](AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001.md).
- [ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001](../governance/ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001.md).
- [ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](../governance/ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md).
- Evidencia técnica de lectura: `form-doc/reglamento.php`, `src/Security/Authorization.php` y `ajax/login.php`.

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

Revisión técnica: `AT-EPIC003-PILOTO-AUTORIZACION-DERIVADA-001`.
