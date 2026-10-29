<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/Tratamiento.php';

class TratamientoController extends CrudController
{
    public function __construct()
    {
        $this->modelo = new Tratamiento(db());
        $this->titulo = 'Tratamientos';
        $this->archivo = 'tratamientos.php';
        $this->campos = [
            'diagnostico_id' => 'diagnosticos',
            'pieza_dental' => 'text',
            'procedimiento' => 'text',
            'medicamento' => 'text',
            'indicaciones' => 'textarea',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'costo' => 'number',
        ];
        $this->obligatorios = ['pieza_dental', 'procedimiento'];
        $this->columnasLista = [
            'paciente' => ['Paciente', 'text'],
            'pieza_dental' => ['Pieza dental', 'text'],
            'procedimiento' => ['Procedimiento', 'text'],
            'fecha_inicio' => ['Inicio', 'date'],
            'costo' => ['Costo', 'number'],
        ];
    }
}
