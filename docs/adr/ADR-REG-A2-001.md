# ADR-REG-A2-001: Contrato objetivo de configuración por entorno

## Estado

APROBADO.

## Contexto

La aplicación actualmente posee múltiples mecanismos de configuración:

```text
.env
    ↓
src/Config/env.php

global.php
    ↓
constantes globales

composer.json
    ↓
autoload.files

header.php
    ↓
carga heredada
```

El análisis `AT-REG-A2-001` determinó que existen dos rutas de inicialización y ausencia de un contrato único de configuración.

Además, existen valores contradictorios de configuración histórica, particularmente:

```text
DB_NAME=db_antr_doc
```

y:

```text
DB_NAME=c1441353_antr_db
```

No existe evidencia suficiente para asociar esos valores a ambientes concretos.

## Decisión

### 1. Precedencia de configuración

La aplicación adoptará como contrato objetivo:

```text
variables externas del proceso/servidor
        ↓
.env local
        ↓
defaults explícitamente permitidos
```

Una variable externa definida tiene prioridad. Se considera suministrada incluso cuando su valor sea una cadena vacía:

```text
variable externa definida
        ↓
mantiene prioridad
        ↓
validación
```

Una variable externa vacía debe ser validada como tal. No podrá ser reemplazada silenciosamente mediante `.env` o un default.

Las fuentes externas comprenden:

- el entorno del proceso;
- la configuración del servidor;
- los mecanismos equivalentes disponibles y soportados por PHP.

La implementación deberá resolver explícitamente las diferencias de disponibilidad, precedencia y representación entre `getenv()`, `$_ENV` y `$_SERVER`. No podrá asumir que contienen siempre los mismos valores.

Los defaults solo podrán existir cuando sean no sensibles y seguros para el ambiente activo. Que un valor no sea sensible no basta para autorizar un default. Los parámetros `DB_*` no utilizarán defaults en producción.

### 2. Configuración como responsabilidad única

La resolución de configuración debe evolucionar hacia un único punto de inicialización:

```text
bootstrap de aplicación
        ├── carga del autoload de Composer
        ├── resolución de configuración
        ├── validación del contrato
        └── disponibilidad para consumidores
```

La resolución de configuración deberá ser:

- única: todos los consumidores obtienen la configuración desde la misma autoridad;
- idempotente: invocaciones repetidas no alteran el resultado ni repiten efectos;
- inmutable durante una ejecución: una vez resuelta y validada, la configuración no puede cambiar durante esa ejecución.

### 3. Autoload y bootstrap

El autoload de Composer y la inicialización de configuración son responsabilidades diferentes.

- Composer carga dependencias y clases.
- El bootstrap de aplicación controla la resolución y validación de configuración.
- Composer no será considerado por sí mismo un sistema de configuración.
- Los efectos globales críticos de inicialización no dependerán implícitamente de `autoload.files`.

El bootstrap podrá cargar el autoload de Composer como parte de su secuencia explícita, sin delegarle la autoridad sobre la configuración.

### 4. Compatibilidad heredada

Durante la migración:

- las constantes globales existentes podrán mantenerse como adaptador temporal;
- `global.php` no será eliminado inmediatamente;
- los consumidores heredados podrán continuar funcionando mediante compatibilidad controlada;
- la eliminación de constantes requerirá un inventario completo de consumidores.

### 5. Validación y errores de configuración

El contrato deberá diferenciar:

- variable ausente;
- variable vacía;
- variable inválida.

Las variables obligatorias deberán producir un error explícito cuando no exista configuración válida. No se permitirá continuar silenciosamente con configuración incompleta.

La política adicional será:

- un archivo `.env` inexistente estará permitido si el contrato completo se satisface mediante fuentes externas;
- un archivo `.env` existente pero ilegible deberá generar un error explícito;
- un archivo `.env` inválido deberá generar un error explícito;
- un ambiente desconocido deberá generar un error y no será interpretado ni tratado implícitamente como producción.

Los errores deberán identificar la causa de configuración sin revelar secretos ni valores sensibles.

## Decisiones fuera de alcance

Este ADR no decide:

- el nombre real de las bases por ambiente;
- credenciales;
- valores productivos;
- infraestructura de hosting;
- la política concreta de secretos externos.

Esas decisiones requieren autoridad operacional.

## Consecuencias positivas

- Configuración reproducible.
- Separación entre código y ambiente.
- Menor riesgo de publicar secretos.
- Menor dependencia del estado local.
- Base para una migración incremental.
- Fallos de configuración detectados antes de utilizar servicios dependientes.

## Consecuencias negativas

- Será necesario migrar consumidores heredados.
- Existirá una etapa temporal con adaptadores.
- Se requerirán pruebas adicionales del bootstrap.
- La configuración incorrecta fallará más temprano.
- La resolución explícita de las fuentes soportadas por PHP agregará complejidad al mecanismo genérico.

## Riesgos

- Aplicar la nueva precedencia sin validar despliegues existentes.
- Retirar constantes antes de migrar consumidores.
- Confundir `.env.example` con configuración productiva.
- Mantener defaults sensibles o inseguros para el ambiente activo.
- Interpretar una variable externa vacía como ausente y aplicar un fallback silencioso.
- Mantener efectos globales críticos acoplados implícitamente a `autoload.files`.

## Requisitos previos para implementación y activación

Antes de crear `TASK-REG-A2-IMPL` deberá existir:

1. Aprobación de este ADR.
2. Criterios de aceptación para construir y validar el mecanismo genérico.
3. Plan de migración reversible.

La construcción y validación técnica del mecanismo genérico podrá utilizar valores controlados y ficticios. No requerirá conocer los valores reales de infraestructura.

Antes del despliegue o activación del contrato en cada ambiente deberá existir:

1. Confirmación operacional del ambiente.
2. Confirmación de los nombres reales de las bases correspondientes.
3. Suministro autorizado de los demás valores reales requeridos.
4. Validación del mecanismo de configuración disponible en ese ambiente.

Este ADR no autoriza el registro de esos valores en el repositorio.
