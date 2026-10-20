<?php
/**
 * Eskaera klasea: eskaera bat sortu (transakzioarekin) eta kontsultatu.
 * Transakzioari esker, stock-a eta eskaera koherenteak dira.
 */
class Eskaera extends Model
{
    /**
     * @param array $saskia [produktuId => kopurua]
     */
    public function sortu(int $erabiltzaileId, array $saskia): int
    {
        $this->db->beginTransaction();
        try {
            $produktua = new Produktua();
            $guztira = 0.0;
            $lerroak = [];

            foreach ($saskia as $pid => $kop) {
                $p = $produktua->bilatu((int) $pid);
                if (!$p || $kop < 1) {
                    throw new RuntimeException('Produktu baliogabea saskian.');
                }
                if (!$produktua->stockMurriztu((int) $pid, (int) $kop)) {
                    throw new RuntimeException('Stock nahikorik ez: ' . $p['izena']);
                }
                $guztira += $p['prezioa'] * $kop;
                $lerroak[] = [$pid, $kop, $p['prezioa']];
            }

            $this->execute(
                'INSERT INTO eskaerak (erabiltzailea_id, guztira, egoera) VALUES (?, ?, "berria")',
                [$erabiltzaileId, $guztira]
            );
            $eskaeraId = (int) $this->db->lastInsertId();

            foreach ($lerroak as [$pid, $kop, $prezioa]) {
                $this->execute(
                    'INSERT INTO eskaera_lerroak (eskaera_id, produktua_id, kopurua, prezioa_unitarioa)
                     VALUES (?, ?, ?, ?)',
                    [$eskaeraId, $pid, $kop, $prezioa]
                );
            }

            $this->db->commit();
            return $eskaeraId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function erabiltzailearenak(int $erabiltzaileId): array
    {
        return $this->fetchAll(
            'SELECT * FROM eskaerak WHERE erabiltzailea_id = ? ORDER BY data DESC',
            [$erabiltzaileId]
        );
    }

    public function guztiak(): array
    {
        return $this->fetchAll(
            'SELECT e.*, u.izena AS bezeroa FROM eskaerak e
             JOIN erabiltzaileak u ON u.id = e.erabiltzailea_id ORDER BY e.data DESC'
        );
    }

    public function bilatu(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM eskaerak WHERE id = ?', [$id]);
    }

    public function lerroak(int $eskaeraId): array
    {
        return $this->fetchAll(
            'SELECT l.*, p.izena FROM eskaera_lerroak l
             JOIN produktuak p ON p.id = l.produktua_id WHERE l.eskaera_id = ?',
            [$eskaeraId]
        );
    }

    public function ezabatu(int $id): bool
    {
        $this->db->beginTransaction();
        try {
            $this->execute('DELETE FROM eskaera_lerroak WHERE eskaera_id = ?', [$id]);
            $ezabatuta = $this->execute('DELETE FROM eskaerak WHERE id = ?', [$id]) === 1;
            $this->db->commit();
            return $ezabatuta;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
