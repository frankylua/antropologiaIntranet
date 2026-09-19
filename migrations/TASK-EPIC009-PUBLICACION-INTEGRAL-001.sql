-- TASK-EPIC009-PUBLICACION-INTEGRAL-001
-- Ejecutar después de verificar schema y conteos. No realiza backfill ni limpieza.
START TRANSACTION;

ALTER TABLE publicacion
    MODIFY usuario INT(11) NULL;

CREATE TABLE publicacion_participacion (
    id_publicacion_participacion INT(11) NOT NULL AUTO_INCREMENT,
    publicacion INT(11) NOT NULL,
    usuario INT(11) NOT NULL,
    rol ENUM('AUTOR', 'COAUTOR') NOT NULL,
    PRIMARY KEY (id_publicacion_participacion),
    UNIQUE KEY uq_publicacion_participacion (publicacion, usuario),
    CONSTRAINT fk_publicacion_participacion_publicacion
        FOREIGN KEY (publicacion) REFERENCES publicacion(id_publicacion)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_publicacion_participacion_usuario
        FOREIGN KEY (usuario) REFERENCES usuario(id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE libro_editorial
    ADD CONSTRAINT uq_libro_editorial UNIQUE (libro, editorial);

COMMIT;

-- Rollback (ejecutar sólo si no existen dependencias nuevas):
-- ALTER TABLE libro_editorial DROP INDEX uq_libro_editorial;
-- DROP TABLE publicacion_participacion;
-- ALTER TABLE publicacion MODIFY usuario INT(11) NOT NULL;
