<?php
declare(strict_types=1);
namespace App\Model;

class Financiamiento
{
    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function insertar(string $nombre): array
    {
        return ejecutarEscritura(
            'INSERT INTO financiamiento (financiamiento) VALUES (:nombre)',
            [':nombre' => $nombre]
        );
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function editar(int $id, string $nombre): array
    {
        return ejecutarEscritura(
            'UPDATE financiamiento SET financiamiento = :nombre WHERE id_financ = :id',
            [':nombre' => $nombre, ':id' => $id]
        );
    }

    public function mostrar(): array
    {
        return ejecutarConsultaResultados(
            'SELECT id_financ, financiamiento FROM financiamiento ORDER BY financiamiento'
        );
    }

    public function existe(int $id): bool
    {
        $consulta = conexion()->prepare(
            'SELECT 1 FROM financiamiento WHERE id_financ = :id LIMIT 1'
        );
        $consulta->execute([':id' => $id]);
        return $consulta->fetchColumn() !== false;
    }

    public function estaEnUso(int $id): bool
    {
        $consulta = conexion()->prepare(
            'SELECT 1 FROM proyecto_investigacion '
            . 'WHERE fuente_financiamiento = :id LIMIT 1'
        );
        $consulta->execute([':id' => $id]);
        return $consulta->fetchColumn() !== false;
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function eliminar(int $id): array
    {
        return ejecutarEscritura(
            'DELETE FROM financiamiento WHERE id_financ = :id',
            [':id' => $id]
        );
    }
}
?>
