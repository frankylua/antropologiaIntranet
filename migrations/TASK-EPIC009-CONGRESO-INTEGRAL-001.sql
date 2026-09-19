-- TASK-EPIC009-CONGRESO-INTEGRAL-001
-- Fase B: no ejecutar automáticamente.

ALTER TABLE `participacion`
  ADD COLUMN `nom_coaut` VARCHAR(60) NULL AFTER `id_coaut`;
