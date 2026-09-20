<?php
declare(strict_types=1);

namespace App\Model;

final class Proyecto
{
    public function insertar(array $datos): array
    {
        return ejecutarEscritura('INSERT INTO proyecto_investigacion (titulo, folio, fuente_financiamiento, anio_adjud, duracion, inst_proy, id_inv, id_coinv, nom_inv, nom_coinv) VALUES (:titulo, :folio, :financiamiento, :anio, :duracion, :institucion, :id_inv, :id_coinv, :nom_inv, :nom_coinv)', $datos, true);
    }

    public function actualizar(int $idProyecto, array $datos): array
    {
        $datos['id_proyecto'] = $idProyecto;
        return ejecutarEscritura('UPDATE proyecto_investigacion SET titulo=:titulo, folio=:folio, fuente_financiamiento=:financiamiento, anio_adjud=:anio, duracion=:duracion, inst_proy=:institucion, id_inv=:id_inv, id_coinv=:id_coinv, nom_inv=:nom_inv, nom_coinv=:nom_coinv WHERE id_proyecto=:id_proyecto', $datos);
    }

    public function eliminar(int $idProyecto): array
    {
        return ejecutarEscritura('DELETE FROM proyecto_investigacion WHERE id_proyecto=:id_proyecto', ['id_proyecto' => $idProyecto]);
    }

    public function detalle(int $idProyecto): ?array
    {
        $consulta = conexion()->prepare($this->consultaBase() . ' WHERE p.id_proyecto=:id_proyecto');
        $consulta->execute(['id_proyecto' => $idProyecto]);
        $fila = $consulta->fetch(\PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }

    public function listarContextual(int $subject): array
    {
        $consulta = conexion()->prepare($this->consultaBase() . ' WHERE p.id_inv=:subject_inv OR p.id_coinv=:subject_coinv ORDER BY p.folio, p.id_proyecto');
        $consulta->execute(['subject_inv' => $subject, 'subject_coinv' => $subject]);
        return $consulta->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listarGlobal(): array
    {
        return conexion()->query($this->consultaBase() . ' ORDER BY p.folio, p.id_proyecto')->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function financiamientoExiste(int $id): bool { return $this->existe('financiamiento', 'id_financ', $id); }
    public function institucionExiste(int $id): bool { return $this->existe('institucion', 'id_inst', $id); }

    private function existe(string $tabla, string $columna, int $id): bool
    {
        $consulta = conexion()->prepare("SELECT 1 FROM {$tabla} WHERE {$columna}=:id LIMIT 1");
        $consulta->execute(['id' => $id]);
        return $consulta->fetchColumn() !== false;
    }

    private function consultaBase(): string
    {
        return "SELECT p.id_proyecto, p.titulo, p.folio, p.fuente_financiamiento, p.anio_adjud, p.duracion, p.inst_proy, p.id_inv, p.id_coinv, p.nom_inv, p.nom_coinv, f.financiamiento, i.inst AS institucion_coinvestigador, TRIM(CONCAT_WS(' ', ui.nombres, ui.ap_pat, ui.ap_mat)) AS investigador_interno, TRIM(CONCAT_WS(' ', uc.nombres, uc.ap_pat, uc.ap_mat)) AS coinvestigador_interno FROM proyecto_investigacion p INNER JOIN financiamiento f ON f.id_financ=p.fuente_financiamiento LEFT JOIN institucion i ON i.id_inst=p.inst_proy LEFT JOIN usuario ui ON ui.id_usuario=p.id_inv LEFT JOIN usuario uc ON uc.id_usuario=p.id_coinv";
    }
}
