<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/Cita.php';

class CitaController extends CrudController
{
    public function __construct()
    {
        $this->modelo = new Cita(db());
        $this->titulo = 'Citas odontológicas';
        $this->archivo = 'citas.php';
        $this->campos = [
            'paciente_id' => 'pacientes',
            'odontologo_id' => 'odontologos',
            'servicio_id' => 'servicios',
            'fecha_hora' => 'datetime-local',
            'estado' => 'estado_cita',
            'motivo' => 'textarea',
        ];
        $this->obligatorios = ['fecha_hora', 'estado'];
        $this->columnasLista = [
            'fecha_hora' => ['Fecha y hora', 'datetime-local'],
            'paciente' => ['Paciente', 'text'],
            'odontologo' => ['Odontólogo', 'text'],
            'servicio' => ['Servicio', 'text'],
            'estado' => ['Estado', 'estado_cita'],
        ];
    }
}
