<?php
declare(strict_types=1);

namespace App\Model;

use PDO;

final class Postdoctorado
{
    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function insertar(
        int $usuario,
        string $profesor,
        int $institucion,
        string $fechaInicio,
        string $fechaTermino
    ): array {
        return ejecutarEscritura(
            'INSERT INTO postdoctorado '
            . '(inst_postdoc, fecha_inicio, fecha_termino, prof, usuario) '
            . 'VALUES (:institucion, :fecha_inicio, :fecha_termino, :profesor, :usuario)',
            [
                'institucion' => $institucion,
                'fecha_inicio' => $fechaInicio,
                'fecha_termino' => $fechaTermino,
                'profesor' => $profesor,
                'usuario' => $usuario,
            ],
            true
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function mostrar(int $usuario): array
    {
        $consulta = conexion()->prepare(
            'SELECT p.id_postdoc, i.id_inst, p.prof, i.inst, '
            . 'p.fecha_inicio, p.fecha_termino '
            . 'FROM postdoctorado p '
            . 'JOIN institucion i ON p.inst_postdoc = i.id_inst '
            . 'WHERE p.usuario = :usuario'
        );
        $consulta->execute(['usuario' => $usuario]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array<string, mixed>|null */
    public function mostrarPostdoc(int $idPostdoctorado): ?array
    {
        $consulta = conexion()->prepare(
            'SELECT p.id_postdoc, p.usuario, p.inst_postdoc, i.id_inst, '
            . 'p.prof, i.inst, p.fecha_inicio, p.fecha_termino '
            . 'FROM postdoctorado p '
            . 'JOIN institucion i ON p.inst_postdoc = i.id_inst '
            . 'WHERE p.id_postdoc = :id_postdoc'
        );
        $consulta->execute(['id_postdoc' => $idPostdoctorado]);
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado === false ? null : $resultado;
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function editarPostdoc(
        int $idPostdoctorado,
        int $usuario,
        string $profesor,
        int $institucion,
        string $fechaInicio,
        string $fechaTermino
    ): array {
        return ejecutarEscritura(
            'UPDATE postdoctorado '
            . 'SET inst_postdoc = :institucion, fecha_inicio = :fecha_inicio, '
            . 'fecha_termino = :fecha_termino, prof = :profesor '
            . 'WHERE id_postdoc = :id_postdoc AND usuario = :usuario',
            [
                'institucion' => $institucion,
                'fecha_inicio' => $fechaInicio,
                'fecha_termino' => $fechaTermino,
                'profesor' => $profesor,
                'id_postdoc' => $idPostdoctorado,
                'usuario' => $usuario,
            ]
        );
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function eliminar(int $idPostdoctorado, int $usuario): array
    {
        return ejecutarEscritura(
            'DELETE FROM postdoctorado '
            . 'WHERE id_postdoc = :id_postdoc AND usuario = :usuario',
            [
                'id_postdoc' => $idPostdoctorado,
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

    public function institucionExiste(int $institucion): bool
    {
        $consulta = conexion()->prepare(
            'SELECT 1 FROM institucion WHERE id_inst = :institucion LIMIT 1'
        );
        $consulta->execute(['institucion' => $institucion]);
        return $consulta->fetchColumn() !== false;
    }
}
