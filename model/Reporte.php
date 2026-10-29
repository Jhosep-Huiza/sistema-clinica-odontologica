<?php
// Consultas de reportes. Son solo lectura y usan parámetros preparados para las fechas.
class Reporte
{
    public function __construct(private PDO $db)
    {
    }

    /** Citas atendidas dentro del periodo (ambas fechas incluidas). */
    public function atenciones(string $desde, string $hasta): array
    {
        return $this->consultar(
            "SELECT c.fecha_hora, CONCAT(p.apellidos, ', ', p.nombres) AS paciente,
                    o.nombre AS odontologo, s.nombre AS servicio, s.precio
             FROM citas c
             JOIN pacientes p ON p.id = c.paciente_id
             JOIN odontologos o ON o.id = c.odontologo_id
             JOIN servicios s ON s.id = c.servicio_id
             WHERE c.estado = 'atendida'
               AND c.fecha_hora >= :desde
               AND c.fecha_hora < DATE_ADD(:hasta, INTERVAL 1 DAY)
             ORDER BY c.fecha_hora",
            $desde,
            $hasta
        );
    }

    /** Ingresos de pagos marcados como pagados, agrupados por método. */
    public function ingresosPorMetodo(string $desde, string $hasta): array
    {
        return $this->consultar(
            "SELECT p.metodo, COUNT(*) AS cantidad, SUM(p.monto) AS total
             FROM pagos p
             WHERE p.estado = 'pagado'
               AND p.fecha_pago >= :desde
               AND p.fecha_pago < DATE_ADD(:hasta, INTERVAL 1 DAY)
             GROUP BY p.metodo
             ORDER BY total DESC",
            $desde,
            $hasta
        );
    }

    /** Citas pendientes o confirmadas dentro del periodo. */
    public function citasPendientes(string $desde, string $hasta): array
    {
        return $this->consultar(
            "SELECT c.fecha_hora, CONCAT(p.apellidos, ', ', p.nombres) AS paciente,
                    o.nombre AS odontologo, s.nombre AS servicio, c.estado
             FROM citas c
             JOIN pacientes p ON p.id = c.paciente_id
             JOIN odontologos o ON o.id = c.odontologo_id
             JOIN servicios s ON s.id = c.servicio_id
             WHERE c.estado IN ('pendiente', 'confirmada')
               AND c.fecha_hora >= :desde
               AND c.fecha_hora < DATE_ADD(:hasta, INTERVAL 1 DAY)
             ORDER BY c.fecha_hora",
            $desde,
            $hasta
        );
    }

    private function consultar(string $sql, string $desde, string $hasta): array
    {
        $consulta = $this->db->prepare($sql);
        $consulta->execute(['desde' => $desde, 'hasta' => $hasta]);
        return $consulta->fetchAll();
    }
}
