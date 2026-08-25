# AT-EPIC008-PERSISTENCIA-DOCUMENTAL-BINARIA-001

## 1. Identificación y estado

- **Nombre:** AT-EPIC008-PERSISTENCIA-DOCUMENTAL-BINARIA-001
- **EPIC principal:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia
- **Coordinación:** EPIC-004 — Integridad transaccional; EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral
- **Primer consumidor:** Curso / programa de Curso
- **Clasificación:** [ARQ] [DB] [SEC] [DOC]
- **Nivel:** L3 — Arquitectura / persistencia / gobierno de datos
- **Estado:** APROBADO documentalmente; pendiente de DFA técnico previo a implementación
- **Fecha:** 2026-08-23

Este AT consolida la decisión aprobada de evolucionar la persistencia documental
desde filesystem hacia una entidad independiente `documento`, con contenido
binario almacenado en MariaDB.

Este documento no implementa código, no modifica schema, no crea migraciones,
no modifica datos o configuración y no autoriza staging, commit, push ni VF. No
crea todavía una Task de implementación ni modifica la Task integral de Cursos.

## 2. Autoridad y encuadre

EPIC-008 es la autoridad principal sobre la evolución del modelo persistente,
la compatibilidad entre estructuras heredadas y nuevas, la migración incremental
y la reversibilidad de cambios estructurales. La decisión de este AT se adopta
como incremento específico y no redefine el objetivo, alcance o exclusiones de
la EPIC.

EPIC-004 continúa gobernando la atomicidad de las operaciones compuestas.
EPIC-009 coordinará posteriormente el consumo de Documento desde el objeto
Curso, pero no absorbe ni sustituye la decisión de persistencia aquí registrada.

Las decisiones de autorización, matriz CRUD y ownership funcional de Cursos
permanecen bajo sus fuentes vigentes:

- `docs/architecture/AT-EPIC003-AUTORIZACION-CURSOS-001.md`;
- `docs/tasks/TASK-EPIC001-AUTORIZACION-CURSOS-INTEGRAL-001.md`.

## 3. Alcance

Este AT define exclusivamente:

- la arquitectura objetivo de persistencia documental;
- el modelo conceptual mínimo de Documento;
- la relación inicial Curso→Documento;
- la cardinalidad y el ownership documental;
- el límite funcional aprobado;
- la política de UPDATE y DELETE;
- los requisitos de seguridad que deben preservarse;
- la estrategia incremental y reversible de migración;
- el impacto arquitectónico sobre el cierre de Cursos;
- las materias que deberá resolver un DFA posterior.

Quedan fuera de alcance:

- tipos SQL concretos distintos de `archivo → LONGBLOB`;
- DDL, schema, índices, FK o migraciones ejecutables;
- implementación PHP, PDO, HTTP o frontend;
- cambios de configuración MariaDB, PHP o Apache;
- saneamiento o eliminación de datos y archivos;
- incorporación inicial de Reglamento u otros consumidores;
- versionado o historial documental;
- creación de ADR o Task de implementación.

## 4. Estado actual confirmado

La persistencia documental funcional actual se compone de:

~~~text
curso.arch_prog
→ basename físico
→ files/prog_curso
→ contenido PDF
~~~

Hechos confirmados y anonimizados:

- `curso.arch_prog` es una columna `varchar(45) NOT NULL` que almacena el
  basename del programa físico;
- `files/prog_curso` contiene los binarios y se encuentra bajo webroot;
- no existe tabla `documento` ni una entidad equivalente reutilizable;
- Curso es el único consumidor documental funcional actual;
- `reglamento.arch_regl` existe como referencia potencial, pero Reglamento no
  tiene registros ni flujo funcional de persistencia o descarga;
- existen 2 referencias Curso→PDF;
- 1 referencia resuelve a un PDF físico válido;
- 1 referencia apunta a un archivo inexistente;
- existen 3 PDF físicos no referenciados por Curso;
- los PDF huérfanos y la referencia faltante son deuda de datos independiente y
  no deben sanearse automáticamente.

El mayor PDF observado ocupa aproximadamente 430 KiB. La arquitectura actual
requiere coordinación y compensación entre MariaDB y filesystem para CREATE,
UPDATE y DELETE.

## 5. Decisión arquitectónica

La persistencia documental objetivo será una entidad independiente:

~~~text
documento
~~~

El contenido binario se almacenará en MariaDB mediante:

~~~text
archivo → LONGBLOB
~~~

El límite funcional aprobado para la nueva arquitectura es:

~~~text
5 MiB por documento
~~~

El límite histórico de 40 MiB no forma parte del nuevo contrato.

Documento no se incorpora dentro de Profesor ni dentro de las columnas propias
de cada objeto consumidor. Cada consumidor se relacionará explícitamente con la
entidad Documento cuando exista una necesidad funcional confirmada.

## 6. Modelo conceptual mínimo

La propuesta objetivo mínima es:

~~~text
documento
- id_documento
- nombre_original
- mime_type
- tamanio
- archivo
- fecha_creacion
- checksum_sha256 (opcional)
~~~

Sólo queda aprobado en este AT el tipo SQL de `archivo`: `LONGBLOB`. El DFA
posterior deberá definir los tipos, tamaños, nulabilidad, defaults, constraints
y codificación de las demás columnas.

### id_documento

Identificador persistente y autoridad técnica para relacionar el Documento con
su consumidor. Su tipo SQL exacto queda pendiente.

### nombre_original

Metadata documental para conservar, cuando exista, el nombre recibido. No es
autoridad de ruta, MIME o seguridad y debe tratarse de forma segura al construir
`Content-Disposition`.

Los archivos heredados no conservan necesariamente el nombre original real. El
DFA deberá definir cómo representar esa ausencia sin atribuir como original un
nombre que no pueda comprobarse.

### mime_type

Metadata obtenida después de validar el contenido real. El valor declarado por
el cliente no constituye autoridad.

### tamanio

Metadata necesaria para aplicar el máximo funcional, preparar la descarga y
evitar recuperar el LONGBLOB para conocer su tamaño.

### archivo

Contenido binario persistido en MariaDB. Su tipo objetivo aprobado es
`LONGBLOB`.

### fecha_creacion

Metadata mínima de creación. Su tipo, precisión y generación quedan pendientes
del DFA.

### checksum_sha256

Metadata opcional para verificar integridad y apoyar la migración. El DFA
resolverá si será obligatorio u opcional en el schema final.

### Campos no requeridos

`nombre_almacenado` no es requerido: al no existir un archivo físico persistente
no se necesita un nombre interno de almacenamiento y `id_documento` actúa como
identificador lógico.

`extension` no es requerida como autoridad. Puede derivarse del MIME validado o
del nombre original y nunca reemplaza la inspección del contenido.

## 7. Ownership documental

Documento no pertenece globalmente a Profesor. El ownership corresponde al
objeto funcional consumidor y a las reglas de autorización de ese objeto.

Para el primer consumidor:

~~~text
curso
→ id_documento_programa
→ documento.id_documento
~~~

El Profesor asociado mediante `curso.profesor` conserva su ownership funcional
sobre las operaciones de Curso definido por la matriz vigente, pero no se
convierte en propietario técnico autónomo del binario. No se agregarán BLOB ni
metadatos documentales a la tabla `profesor`.

La autorización sobre el programa se determina primero desde Curso. Conocer un
`id_documento` no concede acceso directo al contenido.

## 8. Relación y cardinalidad de Curso

La relación preferida es:

~~~text
curso.id_documento_programa
FK → documento.id_documento
~~~

El estado objetivo, cuando la integridad histórica esté saneada, es:

~~~text
Curso → exactamente un programa vigente
~~~

Durante la migración se admite:

~~~text
Curso → 0..1 Documento
~~~

Esta cardinalidad transitoria permite representar el Curso histórico cuyo
archivo físico falta. No se creará un Documento vacío ni se fabricará contenido
para satisfacer artificialmente la relación.

El DFA evaluará:

- FK nullable durante la transición;
- `UNIQUE` sobre `curso.id_documento_programa` para impedir que un Documento
  exclusivo sea compartido accidentalmente;
- evolución a `NOT NULL` sólo cuando la integridad histórica lo permita.

No se utilizará una relación polimórfica `entidad_tipo + entidad_id`, porque
elimina la FK real y debilita la integridad referencial. Tampoco se creará una
tabla puente para un único programa vigente por Curso.

## 9. Lifecycle objetivo

### CREATE

La creación de Curso y Documento deberá producir un único resultado coherente
dentro de una transacción explícita. La secuencia SQL, los contratos de escritura
y el tratamiento del binario serán definidos por el DFA.

### READ y descarga

Las consultas de listado y detalle no deben seleccionar `documento.archivo`.
Sólo podrán consultar la metadata necesaria.

La recuperación binaria ocurrirá exclusivamente en el endpoint autorizado:

~~~text
actor autenticado
→ autorización Cursos
→ id_curso
→ Curso
→ id_documento_programa
→ metadata Documento
→ SELECT del contenido binario
→ headers controlados
→ descarga
~~~

### UPDATE

No existe versionado histórico documental en esta etapa. El reemplazo del
programa actualizará o reemplazará el Documento vigente según la secuencia
técnica mínima que establezca el DFA, sin conservar automáticamente versiones
anteriores ni crear historial documental.

### DELETE

La política objetivo es:

~~~text
DELETE Curso
→ DELETE Curso
→ DELETE Documento exclusivo asociado
→ una transacción explícita
~~~

Una FK desde Curso hacia Documento no puede resolver mediante cascade inverso
la eliminación del Documento padre. La implementación deberá obtener el
Documento asociado y controlar explícitamente ambas eliminaciones dentro de la
misma transacción.

No existe evidencia para compartir un Documento de programa entre varios
Cursos. Si en el futuro apareciera una necesidad de compartición, deberá
evaluarse antes de alterar esta semántica de eliminación exclusiva.

## 10. Seguridad

La persistencia en MariaDB elimina progresivamente la dependencia de:

- rutas físicas y basenames internos;
- traversal de paths;
- `unlink` y `rename`;
- temporales y cuarentena de filesystem;
- `arch_actual` u otra referencia enviada por cliente;
- almacenamiento documental bajo webroot;
- coordinación compensatoria entre BD y filesystem.

El cambio no elimina los controles de seguridad del flujo. Permanecen
obligatorios:

- autenticación;
- autorización por operación;
- ownership funcional de Curso;
- protección contra IDOR;
- validación del upload y del tamaño;
- inspección MIME real mediante `finfo`;
- consultas preparadas y parámetros tipados;
- exclusión del LONGBLOB en listados y detalles;
- descarga mediante endpoint autorizado;
- `Content-Type` controlado;
- `Content-Disposition` seguro;
- `X-Content-Type-Options: nosniff`;
- caché privada conforme al contrato vigente;
- tratamiento de errores sin exponer datos, metadata interna o contenido.

El MIME, extensión y nombre suministrados por el cliente no son autoridad. El
límite debe aplicarse antes de persistir el binario y no sólo en frontend.

## 11. Límite funcional de 5 MiB

La decisión institucional fija un máximo de 5 MiB por Documento para la nueva
arquitectura.

La decisión se sustenta en que:

- el mayor PDF observado ocupa aproximadamente 430 KiB;
- 5 MiB ofrece un margen ampliamente superior al uso actual;
- evita dimensionar la infraestructura para el límite heredado de 40 MiB;
- reduce el impacto sobre memoria, red, transacciones y conexiones;
- limita el crecimiento de backups y tiempos de restauración.

La implementación futura deberá expresar 5 MiB de forma inequívoca en frontend,
backend, validación, pruebas y configuración. No debe conservar 40 MiB como
fallback o contrato alternativo de Documento.

## 12. Infraestructura pendiente

La configuración local observada presenta:

- `max_allowed_packet` aproximado de 1 MiB;
- `innodb_log_file_size` aproximado de 5 MiB;
- `innodb_log_buffer_size` aproximado de 8 MiB;
- `post_max_size` de 40 MiB;
- `upload_max_filesize` de 40 MiB;
- `memory_limit` de 512 MiB;
- PDO MySQL disponible.

`max_allowed_packet` no permite todavía un payload documental de 5 MiB. Antes
de implementar, el DFA deberá determinar un valor seguro superior al máximo
funcional más el overhead del protocolo y de la sentencia.

También deberá revisar y validar:

- capacidad y comportamiento de redo/undo de InnoDB;
- suficiencia de log file y log buffer;
- valores PHP exactos con margen para multipart;
- memoria por solicitud y concurrencia esperada;
- comportamiento efectivo de PDO al insertar, actualizar y recuperar LONGBLOB;
- consultas bufferizadas o no bufferizadas y estrategia de descarga;
- impacto sobre backup, restore y tiempos operacionales;
- límites coherentes en todos los entornos de despliegue.

Este AT no aprueba valores de configuración ni autoriza modificarlos.

## 13. Migración incremental objetivo

La evolución se realizará por fases compatibles. Este AT no ejecuta ninguna.

### Fase 1 — Estructura coexistente

- crear tabla `documento`;
- crear `curso.id_documento_programa` nullable;
- conservar `curso.arch_prog`;
- conservar `files/prog_curso` y sus archivos.

### Fase 2 — Importación válida

- considerar únicamente referencias persistidas por Curso;
- importar sólo el archivo actualmente referenciado y físicamente existente;
- validar MIME real y máximo de 5 MiB;
- calcular checksum según la decisión final del DFA;
- crear Documento y asociar la FK;
- no atribuir un nombre original que no pueda demostrarse.

### Fase 3 — Referencia faltante

- mantener `id_documento_programa = NULL`;
- registrar deuda de integridad;
- no crear Documento vacío;
- no fabricar ni sustituir contenido.

### Fase 4 — PDF huérfanos

- no importar los 3 PDF no referenciados;
- no asociarlos a Curso;
- no eliminarlos automáticamente;
- mantener su clasificación como deuda separada.

### Fase 5 — Adopción por Curso

- usar Documento como fuente preferente;
- mantener fallback temporal a `arch_prog` únicamente durante una transición
  controlada;
- adaptar CREATE, descarga, UPDATE y DELETE;
- evitar una coexistencia indefinida de autoridades documentales.

### Fase 6 — Validación

- ejecutar VF integral del objeto Curso por los actores aprobados;
- validar casos de seguridad, errores y límites;
- validar backup y restauración con binarios;
- comprobar migración, compatibilidad y rollback.

### Fase 7 — Retiro posterior

- retirar `curso.arch_prog` después de cerrar dependencias y rollback;
- retirar el filesystem documental;
- retirar `.htaccess` y compatibilidad asociada cuando ninguna ruta dependa de
  `files/prog_curso`;
- conservar trazabilidad de la deuda de datos no migrada.

## 14. Reversibilidad y coexistencia

Durante la transición se conservarán `arch_prog` y los archivos físicos
existentes. La migración inicial no será destructiva y deberá mantener una
ventana verificable de rollback.

El fallback a filesystem y cualquier dual-write sólo son admisibles como
mecanismos temporales y acotados. Un dual-write prolongado aumenta el riesgo de
divergencia entre Documento y `arch_prog`, duplica almacenamiento y reintroduce
la coordinación entre dos recursos.

El DFA deberá definir:

- autoridad de lectura en cada fase;
- autoridad de escritura en cada fase;
- duración y condición de salida de la coexistencia;
- detección de divergencias;
- secuencia de rollback antes y después del cambio de autoridad;
- punto en que resulta seguro retirar filesystem y columna heredada.

## 15. Impacto sobre Cursos

`TASK-EPIC001-AUTORIZACION-CURSOS-INTEGRAL-001` se encuentra funcionalmente
avanzada, pero queda pausada antes de cierre y VF final debido a esta evolución
arquitectónica de persistencia documental.

Se preservan sin redefinición:

- matriz CRUD institucional;
- actores y capacidades;
- ownership de Curso;
- `cursos.ver`;
- CSRF;
- consultas parametrizadas y contratos explícitos;
- contrato HTTP/JSON;
- frontend y mensajes no dependientes del almacenamiento;
- reglas de IDOR y autorización.

Deberán revisarse y adaptarse posteriormente:

- recepción y persistencia del upload;
- límite de 5 MiB;
- creación de Documento y asociación con Curso;
- descarga del contenido desde MariaDB;
- reemplazo del PDF vigente;
- DELETE transaccional de Curso y Documento;
- fallback y reversión durante migración;
- dependencias de `arch_prog`, filesystem y `.htaccess`.

Este AT no modifica la Task de Cursos. El cambio de schema y el patrón
documental son una decisión arquitectónica independiente bajo EPIC-008 y no
deben incorporarse silenciosamente al alcance anterior de schema intacto.

## 16. Consumidores

Curso será el primer y único consumidor de la implementación inicial.

Reglamento se registra exclusivamente como posible consumidor futuro porque
posee `arch_regl`, pero actualmente no tiene registros ni lifecycle documental
funcional. No ingresa al DFA o implementación inicial y no justifica una
relación polimórfica anticipada.

Profesor, Estudiante, Publicación, Tesis, Proyecto, Congreso, Grado, Beca,
Pasantía y Postdoctorado no presentan actualmente persistencia documental que
deba incorporarse a este alcance.

Una futura adopción por otro objeto requerirá evidencia, relación explícita y
su propio incremento compatible.

## 17. Decisiones cerradas

Quedan cerradas institucionalmente:

1. Documento será una entidad independiente.
2. Documento no estará dentro de Profesor.
3. El contenido binario se almacenará en MariaDB.
4. `archivo` utilizará `LONGBLOB`.
5. El máximo funcional será 5 MiB por Documento.
6. Curso tendrá un programa vigente cuando los datos estén saneados.
7. No existirá versionado histórico en esta etapa.
8. El ownership corresponderá al objeto consumidor.
9. Curso se relacionará mediante una FK explícita hacia Documento.
10. La migración será incremental y reversible.
11. Los PDF huérfanos no se importarán ni asociarán automáticamente.
12. El PDF faltante no se fabricará y se representará con relación nula durante
    la transición.
13. `nombre_almacenado` no formará parte del modelo mínimo.
14. `extension` no será autoridad documental.

Estas decisiones no deben reabrirse en el DFA salvo que aparezca evidencia
incompatible no disponible al aprobar este AT.

## 18. Decisiones técnicas pendientes

El DFA posterior deberá resolver, sin requerir una nueva decisión institucional:

- tipos SQL exactos de todas las columnas excepto `archivo`;
- nulabilidad, defaults y constraints de Documento;
- índices y nombres finales de constraints;
- FK Curso→Documento;
- aplicación de `UNIQUE`;
- condición y secuencia nullable→`NOT NULL`;
- obligatoriedad final de `checksum_sha256`;
- `max_allowed_packet` objetivo;
- capacidad y valores mínimos de InnoDB;
- límites PHP exactos con margen multipart;
- estrategia PDO para INSERT, UPDATE y recuperación LONGBLOB;
- separación de consultas de metadata y contenido;
- secuencia concreta de migración y rollback;
- contratos de error para Documento ausente o inválido;
- archivos de implementación concretos;
- plan de pruebas técnicas y VF posterior.

## 19. Riesgos

- mantener dos autoridades documentales durante demasiado tiempo;
- divergencia por dual-write;
- cargar LONGBLOB en listados o detalles;
- agotar memoria o conexiones bajo concurrencia;
- configurar paquetes sin margen suficiente;
- aumentar backup y restore sin validación operacional;
- conceder acceso por `id_documento` sin autorizar primero el Curso;
- confiar en MIME, extensión o nombre cliente;
- importar o eliminar archivos huérfanos sin ownership demostrado;
- convertir una relación transitoria nullable en `NOT NULL` antes de resolver la
  deuda histórica;
- introducir versionado o consumidores futuros sin evidencia;
- retirar filesystem antes de cerrar la ventana de rollback.

## 20. Condiciones para detener la implementación futura

El DFA o la implementación deberán detenerse y volver a revisión si:

1. la configuración no puede soportar de forma segura documentos de 5 MiB;
2. backup/restore no puede cumplir el nivel operacional requerido;
3. PDO no permite un tratamiento de memoria compatible con la concurrencia
   esperada;
4. aparece una política institucional incompatible de conservación o
   versionado;
5. no puede garantizarse la relación exclusiva Curso→Documento;
6. se necesita incorporar otro consumidor para implementar Curso;
7. la coexistencia no puede mantenerse reversible y acotada;
8. la migración exige fabricar datos o asociar huérfanos sin evidencia;
9. se requiere redefinir la matriz CRUD, autorización u ownership de Cursos;
10. aparece un ADR vigente incompatible.

## 21. Gobierno y siguiente paso

Clasificación de la decisión:

~~~text
[ARQ] arquitectura documental
[DB] modelo persistente y LONGBLOB
[SEC] validación y entrega autorizada
[DOC] trazabilidad y gobierno EPIC-008
~~~

No se crea un ADR en esta etapa. El DFA posterior determinará si la decisión
requiere consolidación adicional mediante ADR o si este AT, bajo la autoridad
vigente de EPIC-008, resulta suficiente.

No quedan decisiones institucionales pendientes dentro del alcance aprobado.
Las materias abiertas son técnicas y corresponden al DFA.

El siguiente paso autorizado es:

~~~text
DFA técnico específico de Documento
→ schema exacto
→ infraestructura mínima
→ contratos PDO/BLOB
→ descarga
→ migración y rollback
→ archivos concretos
→ propuesta de Task de implementación
~~~

El DFA no debe ejecutar cambios ni crear una Task sin una autorización posterior
explícita.

## 22. Fuentes

- `docs/roadmap/ROADMAP.md`, EPIC-008 y coordinación con EPIC-009;
- `docs/architecture/AT-EPIC003-AUTORIZACION-CURSOS-001.md`;
- `docs/tasks/TASK-EPIC001-AUTORIZACION-CURSOS-INTEGRAL-001.md`;
- inspección arquitectónica de persistencia documental y evidencia anonimizada
  del schema, filesystem y configuración local;
- decisión aprobada de persistencia documental suministrada para este AT.

## 23. Dictamen

La persistencia documental objetivo queda aprobada como una entidad Documento
independiente, con contenido `LONGBLOB` en MariaDB, límite funcional de 5 MiB,
ownership por objeto consumidor y Curso como primer consumidor mediante FK
explícita.

La evolución será incremental, compatible y reversible. La Task integral de
Cursos permanece pausada antes de cierre/VF final. Corresponde realizar un DFA
técnico específico antes de crear schema, migración, configuración, código o
Task de implementación.
