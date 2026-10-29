SET NAMES utf8mb4;
-- Datos de demostración (opcional). Ejecutar DESPUÉS de clinica_odontologica.sql y solo una vez.
USE sonrisa_perfecta_db;

INSERT INTO pacientes (nombres, apellidos, dni, fecha_nacimiento, sexo, telefono, email, direccion) VALUES
('María', 'Quispe Huamán', '45871236', '1990-04-12', 'femenino', '987654321', 'maria.quispe@correo.com', 'Av. Los Olivos 123, Lima'),
('Carlos', 'Mendoza Rojas', '40215987', '1985-09-30', 'masculino', '912345678', 'carlos.mendoza@correo.com', 'Jr. Las Flores 456, Lima'),
('Lucía', 'Torres Vega', '72536481', '2001-01-22', 'femenino', '956781234', NULL, 'Calle Real 89, Miraflores');

INSERT INTO odontologos (nombre, colegiatura, especialidad, telefono, email) VALUES
('Dr. Javier Ramírez', 'COP-12345', 'Ortodoncia', '999111222', 'jramirez@sonrisaperfecta.pe'),
('Dra. Andrea Salas', 'COP-23456', 'Endodoncia', '999333444', 'asalas@sonrisaperfecta.pe');

-- Cita 1: atendida (endodoncia)
INSERT INTO citas (paciente_id, odontologo_id, servicio_id, fecha_hora, estado, motivo)
SELECT p.id, o.id, s.id, '2026-10-28 09:00:00', 'atendida', 'Dolor intenso en molar inferior'
FROM pacientes p, odontologos o, servicios s
WHERE p.dni = '45871236' AND o.colegiatura = 'COP-23456' AND s.nombre = 'Endodoncia (tratamiento de conducto)';

-- Cita 2: atendida (extracción)
INSERT INTO citas (paciente_id, odontologo_id, servicio_id, fecha_hora, estado, motivo)
SELECT p.id, o.id, s.id, '2026-10-28 11:00:00', 'atendida', 'Muela del juicio con molestias'
FROM pacientes p, odontologos o, servicios s
WHERE p.dni = '40215987' AND o.colegiatura = 'COP-23456' AND s.nombre = 'Extracción simple';

-- Cita 3: pendiente
INSERT INTO citas (paciente_id, odontologo_id, servicio_id, fecha_hora, estado, motivo)
SELECT p.id, o.id, s.id, '2026-10-29 16:00:00', 'pendiente', 'Evaluación de ortodoncia'
FROM pacientes p, odontologos o, servicios s
WHERE p.dni = '40215987' AND o.colegiatura = 'COP-12345' AND s.nombre = 'Consulta de evaluación';

-- Cita 4: confirmada
INSERT INTO citas (paciente_id, odontologo_id, servicio_id, fecha_hora, estado, motivo)
SELECT p.id, o.id, s.id, '2026-10-30 10:00:00', 'confirmada', 'Limpieza semestral'
FROM pacientes p, odontologos o, servicios s
WHERE p.dni = '72536481' AND o.colegiatura = 'COP-12345' AND s.nombre = 'Limpieza dental';

-- Diagnóstico y tratamiento de la cita 1
INSERT INTO diagnosticos (cita_id, descripcion, observaciones, fecha)
SELECT c.id, 'Pulpitis irreversible en pieza 36', 'Se realiza tratamiento de conducto en dos sesiones', '2026-10-28'
FROM citas c JOIN pacientes p ON p.id = c.paciente_id
WHERE p.dni = '45871236' AND c.fecha_hora = '2026-10-28 09:00:00';

INSERT INTO tratamientos (diagnostico_id, pieza_dental, procedimiento, medicamento, indicaciones, fecha_inicio, fecha_fin, costo)
SELECT d.id, '36', 'Endodoncia (conducto)', 'Ibuprofeno 400 mg', 'Tomar cada 8 horas por 3 días si hay dolor', '2026-10-28', '2026-11-04', 350
FROM diagnosticos d
JOIN citas c ON c.id = d.cita_id
JOIN pacientes p ON p.id = c.paciente_id
WHERE p.dni = '45871236' AND c.fecha_hora = '2026-10-28 09:00:00';

-- Pagos
INSERT INTO pagos (cita_id, monto, metodo, estado, referencia, fecha_pago)
SELECT c.id, 350, 'yape', 'pagado', 'YAPE-000123', '2026-10-28 10:15:00'
FROM citas c JOIN pacientes p ON p.id = c.paciente_id
WHERE p.dni = '45871236' AND c.fecha_hora = '2026-10-28 09:00:00';

INSERT INTO pagos (cita_id, monto, metodo, estado, referencia, fecha_pago)
SELECT c.id, 60, 'efectivo', 'pagado', NULL, '2026-10-28 11:40:00'
FROM citas c JOIN pacientes p ON p.id = c.paciente_id
WHERE p.dni = '40215987' AND c.fecha_hora = '2026-10-28 11:00:00';

INSERT INTO pagos (cita_id, monto, metodo, estado, referencia, fecha_pago)
SELECT c.id, 80, 'tarjeta_debito', 'pendiente', NULL, NULL
FROM citas c JOIN pacientes p ON p.id = c.paciente_id
WHERE p.dni = '72536481' AND c.fecha_hora = '2026-10-30 10:00:00';
