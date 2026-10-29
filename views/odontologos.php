<?php
require_once __DIR__ . '/../controller/OdontologoController.php';

exigirSesion();
$controller = new OdontologoController();
$error = $controller->procesar();

$edit = isset($_GET['editar'])
    ? $controller->modelo->obtener((int) $_GET['editar'])
    : false;

// Si la validación falla, se conserva lo que el usuario escribió.
if ($error !== '' && ($_POST['accion'] ?? '') !== 'eliminar') {
    $edit = $_POST;
}

$rows = $controller->modelo->listar();
$campos = $controller->campos;
$obligatorios = $controller->obligatorios;
$columnasLista = $controller->columnasLista;
$titulo = $controller->titulo;
$archivo = $controller->archivo;

require __DIR__ . '/odontologos/index.php';
