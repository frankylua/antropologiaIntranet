<?php
declare(strict_types=1);

namespace App\Model;

use PDO;

final class Grado
{
    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function insertar(int $usuario, int $instituto, int $titulo, string $fecha): array
    {
        return ejecutarEscritura(
            'INSERT INTO grado_academico '
            . '(usuario, inst_grado, tit_grado, fech_graduacion) '
            . 'VALUES (:usuario, :instituto, :titulo, :fecha)',
            [
                'usuario' => $usuario,
                'instituto' => $instituto,
                'titulo' => $titulo,
                'fecha' => $fecha,
            ],
            true
        );
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function editar(
        int $idGrado,
        int $usuario,
        int $instituto,
        int $titulo,
        string $fecha
    ): array {
        return ejecutarEscritura(
            'UPDATE grado_academico '
            . 'SET inst_grado = :instituto, tit_grado = :titulo, fech_graduacion = :fecha '
            . 'WHERE id_grado = :id_grado AND usuario = :usuario',
            [
                'instituto' => $instituto,
                'titulo' => $titulo,
                'fecha' => $fecha,
                'id_grado' => $idGrado,
                'usuario' => $usuario,
            ]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function mostrar(int $usuario): array
    {
        $consulta = conexion()->prepare(
            'SELECT g.id_grado, i.inst, t.tit_grado, t.tipo_grado, g.fech_graduacion '
            . 'FROM grado_academico g '
            . 'JOIN titulo_grado t ON g.tit_grado = t.id_titulo '
            . 'JOIN institucion i ON g.inst_grado = i.id_inst '
            . 'WHERE g.usuario = :usuario '
            . 'ORDER BY t.tipo_grado'
        );
        $consulta->execute(['usuario' => $usuario]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array<string, mixed>|null */
    public function mostrarGrado(int $idGrado): ?array
    {
        $consulta = conexion()->prepare(
            'SELECT g.id_grado, g.usuario, i.inst, t.tit_grado, t.tipo_grado, '
            . 'g.fech_graduacion, g.inst_grado, g.tit_grado '
            . 'FROM grado_academico g '
            . 'JOIN titulo_grado t ON g.tit_grado = t.id_titulo '
            . 'JOIN institucion i ON g.inst_grado = i.id_inst '
            . 'WHERE g.id_grado = :id_grado'
        );
        $consulta->execute(['id_grado' => $idGrado]);
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado === false ? null : $resultado;
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function eliminar(int $idGrado, int $usuario): array
    {
        return ejecutarEscritura(
            'DELETE FROM grado_academico '
            . 'WHERE id_grado = :id_grado AND usuario = :usuario',
            [
                'id_grado' => $idGrado,
                'usuario' => $usuario,
            ]
        );
    }

    public function usuarioObjetivoValido(int $usuario): bool
    {
        $consulta = conexion()->prepare(
            'SELECT '
            . 'EXISTS(SELECT 1 FROM estudiante e WHERE e.usuario = u.id_usuario) AS es_estudiante, '
            . 'EXISTS(SELECT 1 FROM profesor p WHERE p.usuario = u.id_usuario) AS es_profesor '
            . 'FROM usuario u WHERE u.id_usuario = :usuario'
        );
        $consulta->execute(['usuario' => $usuario]);
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($resultado === false) {
            return false;
        }

        $esEstudiante = (int) $resultado['es_estudiante'] === 1;
        $esProfesor = (int) $resultado['es_profesor'] === 1;
        return $esEstudiante !== $esProfesor;
    }

    public function institucionExiste(int $instituto): bool
    {
        $consulta = conexion()->prepare(
            'SELECT 1 FROM institucion WHERE id_inst = :instituto LIMIT 1'
        );
        $consulta->execute(['instituto' => $instituto]);
        return $consulta->fetchColumn() !== false;
    }

    public function tituloExiste(int $titulo): bool
    {
        $consulta = conexion()->prepare(
            'SELECT 1 FROM titulo_grado WHERE id_titulo = :titulo LIMIT 1'
        );
        $consulta->execute(['titulo' => $titulo]);
        return $consulta->fetchColumn() !== false;
    }
}
