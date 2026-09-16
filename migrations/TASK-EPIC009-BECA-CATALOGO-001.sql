-- TASK-EPIC009-BECA-CATALOGO-001
-- TASK-EPIC009-BECA-INTEGRAL-001
-- MariaDB 10.4.32. Evidencia de schema y fila historica suministrada en la Task.
-- NO EJECUTAR sin autorizacion y respaldo previo.
-- DDL con commits implicitos: migracion no atomica, de una sola aplicacion.
-- Aplicar por etapas, sin escrituras concurrentes sobre el catalogo.
-- Los preflight son verificaciones manuales, no detienen automaticamente el SQL.
-- Detener antes del DDL si cualquier resultado difiere de lo esperado.

-- Preflight: MariaDB 10.4.32 y CHECK activos (checks_activos = 1).
SELECT VERSION() AS version_mariadb,
       @@check_constraint_checks AS checks_activos;

-- Esperado: ambas tablas InnoDB; registrar charset/collation.
SELECT TABLE_NAME, ENGINE, TABLE_COLLATION
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('usuario', 'nombre_beca');

-- Esperado: usuario.id_usuario int(11) signed, NOT NULL, PRI.
SELECT COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, EXTRA
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'usuario'
  AND COLUMN_NAME = 'id_usuario';

-- Esperado: cero columnas nuevas y ninguna colision de constraints.
SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'nombre_beca'
  AND COLUMN_NAME IN ('estado_catalogo', 'propuesto_por');

SELECT CONSTRAINT_NAME, TABLE_NAME
FROM information_schema.TABLE_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = DATABASE()
  AND CONSTRAINT_NAME IN (
    'chk_nombre_beca_estado', 'fk_nombre_beca_propuesto_por'
  );

-- Esperado: una sola fila tipo 0; unica candidata vacia con ID 17.
SELECT COUNT(*) AS filas_tipo_cero,
       SUM(CASE WHEN TRIM(`beca`) = '' THEN 1 ELSE 0 END) AS vacias_tipo_cero,
       SUM(CASE WHEN `id_nom_beca` = 17 AND TRIM(`beca`) = ''
                THEN 1 ELSE 0 END) AS candidata_confirmada
FROM `nombre_beca`
WHERE `tipo_beca` = 0;

SELECT COUNT(*) INTO @beca_catalogo_historicos
FROM `nombre_beca`;

-- Aplicacion: ejecutar solo tras verificar todos los preflight.
ALTER TABLE `nombre_beca`
  ADD COLUMN `estado_catalogo` VARCHAR(10) NULL,
  ADD COLUMN `propuesto_por` INT(11) NULL DEFAULT NULL;

UPDATE `nombre_beca`
SET `estado_catalogo` = 'APROBADA',
    `propuesto_por` = NULL;

UPDATE `nombre_beca`
SET `estado_catalogo` = 'INACTIVA'
WHERE `id_nom_beca` = 17
  AND `tipo_beca` = 0
  AND TRIM(`beca`) = '';

ALTER TABLE `nombre_beca`
  MODIFY COLUMN `estado_catalogo` VARCHAR(10) NOT NULL,
  ADD CONSTRAINT `chk_nombre_beca_estado`
    CHECK (`estado_catalogo` IN ('APROBADA', 'PENDIENTE', 'INACTIVA')),
  ADD CONSTRAINT `fk_nombre_beca_propuesto_por`
    FOREIGN KEY (`propuesto_por`) REFERENCES `usuario` (`id_usuario`)
    ON DELETE SET NULL;

-- InnoDB crea el indice requerido por la FK; no se agrega otro indice.
-- Postflight: conteo preservado, una INACTIVA, resto APROBADA, proponentes NULL.
SELECT @beca_catalogo_historicos AS filas_antes,
       COUNT(*) AS filas_despues,
       SUM(`estado_catalogo` = 'APROBADA') AS aprobadas,
       SUM(`estado_catalogo` = 'INACTIVA') AS inactivas,
       SUM(`propuesto_por` IS NOT NULL) AS proponentes_no_null
FROM `nombre_beca`;

SELECT `id_nom_beca`, `tipo_beca`, `estado_catalogo`, `propuesto_por`
FROM `nombre_beca`
WHERE `id_nom_beca` = 17
  AND `tipo_beca` = 0
  AND TRIM(`beca`) = '';

SHOW CREATE TABLE `nombre_beca`;

-- Rollback manual: retirar antes el codigo dependiente y preservar cualquier
-- estado/proponente generado despues de aplicar. Elimina las nuevas columnas;
-- no elimina filas ni altera nombres o tipos historicos. No es transaccional.
-- ALTER TABLE `nombre_beca`
--   DROP FOREIGN KEY `fk_nombre_beca_propuesto_por`;
-- ALTER TABLE `nombre_beca`
--   DROP CONSTRAINT `chk_nombre_beca_estado`;
-- ALTER TABLE `nombre_beca`
--   DROP COLUMN `propuesto_por`,
--   DROP COLUMN `estado_catalogo`;
