# AT-EPIC008-BECA-LISTA-ASOCIATIVA-001

## 1. Identificación

- **Nombre:** AT-EPIC008-BECA-LISTA-ASOCIATIVA-001
- **EPIC:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia
- **Contexto:** incidencia funcional preexistente detectada antes de implementar `TASK-EPIC008-BECA-INSERTARLIST-ESCRITURA-001`
- **Inspección fuente:** INSPECCIÓN-EPIC008-VF-BECA-LISTA-001
- **Dictamen fuente:** A. Defecto frontend preexistente y corrección mínima identificada.
- **Consumidor:** invocación de `ajaxListas()` para Becas Internas y Becas Externas
- **Archivo potencial:** `js/funcAjax.js`
- **Clasificación:** [FRONT] [CONTRACT] [AT] [GOV] [VF-BLOCKER] [REV]
- **Nivel de operación:** L3 — análisis técnico y de gobierno
- **Estado:** Analizado — Corrección local directa autorizable

## 2. Objetivo y alcance

Determinar si el fallo de carga de las listas de Becas Internas y Becas
Externas puede corregirse mediante una futura adaptación local de la invocación
de `ajaxListas()` correspondiente a Beca, declarando las propiedades asociativas
reales `id_nom_beca` y `beca`.

Este AT es exclusivamente documental. No implementa la corrección, no crea una
Task, no modifica JavaScript, PHP, SQL, datos, helpers globales, endpoints,
modelos, configuración ni la Task de persistencia.

## 3. Baseline funcional

La evidencia funcional aportada por el usuario es:

```text
La lista de becas no carga.
```

El fallo fue observado antes de sustituir `ejecutarConsulta($sql)` por
`ejecutarEscritura($sql)` en `Beca::insertarList($nombre,$tipo_int)`. La
transformación backend todavía no está implementada. Por ello, mientras la
evidencia técnica se mantenga, el defecto se clasifica como preexistente e
independiente de la escritura.

No se ejecutaron POST, consultas funcionales ni escrituras de base durante este
AT.

## 4. Estado Git del análisis

- Rama: `refactor/fase-0-seguridad`.
- HEAD local: `2cbc7b3289e7e97bda941f0867f4c26bbc1d0085`.
- Referencia local `origin/refactor/fase-0-seguridad`:
  `2cbc7b3289e7e97bda941f0867f4c26bbc1d0085`.
- HEAD remoto comprobado mediante `git ls-remote`:
  `2cbc7b3289e7e97bda941f0867f4c26bbc1d0085`.
- Staging inicial: vacío.
- `js/funcAjax.js`: sin cambios locales.
- `src/Model/Beca.php`: sin cambios locales.

Baseline de formato de `js/funcAjax.js`:

- 186 terminadores de línea en total;
- 116 CRLF y 70 LF, por lo que el archivo ya posee EOL mixtos;
- la invocación objetivo, en la línea 99, termina actualmente en CRLF;
- el archivo termina con salto de línea.

Una futura implementación deberá preservar este estado y no normalizar el
archivo completo.

El árbol de trabajo contenía previamente cambios protegidos en:

- `ajax/curso.php`;
- `form-doc/scripts/curso.js`;
- `form-doc/ver.curso.php`;
- `src/Model/Tesis.php`;
- `c1441353_antr_db.sql`.

También contenía documentos no rastreados preexistentes. Todos fueron
preservados y no se activó una condición de detención.

## 5. Fuentes inspeccionadas

Fuentes funcionales obligatorias:

- `js/funcAjax.js`;
- `js/funcForm.js`;
- `admin/scripts/listas.js`;
- `admin/act.list.php`;
- `ajax/beca.php`;
- `src/Model/Beca.php`;
- `src/Config/conexion.php`.

Fuentes auxiliares directamente necesarias:

- `src/Config/ConnectionAuthority.php`, para confirmar `PDO::FETCH_ASSOC`;
- `src/bootstrap/app.php`, para confirmar la carga del helper de conexión;
- `form-doc/footer.php`, para confirmar la carga de `funcAjax.js` y
  `funcForm.js`;
- `c1441353_antr_db.sql`, únicamente como corroboración local de los nombres de
  columna de `nombre_beca`.

Fuentes de independencia respecto de persistencia:

- `docs/tasks/TASK-EPIC008-BECA-INSERTARLIST-ESCRITURA-001.md`;
- `docs/architecture/AT-EPIC008-BECA-INSERTARLIST-CONTRATO-ESCRITURA-001.md`.

No se encontró contradicción entre las fuentes.

## 6. Cadena funcional — Becas Internas

```text
admin/act.list.php
→ #btn_bec_int
→ admin/scripts/listas.js
→ clickListas('bec_int')
→ cargarListas('bec_int')
→ ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input)
→ POST op=read_lista, tipo=bec_int
→ ajax/beca.php normaliza tipo_int=1
→ Beca::mostrarLista(1)
→ ejecutarConsultaResultados($sql)
→ PDO::FETCH_ASSOC
→ json_encode(..., JSON_UNESCAPED_UNICODE)
→ JSON asociativo
```

No se encontró una variante adicional para la categoría interna.

## 7. Cadena funcional — Becas Externas

```text
admin/act.list.php
→ #btn_bec_ext
→ admin/scripts/listas.js
→ clickListas('bec_ext')
→ cargarListas('bec_ext')
→ ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input)
→ POST op=read_lista, tipo=bec_ext
→ ajax/beca.php normaliza tipo_int=2
→ Beca::mostrarLista(2)
→ ejecutarConsultaResultados($sql)
→ PDO::FETCH_ASSOC
→ json_encode(..., JSON_UNESCAPED_UNICODE)
→ JSON asociativo
```

Internas y Externas utilizan exactamente la misma invocación frontend. Solo
cambia el valor de `n_input`.

## 8. Contrato vigente de ajaxListas()

Firma real:

```javascript
function ajaxListas(
  id,
  url,
  op,
  tipo,
  propiedadId,
  propiedadEtiqueta,
  permitirEliminar
)
```

Selección de valores:

```javascript
let idLista = propiedadId ? list[propiedadId] : list[0];
let etiquetaLista = propiedadEtiqueta ? list[propiedadEtiqueta] : list[1];
```

El helper global ya soporta dos contratos:

- acceso asociativo cuando recibe `propiedadId` y `propiedadEtiqueta`;
- fallback posicional cuando ambos nombres se omiten.

No se necesita modificar `ajaxListas()` global. La adaptación correcta consiste
en utilizar su contrato asociativo existente desde el consumidor de Beca.

## 9. Invocación actual y origen de undefined

Invocación vigente:

```javascript
ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input);
```

Argumentos efectivos:

```text
Argumento 4: n_input
Argumento 5: omitido → propiedadId = undefined
Argumento 6: omitido → propiedadEtiqueta = undefined
Argumento 7: omitido → permitirEliminar = undefined
```

Al omitirse las propiedades se activan:

```javascript
list[0]
list[1]
```

La fila recibida es un objeto asociativo sin claves numéricas. En consecuencia:

```javascript
idLista = undefined;
etiquetaLista = undefined;
```

La expresión funcionalmente crítica es:

```javascript
let etiquetaLista = propiedadEtiqueta ? list[propiedadEtiqueta] : list[1];
```

`etiquetaLista` se entrega luego a `cadenaMay()`.

## 10. Contrato backend de lectura

El branch `read_lista` del endpoint ejecuta:

```php
$respuesta=$beca->mostrarLista($tipo_int);
echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
```

El método de modelo es:

```php
public function mostrarLista($tipo){
    $sql="SELECT * FROM nombre_beca WHERE tipo_beca='$tipo'  ORDER BY beca";
    return ejecutarConsultaResultados($sql);
}
```

`ejecutarConsultaResultados()` utiliza `fetchAll()` sin sobrescribir el modo
de fetch. La conexión establece:

```php
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
```

El SQL no contiene aliases. Las propiedades de cada fila corresponden a las
columnas reales de `nombre_beca`:

```text
id_nom_beca
beca
tipo_beca
```

## 11. Estructura JSON derivada

La estructura contractual es:

```json
[
  {
    "id_nom_beca": "...",
    "beca": "...",
    "tipo_beca": "..."
  }
]
```

Los tipos escalares concretos pueden variar según el driver PDO. Los nombres de
propiedad son estables y contractualmente relevantes.

## 12. Comparación contractual

| Aspecto | Frontend actual | Backend real | Compatible |
|---|---|---|---|
| ID | `list[0]` | `id_nom_beca` | No |
| Etiqueta | `list[1]` | `beca` | No |
| Tipo de acceso | Posicional | Asociativo | No |
| Categoría | `bec_int` / `bec_ext` | `tipo_beca` 1 / 2 | Sí, tras normalización |
| `cadenaMay()` recibe | `undefined` | Debiera recibir el texto de `beca` | No |

El punto de incompatibilidad está en la omisión de los argumentos quinto y
sexto del consumidor de Beca. No está en el helper global ni en el backend.

## 13. Contrato de cadenaMay()

La función vigente comienza con:

```javascript
function cadenaMay(cadena) {
  palabras = cadena.split(" ");
```

La función espera una cadena. El fallo no nace dentro de `cadenaMay()`, sino en
la entrega previa de `undefined` como consecuencia de `list[1]`.

No se autoriza ni se necesita modificar `cadenaMay()`.

## 14. Propiedades objetivo

Las propiedades correctas son exclusivamente:

```text
Propiedad ID: id_nom_beca
Propiedad etiqueta: beca
```

`tipo_beca` no debe utilizarse como identificador ni etiqueta. Su única función
en este flujo es filtrar la categoría mediante la normalización existente.

## 15. Transformación técnica potencial

Antes:

```javascript
ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input);
```

Después:

```javascript
ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input, "id_nom_beca", "beca");
```

La implementación futura deberá conservar el formato actual de una sola línea,
las comillas y el resto del archivo. Este AT no ejecuta la transformación.

## 16. Cardinalidad y alcance potencial

Se encontró exactamente una invocación de `ajaxListas()` dirigida a
`ajax/beca.php`. La misma invocación atiende:

- Becas Internas;
- Becas Externas.

Una futura Task puede limitarse exclusivamente a:

```text
Archivo: js/funcAjax.js
Branch: n_input == "bec_ext" || n_input == "bec_int"
Cantidad de invocaciones modificadas: 1
```

No se requiere otro archivo.

## 17. Contrato objetivo observable

Después de una futura corrección:

```javascript
idLista = list["id_nom_beca"];
etiquetaLista = list["beca"];
cadenaMay(etiquetaLista);
```

`cadenaMay()` recibirá el nombre de la beca. La categoría continuará gobernada
exclusivamente por:

```text
bec_int → 1
bec_ext → 2
```

El argumento `permitirEliminar` continuará omitido, por lo que la corrección no
habilitará eliminación de becas.

## 18. Independencia respecto de la Task de persistencia

`TASK-EPIC008-BECA-INSERTARLIST-ESCRITURA-001` permanece independiente porque:

- todavía no fue implementada;
- `Beca::insertarList()` participa en `op=insert-update` con `id=0`;
- `read_lista` invoca `Beca::mostrarLista()`;
- la corrección frontend no cambia escritura, SQL, retorno ni helper de
  persistencia;
- `src/Model/Beca.php` permanece protegido;
- la Task backend puede conservar su aprobación técnica, pero su implementación
  queda pausada hasta completar esta corrección y su VF de carga.

El presente AT no modifica ni amplía la Task backend.

## 19. Defecto preexistente del branch editar

El endpoint mantiene:

```php
$respuesta=$beca->editar($id_inst,$nombre);
```

`$id_inst` no está definido en `ajax/beca.php`. El defecto se clasifica como:

```text
Preexistente
Independiente
Fuera de alcance
```

No debe corregirse dentro de la futura Task de carga asociativa.

## 20. Botón Editar y riesgo funcional posterior

`ajaxListas()` genera un botón `Editar` para cada fila, independientemente del
argumento `permitirEliminar`. Al recuperar correctamente `id_nom_beca` y `beca`,
el botón volverá a ser visible y tendrá valores utilizables.

El flujo posterior es:

```text
botón .editar_lista
→ editarLista(id, nombre)
→ formulario queda en modo Editar
→ submit op=insert-update con id distinto de 0
→ branch editar de ajax/beca.php
→ uso de $id_inst no definido
```

La VF inicial de esta corrección debe limitarse a carga, visualización e
identificación. No debe probarse el guardado de edición mientras el defecto
`$id_inst` permanezca pendiente.

## 21. Componentes protegidos

Una futura Task deberá proteger expresamente:

- implementación de `ajaxListas()` global;
- `ajaxSelect()`;
- `cadenaMay()`;
- `js/funcForm.js`;
- `admin/scripts/listas.js`;
- `admin/act.list.php`;
- `ajax/beca.php`;
- `src/Model/Beca.php`;
- `src/Config/conexion.php`;
- `src/Config/ConnectionAuthority.php`;
- normalización `bec_int → 1` y `bec_ext → 2`;
- SQL, configuración e infraestructura;
- Task y AT de persistencia de `insertarList()`;
- Cursos y Tesis;
- todos los cambios locales y documentos no rastreados preexistentes.

No se autoriza reformatear `js/funcAjax.js` ni modificar otro consumidor.

## 22. Validaciones técnicas de una futura Task

Validaciones mínimas:

```text
git diff --check
git diff --check -- js/funcAjax.js
git diff -- js/funcAjax.js
git diff -U0 -- js/funcAjax.js
git status --short
git diff --cached --name-only
```

Además deberá demostrarse:

1. una sola invocación modificada;
2. diff limitado al branch de Beca en `cargarListas()`;
3. propiedad ID exacta `id_nom_beca`;
4. propiedad etiqueta exacta `beca`;
5. argumento `permitirEliminar` todavía omitido;
6. implementación de `ajaxListas()` global intacta;
7. `ajaxSelect()` intacto;
8. `cadenaMay()` intacta;
9. endpoint, modelo, SQL y configuración intactos;
10. otros consumidores de `ajaxListas()` intactos;
11. normalización de categorías intacta;
12. Task backend intacta;
13. EOL, EOF y formato de una línea preservados;
14. staging vacío;
15. cambios locales protegidos preservados;
16. ausencia de archivos nuevos o modificados fuera del alcance autorizado.

Debido al baseline mixto, no basta con comprobar visualmente el diff. La futura
Task deberá volver a contar CRLF/LF y confirmar que la única línea reemplazada
conserva terminador CRLF y que el archivo continúa terminando con salto de
línea.

## 23. Validación funcional futura

La VF corresponde exclusivamente al usuario. Primera pauta:

1. abrir «Listas predefinidas»;
2. abrir «Becas Internas»;
3. confirmar carga de registros existentes;
4. confirmar nombres correctos;
5. confirmar ausencia de `undefined.split`;
6. abrir «Becas Externas»;
7. confirmar carga de registros existentes;
8. confirmar nombres correctos;
9. confirmar ausencia de errores JavaScript;
10. no crear registros;
11. no probar guardado de edición;
12. confirmar que no aparece acción de eliminación.

Después de aprobar esta VF podrá reanudarse la implementación de
`TASK-EPIC008-BECA-INSERTARLIST-ESCRITURA-001`, sujeta a sus propias
precondiciones.

## 24. Reversión

La reversión de código potencial consiste en restaurar exclusivamente:

```javascript
ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input);
```

No requiere modificar helper global, `cadenaMay()`, endpoint, modelo, SQL,
datos ni Task backend. Reversibilidad esperada: alta.

## 25. Riesgos y controles

| Riesgo | Severidad | Control requerido |
|---|---|---|
| R1 — Modificar `ajaxListas()` global | Alta | Prohibir cambios en el helper; adaptar solo el consumidor de Beca. |
| R2 — Modificar `cadenaMay()` | Alta | Mantenerla intacta y entregar una etiqueta válida. |
| R3 — Propiedad ID incorrecta | Alta | Usar exclusivamente `id_nom_beca`. |
| R4 — Propiedad etiqueta incorrecta | Alta | Usar exclusivamente `beca`. |
| R5 — Afectar ambas categorías | Media | Confirmar la llamada compartida y ejecutar VF en internas y externas. |
| R6 — Alterar normalización de tipo | Alta | Proteger `bec_int → 1` y `bec_ext → 2`. |
| R7 — Exponer edición defectuosa | Media | Limitar la primera VF a lectura/carga; edición fuera de alcance. |
| R8 — Interferir con Task backend | Alta | Proteger `src/Model/Beca.php` y su documentación. |
| R9 — Alterar otro consumidor | Alta | Exigir diff `-U0` de una invocación. |
| R10 — Alterar EOL/EOF mixtos | Media | Reemplazo localizado; preservar CRLF en la línea objetivo, conteos globales y salto final. |

Riesgo global de la corrección local, aplicados los controles: **bajo**.

## 26. Compatibilidad arquitectónica

La corrección potencial:

- adapta un consumidor al contrato asociativo de lectura ya existente;
- utiliza una capacidad ya prevista por `ajaxListas()`;
- no redefine el backend;
- no cambia helper global, modelo, SQL ni configuración;
- no crea una abstracción nueva;
- no altera reglas de negocio;
- es incremental, localizada y reversible.

Se clasifica como **adaptación local del consumidor**. No requiere una decisión
arquitectónica nueva.

## 27. Condiciones de detención para una futura implementación

La futura implementación deberá detenerse sin modificar archivos si:

1. la rama no es `refactor/fase-0-seguridad`;
2. HEAD local y remoto divergen;
3. staging no está vacío;
4. `js/funcAjax.js` contiene cambios locales incompatibles;
5. la invocación ya está corregida;
6. no existe exactamente una invocación compartida;
7. el backend no devuelve `id_nom_beca` o `beca`;
8. `ajaxListas()` ya no posee el contrato documentado;
9. se requiere modificar `ajaxListas()` global, `ajaxSelect()` o
   `cadenaMay()`;
10. se requiere modificar endpoint, modelo, SQL o normalización;
11. se requiere tocar la Task backend o corregir `$id_inst`;
12. se requiere modificar más de una invocación;
13. se requiere una decisión arquitectónica nueva;
14. la fuente resulta insuficiente o contradictoria;
15. no pueden preservarse EOL, EOF y formato de una línea.

## 28. Auditoría metodológica

```text
Baseline funcional fallido
→ INSPECCIÓN-EPIC008-VF-BECA-LISTA-001
→ AT-EPIC008-BECA-LISTA-ASOCIATIVA-001
→ futura Task frontend independiente
→ futura implementación frontend
→ VF de carga sin escrituras
→ reanudación TASK-EPIC008-BECA-INSERTARLIST-ESCRITURA-001
```

- [FRONT] El defecto se localiza en el consumidor de presentación.
- [CONTRACT] Se adapta acceso posicional a propiedades asociativas existentes.
- [AT] Se define una única transformación potencial.
- [GOV] El AT precede a la futura Task; no implementa ni crea la Task.
- [VF-BLOCKER] La carga debe validarse antes de reanudar persistencia.
- [REV] La reversión restaura una invocación de una línea.
- No se mezclan lectura y escritura.
- No se corrigen defectos vecinos.

## 29. Dictamen

**A. Corrección local directa técnicamente autorizable.**

La futura corrección puede limitarse exclusivamente a añadir las propiedades
`id_nom_beca` y `beca` a una única invocación de `ajaxListas()` dentro de
`js/funcAjax.js`. Esa invocación atiende Becas Internas y Becas Externas. No se
requiere modificar helper global, `cadenaMay()`, endpoint, modelo, SQL,
persistencia ni Task backend.

## 30. Siguiente artefacto

El siguiente artefacto metodológico es una Task frontend independiente que
autorice exclusivamente la transformación documentada en este AT, con VF
inicial limitada a carga y visualización.

Este AT no crea esa Task y no registra la corrección como implementada.

## 31. Resumen de autorización futura

```text
Rama: refactor/fase-0-seguridad
HEAD local/remoto: 2cbc7b3289e7e97bda941f0867f4c26bbc1d0085
Staging: vacío
Archivo potencial: js/funcAjax.js
Branch: n_input == "bec_ext" || n_input == "bec_int"
Invocación actual: ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input);
Propiedad ID: id_nom_beca
Propiedad etiqueta: beca
Cantidad invocaciones: 1
Afecta internas: Sí
Afecta externas: Sí
ajaxListas global modificable: No
cadenaMay modificable: No
Backend modificable: No
Modelo modificable: No
Normalización tipo modificable: No
Task backend modificable: No
Defecto editar incluido: No
VF inicial sólo lectura: Sí
Riesgo: Bajo con controles
Reversión: Restaurar la invocación actual de cuatro argumentos
Código modificado: No
Otros documentos modificados: No
Staging modificado: No
Commit creado: No
Push realizado: No
```
