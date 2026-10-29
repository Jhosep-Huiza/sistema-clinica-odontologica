<?php
// Configuración de MySQL: único archivo de configuración.
// Se puede sobrescribir con variables de entorno: DB_HOST, DB_NAME, DB_USER, DB_PASS.
// Valores por defecto pensados para XAMPP (usuario root, sin contraseña).

class Database
{
    private string $host;
    private string $name;
    private string $user;
    private string $password;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->name = getenv('DB_NAME') ?: 'sonrisa_perfecta_db';
        $this->user = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
    }

    public function conectar(): PDO
    {
        return new PDO(
            "mysql:host={$this->host};dbname={$this->name};charset=utf8mb4",
            $this->user,
            $this->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
}
