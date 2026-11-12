-- Crea el inventario de cuentas digitales y sincroniza el stock disponible.
USE ecommerce;
SET NAMES utf8mb4;
START TRANSACTION;

CREATE TABLE IF NOT EXISTS cuentas_digitales (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    producto_id         INT UNSIGNED NOT NULL,
    usuario_cifrado     TEXT NOT NULL,
    contrasena_cifrada  TEXT NOT NULL,
    estado              ENUM('disponible', 'entregada') NOT NULL DEFAULT 'disponible',
    linea_pedido_id     INT UNSIGNED NULL,
    creada              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cuenta_producto FOREIGN KEY (producto_id)
        REFERENCES produktuak(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_cuenta_linea FOREIGN KEY (linea_pedido_id)
        REFERENCES eskaera_lerroak(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_cuenta_disponible (producto_id, estado, id),
    INDEX idx_cuenta_linea (linea_pedido_id)
) ENGINE=InnoDB;

UPDATE produktuak p
JOIN kategoriak k ON k.id = p.kategoria_id
LEFT JOIN (
    SELECT producto_id, COUNT(*) AS total
    FROM cuentas_digitales
    WHERE estado = 'disponible'
    GROUP BY producto_id
) cuentas ON cuentas.producto_id = p.id
SET p.stock = COALESCE(cuentas.total, 0)
WHERE k.izena = 'Cuentas';

COMMIT;