<?php
class CuentaDigital extends Model
{
    public function disponibles(int $productoId): int
    {
        $row = $this->fetchOne(
            "SELECT COUNT(*) AS total FROM cuentas_digitales
             WHERE producto_id = ? AND estado = 'disponible'",
            [$productoId]
        );
        return (int) ($row['total'] ?? 0);
    }

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

    private function cifrar(string $valor): string
    {
        $clave = $this->clave();
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cifrado = sodium_crypto_secretbox($valor, $nonce, $clave);
        return base64_encode($nonce . $cifrado);
    }

    private function descifrar(string $valor): string
    {
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

    private function clave(): string
    {
        if (!extension_loaded('sodium')) {
            throw new RuntimeException('La extensión Sodium de PHP es necesaria para gestionar cuentas digitales.');
        }

        $clave = base64_decode(APP_ENCRYPTION_KEY, true);
        if ($clave === false || strlen($clave) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new RuntimeException('Configura APP_ENCRYPTION_KEY con una clave Base64 de 32 bytes.');
        }
        return $clave;
    }
}