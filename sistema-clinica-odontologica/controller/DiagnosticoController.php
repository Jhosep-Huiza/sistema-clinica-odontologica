<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/Diagnostico.php';

class DiagnosticoController extends CrudController
{
    public function __construct()
    {
        $this->modelo = new Diagnostico(db());
        $this->titulo = 'Diagnósticos';
        $this->archivo = 'diagnosticos.php';
        $this->campos = [
            'cita_id' => 'citas',
            'descripcion' => 'textarea',
            'observaciones' => 'textarea',
            'fecha' => 'date',
        ];
        $this->obligatorios = ['descripcion', 'fecha'];
        $this->columnasLista = [
            'paciente' => ['Paciente', 'text'],
            'cita_fecha' => ['Cita', 'datetime-local'],
            'descripcion' => ['Diagnóstico', 'text'],
            'fecha' => ['Fecha', 'date'],
        ];
    }
}
