-- Actualiza los precios del catálogo y confirma los cambios en una transacción.
USE ecommerce;
SET NAMES utf8mb4;
START TRANSACTION;

UPDATE produktuak
SET prezioa = CASE
    WHEN izena IN ('Pokémonedas · 100', 'Pokemonedas - 100') THEN 0.49
    WHEN izena IN ('Pokémonedas · 550', 'Pokemonedas - 550') THEN 2.49
    WHEN izena IN ('Pokémonedas · 1.200', 'Pokemonedas - 1.200') THEN 5.50
    WHEN izena IN ('Pokémonedas · 2.500', 'Pokemonedas - 2.500') THEN 11.49
    WHEN izena IN ('Valorant Points · 475', 'Valorant Points - 475') THEN 2.50
    WHEN izena IN ('Valorant Points · 1.000', 'Valorant Points - 1.000') THEN 5.49
    WHEN izena IN ('Valorant Points · 2.050', 'Valorant Points - 2.050') THEN 10.50
    WHEN izena IN ('Valorant Points · 3.650', 'Valorant Points - 3.650') THEN 18.49
    WHEN izena IN ('Pavos Fortnite · 1.000', 'Pavos Fortnite - 1.000') THEN 5.50
    WHEN izena IN ('Pavos Fortnite · 2.800', 'Pavos Fortnite - 2.800') THEN 14.49
    WHEN izena IN ('Pavos Fortnite · 5.000', 'Pavos Fortnite - 5.000') THEN 24.50
    WHEN izena IN ('Pavos Fortnite · 13.500', 'Pavos Fortnite - 13.500') THEN 62.49
    WHEN izena IN ('Monedas FIFA · 100.000', 'Monedas FIFA - 100.000') THEN 4.50
    WHEN izena IN ('Monedas FIFA · 500.000', 'Monedas FIFA - 500.000') THEN 19.49
    WHEN izena IN ('Monedas FIFA · 1.000.000', 'Monedas FIFA - 1.000.000') THEN 37.50
    WHEN izena IN ('FIFA Points · 1.050', 'FIFA Points - 1.050') THEN 6.49
    WHEN izena IN ('FIFA Points · 2.800', 'FIFA Points - 2.800') THEN 14.50
    WHEN izena IN ('FIFA Points · 5.900', 'FIFA Points - 5.900') THEN 29.49
    WHEN izena IN ('FUT Champions · 15-0', 'FUT Champions - 15-0') THEN 24.50
    WHEN izena IN ('Pokémon GO · 100.000 polvos estelares', 'Pokemon GO - 100.000 polvos estelares') THEN 2.49
    WHEN izena IN ('Pokémon GO · 500.000 polvos estelares', 'Pokemon GO - 500.000 polvos estelares') THEN 8.50
    WHEN izena IN ('Pokémon GO · 1.000.000 polvos estelares', 'Pokemon GO - 1.000.000 polvos estelares') THEN 15.49
    WHEN izena IN ('Pokémon GO · 1.000.000 XP', 'Pokemon GO - 1.000.000 XP') THEN 3.50
    WHEN izena IN ('Pokémon GO · 5.000.000 XP', 'Pokemon GO - 5.000.000 XP') THEN 14.49
    WHEN izena IN ('Pokémon GO · 10.000.000 XP', 'Pokemon GO - 10.000.000 XP') THEN 27.50
    WHEN izena IN ('Fortnite · Rango Unreal', 'Fortnite - Rango Unreal') THEN 39.49
    WHEN izena IN ('Valorant · Rango Hierro', 'Valorant - Rango Hierro') THEN 3.50
    WHEN izena IN ('Valorant · Rango Bronce', 'Valorant - Rango Bronce') THEN 6.49
    WHEN izena IN ('Valorant · Rango Plata', 'Valorant - Rango Plata') THEN 10.50
    WHEN izena IN ('Valorant · Rango Oro', 'Valorant - Rango Oro') THEN 15.49
    WHEN izena IN ('Valorant · Rango Platino', 'Valorant - Rango Platino') THEN 24.50
    WHEN izena IN ('Valorant · Rango Diamante', 'Valorant - Rango Diamante') THEN 34.49
    WHEN izena IN ('Valorant · Rango Ascendente', 'Valorant - Rango Ascendente') THEN 51.50
    WHEN izena IN ('Valorant · Rango Inmortal', 'Valorant - Rango Inmortal') THEN 79.49
    WHEN izena IN ('Valorant · Rango Radiante', 'Valorant - Rango Radiante') THEN 129.50
    WHEN izena IN ('Pokémon GO · Cuenta nivel 80', 'Pokemon GO - Cuenta nivel 80') THEN 12.49
    WHEN izena IN ('Fortnite · Cuenta con +50 skins', 'Fortnite - Cuenta con +50 skins') THEN 24.50
    WHEN izena IN ('Valorant · Cuenta con +20 skins', 'Valorant - Cuenta con +20 skins') THEN 19.49
    ELSE prezioa
END
WHERE izena IN (
    'Pokémonedas · 100', 'Pokemonedas - 100',
    'Pokémonedas · 550', 'Pokemonedas - 550',
    'Pokémonedas · 1.200', 'Pokemonedas - 1.200',
    'Pokémonedas · 2.500', 'Pokemonedas - 2.500',
    'Valorant Points · 475', 'Valorant Points - 475',
    'Valorant Points · 1.000', 'Valorant Points - 1.000',
    'Valorant Points · 2.050', 'Valorant Points - 2.050',
    'Valorant Points · 3.650', 'Valorant Points - 3.650',
    'Pavos Fortnite · 1.000', 'Pavos Fortnite - 1.000',
    'Pavos Fortnite · 2.800', 'Pavos Fortnite - 2.800',
    'Pavos Fortnite · 5.000', 'Pavos Fortnite - 5.000',
    'Pavos Fortnite · 13.500', 'Pavos Fortnite - 13.500',
    'Monedas FIFA · 100.000', 'Monedas FIFA - 100.000',
    'Monedas FIFA · 500.000', 'Monedas FIFA - 500.000',
    'Monedas FIFA · 1.000.000', 'Monedas FIFA - 1.000.000',
    'FIFA Points · 1.050', 'FIFA Points - 1.050',
    'FIFA Points · 2.800', 'FIFA Points - 2.800',
    'FIFA Points · 5.900', 'FIFA Points - 5.900',
    'FUT Champions · 15-0', 'FUT Champions - 15-0',
    'Pokémon GO · 100.000 polvos estelares', 'Pokemon GO - 100.000 polvos estelares',
    'Pokémon GO · 500.000 polvos estelares', 'Pokemon GO - 500.000 polvos estelares',
    'Pokémon GO · 1.000.000 polvos estelares', 'Pokemon GO - 1.000.000 polvos estelares',
    'Pokémon GO · 1.000.000 XP', 'Pokemon GO - 1.000.000 XP',
    'Pokémon GO · 5.000.000 XP', 'Pokemon GO - 5.000.000 XP',
    'Pokémon GO · 10.000.000 XP', 'Pokemon GO - 10.000.000 XP',
    'Fortnite · Rango Unreal', 'Fortnite - Rango Unreal',
    'Valorant · Rango Hierro', 'Valorant - Rango Hierro',
    'Valorant · Rango Bronce', 'Valorant - Rango Bronce',
    'Valorant · Rango Plata', 'Valorant - Rango Plata',
    'Valorant · Rango Oro', 'Valorant - Rango Oro',
    'Valorant · Rango Platino', 'Valorant - Rango Platino',
    'Valorant · Rango Diamante', 'Valorant - Rango Diamante',
    'Valorant · Rango Ascendente', 'Valorant - Rango Ascendente',
    'Valorant · Rango Inmortal', 'Valorant - Rango Inmortal',
    'Valorant · Rango Radiante', 'Valorant - Rango Radiante',
    'Pokémon GO · Cuenta nivel 80', 'Pokemon GO - Cuenta nivel 80',
    'Fortnite · Cuenta con +50 skins', 'Fortnite - Cuenta con +50 skins',
    'Valorant · Cuenta con +20 skins', 'Valorant - Cuenta con +20 skins'
);

COMMIT;