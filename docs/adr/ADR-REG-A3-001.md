# ADR-REG-A3-001: Contrato de conexión PDO y separación de persistencia

## Estado

PROPUESTO.

## Contexto

El análisis técnico `AT-REG-A3-001` confirmó que la aplicación posee una única construcción de PDO, ubicada en:

```text
src/Config/conexion.php
```

La conexión se obtiene bajo demanda mediante el contrato global:

```text
conexion(): PDO
```

La implementación conserva una instancia estática durante cada ejecución PHP. El mismo archivo también contiene:

- la construcción del DSN y las opciones PDO;
- helpers globales para ejecutar consultas;
- recuperación de resultados e identificadores insertados;
- un contrato inicial de escritura explícita;
- política de registro, respuesta HTTP y terminación ante errores de conexión en producción.

Los modelos existentes consumen los helpers globales heredados. No existen transacciones explícitas y algunos casos de uso ejecutan varias escrituras relacionadas sin una frontera atómica formalizada.

Los ADR `ADR-REG-A2-001`, `ADR-REG-A2-002` y `ADR-REG-A2-003`, junto con `TASK-REG-A2-IMPL`, establecieron un bootstrap único, configuración validada e inicialización explícita. La implementación actual mantiene PDO bajo demanda, pero presenta esta dependencia bidireccional:

```text
src/bootstrap/app.php
        ↓
src/Config/conexion.php

src/Config/conexion.php
        ↓
src/bootstrap/app.php
```

Aunque `require_once` evita la recursión efectiva, esta relación no establece una dirección arquitectónica única. Además, la capa de persistencia no posee un contrato uniforme de errores: un fallo al construir PDO puede terminar la ejecución y producir efectos HTTP, mientras que un fallo de consulta normalmente se propaga como excepción.

La evolución debe separar estas responsabilidades sin retirar contratos utilizados por consumidores existentes ni exigir una migración masiva.

## Decisión

### 1. Autoridad responsable de entregar PDO

Se adopta la **opción A**: mantener temporalmente el contrato público heredado:

```text
conexion(): PDO
```

como adaptador hacia una autoridad interna de conexión.

La autoridad interna futura será el único componente autorizado para:

- construir el DSN a partir de configuración ya resuelta y validada;
- establecer las opciones PDO aprobadas;
- crear la instancia PDO;
- conservar y entregar la instancia correspondiente a la ejecución activa;
- informar fallos de infraestructura mediante excepciones.

`conexion()` conservará temporalmente su firma y su resultado observable. No construirá una conexión alternativa: delegará en la misma autoridad interna.

No se introducirá inmediatamente una nueva abstracción pública porque obligaría a modificar consumidores antes de disponer de una migración individual, validada y reversible. La autoridad interna permite separar responsabilidades sin ampliar todavía la superficie pública ni decidir prematuramente una arquitectura DAO o Repository.

### 2. Dirección de dependencias

El estado objetivo será:

```text
entrada soportada
        ↓
bootstrap de aplicación
        ↓
configuración resuelta y validada
        ↓
consumidor de persistencia
        ↓
adaptador heredado o autoridad interna de conexión
        ↓
PDO
```

Las reglas de dependencia serán:

- el bootstrap inicializa y expone la configuración;
- el bootstrap no abre PDO ni comprueba conectividad;
- la autoridad de conexión consume configuración ya inicializada;
- la autoridad de conexión no carga ni reinicializa el bootstrap;
- configuración no depende de conexión ni de consultas;
- consultas y consumidores no resuelven configuración por su cuenta;
- ningún flujo podrá conservar el ciclo `bootstrap → conexión → bootstrap`.

La disponibilidad de las definiciones de persistencia podrá integrarse durante la transición, pero esa compatibilidad de carga no autoriza a invertir la dependencia conceptual ni a abrir PDO como efecto del bootstrap.

### 3. Lifecycle de PDO

Se mantendrá una única instancia PDO por ejecución PHP para la configuración activa.

El lifecycle autorizado será:

```text
primera solicitud de conexión
        ↓
creación bajo demanda por la autoridad interna
        ↓
reutilización durante la ejecución
        ↓
liberación al finalizar la ejecución PHP
```

Reglas:

- solo la autoridad interna controla la creación;
- los consumidores no crearán instancias PDO directamente;
- invocaciones repetidas obtendrán la misma instancia durante una ejecución;
- no se permitirán conexiones adicionales ad hoc para el mismo contrato de configuración;
- no se incorporará cierre manual obligatorio;
- no se definirá pooling, persistencia entre procesos ni administración externa de conexiones;
- una necesidad futura de múltiples conexiones, bases o credenciales requerirá una nueva decisión arquitectónica.

La conexión seguirá siendo bajo demanda: cargar el bootstrap o resolver configuración no debe establecer conectividad con la base de datos.

### 4. Contrato de errores

Se separarán tres categorías conceptuales:

```text
Configuración inválida
        ↓
error de configuración

Fallo al crear o mantener PDO
        ↓
error de infraestructura de conexión

Fallo al preparar o ejecutar una operación
        ↓
error de persistencia
```

El contrato objetivo utilizará excepciones diferenciadas por categoría:

- `ConfigurationException`, o un tipo semánticamente equivalente, para configuración ausente, vacía o inválida;
- `ConnectionException`, o un tipo semánticamente equivalente, para fallos al crear u obtener PDO;
- `PersistenceException`, o un tipo semánticamente equivalente, para fallos al preparar o ejecutar operaciones de persistencia.

Estos nombres establecen el contrato semántico; su namespace y jerarquía concreta se definirán en la Task correspondiente sin agregar dependencias externas. Las excepciones de conexión y persistencia conservarán la excepción técnica original como causa.

Durante la migración, los adaptadores heredados podrán continuar propagando `PDOException` cuando cambiar ese tipo rompa un consumidor confirmado. Esa excepción de compatibilidad no formará parte del contrato objetivo y deberá inventariarse antes de retirarse.

Además:

- no se utilizarán valores de retorno ambiguos para representar fallos que deban interrumpir la operación.

La persistencia no podrá:

- establecer códigos de respuesta HTTP;
- emitir JSON o HTML;
- finalizar la ejecución mediante `exit` o mecanismo equivalente;
- decidir mensajes para el usuario;
- registrar secretos, credenciales, DSN completos ni valores sensibles.

La captura y traducción de excepciones corresponderá al límite de entrada o a un manejador de aplicación autorizado. Este ADR no define el formato final de respuesta al usuario.

Durante la transición podrá mantenerse un adaptador de compatibilidad para el comportamiento observable existente, siempre que esté fuera de la autoridad interna y tenga retiro planificado.

### 5. Responsabilidad transaccional

Las transacciones pertenecerán al caso de uso que conoce la unidad completa de trabajo.

El flujo objetivo será:

```text
caso de uso
        ↓
solicita unidad transaccional sobre la conexión activa
        ↓
inicia transacción
        ↓
ejecuta todas las operaciones relacionadas
        ↓
confirma si todas tienen éxito

excepción
        ↓
revierte si la transacción continúa activa
        ↓
repropaga el error
```

Responsabilidades:

- el caso de uso o coordinador transaccional autorizado inicia la transacción;
- el mismo propietario confirma o revierte;
- los helpers y operaciones individuales no iniciarán transacciones implícitas;
- no existirá una transacción global para toda la solicitud;
- todas las operaciones de una unidad transaccional utilizarán la misma instancia entregada por la autoridad interna;
- ninguna operación participante podrá abrir una conexión paralela;
- las fronteras transaccionales se aprobarán caso por caso antes de implementarse.

Este ADR establece propiedad y reglas, pero no autoriza incorporar transacciones a un flujo funcional concreto.

### 6. Compatibilidad heredada

Mientras existan consumidores confirmados se mantendrán temporalmente:

```text
conexion()
ejecutarConsulta()
ejecutarConsultaResultados()
obtenerIdConsulta()
resultadoConsultaPorId()
ejecutarEscritura()
```

La coexistencia se regirá por estas condiciones:

- no se cambiará una firma heredada sin inventario de consumidores;
- no se modificará el resultado observable de un helper como efecto colateral de separar la conexión;
- cada consumidor se migrará mediante análisis y Task independientes;
- contratos nuevos y heredados usarán la misma autoridad y la misma PDO durante una ejecución;
- el retiro de un contrato requerirá evidencia de que no conserva consumidores soportados;
- la compatibilidad temporal no convierte los helpers globales en el contrato objetivo definitivo.

### 7. Estrategia incremental

#### Fase 1: formalización del contrato

Aprobar este ADR y registrar los criterios de aceptación de lifecycle, dependencia, errores y compatibilidad. No modificar comportamiento.

#### Fase 2: autoridad interna

Introducir la autoridad interna detrás de `conexion(): PDO`, preservando la creación bajo demanda, una instancia por ejecución, opciones PDO y consumidores existentes.

La Task deberá eliminar la dependencia circular sin abrir conexiones desde el bootstrap.

#### Fase 3: separación de política HTTP

Retirar de la autoridad interna los efectos HTTP y la terminación del proceso. Incorporar la traducción compatible en el límite de entrada o adaptador autorizado antes de retirar el comportamiento heredado.

#### Fase 4: migración gradual de helpers

Migrar consumidores individualmente hacia contratos explícitos de persistencia. Cada migración deberá preservar SQL, reglas de negocio y respuesta observable, salvo autorización independiente.

Las unidades transaccionales solo podrán incorporarse mediante análisis específico del caso de uso.

#### Fase 5: retiro de contratos heredados

Retirar `conexion()` como adaptador y cada helper global únicamente cuando el inventario confirme que no existen consumidores soportados. El retiro requerirá validación y reversión propias.

## Consecuencias positivas

- Una sola autoridad para construir y entregar PDO.
- Dirección de dependencias explícita y sin ciclos.
- Conservación del comportamiento bajo demanda.
- Separación entre configuración, infraestructura, persistencia y transporte HTTP.
- Base para transacciones delimitadas por casos de uso.
- Migración progresiva sin reescritura masiva.
- Compatibilidad temporal para modelos y endpoints existentes.
- Reversión posible por fase y por consumidor.

## Consecuencias negativas

- Coexistirán temporalmente contratos heredados y nuevos.
- La autoridad interna agregará una capa de delegación durante la transición.
- Será necesario mantener pruebas para ambos caminos mientras dure la migración.
- Separar la política HTTP puede cambiar la forma en que emergen errores si no se incorpora primero una traducción compatible.
- La deuda de funciones globales no desaparecerá en la primera implementación.
- Las transacciones requerirán análisis individual de fronteras funcionales.

## Riesgos

- Romper consumidores heredados al cambiar firmas, tipos o resultados.
- Introducir una segunda instancia PDO y perder la unidad de conexión.
- Cambiar la semántica observable de errores antes de adaptar los límites de entrada.
- Mantener accidentalmente el ciclo entre bootstrap y conexión.
- Crear transacciones parciales o anidadas sin una política aprobada.
- Confirmar o revertir desde componentes distintos y dejar incierto el estado transaccional.
- Conservar dependencias ocultas mediante funciones globales por más tiempo del previsto.
- Retirar compatibilidad basándose en búsquedas incompletas de consumidores.
- Registrar información sensible al normalizar errores de infraestructura.
- Ampliar una Task técnica hacia cambios funcionales, SQL o modelo de datos no autorizados.

## Alternativas consideradas

### Introducir inmediatamente una nueva abstracción pública

Rechazada para la primera fase. Haría visible un contrato todavía no adoptado y obligaría a modificar consumidores o mantener dos autoridades públicas. Podrá reconsiderarse cuando exista evidencia suficiente de los contratos requeridos por consumidores migrados.

### Mantener `conexion()` como autoridad definitiva

Rechazada como estado objetivo. Preservaría una función global como autoridad arquitectónica y dificultaría separar lifecycle, errores y pruebas. Se acepta solo como adaptador temporal.

### Permitir que cada consumidor construya PDO

Rechazada. Multiplicaría configuración, conexiones y políticas de error, y haría imposible garantizar que una unidad transaccional utilice la misma conexión.

### Abrir PDO desde el bootstrap

Rechazada. Convertiría la conectividad en un efecto de inicialización, rompería la creación bajo demanda y contradiría `ADR-REG-A2-002` y `ADR-REG-A2-003`.

### Incorporar transacciones automáticas en los helpers

Rechazada. Un helper individual no conoce la frontera completa del caso de uso y podría confirmar una operación antes de completar las demás.

### Mantener efectos HTTP dentro de la conexión

Rechazada como estado objetivo. Acopla persistencia al transporte, impide una captura uniforme y limita reutilización en HTTP, pruebas u otros contextos.

## Límites y decisiones fuera de alcance

Este ADR no autoriza ni decide:

- crear DAO;
- crear Repository;
- introducir ORM;
- migrar completamente los modelos;
- modificar consultas SQL;
- parametrizar masivamente consultas;
- modificar tablas o el modelo de datos;
- optimizar consultas;
- agregar dependencias Composer;
- cambiar credenciales o valores de configuración;
- definir pooling o infraestructura externa;
- definir el formato final de errores para usuarios;
- crear una transacción para un caso de uso concreto;
- retirar inmediatamente `conexion()` o helpers globales;
- modificar consumidores funcionales como parte de la aprobación documental.

## Reversibilidad

Cada fase deberá ser independiente y reversible.

### Reversión de la autoridad interna

1. Restaurar la implementación anterior detrás de `conexion()`.
2. Retirar la delegación interna introducida.
3. Restaurar la secuencia de carga anterior si fue modificada.
4. Verificar que bootstrap y consumidores heredados conservan su comportamiento previo.

### Reversión de la separación HTTP

1. Restaurar temporalmente el adaptador de error anterior en el límite documentado.
2. Retirar el manejador nuevo sin cambiar la autoridad de conexión.
3. Validar respuestas observables de las entradas incluidas.

### Reversión de una migración de consumidor

1. Restaurar el helper heredado utilizado por ese consumidor.
2. Retirar exclusivamente su adaptación nueva.
3. Mantener intactos los demás consumidores migrados.
4. Repetir la validación técnica y funcional asociada.

Ninguna fase podrá eliminar contratos heredados como requisito para activar la siguiente. No se ejecutará una migración irreversible ni masiva.

## Requisitos previos para implementación

Antes de crear una Task de implementación deberá existir:

1. Aprobación de este ADR.
2. Alcance exacto de la fase a implementar.
3. Inventario de archivos y consumidores afectados.
4. Criterios de aceptación para instancia única, lazy loading y ausencia de ciclos.
5. Matriz de comportamiento de errores antes y después del cambio.
6. Correspondencia entre cada archivo modificado y su paso de reversión.
7. Confirmación de que la Task no modifica SQL, modelos funcionales, tablas, credenciales ni infraestructura.
