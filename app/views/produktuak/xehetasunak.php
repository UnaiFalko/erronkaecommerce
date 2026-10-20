    <a class="back-link" href="index.php">&larr; Volver al catálogo</a>
<div class="detail">
    <?php if ($p['irudia']): ?>
        <img class="brand-logo" src="<?= e($p['irudia']) ?>" alt="Logo de <?= e($p['izena']) ?>">
    <?php else: ?>
        <div class="noimg big">Imagen no disponible</div>
    <?php endif; ?>
    <div class="detail-info">
        <span class="tag"><?= e($p['kategoria'] ?? 'Sin categoría') ?></span>
        <h1><?= e($p['izena']) ?></h1>
        <p class="price big"><?= prezioa((float) $p['prezioa']) ?></p>
        <p class="detail-description"><?= nl2br(e($p['deskribapena'])) ?></p>
        <p class="detail-stock <?= $p['stock'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
            <?= $p['stock'] > 0 ? 'Disponible · ' . (int) $p['stock'] . ' en stock' : 'No disponible' ?>
        </p>
        <?php if ($p['stock'] > 0): ?>
            <form method="post" action="index.php?c=saskia&amp;a=gehitu" class="detail-add">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                <label for="product-quantity">Cantidad</label>
                <div class="detail-actions">
                    <input id="product-quantity" type="number" name="kopurua" value="1" min="1" max="<?= (int) $p['stock'] ?>">
                    <button class="btn" type="submit">Añadir al carrito</button>
                </div>
            </form>
        <?php endif; ?>
        <form method="post" action="index.php?c=home&amp;a=favorito" class="detail-favorite">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <input type="hidden" name="detalle" value="1">
            <input type="hidden" name="detalle_id" value="<?= (int) $p['id'] ?>">
            <?php $esFavorito = in_array((int) $p['id'], array_map('intval', $_SESSION['favoritos'] ?? []), true); ?>
            <button class="btn secondary" type="submit"><?= $esFavorito ? '★ Quitar de favoritos' : '☆ Guardar en favoritos' ?></button>
        </form>
    </div>
</div>
