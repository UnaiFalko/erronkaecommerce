<?php
/** Busca usuarios, crea cuentas y comprueba contraseñas. */
class Erabiltzailea extends Model
{
    /** Devuelve los usuarios sin incluir sus contraseñas. */
    public function guztiak(): array
    {
        return $this->fetchAll(
            'SELECT id, izena, email, rola, sortze_data FROM erabiltzaileak ORDER BY id DESC'
        );
    }

    /** Busca un usuario para mostrarlo en el formulario de administración. */
    public function bilatu(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT id, izena, email, rola, sortze_data FROM erabiltzaileak WHERE id = ?',
            [$id]
        );
    }

    /** Busca un usuario por su correo. */
    public function emailezBilatu(string $email): ?array
    {
        return $this->fetchOne('SELECT * FROM erabiltzaileak WHERE email = ?', [$email]);
    }

    /** Crea un usuario y guarda el hash seguro de su contraseña. */
    public function sortu(string $izena, string $email, string $pasahitza): int
    {
        $this->execute(
            'INSERT INTO erabiltzaileak (izena, email, pasahitza_hash, rola) VALUES (?, ?, ?, "bezeroa")',
            [$izena, $email, password_hash($pasahitza, PASSWORD_DEFAULT)]
        );
        return (int) $this->db->lastInsertId();
    }

    /** Crea un usuario desde la página de administración. */
    public function sortuAdministrazioan(string $izena, string $email, string $pasahitza, string $rola): int
    {
        $this->execute(
            'INSERT INTO erabiltzaileak (izena, email, pasahitza_hash, rola) VALUES (?, ?, ?, ?)',
            [$izena, $email, password_hash($pasahitza, PASSWORD_DEFAULT), $rola]
        );
        return (int) $this->db->lastInsertId();
    }

    /** Actualiza un usuario y evita que desaparezca el último administrador. */
    public function eguneratuAdministrazioan(
        int $id,
        string $izena,
        string $email,
        string $rola,
        ?string $pasahitza
    ): bool {
        $this->db->beginTransaction();
        try {
            $administratzaileak = $this->fetchAll(
                "SELECT id FROM erabiltzaileak WHERE rola = 'admin' FOR UPDATE"
            );
            $erabiltzailea = $this->fetchOne(
                'SELECT id, rola FROM erabiltzaileak WHERE id = ? FOR UPDATE',
                [$id]
            );

            if (!$erabiltzailea) {
                $this->db->commit();
                return false;
            }

            if (
                $erabiltzailea['rola'] === 'admin'
                && $rola !== 'admin'
                && count($administratzaileak) <= 1
            ) {
                throw new RuntimeException('No se puede quitar el rol al último administrador.');
            }

            if ($pasahitza !== null) {
                $this->execute(
                    'UPDATE erabiltzaileak
                     SET izena = ?, email = ?, rola = ?, pasahitza_hash = ?
                     WHERE id = ?',
                    [$izena, $email, $rola, password_hash($pasahitza, PASSWORD_DEFAULT), $id]
                );
            } else {
                $this->execute(
                    'UPDATE erabiltzaileak SET izena = ?, email = ?, rola = ? WHERE id = ?',
                    [$izena, $email, $rola, $id]
                );
            }

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /** Elimina un usuario sin permitir borrar al último administrador. */
    public function ezabatuAdministrazioan(int $id): bool
    {
        $this->db->beginTransaction();
        try {
            $administratzaileak = $this->fetchAll(
                "SELECT id FROM erabiltzaileak WHERE rola = 'admin' FOR UPDATE"
            );
            $erabiltzailea = $this->fetchOne(
                'SELECT id, rola FROM erabiltzaileak WHERE id = ? FOR UPDATE',
                [$id]
            );

            if (!$erabiltzailea) {
                $this->db->commit();
                return false;
            }

            if ($erabiltzailea['rola'] === 'admin' && count($administratzaileak) <= 1) {
                throw new RuntimeException('No se puede eliminar al último administrador.');
            }

            $eliminados = $this->execute('DELETE FROM erabiltzaileak WHERE id = ?', [$id]);
            $this->db->commit();
            return $eliminados === 1;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /** Devuelve el usuario solo si el correo y la contraseña son correctos. */
    public function autentifikatu(string $email, string $pasahitza): ?array
    {
        $u = $this->emailezBilatu($email);
        if ($u && password_verify($pasahitza, $u['pasahitza_hash'])) {
            return $u;
        }
        return null;
    }
}
