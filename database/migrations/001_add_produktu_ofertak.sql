-- =====================================================================
-- 3.4.2: Diseinu-aldaketa datuak galdu gabe (migrazioa)
-- Behar berria: produktuei deskontua eta "nabarmendua" markatzeko aukera.
-- ALTER TABLE erabiltzen da; lehendik dauden errenkadak ez dira ukitzen.
-- =====================================================================
USE ecommerce;

ALTER TABLE produktuak
    ADD COLUMN deskontua  TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER stock,
    ADD COLUMN nabarmendua BOOLEAN NOT NULL DEFAULT FALSE AFTER deskontua;

-- Datu zaharrek balio lehenetsiak hartzen dituzte (0 eta FALSE).
-- Itzultzeko (rollback):
--   ALTER TABLE produktuak DROP COLUMN nabarmendua, DROP COLUMN deskontua;
