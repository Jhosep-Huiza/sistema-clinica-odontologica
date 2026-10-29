<?php
// Formulario genérico de los módulos. Espera: $campos, $obligatorios, $edit y $archivo.
?>
<form method="post" action="<?= e($archivo) ?>" class="form-grid">
    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
    <input type="hidden" name="id" value="<?= e($edit['id'] ?? 0) ?>">

    <?php foreach ($campos as $campo => $tipo): ?>
        <?php
        $requerido = in_array($campo, $obligatorios, true) || esReferencia($tipo);
        $valor = $edit[$campo] ?? '';
        $lista = opciones($tipo);
        ?>
        <label>
            <?= e(etiqueta($campo)) ?><?= $requerido ? ' *' : '' ?>

            <?php if (esReferencia($tipo)): ?>
                <select name="<?= e($campo) ?>" <?= $requerido ? 'required' : '' ?>>
                    <option value="">Seleccione...</option>
                    <?php foreach (opcionesReferencia($tipo) as $opcion): ?>
                        <option value="<?= e($opcion['id']) ?>" <?= (string) $valor === (string) $opcion['id'] ? 'selected' : '' ?>>
                            <?= e($opcion['etiqueta']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

            <?php elseif ($lista): ?>
                <select name="<?= e($campo) ?>">
                    <?php foreach ($lista as $clave => $texto): ?>
                        <option value="<?= e($clave) ?>" <?= (string) $valor === (string) $clave ? 'selected' : '' ?>>
                            <?= e($texto) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

            <?php elseif ($tipo === 'textarea'): ?>
                <textarea name="<?= e($campo) ?>" <?= $requerido ? 'required' : '' ?>><?= e($valor) ?></textarea>

            <?php else: ?>
                <?php
                if ($tipo === 'datetime-local' && $valor) {
                    $valor = str_replace(' ', 'T', substr((string) $valor, 0, 16));
                }
                $tipoInput = match ($tipo) {
                    'email' => 'email',
                    'date' => 'date',
                    'datetime-local' => 'datetime-local',
                    'number', 'entero' => 'number',
                    default => 'text',
                };
                $atributos = match ($tipo) {
                    'number' => 'step="0.01" min="0"',
                    'entero' => 'step="1" min="1"',
                    'dni' => 'pattern="[0-9]{8}" maxlength="8" inputmode="numeric" title="Ingrese 8 dígitos"',
                    default => '',
                };
                ?>
                <input
                    name="<?= e($campo) ?>"
                    type="<?= e($tipoInput) ?>"
                    value="<?= e($valor) ?>"
                    <?= $atributos ?>
                    <?= $requerido ? 'required' : '' ?>
                >
            <?php endif; ?>
        </label>
    <?php endforeach; ?>

    <div class="actions">
        <button type="submit" name="accion" value="guardar">Guardar</button>
        <a class="btn secondary" href="<?= e($archivo) ?>">Cancelar</a>
        <button type="reset" class="secondary">Limpiar</button>
    </div>
</form>
