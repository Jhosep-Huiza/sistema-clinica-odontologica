<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/Servicio.php';

class ServicioController extends CrudController
{
    public function __construct()
    {
        $this->modelo = new Servicio(db());
        $this->titulo = 'Servicios';
        $this->archivo = 'servicios.php';
        $this->campos = [
            'nombre' => 'text',
            'descripcion' => 'textarea',
            'precio' => 'number',
            'duracion_min' => 'entero',
        ];
        $this->obligatorios = ['nombre', 'precio', 'duracion_min'];
        $this->columnasLista = [
            'nombre' => ['Servicio', 'text'],
            'descripcion' => ['Descripción', 'text'],
            'precio' => ['Precio', 'number'],
            'duracion_min' => ['Duración', 'minutos'],
        ];
    }
}
