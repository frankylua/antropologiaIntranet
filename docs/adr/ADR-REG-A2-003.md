# ADR-REG-A2-003: Ubicación física y estrategia inicial del bootstrap de aplicación

## Estado

APROBADO.

## Contexto

Los ADR:

```text
ADR-REG-A2-001
Contrato objetivo de configuración por entorno

ADR-REG-A2-002
Bootstrap objetivo de aplicación y transición de configuración
```

establecieron:

- un contrato centralizado de configuración;
- la existencia de un bootstrap único;
- la separación entre Composer y la inicialización de aplicación;
- la compatibilidad temporal con la configuración heredada.

Sin embargo, quedó pendiente definir:

- la ubicación física del bootstrap;
- la primera estrategia de adopción;
- las entradas iniciales afectadas;
- la estrategia de reversión.

## Problema

La aplicación heredada no posee actualmente un punto único de inicialización.

Existen mecanismos distribuidos:

```text
vendor/autoload.php
        ↓
autoload.files
        ↓
env.php
global.php
conexion.php
```

y entradas que cargan configuración mediante rutas diferentes.

Seleccionar una ubicación de bootstrap durante la implementación introduciría una decisión arquitectónica no aprobada. Mantener simultáneamente la carga implícita de configuración mediante Composer y el adaptador heredado también podría producir ciclos de inicialización.

## Decisión

### 1. Ubicación física

El bootstrap de aplicación se ubicará en:

```text
src/bootstrap/app.php
```

Esta ubicación:

- está fuera de directorios de presentación;
- no pertenece a módulos funcionales;
- mantiene infraestructura técnica bajo `src/`;
- separa el bootstrap de las clases de dominio;
- permite evolución incremental;
- es accesible desde las entradas soportadas y desde el adaptador heredado.

El bootstrap no deberá ubicarse en `form-doc/` ni depender de archivos de presentación.

### 2. Responsabilidad del bootstrap

El flujo objetivo será:

```text
entrada
    ↓
bootstrap
    ↓
autoload de Composer
    ↓
resolución de configuración
    ↓
validación del contrato
```

El bootstrap deberá ser:

- único;
- idempotente;
- seguro ante invocaciones repetidas.

No será responsable de:

- autenticación;
- inicio de sesión;
- modificación del estado de usuario;
- renderizado;
- lógica de negocio;
- apertura automática de conexiones PDO;
- comprobación automática de conectividad.

### 3. Relación con Composer

Composer mantendrá exclusivamente `vendor/autoload.php` como mecanismo de:

- carga de dependencias;
- autoload de clases.

Como estado objetivo, la configuración crítica no dependerá de ejecución implícita mediante `autoload.files`.

La primera implementación podrá requerir retirar progresivamente de `autoload.files` la inicialización crítica asociada a:

```text
env.php
global.php
conexion.php
```

Este ADR autoriza esa dirección arquitectónica, pero no define la modificación concreta de `composer.json`. Esa modificación pertenecerá a una TASK de implementación posterior con alcance explícito.

### 4. Evitación de ciclos

No deberá existir este flujo:

```text
bootstrap
    ↓
Composer
    ↓
global.php
    ↓
bootstrap
```

El bootstrap no dependerá de `global.php` ni de otro adaptador heredado. La estrategia de implementación deberá ordenar los cambios de Composer y del adaptador de forma que ningún consumidor observe un bootstrap parcialmente inicializado.

### 5. Estrategia heredada

Durante la transición, las entradas heredadas seguirán temporalmente esta ruta:

```text
entrada heredada
        ↓
global.php (adaptador temporal)
        ↓
bootstrap
```

Las entradas migradas seguirán esta ruta:

```text
entrada migrada
        ↓
bootstrap
```

`global.php` conservará temporalmente las constantes requeridas por consumidores heredados. Su adaptación no autoriza su eliminación ni la migración masiva de consumidores.

### 6. Primera matriz de adopción

La primera fase considerará:

| Entrada | Tipo | Primera fase |
| --- | --- | --- |
| `ajax/*.php` que cargan Composer | AJAX | Sí |
| Páginas que cargan Composer directamente | HTTP | Sí |
| Páginas que llegan mediante `header.php` y `global.php` | HTTP heredada | Sí, mediante adaptador |
| `index.php` | HTTP | No |
| Login y cierre de sesión | HTTP | No |
| `fetchapi/*` | API pendiente de clasificación | No |
| Scripts raíz sin includes confirmados | Heredada no confirmada | No |
| Plantillas y fragmentos | Auxiliar | No |
| CLI | Sin evidencia actual | No |

“Incluida” significa que la entrada deberá alcanzar el bootstrap. No convierte plantillas ni fragmentos en entradas autónomas.

Las entradas excluidas no deberán modificarse ni recibir inicialización global automáticamente durante la primera fase.

### 7. Reversión

Los archivos potencialmente afectados por la primera implementación son:

```text
src/bootstrap/app.php
src/Config/*
src/Config/global.php
composer.json
```

`composer.lock` solo será afectado si existe un cambio real de dependencias. La modificación de la configuración de autoload, por sí sola, no justifica alterar el archivo de bloqueo.

El proceso de reversión será:

1. Restaurar la configuración anterior de Composer.
2. Retirar las invocaciones al bootstrap introducidas por la implementación.
3. Restaurar el comportamiento anterior del adaptador heredado.
4. Regenerar el autoload de Composer.
5. Ejecutar las validaciones heredadas definidas para las entradas incluidas.

La TASK de implementación deberá identificar exactamente cuáles de estos archivos modifica y asociar cada cambio con su paso inverso.

## Alternativas evaluadas

### Mantener `global.php` como bootstrap definitivo

Rechazada porque mezcla responsabilidades, conserva el acoplamiento heredado y dificulta la evolución independiente de la configuración.

### Crear un bootstrap independiente en `src/bootstrap/app.php`

Aceptada porque separa la infraestructura de inicialización, permite adopción incremental y ofrece una ruta estable para entradas nuevas y heredadas.

### Usar Composer como bootstrap

Rechazada porque Composer gobierna el autoload de dependencias y clases, no la inicialización crítica de la aplicación.

## Consecuencias positivas

- Punto único y físicamente identificado para evolucionar la inicialización.
- Menor acoplamiento histórico.
- Configuración desacoplada de presentación.
- Migración incremental y reversible.
- Matriz inicial delimitada.
- Prohibición explícita de ciclos entre Composer y el adaptador.

## Consecuencias negativas

- Coexistencia temporal entre rutas antiguas y nuevas.
- Necesidad de migrar entradas progresivamente.
- La primera fase puede requerir cambios coordinados en Composer y configuración heredada.
- Será necesario regenerar y validar el autoload durante implementación y reversión.

## Riesgos

- Ejecutar `global.php` desde `autoload.files` mientras este intenta cargar el bootstrap.
- Dejar consumidores con configuración parcialmente inicializada.
- Ampliar la primera fase hacia entradas no clasificadas.
- Confundir fragmentos con entradas autónomas.
- Alterar `composer.lock` sin un cambio real de dependencias.
- Retirar compatibilidad antes de completar el inventario de constantes heredadas.

## Fuera de alcance

Este ADR no decide:

- la implementación interna del bootstrap;
- la creación del archivo físico;
- la modificación concreta de `composer.json`;
- la migración completa de consumidores;
- la eliminación inmediata de `global.php`;
- cambios de PDO;
- valores reales de configuración o infraestructura.

## Requisitos previos para implementación

Antes de reanudar `TASK-REG-A2-IMPL` deberá existir:

1. Aprobación de este ADR.
2. Alcance explícito que autorice los cambios concretos necesarios en Composer.
3. Plan ordenado para evitar ciclos durante la transición.
4. Criterios de aceptación para todas las entradas incluidas en la primera matriz.
5. Correspondencia entre cada archivo modificado y su paso de reversión.
