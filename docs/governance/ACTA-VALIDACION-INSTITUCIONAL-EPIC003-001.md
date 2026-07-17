# ACTA-VALIDACION-INSTITUCIONAL-EPIC003-001

## 1. Identificación

- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **ADR relacionado:** [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md).
- **Propósito:** registrar la validación institucional requerida para separar las decisiones arquitectónicas aprobadas de las decisiones institucionales pendientes y de una eventual implementación técnica.
- **Alcance:** identidad, usuario institucional, login, estados académicos, roles, permisos, historial y auditoría relacionados con EPIC-003. No define reglas institucionales ni soluciones de implementación.

## 2. Estado de validación

```text
Pendiente de validación institucional formal.
```

## 3. Decisiones arquitectónicas confirmadas

Estas decisiones corresponden al ámbito arquitectónico y provienen de ADR-002.

- Persona ≠ Login.
- Rol institucional ≠ permiso funcional.
- Estado académico ≠ permiso funcional.
- Participación académica vinculada a objetos académicos.

Estas separaciones son conceptuales; ADR-002 no define su implementación física ni autoriza modificar el modelo actual.

El análisis del modelo actual identifica que `login` funciona como cuenta de acceso y que `permiso_login` registra asignaciones de permisos. Esta interpretación complementa la separación conceptual definida en ADR-002, sin constituir una nueva decisión arquitectónica.

## 4. Decisiones institucionales pendientes

### Identidad

- Significado institucional de Persona.
- Tratamiento de externos.
- Identificación mínima.
- Tratamiento de homónimos.
- Consolidación de identidad.

### Usuario institucional

- Cuándo se crea.
- Vigencia.
- Relación con Persona.

### Login

- Cuentas humanas.
- Cuentas técnicas.
- Responsables.

### Estados académicos

- Catálogo oficial.
- Significado.
- Transiciones.
- Responsables.
- Efectos.

### Roles

- Roles institucionales.
- Roles académicos.
- Vigencia.
- Asignación.

### Permisos

- Capacidades funcionales.
- Responsables.
- Autorización.

### Historial y auditoría

- Eventos obligatorios.
- Retención.
- Responsables.

## 5. Decisiones no resolubles por Dirección Técnica

Dirección Técnica no define:

- Políticas académicas.
- Significado oficial de estados.
- Responsables institucionales.
- Catálogo definitivo de roles.
- Reglas administrativas.
- Permisos institucionales.

## 6. Responsables propuestos para validación

Los siguientes ámbitos son participantes sugeridos para la validación; no constituyen autoridades formalmente aprobadas mediante esta acta.

| Área | Ámbito responsable sugerido |
| --- | --- |
| Identidad académica | Dirección Académica |
| Estados académicos | Dirección Académica / Secretaría |
| Roles institucionales | Dirección Académica / Administración |
| Permisos | Administración / responsable de acceso |
| Datos maestros | Gobierno de datos |
| Historial y auditoría | Secretaría / Gobierno de datos |

## 7. Impacto arquitectónico

```text
ADR-002 mantiene vigencia.

No requiere modificación.

No habilita implementación inmediata.
```

## 8. Impacto Roadmap

```text
ROADMAP.md no requiere modificación.

EPIC-003 mantiene prioridad y dependencias actuales.
```

## 9. Estado del EPIC

```text
Análisis conceptual:
Documentado mediante análisis arquitectónico y antecedentes previos asociados a EPIC-003.

Validación arquitectónica:
Finalizada

Validación institucional:
Pendiente

Implementación:
No iniciada
```

## 10. Próximo paso

```text
La implementación requiere:

- validación institucional formal;
- definición de Feature implementable;
- análisis técnico;
- TASK aprobada.
```
