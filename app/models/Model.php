<?php
/**
 * Clase base de los modelos; comparte la conexión y las consultas preparadas.
 */
abstract class Model
{
    protected PDO $db;

    /** Abre la conexión compartida con la base de datos. */
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** Ejecuta una consulta y devuelve todas sus filas. */
    protected function fetchAll(string $sql, array $params = []): array
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    /** Ejecuta una consulta y devuelve una fila o null si no hay resultados. */
    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);
        $row = $st->fetch();
        return $row ?: null;
    }

    /** Ejecuta una consulta que cambia datos y devuelve el número de filas afectadas. */
    protected function execute(string $sql, array $params = []): int
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->rowCount();
    }
}
