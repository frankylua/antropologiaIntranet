# AT-EPIC009-PUBLICACION-INTEGRAL-001

## Estado

**APROBADO — IMPLEMENTADO — VF APROBADA — CERRADO.**

- **EPIC:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Objeto:** Publicación.
- **Clasificación:** [ARQ] [INST] [DATA] [SEC] [TX].
- **Nivel:** L3 — formalización arquitectónica/técnica.

Este AT conserva la decisión arquitectónica y su cierre técnico. La
implementación, las migraciones y la VF integral ya fueron ejecutadas y
aprobadas; ADR-003 no fue modificado.

## Cierre vigente

- Publicación quedó consolidada como objeto integral EPIC-009.
- Artículo/Revista y Libro tienen CRUD completo, transaccional y autorizado.
- Otra Publicación tiene CRUD completo, independiente de Autor/Coautor.
- Autoría principal exige exactamente AUTOR y COAUTOR; cada posición es interna
  XOR externa, y el usuario académico de la ficha ocupa exactamente una.
- Estudiante/Profesor puede CREATE y READ en su contexto académico o de
  participación, pero no UPDATE ni DELETE. Admin/Comité administra globalmente
  CREATE, READ, UPDATE y DELETE.
- Autor/Coautor y `publicacion.usuario` no conceden autoridad. El último es
  histórico/nullable y no representa propiedad.
- Las migraciones de participación y autoría fueron ejecutadas manualmente y
  verificadas; no requieren reejecución. No hubo backfill histórico.
- La VF integral fue aprobada por el usuario para los tres tipos, grid,
  autoría, CREATE, UPDATE, DELETE y cancelación.

Las secciones de planificación que siguen se preservan como trazabilidad de la
decisión inicial; este cierre vigente prevalece sobre sus referencias futuras.

## Contexto

Publicación es un recurso académico compartido. La participación no equivale a
ownership ni concede administración global. La inspección integral confirmó que
CREATE, READ, UPDATE y DELETE actuales son defectuosos: el endpoint confía en
IDs enviados por cliente, carece de controles backend suficientes y conserva
contratos de persistencia incompletos.

## Evidencia confirmada

El schema activo MySQL/MariaDB usa InnoDB y confirma:

```text
publicacion(id_publicacion AUTO_INCREMENT, nombre, otros_autores NULL,
            anio, estado, usuario)
  estado  -> estado_pub.id_est_pub  CASCADE/CASCADE
  usuario -> usuario.id_usuario     CASCADE/CASCADE

articulo_revista.publicacion -> publicacion.id_publicacion CASCADE/CASCADE
articulo_revista.indizacion  -> indizacion.id_ind          CASCADE/CASCADE
libro.publicacion            -> publicacion.id_publicacion CASCADE/CASCADE
libro_editorial.libro        -> libro.id_libro             CASCADE/CASCADE
libro_editorial.editorial    -> editorial.id_editorial     CASCADE/CASCADE
otra_publicacion.usuario     -> usuario.id_usuario         CASCADE/CASCADE
```

`libro_editorial` no tiene `UNIQUE(libro, editorial)`. `editorial.nombre` no es
único. No hay triggers ni procedures/functions que afecten estas tablas.

## Problema

El modelo heredado usa `publicacion.usuario` como referencia histórica y
`otros_autores` como texto libre. No existe una relación persistente de
participación interna ni una separación verificable entre recurso global,
identidad autenticada, rol de participante y autorización.

También permanecen confirmados SQL interpolado, XSS almacenado por rendering
HTML inseguro, CSRF ausente, acceso horizontal, CREATE compuesto no atómico,
actualización incompleta de editoriales y DELETE sin autorización ni
confirmación.

## Decisiones institucionales

- Publicación es un recurso académico compartido.
- Participación != ownership; participar no concede administración global.
- Estudiante o Profesor autenticado puede crear y consultar Publicaciones en su
  contexto académico o de participación, pero no puede UPDATE ni DELETE.
- Admin y Comité administran globalmente el recurso; la autoría principal se
  modifica junto con la Publicación, no mediante CRUD independiente.
- Los actores externos se mantienen descriptivos, sin identidad ni permisos.
- DELETE de participación != DELETE de Publicación.

## Modelo de participación

Se incorporará conceptualmente la relación:

```text
publicacion_participacion
- id_publicacion_participacion
- publicacion -> publicacion.id_publicacion
- usuario     -> usuario.id_usuario
- rol         -> AUTOR | COAUTOR

UNIQUE(publicacion, usuario)
```

AUTOR y COAUTOR son mutuamente excluyentes para un mismo usuario dentro de una
Publicación. La restricción única garantiza una sola participación interna y,
por tanto, un solo rol por usuario/recurso. El SQL físico, nombres finales,
índices y política detallada de FK se resolverán en la Task/migración.

## Reglas de autorización

La futura implementación debe decidir en backend con identidad de sesión,
acción, recurso y relación persistente; nunca con `usuario`, `id_publicacion`,
`id_libro` u otro ID confiado al cliente.

- Estudiante/Profesor: CREATE y READ en contexto válido, sin UPDATE ni DELETE.
- Admin/Comité: CRUD global del recurso y actualización de su autoría principal
  dentro de la misma operación.
- La edición global no se infiere desde participación.
- READ debe limitarse al contexto funcional y la exposición necesaria para
  fichas/perfiles, sin lectura arbitraria por manipulación de IDs.

## Otros autores

`publicacion.otros_autores` se mantiene como texto libre editable mediante el
textbox existente. Es información descriptiva: no representa identidad,
participación interna, usuario ni ownership. No se estructuran actores externos
ni se crean usuarios automáticamente en esta etapa.

## CREATE

CREATE artículo/revista será una unidad transaccional:

```text
publicacion + articulo_revista + participación inicial
```

CREATE libro será una unidad transaccional:

```text
publicacion + libro + editorial nueva si corresponde
+ libro_editorial + participación inicial
```

Ante cualquier fallo, la operación compuesta debe revertirse íntegramente.

## READ

READ debe respetar identidad autenticada, contexto funcional, reglas de
autorización y la exposición necesaria para fichas/perfiles. No puede existir
lectura arbitraria por manipulación de `usuario`, `id_publicacion`, `id_libro`
u otros IDs proporcionados por cliente.

## UPDATE

UPDATE del recurso y de su autoría principal se realiza de forma conjunta y
transaccional por Admin/Comité. Estudiante/Profesor no modifica el recurso ni
la autoría mediante endpoints independientes; éstos permanecen retirados (410).

## DELETE

DELETE global de Publicación es una operación explícitamente autorizada para
Admin/Comité. Las cascadas actuales eliminan artículo/revista, libro y las
relaciones libro-editorial correspondientes; no eliminan las editoriales.

La relación de participación depende coherentemente de Publicación para que el
DELETE global elimine sus participaciones. No existe DELETE individual vigente
de participación.

## Editorial

`editorial` es un catálogo compartido, no propiedad exclusiva de Publicación.
Eliminar Publicación no debe eliminarla. Al eliminar un libro, sus relaciones
`libro_editorial` desaparecen por CASCADE.

La consolidación deberá impedir duplicados lógicos de `(libro, editorial)`; la
solución física se define posteriormente.

## otra_publicacion

`otra_publicacion` es un objeto separado, con PK, FK y ciclo de vida propios;
no tiene FK hacia `publicacion`. Queda fuera del modelo de participación y no
debe fusionarse ni incluirse en la migración sin una decisión posterior
específica. Su CRUD podrá incorporarse a una Task de interfaz sólo si ésta lo
declara expresamente.

## Persistencia y transacciones

Se aplicará ADR-001. Las escrituras usarán `ejecutarEscritura` con evaluación
explícita de `filasAfectadas` e `idInsertado`, según corresponda. Las
operaciones compuestas usarán transacciones. SQL debe ser parametrizado; un
objeto truthy no constituye evidencia suficiente de éxito.

## Migración requerida

La migración futura deberá crear la relación explícita de participación y
conservar `otros_autores` como texto libre. No debe convertir automáticamente
ese texto en usuarios ni asociaciones.

`publicacion.usuario` no continuará siendo autoridad de ownership/autorización,
pero no se elimina automáticamente. Es un dato histórico cuya semántica no
demuestra AUTOR, COAUTOR, ownership ni creador autenticado; tampoco concede
autorización. No se realizará backfill automático desde ese campo hacia
`publicacion_participacion`, no se crearán roles LEGACY, DESCONOCIDO o
PENDIENTE y una participación formal no admitirá rol NULL. El modelo definitivo
mantiene exclusivamente AUTOR y COAUTOR con `UNIQUE(publicacion, usuario)`.

La preservación de datos reales/productivos y de sus asociaciones continúa
siendo la regla general de la modernización. Como excepción institucional
explícita, los registros históricos actuales de prueba de `publicacion`,
`articulo_revista`, `libro` y `libro_editorial` podrán conservarse
temporalmente sólo si ello es simple, seguro y no introduce ambigüedad
institucional, lógica legacy significativa ni compromete la integridad del
nuevo modelo.

Si conservar ese conjunto actual de prueba obliga a inventar AUTOR/COAUTOR,
introducir estados legacy, mantener compatibilidad compleja únicamente para
datos de prueba o comprometer la integridad, la futura Task podrá justificar y
ejecutar una limpieza controlada. La elección entre conservación temporal y
limpieza no requiere nueva decisión institucional si se limita a esta excepción
y privilegia menor complejidad, integridad, seguridad, reversibilidad y ausencia
de impacto sobre datos ajenos. No constituye una política general para datos
históricos productivos.

Antes de una eventual limpieza deberán considerarse expresamente las cascadas:
eliminar `publicacion` alcanza `articulo_revista`, `libro` y, desde éste,
`libro_editorial`. La autorización no permite eliminar `usuario`, `editorial`,
`estado_pub`, `indizacion`, otros catálogos compartidos ni objetos ajenos a
Publicación; las editoriales deben sobrevivir aunque queden sin relaciones. La
permanencia transitoria o eliminación física de `publicacion.usuario` se
resolverá en la futura Task según sus consumers y la compatibilidad incremental.

## Seguridad

La futura Task debe incluir autenticación y autorización backend, derivación de
identidad desde sesión, prevención de acceso horizontal, CSRF en CREATE/UPDATE/
DELETE, validación backend, SQL parametrizado, render seguro y contrato JSON
consistente. No debe confiar en `usuario` enviado por cliente.

`Authorization.php` y `Login.php` quedan fuera de alcance salvo decisión
posterior explícita.

## Archivos/áreas potencialmente afectadas

- `ajax/publicacion.php`
- `src/Model/Publicacion.php`
- `form-doc/scripts/publicacion.js`
- fichas y consumers de `cargarPub`
- schema/migración y pruebas de integración del objeto

## Exclusiones

Quedan fuera de este AT: implementación, SQL definitivo, migración ejecutable,
cambios a ADR-003, ROADMAP, TASKS.md, Authorization.php o Login.php; una nueva
regla para `otra_publicacion`; estructurar actores externos; y eliminar
`publicacion.usuario`.

## Riesgos

- interpretar participación como autoridad global;
- inventar o atribuir incorrectamente un rol al migrar `publicacion.usuario`;
- conservar datos de prueba ambiguos mediante deuda legacy permanente;
- DELETE global no evaluado frente a sus cascadas;
- duplicados de relaciones libro/editorial;
- inconsistencias si CREATE compuesto no es transaccional;
- IDOR, CSRF, SQL injection y XSS si se conserva el contrato heredado.

## Criterios para derivar Task

La Task deberá definir el DDL exacto y rollback, estrategia de migración de
datos y la justificación de conservar temporalmente o limpiar controladamente
el conjunto actual de prueba de Publicación. También deberá definir contratos
HTTP/JSON, autorizaciones backend por operación, sincronización de editoriales,
transacciones, validación, render seguro y pruebas de recurso, participación,
roles, CSRF, IDOR y DELETE en cascada.

## Decisiones clasificadas

- **[ARQ]** Separación recurso / participación / autorización.
- **[INST]** Roles AUTOR/COAUTOR mutuamente excluyentes.
- **[DATA]** Nueva relación persistente Publicación ↔ Usuario con
  `UNIQUE(publicacion, usuario)` y excepción de limpieza controlada para el
  conjunto actual de datos de prueba.
- **[SEC]** Participación no concede ownership ni administración global;
  `publicacion.usuario` histórico no concede autorización.
- **[GOV]** Decisión institucional posterior sustituye parcialmente la
  preservación absoluta para el conjunto actual de prueba de Publicación, sin
  alterar la regla general aplicable a datos productivos.
- **[TX]** CREATE compuesto requiere atomicidad.

## Relación con ADR-003

Este AT aplica el criterio de recursos académicos compartidos y participación
de ADR-003. No modifica ese ADR ni resuelve su estado documental.

## Estado final

Decisión implementada y cerrada técnicamente; la documentación queda lista para
el commit de Publicación.
