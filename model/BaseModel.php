<?php
// Acceso a datos genérico. Tablas y columnas se definen en cada modelo y nunca
// provienen del usuario. Todos los valores se envían como parámetros preparados.
abstract class BaseModel
{
    protected PDO $db;
    protected string $tabla;
    protected array $columnas;
    /** SELECT del listado con alias "t" para la tabla principal. Vacío = SELECT t.* */
    protected string $listado = '';

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function listar(int $limite = 300): array
    {
        $base = $this->listado !== '' ? $this->listado : "SELECT t.* FROM `{$this->tabla}` t";
        $consulta = $this->db->prepare($base . ' ORDER BY t.id DESC LIMIT :limite');
        $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll();
    }

    public function obtener(int $id): array|false
    {
        $consulta = $this->db->prepare("SELECT * FROM `{$this->tabla}` WHERE id = ?");
        $consulta->execute([$id]);
        return $consulta->fetch();
    }

    public function guardar(array $datos, int $id = 0): void
    {
        $valores = array_map(fn($columna) => $datos[$columna] ?? null, $this->columnas);

        if ($id > 0) {
            $asignaciones = implode(', ', array_map(fn($columna) => "`$columna` = ?", $this->columnas));
            $consulta = $this->db->prepare("UPDATE `{$this->tabla}` SET $asignaciones WHERE id = ?");
            $consulta->execute([...$valores, $id]);
        } else {
            $nombres = implode(', ', array_map(fn($columna) => "`$columna`", $this->columnas));
            $marcadores = implode(', ', array_fill(0, count($this->columnas), '?'));
            $consulta = $this->db->prepare("INSERT INTO `{$this->tabla}` ($nombres) VALUES ($marcadores)");
            $consulta->execute($valores);
        }
    }

    public function eliminar(int $id): void
    {
        $consulta = $this->db->prepare("DELETE FROM `{$this->tabla}` WHERE id = ?");
        $consulta->execute([$id]);
    }
}
