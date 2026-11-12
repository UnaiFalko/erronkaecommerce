# WinaDenda · Servicios digitales (PHP + MVC + PDO)

## Instalazioa
1. Sortu datu-basea eta datuak: mysql -u root -p < database/schema.sql
2. Editatu config/config.php (edo ingurune-aldagaiak DB_HOST, DB_NAME, DB_USER, DB_PASS).
3. Abiarazi zerbitzaria: php -S localhost:8000 -t public
4. Ireki http://localhost:8000

Lehendik dagoen datu-base batera katalogo berria gehitzeko, exekutatu
mysql -u root -p ecommerce < database/migrations/003_winadenda_catalog.sql.
Migrazioak aurreko kategoriak katalogotik ezkutatzen ditu, baina produktuak eta
eskaera-historiala mantentzen ditu.
Katalogoko joko-produktuen prezio berriak aplikatzeko, exekutatu
mysql -u root -p ecommerce < database/migrations/006_catalog_prices.sql.
Streaming-kontuen prezioak ez dira migrazio horretan aldatzen.
Hasierako instalazioetan prezio horiek schema.sql eta
003_winadenda_catalog.sql fitxategietan ere sartuta daude; migrazioak lehendik
dagoen katalogo bati aplikatzen dizkio.

Lehendik instalatutako datu-base bati katalogoko irudiak gehitzeko, inportatu
database/migrations/002_add_catalog_images.sql phpMyAdmin bidez.

## Cuentas digitales
Para actualizar una instalación existente, ejecuta desde la raíz del proyecto:
mysql -u root -p ecommerce < database/migrations/007_digital_accounts.sql
mysql -u root -p ecommerce < database/migrations/008_demo_account_stock.sql.

Las credenciales se guardan cifradas con AES-256-GCM. No hace falta activar
Sodium: las instalaciones de PHP de XAMPP incluyen OpenSSL. Si no defines
APP_ENCRYPTION_KEY, la aplicación crea una clave local en
config/.encryption-key al iniciar. Conserva ese archivo: si se pierde, no se
podrán recuperar las cuentas ya guardadas.

En instalaciones nuevas, `database/schema.sql` ya deja preparadas tres
cuentas ficticias por producto de la categoría Cuentas. En el primer arranque,
la aplicación las cifra con la clave propia de esa instalación y actualiza el
stock. Son credenciales de demostración y no permiten iniciar sesión en
servicios reales.

*Admin:* admin@denda.eus / admin123 · Bezeroak: orrian bertan erregistratu.

## Egitura

config/config.php        DB konfigurazioa
app/core/                bootstrap (autoload, saioa, erroreak), Router, Controller, Database (PDO)
app/models/              Model, Kategoria, Produktua, Erabiltzailea, Eskaera   (OOP)
app/controllers/         Home, Auth, Produktua (CRUD), Erabiltzailea (CRUD bakarrik adminarentzat), Saskia, Eskaera
app/views/               HTML bistak (logikarik gabe) + layout
public/                  index.php (front controller) + css/
database/                schema.sql, migrations/, ER-eredua.md


## Errubrikaren estaldura
| Puntua | Non dago |
|---|---|
| 1.1.3 PHP / HTML-CSS bereizketa | app/views (HTML) vs app/controllers+models (PHP logika); estiloak public/css |
| 2.2.3 MVC arkitektura | app/models, app/views, app/controllers, Router |
| 2.4.1 PHP + MySQL konexioa | config/config.php, app/core/Database.php (PDO, prepared statements, utf8mb4) |
| 1.3.1 OOP | Model oinarri-klasea, Kategoria, Produktua, Eskaera... herentzia, enkapsulazioa, instantziak |
| 1.4.1 Akatsen kudeaketa | bootstrap.php: errore orokor seguruak; try/catch + transakzioak |
| 2.2.1 Frontend-Backend | $_SESSION bidezko login/logout, rolak, CSRF token, bistek kontrolatzaileak emandako datuak bakarrik erakusten dituzte |
| 3.1.2 DB erlazionala | MySQL InnoDB, justifikazioa database/ER-eredua.md-n (FK, transakzioak) |
| 3.2.2 CRUD | ProduktuaController + Produktua (INSERT/SELECT/UPDATE/DELETE) |
| 3.3.1 E-R eredua | database/ER-eredua.md |
| 3.3.2 Diseinua inplementatu | database/schema.sql (CREATE TABLE) |
| 3.4.2 Diseinu-aldaketa | database/migrations/001_...sql (ALTER TABLE, daturik galdu gabe) |

## Segurtasuna
password_hash/password_verify, PDO prepared statements, htmlspecialchars (XSS), CSRF tokenak, session_regenerate_id, rol-kontrola adminerako.
| 3.1.2 DB erlazionala | MySQL InnoDB, justifikazioa database/ER-eredua.md-n (FK, transakzioak) |
| 3.2.2 CRUD | ProduktuaController + Produktua (INSERT/SELECT/UPDATE/DELETE) |
| 3.3.1 E-R eredua | database/ER-eredua.md |
| 3.3.2 Diseinua inplementatu | database/schema.sql (CREATE TABLE) |
| 3.4.2 Diseinu-aldaketa | database/migrations/001_...sql (ALTER TABLE, daturik galdu gabe) |

## Segurtasuna
password_hash/password_verify, PDO prepared statements, htmlspecialchars (XSS), CSRF tokenak, session_regenerate_id, rol-kontrola adminerako.
password_hash/password_verify, PDO prepared statements, htmlspecialchars (XSS), CSRF tokenak, session_regenerate_id, rol-kontrola adminerako.
## Segurtasuna
password_hash/password_verify, PDO prepared statements, htmlspecialchars (XSS), CSRF tokenak, session_regenerate_id, rol-kontrola adminerako.