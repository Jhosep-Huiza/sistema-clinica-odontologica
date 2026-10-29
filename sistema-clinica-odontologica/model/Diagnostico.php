<?php
require_once __DIR__ . '/BaseModel.php';

class Diagnostico extends BaseModel
{
    protected string $tabla = 'diagnosticos';
    protected array $columnas = ['cita_id', 'descripcion', 'observaciones', 'fecha'];
    protected string $listado = "SELECT t.*, CONCAT(p.apellidos, ', ', p.nombres) AS paciente,
                                        c.fecha_hora AS cita_fecha
                                 FROM diagnosticos t
                                 JOIN citas c ON c.id = t.cita_id
                                 JOIN pacientes p ON p.id = c.paciente_id";
}
