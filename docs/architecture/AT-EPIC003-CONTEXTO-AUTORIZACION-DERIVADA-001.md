# AT-EPIC003-CONTEXTO-AUTORIZACION-DERIVADA-001

## Contexto requerido para autorización derivada

## 1. Identificación y alcance

- **Clasificación:** [ARQ] Contexto institucional para autorización derivada; [TEC] Evolución incremental; [DOC] Análisis técnico EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Feature asociada:** [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md).
- **Dependencia que habilita:** [TASK-EPIC003-AUTORIZACION-REGLAMENTO-DERIVADA-PILOTO-001](../tasks/TASK-EPIC003-AUTORIZACION-REGLAMENTO-DERIVADA-PILOTO-001.md).
- **Estado:** Pendiente revisión técnica.

Este AT define conceptualmente el contexto mínimo que requiere una resolución de autorización derivada. No autoriza implementación, cambios de código, cambios de sesión, cambios de login, cambios de datos ni una nueva TASK.

## 2. Problema actual

La sesión vigente expone permisos y claves históricas, pero no un contexto institucional diferenciado.

```text
$_SESSION
    ↓
permisos históricos
```

Por ello no permite establecer de forma confiable si una identidad corresponde a los estados académicos Aceptado, Matriculado o Eliminado. Tampoco permite inferir por sí sola el rol institucional ni la participación aplicable.

La clave histórica no es sustituto de contexto: usarla como tal mezclaría permiso, estado y rol, contradiciendo el modelo aprobado.

## 3. Contexto de autorización conceptual

```text
Contexto de autorización
    =
Identidad
    +
Estado académico
    +
Rol institucional
    +
Participación
```

| Dimensión | Responsabilidad conceptual | No representa |
| --- | --- | --- |
| Identidad | Identificar al usuario autenticado. | Estado, rol o permiso funcional. |
| Estado académico | Exponer el estado vigente de la relación académica del estudiante. | Un permiso histórico. |
| Rol institucional | Exponer la condición institucional, como profesor o administrador. | Estado académico. |
| Participación | Exponer pertenencias acumulativas, como Comité. | Reemplazo de rol o estado. |

La resolución derivada consume este contexto en modo lectura y determina permisos funcionales efectivos según la matriz institucional. No persiste ni sincroniza datos.

```text
Usuario autenticado
        ↓
Contexto de autorización
        ↓
Reglas de autorización
        ↓
Permisos efectivos
```

## 4. Alternativas conceptuales

### A. Sesión enriquecida

Al autenticar, la sesión incorporaría identidad, estado, roles y participaciones como valores separados de los permisos históricos.

**Ventajas:** acceso rápido para los módulos y consumo simple durante la sesión.

**Riesgos:** modifica el contrato de login y sesión; el contexto puede quedar desactualizado; exige definir vigencia, invalidación y compatibilidad. No es apta para el piloto actual sin una autorización específica.

### B. Proveedor independiente de contexto

Una capa de lectura obtiene el contexto institucional vigente a partir de la identidad autenticada, sin modificar la sesión histórica.

**Ventajas:** mantiene la separación de responsabilidades, permite evaluar contexto actual y preserva el contrato legado. Es reversible y acota la evolución al consumidor piloto.

**Riesgos:** requiere definir fuentes de lectura, reglas ante identidad sin contexto y manejo explícito de inconsistencias; no debe modificar modelos ni datos.

### C. Resolución bajo demanda

Cada consulta de autorización resuelve directamente estado, rol y participación al momento de evaluar una capacidad.

**Ventajas:** contexto actualizado y sin estado derivado persistente.

**Riesgos:** acopla la autorización a las fuentes institucionales, duplica consultas si no se centraliza y aumenta el costo y la superficie de error. Requiere un contrato de proveedor para no replicar lógica en los módulos.

## 5. Evaluación comparativa

| Alternativa | Compatibilidad legado | Actualidad del contexto | Impacto inicial | Adecuación al piloto |
| --- | --- | --- | --- | --- |
| Sesión enriquecida | Baja: cambia sesión y login. | Depende de su vigencia. | Alto. | No recomendada ahora. |
| Proveedor independiente | Alta: sesión histórica permanece intacta. | Conforme a la fuente de lectura. | Acotado. | Recomendada. |
| Resolución bajo demanda | Media: necesita contrato central. | Alta. | Medio. | Complementaria al proveedor, no directa desde módulos. |

## 6. Permiso histórico 3

```text
permiso 3
├── estudiante aceptado
└── docente
```

El permiso histórico 3 no permite distinguir un estado académico de un rol institucional. Por tanto:

- No debe usarse como fuente del estado Aceptado.
- No debe usarse como fuente del rol Profesor.
- No debe reinterpretarse, eliminarse ni sincronizarse automáticamente.
- Se conserva únicamente como compatibilidad histórica hasta una definición institucional y técnica posterior.

El contexto derivado debe obtener sus dimensiones desde fuentes separadas, y cualquier divergencia con el resultado legado debe ser identificable para revisión, no corregida automáticamente.

## 7. Recomendación arquitectónica incremental

Se recomienda definir primero un **proveedor independiente de contexto de solo lectura**, invocado por la capa de autorización y no directamente por los módulos. La resolución bajo demanda puede ser su modalidad operativa inicial, evitando persistir o enriquecer `$_SESSION`.

```text
Identidad autenticada
        ↓
Proveedor de contexto de lectura
  - estado académico
  - rol institucional
  - participación
        ↓
Resolución derivada
        ↓
Authorization
        ↓
Módulo piloto
```

Durante la transición, `permiso_login` y las claves históricas conservan su función de compatibilidad. La decisión derivada y la decisión histórica deben poder contrastarse sin fallback silencioso. La sesión enriquecida solo debe evaluarse después de definir su contrato, su vigencia y la política de invalidación mediante una autorización independiente.

## 8. Dependencia para Reglamento

La TASK de Reglamento no debe retomarse hasta que una revisión técnica posterior defina y apruebe:

- la fuente de identidad para el proveedor;
- las fuentes de lectura de estado, rol y participación;
- el contrato de contexto y el comportamiento ante datos ausentes o inconsistentes;
- la trazabilidad de diferencias entre decisión derivada y autorización histórica;
- la reversión del consumidor piloto sin cambios permanentes en datos.

## 9. Exclusiones

Este AT no modifica ni autoriza modificar:

- código fuente;
- `login.php` o el proceso de login;
- `Authorization.php`;
- `permiso_login`;
- SQL, migraciones, tablas o modelo de datos;
- sesiones vigentes;
- reglas institucionales;
- TASK, ADR, Roadmap o Manual Maestro.

No implementa cambios y no realiza commit.

## 10. Fuentes

- [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md).
- [AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001](AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001.md).
- [AT-EPIC003-PILOTO-AUTORIZACION-DERIVADA-001](AT-EPIC003-PILOTO-AUTORIZACION-DERIVADA-001.md).
- [TASK-EPIC003-AUTORIZACION-REGLAMENTO-DERIVADA-PILOTO-001](../tasks/TASK-EPIC003-AUTORIZACION-REGLAMENTO-DERIVADA-PILOTO-001.md).

## 11. Siguiente paso posterior

```text
Revisión técnica:
AT-EPIC003-CONTEXTO-AUTORIZACION-DERIVADA-001
```

Solo tras esa revisión puede evaluarse retomar la implementación supervisada de `TASK-EPIC003-AUTORIZACION-REGLAMENTO-DERIVADA-PILOTO-001`.
