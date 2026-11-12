-- Actualiza el catálogo, sus categorías y los datos de demostración de la tienda.
USE ecommerce;

START TRANSACTION;

UPDATE kategoriak
SET izena = CONCAT('Anterior #', id)
WHERE izena NOT IN ('Productos', 'Boosting', 'Cuentas');

INSERT INTO kategoriak (izena) VALUES ('Productos'), ('Boosting'), ('Cuentas')
ON DUPLICATE KEY UPDATE izena = VALUES(izena);

INSERT INTO produktuak (izena, deskribapena, prezioa, stock, kategoria_id, irudia)
SELECT catalogo.izena, catalogo.deskribapena, catalogo.prezioa, 9999, categorias.id, catalogo.irudia
FROM (
    SELECT 'Pokémonedas · 100' AS izena, 'Pack digital de 100 Pokémonedas.' AS deskribapena, 0.49 AS prezioa, 'Productos' AS categoria, 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85' AS irudia
    UNION ALL SELECT 'Pokémonedas · 550', 'Pack digital de 550 Pokémonedas.', 2.49, 'Productos', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Pokémonedas · 1.200', 'Pack digital de 1.200 Pokémonedas.', 5.50, 'Productos', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Pokémonedas · 2.500', 'Pack digital de 2.500 Pokémonedas.', 11.49, 'Productos', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant Points · 475', 'Pack digital de 475 Valorant Points.', 2.50, 'Productos', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant Points · 1.000', 'Pack digital de 1.000 Valorant Points.', 5.49, 'Productos', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant Points · 2.050', 'Pack digital de 2.050 Valorant Points.', 10.50, 'Productos', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant Points · 3.650', 'Pack digital de 3.650 Valorant Points.', 18.49, 'Productos', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Pavos Fortnite · 1.000', 'Pack digital de 1.000 pavos.', 5.50, 'Productos', 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Pavos Fortnite · 2.800', 'Pack digital de 2.800 pavos.', 14.49, 'Productos', 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Pavos Fortnite · 5.000', 'Pack digital de 5.000 pavos.', 24.50, 'Productos', 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Pavos Fortnite · 13.500', 'Pack digital de 13.500 pavos.', 62.49, 'Productos', 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Monedas FIFA · 100.000', 'Pack de 100.000 monedas para FIFA / EA SPORTS FC.', 4.50, 'Productos', 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Monedas FIFA · 500.000', 'Pack de 500.000 monedas para FIFA / EA SPORTS FC.', 19.49, 'Productos', 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Monedas FIFA · 1.000.000', 'Pack de 1.000.000 monedas para FIFA / EA SPORTS FC.', 37.50, 'Productos', 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'FIFA Points · 1.050', 'Pack digital de 1.050 FIFA Points.', 6.49, 'Productos', 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'FIFA Points · 2.800', 'Pack digital de 2.800 FIFA Points.', 14.50, 'Productos', 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'FIFA Points · 5.900', 'Pack digital de 5.900 FIFA Points.', 29.49, 'Productos', 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'FUT Champions · 15-0', 'Servicio de boosting para conseguir un resultado de 15-0 en FUT Champions.', 24.50, 'Boosting', 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Pokémon GO · 100.000 polvos estelares', 'Pack de boosting para conseguir 100.000 polvos estelares.', 2.49, 'Boosting', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Pokémon GO · 500.000 polvos estelares', 'Pack de boosting para conseguir 500.000 polvos estelares.', 8.50, 'Boosting', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Pokémon GO · 1.000.000 polvos estelares', 'Pack de boosting para conseguir 1.000.000 de polvos estelares.', 15.49, 'Boosting', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Pokémon GO · 1.000.000 XP', 'Pack de boosting para conseguir 1.000.000 XP.', 3.50, 'Boosting', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Pokémon GO · 5.000.000 XP', 'Pack de boosting para conseguir 5.000.000 XP.', 14.49, 'Boosting', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Pokémon GO · 10.000.000 XP', 'Pack de boosting para conseguir 10.000.000 XP.', 27.50, 'Boosting', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Fortnite · Rango Unreal', 'Servicio de boosting hasta el rango Unreal en Fortnite.', 39.49, 'Boosting', 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant · Rango Hierro', 'Servicio de boosting hasta el rango Hierro.', 3.50, 'Boosting', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant · Rango Bronce', 'Servicio de boosting hasta el rango Bronce.', 6.49, 'Boosting', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant · Rango Plata', 'Servicio de boosting hasta el rango Plata.', 10.50, 'Boosting', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant · Rango Oro', 'Servicio de boosting hasta el rango Oro.', 15.49, 'Boosting', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant · Rango Platino', 'Servicio de boosting hasta el rango Platino.', 24.50, 'Boosting', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant · Rango Diamante', 'Servicio de boosting hasta el rango Diamante.', 34.49, 'Boosting', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant · Rango Ascendente', 'Servicio de boosting hasta el rango Ascendente.', 51.50, 'Boosting', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant · Rango Inmortal', 'Servicio de boosting hasta el rango Inmortal.', 79.49, 'Boosting', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant · Rango Radiante', 'Servicio de boosting hasta el rango Radiante.', 129.50, 'Boosting', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Netflix · Cuenta lifetime', 'Cuenta digital lifetime de Netflix.', 79.99, 'Cuentas', 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Prime Video · Cuenta lifetime', 'Cuenta digital lifetime de Prime Video.', 69.99, 'Cuentas', 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Disney+ · Cuenta lifetime', 'Cuenta digital lifetime de Disney+.', 79.99, 'Cuentas', 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'HBO · Cuenta lifetime', 'Cuenta digital lifetime de HBO.', 69.99, 'Cuentas', 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Crunchyroll · Cuenta lifetime', 'Cuenta digital lifetime de Crunchyroll.', 49.99, 'Cuentas', 'https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Spotify · Cuenta lifetime', 'Cuenta digital lifetime de Spotify.', 59.99, 'Cuentas', 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'YouTube Premium · Cuenta lifetime', 'Cuenta digital lifetime de YouTube Premium.', 79.99, 'Cuentas', 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Pokémon GO · Cuenta nivel 80', 'Cuenta de Pokémon GO de nivel 80.', 12.49, 'Cuentas', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Fortnite · Cuenta con +50 skins', 'Cuenta de Fortnite con más de 50 skins.', 24.50, 'Cuentas', 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?auto=format&fit=crop&w=900&q=85'
    UNION ALL SELECT 'Valorant · Cuenta con +20 skins', 'Cuenta de Valorant con más de 20 skins.', 19.49, 'Cuentas', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=85'
) AS catalogo
JOIN kategoriak AS categorias ON categorias.izena = catalogo.categoria;

COMMIT;
