-- TASK-DB-MIGRATION-PAIS-RESTRICT-001
-- Cambia exclusivamente ON DELETE CASCADE a ON DELETE RESTRICT en las cinco
-- claves foraneas que referencian pais(id_pais). Conserva ON UPDATE CASCADE.
--
-- ADVERTENCIA OPERACIONAL:
-- MariaDB/MySQL puede realizar COMMIT implicito alrededor de ALTER TABLE.
-- No se debe asumir un rollback transaccional de todo este archivo.
-- Ejecutar y verificar cada par DROP/ADD paso a paso. Ante cualquier error,
-- detenerse y no volver a ejecutar ciegamente el archivo completo.
--
-- PRECONDICIONES (confirmar antes de ejecutar el DDL):
-- 1. Cada constraint indicada abajo existe con la tabla y columna esperadas.
-- 2. DELETE_RULE = CASCADE y UPDATE_RULE = CASCADE para las cinco constraints.
-- 3. Ninguna de las cinco columnas contiene referencias huerfanas.

-- Precheck de existencia, referencias y reglas actuales (debe devolver 5 filas).
SELECT
    rc.CONSTRAINT_NAME,
    kcu.TABLE_NAME,
    kcu.COLUMN_NAME,
    kcu.REFERENCED_TABLE_NAME,
    kcu.REFERENCED_COLUMN_NAME,
    rc.DELETE_RULE,
    rc.UPDATE_RULE
FROM information_schema.REFERENTIAL_CONSTRAINTS AS rc
INNER JOIN information_schema.KEY_COLUMN_USAGE AS kcu
    ON kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
   AND kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
   AND kcu.TABLE_NAME = rc.TABLE_NAME
WHERE rc.CONSTRAINT_SCHEMA = DATABASE()
  AND (rc.CONSTRAINT_NAME, rc.TABLE_NAME, kcu.COLUMN_NAME) IN (
      ('fk_pais_cotutela', 'cotutela', 'pais_cot'),
      ('fk_pais_pasant', 'pasantia', 'pais_pasant'),
      ('fk_pais_tesis', 'tesis', 'pais_tesis'),
      ('fk_pais_nacionalidad', 'usuario', 'pais_nac'),
      ('fk_pais_residencia', 'usuario', 'pais_res')
  )
ORDER BY rc.CONSTRAINT_NAME;

-- Precheck de huerfanos (cada resultado debe ser 0).
SELECT 'fk_pais_cotutela' AS constraint_name, COUNT(*) AS orphan_count
FROM cotutela AS c LEFT JOIN pais AS p ON p.id_pais = c.pais_cot
WHERE c.pais_cot IS NOT NULL AND p.id_pais IS NULL
UNION ALL
SELECT 'fk_pais_pasant', COUNT(*)
FROM pasantia AS ps LEFT JOIN pais AS p ON p.id_pais = ps.pais_pasant
WHERE ps.pais_pasant IS NOT NULL AND p.id_pais IS NULL
UNION ALL
SELECT 'fk_pais_tesis', COUNT(*)
FROM tesis AS t LEFT JOIN pais AS p ON p.id_pais = t.pais_tesis
WHERE t.pais_tesis IS NOT NULL AND p.id_pais IS NULL
UNION ALL
SELECT 'fk_pais_nacionalidad', COUNT(*)
FROM usuario AS u LEFT JOIN pais AS p ON p.id_pais = u.pais_nac
WHERE u.pais_nac IS NOT NULL AND p.id_pais IS NULL
UNION ALL
SELECT 'fk_pais_residencia', COUNT(*)
FROM usuario AS u LEFT JOIN pais AS p ON p.id_pais = u.pais_res
WHERE u.pais_res IS NOT NULL AND p.id_pais IS NULL;

ALTER TABLE cotutela
    DROP FOREIGN KEY fk_pais_cotutela;

ALTER TABLE cotutela
    ADD CONSTRAINT fk_pais_cotutela
    FOREIGN KEY (pais_cot)
    REFERENCES pais (id_pais)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;

ALTER TABLE pasantia
    DROP FOREIGN KEY fk_pais_pasant;

ALTER TABLE pasantia
    ADD CONSTRAINT fk_pais_pasant
    FOREIGN KEY (pais_pasant)
    REFERENCES pais (id_pais)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;

ALTER TABLE tesis
    DROP FOREIGN KEY fk_pais_tesis;

ALTER TABLE tesis
    ADD CONSTRAINT fk_pais_tesis
    FOREIGN KEY (pais_tesis)
    REFERENCES pais (id_pais)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;

ALTER TABLE usuario
    DROP FOREIGN KEY fk_pais_nacionalidad;

ALTER TABLE usuario
    ADD CONSTRAINT fk_pais_nacionalidad
    FOREIGN KEY (pais_nac)
    REFERENCES pais (id_pais)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;

ALTER TABLE usuario
    DROP FOREIGN KEY fk_pais_residencia;

ALTER TABLE usuario
    ADD CONSTRAINT fk_pais_residencia
    FOREIGN KEY (pais_res)
    REFERENCES pais (id_pais)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;

-- POSTCONDICIONES:
-- La consulta debe devolver exactamente 5 filas, todas con pais(id_pais),
-- DELETE_RULE = RESTRICT y UPDATE_RULE = CASCADE.
SELECT
    rc.CONSTRAINT_NAME,
    kcu.TABLE_NAME,
    kcu.COLUMN_NAME,
    kcu.REFERENCED_TABLE_NAME,
    kcu.REFERENCED_COLUMN_NAME,
    rc.DELETE_RULE,
    rc.UPDATE_RULE
FROM information_schema.REFERENTIAL_CONSTRAINTS AS rc
INNER JOIN information_schema.KEY_COLUMN_USAGE AS kcu
    ON kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
   AND kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
   AND kcu.TABLE_NAME = rc.TABLE_NAME
WHERE rc.CONSTRAINT_SCHEMA = DATABASE()
  AND (rc.CONSTRAINT_NAME, rc.TABLE_NAME, kcu.COLUMN_NAME) IN (
      ('fk_pais_cotutela', 'cotutela', 'pais_cot'),
      ('fk_pais_pasant', 'pasantia', 'pais_pasant'),
      ('fk_pais_tesis', 'tesis', 'pais_tesis'),
      ('fk_pais_nacionalidad', 'usuario', 'pais_nac'),
      ('fk_pais_residencia', 'usuario', 'pais_res')
  )
ORDER BY rc.CONSTRAINT_NAME;

-- Postcheck de referencias y huerfanos (cada resultado debe ser 0).
SELECT 'fk_pais_cotutela' AS constraint_name, COUNT(*) AS orphan_count
FROM cotutela AS c LEFT JOIN pais AS p ON p.id_pais = c.pais_cot
WHERE c.pais_cot IS NOT NULL AND p.id_pais IS NULL
UNION ALL
SELECT 'fk_pais_pasant', COUNT(*)
FROM pasantia AS ps LEFT JOIN pais AS p ON p.id_pais = ps.pais_pasant
WHERE ps.pais_pasant IS NOT NULL AND p.id_pais IS NULL
UNION ALL
SELECT 'fk_pais_tesis', COUNT(*)
FROM tesis AS t LEFT JOIN pais AS p ON p.id_pais = t.pais_tesis
WHERE t.pais_tesis IS NOT NULL AND p.id_pais IS NULL
UNION ALL
SELECT 'fk_pais_nacionalidad', COUNT(*)
FROM usuario AS u LEFT JOIN pais AS p ON p.id_pais = u.pais_nac
WHERE u.pais_nac IS NOT NULL AND p.id_pais IS NULL
UNION ALL
SELECT 'fk_pais_residencia', COUNT(*)
FROM usuario AS u LEFT JOIN pais AS p ON p.id_pais = u.pais_res
WHERE u.pais_res IS NOT NULL AND p.id_pais IS NULL;

-- REVERSIÓN CONCEPTUAL (no ejecutar como parte de esta migracion):
-- Para cada constraint, aplicar nuevamente dos statements independientes:
-- primero DROP FOREIGN KEY y despues ADD CONSTRAINT con ON DELETE CASCADE y
-- ON UPDATE CASCADE, conservando tabla, columna, pais(id_pais) y nombre.
