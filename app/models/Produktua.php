<?php
/**
 * Produktua klasea. CRUD operazioak (3.2.2): sortu, irakurri, eguneratu, ezabatu.
 */
class Produktua extends Model
{
    public ?int $id = null;
    public string $izena = '';
    public string $deskribapena = '';
    public float $prezioa = 0.0;
    public int $stock = 0;
    public ?int $kategoriaId = null;
    public ?string $irudia = null;

    public function betetzen(array $d): self
    {
        $this->izena        = trim($d['izena'] ?? '');
        $this->deskribapena = trim($d['deskribapena'] ?? '');
        $this->prezioa      = (float) str_replace(',', '.', (string) ($d['prezioa'] ?? 0));
        $this->stock        = (int) ($d['stock'] ?? 0);
        $this->kategoriaId  = ($d['kategoria_id'] ?? '') !== '' ? (int) $d['kategoria_id'] : null;
        $this->irudia       = trim($d['irudia'] ?? '') ?: null;
        return $this;
    }

    /** @return string[] balidazio-erroreak */
    public function balidatu(): array
    {
        $err = [];
        if ($this->izena === '' || mb_strlen($this->izena) > 150) {
            $err[] = 'Izena beharrezkoa da (gehienez 150 karaktere).';
        }
        if ($this->prezioa <= 0) {
            $err[] = 'Prezioak 0 baino handiagoa izan behar du.';
        }
        if ($this->stock < 0) {
            $err[] = 'Stock-a ezin da negatiboa izan.';
        }
        return $err;
    }

    // ---- CREATE ----
    public function sortu(): int
    {
        $this->execute(
            'INSERT INTO produktuak (izena, deskribapena, prezioa, stock, kategoria_id, irudia)
             VALUES (?, ?, ?, ?, ?, ?)',
            [$this->izena, $this->deskribapena, $this->prezioa, $this->stock, $this->kategoriaId, $this->irudia]
        );
        return (int) $this->db->lastInsertId();
    }

    // ---- READ ----
    public function guztiak(?int $kategoriaId = null, string $bilaketa = '', string $orden = 'recientes'): array
    {
        return $this->zerrenda($kategoriaId, $bilaketa, false, $orden);
    }

    public function katalogoa(?int $kategoriaId = null, string $bilaketa = '', string $orden = 'recientes'): array
    {
        return $this->zerrenda($kategoriaId, $bilaketa, true, $orden);
    }

    private function zerrenda(?int $kategoriaId, string $bilaketa, bool $dendakoakBakarrik, string $orden): array
    {
        $sql = 'SELECT p.*, k.izena AS kategoria
                FROM produktuak p LEFT JOIN kategoriak k ON k.id = p.kategoria_id
                WHERE 1=1';
        $params = [];
        if ($dendakoakBakarrik) {
            $sql .= " AND k.izena IN ('Productos', 'Boosting', 'Cuentas')";
        }
        if ($kategoriaId) {
            $sql .= ' AND p.kategoria_id = ?';
            $params[] = $kategoriaId;
        }
        if ($bilaketa !== '') {
            $sql .= ' AND (p.izena LIKE ? OR p.deskribapena LIKE ?)';
            $params[] = "%$bilaketa%";
            $params[] = "%$bilaketa%";
        }
        $ordenak = [
            'recientes'    => 'p.id DESC',
            'precio_menor' => 'p.prezioa ASC, p.id DESC',
            'precio_mayor' => 'p.prezioa DESC, p.id DESC',
            'nombre'       => 'p.izena ASC',
        ];
        $sql .= ' ORDER BY ' . ($ordenak[$orden] ?? $ordenak['recientes']);
        return $this->fetchAll($sql, $params);
    }

    public function bilatu(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT p.*, k.izena AS kategoria
             FROM produktuak p LEFT JOIN kategoriak k ON k.id = p.kategoria_id
             WHERE p.id = ?',
            [$id]
        );
    }

    // ---- UPDATE ----
    public function eguneratu(): bool
    {
        return $this->execute(
            'UPDATE produktuak
             SET izena = ?, deskribapena = ?, prezioa = ?, stock = ?, kategoria_id = ?, irudia = ?
             WHERE id = ?',
            [$this->izena, $this->deskribapena, $this->prezioa, $this->stock, $this->kategoriaId, $this->irudia, $this->id]
        ) >= 0;
    }

    public function stockMurriztu(int $id, int $kopurua): bool
    {
        return $this->execute(
            'UPDATE produktuak SET stock = stock - ? WHERE id = ? AND stock >= ?',
            [$kopurua, $id, $kopurua]
        ) === 1;
    }

    // ---- DELETE ----
    public function ezabatu(int $id): bool
    {
        return $this->execute('DELETE FROM produktuak WHERE id = ?', [$id]) === 1;
    }
}
