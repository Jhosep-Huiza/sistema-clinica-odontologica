<?php
require_once __DIR__ . '/BaseModel.php';

class Cita extends BaseModel
{
    protected string $tabla = 'citas';
    protected array $columnas = ['paciente_id', 'odontologo_id', 'servicio_id', 'fecha_hora', 'estado', 'motivo'];
    protected string $listado = "SELECT t.*, CONCAT(p.apellidos, ', ', p.nombres) AS paciente,
                                        o.nombre AS odontologo, s.nombre AS servicio
                                 FROM citas t
                                 JOIN pacientes p ON p.id = t.paciente_id
                                 JOIN odontologos o ON o.id = t.odontologo_id
                                 JOIN servicios s ON s.id = t.servicio_id";

    /** Regla de la clínica: un odontólogo no puede tener dos citas que se crucen en el tiempo. */
    public function guardar(array $datos, int $id = 0): void
    {
        if (($datos['estado'] ?? '') !== 'cancelada' && $this->hayCruce($datos, $id)) {
            throw new RuntimeException(
                'El odontólogo ya tiene una cita que se cruza con ese horario. Elija otra hora u otro odontólogo.'
            );
        }

        parent::guardar($datos, $id);
    }

    private function hayCruce(array $datos, int $excluirId): bool
    {
        // Intervalo nuevo: [inicio, inicio + duración del servicio nuevo).
        // Hay cruce si una cita existente empieza antes de que termine la nueva
        // y termina después de que empiece la nueva.
        $consulta = $this->db->prepare(
            "SELECT COUNT(*)
             FROM citas c
             JOIN servicios existente ON existente.id = c.servicio_id
             JOIN servicios nuevo ON nuevo.id = :servicio
             WHERE c.odontologo_id = :odontologo
               AND c.estado <> 'cancelada'
               AND c.id <> :excluir
               AND c.fecha_hora < DATE_ADD(:inicio_a, INTERVAL COALESCE(nuevo.duracion_min, 30) MINUTE)
               AND DATE_ADD(c.fecha_hora, INTERVAL COALESCE(existente.duracion_min, 30) MINUTE) > :inicio_b"
        );

        $consulta->execute([
            'servicio' => (int) $datos['servicio_id'],
            'odontologo' => (int) $datos['odontologo_id'],
            'excluir' => $excluirId,
            'inicio_a' => $datos['fecha_hora'],
            'inicio_b' => $datos['fecha_hora'],
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }
}
