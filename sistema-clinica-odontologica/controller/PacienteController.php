<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/Paciente.php';

class PacienteController extends CrudController
{
    public function __construct()
    {
        $this->modelo = new Paciente(db());
        $this->titulo = 'Pacientes';
        $this->archivo = 'pacientes.php';
        $this->campos = [
            'nombres' => 'text',
            'apellidos' => 'text',
            'dni' => 'dni',
            'fecha_nacimiento' => 'date',
            'sexo' => 'sexo',
            'telefono' => 'text',
            'email' => 'email',
            'direccion' => 'text',
        ];
        $this->obligatorios = ['nombres', 'apellidos', 'dni'];
        $this->columnasLista = [
            'dni' => ['DNI', 'text'],
            'nombre_completo' => ['Paciente', 'text'],
            'fecha_nacimiento' => ['Nacimiento', 'date'],
            'sexo' => ['Sexo', 'sexo'],
            'telefono' => ['Teléfono', 'text'],
            'email' => ['Correo', 'text'],
        ];
    }
}
