# FEATURE-001 — Evolución de los Contratos de Persistencia

**EPIC:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia  
**Estado:** Aprobada — implementación incremental en progreso  
**Clasificación:** [ARQ] Arquitectura / [GOV] Gobierno documental  
**Ubicación oficial:** `docs/features/EPIC-008_FEATURE-001.md`

## Contexto documental

Este documento constituye la formalización documental vigente de FEATURE-001. No se presenta como reproducción literal de un documento histórico anterior.

## Objetivo

Establecer una estrategia permanente para evolucionar la capa de persistencia mediante contratos explícitos, de forma incremental, compatible, verificable y reversible, sin sustituir anticipadamente los contratos heredados.

## Alcance

- Incorporación progresiva de contratos explícitos.
- Coexistencia con contratos heredados vigentes.
- Migración consumidor por consumidor.
- Definición inequívoca de éxito, error y resultado.
- Preservación del comportamiento observable.
- Validación técnica y funcional de cada incremento.
- Reversión independiente de cada migración.
- Base para futuras capacidades, incluida la atomicidad cuando exista una decisión específica.

## Exclusiones

- Sustitución global de contratos heredados.
- Migración masiva de consumidores.
- Cambios automáticos de SQL, endpoints o reglas de negocio.
- Selección anticipada de ORM o framework.
- Rediseño del modelo conceptual o físico.
- Migraciones destructivas.
- Incorporación de transacciones sin ADR específica.
- Registro de Tasks y commits dentro de este documento.

## Principios

1. **Coexistencia incremental:** los contratos nuevos conviven con los heredados.
2. **Compatibilidad:** cada migración preserva el comportamiento existente.
3. **Semántica explícita:** éxito, error y resultado deben ser verificables.
4. **Migración por consumidor:** cada adopción requiere AT y Task independiente.
5. **Reversibilidad:** cada cambio debe poder revertirse aisladamente.
6. **Trazabilidad:** toda migración debe quedar asociada a decisión, validación y commit.
7. **Cambio mínimo:** no se autorizan modificaciones adicionales no relacionadas.

## Relación con EPIC-008

FEATURE-001 desarrolla el gobierno incremental de los contratos de persistencia dentro de EPIC-008, sin modificar su objetivo, alcance ni exclusiones.

## Criterios de aceptación

- La coexistencia de contratos queda formalmente establecida.
- Los contratos heredados se preservan mientras permanezcan vigentes.
- Los nuevos contratos poseen semántica explícita.
- Las migraciones se ejecutan mediante incrementos pequeños y reversibles.
- Existe al menos un contrato explícito incorporado.
- Existe al menos un consumidor activo migrado y validado.
- No se requiere una sustitución completa de la persistencia.
- La Feature permanece independiente de tecnologías específicas.

## Gobierno documental

- Este archivo será la fuente oficial de FEATURE-001.
- El Roadmap mantendrá exclusivamente la planificación de EPIC-008.
- ADR-001 se documentará de forma independiente.
- El seguimiento de Tasks y commits se mantendrá fuera de esta Feature.
- Cualquier modificación futura requerirá una decisión aprobada.
