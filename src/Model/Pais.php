<?php
declare(strict_types=1);
namespace App\Model;

class Pais
{
    public function mostrarConsultaOrdenada()
    {
        $sql = "SELECT id_pais, pais
                FROM pais
                ORDER BY
                    CASE WHEN id_pais IN (13, 29, 33, 46, 52, 66, 172, 173, 229, 232)
                        THEN 0 ELSE 1
                    END,
                    pais ASC,
                    id_pais ASC";

        return ejecutarConsultaResultados($sql);
    }
}
