<?php
/** Kategoria klasea (1.3.1 OOP) */
class Kategoria extends Model
{
    public ?int $id = null;
    public string $izena = '';

    public function __construct(?int $id = null, string $izena = '')
    {
        parent::__construct();
        $this->id = $id;
        $this->izena = $izena;
    }

    public function guztiak(): array
    {
        return $this->fetchAll('SELECT id, izena FROM kategoriak ORDER BY izena');
    }

    public function dendakoak(): array
    {
        return $this->fetchAll(
            "SELECT id, izena FROM kategoriak
             WHERE izena IN ('Productos', 'Boosting', 'Cuentas')
             ORDER BY CASE izena
                 WHEN 'Productos' THEN 1
                 WHEN 'Boosting' THEN 2
                 WHEN 'Cuentas' THEN 3
             END"
        );
    }

    public function bilatu(int $id): ?array
    {
        return $this->fetchOne('SELECT id, izena FROM kategoriak WHERE id = ?', [$id]);
    }

    public function sortu(): int
    {
        $this->execute('INSERT INTO kategoriak (izena) VALUES (?)', [$this->izena]);
        return (int) $this->db->lastInsertId();
    }
}
