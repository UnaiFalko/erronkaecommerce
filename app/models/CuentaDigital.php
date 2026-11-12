<?php
/** Guarda, cifra y entrega las credenciales de las cuentas digitales. */
class CuentaDigital extends Model
{
    /** Cuenta cuántas credenciales quedan para un producto. */
    public function disponibles(int $productoId): int
    {
        $row = $this->fetchOne(
            "SELECT COUNT(*) AS total FROM cuentas_digitales
             WHERE producto_id = ? AND estado = 'disponible'",
            [$productoId]
        );
        return (int) ($row['total'] ?? 0);
    }

    /** Importa una sola vez las cuentas ficticias preparadas en la base de datos. */
    public function importarCuentasDemoPendientes(): void
    {
        $this->db->beginTransaction();
        try {
            $marcador = $this->db->prepare(
                "INSERT IGNORE INTO cuentas_demo_importadas (id)
                 SELECT 'stock-cuentas-demo-v1'
                 WHERE EXISTS (SELECT 1 FROM cuentas_demo_pendientes)"
            );
            $marcador->execute();
            if ($marcador->rowCount() === 0) {
                $this->db->commit();
                return;
            }

            $pendientes = $this->db->query(
                'SELECT producto_id, usuario, contrasena
                 FROM cuentas_demo_pendientes
                 ORDER BY producto_id, numero'
            )->fetchAll();
            $insertar = $this->db->prepare(
                "INSERT INTO cuentas_digitales
                    (producto_id, usuario_cifrado, contrasena_cifrada, estado)
                 VALUES (?, ?, ?, 'disponible')"
            );

            foreach ($pendientes as $cuenta) {
                $insertar->execute([
                    (int) $cuenta['producto_id'],
                    $this->cifrar((string) $cuenta['usuario']),
                    $this->cifrar((string) $cuenta['contrasena']),
                ]);
            }

            $this->db->exec(
                "UPDATE produktuak p
                 INNER JOIN kategoriak k ON k.id = p.kategoria_id
                 LEFT JOIN (
                     SELECT producto_id, COUNT(*) AS total
                     FROM cuentas_digitales
                     WHERE estado = 'disponible'
                     GROUP BY producto_id
                 ) cuentas ON cuentas.producto_id = p.id
                 SET p.stock = COALESCE(cuentas.total, 0)
                 WHERE k.izena = 'Cuentas'"
            );
            $this->db->exec('DELETE FROM cuentas_demo_pendientes');
            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /** Cifra y guarda una cuenta nueva y actualiza el stock. */
    public function agregar(int $productoId, string $usuario, string $contrasena): void
    {
        $usuario = trim($usuario);
        if ($usuario === '' || $contrasena === '') {
            throw new RuntimeException('Escribe el usuario y la contraseña de la cuenta.');
        }

        $usuarioCifrado = $this->cifrar($usuario);
        $contrasenaCifrada = $this->cifrar($contrasena);

        $this->db->beginTransaction();
        try {
            $this->execute(
                'INSERT INTO cuentas_digitales (producto_id, usuario_cifrado, contrasena_cifrada)
                 VALUES (?, ?, ?)',
                [$productoId, $usuarioCifrado, $contrasenaCifrada]
            );
            $this->execute('UPDATE produktuak SET stock = stock + 1 WHERE id = ?', [$productoId]);
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** Bloquea cuentas disponibles para que no se asignen a dos pedidos. */
    public function bloquearDisponibles(int $productoId, int $cantidad): array
    {
        $consulta = $this->db->prepare(
            "SELECT id FROM cuentas_digitales
             WHERE producto_id = ? AND estado = 'disponible'
             ORDER BY id LIMIT $cantidad FOR UPDATE"
        );
        $consulta->execute([$productoId]);
        $ids = array_map('intval', $consulta->fetchAll(PDO::FETCH_COLUMN));

        if (count($ids) !== $cantidad) {
            throw new RuntimeException('No hay suficientes cuentas disponibles para completar el pedido.');
        }
        return $ids;
    }

    /** Marca las credenciales seleccionadas como entregadas al pedido. */
    public function asignar(array $ids, int $lineaPedidoId): void
    {
        if (!$ids) {
            return;
        }

        $marcadores = implode(', ', array_fill(0, count($ids), '?'));
        $actualizadas = $this->execute(
            "UPDATE cuentas_digitales
             SET estado = 'entregada', linea_pedido_id = ?
             WHERE estado = 'disponible' AND id IN ($marcadores)",
            array_merge([$lineaPedidoId], $ids)
        );

        if ($actualizadas !== count($ids)) {
            throw new RuntimeException('No se pudieron asignar las cuentas al pedido.');
        }
    }

    /** Devuelve descifradas las credenciales de una línea del pedido. */
    public function paraLinea(int $lineaPedidoId): array
    {
        $cuentas = $this->fetchAll(
            'SELECT usuario_cifrado, contrasena_cifrada FROM cuentas_digitales
             WHERE linea_pedido_id = ? ORDER BY id',
            [$lineaPedidoId]
        );

        foreach ($cuentas as &$cuenta) {
            $cuenta = [
                'usuario' => $this->descifrar($cuenta['usuario_cifrado']),
                'contrasena' => $this->descifrar($cuenta['contrasena_cifrada']),
            ];
        }
        unset($cuenta);

        return $cuentas;
    }

    /** Cifra un texto con AES-256-GCM. */
    private function cifrar(string $valor): string
    {
        $clave = $this->clave();
        if (!extension_loaded('openssl')) {
            throw new RuntimeException('La extensión OpenSSL de PHP es necesaria para cifrar cuentas digitales.');
        }

        $nonce = random_bytes(12);
        $tag = '';
        $cifrado = openssl_encrypt($valor, 'aes-256-gcm', $clave, OPENSSL_RAW_DATA, $nonce, $tag);
        if ($cifrado === false) {
            throw new RuntimeException('No se pudo cifrar la cuenta digital.');
        }
        return 'v2:' . base64_encode($nonce . $tag . $cifrado);
    }

    /** Descifra el formato actual y, si hace falta, el formato antiguo. */
    private function descifrar(string $valor): string
    {
        if (str_starts_with($valor, 'v2:')) {
            if (!extension_loaded('openssl')) {
                throw new RuntimeException('La extensión OpenSSL de PHP es necesaria para leer cuentas digitales.');
            }

            $datos = base64_decode(substr($valor, 3), true);
            if ($datos === false || strlen($datos) < 28) {
                throw new RuntimeException('No se pudo leer una cuenta digital.');
            }

            $nonce = substr($datos, 0, 12);
            $tag = substr($datos, 12, 16);
            $cifrado = substr($datos, 28);
            foreach ($this->clavesDisponibles() as $clave) {
                $texto = openssl_decrypt($cifrado, 'aes-256-gcm', $clave, OPENSSL_RAW_DATA, $nonce, $tag);
                if ($texto !== false) {
                    return $texto;
                }
            }
            throw new RuntimeException('La clave de cifrado no coincide con la usada al guardar las cuentas.');
        }

        if (!extension_loaded('sodium')) {
            throw new RuntimeException('La extensión Sodium de PHP es necesaria para leer cuentas cifradas con la versión anterior.');
        }

        $datos = base64_decode($valor, true);
        if ($datos === false || strlen($datos) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
            throw new RuntimeException('No se pudo leer una cuenta digital.');
        }

        $nonce = substr($datos, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cifrado = substr($datos, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $clave = $this->clave();
        $texto = sodium_crypto_secretbox_open($cifrado, $nonce, $clave);
        if ($texto !== false) {
            return $texto;
        }

        $fallback = base64_decode(APP_ENCRYPTION_KEY_FALLBACK, true);
        if ($fallback !== false && strlen($fallback) === SODIUM_CRYPTO_SECRETBOX_KEYBYTES && !hash_equals($clave, $fallback)) {
            $texto = sodium_crypto_secretbox_open($cifrado, $nonce, $fallback);
            if ($texto !== false) {
                return $texto;
            }
        }
        throw new RuntimeException('La clave de cifrado no coincide con la usada al guardar las cuentas.');
    }

    /** Lee y valida la clave principal de cifrado. */
    private function clave(): string
    {
        $clave = base64_decode(APP_ENCRYPTION_KEY, true);
        if ($clave === false || strlen($clave) !== 32) {
            throw new RuntimeException('Configura APP_ENCRYPTION_KEY con una clave Base64 de 32 bytes.');
        }
        return $clave;
    }

    /** Devuelve la clave actual y la anterior si está configurada. */
    private function clavesDisponibles(): array
    {
        $claves = [$this->clave()];
        $fallback = base64_decode(APP_ENCRYPTION_KEY_FALLBACK, true);
        if ($fallback !== false && strlen($fallback) === 32 && !hash_equals($claves[0], $fallback)) {
            $claves[] = $fallback;
        }
        return $claves;
    }
}