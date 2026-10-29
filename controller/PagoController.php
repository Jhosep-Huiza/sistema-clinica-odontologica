<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/Pago.php';

class PagoController extends CrudController
{
    public function __construct()
    {
        $this->modelo = new Pago(db());
        $this->titulo = 'Pagos';
        $this->archivo = 'pagos.php';
        $this->campos = [
            'cita_id' => 'citas',
            'monto' => 'number',
            'metodo' => 'metodo_pago',
            'estado' => 'estado_pago',
            'referencia' => 'text',
            'fecha_pago' => 'datetime-local',
        ];
        $this->obligatorios = ['monto', 'metodo', 'estado'];
        $this->columnasLista = [
            'paciente' => ['Paciente', 'text'],
            'cita_fecha' => ['Cita', 'datetime-local'],
            'monto' => ['Monto', 'number'],
            'metodo' => ['Método', 'metodo_pago'],
            'estado' => ['Estado', 'estado_pago'],
            'referencia' => ['Referencia', 'text'],
            'fecha_pago' => ['Fecha de pago', 'datetime-local'],
        ];
    }
}
