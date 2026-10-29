<?php
require_once __DIR__ . '/../controller/AuthController.php';

$error = '';
$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        validarCsrf();
        $accion = $_POST['accion'] ?? '';

        if ($accion === 'registro') {
            $auth->registrar(
                trim($_POST['nombre'] ?? ''),
                trim($_POST['email'] ?? ''),
                $_POST['password'] ?? ''
            );
            $error = 'Usuario creado. Ahora puede iniciar sesión.';
        } elseif ($accion === 'login') {
            if ($auth->ingresar(trim($_POST['email'] ?? ''), $_POST['password'] ?? '')) {
                redirigir('home.php');
            }
            $error = 'Credenciales incorrectas.';
        }
    } catch (PDOException $ex) {
        error_log($ex->getMessage());
        $error = 'Ese correo ya está registrado o no se pudo completar el registro.';
    } catch (Throwable $ex) {
        $error = $ex->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso | Sonrisa Perfecta</title>
    <link rel="stylesheet" href="../assets/css/clinica.css">
</head>
<body class="auth">
    <div class="auth-box">
        <div class="brand">
            <img class="auth-logo" src="../imagenes/logo.png" alt="Logo de Sonrisa Perfecta">
            Sonrisa Perfecta
            <small>Gestión odontológica</small>
        </div>
        <h2>Bienvenido</h2>

        <?php if ($error !== ''): ?>
            <p role="status"><?= e($error) ?></p>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

            <label for="nombre">Nombre (solo para registrarse)
                <input id="nombre" name="nombre" placeholder="Nombre completo">
            </label>

            <label for="email">Correo electrónico
                <input id="email" name="email" type="email" autocomplete="email" required>
            </label>

            <label for="password">Contraseña
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </label>

            <div class="actions">
                <button type="submit" name="accion" value="login">Ingresar</button>
                <button type="submit" name="accion" value="registro" class="secondary">Registrarme</button>
                <button type="reset" class="secondary">Cancelar</button>
            </div>
        </form>
        <p class="auth-note">Para registrarse, escriba su nombre, correo y una contraseña de al menos 10 caracteres.</p>
    </div>
</body>
</html>
