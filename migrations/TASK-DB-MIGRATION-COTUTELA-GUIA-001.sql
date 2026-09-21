-- TASK-DB-MIGRATION-COTUTELA-GUIA-001
-- TASK-EPIC009-TESIS-INTEGRAL-001
-- M1: Guia interno/externo de Cotutela.
-- M2: Cardinalidad Cotutela 0..1 por Tesis.
--
-- EJECUCION MANUAL. NO ejecutar automaticamente desde Codex.
-- M3: revision CASCADE general pendiente y fuera de alcance.
-- No modifica FK existentes, incluidos: fk_est_tesis, fk_prof_guia,
-- fk_prof_coguia, fk_inst_tesis, fk_pais_tesis, fk_tesis_cot,
-- fk_institucion_cotutela y fk_pais_cotutela.

-- ============================================================================
-- PREFLIGHT (solo lectura)
-- ============================================================================
SELECT COUNT(*) AS total_cotutelas
FROM cotutela;

SELECT tesis, COUNT(*) AS cantidad
FROM cotutela
GROUP BY tesis
HAVING COUNT(*) > 1;

SELECT
    id_cotutela,
    prof_guia,
    tesis
FROM cotutela
WHERE prof_guia IS NULL
   OR TRIM(prof_guia) = '';

-- Si cualquiera de las dos ultimas consultas retorna filas:
-- DETENER MIGRACION.
-- No ejecutar automaticamente ninguna decision basada en resultados.

-- ============================================================================
-- MIGRACION
-- ============================================================================
ALTER TABLE `cotutela`
    MODIFY COLUMN `prof_guia` VARCHAR(45) NULL,
    ADD COLUMN `id_prof_guia` INT(11) NULL AFTER `prof_guia`,
    ADD INDEX `fk_prof_guia_cotutela_idx` (`id_prof_guia`),
    ADD CONSTRAINT `fk_prof_guia_cotutela`
        FOREIGN KEY (`id_prof_guia`)
        REFERENCES `usuario` (`id_usuario`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    ADD CONSTRAINT `chk_cotutela_guia_xor`
        CHECK (
            (`id_prof_guia` IS NOT NULL AND `prof_guia` IS NULL)
            OR
            (`id_prof_guia` IS NULL AND `prof_guia` IS NOT NULL)
        ),
    ADD CONSTRAINT `uq_cotutela_tesis`
        UNIQUE (`tesis`);

-- ============================================================================
-- VALIDACION POST-MIGRACION
-- ============================================================================
SHOW CREATE TABLE `cotutela`;

SELECT
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'cotutela'
  AND COLUMN_NAME IN ('id_prof_guia', 'prof_guia');

SELECT
    rc.CONSTRAINT_NAME,
    rc.DELETE_RULE,
    rc.UPDATE_RULE,
    kcu.COLUMN_NAME,
    kcu.REFERENCED_TABLE_NAME,
    kcu.REFERENCED_COLUMN_NAME
FROM information_schema.REFERENTIAL_CONSTRAINTS rc
JOIN information_schema.KEY_COLUMN_USAGE kcu
  ON kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
 AND kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
 AND kcu.TABLE_NAME = rc.TABLE_NAME
WHERE rc.CONSTRAINT_SCHEMA = DATABASE()
  AND rc.CONSTRAINT_NAME = 'fk_prof_guia_cotutela';

SELECT
    INDEX_NAME,
    NON_UNIQUE,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS columnas
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'cotutela'
GROUP BY INDEX_NAME, NON_UNIQUE
HAVING INDEX_NAME IN (
    'uq_cotutela_tesis',
    'fk_prof_guia_cotutela_idx'
);

SELECT
    CONSTRAINT_NAME,
    CONSTRAINT_TYPE
FROM information_schema.TABLE_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = DATABASE()
  AND TABLE_NAME = 'cotutela'
  AND CONSTRAINT_NAME = 'chk_cotutela_guia_xor';

SELECT tesis, COUNT(*) AS cantidad
FROM cotutela
GROUP BY tesis
HAVING COUNT(*) > 1;

SELECT @@check_constraint_checks AS check_constraint_checks;

-- ============================================================================
-- REVERSIÓN MANUAL (NO ejecutar automaticamente)
-- ============================================================================
-- Primero ejecutar:
-- SELECT
--     id_cotutela,
--     tesis,
--     id_prof_guia
-- FROM cotutela
-- WHERE id_prof_guia IS NOT NULL;
--
-- Si retorna filas:
-- NO eliminar id_prof_guia.
-- DETENER REVERSIÓN.
--
-- Solo cuando no existan identidades internas, ejecutar manualmente:
-- ALTER TABLE `cotutela`
--     DROP CONSTRAINT `chk_cotutela_guia_xor`,
--     DROP INDEX `uq_cotutela_tesis`,
--     DROP FOREIGN KEY `fk_prof_guia_cotutela`,
--     DROP INDEX `fk_prof_guia_cotutela_idx`,
--     DROP COLUMN `id_prof_guia`;
--
-- Despues verificar:
-- SELECT id_cotutela
-- FROM cotutela
-- WHERE prof_guia IS NULL
--    OR TRIM(prof_guia) = '';
--
-- Solo cuando no existan filas incompatibles, ejecutar manualmente:
-- ALTER TABLE `cotutela`
--     MODIFY COLUMN `prof_guia` VARCHAR(45) NOT NULL;
