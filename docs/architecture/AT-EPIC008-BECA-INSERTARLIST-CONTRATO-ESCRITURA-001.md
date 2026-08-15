# AT-EPIC008-BECA-INSERTARLIST-CONTRATO-ESCRITURA-001

## 1. Identificación

- **Nombre:** AT-EPIC008-BECA-INSERTARLIST-CONTRATO-ESCRITURA-001
- **EPIC:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia
- **Feature:** FEATURE-001 — Evolución de los Contratos de Persistencia
- **ADR:** ADR-001 — Contrato explícito para operaciones de escritura
- **Inspección fuente:** INSPECCIÓN-EPIC008-SELECCION-CONSUMIDOR-PERSISTENCIA-002
- **Consumidor:** `Beca::insertarList($nombre,$tipo_int)`
- **Archivo principal:** `src/Model/Beca.php`
- **Clasificación:** [ARQ] [PERSIST] [AT] [GOV] [REV]
- **Nivel de operación:** L3 — análisis técnico y arquitectónico
- **Estado:** Analizado — Migración directa autorizable

## 2. Objetivo y alcance

Determinar si `Beca::insertarList($nombre,$tipo_int)` puede migrar desde el
contrato heredado `ejecutarConsulta($sql)` hacia el contrato explícito
`ejecutarEscritura($sql)` mediante una futura modificación mínima, compatible y
reversible.

Este AT es exclusivamente documental. No implementa la migración, no crea una
Task y no modifica código, SQL, parámetros, endpoints, frontend, helpers,
configuración ni datos.

## 3. Estado Git de la inspección

- Rama: `refactor/fase-0-seguridad`.
- HEAD local: `2cbc7b3289e7e97bda941f0867f4c26bbc1d0085`.
- Referencia local `origin/refactor/fase-0-seguridad`:
  `2cbc7b3289e7e97bda941f0867f4c26bbc1d0085`.
- HEAD remoto verificado con `git ls-remote origin
  refs/heads/refactor/fase-0-seguridad`:
  `2cbc7b3289e7e97bda941f0867f4c26bbc1d0085`.
- Staging inicial: vacío.
- `src/Model/Beca.php`: sin cambios locales; el blob del archivo de trabajo y
  el blob de HEAD coinciden en `f2726ef69af7101aff428a7fcf0bd253c71b27d1`.

El árbol de trabajo contenía previamente cambios ajenos y protegidos en:

- `ajax/curso.php`;
- `form-doc/scripts/curso.js`;
- `form-doc/ver.curso.php`;
- `src/Model/Tesis.php`;
- `c1441353_antr_db.sql`.

También contenía ocho documentos EPIC-003 no rastreados. Todos fueron
preservados y ninguno se utilizó como autoridad para este AT.

No se activó ninguna condición de detención.

## 4. Fuentes inspeccionadas

Fuentes obligatorias:

- `docs/roadmap/ROADMAP.md`;
- `docs/features/EPIC-008_FEATURE-001.md`;
- `docs/adr/ADR-001-contrato-explicito-operaciones-escritura.md`;
- `docs/TASKS.md`;
- `docs/WORKFLOW.md`;
- `src/Config/conexion.php`;
- `src/Model/Beca.php`;
- `ajax/beca.php`;
- `admin/scripts/listas.js`;
- `admin/act.list.php`.

Fuentes adicionales directamente necesarias:

- `src/Config/ConnectionAuthority.php`, para confirmar el modo de errores de
  PDO;
- `src/bootstrap/app.php`, para confirmar la carga del helper de conexión;
- `js/funcAjax.js`, para reconstruir la recarga de listas y comprobar la
  ausencia de eliminación de becas desde la UI;
- historial Git rastreado de `src/Model/Beca.php` y `ajax/beca.php`.

La inspección confirmó la separación de responsabilidades establecida por
ROADMAP, FEATURE-001, ADR-001 y WORKFLOW. No se encontró una fuente oficial que
contradiga la selección aprobada.

## 5. Método objetivo

### Firma exacta

```php
public function insertarList($nombre,$tipo_int)
```

La firma no declara tipos de parámetros ni tipo de retorno.

### Implementación vigente

```php
public function insertarList($nombre,$tipo_int){
    $sql="INSERT INTO nombre_beca (id_nom_beca,beca,tipo_beca) VALUES (NULL,'$nombre','$tipo_int')";
    return ejecutarConsulta($sql);

}
```

### Características verificadas

- Contiene exactamente una sentencia de escritura.
- Operación: `INSERT`.
- Tabla: `nombre_beca`.
- Columnas: `id_nom_beca`, `beca`, `tipo_beca`.
- `id_nom_beca`: se entrega como `NULL`; la generación queda a cargo de la
  base de datos.
- `$nombre`: se interpola directamente en la cadena SQL.
- `$tipo_int`: se interpola directamente en la cadena SQL.
- No utiliza placeholders ni arreglo de parámetros PDO.
- Helper actual: `ejecutarConsulta($sql)`.
- Retorno exacto: el valor retornado por el helper.
- No contiene llamadas internas adicionales.
- No inicia ni finaliza transacciones.
- No captura excepciones.
- Efecto secundario: inserción de una fila de catálogo en `nombre_beca`.

El método coincide íntegramente con la inspección fuente y todavía no utiliza
`ejecutarEscritura()`.

## 6. SQL actual

```php
$sql="INSERT INTO nombre_beca (id_nom_beca,beca,tipo_beca) VALUES (NULL,'$nombre','$tipo_int')";
```

El SQL produce un ID autogenerado, pero el método no lo recupera. No existen
otras escrituras dentro del método. Una futura Task deberá preservar literalmente
esta cadena, incluidos tabla, columnas, orden, valores e interpolación.

La parametrización, sanitización y modificación de SQL están fuera de alcance.

## 7. Contrato actual: ejecutarConsulta()

La implementación vigente es:

```php
function ejecutarConsulta($sql){
    $conexion = conexion();
    $statement = $conexion->prepare($sql);
    $statement->execute();
    return $statement;
}
```

Contrato observado:

- recibe una cadena SQL;
- obtiene la conexión PDO singleton;
- prepara el SQL;
- ejecuta sin arreglo explícito de parámetros;
- retorna el `PDOStatement`;
- no inspecciona el valor booleano retornado por `execute()`;
- no obtiene `rowCount()` ni `lastInsertId()`;
- no captura excepciones de `prepare()` o `execute()`.

`ConnectionAuthority` configura `PDO::ATTR_ERRMODE` como
`PDO::ERRMODE_EXCEPTION`. En entorno de desarrollo, las excepciones de conexión
se propagan; fuera de desarrollo, `conexion()` registra el error, fija HTTP 500
y finaliza con un mensaje genérico. Las excepciones de preparación o ejecución
no se capturan en el helper.

Tipo retornado tras ejecución normal: `PDOStatement`.

## 8. Contrato objetivo: ejecutarEscritura()

Firma vigente:

```php
function ejecutarEscritura(
    string $sql,
    array $parametros = [],
    bool $obtenerIdInsertado = false
): array
```

Comportamiento:

1. obtiene la misma conexión PDO singleton;
2. prepara el SQL;
3. ejecuta con el arreglo `$parametros`;
4. si `execute()` retorna `false`, lanza `RuntimeException`;
5. solo llama `lastInsertId()` si `$obtenerIdInsertado === true`;
6. retorna:

```php
[
    'filasAfectadas' => $statement->rowCount(),
    'idInsertado' => $idInsertado,
]
```

La llamada de un solo argumento `ejecutarEscritura($sql)` es contractualmente
válida porque:

- `$parametros` adopta `[]`;
- `$obtenerIdInsertado` adopta `false`;
- `idInsertado` permanece `null`;
- el SQL interpolado vigente no requiere parámetros enlazados.

El resultado es siempre un arreglo no vacío después de una ejecución normal y,
por tanto, es truthy en PHP.

## 9. Comparación contractual

| Aspecto | `ejecutarConsulta` | `ejecutarEscritura` | Compatibilidad |
|---|---|---|---|
| Ejecuta `INSERT` | Sí, mediante `prepare()` y `execute()` | Sí, mediante `prepare()` y `execute([])` | Compatible |
| Tipo de retorno | `PDOStatement` | `array` con dos claves | Cambio real, no observado por el caller |
| Truthiness en éxito | Objeto truthy | Arreglo no vacío truthy | Compatible con el branch real |
| ID insertado | No lo obtiene | `null` por defecto | Compatible; el caller no requiere ID |
| `rowCount()` | No lo consulta | Se expone como `filasAfectadas` | Sin impacto; el caller no lee el dato |
| Excepciones | PDO en conexión/preparación/ejecución | PDO; además `RuntimeException` si `execute() === false` | Compatible bajo `ERRMODE_EXCEPTION`; diferencia explícita residual |
| SQL recibido | Una cadena | La misma cadena | Compatible |
| Parámetros recibidos | No recibe arreglo | `[]` por defecto | Compatible con el SQL interpolado |
| Necesidad de caller nuevo | No | No | Compatible |

El cambio `PDOStatement` → `array` no altera el comportamiento observable del
consumidor confirmado porque el endpoint solo evalúa truthiness. La diferencia
semántica residual es que `ejecutarEscritura()` convierte explícitamente un
`execute() === false` en excepción; con `PDO::ERRMODE_EXCEPTION`, los errores SQL
normales ya se manifiestan como excepción antes de esa comprobación.

No se autoriza convertir el retorno en `bool` ni adaptar el caller.

## 10. Caller directo

La única llamada directa confirmada se encuentra en `ajax/beca.php`:

```php
case 'insert-update':
    if(($id_beca == 0) ){
        $respuesta=$beca->insertarList($nombre,$tipo_int);
        $respuesta ? $mensaje="Beca registrada" : $mensaje="Error: Beca no ha sido registrada";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
    }
```

Datos relevantes del endpoint:

- `$nombre` proviene de `$_POST['nombre']` o `''`.
- `$tipo_beca` proviene de `$_POST['tipo']` o `''`.
- `$id_beca` proviene de `$_POST['id']`, convertido a entero, o adopta `0`.
- `$tipo_int` se inicializa en `0`.
- `bec_int` se normaliza a `1`.
- `bec_ext` se normaliza a `2`.
- Branch: `op=insert-update` con `$id_beca == 0`.
- Mensaje de éxito: `Beca registrada`.
- Mensaje de fallo ternario: `Error: Beca no ha sido registrada`.
- Respuesta: cadena JSON mediante `json_encode(...,
  JSON_UNESCAPED_UNICODE)`.

El retorno se utiliza exclusivamente como condición booleana. No existe:

- `fetch()`;
- `fetchAll()`;
- `rowCount()`;
- `lastInsertId()`;
- invocación de métodos de `PDOStatement`;
- comparación estricta;
- serialización del retorno del modelo;
- acceso a propiedades o claves;
- devolución del resultado de persistencia al frontend.

La búsqueda global rastreada encontró exactamente un caller directo asociado a
`Beca::insertarList()`.

## 11. Cadena funcional indirecta

```text
admin/act.list.php
→ botones “Becas Internas” / “Becas Externas”
→ admin/scripts/listas.js
→ clickListas('bec_int' | 'bec_ext')
→ #form_lista submit
→ POST ajax/beca.php
→ op=insert-update, id=0, tipo=bec_int|bec_ext
→ normalización tipo_int=1|2
→ Beca::insertarList($nombre,$tipo_int)
→ ejecutarConsulta($sql)
→ PDOStatement truthy
→ mensaje JSON “Beca registrada”
→ callback insertUpdate()
→ cargarListas(n_input)
→ POST op=read_lista
→ Beca::mostrarLista($tipo_int)
→ recarga visible de la categoría
```

`admin/act.list.php` exige sesión `admin` o `comite` para presentar la página.
El formulario envía `nombre`, `id`, `op` y `tipo`. El callback registra la
respuesta en consola, recarga la lista y limpia el formulario; no presenta el
mensaje de inserción en el contenedor visual disponible.

Los dos flujos de categoría convergen en el mismo caller y método. No requieren
cambiar la normalización ni introducir una regla institucional nueva.

## 12. Comportamiento observable y truthiness

### Éxito actual

Después de una ejecución normal, `ejecutarConsulta()` retorna un objeto
`PDOStatement`, que es truthy. El endpoint selecciona `Beca registrada`, lo
serializa como cadena JSON y el frontend vuelve a consultar la categoría. El
registro aparece en la lista si la lectura posterior se completa correctamente.

### Éxito potencial

Después de una ejecución normal, `ejecutarEscritura()` retorna un arreglo no
vacío con `filasAfectadas` e `idInsertado`. El arreglo es truthy aunque
`filasAfectadas` fuese `0`; el endpoint seleccionaría el mismo mensaje y
ejecutaría la misma recarga.

### Error o fallo

- Con `PDO::ERRMODE_EXCEPTION`, un error normal de preparación o ejecución se
  propaga como excepción antes del ternario.
- El endpoint no captura esa excepción; por tanto, el mensaje ternario de fallo
  no representa normalmente los errores SQL.
- El helper objetivo conserva la propagación de excepciones y añade una
  `RuntimeException` explícita para el caso residual `execute() === false`.
- No se incorpora manejo nuevo de errores y no cambia el frontend.

El tipo del retorno cambia, pero ese tipo no forma parte del comportamiento
observado por el único consumidor.

## 13. Dependencia del ID insertado

La base genera `id_nom_beca`, pero el flujo seleccionado:

- no solicita el ID;
- no lo captura;
- no lo serializa;
- no lo devuelve al frontend;
- no lo utiliza en una escritura posterior;
- recarga la lista mediante una consulta independiente.

Por ello, `ejecutarEscritura($sql)` con un solo argumento preserva completamente
el contrato requerido y debe dejar `idInsertado` en `null`.

Solicitar el ID mediante el tercer argumento está prohibido para este
consumidor.

## 14. Transacciones y efectos secundarios

`Beca::insertarList()`:

- no inicia transacción;
- no ejecuta `commit()` ni `rollBack()`;
- no recibe una conexión externa;
- no contiene más de una escritura;
- no depende de rollback;
- no forma parte de una secuencia de escrituras en el branch seleccionado.

Aunque la conexión es singleton dentro de la petición, la cadena inspeccionada
no abre una transacción antes de invocar el método. La operación se ejecuta bajo
el comportamiento de autocommit vigente.

El único efecto persistente es la creación de una fila en `nombre_beca`.

## 15. Métodos vecinos protegidos

### Beca::insertar()

- Inserta una relación de beca en la tabla `beca`.
- Usa `ejecutarConsulta($sql)`.
- Posee firma, tabla, parámetros, caller y finalidad diferentes.
- Queda fuera del futuro incremento.

### Beca::insertarObtenerId()

- Inserta en `nombre_beca`.
- Usa `obtenerIdConsulta($sql)`.
- Retorna el valor de `PDO::lastInsertId()`.
- El branch `op=insert` serializa ese ID directamente.
- Su contrato es incompatible con una sustitución simple que no solicite ID.
- Debe permanecer expresamente intacto y no agruparse en la misma Task.

### Beca::editar()

- Ejecuta un `UPDATE nombre_beca`.
- Usa `ejecutarConsulta($sql)`.
- Pertenece al branch de edición y queda fuera del futuro incremento.

### Hallazgo preexistente del branch editar

El endpoint mantiene el defecto informado:

```php
$respuesta=$beca->editar($id_inst,$nombre);
```

`$id_inst` no se define en `ajax/beca.php`. El defecto no afecta el branch
`insert-update` con `$id_beca == 0`, que invoca `insertarList()`. No bloquea la
migración seleccionada, no debe corregirse en la futura Task y no autoriza
ampliar su alcance.

## 16. Transformación técnica potencial

Solo se considera técnicamente autorizable la siguiente sustitución:

```diff
- return ejecutarConsulta($sql);
+ return ejecutarEscritura($sql);
```

Ubicación exclusiva:

```text
Archivo: src/Model/Beca.php
Método: Beca::insertarList($nombre,$tipo_int)
```

La transformación debe conservar:

- firma;
- SQL;
- parámetros e interpolación;
- llamada con un solo argumento;
- propagación de excepciones;
- endpoint, frontend, mensajes y JSON;
- todos los métodos vecinos;
- EOL, EOF y formato del resto del archivo.

Este AT no implementa ni autoriza por sí solo la transformación. Se requiere una
Task independiente, preparada y aprobada.

## 17. Archivos y elementos protegidos

Una futura Task deberá proteger expresamente:

- `Beca::insertar()`;
- `Beca::insertarObtenerId()`;
- `Beca::editar()`;
- constructor y métodos de lectura de `Beca`;
- `ajax/beca.php`;
- `admin/scripts/listas.js`;
- `admin/act.list.php`;
- `js/funcAjax.js`;
- `src/Config/conexion.php`;
- `src/Config/ConnectionAuthority.php`;
- SQL, tabla, columnas, valores, interpolación y parámetros;
- mensajes y respuesta JSON;
- normalización `bec_int → 1` y `bec_ext → 2`;
- helpers globales y contratos heredados;
- Cursos y Tesis;
- todos los cambios locales y documentos no rastreados preexistentes;
- `docs/TASKS.md`, ROADMAP, FEATURE-001, ADR-001 y el resto de la
  documentación.

Ningún archivo distinto de `src/Model/Beca.php` necesitaría modificarse. Dentro
de ese archivo, solo sería modificable la invocación del helper en
`insertarList()`.

## 18. Validaciones técnicas de una futura Task

La futura implementación deberá ejecutar y registrar como mínimo:

```text
php -l src/Model/Beca.php
git diff --check
git diff -- src/Model/Beca.php
git diff -U0 -- src/Model/Beca.php
git status --short
git diff --cached --name-only
```

Además deberá confirmar:

1. una sola invocación funcional modificada;
2. diff limitado a `Beca::insertarList()`;
3. firma exacta sin cambios;
4. SQL idéntico byte a byte;
5. `$nombre` y `$tipo_int` intactos;
6. llamada objetivo con un único argumento;
7. `Beca::insertar()` intacto;
8. `Beca::insertarObtenerId()` intacto;
9. `Beca::editar()` intacto;
10. lecturas y constructor intactos;
11. único caller directo todavía confirmado;
12. ausencia de usos nuevos de `PDOStatement`, `rowCount()` o
    `lastInsertId()`;
13. `ajax/beca.php` intacto;
14. `admin/scripts/listas.js`, `admin/act.list.php` y `js/funcAjax.js`
    intactos;
15. helpers y configuración intactos;
16. cambios locales protegidos preservados;
17. staging vacío hasta una autorización posterior expresa;
18. ausencia de archivos nuevos o modificados fuera del alcance de la Task.

Si cualquiera de estas verificaciones exige otro cambio, la futura
implementación deberá detenerse.

## 19. Validación funcional potencial

La VF corresponde exclusivamente al usuario. Codex no debe ejecutar POST ni
crear datos.

Flujo potencial:

1. utilizar una base descartable o un entorno con datos de prueba;
2. abrir «Listas predefinidas» con una sesión autorizada;
3. seleccionar «Becas Internas» o «Becas Externas»;
4. crear un nombre de prueba único y reconocible;
5. confirmar en consola la respuesta JSON `Beca registrada`;
6. confirmar la aparición del registro en la categoría correcta;
7. confirmar que se creó exactamente una fila;
8. confirmar ausencia de errores PHP, JavaScript y AJAX;
9. retirar o descartar el entorno de prueba conforme al procedimiento
   autorizado.

### Política segura de datos de VF

Se adopta **A. VF sobre base descartable** como política requerida y preferente.

Fundamento:

- `ajax/beca.php` no implementa un branch activo de eliminación de
  `nombre_beca`;
- `ajaxListas()` no habilita el botón de eliminación para becas;
- `eliminarLista()` rechaza categorías distintas de Pueblo y Título;
- la reversión del código no elimina el dato creado durante la VF.

Solo si la autoridad aprueba previamente un procedimiento concreto podrá
utilizarse **B. limpieza manual previamente autorizada**. La futura Task no
puede crear un mecanismo de eliminación, ampliar el endpoint ni asumir que la
limpieza manual está autorizada.

La VF sobre una base real no descartable y sin limpieza autorizada no es
recomendable.

## 20. Riesgos y controles

| Riesgo | Severidad | Probabilidad | Control requerido |
|---|---|---|---|
| R1 — Cambio `PDOStatement` → `array` | Media | Baja | Mantener caller intacto y verificar consumo exclusivo por truthiness. |
| R2 — Modificar accidentalmente `insertarObtenerId()` | Alta | Baja | Diff nulo del método y no solicitar ID en el helper objetivo. |
| R3 — Alterar el branch de edición defectuoso | Media | Media | Proteger `Beca::editar()` y `ajax/beca.php`; no corregir `$id_inst`. |
| R4 — Cambiar SQL o interpolación | Alta | Baja | Autorizar solo el nombre del helper y comparar el SQL literalmente. |
| R5 — Cambiar categorías interna/externa | Alta | Baja | Preservar endpoint y normalización `1/2`; VF en ambas categorías si se autoriza. |
| R6 — Solicitar accidentalmente ID insertado | Media | Baja | Usar exactamente `ejecutarEscritura($sql)` con un argumento. |
| R7 — Cambiar endpoint o frontend | Alta | Baja | Declararlos protegidos y comprobar diff nulo por ruta. |
| R8 — No poder limpiar el dato de VF | Media | Alta en base real | Exigir base descartable; limpieza manual solo con autorización previa. |
| R9 — Incorporar cambios locales protegidos | Alta | Media | Comandos limitados por ruta, inventario inicial/final y staging controlado. |
| R10 — Reformateo o cambio EOL/EOF | Media | Media | Diff `-U0`, una sola invocación y preservación del formato del archivo. |

Riesgo técnico global de la transformación, aplicados los controles: **bajo**.
Riesgo de datos de una VF sobre base real sin limpieza: **medio y no
aceptable**.

## 21. Reversión

### Reversión de código

La reversión exacta consiste en restaurar una sola línea dentro de
`Beca::insertarList($nombre,$tipo_int)`:

```php
return ejecutarConsulta($sql);
```

No requiere modificar SQL, parámetros, caller, endpoint, frontend, helper ni
otro método.

### Reversión de datos de VF

Es independiente de la reversión de código. Restaurar el helper heredado no
elimina una fila creada por la VF. La UI inspeccionada no permite eliminar
nombres de beca; por ello, la reversión funcional solo es segura mediante:

- descarte completo de la base de prueba; o
- limpieza manual previamente definida y autorizada.

No se afirma reversibilidad total de datos sobre una base real.

## 22. Historial y Tasks previas

Se buscaron `Beca::insertarList`, `src/Model/Beca.php`, `ajax/beca.php` y
`nombre_beca` en documentación oficial, Tasks, AT, commits e historial Git.

Resultados:

- no existe una Task equivalente;
- no existe un AT previo equivalente;
- no existe una migración previa de `insertarList()`;
- no existe implementación parcial del helper objetivo en el método;
- `src/Model/Beca.php` conserva el contrato heredado desde su incorporación y
  posterior traslado PSR-4;
- el método actual y su caller coinciden con la selección aprobada;
- no hay cambios locales en el modelo.

Los documentos EPIC-003 no rastreados fueron ignorados como autoridad.

## 23. Compatibilidad con ADR-001

| Criterio ADR-001 | Evaluación |
|---|---|
| Operación confirmada de escritura | Cumple: un `INSERT` confirmado |
| Consumidor individual | Cumple: `Beca::insertarList()` únicamente |
| Caller identificado | Cumple: exactamente uno |
| Migración incremental | Cumple: un método y una invocación |
| Coexistencia | Cumple: preserva todos los helpers y consumidores heredados |
| Contrato explícito | Cumple: objetivo `ejecutarEscritura()` ya existente |
| Comportamiento observable | Cumple: truthiness, mensaje y recarga preservables |
| Reversibilidad | Cumple para código: restauración exacta de una línea |
| Validación asociable | Cumple con VF en base descartable |
| Ausencia de migración masiva | Cumple |
| SQL, esquema y reglas | Permanecen intactos |

**Evaluación:** la migración cumple ADR-001 y no requiere una decisión
arquitectónica adicional.

## 24. Decisiones fuera de alcance

Este AT no resuelve ni autoriza:

- parametrización o sanitización SQL;
- refactor general de Beca;
- corrección del branch editar o de `$id_inst`;
- migración de `Beca::insertar()`;
- modificación del contrato de `insertarObtenerId()`;
- nuevos helpers o abstracciones;
- Repository, DAO u ORM;
- transacciones;
- cambios de esquema;
- eliminación de registros de catálogo;
- decisiones institucionales sobre tipos de beca;
- cambios de endpoint, frontend, mensajes o JSON;
- creación de datos o ejecución de POST por Codex.

## 25. Auditoría metodológica

```text
EPIC-008 — Gobierno del Modelo de Datos y Persistencia
→ FEATURE-001 — Evolución de los Contratos de Persistencia
→ ADR-001 — Contrato explícito para operaciones de escritura
→ INSPECCIÓN-EPIC008-SELECCION-CONSUMIDOR-PERSISTENCIA-002
→ AT-EPIC008-BECA-INSERTARLIST-CONTRATO-ESCRITURA-001
→ futura Task independiente
```

- [ARQ] Se aplica una decisión existente sin crear arquitectura nueva.
- [PERSIST] SQL, helpers, retornos, ID, transacción y caller fueron verificados.
- [AT] Se define una única transformación potencial y sus controles.
- [GOV] El AT precede a la Task; no se implementó ni cerró el incremento.
- [REV] Se distinguen reversión de código y reversión de datos de VF.
- No se mezclan consumidores.
- No se corrigen defectos preexistentes.
- No se modifican fuentes oficiales ajenas a este AT.

## 26. Dictamen

**A. Migración directa técnicamente autorizable.**

La futura migración puede limitarse exclusivamente a sustituir
`ejecutarConsulta($sql)` por `ejecutarEscritura($sql)` dentro de
`Beca::insertarList($nombre,$tipo_int)`. El SQL, la firma, los parámetros, el
caller, el endpoint, el frontend y los mensajes pueden permanecer intactos.

El problema de limpieza no eleva la transformación a una migración con controles
adicionales de código, pero sí obliga a que la futura VF utilice una base
descartable o una limpieza manual expresamente autorizada.

## 27. Siguiente artefacto

El siguiente artefacto metodológico sería una Task independiente cuyo único
objetivo sea migrar `Beca::insertarList($nombre,$tipo_int)` al contrato explícito
de escritura.

Este AT no crea esa Task, no autoriza su implementación y no asigna estado de
implementado o cerrado.

## 28. Resumen de autorización futura

```text
Rama: refactor/fase-0-seguridad
HEAD local/remoto: 2cbc7b3289e7e97bda941f0867f4c26bbc1d0085
Staging: vacío
Archivo: src/Model/Beca.php
Método: Beca::insertarList($nombre,$tipo_int)
Operación: INSERT
Helper actual: ejecutarConsulta($sql)
Helper potencial: ejecutarEscritura($sql)
Caller directo: ajax/beca.php
Cantidad callers directos: 1
Retorno actual: PDOStatement
Uso del retorno: truthiness exclusivamente
ID requerido: No
Transacción: No
SQL modificable: No
Parámetros modificables: No
Endpoint modificable: No
Frontend modificable: No
insertar modificable: No
insertarObtenerId modificable: No
editar modificable: No
Cambios locales: No en src/Model/Beca.php
Task previa: No equivalente
Riesgo: Bajo con controles; VF en base real sin limpieza no autorizada: medio
Reversión código: Restaurar return ejecutarConsulta($sql);
Reversión datos VF: Descartar base de prueba o limpieza manual previamente autorizada
VF viable: Sí, sobre base descartable
Código modificado: No
Otros documentos modificados: No
Staging modificado: No
Commit creado: No
Push realizado: No
```
