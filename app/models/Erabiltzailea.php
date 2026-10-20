<?php
/** Erabiltzailea klasea: erregistroa eta autentifikazioa. */
class Erabiltzailea extends Model
{
    public function emailezBilatu(string $email): ?array
    {
        return $this->fetchOne('SELECT * FROM erabiltzaileak WHERE email = ?', [$email]);
    }

    public function sortu(string $izena, string $email, string $pasahitza): int
    {
        $this->execute(
            'INSERT INTO erabiltzaileak (izena, email, pasahitza_hash, rola) VALUES (?, ?, ?, "bezeroa")',
            [$izena, $email, password_hash($pasahitza, PASSWORD_DEFAULT)]
        );
        return (int) $this->db->lastInsertId();
    }

    public function autentifikatu(string $email, string $pasahitza): ?array
    {
        $u = $this->emailezBilatu($email);
        if ($u && password_verify($pasahitza, $u['pasahitza_hash'])) {
            return $u;
        }
        return null;
    }
}
