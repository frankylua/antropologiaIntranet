-- TASK-DB-MIGRATION-FINANCIAMIENTO-RESTRICT-001
--
-- PRECONDICIONES:
-- - La constraint existente es fk_financ__proyecto.
-- - La relacion existente es proyecto_investigacion.fuente_financiamiento
--   -> financiamiento.id_financ.
-- - DELETE_RULE actual = CASCADE.
-- - UPDATE_RULE actual = CASCADE.
-- - El indice fk_financ_proyecto_idx existe y debe preservarse.
-- - Ambas tablas utilizan engine InnoDB.
-- - Huerfanos = 0.
-- - La columna fuente_financiamiento es int(11) NOT NULL.
--
-- TRANSFORMACION:
-- PASO 1: eliminar unicamente la FK fk_financ__proyecto.
-- PASO 2: recrear la misma FK con ON DELETE RESTRICT y ON UPDATE CASCADE.
-- Los dos ALTER TABLE son DDL independientes.
-- El indice fk_financ_proyecto_idx, tipos, nulabilidad, PK y datos no cambian.
--
-- ADVERTENCIA MARIA DB:
-- MariaDB realiza commits implicitos alrededor de DDL.
-- Esta migracion debe aplicarse manualmente y de forma controlada.
-- Si el primer ALTER tiene exito y el segundo falla, NO volver a ejecutar el
-- archivo completo: inspeccionar primero el estado fisico de la FK.

ALTER TABLE proyecto_investigacion
    DROP FOREIGN KEY fk_financ__proyecto;

ALTER TABLE proyecto_investigacion
    ADD CONSTRAINT fk_financ__proyecto
    FOREIGN KEY (fuente_financiamiento)
    REFERENCES financiamiento (id_financ)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;

-- POSTCONDICIONES:
-- - CONSTRAINT_NAME = fk_financ__proyecto.
-- - La referencia sigue siendo proyecto_investigacion.fuente_financiamiento
--   -> financiamiento.id_financ.
-- - UPDATE_RULE = CASCADE.
-- - DELETE_RULE = RESTRICT.
-- - El indice fk_financ_proyecto_idx sigue presente.
-- - Huerfanos = 0.
-- - Ningun Proyecto fue eliminado o modificado.
--
-- REVERSION CONCEPTUAL:
-- Eliminar la FK RESTRICT y recrearla con ON DELETE CASCADE y ON UPDATE CASCADE.
-- NO ejecutar la reversion automaticamente.
-- Requiere autorizacion de Direccion Tecnica.
