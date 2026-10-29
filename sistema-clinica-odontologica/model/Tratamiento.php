<?php
require_once __DIR__ . '/BaseModel.php';

class Tratamiento extends BaseModel
{
    protected string $tabla = 'tratamientos';
    protected array $columnas = ['diagnostico_id', 'pieza_dental', 'procedimiento', 'medicamento', 'indicaciones', 'fecha_inicio', 'fecha_fin', 'costo'];
    protected string $listado = "SELECT t.*, CONCAT(p.apellidos, ', ', p.nombres) AS paciente
                                 FROM tratamientos t
                                 JOIN diagnosticos d ON d.id = t.diagnostico_id
                                 JOIN citas c ON c.id = d.cita_id
                                 JOIN pacientes p ON p.id = c.paciente_id";

    public function guardar(array $datos, int $id = 0): void
    {
        if (!empty($datos['fecha_inicio']) && !empty($datos['fecha_fin'])
            && $datos['fecha_fin'] < $datos['fecha_inicio']) {
            throw new RuntimeException('La fecha de fin no puede ser anterior a la fecha de inicio.');
        }

        parent::guardar($datos, $id);
    }
}
