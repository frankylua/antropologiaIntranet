-- TASK-EPIC009-PUBLICACION-AUTORIA-PRINCIPAL-001
-- Incremental sobre TASK-EPIC009-PUBLICACION-INTEGRAL-001. No ejecutar automáticamente.
-- Precondición operacional verificada antes de preparar: publicacion_participacion sin filas.
START TRANSACTION;

ALTER TABLE publicacion_participacion
    MODIFY usuario INT(11) NULL,
    ADD COLUMN nombre_externo VARCHAR(120) NULL AFTER usuario,
    ADD CONSTRAINT uq_publicacion_participacion_rol UNIQUE (publicacion, rol),
    ADD CONSTRAINT chk_publicacion_participacion_identidad CHECK (
        (usuario IS NOT NULL AND nombre_externo IS NULL)
        OR (usuario IS NULL AND nombre_externo IS NOT NULL)
    );

COMMIT;

-- Conserva UNIQUE(publicacion, usuario), las FK existentes y sus cascadas.
-- MariaDB 10.4.32 aplica CHECK; la validación equivalente vive en Publicacion.
