-- TASK-DB-MIGRATION-DELETE-RESTRICT-001
-- AT: AT-DB-MIGRATION-DELETE-RESTRICT-001
-- Objetivo: impedir la eliminacion de catalogos pueblo y titulo_grado en uso.
-- Motor esperado: MySQL/MariaDB (tablas InnoDB).
--
-- IMPORTANTE:
--   * Ejecutar primero en un ambiente controlado y con respaldo verificado.
--   * No ejecutarlo si existen diferencias de esquema entre ambientes, si se
--     requieren cambios de datos, si hay un bloqueo prolongado de metadata o
--     si no puede garantizarse el rollback documentado al final del archivo.
--   * Los ALTER TABLE producen commit implicito; la migracion no es atomica.

DELIMITER $$

DROP PROCEDURE IF EXISTS `task_db_migration_delete_restrict_001_validate_before`$$
CREATE PROCEDURE `task_db_migration_delete_restrict_001_validate_before`()
BEGIN
    DECLARE pueblo_matches INT DEFAULT 0;
    DECLARE titulo_matches INT DEFAULT 0;
    DECLARE pueblo_named_constraints INT DEFAULT 0;
    DECLARE titulo_named_constraints INT DEFAULT 0;

    -- Valida nombre, tablas, columnas y reglas actuales antes de ejecutar DDL.
    SELECT COUNT(*)
      INTO pueblo_named_constraints
      FROM information_schema.REFERENTIAL_CONSTRAINTS
     WHERE CONSTRAINT_SCHEMA = DATABASE()
       AND CONSTRAINT_NAME = 'fk_pueblo';

    SELECT COUNT(*)
      INTO titulo_named_constraints
      FROM information_schema.REFERENTIAL_CONSTRAINTS
     WHERE CONSTRAINT_SCHEMA = DATABASE()
       AND CONSTRAINT_NAME = 'fk_titulo_grado';

    SELECT COUNT(*)
      INTO pueblo_matches
      FROM information_schema.REFERENTIAL_CONSTRAINTS rc
      JOIN information_schema.KEY_COLUMN_USAGE kcu
        ON kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
       AND kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
       AND kcu.TABLE_NAME = rc.TABLE_NAME
     WHERE rc.CONSTRAINT_SCHEMA = DATABASE()
       AND rc.CONSTRAINT_NAME = 'fk_pueblo'
       AND rc.TABLE_NAME = 'usuario'
       AND rc.REFERENCED_TABLE_NAME = 'pueblo'
       AND kcu.COLUMN_NAME = 'pueblo'
       AND kcu.REFERENCED_COLUMN_NAME = 'id_pueblo'
       AND kcu.ORDINAL_POSITION = 1
       AND rc.DELETE_RULE = 'CASCADE'
       AND rc.UPDATE_RULE = 'CASCADE';

    SELECT COUNT(*)
      INTO titulo_matches
      FROM information_schema.REFERENTIAL_CONSTRAINTS rc
      JOIN information_schema.KEY_COLUMN_USAGE kcu
        ON kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
       AND kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
       AND kcu.TABLE_NAME = rc.TABLE_NAME
     WHERE rc.CONSTRAINT_SCHEMA = DATABASE()
       AND rc.CONSTRAINT_NAME = 'fk_titulo_grado'
       AND rc.TABLE_NAME = 'grado_academico'
       AND rc.REFERENCED_TABLE_NAME = 'titulo_grado'
       AND kcu.COLUMN_NAME = 'tit_grado'
       AND kcu.REFERENCED_COLUMN_NAME = 'id_titulo'
       AND kcu.ORDINAL_POSITION = 1
       AND rc.DELETE_RULE = 'CASCADE'
       AND rc.UPDATE_RULE = 'CASCADE';

    IF pueblo_named_constraints <> 1 OR pueblo_matches <> 1 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'DETENCION: fk_pueblo no coincide con la definicion CASCADE esperada';
    END IF;

    IF titulo_named_constraints <> 1 OR titulo_matches <> 1 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'DETENCION: fk_titulo_grado no coincide con la definicion CASCADE esperada';
    END IF;
END$$

CALL `task_db_migration_delete_restrict_001_validate_before`()$$
DROP PROCEDURE `task_db_migration_delete_restrict_001_validate_before`$$

ALTER TABLE `usuario`
    DROP FOREIGN KEY `fk_pueblo`,
    ADD CONSTRAINT `fk_pueblo`
        FOREIGN KEY (`pueblo`) REFERENCES `pueblo` (`id_pueblo`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE$$

ALTER TABLE `grado_academico`
    DROP FOREIGN KEY `fk_titulo_grado`,
    ADD CONSTRAINT `fk_titulo_grado`
        FOREIGN KEY (`tit_grado`) REFERENCES `titulo_grado` (`id_titulo`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE$$

DROP PROCEDURE IF EXISTS `task_db_migration_delete_restrict_001_validate_after`$$
CREATE PROCEDURE `task_db_migration_delete_restrict_001_validate_after`()
BEGIN
    DECLARE pueblo_matches INT DEFAULT 0;
    DECLARE titulo_matches INT DEFAULT 0;

    SELECT COUNT(*)
      INTO pueblo_matches
      FROM information_schema.REFERENTIAL_CONSTRAINTS rc
      JOIN information_schema.KEY_COLUMN_USAGE kcu
        ON kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
       AND kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
       AND kcu.TABLE_NAME = rc.TABLE_NAME
     WHERE rc.CONSTRAINT_SCHEMA = DATABASE()
       AND rc.CONSTRAINT_NAME = 'fk_pueblo'
       AND rc.TABLE_NAME = 'usuario'
       AND rc.REFERENCED_TABLE_NAME = 'pueblo'
       AND kcu.COLUMN_NAME = 'pueblo'
       AND kcu.REFERENCED_COLUMN_NAME = 'id_pueblo'
       AND kcu.ORDINAL_POSITION = 1
       AND rc.DELETE_RULE = 'RESTRICT'
       AND rc.UPDATE_RULE = 'CASCADE';

    SELECT COUNT(*)
      INTO titulo_matches
      FROM information_schema.REFERENTIAL_CONSTRAINTS rc
      JOIN information_schema.KEY_COLUMN_USAGE kcu
        ON kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
       AND kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
       AND kcu.TABLE_NAME = rc.TABLE_NAME
     WHERE rc.CONSTRAINT_SCHEMA = DATABASE()
       AND rc.CONSTRAINT_NAME = 'fk_titulo_grado'
       AND rc.TABLE_NAME = 'grado_academico'
       AND rc.REFERENCED_TABLE_NAME = 'titulo_grado'
       AND kcu.COLUMN_NAME = 'tit_grado'
       AND kcu.REFERENCED_COLUMN_NAME = 'id_titulo'
       AND kcu.ORDINAL_POSITION = 1
       AND rc.DELETE_RULE = 'RESTRICT'
       AND rc.UPDATE_RULE = 'CASCADE';

    IF pueblo_matches <> 1 OR titulo_matches <> 1 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'FALLO: validacion posterior de las FK RESTRICT no superada';
    END IF;
END$$

CALL `task_db_migration_delete_restrict_001_validate_after`()$$
DROP PROCEDURE `task_db_migration_delete_restrict_001_validate_after`$$

DELIMITER ;

-- Evidencia posterior (debe devolver RESTRICT/CASCADE para ambas filas):
SELECT CONSTRAINT_NAME, TABLE_NAME, REFERENCED_TABLE_NAME, DELETE_RULE, UPDATE_RULE
  FROM information_schema.REFERENTIAL_CONSTRAINTS
 WHERE CONSTRAINT_SCHEMA = DATABASE()
   AND CONSTRAINT_NAME IN ('fk_pueblo', 'fk_titulo_grado')
 ORDER BY CONSTRAINT_NAME;

-- Pruebas funcionales BD (ejecutar separadamente con IDs de prueba conocidos):
--
-- 1. Catalogo sin dependencia: DELETE debe completarse.
--    START TRANSACTION;
--    DELETE FROM pueblo WHERE id_pueblo = <ID_PUEBLO_SIN_DEPENDENCIA>;
--    ROLLBACK;
--    Repetir para titulo_grado.
--
-- 2. Catalogo con dependencia: DELETE debe fallar con error de FK y el registro
--    dependiente debe permanecer intacto.
--    DELETE FROM pueblo WHERE id_pueblo = <ID_PUEBLO_CON_USUARIO>;
--    SELECT COUNT(*) FROM usuario
--     WHERE pueblo = <ID_PUEBLO_CON_USUARIO>; -- resultado esperado: sin cambios
--    Repetir para titulo_grado/grado_academico.
--
-- La capa de aplicacion debera traducir una futura violacion de FK a:
-- {"ok":false,"codigo":"TIENE_DEPENDENCIAS","mensaje":"No se puede eliminar porque existen registros asociados"}

-- ROLLBACK MANUAL (solo ante falla tecnica; restaura el comportamiento riesgoso):
-- Verificar primero que ambas FK conservan nombres, tablas y columnas esperadas.
--
-- ALTER TABLE `usuario`
--     DROP FOREIGN KEY `fk_pueblo`,
--     ADD CONSTRAINT `fk_pueblo`
--         FOREIGN KEY (`pueblo`) REFERENCES `pueblo` (`id_pueblo`)
--         ON DELETE CASCADE
--         ON UPDATE CASCADE;
--
-- ALTER TABLE `grado_academico`
--     DROP FOREIGN KEY `fk_titulo_grado`,
--     ADD CONSTRAINT `fk_titulo_grado`
--         FOREIGN KEY (`tit_grado`) REFERENCES `titulo_grado` (`id_titulo`)
--         ON DELETE CASCADE
--         ON UPDATE CASCADE;
