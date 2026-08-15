# TASK-EPIC008-BECA-EDICION-LISTA-001

## 1. Identificación

- **Nombre:** TASK-EPIC008-BECA-EDICION-LISTA-001
- **EPIC:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia
- **Tipo:** corrección local del caller de edición de nombres de beca
- **AT fuente:** AT-EPIC008-BECA-EDICION-LISTA-001
- **Inspección fuente:** INSPECCIÓN-EPIC008-BECA-EDICION-LISTA-001
- **Dictamen del AT:** A. Corrección directa técnicamente autorizable.
- **Clasificación:** [BUG] [CALLER] [IMPL] [GOV] [VF] [REV]
- **Estado:** Aprobada para revisión técnica previa a implementación

La creación de esta Task es exclusivamente documental. No autoriza por sí sola
la implementación, no modifica código y no registra la corrección como
implementada, cerrada o publicada.

## 2. Objetivo único

Autorizar para una futura implementación la sustitución exclusiva de la
variable incorrecta entregada a `Beca::editar()` dentro de:

```text
Archivo: ajax/beca.php
Branch: case 'insert-update' → else de edición
```

Transformación:

```diff
- $respuesta=$beca->editar($id_inst,$nombre);
+ $respuesta=$beca->editar($id_beca,$nombre);
```

Cantidad máxima de cambios funcionales:

```text
1 línea / 1 invocación
```

No se autoriza ampliar esta unidad hacia frontend, modelo, SQL, helpers,
mensajes, categorías ni contratos de retorno.

## 3. Fuente aprobada exacta

Fuente primaria:

```text
docs/architecture/AT-EPIC008-BECA-EDICION-LISTA-001.md
```

SHA-256 verificado:

```text
70C2DAE28E1A6B450180833F1F2474AFF1C151903ECB22B1A7D4E7EE01667D4D
```

Estado del AT:

```text
Analizado — Corrección directa autorizable
```

Dictamen:

```text
A. Corrección directa técnicamente autorizable.
```

Contrato transferido desde el AT:

```text
Variable correcta: $id_beca
Variable incorrecta: $id_inst
Variable incorrecta definida: No
Archivo potencial: ajax/beca.php
Cantidad: 1 línea / 1 invocación
Frontend modificable: No
Modelo modificable: No
SQL modificable: No
Helper modificable: No
insertarList afectado: No
Hallazgo truthy incluido: No
```

Esta Task aplica la fuente sin reconstruirla, ampliarla ni modificar sus
decisiones.

## 4. Estado Git de la creación documental

- Rama: `refactor/fase-0-seguridad`.
- HEAD local: `6fe1ca44fe78d072844be0da5104f1476654d78f`.
- Referencia local `origin/refactor/fase-0-seguridad`:
  `6fe1ca44fe78d072844be0da5104f1476654d78f`.
- HEAD remoto comprobado mediante `git ls-remote`:
  `6fe1ca44fe78d072844be0da5104f1476654d78f`.
- Staging inicial: vacío.
- `ajax/beca.php`: sin cambios locales.
- La llamada defectuosa aparece exactamente una vez en el caller activo.
- No existía una Task equivalente vigente.

Los cambios locales y documentos no rastreados preexistentes permanecieron
protegidos.

## 5. Evidencia funcional

Evidencia aportada por el usuario:

```text
La beca se crea correctamente pero no se edita.
```

Contexto funcional confirmado:

- la carga de Becas Internas y Externas funciona;
- la creación y persistencia de nombres de beca funciona;
- la recarga posterior a la inserción funciona;
- la edición de un registro existente falla;
- el fallo de edición es independiente de las unidades ya publicadas.

La Task no reabre carga ni inserción.

## 6. Cadena confirmada del identificador

```text
id_nom_beca
→ idLista
→ atributo id del botón .editar_lista
→ id_edit
→ editarLista(id_edit, nombre)
→ #oculto
→ id = $('#oculto').val()
→ dato_lista.id
→ POST id
→ $_POST['id']
→ $id_beca
→ branch de edición
```

El frontend entrega correctamente el identificador real de `nombre_beca`. No
se autoriza modificar esta cadena.

## 7. Payload frontend protegido

El payload vigente contiene:

```javascript
const dato_lista = {
  nombre: nombre,
  id: id,
  op: op,
  tipo: n_input
}
```

Contrato:

```text
id: identificador seleccionado
nombre: nuevo nombre de la beca
op: insert-update
tipo: bec_int | bec_ext
```

No se autoriza cambiar nombres, estructura, ruta, método de envío ni obtención
del ID.

## 8. Recepción en el endpoint

Debe preservarse literalmente:

```php
$id_beca=isset($_POST['id'])?(int)$_POST['id']:0;
```

Propiedades protegidas:

```text
Origen: $_POST['id']
Variable: $id_beca
Conversión: int
Valor por defecto: 0
```

`$id_beca` ya contiene el identificador correcto y no requiere una nueva
variable.

## 9. Bifurcación insert/update protegida

La condición vigente debe conservarse:

```php
if(($id_beca == 0) ){
    // inserción
}
else{
    // edición
}
```

Semántica:

```text
$id_beca == 0 → insertar un nombre nuevo
$id_beca != 0 → editar el registro seleccionado
```

La bifurcación es correcta. No se autoriza modificar condición, estructura,
indentación ni branch de inserción.

## 10. Caller defectuoso

La única invocación futura modificable es:

```php
$respuesta=$beca->editar($id_inst,$nombre);
```

La inspección y el AT demostraron:

```text
$id_inst: no definida en ajax/beca.php
$id_inst: no definida por ajax/validaciones.php
$id_inst: no corresponde al dominio de nombre_beca
$id_beca: definida desde POST id
$id_beca: utilizada para entrar al branch de edición
```

El defecto se localiza exclusivamente en el primer argumento del caller.

## 11. Transformación futura exacta

Antes:

```php
$respuesta=$beca->editar($id_inst,$nombre);
```

Después:

```php
$respuesta=$beca->editar($id_beca,$nombre);
```

Restricciones:

- conservar `$nombre`;
- conservar la llamada a `Beca::editar()`;
- no añadir argumentos;
- no cambiar la asignación a `$respuesta`;
- no modificar mensajes ni `json_encode()`;
- no realizar reemplazo global de `$id_inst`;
- no modificar apariciones comentadas o pertenecientes a otros dominios;
- conservar el formato de una sola línea.

## 12. Archivo futuro modificable

Único archivo futuro modificable:

```text
ajax/beca.php
```

Ubicación exclusiva:

```text
case 'insert-update'
→ else
→ caller de Beca::editar()
```

Ningún otro archivo de código o documentación es modificable durante la futura
implementación.

## 13. Modelo protegido

No modificar:

```text
src/Model/Beca.php
```

En particular, debe permanecer:

```php
public function editar($id,$nombre){
    $sql="UPDATE nombre_beca SET beca='$nombre' where id_nom_beca='$id'";
    return ejecutarConsulta($sql);
}
```

La firma acepta el ID y el nombre ya disponibles. El modelo se considera
contractualmente suficiente para esta corrección puntual.

## 14. SQL protegido

Debe permanecer literalmente:

```php
$sql="UPDATE nombre_beca SET beca='$nombre' where id_nom_beca='$id'";
```

No modificar:

- tabla `nombre_beca`;
- columna `beca`;
- columna identificadora `id_nom_beca`;
- `$id`;
- `$nombre`;
- interpolación;
- comillas;
- helper;
- retorno.

No parametrizar ni sanitizar dentro de esta Task.

## 15. Métodos y persistencia protegidos

No modificar ni reabrir:

- `Beca::insertar()`;
- `Beca::insertarObtenerId()`;
- `Beca::insertarList()`;
- `Beca::editar()`;
- `Beca::mostrarLista()`;
- `ejecutarEscritura($sql)`;
- `ejecutarConsulta($sql)`.

La migración de `insertarList()` está publicada en:

```text
6fe1ca44fe78d072844be0da5104f1476654d78f
```

Su VF permanece aprobada y no forma parte de esta unidad.

## 16. Frontend protegido

No modificar:

```text
js/funcAjax.js
admin/scripts/listas.js
admin/act.list.php
js/funcForm.js
```

Protecciones específicas:

- propiedades asociativas `id_nom_beca` y `beca`;
- atributo `id` del botón Editar;
- `id_edit`;
- `#oculto`;
- `dato_lista.id`;
- `op=insert-update`;
- `tipo=bec_int|bec_ext`;
- recarga mediante `cargarListas(n_input)`.

## 17. Mensajes y respuesta protegidos

No modificar:

```text
Beca registrada
Error: Beca no ha sido registrada
Beca ha sido editada
Beca no ha sido editada
```

No modificar:

- asignación a `$mensaje`;
- ternarios;
- `json_encode()`;
- `JSON_UNESCAPED_UNICODE`;
- forma de la respuesta;
- callback frontend.

Esta Task corrige exclusivamente el valor del ID entregado al modelo.

## 18. Hallazgo contractual independiente

Cadena contractual vigente:

```text
Beca::editar()
→ ejecutarConsulta($sql)
→ PDOStatement
→ evaluación por truthiness
```

El endpoint podría emitir un mensaje de éxito aunque el `UPDATE` afecte cero
filas, porque no consulta `rowCount()`.

Clasificación:

```text
Deuda contractual independiente.
No necesaria para resolver el defecto actual.
No bloqueante para corregir el ID.
Fuera del alcance de esta Task.
```

No se autoriza migrar `Beca::editar()`, cambiar el helper, consultar filas
afectadas ni modificar mensajes. Este hallazgo podrá evaluarse posteriormente
dentro de una unidad integral del objeto Beca conforme a la política CRUD.

## 19. Categorías protegidas

Preservar:

```text
bec_int → 1
bec_ext → 2
```

No modificar `$tipo_beca`, `$tipo_int`, `n_input`, filtros de lectura ni reglas
institucionales.

## 20. Baseline EOL y EOF

Baseline material verificado de `ajax/beca.php`:

```text
CRLF worktree: 73
LF sin CR worktree: 0
Newline final: No
BOM: No
Línea objetivo: CRLF
Cantidad de invocaciones objetivo: 1
SHA-256: 427B76148EC1CAEE99AA9B60B01CA6701D63C54E417B33DA68E032530DF8DD60
```

Representación Git:

```text
Blob HEAD: 0 CRLF, 73 LF
Blob padre: 0 CRLF, 73 LF
Atributo: text=auto
core.autocrlf: true
```

No se exige igualdad literal entre worktree y blob. Una futura implementación
deberá:

1. preservar `73 CRLF / 0 LF` en el worktree;
2. preservar la ausencia de newline final;
3. preservar BOM ausente;
4. mantener CRLF en la línea objetivo;
5. no introducir normalización nueva respecto del blob padre;
6. no reformatear ni reconstruir el archivo.

Si una herramienta altera EOL/EOF no relacionados, la ejecución deberá
revertir exclusivamente su modificación y detenerse.

## 21. Criterios de aceptación técnicos

La futura implementación será conforme solo si:

1. modifica exclusivamente `ajax/beca.php`;
2. modifica exclusivamente una invocación;
3. sustituye `$id_inst` por `$id_beca` en el caller activo de
   `Beca::editar()`;
4. no modifica otras apariciones de `$id_inst`;
5. conserva `$id_beca` obtenido desde `$_POST['id']`;
6. conserva cast a `int` y valor por defecto `0`;
7. conserva la bifurcación insert/update;
8. conserva el branch de inserción;
9. conserva `$nombre` y cantidad de argumentos;
10. conserva mensajes y JSON;
11. mantiene frontend intacto;
12. mantiene modelo, SQL y helpers intactos;
13. mantiene `insertarList()` intacto;
14. mantiene categorías intactas;
15. preserva EOL/EOF del worktree;
16. no introduce normalización Git adicional;
17. preserva cambios locales preexistentes;
18. mantiene staging vacío;
19. no crea archivos;
20. no crea commit;
21. no realiza push.

## 22. Validaciones técnicas futuras

Ejecutar como mínimo:

```text
php -l ajax/beca.php
git diff --check
git diff --check -- ajax/beca.php
git diff -- ajax/beca.php
git diff -U0 -- ajax/beca.php
git status --short
git diff --cached --name-only
```

Comprobaciones específicas:

```text
Archivos funcionales modificados: 1
Archivo: ajax/beca.php
Invocaciones modificadas: 1
$id_inst restante en caller editar: 0
$id_beca usado en editar(): Sí
Branch inserción: intacto
Frontend: intacto
Modelo: intacto
SQL: intacto
Helpers: intactos
insertarList: intacto
Mensajes: intactos
EOL/EOF: preservados
Staging: vacío
```

La futura revisión deberá comprobar byte-localmente prefijo y sufijo o utilizar
un mecanismo equivalente que demuestre ausencia de cambios colaterales.

## 23. Validación funcional futura

La VF corresponde exclusivamente al usuario:

1. abrir «Listas predefinidas»;
2. abrir «Becas Internas» o «Becas Externas»;
3. seleccionar un registro existente;
4. confirmar que el formulario carga el nombre;
5. cambiar únicamente ese nombre;
6. guardar;
7. confirmar mensaje `Beca ha sido editada`;
8. confirmar recarga automática;
9. confirmar el nombre nuevo visible;
10. realizar una segunda recarga;
11. confirmar persistencia del cambio;
12. confirmar ausencia de errores PHP, AJAX y JavaScript.

No es necesario crear un registro adicional. La edición y eventual reversión
del dato deberán ejecutarse conforme a la política del entorno de VF.

Codex no debe realizar la prueba funcional ni enviar POST mutantes.

## 24. Reversión

Reversión de código:

```diff
- $respuesta=$beca->editar($id_beca,$nombre);
+ $respuesta=$beca->editar($id_inst,$nombre);
```

Ubicación exclusiva: caller activo del branch de edición.

No requiere modificar frontend, modelo, SQL, helper, esquema ni migraciones.
Reversibilidad de código: alta.

La eventual reversión del nombre editado durante VF es una operación de datos
separada y deberá quedar bajo control del usuario o del entorno autorizado.

## 25. Riesgos y controles

| Riesgo | Severidad | Control |
|---|---|---|
| R1 — Sustituir por una variable diferente de `$id_beca` | Alta | Transformación literal y revisión `-U0`. |
| R2 — Alterar el branch de inserción | Alta | Proteger condición y llamada a `insertarList()`. |
| R3 — Modificar modelo o SQL | Alta | Limitar el archivo funcional a `ajax/beca.php`. |
| R4 — Reabrir persistencia ya publicada | Alta | Proteger `src/Model/Beca.php` y commit `6fe1ca4`. |
| R5 — Modificar frontend sin necesidad | Alta | Diff por rutas y hashes de fuentes protegidas. |
| R6 — Mensaje truthy con cero filas | Media | Mantenerlo como deuda independiente; no mezclarlo. |
| R7 — Alterar EOL/EOF | Media | Sustitución byte-local y conteos antes/después. |
| R8 — Incluir cambios protegidos | Alta | Staging vacío y estado por ruta. |

Riesgo global esperado: bajo con los controles aplicados.

## 26. Cambios locales protegidos

Preservar íntegramente:

```text
ajax/curso.php
form-doc/scripts/curso.js
form-doc/ver.curso.php
src/Model/Tesis.php
c1441353_antr_db.sql
```

Preservar además todos los documentos no rastreados y cambios preexistentes. No
utilizar comandos globales de staging, restauración o limpieza.

## 27. Condiciones de detención

La futura implementación deberá detenerse sin modificar archivos si:

1. la rama es incorrecta;
2. HEAD local y remoto divergen;
3. staging no está vacío;
4. `ajax/beca.php` contiene cambios locales;
5. el AT no está disponible o su SHA difiere;
6. `$id_inst` ya no aparece en el caller activo;
7. `$id_beca` no contiene el POST `id`;
8. el frontend no envía correctamente `id`;
9. `Beca::editar()` cambió de firma;
10. el SQL de edición cambió de forma incompatible;
11. se requiere modificar frontend;
12. se requiere modificar modelo o SQL;
13. se requiere modificar helper o retorno;
14. se requiere modificar branch de inserción;
15. se requiere reabrir `insertarList()`;
16. se requiere modificar mensajes;
17. se requiere modificar más de una invocación;
18. se requiere corregir el hallazgo truthy dentro de la misma unidad;
19. no pueden preservarse EOL y EOF;
20. se requiere una decisión arquitectónica o institucional nueva;
21. la fuente resulta insuficiente o contradictoria.

Ante detención no deberá modificarse ningún archivo. El informe deberá registrar
condición, evidencia, rama, HEAD, staging, cadena del ID, caller, contrato,
discrepancia, riesgo, archivos modificados, commit y push.

## 28. Trazabilidad

```text
VF de edición fallida
→ INSPECCIÓN-EPIC008-BECA-EDICION-LISTA-001
→ AT-EPIC008-BECA-EDICION-LISTA-001
→ TASK-EPIC008-BECA-EDICION-LISTA-001
→ futura revisión técnica
→ futura implementación
→ VF de edición
```

Las unidades publicadas permanecen como antecedentes cerrados:

```text
ee1194f → carga asociativa
6fe1ca4 → inserción mediante contrato explícito de escritura
```

## 29. Regla metodológica de transición

Esta Task conserva un alcance puntual porque corresponde a una unidad ya
iniciada antes de adoptar la política de cierre integral por objeto. No debe
ampliarse retroactivamente.

A partir de la siguiente Task nueva sobre un objeto deberán inspeccionarse
conjuntamente:

```text
CREATE
READ
UPDATE
DELETE
```

Si el CRUD está incompleto, la Task deberá completar las operaciones faltantes
o declararlas formalmente bloqueadas. Esta regla no incorpora el hallazgo truthy
en la presente corrección.

## 30. Auditoría metodológica

- [BUG] El fallo fue confirmado durante VF.
- [CALLER] La única ruptura está en el argumento entregado al modelo.
- [IMPL] La Task define una sustitución futura exacta de una línea.
- [GOV] Inspección y AT preceden a la Task.
- [VF] La prueba futura corresponde exclusivamente al usuario.
- [REV] La reversión restaura una única invocación.
- La corrección actual es `$id_inst → $id_beca`.
- La deuda futura es el contrato de retorno de `Beca::editar()`.
- Ambas decisiones permanecen expresamente separadas.
- No se reabre la persistencia de `insertarList()`.

## 31. Estado y dictamen

```text
Estado:
Aprobada para revisión técnica previa a implementación
```

**A. Task completa y lista para revisión técnica.**

La Task contiene fuente exacta, objetivo único, transformación, alcance,
protecciones, criterios de aceptación, validaciones, VF, reversión, riesgos,
condiciones de detención y trazabilidad. Su creación no implementa la
corrección.

## 32. Resumen de autorización futura

```text
Task creada: TASK-EPIC008-BECA-EDICION-LISTA-001
Estado: Aprobada para revisión técnica previa a implementación
Archivo futuro modificable: ajax/beca.php
Branch: case 'insert-update' → else de edición
Variable incorrecta: $id_inst
Variable correcta: $id_beca
Cantidad cambios: 1 línea / 1 invocación
Frontend modificable: No
Modelo modificable: No
SQL modificable: No
Helper modificable: No
insertarList modificable: No
Mensajes modificables: No
Hallazgo truthy incluido: No
VF por usuario: Sí
EOL worktree preservable: Sí, mediante sustitución byte-local
Riesgo: Bajo con controles
Reversión: Alta
Código modificado: No
Otros documentos modificados: No
Staging modificado: No
Commit creado: No
Push realizado: No
```
