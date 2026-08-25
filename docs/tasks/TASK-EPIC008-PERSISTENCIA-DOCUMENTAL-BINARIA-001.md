# TASK-EPIC008-PERSISTENCIA-DOCUMENTAL-BINARIA-001

## 1. Estado

~~~text
Task: CERRADA
Implementación: COMPLETADA
MIG-1: APLICADA Y VALIDADA
MIG-2: APLICADA Y VALIDADA
Backup/restore BLOB: APROBADO
Ensayo aislado de rollback: APROBADO
Validación funcional integrada: APROBADA POR EL USUARIO
Rollback real: NO REQUERIDO
~~~

### 1.1. Evidencia mínima de cierre

- Implementación completada: tabla `documento` con LONGBLOB, límite backend de
  5 MiB, MIME `application/pdf`, SHA-256 y coherencia entre tamaño y
  `OCTET_LENGTH`; Curso usa `id_documento_programa` y el binding PDO LOB/NULL
  fue incorporado retrocompatiblemente.
- MIG-1 aplicada y validada: tabla Documento, FK/UNIQUE, `arch_prog` nullable y
  postflight estructural aprobados.
- MIG-2 aplicada y validada: 2 Cursos candidatos iniciales, 1 PDF migrado, 1
  referencia faltante conservada, 0 errores, 0 duplicados y huérfanos no
  importados.
- Estado funcional posterior a la VF: 3 Cursos, 2 Documentos, 2 asociados y 1
  Curso histórico sin Documento; corresponde a evolución funcional posterior,
  no a una divergencia de MIG-2.
- Deuda histórica aceptada y no bloqueante: Curso 2 conserva
  `id_documento_programa = NULL` y una referencia filesystem inexistente; el
  fallback responde controladamente y no fabrica Documento.
- Backup post-VF creado con `--single-transaction`, `--quick`, `--hex-blob` y
  packet 16M fuera de Git/webroot; restore aislado aprobado con BLOB
  equivalentes en tamaño y checksum.
- Ensayo aislado de rollback aprobado con `--database=<schema>` y
  `--output-dir=<ruta>`: 2 Documentos procesados y 2 PDFs equivalentes en MIME,
  tamaño y SHA-256; Documento, FK y BLOB preservados; `arch_prog` actualizado
  sólo en BD aislada; BD/filesystem activos intactos y cleanup completo.
- Criterio de reversión satisfecho; rollback real no requerido.
- Validación funcional integrada: **APROBADA POR EL USUARIO**.

Esta Task constituye una única unidad de implementación bajo EPIC-008. No se
divide en micro-Tasks de schema, modelo, Curso, migración, infraestructura,
backup, restore, fallback o rollback.

Su creación documental no autoriza por sí sola a modificar código, schema,
configuración o base de datos. La ejecución futura requiere revisión técnica y
cumplimiento de los prerrequisitos definidos en este documento.

## 2. Identificación

- **TASK:** TASK-EPIC008-PERSISTENCIA-DOCUMENTAL-BINARIA-001.
- **EPIC:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia.
- **Coordinación:** EPIC-001, EPIC-003, EPIC-004 y EPIC-009.
- **AT fuente:** AT-EPIC008-PERSISTENCIA-DOCUMENTAL-BINARIA-001.
- **AT coordinado:** AT-EPIC003-AUTORIZACION-CURSOS-001.
- **Task coordinada:** TASK-EPIC001-AUTORIZACION-CURSOS-INTEGRAL-001.
- **DFA:** DFA técnico Documento aprobado para creación de Task.
- **ADR aplicable:** ADR-001 — Contrato explícito para operaciones de escritura.
- **ADR adicional:** no requerido.
- **Clasificación:** [TASK] [DB] [PERSIST] [BLOB] [MIG] [CRUD] [SEC] [REV] [OPS] [VF].
- **Nivel:** L3 — Gobierno de persistencia / preparación de implementación.

## 3. Baseline de creación

~~~text
Rama: refactor/fase-0-seguridad
HEAD: 109d6c2603176f3c0cd96e80bd3c1a2ab0dccbf5
Staging: vacío
~~~

El worktree contiene cambios conocidos de la implementación técnica de Cursos,
el AT de Cursos modificado, la Task integral de Cursos nueva, el AT de
Persistencia Documental nuevo y artefactos filesystem ya inventariados. Esta
Task debe preservar todos esos cambios y no atribuirlos a su propia creación.

Durante la inspección de creación se confirmó:

- no existe otra Task EPIC-008 equivalente;
- no existe una migración previa de `documento`;
- no existe tabla `documento` en el schema vivo;
- no existe ADR incompatible;
- el staging está vacío;
- no se detectaron anomalías ajenas al trabajo conocido.

El AT fuente está disponible en el worktree de este baseline y aún no posee un
commit independiente. No debe inventarse un commit documental de origen.

## 4. Autoridad y decisiones cerradas

Son decisiones cerradas y no reabribles durante la implementación:

- Documento es una entidad independiente;
- Documento no pertenece a Profesor;
- el contenido binario se almacena en MariaDB;
- `archivo` utiliza `LONGBLOB`;
- el máximo funcional es 5 MiB, equivalentes a 5.242.880 bytes;
- el backend es autoridad del límite;
- no existe versionado histórico documental en esta etapa;
- el ownership procede del objeto consumidor;
- Curso es el primer consumidor;
- Curso referencia Documento mediante FK explícita;
- la migración es incremental y no destructiva;
- no se importan, asocian ni eliminan huérfanos;
- no se fabrica contenido para referencias faltantes;
- no se utiliza dual-write;
- el filesystem se conserva sólo para fallback y reversión transitorios.

El DFA técnico aprobado es autoridad para los tipos SQL, constraints,
configuración mínima, patrón PDO, fases migratorias, fallback, cutover,
reversión y backup/restore registrados en esta Task.

## 5. Objetivo único

Implementar la entidad persistente `documento` e integrar el programa PDF de
Curso como su primer consumidor, de manera que:

~~~text
upload autorizado
→ validación PDF y máximo 5 MiB
→ Documento en MariaDB
→ FK desde Curso
→ descarga autorizada por id_curso
~~~

La unidad incluye schema coexistente, modelo Documento, integración del CRUD
Curso, migración de la única referencia histórica válida, fallback temporal,
cutover sin dual-write, reversión verificable y capacidad probada de
backup/restore.

## 6. Fuera de alcance

Quedan excluidos:

- almacenar documentos dentro de Profesor;
- crear una tabla puente o relación polimórfica;
- agregar consumidores distintos de Curso;
- deduplicar mediante checksum;
- imponer versionado histórico;
- importar o borrar PDFs huérfanos;
- buscar reemplazos para el PDF faltante;
- ejecutar MIG-3;
- retirar `curso.arch_prog`;
- borrar `files/prog_curso` o su `.htaccess`;
- convertir la FK a `NOT NULL`;
- modificar `ConnectionAuthority`;
- crear una capa Service nueva;
- modificar reglas institucionales, matriz CRUD u ownership;
- modificar `my.ini`, `php.ini` o `.env` automáticamente;
- cerrar la Task integral de Cursos;
- crear ADR, addendum o micro-Tasks.

## 7. Modelo persistente objetivo

La entidad aprobada contiene exclusivamente:

| Columna | Tipo | Nulabilidad/default | Índice | Contrato |
| --- | --- | --- | --- | --- |
| `id_documento` | `INT(11)` | `NOT NULL AUTO_INCREMENT` | PK | Identidad persistente |
| `nombre_original` | `VARCHAR(255)` | `NULL DEFAULT NULL` | No | Nombre informativo, nunca ruta o autoridad |
| `mime_type` | `VARCHAR(127)` | `NOT NULL` | No | MIME validado mediante `finfo` |
| `tamanio` | `INT UNSIGNED` | `NOT NULL` | No | Bytes reales, entre 1 y 5.242.880 |
| `archivo` | `LONGBLOB` | `NOT NULL` | No | Contenido binario |
| `fecha_creacion` | `DATETIME` | `NOT NULL DEFAULT CURRENT_TIMESTAMP` | No | Creación sin arquitectura temporal nueva |
| `checksum_sha256` | `CHAR(64)` | `NOT NULL` | No | SHA-256 hexadecimal minúsculo |

`checksum_sha256` no es `UNIQUE` y no autoriza deduplicación. Dos consumidores
podrían contener bytes iguales sin compartir Documento.

## 8. DDL exacto autorizado para MIG-1

~~~sql
CREATE TABLE `documento` (
  `id_documento` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_original` varchar(255) DEFAULT NULL,
  `mime_type` varchar(127) NOT NULL,
  `tamanio` int unsigned NOT NULL,
  `archivo` longblob NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `checksum_sha256` char(64) NOT NULL,
  PRIMARY KEY (`id_documento`),
  CONSTRAINT `chk_documento_tamanio`
    CHECK (`tamanio` BETWEEN 1 AND 5242880),
  CONSTRAINT `chk_documento_archivo_tamanio`
    CHECK (octet_length(`archivo`) = `tamanio`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci
  ROW_FORMAT=DYNAMIC;
~~~

La relación inicial debe aplicar conceptualmente:

~~~sql
ALTER TABLE `curso`
  MODIFY COLUMN `arch_prog` varchar(45) NULL,
  ADD COLUMN `id_documento_programa` int(11) NULL DEFAULT NULL AFTER `arch_prog`,
  ADD UNIQUE KEY `uq_curso_documento_programa` (`id_documento_programa`),
  ADD CONSTRAINT `fk_documento_programa_curso`
    FOREIGN KEY (`id_documento_programa`)
    REFERENCES `documento` (`id_documento`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;
~~~

La implementación futura debe reproducir este DDL en una migración versionada
con preflight, postflight y rollback comentado; no debe ejecutarlo directamente
desde el endpoint ni desde el modelo.

## 9. Integridad de la relación Curso → Documento

La FK permanecerá nullable durante la coexistencia. El índice `UNIQUE` es
obligatorio porque cada Documento tiene ownership exclusivo de un Curso y no
puede compartirse accidentalmente.

`ON DELETE RESTRICT` impide eliminar un Documento aún referenciado. El flujo de
DELETE debe eliminar primero Curso y después Documento dentro de la misma
transacción. `ON UPDATE CASCADE` conserva la convención del schema para cambios
de identificador, aunque el PK autoincremental no deba actualizarse en el flujo
normal.

La conversión futura a `NOT NULL` sólo será admisible cuando:

- no existan Cursos con FK nula;
- el PDF faltante haya sido reemplazado por un upload real;
- no exista uso del fallback;
- backup y restore estén aprobados;
- la VF integral de Cursos esté aprobada;
- la ventana de reversión al filesystem esté cerrada.

## 10. Archivos autorizados para la implementación

Los nombres y ubicaciones definitivos de los artefactos nuevos son:

~~~text
migrations/TASK-DB-MIGRATION-DOCUMENTO-SCHEMA-001.sql
migrations/TASK-DB-MIGRATION-DOCUMENTO-DATOS-001.php
migrations/TASK-DB-MIGRATION-DOCUMENTO-ROLLBACK-FILESYSTEM-001.php
src/Model/Documento.php
docs/governance/PROCEDIMIENTO-OPERATIVO-PERSISTENCIA-DOCUMENTAL-BINARIA-001.md
~~~

Los archivos existentes autorizados para modificación futura son:

~~~text
src/Config/conexion.php
src/Model/Curso.php
ajax/curso.php
form-doc/scripts/curso.js
~~~

La autorización sobre `src/Config/conexion.php` se limita exclusivamente a la
evolución retrocompatible de `ejecutarEscritura()` definida en la sección 17.
No autoriza modificar `ConnectionAuthority`, atributos de conexión, buffering,
credenciales, funciones de lectura ni ninguna otra función o parte del archivo.

El siguiente archivo queda expresamente protegido y no se modifica en esta
implementación:

~~~text
form-doc/ver.curso.php
~~~

También permanecen protegidos `ConnectionAuthority`, Login, Docente, Profesor,
Estudiante, Usuario, Authorization, AT, roadmap, `my.ini`, `php.ini`, `.env`,
los PDFs huérfanos y `files/prog_curso/.htaccess`. El dump no se edita: su única
operación futura autorizada es el traslado de seguridad definido en la sección
29.

No se autoriza modificar otros archivos sin detener la implementación y
realizar una revisión técnica del nuevo alcance.

## 11. MIG-1 — Schema coexistente

La migración SQL de schema debe:

1. comprobar que `documento` no existe;
2. comprobar que `curso.id_documento_programa` no existe;
3. confirmar la forma vigente de `curso.arch_prog`;
4. comprobar que `SELECT @@check_constraint_checks` devuelve `1`;
5. crear `documento` con el DDL exacto aprobado;
6. volver nullable `curso.arch_prog`;
7. agregar la FK nullable, UNIQUE y acciones referenciales aprobadas;
8. comprobar tipos, engine, row format, charset, constraints e índices;
9. comprobar que todas las filas Curso siguen presentes y con FK nula.

Si `@@check_constraint_checks != 1`, MIG-1 se detiene. Se prohíbe deshabilitar
los CHECK para instalar o validar el schema.

MariaDB ejecuta DDL con commits implícitos; MIG-1 no debe simular atomicidad ni
envolver el DDL en `BEGIN`, `COMMIT` o `ROLLBACK`. Su secuencia exacta es:

1. completar backup y preflight sin mutaciones;
2. ejecutar `CREATE TABLE documento` como una unidad DDL;
3. verificar inmediatamente tabla, columnas, CHECK, engine y row format;
4. ejecutar el `ALTER TABLE curso` aprobado como una unidad DDL;
5. ejecutar el postflight completo de columna, índice, FK, acciones
   referenciales, conteos y FK nulas;
6. registrar cada paso y su resultado antes de continuar.

Si cualquier paso falla después de un DDL exitoso, el entorno queda en estado
parcial explícito: se detiene la migración, se prohíbe continuar con MIG-2 o
aplicación, se inspecciona el schema efectivo y se aplica manualmente el
rollback documentado que corresponda al último postflight aprobado. Nunca se
reintenta a ciegas ni se declara rollback transaccional de DDL.

El rollback pre-cutover debe estar documentado y sólo podrá ejecutarse después
de verificar que no existen escrituras nuevas dependientes de Documento. Debe
retirar FK, índice, columna y tabla en orden seguro, y sólo restaurar
`arch_prog NOT NULL` si no contiene valores nulos.

## 12. MIG-2 — Datos históricos

La migración de datos debe implementarse preferentemente como PHP CLI
versionado porque debe usar filesystem, `finfo`, hashing y PDO. No debe depender
de una petición HTTP ni de variables de sesión.

Por cada Curso con FK nula y referencia `arch_prog` no vacía debe:

1. validar que la referencia sea un basename seguro;
2. construir la ruta únicamente dentro de `files/prog_curso`;
3. comprobar confinamiento mediante ruta real y prefijo aprobado;
4. comprobar `is_file` y legibilidad;
5. determinar MIME real mediante `finfo`;
6. aceptar sólo `application/pdf` para Curso;
7. obtener tamaño real y exigir `1..5242880` bytes;
8. leer los bytes de manera acotada;
9. volver a comprobar `strlen`;
10. calcular SHA-256 hexadecimal minúsculo;
11. iniciar una transacción exclusiva para ese Curso;
12. bloquear y volver a comprobar que la FK continúa nula;
13. insertar Documento con `nombre_original = NULL`;
14. asociar `curso.id_documento_programa`;
15. ejecutar postflight de tamaño, checksum y FK;
16. confirmar la transacción.

Una excepción debe revertir sólo el Curso en procesamiento, producir código de
salida no exitoso y no dejar Documento huérfano. La salida del script debe
informar identificadores, clasificación y resultado sin imprimir bytes ni
datos sensibles.

La ejecución debe ser repetible de forma segura: una FK ya poblada no se
reimporta ni reemplaza automáticamente.

## 13. Estado histórico esperado

El entorno inspeccionado en el baseline conocido contiene:

- dos Cursos;
- una referencia PDF existente y migrable;
- una referencia cuyo archivo falta;
- tres PDFs físicos sin referencia persistida.

Resultado esperado de MIG-2:

| Caso | Acción | Resultado |
| --- | --- | --- |
| Referencia válida | Validar e importar | Documento asociado; `nombre_original=NULL` |
| Referencia faltante | No importar | FK permanece `NULL`; deuda explícita |
| PDF huérfano | No inspeccionar como candidato | Sin asociación, modificación ni eliminación |

El basename técnico heredado no representa evidencia del nombre original y no
debe exponerse como tal.

Estos conteos no son una expectativa ni un contrato para producción. No debe
asumirse que producción contiene dos Cursos, un PDF válido, un PDF faltante y
tres huérfanos. En cada ambiente, MIG-2 debe descubrir y reportar el estado
real. No puede borrar ni asociar archivos huérfanos automáticamente ni fabricar
Documentos para referencias faltantes.

## 14. MIG-3 — Incremento futuro excluido

MIG-3 no se crea ni ejecuta dentro de esta primera Task. Su incremento futuro
podrá contemplar:

- resolver toda FK nula con contenido real;
- convertir `id_documento_programa` a `NOT NULL`;
- retirar fallback y callers de `arch_prog`;
- eliminar `arch_prog`;
- retirar el filesystem documental y su `.htaccess`.

Cada retiro requiere autorización posterior y evidencia de que se cerraron
dependencias, restore y reversión.

## 15. Prerrequisitos de MariaDB por ambiente

Los valores aplicados y verificados en desarrollo no implican que
preproducción/staging, si existe, ni producción estén preparados. Antes de
publicar Documento en cualquier ambiente debe ejecutarse un preflight de
infraestructura independiente en ese ambiente.

Antes de ejecutar MIG-1, MIG-2, desplegar o activar código BLOB o ejecutar VF
deben comprobarse mediante consultas efectivas, después del restart:

| Variable | Actual inspeccionado | Gate operativo único |
| --- | ---: | ---: |
| `max_allowed_packet` | `1M` | `16M` obligatorio |
| `innodb_log_file_size` | `5M` por archivo | `64M` obligatorio |
| `innodb_log_buffer_size` | `8M` | `16M` obligatorio |
| `innodb_file_per_table` | `ON` | `ON` |
| `check_constraint_checks` | `1` | `1` obligatorio |

Los cinco valores son prerrequisitos operativos: deben estar efectivos y
verificados en el ambiente de destino antes de MIG-1, MIG-2, despliegue de
código BLOB o VF. No basta una configuración declarada ni evidencia obtenida
en otro ambiente. No existe un valor menor admisible ni una distinción
pendiente entre mínimo y recomendado.

Los cambios se realizan manualmente en el entorno conforme al procedimiento
operativo. Para MariaDB 10.4 local, la secuencia controlada es: respaldar la
configuración vigente, detener escrituras, detener MariaDB limpiamente,
confirmar que el proceso quedó detenido, aplicar los valores administrados bajo
`[mysqld]`, iniciar MariaDB, revisar el log de arranque y verificar los cinco
valores efectivos mediante `SHOW VARIABLES` o consultas equivalentes. Si el
servicio no inicia, se detiene el intento, se restaura la configuración previa
y se reinicia con ella; si tampoco inicia, se escala la incidencia. Se prohíbe
borrar, renombrar, reemplazar o manipular manualmente archivos `ib_logfile*`.

La Task no autoriza escribir `my.ini` desde código ni versionar la configuración
local. `my.ini` y la configuración del servicio MariaDB no necesariamente
forman parte del deploy Git: deben aplicarse mediante el procedimiento
operacional propio del servidor de destino, sin registrar secretos.

`max_allowed_packet=1M` es un bloqueo duro: la implementación no debe intentar
MIG-2 ni una escritura BLOB de 5 MiB mientras siga efectivo.

## 16. Configuración PHP por ambiente

La configuración inspeccionada es:

~~~text
upload_max_filesize = 40M
post_max_size = 40M
memory_limit = 512M
~~~

Es compatible con el contrato de Documento. No debe modificarse `php.ini`
global en esta Task. El endpoint debe imponer siempre el máximo exacto de
5 MiB, es decir, 5.242.880 bytes (`5242880` bytes), y no conservar 40 MiB como
límite funcional.

En cada ambiente, y especialmente en producción, el preflight debe verificar
como mínimo los valores efectivos de PHP:

~~~text
upload_max_filesize >= 6M
post_max_size >= 8M
memory_limit >= 128M
~~~

PHP puede permitir valores mayores; el backend continúa siendo la autoridad
del límite funcional exacto. `php.ini` no necesariamente forma parte del deploy
Git y debe aplicarse mediante el procedimiento operacional del servidor de
destino, sin incluir secretos.

El tamaño informado por el cliente no es autoridad. Deben comprobarse error de
upload, `is_uploaded_file`, tamaño real, MIME real, lectura completa y longitud
de los bytes.

## 17. Contrato PDO/BLOB

Se conserva la conexión singleton vigente:

- driver `pdo_mysql` con `mysqlnd`;
- `PDO::ATTR_ERRMODE = PDO::ERRMODE_EXCEPTION`;
- `PDO::ATTR_EMULATE_PREPARES = false`;
- consultas bufferizadas por defecto.

No se modifica `ConnectionAuthority` ni el buffering global. Se autoriza, en la
implementación futura, modificar únicamente `ejecutarEscritura()` en
`src/Config/conexion.php` con esta firma exacta y retrocompatible:

~~~php
function ejecutarEscritura(
    string $sql,
    array $parametros = [],
    bool $obtenerIdInsertado = false,
    array $tipos = []
): array
~~~

El tercer argumento conserva su significado booleano histórico. Por tanto,
siguen siendo válidas sin cambios las llamadas existentes con uno, dos o tres
argumentos, incluida `ejecutarEscritura($sql, $parametros, true)`. Los tipos se
incorporan sólo como cuarto argumento opcional.

El comportamiento exacto del helper es:

1. si `$tipos === []`, preservar literalmente la ruta vigente
   `$statement->execute($parametros)`;
2. si `$tipos !== []`, aceptar exclusivamente parámetros nombrados en ambos
   arrays, normalizando cada clave mediante la eliminación de un único `:`
   inicial opcional;
3. rechazar claves numéricas, nombres vacíos o inválidos y colisiones después
   de normalizar, incluida la presencia simultánea de `id` y `:id`;
4. exigir igualdad exacta entre el conjunto normalizado de claves de
   `$parametros` y el de `$tipos`, sin tipos faltantes ni sobrantes;
5. aceptar exclusivamente `PDO::PARAM_STR`, `PDO::PARAM_INT`,
   `PDO::PARAM_BOOL`, `PDO::PARAM_NULL` y `PDO::PARAM_LOB`;
6. validar estrictamente la coherencia valor/tipo: string para `PARAM_STR`, int
   para `PARAM_INT`, bool para `PARAM_BOOL`, `null` para `PARAM_NULL`, y string
   binario o recurso para `PARAM_LOB`;
7. ejecutar exactamente una vez
   `bindValue(':' . $nombreNormalizado, $valor, $tipo)` por parámetro;
8. finalizar la ruta tipada con `$statement->execute()` sin volver a entregar
   el array y sin duplicar bindings.

La ruta tipada no introduce soporte posicional ni orden implícito. Las llamadas
posicionales históricas continúan exclusivamente por la ruta no tipada. El
helper conserva sin cambios el retorno `filasAfectadas`/`idInsertado`, la
semántica de `$obtenerIdInsertado`, la propagación de excepciones y la ausencia
de control transaccional interno.

Para INSERT y UPDATE:

~~~text
upload validado
→ file_get_contents acotado
→ strlen
→ hash('sha256', bytes)
→ statement preparado
→ bindValue del archivo con PDO::PARAM_LOB
→ execute
~~~

PDO MySQL no debe tratarse como garantía de streaming LOB real. El diseño
acepta materializar un máximo de 5 MiB en memoria y debe medir el peak con
mysqlnd y concurrencia durante la revisión/VF técnica.

Con prepares nativos, el contrato aprobado para Documento es binding explícito:
`archivo => PDO::PARAM_LOB`; `nombre_original => PDO::PARAM_NULL` cuando sea
nulo o `PDO::PARAM_STR` cuando tenga valor; identificadores y tamaño
`PDO::PARAM_INT`; MIME y checksum `PDO::PARAM_STR`. La persistencia y lectura de
un BLOB de 5 MiB debe comprobarse en VF antes del cutover.

## 18. Modelo `Documento`

La futura implementación debe crear `src/Model/Documento.php` como entidad
independiente. Sus firmas autorizadas son:

~~~php
insertar(
    ?string $nombreOriginal,
    string $mimeType,
    int $tamanio,
    string $archivo,
    string $checksumSha256
): array

actualizar(
    int $idDocumento,
    ?string $nombreOriginal,
    string $mimeType,
    int $tamanio,
    string $archivo,
    string $checksumSha256
): array

obtenerMetadata(int $idDocumento, bool $bloquear = false): ?array
obtenerContenido(int $idDocumento): ?array
eliminar(int $idDocumento): array
~~~

Contratos obligatorios:

- SQL preparado y parámetros tipados;
- `PDO::PARAM_LOB` para el binario;
- INSERT y UPDATE ejecutados obligatoriamente mediante la ruta tipada de
  `ejecutarEscritura()`; se prohíbe ejecutar escrituras con PDO directo en
  `Documento`;
- retorno explícito de filas afectadas e ID insertado cuando corresponda;
- metadata separada del contenido;
- ningún `SELECT *`;
- ningún listado de BLOB;
- ningún BEGIN, COMMIT o ROLLBACK interno;
- ninguna autorización dentro del modelo;
- ninguna ruta filesystem dentro del modelo.

`Documento` y `Curso` utilizan el mismo objeto devuelto por `conexion()`. La
frontera transaccional permanece en `ajax/curso.php`, nivel coordinador ya
existente.

## 19. Integración del modelo `Curso`

`src/Model/Curso.php` debe evolucionar para:

- insertar `id_documento_programa` y permitir `arch_prog = NULL`;
- actualizar la FK únicamente cuando un Curso transitorio recibe su primer
  Documento;
- seleccionar FK y metadata estrictamente necesaria en detalle;
- mantener el BLOB fuera de listado, catálogo y detalle;
- bloquear Curso mediante `FOR UPDATE` cuando lo solicite el coordinador;
- preservar consultas preparadas y contratos explícitos de escritura.

No debe absorber SQL genérico de Documento ni controlar transacciones.

## 20. CREATE Curso

Se conserva la matriz de actores, CSRF, validación funcional, ownership y
validación del Profesor Aceptado. El flujo documental objetivo es:

~~~text
autorizar actor
→ validar payload y upload
→ comprobar tamaño, finfo, bytes y checksum
→ BEGIN
→ validar relaciones y ownership
→ INSERT documento
→ obtener id_documento
→ INSERT curso con FK y arch_prog = NULL
→ COMMIT
~~~

Una excepción antes del commit ejecuta exclusivamente `ROLLBACK`. No existen
nombre físico, temporal final, rename, cuarentena, unlink ni compensación de
filesystem.

## 21. UPDATE Curso

### Sin PDF nuevo

- no seleccionar ni materializar `documento.archivo`;
- no modificar Documento;
- actualizar sólo campos funcionales de Curso;
- conservar FK y metadata documental;
- si la FK es nula y tampoco existe fallback válido, exigir un PDF de reemplazo
  como en el comportamiento aprobado.

### Con PDF nuevo

~~~text
autorizar y validar upload
→ bytes/checksum
→ BEGIN
→ SELECT Curso FOR UPDATE
→ validar ownership y relaciones
→ SELECT Documento FOR UPDATE, si existe
→ UPDATE Documento vigente
   o INSERT Documento y asociar FK si aún no existe
→ UPDATE Curso
→ COMMIT
~~~

El mismo Documento se actualiza porque no existe versionado. Esto conserva la
FK, evita huérfanos y permite que el rollback transaccional restaure contenido
y metadata anteriores.

`arch_prog` no se escribe ni actualiza: queda congelado como compatibilidad
histórica.

## 22. DELETE Curso

El flujo obligatorio es:

~~~text
BEGIN
→ SELECT Curso FOR UPDATE
→ obtener id_documento_programa
→ DELETE Curso
→ si la FK no era NULL, DELETE Documento
→ COMMIT
~~~

El orden satisface `ON DELETE RESTRICT`. Si la FK es nula, se elimina sólo
Curso. No se busca, renombra ni elimina un supuesto archivo histórico o
huérfano. Una excepción revierte ambas eliminaciones mediante ROLLBACK.

## 23. READ, detalle y catálogo

Los listados, detalles y catálogo no pueden seleccionar `documento.archivo`.
Sólo podrán recuperar metadata cuando el contrato visible la necesite:

- disponibilidad;
- `nombre_original`;
- `mime_type`;
- `tamanio`.

`id_documento` no se expone como autoridad al frontend. Las operaciones
documentales continúan identificándose mediante `id_curso`.

## 24. Download autorizado

El único input autorizado es `id_curso`:

~~~text
cursos.ver
→ Curso por id_curso
→ id_documento_programa
→ metadata Documento
→ consulta exclusiva del BLOB
→ comprobación tamaño/MIME
→ headers controlados
→ salida
~~~

No se acepta `id_documento`, nombre ni ruta como autoridad cliente. Los headers
mínimos son:

~~~text
Content-Type: application/pdf
Content-Disposition: attachment
Content-Length
X-Content-Type-Options: nosniff
Cache-Control: private, no-store, max-age=0
~~~

Si `nombre_original` es nulo se utiliza:

~~~text
programa_curso_<id_curso>.pdf
~~~

Si existe, sólo se utiliza para presentación después de retirar CR/LF, NUL,
controles, separadores y comillas, limitar longitud y emitir un fallback ASCII
junto con `filename*` UTF-8 seguro. Nunca determina MIME, extensión efectiva,
ruta ni autorización.

La consulta PDO materializará hasta 5 MiB. La salida HTTP puede fragmentarse,
pero no debe describirse como streaming desde MariaDB.

## 25. Fallback transitorio

La regla es determinista:

~~~text
id_documento_programa IS NOT NULL
→ Documento BD es la única autoridad

id_documento_programa IS NULL
→ evaluar únicamente arch_prog heredado
~~~

El fallback sólo es legítimo si `arch_prog`:

- es un basename seguro;
- resuelve dentro de `files/prog_curso`;
- apunta a un archivo regular y legible;
- tiene MIME real `application/pdf`;
- contiene entre 1 y 5.242.880 bytes.

Una referencia faltante informa recurso no disponible. Nunca se buscan nombres
parecidos, se recorren huérfanos, se repara automáticamente ni se vuelve a
escribir `arch_prog`.

Durante la transición, `files/prog_curso` permanece disponible exclusivamente
para este fallback histórico. Debe conservar protección HTTP equivalente a
`Require all denied` o el control equivalente del servidor de destino. El
preflight debe verificar el mecanismo real que impide el acceso directo, sin
asumir que producción utiliza Apache o XAMPP.

## 26. Cutover y ausencia de dual-write

Desde el cutover, Documento BD es la única autoridad de escritura:

- CREATE nuevo inserta Documento y deja `arch_prog = NULL`;
- UPDATE con PDF actualiza o crea Documento;
- UPDATE sin PDF no toca Documento;
- ningún flujo nuevo escribe filesystem;
- `arch_prog` preexistente permanece congelado.

No se implementa dual-write porque mantendría dos autoridades, duplicaría
almacenamiento y reintroduciría divergencia y compensaciones no atómicas.

La coexistencia termina cuando no existan dependencias activas de fallback y se
hayan aprobado VF, backup, restore y reversión.

## 27. Reversión

### Antes del cutover

Los archivos existentes y `arch_prog` se conservan. Si aún no existen
escrituras sólo-BD, puede revertirse el código y, después de desasociar/eliminar
Documentos importados de manera controlada, revertir MIG-1.

### Después del cutover

Si ya existen CREATE o UPDATE sólo-BD, no existe rollback directo hacia el
código filesystem. Antes de revertir la aplicación debe ejecutarse un
procedimiento controlado mediante el artefacto independiente y versionado:

~~~text
migrations/TASK-DB-MIGRATION-DOCUMENTO-ROLLBACK-FILESYSTEM-001.php
~~~

Este script es exclusivamente CLI, no forma parte de MIG-2, no se ejecuta
durante la migración normal y no implementa dual-write. Debe operar por defecto
en modo `--dry-run`, sin
mutaciones, e imprimir el inventario y plan sin bytes ni datos sensibles. Sólo
puede escribir con un flag explícito `--execute`, después de backup, ventana de
mantenimiento y aprobación operativa.

En modo de ejecución debe:

1. inventariar Cursos y Documentos que requieren exportación;
2. omitir de forma explícita cualquier Curso que ya tenga un fallback válido y
   no sobrescribir archivos existentes;
3. generar basenames exclusivos del lado servidor;
4. exportar bytes mediante archivo temporal y publicación atómica dentro de
   `files/prog_curso`, con confinamiento de ruta y permisos aprobados;
5. comprobar tamaño, MIME real y SHA-256 contra la fila Documento;
6. repoblar `arch_prog` únicamente después de publicar y verificar el archivo,
   dentro de una transacción por Curso y sin eliminar Documento;
7. operar de forma idempotente, registrar el resultado por identificador y
   detener el Curso afectado ante cualquier divergencia;
8. comprobar la descarga mediante el flujo anterior;
9. producir un postflight sin FK sólo-BD pendiente de exportación;
10. recién entonces autorizar el rollback de aplicación.

El procedimiento no es dual-write permanente. Existe únicamente para la
ventana corta de reversión y debe probarse antes de considerar cerrado el
cutover.

## 28. Procedimiento operativo versionado

El documento operativo futuro se crea exactamente en:

~~~text
docs/governance/PROCEDIMIENTO-OPERATIVO-PERSISTENCIA-DOCUMENTAL-BINARIA-001.md
~~~

Debe contener cuatro bloques explícitos y separados, en este orden:

1. `DESARROLLO`;
2. `PREPRODUCCIÓN/STAGING SI EXISTE`;
3. `PRODUCCIÓN`;
4. `ROLLBACK`.

Dentro de cada bloque de ambiente que corresponda debe organizar, en este
orden, las siguientes etapas:

1. `PRECHECK`: versiones, Git, schema, variables efectivas, disco, rutas y
   ausencia de colisiones;
2. `SECURITY CHECK DEL DUMP`: localización de copias y retiro del dump conocido
   del alcance HTTP;
3. `BACKUP`: creación sensible fuera de Git y de todo DocumentRoot;
4. `AJUSTE MARIADB`: valores definitivos y ubicación administrada por entorno;
5. `RESTART`: detención limpia, inicio, log y procedimiento seguro de fallo;
6. `POSTCHECK INFRA`: comprobación de variables efectivas y salud del servicio;
7. `MIG-1`: ejecución, postflight, estados parciales y rollback DDL;
8. `MIG-2`: dry-run, ejecución, reporte, idempotencia y rollback por Curso;
9. `POSTFLIGHT`: constraints, conteos, bytes, checksums, FK y huérfanos;
10. `VF`: pruebas técnicas, funcionales, memoria y concurrencia;
11. `RESTORE AISLADO`: restauración y criterios de aceptación;
12. `ROLLBACK`: reversión pre-cutover y uso del script de exportación separado;
13. `CUTOVER`: orden de despliegue, fuente única, fallback y cierre de ventana.

El bloque `PRODUCCIÓN` debe poder ejecutarse como checklist operacional, debe
incorporar literalmente el orden obligatorio de producción definido en esta
Task y no puede depender de valores, rutas, servicios ni supuestos locales de
XAMPP.

El procedimiento debe prohibir guardar dumps o binarios sensibles en Git y
debe registrar comandos, resultados y responsables sin exponer contenido.

No se versionan `my.ini`, `php.ini`, `.env` ni necesariamente la configuración
del servicio MariaDB. Tampoco se presume que formen parte del deploy Git. Los
cambios de entorno se aplican mediante el procedimiento operacional del
servidor de destino; el procedimiento y sus verificaciones sí son
versionables. Nunca se registran secretos.

## 29. Backup y restore

Antes de MIG-1, y obligatoriamente en producción, se requiere un backup lógico
completo compatible con BLOB mediante `mariadb-dump` o `mysqldump`:

~~~text
--single-transaction
--quick
--hex-blob
--max-allowed-packet=16M
~~~

El dump debe almacenarse fuera del repositorio, fuera de Git y de todo
`DocumentRoot`, con acceso restringido, y nunca debe versionarse. Debe tratarse
como información sensible. Su mera creación no demuestra recuperabilidad: debe
existir un procedimiento de restore verificable y disponible antes de MIG-1.

Antes de cualquier implementación debe corregirse la exposición del dump
conocido `c1441353_antr_db.sql`: se mueve, sin leer ni mostrar su contenido, a
un `<BACKUP_DIR_PRIVADO>` que esté simultáneamente fuera del repositorio, fuera
de `C:\xampp\htdocs` y fuera de cualquier `DocumentRoot`, con acceso local
restringido. No basta con ignorarlo en Git.

Se prohíbe generar un dump nuevo con BLOB bajo `C:\xampp\htdocs`, dentro del
repositorio o en cualquier carpeta pública web.

El gate `SECURITY CHECK DEL DUMP` debe demostrar:

- ausencia del archivo original dentro del repositorio y del alcance HTTP;
- respuesta `403` o `404`, nunca `200`, para la URL antigua;
- ausencia del dump en Git tracked, staging y untracked del repositorio;
- existencia de la copia privada sólo si la política exige conservarla;
- ausencia de cualquier otra copia sensible `.sql` expuesta bajo un
  `DocumentRoot`; si aparece otra, se detiene la implementación.

Antes del cierre debe restaurarse en una base aislada y comprobar:

- creación de `documento`;
- constraints, FK y UNIQUE;
- conteos de Curso y Documento;
- `OCTET_LENGTH(archivo) = tamanio`;
- SHA-256 del contenido igual a `checksum_sha256`;
- asociación Curso→Documento;
- referencia faltante aún nula;
- descarga autenticada;
- ausencia de importación o eliminación de huérfanos.

## 30. Seguridad obligatoria

Se preservan:

- capacidad `cursos.ver`;
- matriz CRUD y ownership backend;
- CSRF en mutaciones;
- Profesor Aceptado y READ Estudiante/permiso histórico 3;
- MIME real mediante `finfo`;
- máximo backend de 5 MiB;
- SQL preparado;
- descarga por `id_curso` autorizado;
- Content-Disposition seguro;
- BLOB ausente de JSON, logs, listados y detalles.
- dumps con BLOB tratados como datos sensibles, almacenados exclusivamente
  fuera de Git y de todo `DocumentRoot`, con acceso local restringido.

La ruta nueva elimina operaciones de persistencia final basadas en path,
`move_uploaded_file`, `rename`, cuarentena y `unlink`. Permanecen como riesgos a
controlar: IDOR, autorización, contenido falso, payload grande, consumo de
memoria/conexiones, SQL, exposición accidental en logs/dumps y disponibilidad
del backup.

## 31. Código de Cursos que debe preservarse

De la implementación y Task integral de Cursos se preservan:

- matriz CRUD por actor;
- ownership de Profesor;
- capacidad `cursos.ver`;
- CSRF;
- validación de Profesor Aceptado;
- Estudiante READ y permiso histórico 3 READ;
- queries parametrizadas;
- contratos HTTP/JSON;
- frontend con render seguro y éxito sólo cuando `ok=true`;
- confirmación DELETE;
- rutas legacy retiradas mediante `410`.

Estas decisiones no se reabren ni se reinterpretan desde EPIC-008.

## 32. Código de Cursos que debe reemplazarse

En la ruta principal deben retirarse:

- constante y mensajes de 40 MiB;
- generación de nombres físicos;
- temporales usados como persistencia final;
- `move_uploaded_file` hacia almacenamiento final;
- `rename`, cuarentena, `unlink` y compensaciones filesystem;
- `arch_prog` como autoridad documental;
- disponibilidad calculada sólo desde filesystem;
- descarga mediante `fopen`/`fread` del archivo persistido.

Las funciones mínimas de resolución segura de `arch_prog` podrán mantenerse
aisladas únicamente para el fallback transitorio de una FK nula.

## 33. Contrato frontend visible

`form-doc/scripts/curso.js` debe imponer y comunicar 5 MiB, sin tratar la
validación cliente como autoridad. `form-doc/ver.curso.php` queda protegido y no
se modifica; el contrato visible se resuelve con el markup vigente y el script
autorizado.

No se expone `id_documento`, checksum ni contenido en el DOM o JSON. La descarga
continúa construyéndose desde `id_curso`.

## 34. Validación técnica y VF futura

### Infraestructura

- `max_allowed_packet=16M` efectivo en servidor y clientes pertinentes;
- `innodb_log_file_size=64M` efectivo;
- `innodb_log_buffer_size=16M` efectivo;
- `innodb_file_per_table=ON`;
- `@@check_constraint_checks=1` antes de MIG-1;
- dump conocido fuera de Git, repositorio y todo `DocumentRoot`, con URL antigua
  en `403` o `404`;
- backup creado fuera de Git;
- restore aislado aprobado.

### Documento

- INSERT de PDF válido;
- rechazo de vacío y de más de 5 MiB;
- aceptación del límite exacto de 5.242.880 bytes;
- rechazo de MIME declarado PDF con contenido falso;
- metadata, tamaño y checksum coherentes;
- CHECK de tamaño y longitud efectivo;
- ningún checksum `UNIQUE` ni deduplicación.

### CREATE Curso

- Curso y Documento se confirman en una transacción;
- fallo de Documento no crea Curso;
- fallo de Curso no deja Documento;
- `arch_prog` queda nulo;
- no se crea archivo físico.

### UPDATE

- sin PDF no consulta ni modifica BLOB;
- con PDF actualiza el Documento vigente;
- una FK nula recibe Documento nuevo;
- rollback conserva Documento y Curso anteriores;
- Curso sin Documento ni fallback exige reemplazo.

### DELETE

- elimina Curso antes de Documento;
- `ON DELETE RESTRICT` impide orden inverso;
- rollback restaura ambos;
- FK nula permite eliminar sólo Curso;
- filesystem y huérfanos permanecen intactos.

### READ y download

- listado, catálogo y detalle no recuperan BLOB;
- download sólo acepta `id_curso`;
- todos los actores conservan la matriz aprobada;
- headers, nombre, tamaño y contenido son coherentes;
- ausencia, corrupción o inconsistencia produce error inequívoco;
- memoria y concurrencia se miden con un Documento máximo.

### Migración y fallback

- sólo el PDF referenciado y válido se importa;
- su `nombre_original` queda nulo;
- la referencia faltante conserva FK nula;
- los tres huérfanos permanecen intactos;
- una segunda ejecución no duplica Documento;
- fallback sólo opera con FK nula y ruta válida;
- Documento BD siempre prevalece cuando existe FK.

### Reversión

- rollback pre-cutover probado;
- exportación Documento→filesystem validada para escrituras sólo-BD;
- no se promete reversión directa después del cutover.

### Post-deploy funcional de producción

- CREATE Curso con Documento;
- UPDATE Curso sin PDF;
- UPDATE Curso con PDF;
- DELETE Curso con su Documento;
- listado sin recuperar BLOB;
- descarga autorizada;
- rechazo de archivo mayor que 5 MiB;
- rechazo de MIME PDF falso;
- READ para permiso histórico 3;
- ownership de Profesor;
- CSRF en mutaciones;
- fallback legacy;
- PDF faltante controlado;
- archivos huérfanos intactos.

La VF funcional integral de Cursos será ejecutada por el usuario. La Task no se
cierra sin su aprobación explícita y sin la evidencia técnica de migración,
backup y restore.

## 35. Condiciones para autorizar implementación

La revisión técnica previa debe confirmar:

1. remediación del dump sensible y URL antigua en `403` o `404`;
2. gates efectivos `16M`/`64M`/`16M`, `innodb_file_per_table=ON` y
   `@@check_constraint_checks=1`;
3. nombres definitivos y ausencia de colisiones para MIG-1 y MIG-2;
4. contrato del rollback filesystem separado con `--dry-run` y `--execute`;
5. ubicación definitiva y contenido obligatorio del procedimiento operativo;
6. evolución exclusiva y retrocompatible de `ejecutarEscritura()` con el cuarto
   argumento `$tipos`;
7. conservación exacta de todas las llamadas históricas al helper;
8. binding nativo de `archivo` con `PDO::PARAM_LOB` y de nulos con
   `PDO::PARAM_NULL`;
9. firmas y retornos autorizados de `Documento.php`;
10. SQL exacto de Documento y Curso;
11. uso de la misma conexión PDO y frontera transaccional única en
    `ajax/curso.php`;
12. separación metadata/contenido, estrategia de memoria y fallback confinado;
13. backup y destino aislado de restore disponibles;
14. EOL LF, UTF-8 sin BOM y exactamente un salto final;
15. ausencia de nuevos cambios inesperados en el worktree.

## 36. Condiciones para detener la implementación

Debe detenerse si:

1. aparece una Task o migración Documento contradictoria;
2. el dump conocido o cualquier copia sensible continúa accesible bajo un
   `DocumentRoot`;
3. cualquiera de los gates `16M`/`64M`/`16M`/`ON` no está efectivo;
4. el entorno no permite configurar o validar InnoDB con margen suficiente;
5. la evolución de `ejecutarEscritura()` rompe una llamada histórica, altera el
   retorno o no valida exactamente el binding tipado;
6. PDO con prepares nativos no puede persistir y recuperar 5 MiB mediante
   `PDO::PARAM_LOB` dentro del límite de memoria aprobado;
7. backup o restore no preserva los binarios;
8. se descubre un consumidor documental adicional incompatible;
9. se requiere modificar `ConnectionAuthority` o crear una capa global nueva;
10. la migración incremental exige fabricar o atribuir contenido;
11. rollback exige dual-write indefinido o no puede ejecutarse con la herramienta
    separada aprobada;
12. cambia la matriz institucional, ownership o `cursos.ver`;
13. se requiere tocar un archivo fuera del alcance sin nueva revisión;
14. el estado Git presenta cambios inesperados o solapamiento no preservable.

## 37. Reversión de la implementación

La reversión debe respetar este orden:

~~~text
detener nuevas escrituras
→ determinar si hubo escrituras sólo-BD
→ crear y verificar backup
→ ejecutar primero el dry-run de TASK-DB-MIGRATION-DOCUMENTO-ROLLBACK-FILESYSTEM-001.php
→ aprobar y ejecutar su modo --execute
→ exportar, verificar tamaño/MIME/SHA-256 y repoblar arch_prog
→ confirmar cobertura de todos los Cursos requeridos
→ validar flujo filesystem anterior
→ revertir aplicación
→ comprobar conteos, referencias y archivos
→ desasociar/eliminar Documento sólo si corresponde y de manera controlada
→ revertir MIG-1 al final y sólo si no quedan dependencias
~~~

Nunca debe eliminarse primero la tabla, FK, columna o contenido sin demostrar
que el código activo y el rollback no dependen de ellos.

La herramienta post-cutover es un artefacto independiente de MIG-2 y debe estar
implementada, probada y disponible antes del cutover. Su ejecución no autoriza
eliminar Documentos ni convertir la exportación temporal en dual-write.

## 38. Gobierno de la Task de Cursos

`TASK-EPIC001-AUTORIZACION-CURSOS-INTEGRAL-001` quedó cerrada después de
completar esta implementación y aprobar su VF integral.

No se creó addendum. El resultado Documento quedó reflejado en el cierre propio
de Cursos.

## 39. Gobierno documental

Esta corrección contractual modifica exclusivamente:

~~~text
docs/tasks/TASK-EPIC008-PERSISTENCIA-DOCUMENTAL-BINARIA-001.md
~~~

No modifica AT, ADR, roadmap, código, schema, migraciones, configuración, base
de datos, dump ni otros documentos. No mueve archivos, no realiza staging,
commit, push ni VF.

## 40. Estado de autorización previo a la implementación (histórico)

Este bloque se conserva como trazabilidad del contrato previo. El estado final
vigente es el registrado en la sección 1.

~~~text
CORRECCIÓN CONTRACTUAL H1/H2: AUTORIZADA Y DOCUMENTADA
MODIFICAR src/Config/conexion.php EN IMPLEMENTACIÓN FUTURA: AUTORIZADO SÓLO PARA ejecutarEscritura()
REMEDIAR DUMP ANTES DE IMPLEMENTAR: OBLIGATORIO
APLICAR/VERIFICAR GATES MARIADB ANTES DE IMPLEMENTAR: OBLIGATORIO
REVISIÓN TÉCNICA POSTERIOR: REQUERIDA
IMPLEMENTAR CÓDIGO AHORA: PROHIBIDO
CREAR/EJECUTAR MIGRACIONES AHORA: PROHIBIDO
MODIFICAR SCHEMA/BD AHORA: PROHIBIDO
MODIFICAR CONFIGURACIÓN AHORA: PROHIBIDO
EJECUTAR VF AHORA: PROHIBIDO
CREAR ADR/ADDENDUM: PROHIBIDO
STAGING/COMMIT/PUSH: PROHIBIDO
~~~

## 41. Criterios de cierre futuro

La Task sólo podrá cerrarse cuando:

- el dump conocido esté fuera del repositorio y de todo `DocumentRoot`, la URL
  antigua responda `403` o `404` y no exista otra copia sensible expuesta;
- prerrequisitos MariaDB estén aplicados y evidenciados;
- backup y restore aislado estén aprobados;
- MIG-1 y MIG-2 estén implementadas y verificadas;
- el script independiente de reversión filesystem esté probado en dry-run y en
  un ensayo controlado;
- `ejecutarEscritura()` preserve llamadas históricas y soporte el binding
  tipado exacto aprobado;
- modelo Documento e integración Curso estén completos;
- CREATE/UPDATE/DELETE sean atómicos dentro de MariaDB;
- download autorizado opere por `id_curso`;
- fallback y fuente de autoridad sean inequívocos;
- huérfanos y referencia faltante permanezcan conforme a decisión;
- reversión pre y post-cutover sea verificable;
- VF técnica esté completa;
- VF funcional integral de Cursos sea aprobada por el usuario;
- integración Git sea revisada y publicada conforme al flujo del proyecto.

## 42. Requisitos obligatorios de despliegue a producción

### 42.1. Principio y condición de publicación

Los valores aplicados y verificados en desarrollo no implican que producción
esté preparada. Antes de publicar Documento se ejecuta el preflight de
infraestructura directamente en producción y se conserva su evidencia.

La existencia del código y de las migraciones no autoriza por sí sola el
deploy. Producción sólo puede publicar Documento cuando estén aprobados:

- preflight de infraestructura: `PASS`;
- backup: `PASS`;
- procedimiento y capacidad de restore: disponibles;
- configuración MariaDB efectiva: `PASS`;
- configuración PHP efectiva: `PASS`;
- preflight de MIG-1: `PASS`;
- ventana y procedimiento de rollback: disponibles.

Un gate fallido detiene la publicación. No se sustituye evidencia efectiva del
servidor por valores declarados en archivos ni por verificaciones de otro
ambiente.

El gate MariaDB de producción debe devolver efectivamente, después del
restart:

~~~text
max_allowed_packet = 16M
innodb_log_file_size = 64M
innodb_log_buffer_size = 16M
innodb_file_per_table = 1
check_constraint_checks = 1
~~~

El gate PHP de producción debe devolver como mínimo
`upload_max_filesize >= 6M`, `post_max_size >= 8M` y
`memory_limit >= 128M`. Estos márgenes no alteran el límite funcional de la
aplicación de 5 MiB exactos (`5242880` bytes), impuesto por el backend.

### 42.2. Orden operacional obligatorio

El despliegue a producción debe respetar exactamente este orden:

1. abrir la ventana de mantenimiento/despliegue;
2. detener escrituras relevantes, si corresponde;
3. realizar el backup completo previo;
4. verificar que el backup esté fuera de todo `DocumentRoot`;
5. verificar la capacidad y el procedimiento de restore;
6. aplicar los parámetros MariaDB mediante el procedimiento operacional;
7. realizar un restart limpio de MariaDB;
8. verificar los parámetros efectivos mediante consultas al servidor;
9. verificar los parámetros efectivos de PHP;
10. desplegar los artefactos de aplicación y migración;
11. ejecutar el preflight de MIG-1;
12. ejecutar MIG-1;
13. ejecutar el postflight de MIG-1;
14. ejecutar MIG-2;
15. ejecutar el postflight de MIG-2;
16. desplegar o activar el código Documento;
17. ejecutar el smoke test;
18. ejecutar la VF funcional;
19. validar la descarga autorizada;
20. monitorear errores y logs;
21. cerrar la ventana únicamente con todas las validaciones aprobadas.

### 42.3. Migraciones y estados parciales

En producción se ejecutan, en orden:

~~~text
migrations/TASK-DB-MIGRATION-DOCUMENTO-SCHEMA-001.sql
migrations/TASK-DB-MIGRATION-DOCUMENTO-DATOS-001.php
~~~

MIG-2 se ejecuta exclusivamente desde CLI y nunca antes de MIG-1. MIG-3 no se
ejecuta en este incremento.

MIG-1 contiene DDL MariaDB con commits implícitos. Por tanto, producción no
puede asumir rollback transaccional: debe completar el preflight, revisar y
registrar el estado efectivo después de cada etapa y utilizar el procedimiento
de recuperación correspondiente al estado parcial alcanzado. Ante una falla se
detiene MIG-2 y la activación de Documento hasta resolver el estado parcial.

MIG-2 descubre y reporta los datos reales de producción. Los conteos del
entorno inspeccionado no son un criterio de aceptación productivo y no se
fabrican faltantes ni se asocian o eliminan huérfanos automáticamente.

### 42.4. Cutover y rollback de producción

Después de activar el código, Documento BD pasa a ser la autoridad de nuevas
escrituras. No existe dual-write. `arch_prog` queda sólo como compatibilidad
histórica y en esta publicación no se retiran el filesystem,
`curso.arch_prog` ni el fallback.

Si todavía no existen escrituras sólo-BD, puede revertirse la aplicación hacia
la infraestructura legacy conservada siguiendo la reversión pre-cutover. Si ya
existen CREATE o UPDATE sólo-BD, se prohíbe revertir directamente la
aplicación. Primero debe ejecutarse y validarse:

~~~text
Documento
→ filesystem
→ checksum/tamaño/MIME
→ arch_prog
~~~

mediante
`migrations/TASK-DB-MIGRATION-DOCUMENTO-ROLLBACK-FILESYSTEM-001.php`. El schema
se revierte al final, nunca primero, y sólo después de demostrar cobertura del
rollback filesystem.

## 43. Dictamen contractual previo

**A. Contrato H1/H2 y requisitos de producción consolidados; ejecutar
remediación del dump, preflight por ambiente y prerrequisitos MariaDB/PHP antes
de autorizar la implementación o publicación.**
