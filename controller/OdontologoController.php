<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/Odontologo.php';

class OdontologoController extends CrudController
{
    public function __construct()
    {
        $this->modelo = new Odontologo(db());
        $this->titulo = 'Odontólogos';
        $this->archivo = 'odontologos.php';
        $this->campos = [
            'nombre' => 'text',
            'colegiatura' => 'text',
            'especialidad' => 'especialidad',
            'telefono' => 'text',
            'email' => 'email',
        ];
        $this->obligatorios = ['nombre', 'especialidad'];
        $this->columnasLista = [
            'nombre' => ['Nombre', 'text'],
            'colegiatura' => ['Colegiatura (COP)', 'text'],
            'especialidad' => ['Especialidad', 'especialidad'],
            'telefono' => ['Teléfono', 'text'],
            'email' => ['Correo', 'text'],
        ];
    }
}
