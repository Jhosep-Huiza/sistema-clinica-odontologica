<?php
require_once __DIR__ . '/BaseModel.php';

class Paciente extends BaseModel
{
    protected string $tabla = 'pacientes';
    protected array $columnas = ['nombres', 'apellidos', 'dni', 'fecha_nacimiento', 'sexo', 'telefono', 'email', 'direccion'];
    protected string $listado = "SELECT t.*, CONCAT(t.apellidos, ', ', t.nombres) AS nombre_completo
                                 FROM pacientes t";
}
