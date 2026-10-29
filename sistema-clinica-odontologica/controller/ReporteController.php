<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/Reporte.php';

/** Genera los reportes del periodo elegido (no es un CRUD). */
class ReporteController
{
    public Reporte $modelo;
    public string $titulo = 'Reportes';
    public string $desde;
    public string $hasta;

    public function __construct()
    {
        $this->modelo = new Reporte(db());

        $desde = $_GET['desde'] ?? null;
        $hasta = $_GET['hasta'] ?? null;

        // Por defecto: desde el primer día del mes hasta hoy.
        $this->desde = (is_string($desde) ? fechaValida($desde) : null) ?? date('Y-m-01');
        $this->hasta = (is_string($hasta) ? fechaValida($hasta) : null) ?? date('Y-m-d');

        if ($this->desde > $this->hasta) {
            [$this->desde, $this->hasta] = [$this->hasta, $this->desde];
        }
    }

    public function datos(): array
    {
        return [
            'atenciones' => $this->modelo->atenciones($this->desde, $this->hasta),
            'ingresos' => $this->modelo->ingresosPorMetodo($this->desde, $this->hasta),
            'pendientes' => $this->modelo->citasPendientes($this->desde, $this->hasta),
        ];
    }
}
