<?php
require __DIR__ . '/../layout.php';
layoutStart($titulo);

$totalIngresos = (float) array_sum(array_column($datos['ingresos'], 'total'));
$totalPagos = (int) array_sum(array_column($datos['ingresos'], 'cantidad'));
?>

<section class="panel no-print">
    <h2>Periodo del reporte</h2>
    <form method="get" class="filtro">
        <label>Desde
            <input type="date" name="desde" value="<?= e($desde) ?>" required>
        </label>
        <label>Hasta
            <input type="date" name="hasta" value="<?= e($hasta) ?>" required>
        </label>
        <div class="actions">
            <button type="submit">Generar reporte</button>
            <button type="button" class="secondary" onclick="window.print()">Imprimir</button>
        </div>
    </form>
</section>

<p class="intro">
    Periodo del <strong><?= e(date('d/m/Y', strtotime($desde))) ?></strong>
    al <strong><?= e(date('d/m/Y', strtotime($hasta))) ?></strong>
</p>

<div class="kpis">
    <div class="kpi">
        <span>Atenciones realizadas</span>
        <strong><?= count($datos['atenciones']) ?></strong>
    </div>
    <div class="kpi">
        <span>Ingresos obtenidos</span>
        <strong>S/ <?= number_format($totalIngresos, 2) ?></strong>
        <small><?= $totalPagos ?> pago(s) registrados como pagados</small>
    </div>
    <div class="kpi">
        <span>Citas pendientes</span>
        <strong><?= count($datos['pendientes']) ?></strong>
    </div>
</div>

<section class="panel">
    <h2>1. Atenciones realizadas</h2>
    <div class="table-scroll">
        <table>
            <thead>
                <tr><th>Fecha y hora</th><th>Paciente</th><th>Odontólogo</th><th>Servicio</th><th>Precio</th></tr>
            </thead>
            <tbody>
                <?php if (!$datos['atenciones']): ?>
                    <tr><td colspan="5">Sin atenciones en el periodo.</td></tr>
                <?php endif; ?>
                <?php foreach ($datos['atenciones'] as $fila): ?>
                    <tr>
                        <td><?= e(formatearValor('datetime-local', $fila['fecha_hora'])) ?></td>
                        <td><?= e($fila['paciente']) ?></td>
                        <td><?= e($fila['odontologo']) ?></td>
                        <td><?= e($fila['servicio']) ?></td>
                        <td><?= e(formatearValor('number', $fila['precio'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <h2>2. Ingresos obtenidos por método de pago</h2>
    <div class="table-scroll">
        <table>
            <thead>
                <tr><th>Método</th><th>Cantidad de pagos</th><th>Total</th></tr>
            </thead>
            <tbody>
                <?php if (!$datos['ingresos']): ?>
                    <tr><td colspan="3">Sin pagos registrados en el periodo.</td></tr>
                <?php endif; ?>
                <?php foreach ($datos['ingresos'] as $fila): ?>
                    <tr>
                        <td><?= e(formatearValor('metodo_pago', $fila['metodo'])) ?></td>
                        <td><?= e($fila['cantidad']) ?></td>
                        <td><?= e(formatearValor('number', $fila['total'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($datos['ingresos']): ?>
                    <tr>
                        <th>Total</th>
                        <th><?= $totalPagos ?></th>
                        <th>S/ <?= number_format($totalIngresos, 2) ?></th>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <h2>3. Citas pendientes o confirmadas</h2>
    <div class="table-scroll">
        <table>
            <thead>
                <tr><th>Fecha y hora</th><th>Paciente</th><th>Odontólogo</th><th>Servicio</th><th>Estado</th></tr>
            </thead>
            <tbody>
                <?php if (!$datos['pendientes']): ?>
                    <tr><td colspan="5">Sin citas pendientes en el periodo.</td></tr>
                <?php endif; ?>
                <?php foreach ($datos['pendientes'] as $fila): ?>
                    <tr>
                        <td><?= e(formatearValor('datetime-local', $fila['fecha_hora'])) ?></td>
                        <td><?= e($fila['paciente']) ?></td>
                        <td><?= e($fila['odontologo']) ?></td>
                        <td><?= e($fila['servicio']) ?></td>
                        <td><?= e(formatearValor('estado_cita', $fila['estado'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php layoutEnd(); ?>
