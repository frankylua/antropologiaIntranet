<?php
declare(strict_types=1);

namespace App\Model;

use PDO;

final class Pasantia
{
    private const SELECT_FILA = 'SELECT p.id_pasantia, p.usuario, p.inst_pasant, i.inst, '
        . 'p.pais_pasant, pa.pais, p.prof_patr, p.fondo, p.ciudad, p.fech_in, p.fech_ter '
        . 'FROM pasantia p JOIN institucion i ON i.id_inst = p.inst_pasant '
        . 'JOIN pais pa ON pa.id_pais = p.pais_pasant ';

    /** @param array{inst_pasant:int,pais_pasant:int,prof_patr:string,fondo:string,ciudad:string,fech_in:string,fech_ter:string} $datos */
    public function insertar(int $usuario, array $datos): array
    {
        return ejecutarEscritura(
            'INSERT INTO pasantia (usuario, inst_pasant, pais_pasant, prof_patr, fondo, ciudad, fech_in, fech_ter) '
            . 'VALUES (:usuario, :inst_pasant, :pais_pasant, :prof_patr, :fondo, :ciudad, :fech_in, :fech_ter)',
            ['usuario' => $usuario] + $datos,
            true
        );
    }

    public function mostrar(int $usuario): array
    {
        $consulta = conexion()->prepare(self::SELECT_FILA . 'WHERE p.usuario = :usuario ORDER BY p.id_pasantia');
        $consulta->execute(['usuario' => $usuario]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function detalle(int $idPasantia): ?array
    {
        $consulta = conexion()->prepare(self::SELECT_FILA . 'WHERE p.id_pasantia = :id_pasantia');
        $consulta->execute(['id_pasantia' => $idPasantia]);
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }

    /** @param array{inst_pasant:int,pais_pasant:int,prof_patr:string,fondo:string,ciudad:string,fech_in:string,fech_ter:string} $datos */
    public function editar(int $idPasantia, int $usuario, array $datos): array
    {
        return ejecutarEscritura(
            'UPDATE pasantia SET inst_pasant = :inst_pasant, pais_pasant = :pais_pasant, '
            . 'prof_patr = :prof_patr, fondo = :fondo, ciudad = :ciudad, fech_in = :fech_in, fech_ter = :fech_ter '
            . 'WHERE id_pasantia = :id_pasantia AND usuario = :usuario',
            ['id_pasantia' => $idPasantia, 'usuario' => $usuario] + $datos
        );
    }

    public function eliminar(int $idPasantia, int $usuario): array
    {
        return ejecutarEscritura(
            'DELETE FROM pasantia WHERE id_pasantia = :id_pasantia AND usuario = :usuario',
            ['id_pasantia' => $idPasantia, 'usuario' => $usuario]
        );
    }

    public function especializacion(int $usuario): ?string
    {
        $consulta = conexion()->prepare(
            'SELECT EXISTS(SELECT 1 FROM estudiante e WHERE e.usuario = u.id_usuario) AS estudiante, '
            . 'EXISTS(SELECT 1 FROM profesor pr WHERE pr.usuario = u.id_usuario) AS profesor '
            . 'FROM usuario u WHERE u.id_usuario = :usuario'
        );
        $consulta->execute(['usuario' => $usuario]);
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($fila === false || (int) $fila['estudiante'] === (int) $fila['profesor']) {
            return null;
        }
        return (int) $fila['estudiante'] === 1 ? 'estudiante' : 'profesor';
    }

    public function institucionExiste(int $institucion): bool
    {
        $consulta = conexion()->prepare('SELECT 1 FROM institucion WHERE id_inst = :id');
        $consulta->execute(['id' => $institucion]);
        return $consulta->fetchColumn() !== false;
    }

    public function paisExiste(int $pais): bool
    {
        $consulta = conexion()->prepare('SELECT 1 FROM pais WHERE id_pais = :id');
        $consulta->execute(['id' => $pais]);
        return $consulta->fetchColumn() !== false;
    }
}
