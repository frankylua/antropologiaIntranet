# ADR-002 — Evolución del modelo de identidad y participación académica

## Estado

```text
Aprobado arquitectónicamente.

Pendiente de implementación.
```

## Contexto

El modelo actual mezcla identidad personal, cuenta de acceso, autenticación, permisos, roles institucionales, perfiles académicos, estados académicos y participaciones académicas.

Esta ADR registra la dirección arquitectónica aprobada para distinguir esas dimensiones conceptuales, sin definir soluciones físicas.

## Decisión arquitectónica

La dirección futura aprobada distingue los siguientes conceptos:

```text
Persona

- Usuario institucional

- Perfil académico

- Estado académico

- Rol institucional

- Participación académica

- Objeto académico específico
```

## Principios aprobados

### Separación identidad/acceso

```text
Persona ≠ Login
```

### Separación rol/permiso

```text
Rol institucional ≠ Permiso funcional
```

### Separación estado/autorización

```text
Estado académico ≠ Permiso
```

### Participación académica

```text
Persona

participa en

Objeto académico

mediante

Rol académico
```

## Objetos académicos

Los objetos núcleo son:

- Tesis
- Publicación
- Proyecto
- Congreso

No existe decisión de crear un objeto académico genérico. Cada objeto mantiene sus reglas propias.

## Dimensión temporal

Como principio futuro, se consideran:

```text
Estado actual

- Historial

- Vigencia

- Auditoría
```

Esta ADR no define su implementación.

## Restricciones

Esta ADR no autoriza:

- crear tabla Persona;
- crear tabla Participación;
- modificar el modelo actual;
- migrar datos;
- eliminar estructuras heredadas;
- reemplazar permisos actuales.

## Estrategia de evolución

La estrategia de transición queda pendiente.

Las alternativas futuras son:

- migración incremental;
- coexistencia temporal;
- compatibilidad controlada;
- migración estructural posterior.

Esta ADR no selecciona ninguna alternativa.

## Relación EPIC

Relación principal:

```text
EPIC-003 — Consolidación del modelo de datos
```

Relación secundaria:

```text
EPIC-008 — Gobierno del modelo de datos y persistencia
```

## Dependencias

Análisis técnicos utilizados:

- AT-ARQ-IDENTIDAD-PARTICIPACION-IMPACTO-001
- AT-ARQ-DOMINIO-OBJETOS-ACADEMICOS-001
- AT-ARQ-DOMINIO-CICLOS-VIDA-HISTORIAL-001
- AT-ARQ-SINTESIS-ADR002-001
- AT-ARQ-DOMINIO-REGLAS-INSTITUCIONALES-001

La decisión se encuentra validada por Dirección Técnica.
