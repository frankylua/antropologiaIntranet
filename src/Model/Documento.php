<?php
declare(strict_types=1);

namespace App\Model;

use PDO;
use RuntimeException;

final class Documento
{
    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function insertar(
        ?string $nombreOriginal,
        string $mimeType,
        int $tamanio,
        string $archivo,
        string $checksumSha256
    ): array {
        $resultado = ejecutarEscritura(
            'INSERT INTO documento '
            . '(nombre_original, mime_type, tamanio, archivo, checksum_sha256) '
            . 'VALUES (:nombre_original, :mime_type, :tamanio, :archivo, :checksum_sha256)',
            [
                'nombre_original' => $nombreOriginal,
                'mime_type' => $mimeType,
                'tamanio' => $tamanio,
                'archivo' => $archivo,
                'checksum_sha256' => $checksumSha256,
            ],
            true,
            [
                'nombre_original' => $nombreOriginal === null ? PDO::PARAM_NULL : PDO::PARAM_STR,
                'mime_type' => PDO::PARAM_STR,
                'tamanio' => PDO::PARAM_INT,
                'archivo' => PDO::PARAM_LOB,
                'checksum_sha256' => PDO::PARAM_STR,
            ]
        );

        $idDocumento = isset($resultado['idInsertado'])
            && is_string($resultado['idInsertado'])
            && ctype_digit($resultado['idInsertado'])
            ? (int) $resultado['idInsertado']
            : 0;
        if ((int) $resultado['filasAfectadas'] !== 1 || $idDocumento < 1) {
            throw new RuntimeException('La inserción de Documento no produjo un resultado inequívoco.');
        }

        return $resultado;
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function actualizar(
        int $idDocumento,
        ?string $nombreOriginal,
        string $mimeType,
        int $tamanio,
        string $archivo,
        string $checksumSha256
    ): array {
        return ejecutarEscritura(
            'UPDATE documento SET nombre_original = :nombre_original, mime_type = :mime_type, '
            . 'tamanio = :tamanio, archivo = :archivo, checksum_sha256 = :checksum_sha256 '
            . 'WHERE id_documento = :id_documento',
            [
                'nombre_original' => $nombreOriginal,
                'mime_type' => $mimeType,
                'tamanio' => $tamanio,
                'archivo' => $archivo,
                'checksum_sha256' => $checksumSha256,
                'id_documento' => $idDocumento,
            ],
            false,
            [
                'nombre_original' => $nombreOriginal === null ? PDO::PARAM_NULL : PDO::PARAM_STR,
                'mime_type' => PDO::PARAM_STR,
                'tamanio' => PDO::PARAM_INT,
                'archivo' => PDO::PARAM_LOB,
                'checksum_sha256' => PDO::PARAM_STR,
                'id_documento' => PDO::PARAM_INT,
            ]
        );
    }

    /** @return array<string, mixed>|null */
    public function obtenerMetadata(int $idDocumento, bool $bloquear = false): ?array
    {
        $sql = 'SELECT id_documento, nombre_original, mime_type, tamanio, fecha_creacion, '
            . 'checksum_sha256 FROM documento WHERE id_documento = :id_documento';
        if ($bloquear) {
            $sql .= ' FOR UPDATE';
        }

        $consulta = conexion()->prepare($sql);
        $consulta->bindValue(':id_documento', $idDocumento, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        return $resultado === false ? null : $resultado;
    }

    /** @return array<string, mixed>|null */
    public function obtenerContenido(int $idDocumento): ?array
    {
        $consulta = conexion()->prepare(
            'SELECT archivo, nombre_original, mime_type, tamanio, checksum_sha256 '
            . 'FROM documento WHERE id_documento = :id_documento'
        );
        $consulta->bindValue(':id_documento', $idDocumento, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        return $resultado === false ? null : $resultado;
    }

    /** @return array{filasAfectadas:int,idInsertado:?string} */
    public function eliminar(int $idDocumento): array
    {
        return ejecutarEscritura(
            'DELETE FROM documento WHERE id_documento = :id_documento',
            ['id_documento' => $idDocumento],
            false,
            ['id_documento' => PDO::PARAM_INT]
        );
    }
}
