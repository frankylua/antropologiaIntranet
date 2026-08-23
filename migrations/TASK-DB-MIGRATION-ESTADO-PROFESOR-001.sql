-- TASK-DB-MIGRATION-ESTADO-PROFESOR-001
-- NO EJECUTAR sin autorizacion tecnica y respaldo previo.

-- Preflight: verificar que la tabla exista (esperado: 1).
SELECT COUNT(*) AS tabla_profesor_existente
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'profesor';

-- Preflight: verificar que la columna aun no exista (esperado: 0).
SELECT COUNT(*) AS columna_estado_profesor_existente
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'profesor'
  AND COLUMN_NAME = 'estado_profesor';

-- Preflight: conservar el conteo que debe migrar integramente a estado 2.
SELECT COUNT(*) INTO @profesores_preexistentes
FROM `profesor`;

SELECT @profesores_preexistentes AS profesores_preexistentes;

-- Nucleo de migracion.
ALTER TABLE `profesor`
  ADD COLUMN `estado_profesor` INT(11) NULL AFTER `anio_ingreso`;

UPDATE `profesor`
SET `estado_profesor` = 2
WHERE `estado_profesor` IS NULL;

ALTER TABLE `profesor`
  MODIFY COLUMN `estado_profesor` INT(11) NOT NULL,
  ADD CONSTRAINT `chk_profesor_estado`
    CHECK (`estado_profesor` IN (1, 2, 3));

-- Postflight: columna unica, INT(11), NOT NULL y sin DEFAULT (esperado: 1 fila).
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'profesor'
  AND COLUMN_NAME = 'estado_profesor';

-- Postflight: CHECK presente (esperado: 1).
SELECT COUNT(*) AS constraint_estado_profesor_existente
FROM information_schema.TABLE_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = DATABASE()
  AND TABLE_NAME = 'profesor'
  AND CONSTRAINT_NAME = 'chk_profesor_estado'
  AND CONSTRAINT_TYPE = 'CHECK';

-- Postflight: cero NULL y cero valores fuera de dominio.
SELECT
  SUM(CASE WHEN `estado_profesor` IS NULL THEN 1 ELSE 0 END) AS estados_null,
  SUM(CASE WHEN `estado_profesor` NOT IN (1, 2, 3) THEN 1 ELSE 0 END) AS estados_invalidos
FROM `profesor`;

-- Postflight: sin concurrencia durante la migracion, ambos conteos deben coincidir
-- con @profesores_preexistentes y todos los registros deben estar en estado 2.
SELECT
  @profesores_preexistentes AS profesores_preexistentes,
  COUNT(*) AS profesores_despues,
  SUM(CASE WHEN `estado_profesor` = 2 THEN 1 ELSE 0 END) AS profesores_aceptados
FROM `profesor`;

-- Rollback manual. Antes debe retirarse el codigo que utiliza estado_profesor.
-- Este rollback elimina cualquier estado generado despues de aplicar la migracion.
-- ALTER TABLE `profesor` DROP CONSTRAINT `chk_profesor_estado`;
-- ALTER TABLE `profesor` DROP COLUMN `estado_profesor`;
