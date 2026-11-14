-- TASK-DB-MIGRATION-PERFILES-INACTIVOS-001
-- BORRADOR PARA REVISION. NO EJECUTAR sin autorizacion expresa y respaldo previo.
-- Mantener una ventana de despliegue coordinada con el codigo de inactivacion.
-- MariaDB: ALTER TABLE confirma implicitamente; no hay transaccion global del script.
-- No contiene DELETE ni modifica identidades, permisos o antecedentes academicos.

-- Preflight: debe existir chk_profesor_estado con dominio actual (1,2,3).
SELECT tc.CONSTRAINT_NAME, cc.CHECK_CLAUSE
FROM information_schema.TABLE_CONSTRAINTS tc
JOIN information_schema.CHECK_CONSTRAINTS cc
  ON cc.CONSTRAINT_SCHEMA = tc.CONSTRAINT_SCHEMA
 AND cc.CONSTRAINT_NAME = tc.CONSTRAINT_NAME
WHERE tc.CONSTRAINT_SCHEMA = DATABASE()
  AND tc.TABLE_NAME = 'profesor'
  AND tc.CONSTRAINT_TYPE = 'CHECK';

-- Preflight: debe existir exactamente el estado Estudiante 6.
SELECT id_tipo_est, tipo
FROM tipo_estudiante
WHERE id_tipo_est = 6;

-- Preflight: cero registros Profesor fuera del dominio original.
SELECT COUNT(*) AS profesores_fuera_de_dominio
FROM profesor
WHERE estado_profesor IS NULL OR estado_profesor NOT IN (1, 2, 3);

-- Ampliar el dominio en una unica sentencia DDL, conservando valores existentes.
ALTER TABLE profesor
  DROP CONSTRAINT chk_profesor_estado,
  ADD CONSTRAINT chk_profesor_estado
    CHECK (estado_profesor IN (1, 2, 3, 4));

-- El codigo 6 se conserva. No se reasignan estados de personas ni sus relaciones.
UPDATE tipo_estudiante
SET tipo = 'inactivo(a)'
WHERE id_tipo_est = 6 AND tipo = 'eliminado(a)';

-- Postflight: dominio Profesor (1,2,3,4) y etiqueta Estudiante inactivo(a).
SELECT tc.CONSTRAINT_NAME, cc.CHECK_CLAUSE
FROM information_schema.TABLE_CONSTRAINTS tc
JOIN information_schema.CHECK_CONSTRAINTS cc
  ON cc.CONSTRAINT_SCHEMA = tc.CONSTRAINT_SCHEMA
 AND cc.CONSTRAINT_NAME = tc.CONSTRAINT_NAME
WHERE tc.CONSTRAINT_SCHEMA = DATABASE()
  AND tc.TABLE_NAME = 'profesor'
  AND tc.CONSTRAINT_NAME = 'chk_profesor_estado';

SELECT id_tipo_est, tipo
FROM tipo_estudiante
WHERE id_tipo_est = 6;

-- Reversion condicionada, NO automatica:
-- Antes de volver a codigo/esquema anterior, comprobar que no existen Profesores
-- en estado 4. Si existen, detener la reversion y definir su estado con Admin/Comite.
-- No convertirlos masivamente a Aceptado ni borrar registros para revertir.
-- SELECT COUNT(*) AS inactivos_que_impiden_reversion FROM profesor WHERE estado_profesor=4;
-- Solo si el conteo anterior es cero y se autoriza la reversion:
-- ALTER TABLE profesor DROP CONSTRAINT chk_profesor_estado,
--   ADD CONSTRAINT chk_profesor_estado CHECK (estado_profesor IN (1,2,3));
-- UPDATE tipo_estudiante SET tipo='eliminado(a)' WHERE id_tipo_est=6 AND tipo='inactivo(a)';
