<?php
require __DIR__ . '/../layout.php';
layoutStart($titulo);
?>

<?php if ($error !== ''): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<section class="panel">
    <h2><?= !empty($edit['id']) ? 'Editar' : 'Nuevo' ?> registro</h2>
    <?php require __DIR__ . '/form.php'; ?>
</section>

<section class="panel">
    <h2>Registros (<?= count($rows) ?>)</h2>
    <?php require __DIR__ . '/lista.php'; ?>
</section>

<?php layoutEnd(); ?>
