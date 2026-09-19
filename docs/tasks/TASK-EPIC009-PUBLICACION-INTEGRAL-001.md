# TASK-EPIC009-PUBLICACION-INTEGRAL-001

## Estado

**CERRADA / VF APROBADA.**

- **EPIC:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Objeto:** Publicación.
- **Clasificación:** [ARQ] [INST] [DATA] [SEC] [TX] [GOV] [VF].
- **Tipo:** Task técnica integral; no dividir CREATE, READ, UPDATE, DELETE,
  participación, editorial, seguridad ni migración en micro-Tasks.

## Cierre vigente

La implementación y la VF integral están aprobadas por el usuario. Publicación
queda lista para cierre/commit con el siguiente estado final:

- Artículo/Revista y Libro: CRUD completo, con CREATE/UPDATE transaccionales,
  autoría principal y editoriales de Libro.
- Otra Publicación: CRUD completo e independiente del modelo Autor/Coautor.
- Autoría: exactamente un AUTOR y un COAUTOR; cada rol usa usuario académico
  interno o `nombre_externo`, nunca ambos. El usuario de ficha ocupa uno de los
  dos roles; otros coautores permanece como texto libre.
- Estudiante/Profesor: CREATE y READ según contexto académico/participación;
  sin UPDATE ni DELETE. Admin/Comité: administración global de CRUD.
- Autor/Coautor no concede mutación; `publicacion.usuario` es histórico,
  nullable y no expresa propiedad ni autorización.
- Las operaciones independientes de participación fueron retiradas del contrato
  vigente (HTTP 410); la autoría se modifica junto con la Publicación.
- El grid expone Autor, Coautor, otros coautores y editoriales, preserva
  capitalización y limita Editar/Eliminar a Admin/Comité.
- Las dos migraciones fueron ejecutadas manualmente y verificadas; no se deben
  reejecutar ni se realizó backfill histórico.

Las secciones de planificación posteriores se preservan por trazabilidad. Este
cierre vigente prevalece sobre cualquier estado pendiente o flujo de
participación individual allí descrito.

## Objetivo

Consolidar el objeto Publicación mediante un modelo explícito de participación,
CRUD seguro y transaccional, persistencia parametrizada, compatibilidad
incremental y validaciones. La VF corresponde al usuario.

## Fuente arquitectónica

Fuente obligatoria: `docs/architecture/AT-EPIC009-PUBLICACION-INTEGRAL-001.md`.
Esta Task no reinterpreta sus decisiones ni modifica ADR-003.

## Alcance

- Crear y migrar `publicacion_participacion`.
- Corregir persistencia, transacciones, backend, frontend, autorización,
  seguridad, editoriales y consumers confirmados de Publicación.
- Incorporar pruebas y matriz de VF para el objeto completo.

## Fuera de alcance

- Modificar `Authorization.php`, `Login.php`, ADR-003, objetos cerrados o
  `otra_publicacion` conceptualmente.
- Convertir actores externos u `otros_autores` en identidades.
- Eliminar `publicacion.usuario` prematuramente.
- Ejecutar limpieza sin inspección y justificación.

## Estado CRUD inicial

CREATE, READ, UPDATE y DELETE son defectuosos: falta autenticación/autorización
suficiente, hay acceso horizontal, asociación por usuario enviado por cliente,
SQL interpolado, CSRF ausente, XSS almacenado, escrituras no atómicas, contratos
incompletos, UPDATE parcial de editoriales y DELETE sin autorización ni
confirmación.

## Decisiones institucionales aplicables

Publicación es recurso académico compartido; participación != ownership. Admin
y Comité administran globalmente el recurso y actualizan la autoría junto con
él. Estudiante o Profesor autenticado puede crear y consultar en contexto
válido, pero no hace UPDATE ni DELETE. No existe CRUD individual vigente de
participación.

## Migración

Crear migración reversible para `publicacion_participacion` con PK
AUTO_INCREMENT, FK a `publicacion`, FK a `usuario`, rol obligatorio y
`UNIQUE(publicacion, usuario)`. La FK desde Publicación debe eliminar
participaciones al borrar el recurso. La FK de usuario será:

```text
FOREIGN KEY (usuario) REFERENCES usuario(id_usuario)
ON DELETE CASCADE
ON UPDATE CASCADE
```

Eliminar un usuario elimina exclusivamente sus participaciones, nunca la
Publicación. Restringir `rol` a AUTOR/COAUTOR.

Inspeccionar datos y constraints antes de modificar el schema. Detectar
duplicados de `libro_editorial(libro, editorial)`, resolverlos sólo si son datos
de prueba autorizados, crear `UNIQUE(libro, editorial)` y validar posteriormente
la constraint. No afectar editoriales.

## Modelo de participación

```text
publicacion_participacion
- id_publicacion_participacion
- publicacion -> publicacion.id_publicacion
- usuario     -> usuario.id_usuario
- rol         -> AUTOR | COAUTOR

UNIQUE(publicacion, usuario)
```

AUTOR y COAUTOR son mutuamente excluyentes. Una participación formal no admite
NULL, LEGACY, DESCONOCIDO ni PENDIENTE.

## Compatibilidad histórica

`publicacion.usuario` es histórico: no prueba AUTOR, COAUTOR, ownership,
creador autenticado ni autorización; tampoco es identidad confiable enviada por
cliente. No realizar backfill automático hacia ningún rol. Puede coexistir sólo
durante la transición y su permanencia o eliminación se decidirá según consumers
y compatibilidad incremental. Al cierre de la Task no se utilizará
funcionalmente para autorización, ownership, participación formal, READ por
ficha, CREATE, UPDATE ni DELETE; podrá permanecer físicamente como columna
legacy. Si su condición NOT NULL obliga a persistir una semántica falsa, la
migración podrá hacerla nullable.

Los datos reales/productivos se preservan. Los registros actuales de prueba de
`publicacion`, `articulo_revista`, `libro` y `libro_editorial` pueden conservarse
si es simple y seguro, o limpiarse controladamente si conservarlos exige
inventar roles, estados legacy, compatibilidad compleja o compromete integridad.
La Task debe justificar la opción. La limpieza nunca autoriza borrar `usuario`,
`editorial`, `estado_pub`, `indizacion`, catálogos compartidos ni otros objetos.
Considerar cascadas `publicacion -> articulo_revista`, `publicacion -> libro ->
libro_editorial`; editorial debe sobrevivir.

Antes de cualquier limpieza, revalidar el conjunto mediante conteos anónimos y
detener la ejecución si aparecen datos no verificables como prueba. Requerir
backup verificable y registrar conteos pre/post de las tablas afectadas y de los
catálogos protegidos.

## Persistencia y transacciones

Aplicar ADR-001: escrituras mediante `ejecutarEscritura`, verificando
explícitamente `filasAfectadas` e `idInsertado` según corresponda. Usar SQL
parametrizado; un resultado truthy no prueba escritura efectiva.

En INSERT, `idInsertado` debe ser válido cuando se cree una entidad con ID
generado. En DELETE de una entidad concreta, `filasAfectadas` debe ser 1. En
UPDATE, `filasAfectadas = 1` confirma cambio; `filasAfectadas = 0` es aceptable
sólo si el recurso existe y los valores enviados son idénticos. Distinguir
explícitamente inexistencia de ausencia de cambio.

CREATE artículo/revista es atómico: `publicacion + articulo_revista +
participación inicial`. CREATE libro es atómico: `publicacion + libro +
editorial nueva si corresponde + libro_editorial + participación inicial`.
UPDATE de libro/editoriales debe conservar `publicacion + libro +
libro_editorial` dentro de una transacción.

## Autorización

Derivar identidad en backend desde sesión, nunca desde `usuario` o `id_usuario`
enviado por cliente. CREATE deja autoría explícita al Estudiante/Profesor;
Admin/Comité puede crear globalmente en contexto válido. READ permite
fichas/perfiles legítimos sin IDOR. UPDATE y DELETE del recurso son exclusivos
de Admin/Comité; la autoría se reemplaza dentro del UPDATE del recurso y las
rutas independientes de participación están retiradas (410).

Autor/Coautor no otorga autoridad de mutación sobre el recurso. Estudiante y
Profesor pueden registrar y consultar Publicaciones según su contexto y
participación, pero UPDATE y DELETE del recurso requieren intervención de
Admin/Comité. `publicacion.usuario` no constituye propiedad ni autoridad.

## CREATE

Implementar los CREATE transaccionales definidos, validando identidad, rol, IDs
y cadenas. La participación inicial debe ser formal y válida. Estudiante/Profesor
selecciona explícita y obligatoriamente su rol inicial, AUTOR o COAUTOR; no hay
valor por defecto ni inferencia automática de AUTOR. Cuando Admin/Comité crea
con participante inicial, selecciona explícitamente usuario y rol.

## READ

Limitar READ al contexto propio, administrativo o necesario para fichas/perfiles;
no permitir lectura arbitraria por IDs manipulados. Separar exposición de recurso
y participación.

## UPDATE

Separar UPDATE del recurso de UPDATE de participación. Corregir sincronización
de `libro_editorial`, evitar duplicados y comprobar escrituras efectivas.

## DELETE

Exigir autorización backend y confirmación frontend para DELETE global. El
DELETE de Publicación respeta sus cascadas y elimina participaciones; DELETE de
participación no elimina recurso ni participaciones ajenas. Al eliminar la última
participación, la Publicación permanece y puede quedar con cero participantes;
nunca se elimina automáticamente el recurso.

## Editorial

`editorial` es catálogo compartido. Mantener autocompletado basado en
`id_editorial` y `nombre`, render seguro, SQL parametrizado y relaciones
`libro_editorial` sin duplicados. Nunca eliminar editorial al borrar Publicación.

## Otros autores

`publicacion.otros_autores` permanece textbox/textarea de texto libre. No
convertirlo en usuarios, participaciones, identidad ni permisos.

## otra_publicacion

Permanece separada de `publicacion_participacion`, sin fusión ni nuevas reglas
de participación ni AUTOR/COAUTOR. Si comparte endpoint, modelo o interfaz, su
CRUD debe preservarse sin regresión funcional ni reducción de seguridad, y debe
permanecer excluida del nuevo flujo de participación.

## Frontend

Revisar `formArtRev`, `formLibro`, `cargarPub`, edición, eliminación, cards,
listas, mensajes, selects, editorial, otros_autores, rol y participación.
Soportar AUTOR/COAUTOR interno, separar edición de recurso/participación, render
seguro, IDs únicos, sin formularios anidados, sin duplicados visuales, cache
busting real y compatibilidad con consumers actuales.

## Seguridad

Incluir autenticación y autorización backend, CSRF en CREATE/UPDATE/DELETE,
protección horizontal, validación de IDs y strings, JSON uniforme, render seguro
y no exposición de SQL/excepciones. El frontend no es fuente de autoridad.

## Archivos modificables

- `src/Model/Publicacion.php`
- `ajax/publicacion.php`
- `form-doc/scripts/publicacion.js`
- `form-doc/ficha.estudiante.php`
- `form-doc/ficha.docente.php`
- `form-doc/scripts/ficha.estudiante.js`
- `form-doc/scripts/ficha.docente.js`
- `form-doc/scripts/info.estudiante.js`
- `form-doc/scripts/info.docente.js`
- `form-doc/footer.php`
- La migración correspondiente.

Los consumers anteriores son condicionales: se modificarán sólo si la
inspección confirma uso real afectado por el nuevo contrato. Cualquier archivo
adicional requiere que se documente su consumer, el cambio mínimo y su relación
con Publicación, CSRF, render seguro o cache busting antes de modificarlo.

## Archivos protegidos

- `docs/architecture/ADR-003-RECURSOS-ACADEMICOS-COMPARTIDOS-PARTICIPACION.md`
- `files/prog_curso/curso_45afc744db0fb82f1db6f16d31ded67a.pdf`
- `Authorization.php`, `Login.php`, Beca, Pasantía, Grado, Postdoctorado,
  Institución y cualquier archivo ajeno.

## Plan de implementación

1. Inspeccionar schema, datos, consumers y staging; revalidar mediante conteos
   anónimos los datos de prueba, confirmar backup verificable y duplicados.
2. Definir DDL, rollback y estrategia justificada de conservación o limpieza.
3. Implementar modelo, persistencia transaccional y contratos backend seguros.
4. Integrar frontend y consumers; separar recurso/participación.
5. Validar técnicamente y preparar evidencia para VF del usuario.

## Validaciones técnicas

- `php -l` en cada PHP modificado.
- `git diff --check` y revisión de staging.
- Inspección de schema antes/después, constraints y cascadas.
- Validación de transacciones, roles, `UNIQUE(publicacion, usuario)` y
  duplicados `libro_editorial`.
- Pruebas aisladas si son posibles; usar Node sólo si ya está disponible, sin
  instalarlo.

## Matriz de VF

| Área | Casos mínimos a ejecutar por el usuario |
| --- | --- |
| CREATE | Artículo/revista y libro como Estudiante/Profesor; Admin/Comité global; participación inicial AUTOR y COAUTOR explícita; `otros_autores` libre. |
| READ | Propia, ficha válida, Admin/Comité y rechazo de acceso horizontal. |
| UPDATE | Estudiante/Profesor no edita; Admin/Comité edita recurso y autoría principal conjuntamente; editorial consistente. |
| DELETE | Estudiante/Profesor no elimina; Admin/Comité borra recurso; cascadas correctas y editorial sobrevive. |
| Seguridad | Anónimo rechazado, CSRF requerido, IDs manipulados rechazados y render seguro. |

## Riesgos

- Inventar roles históricos o convertir participación en ownership.
- Limpiar datos no identificados como prueba o afectar catálogos compartidos.
- Constraint de editoriales incompatible con datos existentes.
- Pérdida de atomicidad, IDOR, CSRF, SQL injection o XSS.
- Consumers no detectados o dependencia que requiera archivos protegidos.

## Reversión

La migración debe incluir rollback validado. Antes de limpieza autorizada,
definir respaldo y procedimiento reversible limitado al conjunto de prueba. Los
cambios de aplicación deben revertirse junto con contratos y consumers.

## Criterios de aceptación

- Participación explícita con sólo AUTOR/COAUTOR y unicidad por recurso/usuario.
- Sin backfill desde `publicacion.usuario`, que deja de tener uso funcional y
  puede permanecer físicamente como legacy; `otros_autores` libre.
- CRUD autorizado en backend, CSRF y protección horizontal efectivos.
- CREATE y UPDATE compuesto atómicos; SQL parametrizado y escritura verificada.
- Editorial preservada, `UNIQUE(libro, editorial)` validado, `otra_publicacion`
  separada y sin regresión, y compatibilidad evaluada.
- Validaciones técnicas completadas y VF entregada al usuario.

## Condiciones para detener ejecución

Detener si el schema contradice el AT; aparecen datos productivos donde sólo se
autorizaron datos de prueba; `UNIQUE(libro, editorial)` afecta datos no
identificados como prueba; no puede garantizarse atomicidad; se requiere
modificar `Authorization.php`/`Login.php`; aparecen consumers o dependencias no
contemplados; la migración inventa roles históricos; debe cambiarse
conceptualmente `otra_publicacion`; o existe staging previo ajeno.
