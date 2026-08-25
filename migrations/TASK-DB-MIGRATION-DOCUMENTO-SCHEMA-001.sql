-- TASK-DB-MIGRATION-DOCUMENTO-SCHEMA-001
-- MariaDB 10.4.32. NO EJECUTAR sin ventana, backup y preflight aprobados.
-- El DDL produce commits implícitos: esta migración no es atómica y no utiliza
-- BEGIN, COMMIT ni ROLLBACK. Detenerse ante cualquier resultado inesperado.

-- PRECHECK OPERATIVO (todos los valores deben estar efectivos).
SELECT VERSION() AS version_mariadb;
SELECT
  @@max_allowed_packet AS max_allowed_packet,
  @@innodb_log_file_size AS innodb_log_file_size,
  @@innodb_log_buffer_size AS innodb_log_buffer_size,
  @@innodb_file_per_table AS innodb_file_per_table,
  @@check_constraint_checks AS check_constraint_checks;

-- Esperado: todos los indicadores en 1.
SELECT
  VERSION() REGEXP '^10[.]4[.]' AS version_compatible,
  @@max_allowed_packet = 16777216 AS packet_16m,
  @@innodb_log_file_size = 67108864 AS log_file_64m,
  @@innodb_log_buffer_size = 16777216 AS log_buffer_16m,
  @@innodb_file_per_table = 1 AS file_per_table_activo,
  @@check_constraint_checks = 1 AS checks_activos;

-- PREFLIGHT SCHEMA: esperado tabla curso=1, engine InnoDB y tabla documento=0.
SELECT TABLE_NAME, ENGINE, ROW_FORMAT, TABLE_COLLATION
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('curso', 'documento')
ORDER BY TABLE_NAME;

-- Esperado: una fila varchar(45), IS_NULLABLE=NO, sin default.
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'curso'
  AND COLUMN_NAME = 'arch_prog';

-- Esperado: 0. Detener si la columna ya existe.
SELECT COUNT(*) AS columna_documento_programa_existente
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'curso'
  AND COLUMN_NAME = 'id_documento_programa';

-- Esperado: 0 filas. Detecta colisiones de nombres UNIQUE/FK/CHECK.
SELECT CONSTRAINT_NAME, TABLE_NAME, CONSTRAINT_TYPE
FROM information_schema.TABLE_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = DATABASE()
  AND CONSTRAINT_NAME IN (
    'uq_curso_documento_programa',
    'fk_documento_programa_curso',
    'chk_documento_tamanio',
    'chk_documento_archivo_tamanio'
  );

-- Esperado: 0. MIG-1 parte de arch_prog NOT NULL.
SELECT COUNT(*) AS cursos_arch_prog_null_preflight
FROM `curso`
WHERE `arch_prog` IS NULL;

-- Registrar este conteo antes del DDL; debe coincidir en postflight.
SELECT COUNT(*) AS cursos_preflight
FROM `curso`;

-- APLICACIÓN ETAPA 1. Ejecutar sólo si todo el preflight fue aprobado.
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

-- POSTFLIGHT ETAPA 1. Aprobar antes de ejecutar ALTER TABLE curso.
SELECT TABLE_NAME, ENGINE, ROW_FORMAT, TABLE_COLLATION
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'documento';

SELECT ORDINAL_POSITION, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT,
       EXTRA, CHARACTER_SET_NAME, COLLATION_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'documento'
ORDER BY ORDINAL_POSITION;

SELECT CONSTRAINT_NAME, CONSTRAINT_TYPE
FROM information_schema.TABLE_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = DATABASE()
  AND TABLE_NAME = 'documento'
ORDER BY CONSTRAINT_NAME;

SELECT CONSTRAINT_NAME, CHECK_CLAUSE
FROM information_schema.CHECK_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = DATABASE()
  AND CONSTRAINT_NAME IN (
    'chk_documento_tamanio',
    'chk_documento_archivo_tamanio'
  )
ORDER BY CONSTRAINT_NAME;

-- APLICACIÓN ETAPA 2. Unidad DDL única aprobada.
ALTER TABLE `curso`
  MODIFY COLUMN `arch_prog` varchar(45) NULL,
  ADD COLUMN `id_documento_programa` int(11) NULL DEFAULT NULL AFTER `arch_prog`,
  ADD UNIQUE KEY `uq_curso_documento_programa` (`id_documento_programa`),
  ADD CONSTRAINT `fk_documento_programa_curso`
    FOREIGN KEY (`id_documento_programa`)
    REFERENCES `documento` (`id_documento`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;

-- POSTFLIGHT COMPLETO: columnas y nulabilidad.
SELECT ORDINAL_POSITION, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'curso'
  AND COLUMN_NAME IN ('arch_prog', 'id_documento_programa')
ORDER BY ORDINAL_POSITION;

-- PK, CHECK, UNIQUE y FK.
SELECT CONSTRAINT_NAME, CONSTRAINT_TYPE, TABLE_NAME
FROM information_schema.TABLE_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('documento', 'curso')
  AND CONSTRAINT_NAME IN (
    'PRIMARY',
    'chk_documento_tamanio',
    'chk_documento_archivo_tamanio',
    'uq_curso_documento_programa',
    'fk_documento_programa_curso'
  )
ORDER BY TABLE_NAME, CONSTRAINT_NAME;

SELECT INDEX_NAME, NON_UNIQUE, SEQ_IN_INDEX, COLUMN_NAME
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'curso'
  AND INDEX_NAME = 'uq_curso_documento_programa';

SELECT rc.CONSTRAINT_NAME, rc.TABLE_NAME, rc.REFERENCED_TABLE_NAME,
       rc.DELETE_RULE, rc.UPDATE_RULE, kcu.COLUMN_NAME,
       kcu.REFERENCED_COLUMN_NAME
FROM information_schema.REFERENTIAL_CONSTRAINTS rc
JOIN information_schema.KEY_COLUMN_USAGE kcu
  ON kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
 AND kcu.TABLE_NAME = rc.TABLE_NAME
 AND kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
WHERE rc.CONSTRAINT_SCHEMA = DATABASE()
  AND rc.CONSTRAINT_NAME = 'fk_documento_programa_curso';

-- Conteos: Curso debe coincidir con preflight; los demás deben ser cero.
SELECT COUNT(*) AS cursos_postflight
FROM `curso`;

SELECT COUNT(*) AS documentos_postflight
FROM `documento`;

SELECT COUNT(*) AS cursos_con_documento_postflight
FROM `curso`
WHERE `id_documento_programa` IS NOT NULL;

-- ROLLBACK MANUAL ORIENTATIVO, sólo antes de MIG-2/cutover y después de
-- comprobar que no existen escrituras Documento-only. Ejecutar paso a paso,
-- verificando cada estado. No se autoejecuta y no es rollback transaccional.
--
-- ALTER TABLE `curso`
--   DROP FOREIGN KEY `fk_documento_programa_curso`;
-- ALTER TABLE `curso`
--   DROP INDEX `uq_curso_documento_programa`;
-- ALTER TABLE `curso`
--   DROP COLUMN `id_documento_programa`;
-- SELECT COUNT(*) FROM `curso` WHERE `arch_prog` IS NULL;
-- Sólo si el conteo anterior es 0:
-- ALTER TABLE `curso`
--   MODIFY COLUMN `arch_prog` varchar(45) NOT NULL;
-- DROP TABLE `documento`;
