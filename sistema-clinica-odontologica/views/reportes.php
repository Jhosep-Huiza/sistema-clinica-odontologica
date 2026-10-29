<?php
require_once __DIR__ . '/../controller/ReporteController.php';

exigirSesion();
$controller = new ReporteController();
$titulo = $controller->titulo;
$desde = $controller->desde;
$hasta = $controller->hasta;
$datos = $controller->datos();

require __DIR__ . '/reportes/index.php';
