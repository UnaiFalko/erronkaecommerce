-- Añade descuento y destacado sin borrar los productos existentes.
-- Para deshacer el cambio, elimina las dos columnas indicadas al final.
USE ecommerce;

ALTER TABLE produktuak
    ADD COLUMN deskontua  TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER stock,
    ADD COLUMN nabarmendua BOOLEAN NOT NULL DEFAULT FALSE AFTER deskontua;

-- Datu zaharrek balio lehenetsiak hartzen dituzte (0 eta FALSE).
-- Para deshacer el cambio:
--   ALTER TABLE produktuak DROP COLUMN nabarmendua, DROP COLUMN deskontua;
