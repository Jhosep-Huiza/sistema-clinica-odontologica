<?php
require_once __DIR__ . '/../controller/BaseController.php';

/** Inicio del diseño: encabezado de dos niveles, barra horizontal, contenido principal. */
function layoutStart(string $titulo): void
{
    $modulos = modulos();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($titulo) ?> | Sonrisa Perfecta</title>
        <link rel="stylesheet" href="../assets/css/clinica.css">
    </head>
    <body>
        <header>
            <div class="header-top">
                <strong>🦷 Sonrisa Perfecta · Clínica odontológica</strong>
                <span>
                    <?= e($_SESSION['nombre'] ?? '') ?> ·
                    <a href="logout.php">Cerrar sesión</a>
                </span>
            </div>
            <nav class="nav" aria-label="Menú principal">
                <a href="home.php">Inicio</a>
                <?php foreach ($modulos as $modulo): ?>
                    <a href="<?= e($modulo['url']) ?>"><?= e($modulo['nombre']) ?></a>
                <?php endforeach; ?>
            </nav>
        </header>
        <div class="shell">
            <main>
                <h1><?= e($titulo) ?></h1>
                <?php if (isset($_SESSION['flash'])): ?>
                    <p class="notice"><?= e($_SESSION['flash']) ?></p>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>
    <?php
}

/** Cierre del diseño: menú vertical a la derecha y pie de página. */
function layoutEnd(): void
{
    $modulos = modulos();
    ?>
            </main>
            <aside aria-label="Menú lateral">
                <h3>Menú rápido</h3>
                <a href="home.php">🏠 Inicio</a>
                <?php foreach ($modulos as $modulo): ?>
                    <a href="<?= e($modulo['url']) ?>">▸ <?= e($modulo['nombre']) ?></a>
                <?php endforeach; ?>
            </aside>
        </div>
        <footer>© <?= date('Y') ?> Clínica Odontológica Sonrisa Perfecta · Sistema de gestión</footer>
    </body>
    </html>
    <?php
}
