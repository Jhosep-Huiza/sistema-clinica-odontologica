<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/Usuario.php';

class AuthController
{
    private Usuario $usuarios;

    public function __construct()
    {
        $this->usuarios = new Usuario(db());
    }

    public function ingresar(string $email, string $password): bool
    {
        $usuario = $this->usuarios->porCorreo($email);

        if (!$usuario || !$usuario['activo'] || !password_verify($password, $usuario['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['uid'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];
        return true;
    }

    public function registrar(string $nombre, string $email, string $password): void
    {
        if (strlen($password) < 10) {
            throw new RuntimeException('La contraseña debe tener al menos 10 caracteres.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Correo electrónico inválido.');
        }
        if ($nombre === '') {
            throw new RuntimeException('Indique su nombre.');
        }

        $this->usuarios->crear($nombre, $email, $password);
    }
}
