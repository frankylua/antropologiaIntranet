# AT-EPIC008-BECA-EDICION-LISTA-001

## 1. Identificación

- **Nombre:** AT-EPIC008-BECA-EDICION-LISTA-001
- **EPIC:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia
- **Contexto:** defecto funcional confirmado durante la VF posterior a la migración de persistencia de Beca
- **Inspección fuente:** INSPECCIÓN-EPIC008-BECA-EDICION-LISTA-001
- **Dictamen fuente:** A. Defecto local en caller identificado; corrección mínima directa.
- **Archivo potencial:** `ajax/beca.php`
- **Branch potencial:** `case 'insert-update'`, rama `else` de edición
- **Clasificación:** [BUG] [FRONT/BACK-CALLER] [AT] [GOV] [VF] [REV]
- **Nivel de operación:** L3 — análisis técnico y de gobierno
- **Estado:** Analizado — Corrección directa autorizable

## 2. Objetivo y alcance

Determinar formalmente si el fallo de edición de nombres de beca puede
corregirse mediante una futura sustitución exclusiva de la variable incorrecta
usada por el caller:

```diff
- $respuesta=$beca->editar($id_inst,$nombre);
+ $respuesta=$beca->editar($id_beca,$nombre);
```

Este AT es exclusivamente documental. No implementa la corrección, no crea una
Task y no modifica PHP, JavaScript, SQL, configuración, datos ni documentación
adicional.

## 3. Estado Git del análisis

- Rama: `refactor/fase-0-seguridad`.
- HEAD local: `6fe1ca44fe78d072844be0da5104f1476654d78f`.
- Referencia local `origin/refactor/fase-0-seguridad`:
  `6fe1ca44fe78d072844be0da5104f1476654d78f`.
- HEAD remoto comprobado mediante `git ls-remote`:
  `6fe1ca44fe78d072844be0da5104f1476654d78f`.
- Staging inicial: vacío.
- `ajax/beca.php`: sin cambios locales.
- Los cambios locales protegidos y documentos no rastreados preexistentes
  permanecieron presentes.

No se activó una condición de detención.

## 4. Evidencia funcional

La evidencia aportada por el usuario durante la VF es:

```text
La beca se crea correctamente pero no se edita.
```

La carga asociativa de nombres de beca fue corregida y publicada mediante:

```text
ee1194fac832e9ad0bac883196b0096eb0eb7087
fix(frontend): adapt scholarship lists to associative response
```

La migración de `Beca::insertarList()` fue validada funcionalmente y publicada
mediante:

```text
6fe1ca44fe78d072844be0da5104f1476654d78f
refactor(persistence): migrate scholarship list insert contract
```

La inserción queda cerrada para el alcance de este AT. El defecto analizado
pertenece exclusivamente a la edición.

## 5. Fuentes inspeccionadas

Fuentes obligatorias:

- `ajax/beca.php`;
- `src/Model/Beca.php`;
- `js/funcAjax.js`;
- `admin/scripts/listas.js`;
- `admin/act.list.php`.

Fuentes auxiliares directamente necesarias:

- `js/funcForm.js`, por `espacios()` y `cadenaMay()`;
- `ajax/validaciones.php`, para descartar una definición indirecta de
  `$id_inst`;
- `src/Config/conexion.php`, para determinar el contrato de
  `ejecutarConsulta()`;
- historial Git de `ajax/beca.php`;
- AT y Tasks EPIC-008 que mencionan el defecto como fuera de alcance.

Huellas SHA-256 de las fuentes materiales principales durante el análisis:

```text
ajax/beca.php:               427B76148EC1CAEE99AA9B60B01CA6701D63C54E417B33DA68E032530DF8DD60
src/Model/Beca.php:          1A177E5A0266BA4AD47947A308BE9CEFFD4536EAF2C8D056457E249EEB9B24F5
js/funcAjax.js:              EFD34FDA2ACAEAC1C881CA3FE8745A0072C51DD3EF0FB1FDF44E8F0B0E9ED802
admin/scripts/listas.js:     6727E7A7923830F0AC9AB4722FE3D2470875A60A3CEAE0EFCB2BC60A44F3163C
admin/act.list.php:          1662B1BE47532049F21B814247070323F7F6F90F4C26DFA5E25F70AEB05EBCC0
js/funcForm.js:              5AAB96906F7F80ADB6B870FAB2A3908DF27318EC24D862A2E91C6F4235DC18A8
src/Config/conexion.php:     BB25FF18781FE109D5BAAFB1CDEBEC4B54882E74490708E8B7D3E2C344E9945E
```

No se encontraron contradicciones entre las fuentes.

## 6. Identificador real

La tabla `nombre_beca` identifica cada registro mediante:

```text
id_nom_beca
```

El contrato de lectura vigente entrega filas asociativas con:

```text
id_nom_beca
beca
tipo_beca
```

La invocación frontend publicada declara expresamente:

```javascript
ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input, "id_nom_beca", "beca");
```

Por ello, el frontend dispone del identificador real antes de construir la
acción Editar.

## 7. Cadena completa del ID

```text
fila JSON: id_nom_beca
→ idLista = list["id_nom_beca"]
→ atributo id del botón .editar_lista
→ id_edit = $(this).attr('id')
→ editarLista(id_edit, nombre)
→ #oculto recibe el ID
→ id = $('#oculto').val()
→ dato_lista.id
→ POST id
→ $_POST['id']
→ $id_beca
→ bifurcación insert/update
→ caller defectuoso usa $id_inst
→ Beca::editar($id,$nombre)
→ WHERE id_nom_beca='$id'
```

La única ruptura demostrada se produce entre `$id_beca` y el primer argumento
entregado a `Beca::editar()`.

## 8. Construcción del botón Editar

`ajaxListas()` selecciona:

```javascript
let idLista = propiedadId ? list[propiedadId] : list[0];
let etiquetaLista = propiedadEtiqueta ? list[propiedadEtiqueta] : list[1];
```

Y construye:

```html
<button type="button"
        class="btn btn-link link-success btn-sm editar_lista"
        name="${etiquetaLista}"
        id="${idLista}">Editar</button>
```

Para Beca, `idLista` contiene `id_nom_beca` y `etiquetaLista` contiene `beca`.
La acción no utiliza un índice posicional ni pierde el ID.

## 9. Selección y formulario

El handler delegado captura:

```javascript
id_edit = $(this).attr('id');
nombre = $(this).attr('name');
editarLista(id_edit, nombre);
```

`editarLista()` ejecuta:

```javascript
$('#agregar_lista').prop('value', 'Editar');
$('#oculto').attr('value', id);
$('#dato_lista').val(nombre);
```

El formulario contiene:

```html
<input type="hidden" name="0" id="oculto" class="form-control">
<input type="text" name="" id="dato_lista" class="form-control" maxlength="80">
```

El nombre HTML `0` de `#oculto` no afecta el envío porque el formulario no se
serializa. El payload se construye manualmente desde `$('#oculto').val()`.

## 10. Payload frontend

El submit obtiene:

```javascript
n_input = $('#dato_lista').attr('name');
nombre = espacios($('#dato_lista').val());
id = $('#oculto').val();
op = 'insert-update';
```

Y crea:

```javascript
const dato_lista = {
  nombre: nombre,
  id: id,
  op: op,
  tipo: n_input
}
```

Para `bec_int` y `bec_ext` se envía mediante:

```javascript
insertUpdate(ruta+'ajax/beca.php', dato_lista);
```

El POST contiene exactamente:

```text
id
nombre
op=insert-update
tipo=bec_int|bec_ext
```

El ID procede del registro seleccionado y llega con el nombre esperado por el
endpoint.

## 11. Recepción en el endpoint

`ajax/beca.php` recibe el ID mediante:

```php
$id_beca=isset($_POST['id'])?(int)$_POST['id']:0;
```

Contrato observado:

```text
Origen: $_POST['id']
Variable: $id_beca
Tipo materializado: int
Valor de inserción: 0
Valor de edición: id_nom_beca distinto de 0
```

La variable está disponible antes del `switch` y es utilizada correctamente
para decidir entre inserción y edición.

## 12. Bifurcación insert/update

El branch vigente es:

```php
case 'insert-update':
    if(($id_beca == 0) ){
        $respuesta=$beca->insertarList($nombre,$tipo_int);
        $respuesta ? $mensaje="Beca registrada" : $mensaje="Error: Beca no ha sido registrada";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
    }
    else{
        $respuesta=$beca->editar($id_inst,$nombre);
        $respuesta ? $mensaje="Beca ha sido editada" : $mensaje="Beca no ha sido editada";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
    }
    break;
```

La condición posee la semántica correcta:

```text
$id_beca == 0  → inserción
$id_beca != 0  → edición
```

No se necesita modificar la bifurcación.

## 13. Caller defectuoso

La llamada defectuosa es:

```php
$respuesta=$beca->editar($id_inst,$nombre);
```

Resultados de la inspección:

```text
$id_inst: no definida en ajax/beca.php
$id_inst: no definida por ajax/validaciones.php
$id_inst: no relacionada con el identificador de nombre_beca
$id_beca: definida y contiene el POST id convertido a int
$id_beca: ya gobierna la selección del branch de edición
```

Las apariciones activas de `$id_inst` en otros endpoints pertenecen al dominio
de Institución y no proporcionan una variable al scope de `ajax/beca.php`.

## 14. Beca::editar()

Firma vigente:

```php
public function editar($id,$nombre)
```

Implementación:

```php
public function editar($id,$nombre){
    $sql="UPDATE nombre_beca SET beca='$nombre' where id_nom_beca='$id'";
    return ejecutarConsulta($sql);
}
```

Propiedades contractuales:

```text
Tabla: nombre_beca
Columna de ID: id_nom_beca
Parámetro 1: $id
Parámetro 2: $nombre
Helper: ejecutarConsulta($sql)
Retorno: PDOStatement
Caller directo de edición: ajax/beca.php
Uso del retorno: truthiness
```

La firma acepta exactamente los dos valores ya disponibles en el endpoint. El
método no requiere cambios para resolver el defecto del identificador.

## 15. SQL UPDATE

SQL vigente:

```php
$sql="UPDATE nombre_beca SET beca='$nombre' where id_nom_beca='$id'";
```

El SQL utiliza la tabla y la columna identificadora correctas. Una vez que el
caller entregue `$id_beca`, `$id` recibirá el valor de `id_nom_beca` seleccionado
por el usuario.

No se requiere cambiar tabla, columnas, interpolación, parámetros, helper ni
firma. Parametrización y sanitización permanecen fuera de este incremento.

## 16. Respuesta y recarga

El endpoint evalúa:

```php
$respuesta ? $mensaje="Beca ha sido editada" : $mensaje="Beca no ha sido editada";
echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
```

El callback frontend:

```javascript
$.post(url, dato_lista, function (response) {
  console.log(response)
  cargarListas(n_input);
  if ($('#agregar_lista').val() == 'Editar') {
    $('#agregar_lista').val('Agregar');
  }
  click();
})
```

La recarga consulta nuevamente `Beca::mostrarLista($tipo_int)`. No depende del
tipo concreto retornado por `Beca::editar()` y no requiere adaptación.

## 17. Causa raíz

La causa raíz formal es:

```text
Variable PHP incorrecta/no definida en caller.
```

El frontend entrega el ID correcto. El endpoint lo recibe como `$id_beca` y lo
utiliza para entrar al branch de edición, pero llama al modelo con `$id_inst`.
El modelo y el SQL esperan un identificador genérico y son compatibles con
`$id_beca`.

No existe evidencia que requiera modificar frontend, modelo, SQL, helper,
firma, categorías o persistencia de `insertarList()`.

## 18. Transformación potencial

La única transformación técnicamente considerada es:

```diff
- $respuesta=$beca->editar($id_inst,$nombre);
+ $respuesta=$beca->editar($id_beca,$nombre);
```

Ubicación exclusiva:

```text
Archivo: ajax/beca.php
Branch: case 'insert-update' → else de edición
Cantidad: 1 línea / 1 invocación
```

No se necesita modificar ningún otro archivo. Este AT no ejecuta la
transformación.

## 19. Alcance protegido

Una futura Task deberá proteger expresamente:

- branch de inserción de `ajax/beca.php`;
- normalización `bec_int → 1` y `bec_ext → 2`;
- mensajes y respuesta JSON;
- `src/Model/Beca.php` completo;
- `Beca::insertarList()` y `ejecutarEscritura($sql)`;
- `Beca::insertar()`;
- `Beca::insertarObtenerId()`;
- `Beca::editar()`;
- `Beca::mostrarLista()`;
- `js/funcAjax.js`;
- `admin/scripts/listas.js`;
- `admin/act.list.php`;
- `js/funcForm.js`;
- `src/Config/conexion.php`;
- SQL, firma, parámetros, helpers y categorías;
- Cursos, Tesis y SQL local;
- todos los demás cambios y documentos no rastreados preexistentes.

No se autoriza refactorizar nombres ni corregir defectos vecinos dentro de la
misma unidad.

## 20. Hallazgo contractual independiente: retorno truthy

`Beca::editar()` retorna el resultado de:

```php
ejecutarConsulta($sql)
```

El helper retorna un `PDOStatement` después de ejecutar la sentencia. El
endpoint evalúa ese objeto por truthiness sin consultar `rowCount()`.

Consecuencia potencial:

```text
UPDATE ejecutado con cero filas afectadas
→ PDOStatement truthy
→ mensaje "Beca ha sido editada"
→ recarga muestra el valor anterior
```

Este comportamiento puede explicar un mensaje de éxito engañoso, pero no es la
causa que impide entregar el ID real al `UPDATE`. Sustituir `$id_inst` por
`$id_beca` es suficiente para corregir el flujo normal de edición demostrado.

Clasificación:

```text
Hallazgo contractual independiente.
Deuda técnica separable.
No bloqueante para corregir el ID.
Fuera del alcance de la corrección del identificador.
```

Una futura Task de ID no debe cambiar helper, retorno, mensajes ni manejo de
filas afectadas.

## 21. Validación funcional potencial

La VF futura corresponde exclusivamente al usuario:

1. abrir «Listas predefinidas»;
2. abrir «Becas Internas» o «Becas Externas»;
3. seleccionar un registro existente;
4. confirmar que el formulario carga ID y nombre;
5. cambiar únicamente el nombre;
6. guardar;
7. confirmar mensaje de edición;
8. confirmar recarga con el nombre nuevo;
9. recargar nuevamente la página;
10. confirmar persistencia del nombre;
11. verificar ausencia de errores PHP, AJAX y JavaScript.

No se necesita crear otro registro para esta VF. La política de datos deberá
ser definida por la futura Task o por la autoridad que ejecute la prueba.

## 22. EOL y EOF

Baseline material del worktree para `ajax/beca.php`:

```text
SHA-256: 427B76148EC1CAEE99AA9B60B01CA6701D63C54E417B33DA68E032530DF8DD60
CRLF: 73
LF sin CR: 0
Newline final: No
BOM: No
Línea objetivo: CRLF
Offset de la cadena objetivo: 1931
```

Representación Git:

```text
Blob HEAD: 0 CRLF, 73 LF, sin newline final, sin BOM
Blob padre: 0 CRLF, 73 LF, sin newline final, sin BOM
Atributo: text=auto
core.autocrlf: true
```

La diferencia worktree/blob es normalización Git preexistente y no constituye
un defecto. Una futura implementación deberá:

1. preservar `73 CRLF / 0 LF` en el worktree;
2. preservar la ausencia de newline final y BOM;
3. conservar CRLF en la línea objetivo;
4. no introducir un patrón EOL nuevo respecto del blob padre;
5. evitar formateadores y reconstrucciones del archivo.

## 23. Riesgos y controles

| Riesgo | Severidad | Control requerido |
|---|---|---|
| R1 — Usar una variable distinta de `$id_beca` | Alta | Sustitución literal y diff `-U0` de una invocación. |
| R2 — Modificar el branch de inserción | Alta | Proteger líneas de `$id_beca == 0` e `insertarList()`. |
| R3 — Modificar modelo o SQL | Alta | Limitar el único archivo a `ajax/beca.php`. |
| R4 — Reabrir la migración `insertarList()` | Alta | Proteger `src/Model/Beca.php` y commit `6fe1ca4`. |
| R5 — Cambiar frontend | Alta | Mantener todos los JavaScript y la vista fuera del diff. |
| R6 — Éxito truthy con cero filas | Media | Registrar como deuda independiente; no mezclar con la corrección del ID. |
| R7 — Alterar EOL/EOF | Media | Sustitución byte-local y verificación worktree/blob. |
| R8 — Incluir cambios locales protegidos | Alta | Estado por ruta y staging vacío durante implementación. |

R6 no bloquea la corrección del ID. El riesgo global de la transformación
local, aplicados los controles, es **bajo**.

## 24. Reversión

Reversión potencial de código:

```diff
- $respuesta=$beca->editar($id_beca,$nombre);
+ $respuesta=$beca->editar($id_inst,$nombre);
```

La reversión afecta una única cadena dentro de `ajax/beca.php`. No requiere
reversión de esquema, migraciones, helpers, frontend ni datos.

Reversibilidad esperada: alta.

## 25. Historial y antecedentes

La inspección del historial determinó:

- `$id_beca` y la llamada defectuosa con `$id_inst` están presentes desde el
  commit inicial `684a9db` (`creacion repositorio`);
- `git blame` atribuye la llamada activa de edición en la línea 49 a
  `684a9db`;
- los commits posteriores `603134d`, `0bd496d` y `47016cf` modificaron otros
  aspectos de `ajax/beca.php`, pero no corrigieron esa llamada;
- el commit frontend `ee1194f` corrigió la entrega asociativa de
  `id_nom_beca`, no el caller PHP;
- el commit de persistencia `6fe1ca4` modificó exclusivamente
  `Beca::insertarList()` y su documentación;
- no existe corrección parcial activa del caller;
- no existe archivo AT o Task vigente cuyo nombre corresponda a la edición de
  listas de Beca;
- los AT y Tasks previos solo documentan `$id_inst` como defecto preexistente,
  independiente, protegido y fuera de sus respectivos alcances.

El defecto es histórico. La intención documental previa fue posponerlo para una
unidad independiente, no corregirlo dentro de las unidades de carga o
persistencia.

## 26. Compatibilidad técnica y arquitectónica

La transformación potencial:

- utiliza una variable ya inicializada por el endpoint;
- preserva el contrato POST existente;
- preserva bifurcación, modelo, SQL, helper y respuesta;
- no introduce abstracciones;
- no cambia reglas institucionales;
- no altera categorías;
- no afecta carga ni inserción;
- es incremental, local y reversible.

No requiere una decisión arquitectónica o institucional nueva. Se clasifica
como corrección local del caller.

## 27. Validaciones de una futura Task

Una futura implementación deberá comprobar como mínimo:

```text
php -l ajax/beca.php
git diff --check
git diff --check -- ajax/beca.php
git diff -- ajax/beca.php
git diff -U0 -- ajax/beca.php
git status --short
git diff --cached --name-only
```

Además deberá demostrar:

1. una sola invocación modificada;
2. `$id_inst` sustituida exclusivamente en el caller activo de Beca;
3. `$id_beca` todavía inicializada desde `$_POST['id']`;
4. bifurcación insert/update intacta;
5. branch de inserción intacto;
6. mensajes y JSON intactos;
7. modelo, SQL, helpers y frontend intactos;
8. `insertarList()` intacto;
9. EOL y EOF del worktree preservados;
10. patrón del blob sin normalización adicional;
11. staging vacío;
12. cambios locales protegidos preservados.

## 28. Condiciones de detención de una futura implementación

La futura implementación deberá detenerse sin modificar archivos si:

1. la rama o HEAD aprobados difieren;
2. local y remoto divergen;
3. staging no está vacío;
4. `ajax/beca.php` contiene cambios incompatibles;
5. `$id_inst` ya no aparece en la llamada activa;
6. `$id_beca` ya no contiene el POST `id`;
7. el frontend ya no entrega `id_nom_beca`;
8. `Beca::editar()` ya no utiliza `id_nom_beca`;
9. se necesita modificar más de una invocación;
10. se necesita modificar otro archivo;
11. se necesita modificar frontend, modelo, SQL o helper;
12. se necesita reabrir `insertarList()`;
13. aparece una Task equivalente vigente;
14. no pueden preservarse EOL y EOF;
15. se requiere una decisión arquitectónica o institucional nueva;
16. la fuente resulta insuficiente o contradictoria.

## 29. Auditoría metodológica

```text
VF inserción aprobada
→ INSPECCIÓN-EPIC008-BECA-EDICION-LISTA-001
→ AT-EPIC008-BECA-EDICION-LISTA-001
→ futura TASK-EPIC008-BECA-EDICION-LISTA-001
→ revisión técnica
→ implementación local del caller
→ VF de edición
```

- [BUG] El comportamiento observable está confirmado por VF.
- [CALLER] La ruptura está entre la variable recibida y el argumento del
  modelo.
- [AT] Se define una transformación potencial sin implementarla.
- [GOV] Diagnóstico y AT preceden a la futura Task.
- [VF] La futura prueba corresponde al usuario.
- [REV] Código reversible en una sola línea.
- La corrección del ID se mantiene separada del contrato truthy.
- No se reabre la migración de persistencia.
- No se mezclan carga, inserción y edición.

## 30. Dictamen

**A. Corrección directa técnicamente autorizable.**

La evidencia demuestra que `id_nom_beca` llega correctamente desde el frontend
hasta `$id_beca`, pero el branch de edición utiliza `$id_inst`, variable no
definida. Una futura corrección puede limitarse exclusivamente a sustituir el
primer argumento de una llamada en `ajax/beca.php`. No requiere modificar
frontend, modelo, SQL, helper, firma, categorías ni `insertarList()`.

## 31. Siguiente artefacto

El siguiente artefacto metodológico es:

```text
TASK-EPIC008-BECA-EDICION-LISTA-001
```

Su objetivo deberá limitarse a autorizar la transformación exacta documentada,
con controles byte-locales, protecciones y VF de edición. Este AT no crea esa
Task ni autoriza automáticamente la implementación.

## 32. Resumen de autorización futura

```text
Archivo potencial: ajax/beca.php
Branch: case 'insert-update' → else de edición
Variable correcta: $id_beca
Variable incorrecta: $id_inst
Variable incorrecta definida: No
Transformación: $beca->editar($id_inst,$nombre) → $beca->editar($id_beca,$nombre)
Cantidad cambios: 1 línea / 1 invocación
Frontend modificable: No
Modelo modificable: No
SQL modificable: No
Helper modificable: No
insertarList afectado: No
Hallazgo retorno incluido en corrección: No
VF viable: Sí, sobre un registro existente
Riesgo: Bajo con controles
Reversión: Alta; restaurar una invocación
Código modificado: No
Otros documentos modificados: No
Staging modificado: No
Commit creado: No
Push realizado: No
```
