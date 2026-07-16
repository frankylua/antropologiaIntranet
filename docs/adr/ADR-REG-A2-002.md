# ADR-REG-A2-002: Bootstrap objetivo de aplicación y transición de configuración

## Estado

APROBADO.

## Contexto

El análisis `AT-REG-A2-002` determinó que la aplicación no posee actualmente un bootstrap único.

Existen múltiples rutas de inicialización:

```text
Ruta A:
entrada
    ↓
vendor/autoload.php
    ↓
env.php
    ↓
global.php
    ↓
conexion.php

Ruta B:
entrada
    ↓
form-doc/header.php
    ↓
global.php
    ↓
env.php

Ruta C:
entrada pública
    ↓
sesión/renderizado
    ↓
sin configuración
```

Estas rutas generan una inicialización distribuida.

## Problema

Actualmente:

- Composer actúa parcialmente como punto de carga;
- `global.php` concentra configuración y efectos globales;
- `header.php` mezcla presentación con inicialización;
- algunos endpoints cargan configuración directamente;
- algunas entradas no inicializan configuración.

No existe un punto único autorizado para integrar el contrato definido en `ADR-REG-A2-001`.

## Decisión

Crear un bootstrap explícito de aplicación como punto único futuro de inicialización.

El flujo objetivo será:

```text
bootstrap de aplicación
        ↓
autoload de dependencias
        ↓
resolución de configuración
        ↓
validación del contrato
        ↓
disponibilidad de la configuración
        ↓
compatibilidad heredada temporal para consumidores
```

La ubicación física definitiva del bootstrap se decidirá antes de implementar. No deberá pertenecer a directorios de presentación como `form-doc/`.

## Responsabilidades y límites del bootstrap

El bootstrap deberá:

- cargar las dependencias necesarias;
- inicializar la configuración;
- validar el contrato;
- exponer la configuración resuelta;
- ser el único punto autorizado de inicialización crítica;
- ser idempotente y seguro ante invocaciones repetidas.

El bootstrap no deberá:

- contener lógica de negocio;
- iniciar renderizado HTML;
- manejar navegación;
- mezclar sesión con presentación;
- abrir conexiones PDO;
- comprobar conectividad de base de datos automáticamente;
- depender de `global.php`.

Cargar el bootstrap resolverá y validará configuración, pero la creación de conexiones permanecerá bajo demanda de los consumidores correspondientes.

## Relación con Composer

Composer mantiene la responsabilidad del autoload de clases y dependencias.

Composer no será responsable del bootstrap de aplicación ni de la inicialización crítica de configuración.

Este ADR no define cambios concretos sobre `composer.json`, pero establece como estado objetivo que la configuración crítica no dependa de ejecución implícita mediante `autoload.files`.

El bootstrap podrá cargar el autoload de Composer como parte explícita de su secuencia sin delegarle la autoridad de inicialización.

## Compatibilidad heredada

Durante la transición, `global.php` podrá actuar como adaptador temporal para los consumidores heredados. No será eliminado inmediatamente.

Las entradas migradas seguirán esta ruta:

```text
entrada migrada
        ↓
bootstrap único
```

Las entradas heredadas seguirán temporalmente esta ruta:

```text
entrada heredada
        ↓
global.php (adaptador temporal)
        ↓
bootstrap único
```

El bootstrap no dependerá de `global.php`. La transición deberá impedir ciclos de carga como:

```text
bootstrap
    ↓
global.php
    ↓
bootstrap
```

La migración será incremental:

```text
adaptación temporal de global.php
        ↓
migración progresiva de entradas y consumidores
        ↓
inventario y validación de dependencias restantes
        ↓
retiro progresivo del adaptador
```

## Tratamiento de `header.php`

`header.php` dejará de considerarse un punto de inicialización.

Su responsabilidad futura se limitará a:

- presentación;
- navegación;
- elementos visuales.

En las entradas soportadas que lo utilicen, la carga del bootstrap deberá ocurrir antes de cargar `header.php`.

## Política de sesiones

Este ADR no decide la política de inicio de sesión.

El bootstrap:

- no iniciará sesiones;
- no modificará estado de usuario;
- no asumirá responsabilidades de autenticación.

Cualquier excepción requerirá una decisión arquitectónica posterior.

## Clasificación de entradas

Antes de modificar comportamiento deberá establecerse una matriz de entradas soportadas y no soportadas. No se agregará configuración global automáticamente a archivos que no hayan sido clasificados.

| Tipo de entrada | Regla |
| --- | --- |
| HTTP | Debe alcanzar el bootstrap antes de consumir configuración o dependencias de aplicación. |
| AJAX | Debe alcanzar el bootstrap antes de consumir configuración o dependencias de aplicación. |
| CLI, si existe | Debe definirse explícitamente y registrar cómo alcanza el bootstrap. |
| Plantillas | No son entradas autónomas. |
| Fragmentos auxiliares | No inicializan configuración. |

Los archivos no soportados deberán identificarse antes de cambiar su comportamiento. La clasificación no convertirá artificialmente plantillas o fragmentos en entradas ejecutables autónomas.

## Consecuencias positivas

- Punto único de evolución.
- Configuración centralizada.
- Menor dependencia implícita.
- Facilita la migración incremental.
- Separa infraestructura de presentación.
- Evita que cargar configuración implique abrir conexiones.
- Permite verificar de forma uniforme las entradas soportadas.

## Consecuencias negativas

- Requiere migración progresiva.
- Existirá compatibilidad temporal.
- Aumenta inicialmente la complejidad.
- Los consumidores deberán inventariarse.
- Será necesario mantener y probar rutas heredadas y migradas durante la transición.

## Riesgos

- Crear una segunda autoridad de inicialización durante la transición.
- Introducir ciclos entre el bootstrap y `global.php`.
- Mantener efectos críticos implícitos en `autoload.files`.
- Migrar `header.php` antes de que sus entradas carguen el bootstrap.
- Convertir plantillas o fragmentos en entradas autónomas por error.
- Abrir conexiones o iniciar sesiones como efectos colaterales del bootstrap.
- Alterar entradas no clasificadas.

## Decisiones fuera de alcance

Este ADR no decide:

- la ubicación física definitiva del archivo bootstrap;
- la implementación del resolvedor;
- la modificación concreta de `composer.json`;
- la migración concreta de consumidores;
- la eliminación de `global.php`;
- cambios en PDO;
- la política de inicio de sesión;
- la clasificación definitiva de cada archivo existente.

## Requisitos previos para implementación

Antes de crear una TASK de implementación deberá existir:

1. Aprobación de este ADR.
2. Definición de la ubicación física del bootstrap, fuera de directorios de presentación.
3. Matriz de entradas afectadas que distinga entradas, plantillas y fragmentos.
4. Estrategia de migración para entradas heredadas y migradas.
5. Criterios de aceptación para idempotencia, ausencia de ciclos y carga única.
6. Plan de reversión que preserve la compatibilidad heredada.

La implementación deberá mantener la conexión PDO bajo demanda y no podrá incorporar decisiones de sesión no aprobadas.
