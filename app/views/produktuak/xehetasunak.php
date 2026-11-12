<!-- Información, disponibilidad y acciones de compra de un producto. -->
<?php
$enCarrito = (int) ($_SESSION['saskia'][(int) $p['id']] ?? 0);
$stockDisponible = max(0, (int) $p['stock'] - $enCarrito);
$esFavorito = in_array((int) $p['id'], array_map('intval', $_SESSION['favoritos'] ?? []), true);
?>
<a class="back-link" href="index.php">&larr; <?= tr('Volver al catálogo', 'Katalogora itzuli') ?></a>
<div class="detail">
    <?php if ($p['irudia']): ?>
        <img src="<?= e($p['irudia']) ?>" alt="<?= e(localizedProductName($p['izena'])) ?>">
    <?php else: ?>
        <div class="noimg big"><?= tr('Imagen no disponible', 'Irudia ez dago erabilgarri') ?></div>
    <?php endif; ?>
    <div class="detail-info">
        <span class="tag"><?= e(categoryLabel($p['kategoria'] ?? null)) ?></span>
        <h1><?= e(localizedProductName($p['izena'])) ?></h1>
        <p class="price big"><?= prezioa((float) $p['prezioa']) ?></p>
        <p class="detail-description"><?= nl2br(e(localizedProductDescription($p['deskribapena']))) ?></p>
        <p class="detail-stock <?= (int) $p['stock'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
            <?= (int) $p['stock'] > 0 ? tr('Stock disponible: ', 'Stock erabilgarria: ') . (int) $p['stock'] . tr(' unidades', ' unitate') : tr('Agotado', 'Agortuta') ?>
        </p>
        <?php if ($stockDisponible > 0): ?>
            <form method="post" action="index.php?c=saskia&amp;a=gehitu" class="detail-add">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                <input type="hidden" name="jatorria" value="xehetasuna">
                <label for="product-quantity"><?= tr('Cantidad (máximo ', 'Kopurua (gehienez ') . $stockDisponible . ')' ?></label>
                <div class="detail-actions">
                    <input id="product-quantity" type="number" name="kopurua" value="1" min="1" max="<?= $stockDisponible ?>" step="1" required>
                    <button class="btn" type="submit"><?= tr('Añadir al carrito', 'Saskira gehitu') ?></button>
                </div>
            </form>
        <?php elseif ((int) $p['stock'] > 0): ?>
            <p class="muted"><?= tr('Ya tienes todas las unidades disponibles en el carrito.', 'Dagoeneko eskuragarri dauden unitate guztiak saskian dituzu.') ?></p>
        <?php endif; ?>
        <form method="post" action="index.php?c=home&amp;a=favorito" class="detail-favorite">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <input type="hidden" name="detalle" value="1">
            <input type="hidden" name="detalle_id" value="<?= (int) $p['id'] ?>">
            <button class="btn secondary" type="submit"><?= $esFavorito ? tr('★ Quitar de favoritos', '★ Gogokoetatik kendu') : tr('☆ Guardar en favoritos', '☆ Gogokoetan gorde') ?></button>
        </form>
    </div>
</div>
