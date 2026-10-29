<?php
require_once __DIR__ . '/BaseModel.php';

class Pago extends BaseModel
{
    protected string $tabla = 'pagos';
    protected array $columnas = ['cita_id', 'monto', 'metodo', 'estado', 'referencia', 'fecha_pago'];
    protected string $listado = "SELECT t.*, CONCAT(p.apellidos, ', ', p.nombres) AS paciente,
                                        c.fecha_hora AS cita_fecha
                                 FROM pagos t
                                 JOIN citas c ON c.id = t.cita_id
                                 JOIN pacientes p ON p.id = c.paciente_id";

    public function guardar(array $datos, int $id = 0): void
    {
        if ((float) ($datos['monto'] ?? 0) <= 0) {
            throw new RuntimeException('El monto debe ser mayor que cero.');
        }

        // Si se marca como pagado y no tiene fecha, se registra el momento actual.
        if (($datos['estado'] ?? '') === 'pagado' && empty($datos['fecha_pago'])) {
            $datos['fecha_pago'] = date('Y-m-d H:i:s');
        }

        parent::guardar($datos, $id);
    }
}
