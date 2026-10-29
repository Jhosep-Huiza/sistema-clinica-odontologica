SET NAMES utf8mb4;
-- Sistema de gestión para la Clínica Odontológica "Sonrisa Perfecta"
-- Motor: MySQL 8 / MariaDB (InnoDB). Ejecutar completo en MySQL Workbench, phpMyAdmin o consola.

CREATE DATABASE IF NOT EXISTS sonrisa_perfecta_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sonrisa_perfecta_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'odontologo', 'recepcion') NOT NULL DEFAULT 'recepcion',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS pacientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(120) NOT NULL,
    apellidos VARCHAR(120) NOT NULL,
    dni CHAR(8) NOT NULL UNIQUE,
    fecha_nacimiento DATE,
    sexo ENUM('masculino', 'femenino', 'otro'),
    telefono VARCHAR(25),
    email VARCHAR(150),
    direccion VARCHAR(255)
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS odontologos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    colegiatura VARCHAR(50) UNIQUE,
    especialidad VARCHAR(120) NOT NULL,
    telefono VARCHAR(25),
    email VARCHAR(150)
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS servicios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL DEFAULT 0,
    duracion_min INT UNSIGNED NOT NULL DEFAULT 30
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS citas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT UNSIGNED NOT NULL,
    odontologo_id INT UNSIGNED NOT NULL,
    servicio_id INT UNSIGNED NOT NULL,
    fecha_hora DATETIME NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'atendida', 'cancelada') NOT NULL DEFAULT 'pendiente',
    motivo TEXT,
    FOREIGN KEY (paciente_id) REFERENCES pacientes (id),
    FOREIGN KEY (odontologo_id) REFERENCES odontologos (id),
    FOREIGN KEY (servicio_id) REFERENCES servicios (id),
    INDEX (fecha_hora)
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS diagnosticos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cita_id INT UNSIGNED NOT NULL,
    descripcion TEXT NOT NULL,
    observaciones TEXT,
    fecha DATE NOT NULL,
    FOREIGN KEY (cita_id) REFERENCES citas (id)
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS tratamientos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    diagnostico_id INT UNSIGNED NOT NULL,
    pieza_dental VARCHAR(30) NOT NULL,
    procedimiento VARCHAR(200) NOT NULL,
    medicamento VARCHAR(200),
    indicaciones TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    costo DECIMAL(10, 2),
    FOREIGN KEY (diagnostico_id) REFERENCES diagnosticos (id)
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS pagos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cita_id INT UNSIGNED NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    metodo ENUM('efectivo', 'tarjeta_credito', 'tarjeta_debito', 'yape') NOT NULL,
    estado ENUM('pendiente', 'pagado', 'anulado') NOT NULL DEFAULT 'pendiente',
    referencia VARCHAR(120),
    fecha_pago DATETIME,
    FOREIGN KEY (cita_id) REFERENCES citas (id)
) ENGINE=INNODB;

-- Catálogo inicial de servicios (valores de ejemplo, en soles). Se insertan solo si no existen.
INSERT INTO servicios (nombre, descripcion, precio, duracion_min)
SELECT 'Consulta de evaluación', 'Revisión general y diagnóstico inicial', 30, 30 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Consulta de evaluación');

INSERT INTO servicios (nombre, descripcion, precio, duracion_min)
SELECT 'Limpieza dental', 'Eliminación de sarro y placa bacteriana', 80, 45 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Limpieza dental');

INSERT INTO servicios (nombre, descripcion, precio, duracion_min)
SELECT 'Extracción simple', 'Extracción de pieza dental sin complicaciones', 60, 30 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Extracción simple');

INSERT INTO servicios (nombre, descripcion, precio, duracion_min)
SELECT 'Obturación con resina', 'Restauración de caries con resina compuesta', 90, 45 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Obturación con resina');

INSERT INTO servicios (nombre, descripcion, precio, duracion_min)
SELECT 'Endodoncia (tratamiento de conducto)', 'Tratamiento del nervio dental', 350, 90 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Endodoncia (tratamiento de conducto)');

INSERT INTO servicios (nombre, descripcion, precio, duracion_min)
SELECT 'Blanqueamiento dental', 'Blanqueamiento profesional en consultorio', 450, 60 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Blanqueamiento dental');

INSERT INTO servicios (nombre, descripcion, precio, duracion_min)
SELECT 'Radiografía panorámica', 'Imagen radiográfica de toda la boca', 40, 15 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM servicios WHERE nombre = 'Radiografía panorámica');
