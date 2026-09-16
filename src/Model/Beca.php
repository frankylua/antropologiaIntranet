<?php
declare(strict_types=1);
namespace App\Model;

class Beca {
    public const ESTADO_APROBADA = 'APROBADA';
    public const ESTADO_PENDIENTE = 'PENDIENTE';
    public const ESTADO_INACTIVA = 'INACTIVA';

    public function __construct(){
    }

    /*
     * Adaptadores temporales para los consumers actuales.
     * ajax/beca.php debe migrar a los contratos explícitos en esta Task.
     */
    public function insertar($nombre,$tipo_beca,$inst,$fecha_in,$fecha_ter,$alumno){
        return $this->crearBeca(
            (int) $alumno,
            (int) $nombre,
            (int) $inst,
            (string) $fecha_in,
            (string) $fecha_ter
        );
    }

    public function insertarObtenerId($nombre,$tipo_beca){
        $resultado = $this->crearCatalogoOficial(
            (string) $nombre,
            (int) $tipo_beca
        );

        if (
            (int) $resultado['filasAfectadas'] !== 1
            || $resultado['idInsertado'] === null
        ) {
            return null;
        }

        return $resultado['idInsertado'];
    }

    public function insertarList($nombre,$tipo_int){
        return $this->crearCatalogoOficial(
            (string) $nombre,
            (int) $tipo_int
        );
    }

    public function editar($id,$nombre){
        $catalogo = $this->obtenerNombreBeca((int) $id);

        if ($catalogo === null) {
            return [
                'filasAfectadas' => 0,
                'idInsertado' => null,
            ];
        }

        return $this->actualizarCatalogo(
            (int) $id,
            (string) $nombre,
            (int) $catalogo['tipo_beca']
        );
    }

    public function mostrar($usuario){
        return $this->listarBecas((int) $usuario);
    }

    public function mostrarLista($tipo){
        return $this->listarCatalogo((int) $tipo, null);
    }

    public function mostrarNomBeca($tipo_beca){
        return $this->listarOpcionesCatalogo((int) $tipo_beca, null);
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function crearBeca(
        int $alumno,
        int $nombreBeca,
        int $institucion,
        string $fechaInicio,
        string $fechaTermino
    ): array {
        return ejecutarEscritura(
            'INSERT INTO beca '
            . '(nom_beca, inst_beca, fech_in, fech_ter, alumno) '
            . 'VALUES (:nombre_beca, :institucion, :fecha_inicio, :fecha_termino, :alumno)',
            [
                'nombre_beca' => $nombreBeca,
                'institucion' => $institucion,
                'fecha_inicio' => $fechaInicio,
                'fecha_termino' => $fechaTermino,
                'alumno' => $alumno,
            ],
            true
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function listarBecas(int $alumno): array
    {
        $consulta = conexion()->prepare(
            'SELECT b.id_beca, b.alumno, b.nom_beca, b.inst_beca, '
            . 'b.fech_in, b.fech_ter, n.beca, n.tipo_beca, '
            . 'n.estado_catalogo, i.inst '
            . 'FROM beca b '
            . 'JOIN nombre_beca n ON n.id_nom_beca = b.nom_beca '
            . 'JOIN institucion i ON i.id_inst = b.inst_beca '
            . 'WHERE b.alumno = :alumno '
            . 'ORDER BY n.tipo_beca, n.beca, b.id_beca'
        );
        $consulta->execute(['alumno' => $alumno]);

        return $consulta->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** @return array<string, mixed>|null */
    public function obtenerBeca(int $idBeca, ?int $alumno = null): ?array
    {
        $sql = 'SELECT b.id_beca, b.alumno, b.nom_beca, b.inst_beca, '
            . 'b.fech_in, b.fech_ter, n.beca, n.tipo_beca, '
            . 'n.estado_catalogo, n.propuesto_por, i.inst '
            . 'FROM beca b '
            . 'JOIN nombre_beca n ON n.id_nom_beca = b.nom_beca '
            . 'JOIN institucion i ON i.id_inst = b.inst_beca '
            . 'WHERE b.id_beca = :id_beca';

        $parametros = ['id_beca' => $idBeca];

        if ($alumno !== null) {
            $sql .= ' AND b.alumno = :alumno';
            $parametros['alumno'] = $alumno;
        }

        $consulta = conexion()->prepare($sql);
        $consulta->execute($parametros);
        $resultado = $consulta->fetch(\PDO::FETCH_ASSOC);

        return $resultado === false ? null : $resultado;
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function actualizarBeca(
        int $idBeca,
        int $alumno,
        int $nombreBeca,
        int $institucion,
        string $fechaInicio,
        string $fechaTermino
    ): array {
        return ejecutarEscritura(
            'UPDATE beca '
            . 'SET nom_beca = :nombre_beca, inst_beca = :institucion, '
            . 'fech_in = :fecha_inicio, fech_ter = :fecha_termino '
            . 'WHERE id_beca = :id_beca AND alumno = :alumno',
            [
                'nombre_beca' => $nombreBeca,
                'institucion' => $institucion,
                'fecha_inicio' => $fechaInicio,
                'fecha_termino' => $fechaTermino,
                'id_beca' => $idBeca,
                'alumno' => $alumno,
            ]
        );
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function eliminarBeca(int $idBeca, int $alumno): array
    {
        return ejecutarEscritura(
            'DELETE FROM beca '
            . 'WHERE id_beca = :id_beca AND alumno = :alumno',
            [
                'id_beca' => $idBeca,
                'alumno' => $alumno,
            ]
        );
    }

    public function usuarioEstudianteValido(int $usuario): bool
    {
        $consulta = conexion()->prepare(
            'SELECT '
            . 'EXISTS(SELECT 1 FROM estudiante e '
            . 'WHERE e.usuario = u.id_usuario) AS es_estudiante, '
            . 'EXISTS(SELECT 1 FROM profesor p '
            . 'WHERE p.usuario = u.id_usuario) AS es_profesor '
            . 'FROM usuario u '
            . 'WHERE u.id_usuario = :usuario'
        );
        $consulta->execute(['usuario' => $usuario]);
        $resultado = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($resultado === false) {
            return false;
        }

        return (int) $resultado['es_estudiante'] === 1
            && (int) $resultado['es_profesor'] === 0;
    }

    public function institucionExiste(int $institucion): bool
    {
        $consulta = conexion()->prepare(
            'SELECT 1 FROM institucion '
            . 'WHERE id_inst = :institucion LIMIT 1'
        );
        $consulta->execute(['institucion' => $institucion]);

        return $consulta->fetchColumn() !== false;
    }

    /** @return array<string, mixed>|null */
    public function obtenerNombreBeca(int $idNombreBeca): ?array
    {
        $consulta = conexion()->prepare(
            'SELECT id_nom_beca, beca, tipo_beca, '
            . 'estado_catalogo, propuesto_por '
            . 'FROM nombre_beca '
            . 'WHERE id_nom_beca = :id_nombre_beca'
        );
        $consulta->execute(['id_nombre_beca' => $idNombreBeca]);
        $resultado = $consulta->fetch(\PDO::FETCH_ASSOC);

        return $resultado === false ? null : $resultado;
    }

    /**
     * @param array<string, mixed> $catalogo
     */
    public function catalogoSeleccionable(
        array $catalogo,
        int $tipo,
        ?int $proponente
    ): bool {
        if (
            !isset(
                $catalogo['id_nom_beca'],
                $catalogo['beca'],
                $catalogo['tipo_beca'],
                $catalogo['estado_catalogo']
            )
            || trim((string) $catalogo['beca']) === ''
            || (int) $catalogo['tipo_beca'] !== $tipo
        ) {
            return false;
        }

        if ($catalogo['estado_catalogo'] === self::ESTADO_APROBADA) {
            return true;
        }

        return $catalogo['estado_catalogo'] === self::ESTADO_PENDIENTE
            && $proponente !== null
            && isset($catalogo['propuesto_por'])
            && (int) $catalogo['propuesto_por'] === $proponente;
    }

    /** @return array<int, array<string, mixed>> */
    public function listarOpcionesCatalogo(
        int $tipo,
        ?int $proponente
    ): array {
        $visibilidad = 'n.estado_catalogo = :estado_aprobada';
        $parametros = [
            'tipo' => $tipo,
            'estado_aprobada' => self::ESTADO_APROBADA,
        ];

        if ($proponente !== null) {
            $visibilidad = '('
                . $visibilidad
                . ' OR (n.estado_catalogo = :estado_pendiente '
                . 'AND n.propuesto_por = :proponente))';
            $parametros['estado_pendiente'] = self::ESTADO_PENDIENTE;
            $parametros['proponente'] = $proponente;
        }

        $consulta = conexion()->prepare(
            'SELECT n.id_nom_beca, n.beca, n.tipo_beca, '
            . 'n.estado_catalogo, n.propuesto_por '
            . 'FROM nombre_beca n '
            . 'WHERE n.tipo_beca = :tipo '
            . "AND TRIM(n.beca) <> '' "
            . 'AND ' . $visibilidad . ' '
            . 'ORDER BY n.beca, n.id_nom_beca'
        );
        $consulta->execute($parametros);

        return $consulta->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** @return array<int, array<string, mixed>> */
    public function buscarCoincidenciasCatalogo(
        string $nombre,
        int $tipo,
        ?int $excluirId = null
    ): array {
        $sql = 'SELECT id_nom_beca, beca, tipo_beca, '
            . 'estado_catalogo, propuesto_por '
            . 'FROM nombre_beca '
            . 'WHERE tipo_beca = :tipo';
        $parametros = ['tipo' => $tipo];

        if ($excluirId !== null) {
            $sql .= ' AND id_nom_beca <> :excluir_id';
            $parametros['excluir_id'] = $excluirId;
        }

        $sql .= ' ORDER BY id_nom_beca';

        $consulta = conexion()->prepare($sql);
        $consulta->execute($parametros);
        $coincidencias = [];

        foreach ($consulta->fetchAll(\PDO::FETCH_ASSOC) as $catalogo) {
            if (
                $this->nombresEquivalentes(
                    (string) $catalogo['beca'],
                    $nombre
                )
            ) {
                $coincidencias[] = $catalogo;
            }
        }

        return $coincidencias;
    }

    /**
     * @return array{
     *     filasAfectadas:int,
     *     idInsertado:string,
     *     idNombreBeca:int,
     *     catalogoCreado:bool
     * }
     */
    public function crearBecaConPropuesta(
        int $alumno,
        string $nombre,
        int $tipo,
        int $institucion,
        string $fechaInicio,
        string $fechaTermino
    ): array {
        $pdo = conexion();

        if ($pdo->inTransaction()) {
            throw new \LogicException(
                'No se admite una transacción anidada para crear la Beca.'
            );
        }

        $pdo->beginTransaction();

        try {
            $catalogo = $this->buscarCatalogoUtilizableBloqueado(
                $nombre,
                $tipo,
                $alumno
            );
            $catalogoCreado = false;

            if ($catalogo === null) {
                $resultadoCatalogo = ejecutarEscritura(
                    'INSERT INTO nombre_beca '
                    . '(beca, tipo_beca, estado_catalogo, propuesto_por) '
                    . 'VALUES (:nombre, :tipo, :estado, :proponente)',
                    [
                        'nombre' => $nombre,
                        'tipo' => $tipo,
                        'estado' => self::ESTADO_PENDIENTE,
                        'proponente' => $alumno,
                    ],
                    true
                );

                $idNombreBeca = (int) (
                    $resultadoCatalogo['idInsertado'] ?? 0
                );

                if (
                    (int) $resultadoCatalogo['filasAfectadas'] !== 1
                    || $idNombreBeca <= 0
                ) {
                    throw new \RuntimeException(
                        'No fue posible confirmar la propuesta de catálogo.'
                    );
                }

                $catalogoCreado = true;
            } else {
                $idNombreBeca = (int) $catalogo['id_nom_beca'];
            }

            $resultadoBeca = $this->crearBeca(
                $alumno,
                $idNombreBeca,
                $institucion,
                $fechaInicio,
                $fechaTermino
            );

            if (
                (int) $resultadoBeca['filasAfectadas'] !== 1
                || $resultadoBeca['idInsertado'] === null
                || (int) $resultadoBeca['idInsertado'] <= 0
            ) {
                throw new \RuntimeException(
                    'No fue posible confirmar la creación de la Beca.'
                );
            }

            $pdo->commit();

            return [
                'filasAfectadas' => (int) $resultadoBeca['filasAfectadas'],
                'idInsertado' => $resultadoBeca['idInsertado'],
                'idNombreBeca' => $idNombreBeca,
                'catalogoCreado' => $catalogoCreado,
            ];
        } catch (\Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $error;
        }
    }

    /** @return array<int, array<string, mixed>> */
    public function listarCatalogo(
        ?int $tipo,
        ?string $estado
    ): array {
        $sql = 'SELECT n.id_nom_beca, n.beca, n.tipo_beca, '
            . 'n.estado_catalogo, n.propuesto_por, '
            . "CONCAT_WS(' ', u.nombres, u.ap_pat, u.ap_mat) AS proponente, "
            . 'COUNT(b.id_beca) AS referencias '
            . 'FROM nombre_beca n '
            . 'LEFT JOIN usuario u ON u.id_usuario = n.propuesto_por '
            . 'LEFT JOIN beca b ON b.nom_beca = n.id_nom_beca '
            . 'WHERE 1 = 1';
        $parametros = [];

        if ($tipo !== null) {
            $sql .= ' AND n.tipo_beca = :tipo';
            $parametros['tipo'] = $tipo;
        }

        if ($estado !== null) {
            $sql .= ' AND n.estado_catalogo = :estado';
            $parametros['estado'] = $estado;
        }

        $sql .= ' GROUP BY n.id_nom_beca, n.beca, n.tipo_beca, '
            . 'n.estado_catalogo, n.propuesto_por, '
            . 'u.nombres, u.ap_pat, u.ap_mat '
            . 'ORDER BY n.tipo_beca, n.estado_catalogo, n.beca, '
            . 'n.id_nom_beca';

        $consulta = conexion()->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function crearCatalogoOficial(
        string $nombre,
        int $tipo
    ): array {
        return ejecutarEscritura(
            'INSERT INTO nombre_beca '
            . '(beca, tipo_beca, estado_catalogo, propuesto_por) '
            . 'VALUES (:nombre, :tipo, :estado, NULL)',
            [
                'nombre' => $nombre,
                'tipo' => $tipo,
                'estado' => self::ESTADO_APROBADA,
            ],
            true
        );
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function actualizarCatalogo(
        int $idNombreBeca,
        string $nombre,
        int $tipo
    ): array {
        return ejecutarEscritura(
            'UPDATE nombre_beca '
            . 'SET beca = :nombre, tipo_beca = :tipo '
            . 'WHERE id_nom_beca = :id_nombre_beca',
            [
                'nombre' => $nombre,
                'tipo' => $tipo,
                'id_nombre_beca' => $idNombreBeca,
            ]
        );
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function aprobarCatalogo(int $idNombreBeca): array
    {
        return ejecutarEscritura(
            'UPDATE nombre_beca '
            . 'SET estado_catalogo = :estado_aprobada '
            . 'WHERE id_nom_beca = :id_nombre_beca '
            . 'AND estado_catalogo = :estado_pendiente',
            [
                'estado_aprobada' => self::ESTADO_APROBADA,
                'id_nombre_beca' => $idNombreBeca,
                'estado_pendiente' => self::ESTADO_PENDIENTE,
            ]
        );
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function inactivarCatalogo(int $idNombreBeca): array
    {
        return ejecutarEscritura(
            'UPDATE nombre_beca '
            . 'SET estado_catalogo = :estado_inactiva '
            . 'WHERE id_nom_beca = :id_nombre_beca '
            . 'AND estado_catalogo IN '
            . '(:estado_aprobada, :estado_pendiente)',
            [
                'estado_inactiva' => self::ESTADO_INACTIVA,
                'id_nombre_beca' => $idNombreBeca,
                'estado_aprobada' => self::ESTADO_APROBADA,
                'estado_pendiente' => self::ESTADO_PENDIENTE,
            ]
        );
    }

    public function contarReferenciasCatalogo(int $idNombreBeca): int
    {
        $consulta = conexion()->prepare(
            'SELECT COUNT(*) FROM beca '
            . 'WHERE nom_beca = :id_nombre_beca'
        );
        $consulta->execute(['id_nombre_beca' => $idNombreBeca]);

        return (int) $consulta->fetchColumn();
    }

    /**
     * @return array{
     *     filasAfectadas:int,
     *     idInsertado:null,
     *     becasReasignadas:int,
     *     catalogosInactivados:int,
     *     origen:int,
     *     destino:int
     * }
     */
    public function unificarCatalogo(
        int $origen,
        int $destino
    ): array {
        if ($origen === $destino) {
            throw new \InvalidArgumentException(
                'El origen y el destino deben ser distintos.'
            );
        }

        $pdo = conexion();

        if ($pdo->inTransaction()) {
            throw new \LogicException(
                'No se admite una transacción anidada para unificar el catálogo.'
            );
        }

        $pdo->beginTransaction();

        try {
            $consulta = $pdo->prepare(
                'SELECT id_nom_beca, tipo_beca, estado_catalogo '
                . 'FROM nombre_beca '
                . 'WHERE id_nom_beca IN (:origen, :destino) '
                . 'FOR UPDATE'
            );
            $consulta->execute([
                'origen' => $origen,
                'destino' => $destino,
            ]);

            $catalogos = [];

            foreach ($consulta->fetchAll(\PDO::FETCH_ASSOC) as $catalogo) {
                $catalogos[(int) $catalogo['id_nom_beca']] = $catalogo;
            }

            if (
                !isset($catalogos[$origen])
                || !isset($catalogos[$destino])
            ) {
                throw new \UnexpectedValueException(
                    'No existe el origen o el destino de la unificación.'
                );
            }

            if (
                (int) $catalogos[$origen]['tipo_beca']
                !== (int) $catalogos[$destino]['tipo_beca']
            ) {
                throw new \DomainException(
                    'El origen y el destino deben pertenecer al mismo tipo de Beca.'
                );
            }

            if (
                $catalogos[$destino]['estado_catalogo']
                !== self::ESTADO_APROBADA
            ) {
                throw new \DomainException(
                    'El destino debe ser una entrada aprobada.'
                );
            }

            $referenciasEsperadas = $this->contarReferenciasCatalogo(
                $origen
            );

            $resultadoReasignacion = ejecutarEscritura(
                'UPDATE beca '
                . 'SET nom_beca = :destino '
                . 'WHERE nom_beca = :origen',
                [
                    'destino' => $destino,
                    'origen' => $origen,
                ]
            );

            if (
                (int) $resultadoReasignacion['filasAfectadas']
                !== $referenciasEsperadas
            ) {
                throw new \RuntimeException(
                    'La cantidad de Becas reasignadas no coincide.'
                );
            }

            if ($this->contarReferenciasCatalogo($origen) !== 0) {
                throw new \RuntimeException(
                    'Persisten referencias al catálogo de origen.'
                );
            }

            $resultadoInactivacion = ejecutarEscritura(
                'UPDATE nombre_beca '
                . 'SET estado_catalogo = :estado_inactiva '
                . 'WHERE id_nom_beca = :origen '
                . 'AND estado_catalogo <> :estado_actual',
                [
                    'estado_inactiva' => self::ESTADO_INACTIVA,
                    'origen' => $origen,
                    'estado_actual' => self::ESTADO_INACTIVA,
                ]
            );

            $filasInactivadas = (int) (
                $resultadoInactivacion['filasAfectadas']
            );

            if ($filasInactivadas > 1) {
                throw new \RuntimeException(
                    'La inactivación afectó una cantidad inesperada de filas.'
                );
            }

            $pdo->commit();
            $becasReasignadas = (int) (
                $resultadoReasignacion['filasAfectadas']
            );

            return [
                'filasAfectadas' => $becasReasignadas
                    + $filasInactivadas,
                'idInsertado' => null,
                'becasReasignadas' => $becasReasignadas,
                'catalogosInactivados' => $filasInactivadas,
                'origen' => $origen,
                'destino' => $destino,
            ];
        } catch (\Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $error;
        }
    }

    /** @return array<string, mixed>|null */
    private function buscarCatalogoUtilizableBloqueado(
        string $nombre,
        int $tipo,
        int $proponente
    ): ?array {
        $consulta = conexion()->prepare(
            'SELECT id_nom_beca, beca, tipo_beca, '
            . 'estado_catalogo, propuesto_por '
            . 'FROM nombre_beca '
            . 'WHERE tipo_beca = :tipo '
            . 'ORDER BY id_nom_beca '
            . 'FOR UPDATE'
        );
        $consulta->execute(['tipo' => $tipo]);
        $pendientePropia = null;

        foreach ($consulta->fetchAll(\PDO::FETCH_ASSOC) as $catalogo) {
            if (
                !$this->nombresEquivalentes(
                    (string) $catalogo['beca'],
                    $nombre
                )
            ) {
                continue;
            }

            if (
                $catalogo['estado_catalogo']
                === self::ESTADO_APROBADA
            ) {
                return $catalogo;
            }

            if (
                $pendientePropia === null
                && $catalogo['estado_catalogo']
                    === self::ESTADO_PENDIENTE
                && isset($catalogo['propuesto_por'])
                && (int) $catalogo['propuesto_por'] === $proponente
            ) {
                $pendientePropia = $catalogo;
            }
        }

        return $pendientePropia;
    }

    private function nombresEquivalentes(
        string $nombreExistente,
        string $nombreBuscado
    ): bool {
        $consulta = conexion()->prepare(
            'SELECT :nombre_existente = :nombre_buscado'
        );
        $consulta->execute([
            'nombre_existente' => $this->normalizarEspacios(
                $nombreExistente
            ),
            'nombre_buscado' => $this->normalizarEspacios(
                $nombreBuscado
            ),
        ]);

        return (int) $consulta->fetchColumn() === 1;
    }

    private function normalizarEspacios(string $nombre): string
    {
        $normalizado = preg_replace('/\s+/u', ' ', trim($nombre));

        return $normalizado === null ? trim($nombre) : $normalizado;
    }
}
?>