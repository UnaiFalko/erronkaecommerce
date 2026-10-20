USE ecommerce;
SET NAMES utf8mb4;

UPDATE produktuak AS p
JOIN kategoriak AS k ON k.id = p.kategoria_id
SET p.izena = CASE
        WHEN p.izena = 'Pokemonedas 100' THEN 'Pokemonedas - 100'
        ELSE REPLACE(
            REPLACE(
                REPLACE(
                    p.izena,
                    CONVERT(0xE2949CC2AE USING utf8mb4) COLLATE utf8mb4_unicode_ci,
                    'e' COLLATE utf8mb4_unicode_ci
                ),
                CONVERT(0xE294ACC380 USING utf8mb4) COLLATE utf8mb4_unicode_ci,
                ' - ' COLLATE utf8mb4_unicode_ci
            ),
            '  -  ' COLLATE utf8mb4_unicode_ci,
            ' - ' COLLATE utf8mb4_unicode_ci
        )
    END,
    p.deskribapena = REPLACE(
        REPLACE(
            p.deskribapena,
            CONVERT(0xE2949CC2AE USING utf8mb4) COLLATE utf8mb4_unicode_ci,
            'e' COLLATE utf8mb4_unicode_ci
        ),
        CONVERT(0xE2949CC3AD USING utf8mb4) COLLATE utf8mb4_unicode_ci,
        'a' COLLATE utf8mb4_unicode_ci
    ),
    p.irudia = CASE
        WHEN p.izena LIKE 'Pok%' THEN 'images/logos/index.php?marca=pokemon-go'
        WHEN p.izena LIKE 'Valorant%' THEN 'images/logos/index.php?marca=valorant'
        WHEN p.izena LIKE 'Pavos Fortnite%' OR p.izena LIKE 'Fortnite%' THEN 'images/logos/index.php?marca=fortnite'
        WHEN p.izena LIKE 'Monedas FIFA%' OR p.izena LIKE 'FIFA Points%' OR p.izena LIKE 'FUT Champions%' THEN 'images/logos/index.php?marca=ea-sports-fc'
        WHEN p.izena LIKE 'Netflix%' THEN 'images/logos/index.php?marca=netflix'
        WHEN p.izena LIKE 'Prime Video%' THEN 'images/logos/index.php?marca=prime-video'
        WHEN p.izena LIKE 'Disney+%' THEN 'images/logos/index.php?marca=disney-plus'
        WHEN p.izena LIKE 'HBO%' THEN 'images/logos/index.php?marca=hbo'
        WHEN p.izena LIKE 'Crunchyroll%' THEN 'images/logos/index.php?marca=crunchyroll'
        WHEN p.izena LIKE 'Spotify%' THEN 'images/logos/index.php?marca=spotify'
        WHEN p.izena LIKE 'YouTube Premium%' THEN 'images/logos/index.php?marca=youtube-premium'
        ELSE p.irudia
    END
WHERE k.izena IN ('Productos', 'Boosting', 'Cuentas');
