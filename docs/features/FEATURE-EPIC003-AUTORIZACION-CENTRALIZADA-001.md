# FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001

## Centralización incremental del modelo de autorización

## 1. Identificación

- **Clasificación:** [ARQ] Evolución de arquitectura de autorización; [TEC] Feature técnica incremental; [GOV] Derivada de EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Estado:** Propuesta.
- **Pendiente:** aprobación de Dirección Técnica y creación de la TASK piloto.

## 2. Objetivo

Registrar la evolución incremental que incorporará un punto central de evaluación de autorización, manteniendo inicialmente la fuente y el comportamiento observable de permisos existentes.

```text
Actual

Módulo
  ↓
$_SESSION
  ↓
validación distribuida
```

```text
Evolución

Módulo
  ↓
Authorization
  ↓
fuente actual de permisos
```

La Feature preserva inicialmente el `login` actual, `permiso_login`, las sesiones existentes y el comportamiento observable previamente validado.

## 3. Principios arquitectónicos

La Feature mantiene las separaciones aprobadas en ADR-002:

```text
Estado académico
  ≠
Rol institucional
  ≠
Participación
  ≠
Permiso funcional
```

La centralización permitirá una evolución posterior hacia el siguiente modelo, sin implementarlo en esta Feature:

```text
Estado / Rol / Participación
  ↓
Reglas institucionales
  ↓
Permisos efectivos
```

En la presente etapa, `Authorization` centralizará el consumo de autorizaciones vigentes; no calculará ni persistirá permisos desde estados, roles o participaciones.

## 4. Alcance

### Incluye

- Evolución incremental de `Authorization` como punto común de consulta de autorización.
- Centralización progresiva de las consultas de autorización de los módulos.
- Migración controlada y gradual de módulos que hoy consumen directamente sesión.
- Un primer piloto acotado en el módulo Reglamento.
- Preservación de compatibilidad con permisos, sesiones y comportamiento actuales.

### No incluye

- Reemplazar `permiso_login`.
- Crear un nuevo modelo de permisos o una nueva fuente de permisos.
- Migrar todos los módulos en una única etapa.
- Modificar estados académicos.
- Automatizar estado → permiso.
- Rediseñar el inicio de sesión.
- Implementar código, SQL o cambios de datos en esta Feature documental.

## 5. Fases propuestas

### Fase 1 — Centralizar el punto de consulta de autorización

Definir el punto común de evaluación en `Authorization`, compatible con las claves de sesión actuales. Esta fase no modifica la procedencia de los permisos ni reconstruye permisos.

### Fase 2 — Piloto: módulo Reglamento

Aplicar la consulta centralizada al módulo Reglamento mediante una TASK específica posterior.

Características del piloto:

- Solo lectura.
- Bajo riesgo.
- Reversible.
- Orientado a comprobar equivalencia con el control de acceso actual.

### Fase 3 — Migración progresiva de otros módulos

Extender el patrón gradualmente a los demás módulos protegidos, con validación funcional por cada alcance. Cada migración requiere una TASK independiente; no se autoriza una migración masiva mediante esta Feature.

## 6. Riesgos y mitigación

| Riesgo | Mitigación |
| --- | --- |
| Cambio accidental de comportamiento autorizado | Cambios pequeños, equivalencia explícita con controles existentes y validación funcional. |
| Pérdida de compatibilidad con sesiones o permisos vigentes | Mantener inicialmente `login`, `permiso_login` y sesiones existentes como fuente operativa. |
| Mezcla entre reglas académicas y autorización técnica | Mantener la separación estado/rol/participación/permiso funcional y no derivar permisos en esta etapa. |
| Migración demasiado amplia | Piloto reversible y TASK independiente por cada módulo. |
| Divergencia temporal entre módulos migrados y controles directos | Inventario, pruebas de regresión y trazabilidad de cada migración. |

## 7. Dependencias

```text
EPIC-003
  ↓
ADR-002
  ↓
Resolución reglas permisos
  ↓
AT-EPIC003-IMPLEMENTACION-REGLAS-PERMISOS-001
  ↓
AT-EPIC003-AUTORIZACION-CENTRALIZACION-001
  ↓
FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001
```

La resolución institucional define las reglas funcionales aprobadas, pero no autoriza su implementación automática. Esta Feature tampoco modifica esa restricción.

## 8. Criterios de aceptación

- Existe una capa central de autorización con responsabilidad delimitada.
- Un módulo piloto puede consumirla mediante una TASK independiente.
- La migración del piloto no cambia el comportamiento actualmente autorizado.
- La evolución no elimina ni reemplaza `permiso_login`.
- El punto central permite una evolución posterior hacia permisos funcionales efectivos, sin confundirlos con estados, roles o participaciones.
- Cada migración posterior cuenta con alcance, validación funcional y reversibilidad definidos.

## 9. Restricciones

Esta Feature no autoriza modificar código, SQL, ADR, Roadmap, Manual Maestro ni actas existentes. No crea TASK, no implementa cambios y no requiere commit.

## 10. Fuentes

- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md](../governance/ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md)
- [AT-EPIC003-IMPLEMENTACION-REGLAS-PERMISOS-001.md](../architecture/AT-EPIC003-IMPLEMENTACION-REGLAS-PERMISOS-001.md)
- [AT-EPIC003-AUTORIZACION-CENTRALIZACION-001.md](../architecture/AT-EPIC003-AUTORIZACION-CENTRALIZACION-001.md)

## 11. Siguiente paso

Revisión documental de `FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md` antes de commit. Una vez aprobada, corresponderá evaluar la creación de una TASK piloto para Reglamento.
