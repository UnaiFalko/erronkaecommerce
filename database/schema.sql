-- =====================================================================
-- E-commerce datu-basea (MySQL / MariaDB)
-- 3.3.2: E-R eredua taula fisiko gisa (CREATE TABLE) .sql fitxategian
-- Erabilera:  mysql -u root -p < database/schema.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS ecommerce
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce;
SET NAMES utf8mb4;

-- ---------- ERABILTZAILEAK ----------
CREATE TABLE IF NOT EXISTS erabiltzaileak (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    izena          VARCHAR(100) NOT NULL,
    email          VARCHAR(150) NOT NULL UNIQUE,
    pasahitza_hash VARCHAR(255) NOT NULL,
    rola           ENUM('bezeroa','admin') NOT NULL DEFAULT 'bezeroa',
    sortze_data    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- KATEGORIAK ----------
CREATE TABLE IF NOT EXISTS kategoriak (
    id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    izena VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------- PRODUKTUAK (1 kategoria -> N produktu) ----------
CREATE TABLE IF NOT EXISTS produktuak (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    izena         VARCHAR(150) NOT NULL,
    deskribapena  TEXT NULL,
    prezioa       DECIMAL(10,2) NOT NULL CHECK (prezioa > 0),
    stock         INT UNSIGNED NOT NULL DEFAULT 0,
    kategoria_id  INT UNSIGNED NULL,
    irudia        VARCHAR(255) NULL,
    CONSTRAINT fk_prod_kat FOREIGN KEY (kategoria_id)
        REFERENCES kategoriak(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_prod_kat (kategoria_id)
) ENGINE=InnoDB;

-- ---------- ESKAERAK (1 erabiltzaile -> N eskaera) ----------
CREATE TABLE IF NOT EXISTS eskaerak (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    erabiltzailea_id INT UNSIGNED NOT NULL,
    data             TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    guztira          DECIMAL(10,2) NOT NULL,
    egoera           ENUM('berria','prozesatzen','bidalita','entregatuta','bertan_behera') NOT NULL DEFAULT 'berria',
    CONSTRAINT fk_esk_erab FOREIGN KEY (erabiltzailea_id)
        REFERENCES erabiltzaileak(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_esk_erab (erabiltzailea_id)
) ENGINE=InnoDB;

-- ---------- ESKAERA_LERROAK (eskaera N <-> N produktua) ----------
CREATE TABLE IF NOT EXISTS eskaera_lerroak (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    eskaera_id        INT UNSIGNED NOT NULL,
    produktua_id      INT UNSIGNED NOT NULL,
    kopurua           INT UNSIGNED NOT NULL CHECK (kopurua > 0),
    prezioa_unitarioa DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_lerro_esk FOREIGN KEY (eskaera_id)
        REFERENCES eskaerak(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_lerro_prod FOREIGN KEY (produktua_id)
        REFERENCES produktuak(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_lerro_esk (eskaera_id),
    INDEX idx_lerro_prod (produktua_id)
) ENGINE=InnoDB;

-- ---------- KONTU DIGITALAK (eskaera bakoitzeko kredentzial bakarrak) ----------
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

-- =====================================================================
-- PROBA-DATUAK
-- Admin: admin@denda.eus  /  pasahitza: admin123
-- =====================================================================
INSERT INTO erabiltzaileak (izena, email, pasahitza_hash, rola) VALUES
('Administratzailea', 'admin@denda.eus',
 '$2y$10$/LDmj3/qjGsKHqJjVQzPLOp2srxJOGreteic/yX00wXp93Ic3qAnK', 'admin');

INSERT INTO kategoriak (izena) VALUES ('Productos'), ('Boosting'), ('Cuentas');

INSERT INTO produktuak (izena, deskribapena, prezioa, stock, kategoria_id, irudia) VALUES
('Pokémonedas · 100', 'Pack digital de 100 Pokémonedas.', 0.49, 9999, 1, 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'),
('Pokémonedas · 550', 'Pack digital de 550 Pokémonedas.', 2.49, 9999, 1, 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'),
('Pokémonedas · 1.200', 'Pack digital de 1.200 Pokémonedas.', 5.50, 9999, 1, 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'),
('Pokémonedas · 2.500', 'Pack digital de 2.500 Pokémonedas.', 11.49, 9999, 1, 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'),
('Valorant Points · 475', 'Pack digital de 475 Valorant Points.', 2.50, 9999, 1, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'),
('Valorant Points · 1.000', 'Pack digital de 1.000 Valorant Points.', 5.49, 9999, 1, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'),
('Valorant Points · 2.050', 'Pack digital de 2.050 Valorant Points.', 10.50, 9999, 1, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'),
('Valorant Points · 3.650', 'Pack digital de 3.650 Valorant Points.', 18.49, 9999, 1, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'),
('Pavos Fortnite · 1.000', 'Pack digital de 1.000 pavos.', 5.50, 9999, 1, 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?auto=format&fit=crop&w=900&q=85'),
('Pavos Fortnite · 2.800', 'Pack digital de 2.800 pavos.', 14.49, 9999, 1, 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?auto=format&fit=crop&w=900&q=85'),
('Pavos Fortnite · 5.000', 'Pack digital de 5.000 pavos.', 24.50, 9999, 1, 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?auto=format&fit=crop&w=900&q=85'),
('Pavos Fortnite · 13.500', 'Pack digital de 13.500 pavos.', 62.49, 9999, 1, 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?auto=format&fit=crop&w=900&q=85'),
('Monedas FIFA · 100.000', 'Pack de 100.000 monedas para FIFA / EA SPORTS FC.', 4.50, 9999, 1, 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'),
('Monedas FIFA · 500.000', 'Pack de 500.000 monedas para FIFA / EA SPORTS FC.', 19.49, 9999, 1, 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'),
('Monedas FIFA · 1.000.000', 'Pack de 1.000.000 monedas para FIFA / EA SPORTS FC.', 37.50, 9999, 1, 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'),
('FIFA Points · 1.050', 'Pack digital de 1.050 FIFA Points.', 6.49, 9999, 1, 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'),
('FIFA Points · 2.800', 'Pack digital de 2.800 FIFA Points.', 14.50, 9999, 1, 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'),
('FIFA Points · 5.900', 'Pack digital de 5.900 FIFA Points.', 29.49, 9999, 1, 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'),
('FUT Champions · 15-0', 'Servicio de boosting para conseguir un resultado de 15-0 en FUT Champions.', 24.50, 9999, 2, 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'),
('Pokémon GO · 100.000 polvos estelares', 'Pack de boosting para conseguir 100.000 polvos estelares.', 2.49, 9999, 2, 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'),
('Pokémon GO · 500.000 polvos estelares', 'Pack de boosting para conseguir 500.000 polvos estelares.', 8.50, 9999, 2, 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'),
('Pokémon GO · 1.000.000 polvos estelares', 'Pack de boosting para conseguir 1.000.000 de polvos estelares.', 15.49, 9999, 2, 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'),
('Pokémon GO · 1.000.000 XP', 'Pack de boosting para conseguir 1.000.000 XP.', 3.50, 9999, 2, 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'),
('Pokémon GO · 5.000.000 XP', 'Pack de boosting para conseguir 5.000.000 XP.', 14.49, 9999, 2, 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'),
('Pokémon GO · 10.000.000 XP', 'Pack de boosting para conseguir 10.000.000 XP.', 27.50, 9999, 2, 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'),
('Fortnite · Rango Unreal', 'Servicio de boosting hasta el rango Unreal en Fortnite.', 39.49, 9999, 2, 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?auto=format&fit=crop&w=900&q=85'),
('Valorant · Rango Hierro', 'Servicio de boosting hasta el rango Hierro.', 3.50, 9999, 2, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'),
('Valorant · Rango Bronce', 'Servicio de boosting hasta el rango Bronce.', 6.49, 9999, 2, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'),
('Valorant · Rango Plata', 'Servicio de boosting hasta el rango Plata.', 10.50, 9999, 2, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'),
('Valorant · Rango Oro', 'Servicio de boosting hasta el rango Oro.', 15.49, 9999, 2, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'),
('Valorant · Rango Platino', 'Servicio de boosting hasta el rango Platino.', 24.50, 9999, 2, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'),
('Valorant · Rango Diamante', 'Servicio de boosting hasta el rango Diamante.', 34.49, 9999, 2, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'),
('Valorant · Rango Ascendente', 'Servicio de boosting hasta el rango Ascendente.', 51.50, 9999, 2, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'),
('Valorant · Rango Inmortal', 'Servicio de boosting hasta el rango Inmortal.', 79.49, 9999, 2, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'),
('Valorant · Rango Radiante', 'Servicio de boosting hasta el rango Radiante.', 129.50, 9999, 2, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'),
('Netflix · Cuenta lifetime', 'Cuenta digital lifetime de Netflix.', 79.99, 9999, 3, 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=900&q=85'),
('Prime Video · Cuenta lifetime', 'Cuenta digital lifetime de Prime Video.', 69.99, 9999, 3, 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?auto=format&fit=crop&w=900&q=85'),
('Disney+ · Cuenta lifetime', 'Cuenta digital lifetime de Disney+.', 79.99, 9999, 3, 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&w=900&q=85'),
('HBO · Cuenta lifetime', 'Cuenta digital lifetime de HBO.', 69.99, 9999, 3, 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?auto=format&fit=crop&w=900&q=85'),
('Crunchyroll · Cuenta lifetime', 'Cuenta digital lifetime de Crunchyroll.', 49.99, 9999, 3, 'https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=900&q=85'),
('Spotify · Cuenta lifetime', 'Cuenta digital lifetime de Spotify.', 59.99, 9999, 3, 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=900&q=85'),
('YouTube Premium · Cuenta lifetime', 'Cuenta digital lifetime de YouTube Premium.', 79.99, 9999, 3, 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=900&q=85'),
('Pokémon GO · Cuenta nivel 80', 'Cuenta de Pokémon GO de nivel 80.', 12.49, 9999, 3, 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'),
('Fortnite · Cuenta con +50 skins', 'Cuenta de Fortnite con más de 50 skins.', 24.50, 9999, 3, 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?auto=format&fit=crop&w=900&q=85'),
('Valorant · Cuenta con +20 skins', 'Cuenta de Valorant con más de 20 skins.', 19.49, 9999, 3, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85');

UPDATE produktuak p
JOIN kategoriak k ON k.id = p.kategoria_id
SET p.izena = REPLACE(REPLACE(p.izena, ' · ', ' - '), 'Pokémon', 'Pokemon'),
    p.deskribapena = REPLACE(p.deskribapena, 'Pokémon', 'Pokemon'),
    p.irudia = CASE
        WHEN p.izena LIKE 'Pok%' THEN 'images/logos/pokemon-go.svg'
        WHEN p.izena LIKE 'Valorant%' THEN 'images/logos/valorant.svg'
        WHEN p.izena LIKE 'Pavos Fortnite%' OR p.izena LIKE 'Fortnite%' THEN 'images/logos/fortnite.svg'
        WHEN p.izena LIKE 'Monedas FIFA%' OR p.izena LIKE 'FIFA Points%' OR p.izena LIKE 'FUT Champions%' THEN 'images/logos/ea-sports-fc.svg'
        WHEN p.izena LIKE 'Netflix%' THEN 'images/logos/netflix.svg'
        WHEN p.izena LIKE 'Prime Video%' THEN 'images/logos/prime-video.svg'
        WHEN p.izena LIKE 'Disney+%' THEN 'images/logos/disney-plus.svg'
        WHEN p.izena LIKE 'HBO%' THEN 'images/logos/hbo.svg'
        WHEN p.izena LIKE 'Crunchyroll%' THEN 'images/logos/crunchyroll.svg'
        WHEN p.izena LIKE 'Spotify%' THEN 'images/logos/spotify.svg'
        WHEN p.izena LIKE 'YouTube Premium%' THEN 'images/logos/youtube-premium.svg'
        ELSE p.irudia
    END
WHERE k.izena IN ('Productos', 'Boosting', 'Cuentas');

UPDATE produktuak p
JOIN kategoriak k ON k.id = p.kategoria_id
SET p.stock = 0
WHERE k.izena = 'Cuentas';

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
