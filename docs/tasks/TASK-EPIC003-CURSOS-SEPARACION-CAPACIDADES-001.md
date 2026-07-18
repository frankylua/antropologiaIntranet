# TASK-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001

## Separación técnica incremental de capacidades del módulo Cursos

## 1. Identificación

- **Clasificación:** [TEC] separación de capacidades del módulo Cursos; [ARQ] preparación de evolución de autorización; [GOV] derivada de EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Feature relacionada:** FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.
- **Antecedentes:** AT-EPIC003-AUTORIZACION-CURSOS-001 y AT-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.
- **TASK relacionada bloqueada:** TASK-EPIC003-AUTORIZACION-CURSOS-CONSULTA-PILOTO-001.
- **Estado:** 🟡 Pendiente revisión técnica.

## 2. Antecedentes y dependencias

Esta TASK deriva del análisis que confirmó que el módulo Cursos reúne consulta y operaciones administrativas bajo una validación general de acceso. La centralización de la consulta no puede continuar mientras dicha autorización permita implícitamente crear, actualizar o eliminar.

Dependencias:

- ✅ EPIC-003.
- ✅ FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.
- ✅ AT-EPIC003-AUTORIZACION-CURSOS-001.
- ✅ AT-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.

Pendiente: revisión técnica de esta TASK.

## 3. Objetivo

Definir una evolución técnica incremental para separar las capacidades funcionales del módulo Cursos antes de reanudar la centralización de su autorización de consulta.

```text
Visualización
  ≠
Creación
  ≠
Actualización
  ≠
Eliminación
```

Estado actual:

```text
Permiso actual
  ↓
Módulo Cursos
  ↓
Consulta + Escritura + Actualización + Eliminación
```

Estado objetivo conceptual:

```text
Permiso funcional
  ↓
Capacidad específica
  ↓
Acción permitida
```

Esta TASK no define ni implementa un modelo definitivo de permisos.

## 4. Principios y restricción principal

Tener acceso a visualizar Cursos no debe implicar capacidad administrativa. La separación debe aplicarse de forma coherente en:

```text
Interfaz
  ×
Endpoint
  ×
Regla de autorización
```

Ocultar botones por sí solo no cumple el objetivo: cada operación debe tener una frontera de autorización verificable antes de realizarse.

La evolución debe respetar la separación aprobada:

```text
Estado académico
  ≠
Rol institucional
  ≠
Participación
  ≠
Permiso funcional
  ≠
Acción permitida
```

## 5. Alcance incluido

- Análisis técnico del acoplamiento actual entre consulta y administración del módulo Cursos.
- Identificación de los puntos donde se mezclan capacidades en interfaz, script, endpoint y métodos asociados.
- Separación conceptual entre lectura/consulta y administración.
- Definición técnica de protección de endpoints según operación: lectura, creación, actualización y eliminación.
- Identificación de condiciones de compatibilidad, reversión y validación necesarias para futuras autorizaciones centralizadas.
- Registro de los cambios futuros mínimos que una implementación aprobada tendría que evaluar, sin ejecutarlos mediante esta TASK documental.

## 6. Alcance excluido

- Crear permisos nuevos o definir sus nombres definitivos.
- Modificar `permiso_login`.
- Cambiar el modelo de usuarios, login o sesiones.
- Cambiar estados académicos, roles, participaciones o reglas institucionales.
- Migrar Cursos a `Authorization`.
- Implementar permisos funcionales o cambios en código, SQL o datos.
- Modificar ADR, Roadmap, Manual Maestro, Feature, AT o TASK existentes.

## 7. Áreas técnicas a revisar en una futura ejecución aprobada

| Área | Archivo o componente | Revisión requerida |
| --- | --- | --- |
| Frontend | `form-doc/ver.curso.php` | Separar visualmente controles de consulta, alta, edición y eliminación; no inferir seguridad desde su visibilidad. |
| Cliente | `form-doc/scripts/curso.js` | Inventariar y asociar cada llamada AJAX a una capacidad específica: `read`, `query_id`, `insert-update` y `delete`. |
| Backend | `ajax/curso.php` | Establecer una frontera de autorización por operación antes de las acciones de lectura o mutación. |
| Persistencia | Métodos asociados de `Curso` | Verificar el contrato de creación, edición y eliminación, incluido el archivo de programa asociado. |
| Autorización | Sesiones actuales y futura integración con `Authorization` | Mantener inicialmente la fuente vigente, comprobar acumulación de claves y evitar interpretar claves históricas como permisos funcionales. |

La revisión debe considerar también la discrepancia ya identificada entre la navegación del módulo y el acceso por URL directa.

## 8. Condiciones técnicas necesarias

Antes de una implementación deben quedar documentadas y validadas las siguientes condiciones:

1. Política efectiva que debe conservarse para cada condición histórica de acceso, incluida la divergencia entre navegación y URL directa.
2. Capacidad administrativa requerida por cada operación de creación, actualización y eliminación; no puede inferirse desde la capacidad de consulta.
3. Mecanismo para autorizar cada operación en el endpoint, además de la interfaz.
4. Estrategia de compatibilidad con sesiones y permisos acumulativos actuales, sin modificar `permiso_login`.
5. Casos de prueba funcionales para acceso permitido, bloqueo y ausencia de capacidades adicionales.
6. Criterio de reversión por cambio pequeño y aislado.

## 9. Criterios de aceptación

### Funcionales

- Existe una diferenciación conceptual explícita entre consulta y administración.
- La consulta no implica creación, actualización ni eliminación.
- La escritura no queda disponible únicamente por disponer de acceso visual.
- No se asume que una clave histórica, estado académico o participación autorice una acción administrativa.

### Técnicos

- Están identificados los puntos de acoplamiento en interfaz, cliente, endpoint y persistencia.
- Están identificados los cambios futuros necesarios para aplicar autorización por operación.
- La estrategia prevista protege endpoint y no se limita a ocultar controles.
- La evolución propuesta es pequeña, gradual y reversible.

### Arquitectónicos

- La separación permite una integración posterior con `Authorization` para consulta sin conceder administración.
- No rompe el modelo actual ni altera permisos existentes sin una validación explícita.
- Mantiene la separación entre estado, rol, participación, permiso funcional y acción permitida.

## 10. Validación funcional requerida

| Caso | Resultado esperado |
| --- | --- |
| Usuario autorizado solo para consulta | Puede listar y consultar detalle; no puede crear, actualizar ni eliminar. |
| Usuario autorizado para una operación administrativa definida | Solo puede ejecutar la operación que la política aprobada le permita. |
| Usuario sin acceso aplicable | No puede acceder a la capacidad ni invocar con éxito su endpoint asociado. |
| Usuario con permisos acumulativos | Conserva únicamente las capacidades efectivas que correspondan a la política aprobada. |

## 11. Riesgos

- Cambio accidental de permisos al sustituir o reinterpretar condiciones históricas.
- Pérdida de accesos actuales por no preservar la acumulación de claves de sesión.
- Creación prematura de permisos funcionales o modificación indebida de `permiso_login`.
- Duplicación de reglas entre interfaz, endpoint y componente de autorización.
- Aumento accidental de privilegios al conservar operaciones administrativas tras habilitar consulta.
- Impacto observable en usuarios existentes al modificar botones o condiciones sin validación funcional previa.

## 12. Reversión y condiciones de detención

La futura implementación deberá realizar cambios pequeños, independientes y verificables. Cada cambio debe poder revertirse sin modificar usuarios, permisos existentes, login, sesiones ni otros módulos.

Detener la implementación y solicitar revisión técnica si:

- se requiere crear un permiso nuevo o modificar `permiso_login`;
- no es posible separar la consulta de las operaciones administrativas en el endpoint;
- se requiere redefinir reglas académicas, institucionales o el modelo de permisos;
- la equivalencia entre navegación y URL directa no puede resolverse con política aprobada;
- se identifican impactos fuera del alcance de Cursos.

## 13. Restricciones documentales

Esta TASK no autoriza cambios de código fuente, SQL, datos, ADR, Roadmap, Manual Maestro, Feature, AT ni TASK existentes. No incluye implementación ni commit.

## 14. Fuentes utilizadas

- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md)
- [AT-EPIC003-AUTORIZACION-CURSOS-001.md](../architecture/AT-EPIC003-AUTORIZACION-CURSOS-001.md)
- [AT-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.md](../architecture/AT-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001.md)
- [TASK-EPIC003-AUTORIZACION-CURSOS-CONSULTA-PILOTO-001.md](TASK-EPIC003-AUTORIZACION-CURSOS-CONSULTA-PILOTO-001.md)

## 15. Siguiente paso

```text
Revisión técnica TASK-EPIC003-CURSOS-SEPARACION-CAPACIDADES-001
```

Solo tras esa revisión corresponde autorizar una implementación acotada.
