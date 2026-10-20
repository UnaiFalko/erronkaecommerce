<?php
/**
 * Oinarrizko modeloa: PDO konexioa eta kontsulta laguntzaileak.
 * Kontsulta guztiak prepared statements dira (3.2.2).
 */
abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);
        $row = $st->fetch();
        return $row ?: null;
    }

    protected function execute(string $sql, array $params = []): int
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->rowCount();
    }
}
