<?php
require_once __DIR__ . '/../controller/BaseController.php';
exigirSesion();
require_once __DIR__ . '/layout.php';

layoutStart('Panel de control');
?>
<p class="intro">Bienvenido al sistema de la clínica. Seleccione un módulo para comenzar.</p>

<div class="cards">
    <?php foreach (modulos() as $clave => $modulo): ?>
        <?php $cantidad = contarRegistros($clave); ?>
        <a class="card" href="<?= e($modulo['url']) ?>">
            <img loading="lazy" alt="<?= e($modulo['nombre']) ?>" src="../imagenes/modulos/<?= e($modulo['imagen']) ?>">
            <div>
                <h2><?= e($modulo['nombre']) ?></h2>
                <p><?= e($modulo['descripcion']) ?></p>
                <span>
                    <?= $cantidad !== null ? e($cantidad) . ' registro(s) · ' : '' ?>Abrir módulo →
                </span>
            </div>
        </a>
    <?php endforeach; ?>
</div>
<?php layoutEnd(); ?>
