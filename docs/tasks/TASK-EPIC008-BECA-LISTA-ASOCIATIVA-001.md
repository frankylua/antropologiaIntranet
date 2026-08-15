# TASK-EPIC008-BECA-LISTA-ASOCIATIVA-001

## 1. Identificación

- **Nombre:** TASK-EPIC008-BECA-LISTA-ASOCIATIVA-001
- **EPIC:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia
- **Tipo:** corrección frontend independiente y previa a `TASK-EPIC008-BECA-INSERTARLIST-ESCRITURA-001`
- **AT fuente:** AT-EPIC008-BECA-LISTA-ASOCIATIVA-001
- **Inspección fuente:** INSPECCIÓN-EPIC008-VF-BECA-LISTA-001
- **Clasificación:** [FRONT] [CONTRACT] [IMPL] [GOV] [VF-BLOCKER] [REV]
- **Estado:** Aprobada para revisión técnica previa a implementación

La creación de esta Task no autoriza automáticamente la implementación. La
corrección requiere una revisión técnica previa y una autorización de ejecución
independiente.

## 2. Objetivo único

Autorizar para una futura implementación la adaptación exclusiva de la llamada
a `ajaxListas()` utilizada por las listas de Becas Internas y Becas Externas,
declarando las propiedades asociativas `id_nom_beca` y `beca`.

```text
Archivo futuro modificable: js/funcAjax.js
Branch: n_input == "bec_ext" || n_input == "bec_int"
Cantidad esperada de cambios funcionales: 1 invocación
```

Esta ejecución es exclusivamente documental y no implementa la corrección.

## 3. Fuente aprobada

Fuente primaria:

```text
docs/architecture/AT-EPIC008-BECA-LISTA-ASOCIATIVA-001.md
```

Huella SHA-256 verificada:

```text
8B2596A89D30918B1DC349CFE15E216597820FA44BADB9FF6CC3E4C45DAE98C8
```

Estado del AT:

```text
Analizado — Corrección local directa autorizable
```

Dictamen del AT:

```text
A. Corrección local directa técnicamente autorizable.
```

Esta Task aplica el AT sin reconstruirlo, ampliarlo ni modificar sus decisiones.

## 4. Baseline funcional

Evidencia funcional aportada por el usuario:

```text
La lista de becas no carga.
```

El fallo fue observado antes de implementar
`TASK-EPIC008-BECA-INSERTARLIST-ESCRITURA-001`. El defecto es frontend,
preexistente e independiente de la futura migración backend.

## 5. Estado Git de la creación documental

- Rama: `refactor/fase-0-seguridad`.
- HEAD local: `2cbc7b3289e7e97bda941f0867f4c26bbc1d0085`.
- Referencia local `origin/refactor/fase-0-seguridad`:
  `2cbc7b3289e7e97bda941f0867f4c26bbc1d0085`.
- HEAD remoto comprobado mediante `git ls-remote`:
  `2cbc7b3289e7e97bda941f0867f4c26bbc1d0085`.
- Staging inicial: vacío.
- `js/funcAjax.js`: sin cambios locales.
- La invocación objetivo todavía no contiene las propiedades asociativas.
- Existe exactamente una invocación objetivo compartida.

Los cambios locales y documentos no rastreados preexistentes fueron preservados.

## 6. Contrato backend vigente

Becas Internas:

```text
bec_int
→ tipo_int = 1
→ Beca::mostrarLista(1)
```

Becas Externas:

```text
bec_ext
→ tipo_int = 2
→ Beca::mostrarLista(2)
```

Método de lectura:

```php
public function mostrarLista($tipo){
    $sql="SELECT * FROM nombre_beca WHERE tipo_beca='$tipo'  ORDER BY beca";
    return ejecutarConsultaResultados($sql);
}
```

La conexión utiliza `PDO::FETCH_ASSOC`. Cada fila contiene las propiedades:

```text
id_nom_beca
beca
tipo_beca
```

El backend, modelo y SQL no son modificables en esta Task.

## 7. Contrato frontend vigente

Firma del helper global:

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

Selección de datos:

```javascript
let idLista = propiedadId ? list[propiedadId] : list[0];
let etiquetaLista = propiedadEtiqueta ? list[propiedadEtiqueta] : list[1];
```

Invocación actual de Beca:

```javascript
ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input);
```

Al omitir los argumentos quinto y sexto se utilizan incorrectamente `list[0]`
y `list[1]` sobre filas asociativas.

## 8. Causa técnica confirmada

La fila real ofrece:

```javascript
list["id_nom_beca"]
list["beca"]
```

El consumidor actual intenta:

```javascript
list[0]
list[1]
```

Por ello `etiquetaLista` queda en `undefined` y
`cadenaMay(etiquetaLista)` intenta operar sobre un valor no textual.

La causa no está en `cadenaMay()`, `ajaxListas()` global ni el backend.

## 9. Archivo e invocación futura modificables

Único archivo futuro modificable:

```text
js/funcAjax.js
```

Única invocación futura modificable:

```javascript
ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input);
```

Ubicación:

```text
Función: cargarListas(n_input)
Branch: n_input == "bec_ext" || n_input == "bec_int"
```

No se autoriza modificar otra llamada a `ajaxListas()`.

## 10. Transformación exacta

Antes:

```javascript
ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input);
```

Después:

```javascript
ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input, "id_nom_beca", "beca");
```

Solo se autoriza añadir:

```text
"id_nom_beca"
"beca"
```

`n_input` deberá permanecer en su posición actual. No se añadirá el argumento
`permitirEliminar` y se preservará el formato de una sola línea.

## 11. Contrato objetivo

Después de la futura corrección:

```javascript
propiedadId = "id_nom_beca";
propiedadEtiqueta = "beca";
idLista = list["id_nom_beca"];
etiquetaLista = list["beca"];
```

`cadenaMay(etiquetaLista)` recibirá la etiqueta textual real.

## 12. Categorías protegidas

Debe preservarse exactamente:

```text
bec_int → 1
bec_ext → 2
```

No son modificables `n_input`, la normalización, `tipo_beca`, el endpoint ni el
filtro SQL.

## 13. Componentes protegidos

No modificar:

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
- AT y Task de persistencia de `insertarList()`;
- SQL, configuración e infraestructura;
- Cursos y Tesis;
- todos los cambios locales y documentos no rastreados preexistentes.

No modificar `docs/TASKS.md` dentro de esta Task.

## 14. Defecto de edición fuera de alcance

El branch de edición de `ajax/beca.php` utiliza `$id_inst`, variable no definida
en el endpoint. El defecto permanece clasificado como:

```text
Preexistente
Independiente
Fuera de alcance
```

No debe corregirse ni utilizarse para ampliar esta Task. La VF inicial no deberá
probar guardado de edición.

## 15. Preservación EOL y EOF

Baseline verificado de `js/funcAjax.js`:

```text
Terminadores totales: 186
CRLF: 116
LF sin CR: 70
Línea objetivo: termina en CRLF
Newline final: Sí
```

El archivo posee EOL mixtos preexistentes. La futura implementación deberá:

- efectuar un reemplazo localizado;
- conservar CRLF en la línea objetivo;
- mantener los conteos globales equivalentes;
- preservar el newline final;
- no ejecutar formateador;
- no normalizar el archivo.

Si la herramienta altera EOL globalmente, deberá restaurarse el baseline antes
de finalizar.

## 16. Criterios de aceptación técnicos

La futura implementación será conforme solo si:

1. modifica exclusivamente `js/funcAjax.js`;
2. modifica exclusivamente una invocación;
3. la invocación pertenece al branch compartido de Beca;
4. añade exactamente `id_nom_beca` y `beca`;
5. conserva `n_input` en su posición;
6. no añade `permitirEliminar`;
7. mantiene intacta la implementación de `ajaxListas()` global;
8. mantiene intacto `ajaxSelect()`;
9. mantiene intacta `cadenaMay()`;
10. conserva endpoint, modelo, SQL y configuración;
11. conserva la Task backend;
12. conserva las categorías y su normalización;
13. conserva todos los demás consumidores;
14. conserva EOL, EOF y formato de una línea;
15. preserva los cambios locales ajenos;
16. mantiene staging vacío;
17. no crea archivos durante la implementación;
18. no crea commit;
19. no realiza push.

## 17. Validaciones técnicas futuras

Ejecutar como mínimo:

```text
git diff --check
git diff --check -- js/funcAjax.js
git diff -- js/funcAjax.js
git diff -U0 -- js/funcAjax.js
git status --short
git diff --cached --name-only
```

Además deberá confirmarse:

```text
Invocaciones modificadas: 1
Propiedad ID: id_nom_beca
Propiedad etiqueta: beca
ajaxListas global: intacto
ajaxSelect: intacto
cadenaMay: intacto
Backend: intacto
Beca.php: intacto
Task backend: intacta
Otros consumidores: intactos
```

También deberán contarse nuevamente CRLF/LF, comprobar el terminador de la línea
objetivo y confirmar el newline final.

## 18. Validación funcional futura

La VF corresponde exclusivamente al usuario.

Primera VF:

1. abrir «Listas predefinidas»;
2. abrir «Becas Internas»;
3. confirmar que la lista carga;
4. confirmar nombres correctos;
5. confirmar ausencia del error asociado a `undefined.split`;
6. no crear registros;
7. no guardar edición;
8. abrir «Becas Externas»;
9. confirmar que la lista carga;
10. confirmar nombres correctos;
11. confirmar ausencia de errores JavaScript;
12. no crear registros;
13. no guardar edición;
14. confirmar que no se habilitó eliminación.

Si esta VF es aprobada, podrá reanudarse
`TASK-EPIC008-BECA-INSERTARLIST-ESCRITURA-001`, sujeta a sus propias
precondiciones.

## 19. Reversión

Restaurar exclusivamente:

```javascript
ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input);
```

No modificar ningún otro elemento. Reversibilidad: alta.

## 20. Riesgos y controles

| Riesgo | Severidad | Control |
|---|---|---|
| R1 — Modificar `ajaxListas()` global | Alta | Prohibido; adaptar exclusivamente el consumidor. |
| R2 — Modificar `cadenaMay()` | Alta | Prohibido; entregar la etiqueta asociativa correcta. |
| R3 — Propiedad ID incorrecta | Alta | Usar exclusivamente `id_nom_beca`. |
| R4 — Propiedad etiqueta incorrecta | Alta | Usar exclusivamente `beca`. |
| R5 — Alterar ambas categorías | Media | Una llamada compartida y VF en ambas categorías. |
| R6 — Alterar normalización | Alta | Proteger `n_input`, `bec_int` y `bec_ext`. |
| R7 — Activar edición defectuosa | Media | VF limitada a lectura; guardado fuera de alcance. |
| R8 — Interferir con Task backend | Alta | Proteger modelo y documentos de persistencia. |
| R9 — Afectar otro consumidor | Alta | Diff `-U0` limitado a una invocación. |
| R10 — Normalizar EOL | Media | Comparación explícita del baseline mixto. |

Riesgo global esperado: bajo con controles.

## 21. Condiciones de detención

La futura implementación deberá detenerse sin modificar archivos si:

1. la rama no es `refactor/fase-0-seguridad`;
2. HEAD local y remoto divergen;
3. staging no está vacío;
4. `js/funcAjax.js` contiene cambios locales;
5. el AT fuente no está disponible o su huella difiere;
6. la invocación ya fue corregida;
7. no existe exactamente una invocación objetivo compartida;
8. el backend no devuelve `id_nom_beca` o `beca`;
9. `ajaxListas()` no posee el contrato documentado;
10. se requiere modificar `cadenaMay()`, `ajaxListas()` global o `ajaxSelect()`;
11. se requiere modificar endpoint, modelo o SQL;
12. se requiere cambiar `bec_int`, `bec_ext` o `n_input`;
13. se requiere añadir eliminación;
14. se requiere modificar la Task backend;
15. se requiere corregir `$id_inst`;
16. se requiere modificar más de una invocación;
17. se requiere modificar otro consumidor;
18. se requiere tocar Cursos o Tesis;
19. se requiere una decisión arquitectónica nueva;
20. la fuente resulta insuficiente o contradictoria;
21. no pueden preservarse EOL y EOF.

Ante una detención no deberá modificarse ningún archivo. El informe deberá
registrar condición, evidencia, invocación, contrato backend, EOL inicial,
discrepancia, riesgo, alternativa mínima, archivos modificados, staging, commit
y push.

## 22. Trazabilidad

```text
Baseline de listas de Beca fallido
→ INSPECCIÓN-EPIC008-VF-BECA-LISTA-001
→ AT-EPIC008-BECA-LISTA-ASOCIATIVA-001
→ TASK-EPIC008-BECA-LISTA-ASOCIATIVA-001
→ futura revisión técnica
→ futura implementación
→ VF de lectura
→ reanudación TASK-EPIC008-BECA-INSERTARLIST-ESCRITURA-001
```

## 23. Auditoría metodológica

- [FRONT] La futura modificación se limita al consumidor frontend.
- [CONTRACT] Se declara el contrato asociativo existente.
- [IMPL] Se define una transformación exacta aún no ejecutada.
- [GOV] Inspección y AT preceden a la Task.
- [VF-BLOCKER] La carga debe validarse antes de reanudar persistencia.
- [REV] La reversión restaura una invocación de cuatro argumentos.
- No se salta directamente a implementación.
- No se mezclan correcciones de lectura, edición y escritura.

## 24. Estado y dictamen

```text
Estado:
Aprobada para revisión técnica previa a implementación
```

**A. Task completa y lista para revisión técnica.**

La Task contiene objetivo único, fuente con huella verificada, transformación
exacta, alcance, protecciones, criterios de aceptación, validaciones, VF,
reversión, riesgos y condiciones de detención. Su creación no autoriza aún la
implementación.

## 25. Resumen de autorización futura

```text
Task creada: TASK-EPIC008-BECA-LISTA-ASOCIATIVA-001
Estado: Aprobada para revisión técnica previa a implementación
Archivo futuro modificable: js/funcAjax.js
Branch: n_input == "bec_ext" || n_input == "bec_int"
Invocación modificable: ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input);
Propiedad ID: id_nom_beca
Propiedad etiqueta: beca
Cantidad invocaciones: 1
Afecta internas: Sí
Afecta externas: Sí
ajaxListas global modificable: No
cadenaMay modificable: No
Backend modificable: No
Modelo modificable: No
Task backend modificable: No
Defecto editar incluido: No
VF inicial sólo lectura: Sí
EOL baseline preservable: Sí, con reemplazo localizado y comprobación explícita
Código modificado: No
Otros documentos modificados: No
Staging modificado: No
Commit creado: No
Push realizado: No
```
