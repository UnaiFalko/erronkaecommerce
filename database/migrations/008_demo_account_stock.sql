-- Prepara cuentas ficticias pendientes para cargarlas de forma segura al iniciar.
USE ecommerce;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS cuentas_demo_pendientes (
    producto_id  INT UNSIGNED NOT NULL,
    numero       TINYINT UNSIGNED NOT NULL,
    usuario      VARCHAR(150) NOT NULL,
    contrasena   VARCHAR(100) NOT NULL,
    PRIMARY KEY (producto_id, numero),
    CONSTRAINT fk_demo_cuenta_producto FOREIGN KEY (producto_id)
        REFERENCES produktuak(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cuentas_demo_importadas (
    id       VARCHAR(64) PRIMARY KEY,
    aplicada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO cuentas_demo_pendientes (producto_id, numero, usuario, contrasena)
SELECT generadas.producto_id,
       generadas.numero,
       CONCAT(generadas.nombre, '.', generadas.apellido, generadas.sufijo, '@gmail.com'),
       CONCAT(generadas.nombre, generadas.sufijo)
FROM (
    SELECT p.id AS producto_id,
           unidades.numero,
           ELT(FLOOR(RAND() * 20) + 1,
               'maria', 'juan', 'lucia', 'david', 'sofia', 'pablo', 'laura', 'alejandro',
               'nerea', 'sergio', 'clara', 'carlos', 'ines', 'jorge', 'martina', 'ivan',
               'patricia', 'adrian', 'noa', 'leo') AS nombre,
           ELT(FLOOR(RAND() * 20) + 1,
               'garcia', 'lopez', 'martinez', 'fernandez', 'sanchez', 'gonzalez', 'torres',
               'ruiz', 'castro', 'moreno', 'jimenez', 'navarro', 'diaz', 'ramirez',
               'vazquez', 'bergua', 'santos', 'romero', 'silva', 'alvarez') AS apellido,
           LPAD(FLOOR(RAND() * 90) + 10, 2, '0') AS sufijo
    FROM produktuak p
    INNER JOIN kategoriak k ON k.id = p.kategoria_id
    CROSS JOIN (
        SELECT 1 AS numero UNION ALL SELECT 2 UNION ALL SELECT 3
    ) unidades
    WHERE k.izena = 'Cuentas'
) generadas
WHERE NOT EXISTS (
    SELECT 1 FROM cuentas_demo_importadas WHERE id = 'stock-cuentas-demo-v1'
);
