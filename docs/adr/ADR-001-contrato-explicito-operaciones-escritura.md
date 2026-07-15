# ADR-001 — Contrato explícito para operaciones de escritura

**Estado:** Aprobada  
**Clasificación:** [ARQ] Arquitectura  
**Feature asociada:** FEATURE-001 — Evolución de los Contratos de Persistencia  
**EPIC asociada:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia  
**Ubicación oficial propuesta:** `docs/adr/ADR-001-contrato-explicito-operaciones-escritura.md`

## Contexto documental

Este documento constituye la formalización documental vigente de ADR-001. No se presenta como reproducción literal de un documento histórico anterior.

## Contexto

La capa de persistencia heredada utiliza contratos genéricos que pueden ser consumidos tanto por operaciones de lectura como de escritura.

Esta situación dificulta identificar inequívocamente:

- la intención de cada operación;
- el criterio de éxito;
- las filas afectadas;
- el identificador generado, cuando corresponda;
- el comportamiento esperado ante errores.

FEATURE-001 establece que la persistencia debe evolucionar mediante contratos explícitos, manteniendo coexistencia y compatibilidad con los contratos heredados.

## Problema

Utilizar un contrato genérico para operaciones de escritura:

- oculta la semántica real de la operación;
- dificulta verificar el resultado;
- condiciona futuras capacidades transaccionales;
- aumenta el riesgo de interpretar incorrectamente el valor retornado;
- impide migrar consumidores de forma trazable y controlada.

No corresponde sustituir globalmente los contratos heredados ni migrar todos sus consumidores en un único incremento.

## Decisión

Incorporar un contrato explícito para operaciones de escritura mediante:

```php
ejecutarEscritura()
```

El nuevo contrato deberá utilizarse progresivamente en consumidores confirmados de escritura.

Su incorporación no modifica ni elimina los contratos heredados.

Cada consumidor deberá migrarse mediante un Análisis Técnico y una Task independiente.

## Contrato esperado

`ejecutarEscritura()` debe:

- representar exclusivamente operaciones de escritura;
- ejecutar la sentencia mediante el mecanismo de persistencia vigente;
- permitir determinar inequívocamente si la operación fue ejecutada;
- entregar información explícita sobre el resultado;
- preservar el manejo de errores definido por la capa de persistencia;
- no alterar automáticamente SQL, parámetros ni reglas de negocio.

La estructura de resultado deberá mantener una semántica explícita equivalente a:

```php
[
    'filasAfectadas' => $filasAfectadas,
    'idInsertado' => $idInsertado,
]
```

`idInsertado` podrá ser `null` cuando la operación no produzca o no requiera un identificador generado.

## Compatibilidad

Los contratos heredados permanecerán disponibles mientras existan consumidores vigentes.

La existencia de `ejecutarEscritura()` no autoriza:

- modificar globalmente los consumidores existentes;
- cambiar contratos de lectura;
- eliminar helpers heredados;
- alterar endpoints;
- modificar respuestas observables;
- migrar consumidores sin confirmar su uso real;
- incorporar transacciones automáticamente.

## Estrategia de adopción

La migración deberá realizarse consumidor por consumidor.

Cada migración deberá verificar:

- que la operación sea efectivamente de escritura;
- que exista un consumidor activo;
- que el comportamiento observable pueda preservarse;
- que el cambio sea mínimo;
- que exista una reversión exacta;
- que la operación pueda validarse técnica y funcionalmente.

## Consecuencias positivas

- Semántica explícita para operaciones de escritura.
- Mayor trazabilidad entre intención y resultado.
- Validaciones más precisas.
- Menor dependencia de contratos ambiguos.
- Base para futuras capacidades de atomicidad.
- Migración incremental sin reescritura completa.
- Preservación de compatibilidad con consumidores heredados.

## Consecuencias y riesgos

- Coexistencia temporal de contratos heredados y nuevos.
- Necesidad de inventariar consumidores antes de migrarlos.
- Riesgo de migrar métodos sin consumidores activos.
- Riesgo de alterar el contrato observable si se interpreta incorrectamente el nuevo resultado.
- Necesidad de mantener Tasks pequeñas y separadas.
- Posible duplicación temporal hasta completar futuras migraciones autorizadas.

## Elementos fuera de alcance

Esta ADR no autoriza:

- transacciones;
- Unit of Work;
- cambios de esquema;
- modificación del modelo conceptual;
- reemplazo de PDO;
- incorporación de ORM;
- parametrización masiva de SQL;
- refactorización general de modelos;
- migración global de operaciones de escritura;
- eliminación de contratos heredados.

## Criterios de aplicación

Un consumidor podrá migrarse a `ejecutarEscritura()` únicamente cuando:

- su operación de escritura esté confirmada;
- su flujo consumidor esté identificado;
- exista evidencia de compatibilidad;
- la transformación exacta esté definida;
- la Task incluya reversión y condiciones de detención;
- la validación funcional pueda asociarse al consumidor migrado.

## Gobierno documental

- Este archivo será la fuente oficial de ADR-001.
- FEATURE-001 mantendrá la estrategia general.
- Esta ADR registrará exclusivamente la decisión arquitectónica.
- Tasks, commits y avances de implementación se mantendrán en registros separados.
- Cualquier cambio al contrato requerirá una nueva decisión arquitectónica o una modificación formalmente aprobada de esta ADR.
