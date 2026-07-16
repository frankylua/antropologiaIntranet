# AT-REG-A3-002: Definición técnica de la autoridad interna de conexión PDO

## Estado

APROBADO.

## Propósito

Este documento materializa la definición técnica utilizada para cerrar las decisiones internas requeridas por `TASK-REG-A3-IMPL` y permitir su ejecución sin delegar decisiones arquitectónicas nuevas.

La trazabilidad aplicable es:

```text
ADR-REG-A3-001
        ↓
AT-REG-A3-002
        ↓
TASK-REG-A3-IMPL
        ↓
implementación ConnectionAuthority
```

Este documento complementa `AT-REG-A3-001` y concreta el contrato arquitectónico aprobado en `docs/adr/ADR-REG-A3-001.md`.

## Definición técnica aprobada

### Componente

```text
ConnectionAuthority
```

### Namespace

```text
App\Config
```

### Archivo

```text
src/Config/ConnectionAuthority.php
```

## Responsabilidad

`ConnectionAuthority` será responsable únicamente de:

- crear PDO bajo demanda;
- entregar la instancia PDO;
- reutilizar la misma instancia durante una ejecución PHP individual;
- consumir la configuración validada existente.

## No responsabilidades

`ConnectionAuthority` no deberá:

- iniciar ni cargar el bootstrap;
- cargar o resolver configuración;
- ejecutar consultas SQL;
- manejar HTTP ni emitir respuestas;
- manejar sesiones;
- manejar autenticación;
- modificar reglas de negocio.

## Dirección de dependencias

La dirección válida es:

```text
bootstrap
    ↓
Configuration
    ↓
ConnectionAuthority
    ↓
PDO
```

No es válida la dependencia inversa:

```text
ConnectionAuthority
    ↓
bootstrap
```

No deberá existir una dependencia circular entre bootstrap, configuración y conexión.

## Integración heredada

`src/Config/conexion.php` mantendrá temporalmente:

```php
conexion(): PDO
```

como adaptador heredado hacia `App\Config\ConnectionAuthority`.

La firma pública no cambiará y los consumidores existentes continuarán utilizando `conexion(): PDO` durante la transición.

## Lifecycle aprobado

La instancia PDO será única solamente durante el ciclo de vida de una ejecución PHP individual, ya sea una solicitud HTTP, una ejecución AJAX o una ejecución CLI, si existiera.

Este lifecycle no implica:

- conexiones persistentes;
- conexiones compartidas entre requests;
- conexiones compartidas entre procesos;
- pools externos;
- almacenamiento permanente de conexiones.

La creación continuará siendo bajo demanda. La carga del bootstrap no abrirá automáticamente una conexión PDO.

## Alcance y límites

Esta definición no autoriza:

- migrar consumidores;
- eliminar helpers heredados;
- crear DAO;
- crear Repository;
- modificar consultas SQL;
- modificar el modelo de datos;
- modificar tablas;
- introducir cambios funcionales;
- modificar Composer;
- incorporar conexiones persistentes o pooling;
- ampliar las responsabilidades de `ConnectionAuthority`.

Una necesidad distinta de esta definición requerirá revisión arquitectónica.

## Reversibilidad documental

La reversión de esta incorporación documental consiste exclusivamente en retirar `docs/architecture/AT-REG-A3-002.md`.

No requiere revertir código, configuración ni ADR aprobados.
