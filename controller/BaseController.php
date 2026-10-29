<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/BaseModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Conexión PDO compartida durante la petición. */
function db(): PDO
{
    static $db = null;
    return $db ??= (new Database())->conectar();
}

/** Escapa un valor para mostrarlo en HTML (protección contra XSS). */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/** Token CSRF de la sesión actual. */
function csrf(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function validarCsrf(): void
{
    if (!hash_equals(csrf(), (string) ($_POST['csrf'] ?? ''))) {
        throw new RuntimeException('Token de seguridad no válido');
    }
}

function exigirSesion(): void
{
    if (empty($_SESSION['uid'])) {
        header('Location: login.php');
        exit;
    }
}

function redirigir(string $ruta): void
{
    header('Location: ' . $ruta);
    exit;
}

function notificar(string $mensaje): void
{
    $_SESSION['flash'] = $mensaje;
}

/** Tipos que son claves foráneas: el valor es el id de otra tabla. */
function esReferencia(string $tipo): bool
{
    return in_array($tipo, ['pacientes', 'odontologos', 'servicios', 'citas', 'diagnosticos'], true);
}

/** Listas fijas de valores permitidos: [valor => texto visible]. */
function opciones(string $clave): array
{
    $especialidades = [
        'Odontología general', 'Ortodoncia', 'Endodoncia', 'Periodoncia', 'Cirugía bucal',
        'Odontopediatría', 'Implantología', 'Estética dental', 'Prostodoncia', 'Rehabilitación oral',
    ];

    $listas = [
        'especialidad' => array_combine($especialidades, $especialidades),
        'sexo' => ['masculino' => 'Masculino', 'femenino' => 'Femenino', 'otro' => 'Otro'],
        'estado_cita' => ['pendiente' => 'Pendiente', 'confirmada' => 'Confirmada', 'atendida' => 'Atendida', 'cancelada' => 'Cancelada'],
        'estado_pago' => ['pendiente' => 'Pendiente', 'pagado' => 'Pagado', 'anulado' => 'Anulado'],
        'metodo_pago' => ['efectivo' => 'Efectivo', 'tarjeta_credito' => 'Tarjeta de crédito', 'tarjeta_debito' => 'Tarjeta de débito', 'yape' => 'Yape'],
    ];

    return $listas[$clave] ?? [];
}

/** Etiqueta visible de cada campo del formulario. */
function etiqueta(string $campo): string
{
    $etiquetas = [
        'nombres' => 'Nombres',
        'apellidos' => 'Apellidos',
        'dni' => 'DNI',
        'fecha_nacimiento' => 'Fecha de nacimiento',
        'sexo' => 'Sexo',
        'telefono' => 'Teléfono',
        'email' => 'Correo electrónico',
        'direccion' => 'Dirección',
        'nombre' => 'Nombre',
        'colegiatura' => 'N.º de colegiatura (COP)',
        'especialidad' => 'Especialidad',
        'descripcion' => 'Descripción',
        'precio' => 'Precio (S/)',
        'duracion_min' => 'Duración (minutos)',
        'paciente_id' => 'Paciente',
        'odontologo_id' => 'Odontólogo',
        'servicio_id' => 'Servicio',
        'fecha_hora' => 'Fecha y hora',
        'estado' => 'Estado',
        'motivo' => 'Motivo de la consulta',
        'cita_id' => 'Cita',
        'diagnostico_id' => 'Diagnóstico',
        'observaciones' => 'Observaciones',
        'fecha' => 'Fecha',
        'pieza_dental' => 'Pieza dental (FDI, ej. 16)',
        'procedimiento' => 'Procedimiento',
        'medicamento' => 'Medicamento',
        'indicaciones' => 'Indicaciones',
        'fecha_inicio' => 'Fecha de inicio',
        'fecha_fin' => 'Fecha de fin',
        'costo' => 'Costo (S/)',
        'monto' => 'Monto (S/)',
        'metodo' => 'Método de pago',
        'referencia' => 'N.º de operación / referencia',
        'fecha_pago' => 'Fecha de pago',
    ];

    return $etiquetas[$campo] ?? ucfirst(str_replace('_', ' ', $campo));
}

/** Devuelve la fecha (Y-m-d) si es válida; si no, null. */
function fechaValida(string $valor): ?string
{
    $fecha = DateTime::createFromFormat('!Y-m-d', $valor);
    return $fecha && $fecha->format('Y-m-d') === $valor ? $valor : null;
}

/**
 * Opciones de los <select> de claves foráneas: [id, etiqueta].
 * Solo usa consultas fijas en el código; ningún dato del usuario entra al SQL.
 */
function opcionesReferencia(string $tipo): array
{
    $consultas = [
        'pacientes' => "SELECT id, CONCAT(apellidos, ', ', nombres, ' (DNI ', dni, ')') AS etiqueta
                        FROM pacientes ORDER BY apellidos, nombres LIMIT 500",
        'odontologos' => "SELECT id, CONCAT(nombre, ' · ', especialidad) AS etiqueta
                          FROM odontologos ORDER BY nombre LIMIT 500",
        'servicios' => "SELECT id, CONCAT(nombre, ' (S/ ', precio, ')') AS etiqueta
                        FROM servicios ORDER BY nombre LIMIT 500",
        'citas' => "SELECT c.id, CONCAT(DATE_FORMAT(c.fecha_hora, '%d/%m/%Y %H:%i'), ' · ', p.apellidos, ', ', p.nombres) AS etiqueta
                    FROM citas c JOIN pacientes p ON p.id = c.paciente_id
                    ORDER BY c.fecha_hora DESC LIMIT 500",
        'diagnosticos' => "SELECT d.id, CONCAT('Diagnóstico #', d.id, ' · ', LEFT(d.descripcion, 40)) AS etiqueta
                           FROM diagnosticos d ORDER BY d.id DESC LIMIT 500",
    ];

    if (!isset($consultas[$tipo])) {
        return [];
    }

    return db()->query($consultas[$tipo])->fetchAll();
}

/** Texto legible de un valor guardado, según su tipo. */
function formatearValor(string $tipo, $valor): string
{
    if ($valor === null || $valor === '') {
        return '—';
    }

    if ($lista = opciones($tipo)) {
        return $lista[$valor] ?? (string) $valor;
    }

    return match ($tipo) {
        'number' => 'S/ ' . number_format((float) $valor, 2),
        'minutos' => $valor . ' min',
        'date' => date('d/m/Y', strtotime((string) $valor)),
        'datetime-local' => date('d/m/Y H:i', strtotime((string) $valor)),
        default => (string) $valor,
    };
}

/**
 * Valida y limpia los datos enviados por un formulario.
 * $campos: campo => tipo. $obligatorios: campos que no pueden quedar vacíos.
 */
function validarDatos(array $campos, array $datos, array $obligatorios = []): array
{
    $salida = [];

    foreach ($campos as $campo => $tipo) {
        $valor = trim((string) ($datos[$campo] ?? ''));
        $referencia = esReferencia($tipo);

        if ($valor === '') {
            if ($referencia || in_array($campo, $obligatorios, true)) {
                throw new RuntimeException('Complete el campo obligatorio: ' . etiqueta($campo) . '.');
            }
            $salida[$campo] = null;
            continue;
        }

        if ($referencia) {
            if (!ctype_digit($valor) || (int) $valor < 1) {
                throw new RuntimeException('Seleccione una opción válida en: ' . etiqueta($campo) . '.');
            }
        } elseif ($tipo === 'number') {
            if (!is_numeric($valor) || (float) $valor < 0) {
                throw new RuntimeException('Ingrese un número mayor o igual a 0 en: ' . etiqueta($campo) . '.');
            }
        } elseif ($tipo === 'entero') {
            if (!ctype_digit($valor) || (int) $valor < 1) {
                throw new RuntimeException('Ingrese un número entero mayor que 0 en: ' . etiqueta($campo) . '.');
            }
        } elseif ($tipo === 'email') {
            if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('El correo electrónico no es válido.');
            }
        } elseif ($tipo === 'dni') {
            if (!preg_match('/^\d{8}$/', $valor)) {
                throw new RuntimeException('El DNI debe tener exactamente 8 dígitos.');
            }
        } elseif ($tipo === 'date') {
            if (!fechaValida($valor)) {
                throw new RuntimeException('Fecha no válida en: ' . etiqueta($campo) . '.');
            }
        } elseif ($tipo === 'datetime-local') {
            $fecha = DateTime::createFromFormat('!Y-m-d\TH:i', $valor);
            if (!$fecha || $fecha->format('Y-m-d\TH:i') !== $valor) {
                throw new RuntimeException('Fecha y hora no válidas en: ' . etiqueta($campo) . '.');
            }
            $valor = $fecha->format('Y-m-d H:i:s');
        } elseif (!empty($lista = opciones($tipo)) && !array_key_exists($valor, $lista)) {
            throw new RuntimeException('Selección no válida en: ' . etiqueta($campo) . '.');
        }

        $salida[$campo] = $valor;
    }

    return $salida;
}

/** Módulos del sistema: menú, tarjetas de inicio y tabla asociada (null si no tiene tabla). */
function modulos(): array
{
    return [
        'pacientes' => ['nombre' => 'Pacientes', 'url' => 'pacientes.php', 'tabla' => 'pacientes', 'imagen' => 'pacientes.jpg', 'descripcion' => 'Datos personales de cada paciente.'],
        'odontologos' => ['nombre' => 'Odontólogos', 'url' => 'odontologos.php', 'tabla' => 'odontologos', 'imagen' => 'odontologos.jpg', 'descripcion' => 'Odontólogos de la clínica y su especialidad.'],
        'servicios' => ['nombre' => 'Servicios', 'url' => 'servicios.php', 'tabla' => 'servicios', 'imagen' => 'servicios.jpg', 'descripcion' => 'Servicios que ofrece la clínica, con precio y duración.'],
        'citas' => ['nombre' => 'Citas', 'url' => 'citas.php', 'tabla' => 'citas', 'imagen' => 'citas.jpg', 'descripcion' => 'Programación de citas con fecha, hora y estado.'],
        'diagnosticos' => ['nombre' => 'Diagnósticos', 'url' => 'diagnosticos.php', 'tabla' => 'diagnosticos', 'imagen' => 'diagnosticos.jpg', 'descripcion' => 'Diagnósticos registrados en cada atención.'],
        'tratamientos' => ['nombre' => 'Tratamientos', 'url' => 'tratamientos.php', 'tabla' => 'tratamientos', 'imagen' => 'tratamientos.jpg', 'descripcion' => 'Tratamientos realizados, por pieza dental.'],
        'pagos' => ['nombre' => 'Pagos', 'url' => 'pagos.php', 'tabla' => 'pagos', 'imagen' => 'pagos.jpg', 'descripcion' => 'Pagos en efectivo, tarjeta, Yape.'],
        'reportes' => ['nombre' => 'Reportes', 'url' => 'reportes.php', 'tabla' => null, 'imagen' => 'reportes.jpg', 'descripcion' => 'Atenciones realizadas, ingresos y citas pendientes.'],
    ];
}

/** Cantidad de registros de un módulo, o null si no tiene tabla. */
function contarRegistros(string $modulo): ?int
{
    $tabla = modulos()[$modulo]['tabla'] ?? null;
    if ($tabla === null) {
        return null;
    }
    return (int) db()->query("SELECT COUNT(*) FROM `$tabla`")->fetchColumn();
}

/**
 * Lógica CRUD común a todos los módulos.
 * Cada controlador concreto solo declara su modelo, campos, obligatorios y columnas.
 */
abstract class CrudController
{
    public BaseModel $modelo;
    public string $titulo = '';
    public string $archivo = '';
    /** campo => tipo (define el formulario y la validación) */
    public array $campos = [];
    /** campos que no pueden quedar vacíos */
    public array $obligatorios = [];
    /** columna => [etiqueta, tipo] (define la tabla del listado) */
    public array $columnasLista = [];

    public function procesar(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return '';
        }

        $accion = $_POST['accion'] ?? '';

        try {
            validarCsrf();
            $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;

            if ($accion === 'eliminar') {
                $this->modelo->eliminar($id);
            } else {
                $datos = validarDatos($this->campos, $_POST, $this->obligatorios);
                $this->modelo->guardar($datos, $id);
            }

            notificar('Operación realizada correctamente');
            redirigir($this->archivo);
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            if ((string) $ex->getCode() === '23000') {
                return $accion === 'eliminar'
                    ? 'No se puede eliminar: el registro tiene información relacionada (citas, pagos, etc.).'
                    : 'Ya existe un registro con ese dato único (DNI o colegiatura) o falta una referencia válida.';
            }
            return 'No se pudo completar la operación. Revise los datos ingresados.';
        } catch (Throwable $ex) {
            return $ex->getMessage();
        }
    }
}
