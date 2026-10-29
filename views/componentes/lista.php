<?php
// Tabla genérica de los módulos. Espera: $rows, $columnasLista, $archivo.
?>
<div class="table-scroll">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <?php foreach ($columnasLista as [$etiquetaColumna]): ?>
                    <th><?= e($etiquetaColumna) ?></th>
                <?php endforeach; ?>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$rows): ?>
                <tr><td colspan="<?= count($columnasLista) + 2 ?>">No hay registros todavía.</td></tr>
            <?php endif; ?>

            <?php foreach ($rows as $fila): ?>
                <tr>
                    <td><?= e($fila['id']) ?></td>
                    <?php foreach ($columnasLista as $columna => [, $tipo]): ?>
                        <td><?= e(formatearValor($tipo, $fila[$columna] ?? null)) ?></td>
                    <?php endforeach; ?>
                    <td>
                        <a href="<?= e($archivo) ?>?editar=<?= e($fila['id']) ?>">Editar</a>
                        <form method="post" action="<?= e($archivo) ?>" onsubmit="return confirm('¿Eliminar registro?')">
                            <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                            <input type="hidden" name="id" value="<?= e($fila['id']) ?>">
                            <button type="submit" class="danger" name="accion" value="eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
