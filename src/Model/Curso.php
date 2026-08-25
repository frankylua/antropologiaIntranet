<?php
declare(strict_types=1);

namespace App\Model;

use PDO;

final class Curso
{
    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function insertar(
        string $creditos,
        int $caracter,
        int $periodo,
        int $anioCurso,
        int $cargaHoraria,
        int $nombreCurso,
        int $idDocumentoPrograma,
        int $profesor
    ): array {
        return ejecutarEscritura(
            'INSERT INTO curso '
            . '(creditos, caracter, periodo, anio_curso, carga_hor, nom_curso, profesor, '
            . 'arch_prog, id_documento_programa) '
            . 'VALUES (:creditos, :caracter, :periodo, :anio_curso, :carga_hor, :nom_curso, '
            . ':profesor, NULL, :id_documento_programa)',
            [
                'creditos' => $creditos,
                'caracter' => $caracter,
                'periodo' => $periodo,
                'anio_curso' => $anioCurso,
                'carga_hor' => $cargaHoraria,
                'nom_curso' => $nombreCurso,
                'profesor' => $profesor,
                'id_documento_programa' => $idDocumentoPrograma,
            ],
            true,
            [
                'creditos' => PDO::PARAM_STR,
                'caracter' => PDO::PARAM_INT,
                'periodo' => PDO::PARAM_INT,
                'anio_curso' => PDO::PARAM_INT,
                'carga_hor' => PDO::PARAM_INT,
                'nom_curso' => PDO::PARAM_INT,
                'profesor' => PDO::PARAM_INT,
                'id_documento_programa' => PDO::PARAM_INT,
            ]
        );
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function editar(
        int $idCurso,
        string $creditos,
        int $caracter,
        int $periodo,
        int $anioCurso,
        int $cargaHoraria,
        int $nombreCurso,
        ?int $idDocumentoPrograma,
        int $profesor
    ): array {
        return ejecutarEscritura(
            'UPDATE curso SET creditos = :creditos, caracter = :caracter, periodo = :periodo, '
            . 'anio_curso = :anio_curso, carga_hor = :carga_hor, nom_curso = :nom_curso, '
            . 'profesor = :profesor, id_documento_programa = :id_documento_programa '
            . 'WHERE id_curso = :id_curso',
            [
                'creditos' => $creditos,
                'caracter' => $caracter,
                'periodo' => $periodo,
                'anio_curso' => $anioCurso,
                'carga_hor' => $cargaHoraria,
                'nom_curso' => $nombreCurso,
                'profesor' => $profesor,
                'id_documento_programa' => $idDocumentoPrograma,
                'id_curso' => $idCurso,
            ],
            false,
            [
                'creditos' => PDO::PARAM_STR,
                'caracter' => PDO::PARAM_INT,
                'periodo' => PDO::PARAM_INT,
                'anio_curso' => PDO::PARAM_INT,
                'carga_hor' => PDO::PARAM_INT,
                'nom_curso' => PDO::PARAM_INT,
                'profesor' => PDO::PARAM_INT,
                'id_documento_programa' => $idDocumentoPrograma === null
                    ? PDO::PARAM_NULL
                    : PDO::PARAM_INT,
                'id_curso' => PDO::PARAM_INT,
            ]
        );
    }

    /** @return list<array<string, mixed>> */
    public function mostrar(): array
    {
        $consulta = conexion()->prepare(
            'SELECT c.id_curso, c.creditos, c.caracter, c.periodo, c.anio_curso, c.carga_hor, '
            . 'c.nom_curso AS id_nom_curso, n.nom_curso, c.profesor, u.nombres, u.ap_pat, u.ap_mat '
            . 'FROM curso c JOIN nombre_curso n ON n.id_nom_curso = c.nom_curso '
            . 'JOIN usuario u ON u.id_usuario = c.profesor '
            . 'ORDER BY c.anio_curso DESC, n.nom_curso, c.id_curso'
        );
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array<string, mixed>> */
    public function mostrarCursos(): array
    {
        $consulta = conexion()->prepare(
            'SELECT id_nom_curso, nom_curso FROM nombre_curso ORDER BY nom_curso'
        );
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array<string, mixed>|null */
    public function mostrarPorId(int $idCurso, bool $bloquear = false): ?array
    {
        $sql = 'SELECT c.id_curso, c.creditos, c.caracter, c.periodo, c.anio_curso, c.carga_hor, '
            . 'c.nom_curso AS id_nom_curso, n.nom_curso, c.profesor, c.arch_prog, '
            . 'c.id_documento_programa, '
            . 'u.nombres, u.ap_pat, u.ap_mat '
            . 'FROM curso c '
            . 'JOIN nombre_curso n ON n.id_nom_curso = c.nom_curso '
            . 'JOIN usuario u ON u.id_usuario = c.profesor '
            . 'WHERE c.id_curso = :id_curso';
        if ($bloquear) {
            $sql .= ' FOR UPDATE';
        }
        $consulta = conexion()->prepare($sql);
        $consulta->execute(['id_curso' => $idCurso]);
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado === false ? null : $resultado;
    }

    public function nombreCursoExiste(int $idNombreCurso): bool
    {
        $consulta = conexion()->prepare(
            'SELECT id_nom_curso FROM nombre_curso WHERE id_nom_curso = :id_nom_curso'
        );
        $consulta->execute(['id_nom_curso' => $idNombreCurso]);
        return $consulta->fetchColumn() !== false;
    }

    /** @return list<array<string, mixed>> */
    public function buscarIdentidadProfesor(int $idUsuario, bool $bloquear = false): array
    {
        $sql = 'SELECT u.id_usuario, p.id_profesor, p.estado_profesor '
            . 'FROM usuario u LEFT JOIN profesor p ON p.usuario = u.id_usuario '
            . 'WHERE u.id_usuario = :id_usuario';
        if ($bloquear) {
            $sql .= ' FOR UPDATE';
        }
        $consulta = conexion()->prepare($sql);
        $consulta->execute(['id_usuario' => $idUsuario]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function eliminar(int $idCurso): array
    {
        return ejecutarEscritura(
            'DELETE FROM curso WHERE id_curso = :id_curso',
            ['id_curso' => $idCurso]
        );
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function asociarDocumentoPrograma(int $idCurso, int $idDocumento): array
    {
        return ejecutarEscritura(
            'UPDATE curso SET id_documento_programa = :id_documento '
            . 'WHERE id_curso = :id_curso AND id_documento_programa IS NULL',
            [
                'id_documento' => $idDocumento,
                'id_curso' => $idCurso,
            ],
            false,
            [
                'id_documento' => PDO::PARAM_INT,
                'id_curso' => PDO::PARAM_INT,
            ]
        );
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function actualizarProgramaLegacy(int $idCurso, string $programa): array
    {
        return ejecutarEscritura(
            'UPDATE curso SET arch_prog = :arch_prog WHERE id_curso = :id_curso',
            [
                'arch_prog' => $programa,
                'id_curso' => $idCurso,
            ],
            false,
            [
                'arch_prog' => PDO::PARAM_STR,
                'id_curso' => PDO::PARAM_INT,
            ]
        );
    }
}
