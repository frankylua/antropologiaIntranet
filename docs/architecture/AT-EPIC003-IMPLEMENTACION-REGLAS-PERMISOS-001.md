# AT-EPIC003-IMPLEMENTACION-REGLAS-PERMISOS-001

## Análisis técnico documental para una futura implementación de reglas de permisos

## 1. Identificación y alcance

- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Clasificación:** [ARQ] Análisis de impacto arquitectónico; [TEC] evaluación de implementación actual; [DOC] documento técnico oficial.
- **Estado:** análisis técnico documental. No autoriza implementación.

Este documento analiza la brecha entre las reglas institucionales aprobadas y la implementación actualmente documentada. Identifica riesgos, alternativas y archivos que una futura iniciativa podría afectar. No modifica ni define código, SQL, modelo de datos, reglas institucionales, ADR, Roadmap, Manual Maestro ni TASK.

## 2. Marco aprobado

ADR-002 mantiene la separación entre identidad, login, estado académico, rol institucional, participación académica y permiso funcional. La resolución institucional aprobada precisa el encadenamiento aplicable:

```text
Estado académico/institucional
  ↓
Reglas institucionales de acceso
  ↓
Permisos funcionales efectivos
```

Por tanto, un estado no equivale a un permiso: determina reglas que, al aplicarse, producen los permisos funcionales efectivos. Del mismo modo, un rol institucional o una participación académica no equivale por sí mismo a un permiso funcional.

## 3. Modelo actual documentado

### 3.1 Autenticación y autorización efectiva

La documentación técnica disponible describe el siguiente flujo actual:

```text
Credenciales
  ↓
login + permiso_login
  ↓
inicio de sesión PHP
  ↓
claves de $_SESSION por permiso
  ↓
controles de autorización de módulos
```

`permiso_login` es la fuente persistente inmediata de permisos durante el inicio de sesión. Al autenticar, sus registros se materializan como claves de sesión —entre ellas `admin`, `comite`, `aceptado`, `docente` y `estudiante`— y los módulos consultan esas claves, mediante `Authorization::hasAny()` de forma parcial o mediante verificaciones directas de sesión.

La implementación soporta múltiples permisos por `login`: una misma sesión puede contener más de una clave. Los controles no usan una consulta directa al estado académico en cada autorización.

### 3.2 Estado académico

El estado académico del estudiante se documenta en `estudiante.tipo_est`, con catálogo en `tipo_estudiante`. La actualización documentada de estado escribe ese campo y no escribe activamente en `permiso_login`. La creación de estudiantes, en cambio, contiene una asignación inicial de permisos relacionada con determinados identificadores de `tipo_est`.

En consecuencia, coexisten dos comportamientos que una evolución deberá reconciliar: una asignación inicial con dependencia de estado y una actualización de estado que preserva los permisos existentes. La documentación disponible no permite asignar con certeza cada identificador técnico de `tipo_estudiante` a todos los nombres institucionales de estado sin validación de datos.

## 4. Modelo institucional aprobado

### 4.1 Estudiantes

Los estados institucionales son: postulante, aceptado, matriculado, graduado, reprobado, retirado y eliminado.

| Estado | Perfil/Mi perfil | Reglamento | Calendario | Cursos |
| --- | --- | --- | --- | --- |
| Postulante | Sí | Sí | No | No |
| Aceptado | Sí | Sí | Sí | Solo vista |
| Matriculado | Sí | Sí | Sí | Solo vista |
| Graduado | Sí | Sí | No | No |
| Reprobado | Sí | Sí | No | No |
| Retirado | Sin acceso a intranet | Sin acceso | Sin acceso | Sin acceso |
| Eliminado | Sin acceso a intranet | Sin acceso | Sin acceso | Sin acceso |

Perfil/Mi perfil comprende datos personales y datos académicos propios; no concede automáticamente acceso a todos los módulos académicos.

### 4.2 Profesores y roles institucionales

Los estados de profesor aprobados son registrado, aceptado e inhabilitado. El profesor registrado posee Perfil y Reglamento; el aceptado añade Cursos y Calendario; el inhabilitado no accede a la intranet.

Los roles institucionales expresamente considerados son administrador y Comité académico. Un profesor puede pertenecer al Comité; sus permisos se suman a los propios del profesor. También se autorizan las coexistencias profesor + administrador y administrador + otras participaciones autorizadas. La asignación y remoción de integrantes del Comité corresponde exclusivamente al administrador.

No hay regla aprobada para participantes externos ni personas sin login.

## 5. Análisis de brecha

| Concepto | Actual documentado | Aprobado institucionalmente | Brecha a resolver en una futura iniciativa |
| --- | --- | --- | --- |
| Estado estudiante | `estudiante.tipo_est` y catálogo técnico; correspondencia nominal completa no confirmada. | Siete estados con matriz funcional explícita. | Traducir estados técnicos validados a reglas de acceso, sin convertir el estado en permiso. |
| Permiso login | Filas múltiples de `permiso_login` materializadas al iniciar sesión. | Permisos funcionales resultan de reglas institucionales. | Definir cómo se calculan, persisten, actualizan o verifican los permisos efectivos. |
| Sesiones | Claves PHP por permiso, con controles parcialmente centralizados y parcialmente directos. | El acceso debe reflejar las reglas aprobadas. | Mapear de forma compatible las capacidades aprobadas a las claves y controles existentes. |
| Profesor | Sesión `docente` documentada; no hay matriz técnica de estados de profesor en las fuentes autorizadas. | Registrado, aceptado e inhabilitado con capacidades diferenciadas. | Incorporar reglas por estado sin inferir una equivalencia técnica no validada. |
| Comité | Sesión `comite` y controles administrativos documentados. | Rol acumulativo, administrado exclusivamente por administrador. | Preservar su acumulación y establecer su fuente de asignación/remoción. |
| Administrador | Sesión `admin`, con prioridad en redirección inicial. | Puede coexistir con otras participaciones autorizadas. | Evitar que la prioridad de navegación o una reconstrucción oculte o elimine capacidades coexistentes. |
| Acumulación de permisos | Múltiples permisos por login y múltiples claves de sesión. | Se acumulan permisos de roles y participaciones autorizadas. | Definir composición, revocación acotada y casos de acceso denegado por estado sin pérdida de permisos independientes. |

## 6. Riesgos identificados

- **Reconstrucción automática de permisos:** regenerar el conjunto completo desde un solo estado puede eliminar permisos de Comité, administración u otras participaciones válidas.
- **Pérdida de permisos existentes:** retirar y reinsertar filas de `permiso_login` sin una separación de origen puede borrar permisos no comprendidos en el cambio de estado.
- **Mezcla estado/permiso:** reutilizar una clave como `aceptado` para representar simultáneamente estado y capacidad impide expresar con precisión la matriz aprobada.
- **Permisos acumulativos:** un modelo de reemplazo total no representa profesor + Comité, profesor + administrador ni otras combinaciones autorizadas.
- **Coexistencia de roles:** la redirección inicial con prioridades fijas y los controles distribuidos pueden mostrar una experiencia distinta de las capacidades efectivas.
- **Personas con múltiples roles o perfiles:** cambios en `estudiante.tipo_est` o en un estado de profesor no deben afectar permisos que provengan de un rol institucional independiente.
- **Cobertura funcional desigual:** la documentación registra que algunos controles de navegación y acceso directo no aplican idénticas combinaciones de sesión; una asignación nueva podría hacer visible esa divergencia.
- **Actores sin regla aprobada:** derivar permisos para participantes externos o personas sin login excedería la resolución vigente.

## 7. Alternativas técnicas a evaluar

No se selecciona alternativa en este análisis.

### Alternativa A — Mantener `permiso_login` como fuente efectiva y agregar reglas de asignación

- **Ventajas:** máxima compatibilidad con inicio de sesión, sesiones PHP y controles actuales; menor cambio inicial de infraestructura.
- **Riesgos:** las reglas pueden quedar repartidas entre creación, cambio de estado y administración de roles; se debe prevenir reconstrucción destructiva y pérdida de procedencia de permisos.
- **Impacto:** requiere definir operaciones explícitas y acotadas de altas, bajas y preservación en `permiso_login`, además de revisar los módulos consumidores.

### Alternativa B — Crear una capa de autorización derivada

- **Ventajas:** expresa directamente estado → reglas → permisos efectivos; separa con mayor claridad dominio institucional, roles y controles funcionales.
- **Riesgos:** introduce coexistencia con la autorización basada en sesión; exige resolver compatibilidad, recalculo, auditoría y cobertura de controles distribuidos.
- **Impacto:** mayor alcance arquitectónico y de pruebas; posiblemente requiere cambios transversales en autenticación, sesión y verificaciones de módulos.

### Alternativa C — Modelo híbrido incremental

- **Ventajas:** permite conservar `permiso_login` y las sesiones actuales como mecanismo operativo mientras una capa o reglas explícitas determinan cambios acotados y trazables; reduce el riesgo de una migración única.
- **Riesgos:** coexistencia temporal de fuentes o reglas puede provocar divergencias si no se define una fuente efectiva para cada fase.
- **Impacto:** requiere etapas, criterios de compatibilidad, pruebas de regresión y una delimitación explícita de qué componente calcula, persiste y consume cada permiso.

## 8. Archivos potencialmente afectados

La siguiente identificación procede de la evidencia documentada y no implica autorización de modificación:

| Área | Archivos potencialmente afectados | Motivo |
| --- | --- | --- |
| Autenticación y sesión | `src/Model/Login.php`, `ajax/login.php`, `src/Security/Authorization.php`, `index.php` | Obtención de permisos, creación de sesión, comprobación y redirección inicial. |
| Estado y asignación de estudiante | `ajax/estudiante.php`, `src/Model/Estudiante.php`, `src/Model/Usuario.php`, `form-doc/scripts/estudiante.js` | Creación, actualización de `tipo_est` y persistencia de permisos. |
| Módulos protegidos | `admin/inicio.php`, `form-doc/ver.curso.php`, `form-doc/calend.acad.php`, `form-doc/reglamento.php`, `form-doc/info.estudiante.php`, `form-doc/info.docente.php`, `form-doc/header.php` | Validaciones de sesión y navegación documentadas. |
| Datos y pruebas | Estructuras y datos de `login`, `permiso_login`, `estudiante`, `tipo_estudiante` y perfiles de profesor; pruebas futuras por matriz aprobada. | Validación de correspondencias, acumulación y no regresión. |

La fuente autorizada `AT-FEATURE003-001-INVENTARIO-AUTORIZACION-001.md` no está presente en el árbol actual. Los archivos anteriores se registran desde la evidencia citada en `AT-EPIC003-PERMISOS-FUNCIONALES-001.md`; deberán contrastarse con el inventario si se restituye antes de iniciar implementación.

## 9. Recomendación técnica

La resolución institucional habilita continuar con una **Feature de evolución incremental** y, después de su revisión técnica, con una **TASK específica y acotada** para la alternativa que se apruebe. La TASK no corresponde crearla en este documento.

No se identifica una necesidad inmediata de ADR adicional: ADR-002 ya fija la separación conceptual y deja abierta la estrategia de transición. Una ADR adicional solo sería necesaria si la futura decisión elige una nueva arquitectura persistente o una estrategia de coexistencia que exceda la evolución incremental y no pueda justificarse dentro de sus principios aprobados.

Antes de una TASK se recomienda validar: correspondencia técnica completa de estados; representación de Perfil, Reglamento, Calendario y Cursos frente a permisos/sesiones actuales; fuente y ciclo de vida de roles acumulativos; reglas de revocación para retirado, eliminado e inhabilitado; y consistencia entre controles de navegación y acceso directo.

## 10. Estado de EPIC-003

```text
Arquitectura:
✅ Consolidada

Resolución institucional:
✅ Aprobada

Implementación:
🟡 Pendiente análisis de evolución

TASK:
No creada
```

## 11. Hallazgos y límites de certeza

### Confirmados

- La autorización documentada se materializa desde `permiso_login` en sesiones PHP y los módulos consumen esas sesiones.
- La implementación soporta múltiples permisos por login.
- El estado de estudiante se persiste separadamente y su actualización documentada no modifica activamente `permiso_login`.
- La resolución aprobó matrices para estudiantes y profesores, además de acumulación para roles y participaciones autorizadas.

### Hipótesis o validaciones pendientes

- La equivalencia exacta entre cada identificador técnico de `tipo_estudiante` y el catálogo institucional completo.
- El mapeo suficiente entre las capacidades aprobadas y las claves de sesión existentes.
- La forma de persistir o derivar permisos sin convertir el estado o el rol en permiso funcional.
- El comportamiento técnico actual de estados de profesor, Comité y administración fuera de la evidencia documental disponible.

## 12. Fuentes utilizadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md](../governance/ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md)
- [ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md](../governance/ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md)
- [ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.md](../governance/ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.md)
- AT-FEATURE003-001-INVENTARIO-AUTORIZACION-001.md (referenciada por las fuentes, no presente en el árbol actual).
- [AT-EPIC003-PERMISOS-FUNCIONALES-001.md](AT-EPIC003-PERMISOS-FUNCIONALES-001.md)

## 13. Cierre

Este análisis no crea TASK ni implementa cambios. El siguiente paso aplicable es la revisión técnica de este documento.
