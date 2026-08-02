# AT-EPIC008-FINANCIAMIENTO-CONTRATO-ESCRITURA-001

## 1. Identificación

- **Nombre:** AT-EPIC008-FINANCIAMIENTO-CONTRATO-ESCRITURA-001
- **EPIC:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia
- **Feature:** FEATURE-001 — Evolución de los Contratos de Persistencia
- **ADR:** ADR-001 — Contrato explícito para operaciones de escritura
- **Consumidor:** `Financiamiento::insertar($nombre)`
- **Archivo principal:** `src/Model/Financiamiento.php`
- **Clasificación:** [ARQ] [PERSIST] [IMPL] [GOV] [DOC]
- **Nivel de operación:** L2 — análisis técnico documental bajo supervisión de Dirección Técnica
- **Estado:** Aprobado

## 2. Contexto

FEATURE-001 establece la evolución incremental de los contratos de persistencia, consumidor por consumidor, con compatibilidad, reversibilidad y validación independiente. ADR-001 define `ejecutarEscritura()` como contrato explícito para operaciones de escritura y mantiene la coexistencia con los helpers heredados.

Este análisis determina si `Financiamiento::insertar($nombre)` puede abandonar el contrato genérico `ejecutarConsulta($sql)` mediante un incremento mínimo. El análisis es exclusivamente documental: no implementa la migración, no crea una Task y no modifica código, SQL, parámetros, callers, helpers, configuración ni datos.

### Estado Git de la inspección

- Rama: `refactor/fase-0-seguridad`.
- HEAD local: `c753454733161c660449074f5ce720c93a518cd3`.
- Referencia local `origin/refactor/fase-0-seguridad`: `c753454733161c660449074f5ce720c93a518cd3`.
- SHA remoto comprobado mediante `git ls-remote origin refs/heads/refactor/fase-0-seguridad`: `c753454733161c660449074f5ce720c93a518cd3`.
- Último commit publicado esperado: disponible y coincidente.
- `git fetch origin`: no pudo actualizarse porque el entorno denegó escritura sobre `.git/FETCH_HEAD` con `Permission denied`. La comprobación remota se realizó sin modificar `.git` mediante `git ls-remote`.
- Staging inicial: vacío.

El árbol de trabajo ya contenía cambios ajenos y protegidos en:

- `ajax/curso.php`;
- `form-doc/scripts/curso.js`;
- `form-doc/ver.curso.php`;
- `src/Model/Tesis.php`;
- `c1441353_antr_db.sql`.

También existían previamente los siguientes documentos no rastreados, preservados y no utilizados como autoridad:

- `docs/architecture/AT-EPIC003-CONTEXTO-AUTORIZACION-DERIVADA-001.md`;
- `docs/architecture/AT-EPIC003-RESOLUCION-PERMISO-HISTORICO-003-001.md`;
- `docs/architecture/AT-EPIC003-RESOLUCION-PERMISO-HISTORICO-005-001.md`;
- `docs/tasks/TASK-EPIC003-AUTORIZACION-REGLAMENTO-DERIVADA-PILOTO-001.md`;
- `docs/tasks/TASK-EPIC003-INVENTARIO-CONSISTENCIA-ROL-ESTUDIANTE-005-001.md`;
- `docs/tasks/TASK-EPIC003-INVENTARIO-IDENTIDAD-DOCENTE-PERMISO-004-001.md`;
- `docs/tasks/TASK-EPIC003-INVENTARIO-LOGIN-SIN-USUARIO-001.md`;
- `docs/tasks/TASK-EPIC003-INVENTARIO-PERMISO-HISTORICO-003-001.md`.

## 3. Fuente

La fuente principal es el resultado aprobado `INSPECCIÓN-EPIC008-SELECCION-CONSUMIDOR-PERSISTENCIA-001`, suministrado como fuente exacta para esta ejecución. Sus hechos relevantes fueron contrastados directamente con el contenido rastreado del repositorio.

Fuentes inspeccionadas:

- `docs/roadmap/ROADMAP.md`;
- `docs/TASKS.md`;
- `docs/WORKFLOW.md`;
- `docs/features/EPIC-008_FEATURE-001.md`;
- `docs/adr/ADR-001-contrato-explicito-operaciones-escritura.md`;
- `src/Config/conexion.php`;
- `src/Config/ConnectionAuthority.php`;
- `src/Model/Financiamiento.php`;
- `ajax/financiamiento.php`;
- `admin/scripts/listas.js`;
- `admin/act.list.php`;
- `js/funcAjax.js`;
- los segmentos consumidores de `form-doc/scripts/proyecto.js` y `form-doc/scripts/tesis.js`;
- historial Git completo de `src/Model/Financiamiento.php`;
- referencias rastreadas a Financiamiento, callers y Tasks de persistencia anteriores.

La búsqueda documental no encontró una Task previa dedicada a `Financiamiento::insertar()` ni una migración parcial del método. El blob del archivo de trabajo y el blob de HEAD para `src/Model/Financiamiento.php` coinciden en `17b397e68b1b68d4e1a46640a793893e99a271ff`; por tanto, el consumidor no tiene cambios locales.

El historial del modelo contiene solamente su creación heredada y el traslado PSR-4 del commit `0bd496d3e96eeb7ed40d504169a555ac17a84fb7`; la lógica de `insertar()` permanece igual desde la creación del repositorio.

## 4. Método objetivo

### Firma

```php
public function insertar($nombre)
```

No declara tipos de parámetro ni de retorno.

### Implementación vigente

```php
public function insertar($nombre){
    $sql="INSERT INTO financiamiento (id_financ,financiamiento) VALUES (NULL,'$nombre')";
    return ejecutarConsulta($sql);
}
```

### Características verificadas

- Operación: `INSERT`.
- Tabla afectada: `financiamiento`.
- Columnas: `id_financ` y `financiamiento`.
- Valor de `id_financ`: `NULL`, con generación delegada a la base de datos.
- Parámetro de entrada: `$nombre`.
- Interpolación actual: directa dentro de la cadena SQL; no utiliza parámetros enlazados.
- Helper actual: `ejecutarConsulta($sql)`.
- Retorno actual: el `PDOStatement` retornado por el helper.
- Excepciones: no captura excepciones. Las excepciones de conexión, preparación o ejecución se propagan; la conexión configura `PDO::ATTR_ERRMODE` como `PDO::ERRMODE_EXCEPTION`.
- Side effect: inserción de una fila en `financiamiento` y generación del identificador correspondiente por la base de datos.
- Transacción: el método y el helper no abren, confirman ni revierten una transacción explícita.
- Relación con otros métodos: comparte literalmente el SQL con `insertarObtenerId($nombre)`, pero usa un helper y un contrato de retorno distintos. `editar()`, `mostrar()` y `eliminar()` son independientes.

La única transformación técnicamente necesaria y autorizable en una futura Task es:

```diff
- return ejecutarConsulta($sql);
+ return ejecutarEscritura($sql);
```

No se autoriza la transformación si llegara a exigir cualquier otro cambio.

## 5. Contrato actual

`ejecutarConsulta($sql)` obtiene la conexión, prepara el SQL, ejecuta la sentencia sin arreglo de parámetros y retorna el `PDOStatement`.

Para el caller incluido se verificó lo siguiente:

- evalúa el retorno solamente mediante una condición booleana ternaria;
- no realiza comparación estricta de tipo o valor;
- no comprueba que el retorno sea instancia de `PDOStatement`;
- no llama `fetch()`, `fetchAll()` ni otro método del statement;
- no llama `rowCount()`;
- no consulta `lastInsertId()`;
- no serializa el `PDOStatement`;
- no devuelve el retorno del modelo al frontend;
- solo distingue éxito truthy de fallo falsy para seleccionar un mensaje;
- no existen otros consumidores directos del retorno de `Financiamiento::insertar($nombre)`.

Con `PDO::ERRMODE_EXCEPTION`, los fallos de preparación o ejecución se propagan como excepción. El método no incorpora manejo local de errores. No existe dependencia observable del tipo `PDOStatement`.

## 6. Contrato objetivo

La firma vigente del helper es:

```php
function ejecutarEscritura(
    string $sql,
    array $parametros = [],
    bool $obtenerIdInsertado = false
): array
```

Su resultado explícito es:

```php
[
    'filasAfectadas' => $statement->rowCount(),
    'idInsertado' => $idInsertado,
]
```

Características aplicables:

- Tipo de retorno: `array`.
- `filasAfectadas`: cantidad informada por `PDOStatement::rowCount()` después de ejecutar.
- `idInsertado`: `null` por defecto; solo consulta `PDO::lastInsertId()` cuando el tercer argumento es `true`.
- Parámetros: el segundo argumento es un arreglo vacío por defecto, compatible con el SQL interpolado vigente.
- Error explícito: si `execute()` retorna `false`, lanza `RuntimeException`.
- Otras excepciones: no se capturan; las excepciones de conexión, `prepare()`, `execute()`, `rowCount()` o `lastInsertId()` se propagan. Con la configuración PDO vigente, un error SQL normalmente se manifiesta como `PDOException` antes del control de retorno falso.
- Transacción: no incorpora control transaccional.

El arreglo retornado siempre contiene dos claves y es truthy en PHP, incluso si `filasAfectadas` fuese `0`. Esto preserva el branch de éxito que hoy recibe un `PDOStatement` truthy tras una ejecución sin excepción. El endpoint conserva su comportamiento de fallo por propagación de excepción; no se añade ni cambia manejo de errores.

La sentencia `INSERT` existente es compatible con `ejecutarEscritura($sql)` sin alterar SQL ni parámetros. Para este consumidor debe usarse el helper con un único argumento: no se solicita identificador insertado y `idInsertado` debe permanecer `null`.

## 7. Callers

### Cadena funcional incluida

```text
admin/act.list.php
→ admin/scripts/listas.js
→ POST ajax/financiamiento.php
→ op=insert-update, id=0
→ Financiamiento::insertar($nombre)
→ evaluación booleana del retorno
→ mensaje JSON
→ recarga mediante op=read
```

### Evento UI y payload

1. `#btn_financ` ejecuta `clickListas('financ')`, muestra la tabla y carga la lista.
2. El envío de `#form_lista` se intercepta con `preventDefault()`.
3. Para `n_input == 'financ'`, JavaScript envía mediante POST a `ajax/financiamiento.php`:

```text
nombre: valor normalizado del campo
id: valor de #oculto
op: insert-update
tipo: financ
```

4. En una creación, `#oculto` está vacío; el endpoint normaliza y convierte el valor a entero, resultando `0`.
5. `op=insert-update` con `id == 0` llama exclusivamente a `Financiamiento::insertar($nom_financ)`.

### Respuesta y recarga

- Mensaje de éxito del endpoint: `Fuente de Financiamiento Registrada`.
- Mensaje de fallo declarado: `Fuente de Financiamiento no ha sido registrada`.
- Contrato JSON: cadena JSON producida por `json_encode($mensaje, JSON_UNESCAPED_UNICODE)`.
- El callback de `insertUpdate()` registra la respuesta en consola, llama `cargarListas(n_input)`, restablece el botón y limpia el formulario.
- `cargarListas('financ')` solicita `ajax/financiamiento.php` con `op=read`, recargando la lista.
- El retorno del modelo no se entrega al frontend; únicamente determina el mensaje del endpoint.

La búsqueda global rastreada encontró un único caller directo de `Financiamiento::insertar($nombre)`: `ajax/financiamiento.php`, línea correspondiente al branch `op=insert-update` e `id=0`.

## 8. Separación de consumidores

### Consumidor incluido

- Método: `Financiamiento::insertar($nombre)`.
- Endpoint: `ajax/financiamiento.php`.
- Branch: `op=insert-update` con `id=0`.
- Necesidad observable: conocer únicamente éxito o fallo mediante truthiness.

### Consumidor excluido

- Método: `Financiamiento::insertarObtenerId($nombre)`.
- Endpoint: `ajax/financiamiento.php`.
- Branch: `op=insert`.
- Helper: `obtenerIdConsulta($sql)`.
- Retorno: identificador generado por `PDO::lastInsertId()`.
- Consumidores indirectos identificados: flujos de Proyecto y Tesis que crean una fuente seleccionando «Otro» y usan la respuesta como identificador.

`insertarObtenerId()` posee un contrato distinto y no debe modificarse, analizarse como parte de la futura Task ni reemplazarse indirectamente. La futura migración de `insertar()` no afecta el branch `op=insert`, Proyecto ni Tesis.

## 9. Alcance potencial de implementación

### Archivo modificable

`src/Model/Financiamiento.php`

### Método modificable

`Financiamiento::insertar($nombre)`

### Transformación única

Sustituir exclusivamente `ejecutarConsulta($sql)` por `ejecutarEscritura($sql)` en la sentencia `return` del método.

### Elementos que deben preservarse literalmente o sin cambio

- firma y nombre del método;
- SQL, tabla, columnas y valores;
- parámetro `$nombre` y su interpolación vigente;
- llamada al helper con un único argumento;
- consumo truthy del retorno;
- propagación de excepciones;
- callers, endpoint, frontend y mensajes;
- todos los demás métodos de `Financiamiento`;
- formato, EOL y EOF del resto del archivo.

La futura Task no queda autorizada si requiere modificar otro archivo o cualquier otro elemento.

## 10. Exclusiones

Quedan expresamente fuera del alcance:

- `Financiamiento::insertarObtenerId()`;
- `Financiamiento::editar()`;
- `Financiamiento::eliminar()`;
- `Financiamiento::mostrar()`;
- `ajax/financiamiento.php`;
- `admin/scripts/listas.js`;
- `admin/act.list.php`;
- Proyecto;
- Tesis;
- Cursos;
- helpers globales y `src/Config/conexion.php`;
- SQL, tablas, columnas y valores;
- parámetros e interpolación;
- mensajes y respuestas JSON;
- transacciones;
- parametrización y sanitización;
- reglas de negocio;
- validaciones y manejo de errores;
- refactorización adicional;
- cambios de formato, EOL, EOF o reorganización de código;
- datos y pruebas que escriban sobre una base real;
- `docs/TASKS.md`, `docs/roadmap/ROADMAP.md`, `docs/PROJECT_CONTEXT.md`, `docs/WORKFLOW.md`, ADR-001, FEATURE-001 y cualquier otro documento.

### Defectos preexistentes observados y excluidos

- `editar()` utiliza la tabla escrita como `financiamientp`; el endpoint además entrega el objeto `$financ` como segundo argumento y contiene nombres de variables de mensaje inconsistentes.
- El branch `delete` usa `$id_pueblo`, variable no definida en el endpoint, y también contiene una variable de mensaje inconsistente.
- Los SQL del modelo interpolan entradas directamente y no utilizan parámetros enlazados.
- Los flujos Proyecto y Tesis envían el dato de la nueva fuente bajo la clave `financ`, mientras el endpoint lee `nombre` para `op=insert`.
- El formulario de listas solo evita el envío vacío en cliente; el endpoint no incorpora una validación específica del nombre más allá de `limpiar_datos()`.
- El callback de administración registra la respuesta en consola, pero no presenta el mensaje JSON en un componente visual.
- Bajo `PDO::ERRMODE_EXCEPTION`, los errores SQL se propagan; por ello, el mensaje ternario de fallo no cubre normalmente una excepción de ejecución.

Estos defectos no forman parte del alcance, no deben corregirse ni utilizarse para ampliar la futura Task. No impiden validar aisladamente el contrato de `insertar()` en el branch incluido.

## 11. Riesgos

| Riesgo | Nivel | Evaluación y control |
|---|---|---|
| Cambio de tipo de retorno de `PDOStatement` a `array` | Medio | Es un cambio contractual real, pero el único caller usa exclusivamente truthiness. El riesgo residual es bajo tras preservar el caller. |
| Dependencia oculta del caller respecto del tipo exacto | Bajo | La búsqueda rastreada confirma un solo caller directo, sin comparaciones estrictas ni métodos de `PDOStatement`. |
| Modificación accidental de `insertarObtenerId()` | Medio | Debe comprobarse que su diff sea nulo; su contrato de ID y branch `op=insert` son independientes. |
| Interferencia con el branch `op=insert` | Bajo | La futura línea está dentro de otro método y el endpoint no cambia. |
| Interferencia con Proyecto o Tesis | Bajo | Esos flujos consumen `insertarObtenerId()` mediante `op=insert`, ambos excluidos. |
| Corrección accidental de `editar()` o `eliminar()` | Medio | Existen defectos visibles que podrían inducir expansión de alcance; se exige que ambos métodos permanezcan idénticos. |
| Cambio involuntario de SQL o parámetros | Medio | La autorización cubre solo el nombre del helper en una línea; el diff debe mostrar la cadena SQL intacta. |
| Cambio de formato, EOL o EOF | Medio | El archivo heredado debe editarse preservando su formato; el diff se limita a una invocación. |
| Inclusión accidental de cambios locales protegidos | Alto | El árbol está sucio. Deben usarse comandos limitados por ruta, mantener staging vacío y comparar el estado inicial y final. |
| Validación funcional que altere datos reales sin control | Alto | Codex no debe ejecutarla. Solo el usuario puede crear y retirar un dato controlado mediante procedimiento autorizado. |

No se identifica ningún riesgo bloqueante. El riesgo técnico global de la migración, aplicados los controles, es **bajo**.

## 12. Validación técnica futura

Una futura Task deberá ejecutar y registrar como mínimo:

1. `php -l src/Model/Financiamiento.php`.
2. `git diff --check`.
3. Diff limitado a una sola invocación en `Financiamiento::insertar($nombre)`.
4. Verificación estática del único caller directo en `ajax/financiamiento.php`.
5. Confirmación de que no se agregaron comparaciones estrictas ni usos de `PDOStatement`.
6. Confirmación de que `insertarObtenerId()` permanece idéntico.
7. Confirmación de que `editar()`, `mostrar()` y `eliminar()` permanecen idénticos.
8. Confirmación de que `ajax/financiamiento.php` permanece idéntico.
9. Confirmación de que `admin/scripts/listas.js`, `admin/act.list.php`, Proyecto, Tesis y Cursos permanecen intactos.
10. Confirmación de SQL y parámetros idénticos.
11. `git diff --cached --name-only` vacío durante la implementación.
12. `git status --short` sin archivos nuevos o modificados atribuibles a la Task fuera de `src/Model/Financiamiento.php`.

La validación debe comparar por separado los cambios locales protegidos preexistentes y nunca incorporarlos al diff o staging de la futura Task.

## 13. Validación funcional futura

La validación funcional corresponde exclusivamente al usuario:

1. Abrir la administración de listas.
2. Seleccionar «Fuente de Financiamiento».
3. Crear un registro de prueba controlado y autorizado.
4. Confirmar la respuesta `Fuente de Financiamiento Registrada`.
5. Confirmar la aparición del registro en la lista recargada mediante `op=read`.
6. Confirmar que se creó exactamente una fila.
7. Confirmar ausencia de warnings y errores PHP.
8. Validar separadamente, en su flujo propio, que `op=insert` continúa retornando el identificador requerido.
9. Retirar el dato de prueba mediante el procedimiento autorizado, si corresponde.

Codex no debe ejecutar escrituras reales ni crear datos temporales.

## 14. Reversión

La reversión exacta consiste en restaurar una sola línea dentro de `Financiamiento::insertar($nombre)`:

```php
return ejecutarConsulta($sql);
```

No debe modificarse SQL, parámetros, otros métodos, endpoint, frontend, helpers, datos ni Tasks anteriores. La reversión restablece el retorno heredado `PDOStatement`.

## 15. Compatibilidad con ADR-001

- Operación de escritura: confirmada como `INSERT`.
- Consumidor: único caller directo confirmado.
- Migración incremental: un método y una invocación.
- Coexistencia: preserva `ejecutarConsulta()`, `obtenerIdConsulta()` y todos sus demás consumidores.
- Contrato explícito: adopta `ejecutarEscritura()` sin modificarlo.
- Migración masiva: ausente.
- Cambio funcional observable: no requerido; se preservan truthiness, endpoint, mensajes y recarga.
- Reversibilidad: exacta y de una línea.
- Validación independiente: técnica sobre un archivo y funcional sobre el flujo de listas.
- Transacciones, SQL, esquema y reglas de negocio: sin cambios.

**Evaluación:** Compatible.

## 16. Decisión

**A. Migración directa autorizable mediante cambio único de helper.**

La viabilidad queda limitada a sustituir la invocación del helper dentro de `Financiamiento::insertar($nombre)`. Este AT no autoriza automáticamente la implementación: se requiere una Task independiente, preparada y aprobada conforme al workflow.

## 17. Task derivable

- **ID propuesto:** `TASK-EPIC008-FINANCIAMIENTO-INSERTAR-ESCRITURA-001`
- **Objetivo:** migrar exclusivamente `Financiamiento::insertar($nombre)` desde `ejecutarConsulta($sql)` hacia `ejecutarEscritura($sql)`.
- **Archivo modificable:** `src/Model/Financiamiento.php`.
- **Método modificable:** `Financiamiento::insertar($nombre)`.
- **Archivos protegidos:** todos los demás.
- **Implementación:** una sustitución de helper, sin solicitar ID insertado.
- **Reversión:** restaurar `return ejecutarConsulta($sql);`.

La Task se define únicamente de forma conceptual. No se crea su documento ni se autoriza su implementación durante esta ejecución.

## 18. Trazabilidad y auditoría metodológica

```text
EPIC-008 — Gobierno del Modelo de Datos y Persistencia
→ FEATURE-001 — Evolución de los Contratos de Persistencia
→ ADR-001 — Contrato explícito para operaciones de escritura
→ INSPECCIÓN-EPIC008-SELECCION-CONSUMIDOR-PERSISTENCIA-001
→ AT-EPIC008-FINANCIAMIENTO-CONTRATO-ESCRITURA-001
→ TASK-EPIC008-FINANCIAMIENTO-INSERTAR-ESCRITURA-001 (propuesta, no creada)
```

Auditoría:

- [ARQ] La decisión aplica ADR-001 sin introducir una decisión arquitectónica nueva.
- [PERSIST] La operación, helper actual, helper objetivo y contratos fueron verificados directamente.
- [IMPL] El alcance potencial contiene una sola transformación reversible.
- [GOV] Se mantiene la secuencia AT anterior a Task e implementación.
- [DOC] Solo se autoriza este documento; no se modifican fuentes oficiales adicionales.
- [BLOCK] No activado: no hay dependencia de `PDOStatement`, caller adicional, cambio de SQL, cambio de parámetros ni archivo implementativo adicional.
- La fuente exacta fue accesible en la autorización de ejecución y su evidencia fue corroborada contra el repositorio rastreado.
- Se preservan las reglas observables, el SQL y la separación entre `insertar()` e `insertarObtenerId()`.
- Cursos, Tesis y Secure quedan excluidos y sin modificación.
- La validación funcional queda a cargo del usuario.
- No se inventan reglas de negocio ni se corrigen defectos preexistentes.

## 19. Estado

- **AT:** Aprobado.
- **Dictamen:** A. Migración directa autorizable.
- **Crear AT:** autorizado y materializado en este documento.
- **Modificar código:** prohibido durante este AT.
- **Crear Task:** prohibido durante este AT.
- **Implementar:** prohibido durante este AT.
- **Modificar SQL, parámetros, callers o helpers:** prohibido.
- **Staging, commit y push:** prohibidos.

La actualización posterior de `docs/TASKS.md`, `docs/roadmap/ROADMAP.md`, `docs/PROJECT_CONTEXT.md`, `docs/WORKFLOW.md`, ADR-001 o FEATURE-001 requiere una Task documental independiente.
