<?php
/** Consulta y crea las categorías de productos. */
class Kategoria extends Model
{
    public ?int $id = null;
    public string $izena = '';

    /** Guarda el identificador y nombre recibidos. */
    public function __construct(?int $id = null, string $izena = '')
    {
        parent::__construct();
        $this->id = $id;
        $this->izena = $izena;
    }

    /** Devuelve todas las categorías ordenadas por nombre. */
    public function guztiak(): array
    {
        return $this->fetchAll('SELECT id, izena FROM kategoriak ORDER BY izena');
    }

    /** Devuelve solo las categorías que aparecen en la tienda. */
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

    /** Busca una categoría por su identificador. */
    public function bilatu(int $id): ?array
    {
        return $this->fetchOne('SELECT id, izena FROM kategoriak WHERE id = ?', [$id]);
    }

    /** Crea una categoría y devuelve su nuevo identificador. */
    public function sortu(): int
    {
        $this->execute('INSERT INTO kategoriak (izena) VALUES (?)', [$this->izena]);
        return (int) $this->db->lastInsertId();
    }
}
