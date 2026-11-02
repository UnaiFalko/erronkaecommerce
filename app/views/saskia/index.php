<div class="cart-heading">
    <div>
        <p class="eyebrow"><?= tr('Tu compra', 'Zure erosketa') ?></p>
        <h1><?= tr('Carrito', 'Saskia') ?></h1>
    </div>
    <a class="btn secondary small" href="index.php"><?= tr('Seguir comprando', 'Erosten jarraitu') ?></a>
</div>

<?php if (!$lerroak): ?>
    <div class="empty-catalog cart-empty">
        <strong><?= tr('Tu carrito está vacío.', 'Zure saskia hutsik dago.') ?></strong>
        <p class="muted"><?= tr('Explora el catálogo y añade los servicios que quieras comprar.', 'Arakatu katalogoa eta gehitu erosi nahi dituzun zerbitzuak.') ?></p>
        <a class="btn" href="index.php"><?= tr('Explorar catálogo', 'Katalogoa arakatu') ?></a>
    </div>
<?php else: ?>
<div class="cart-toolbar">
    <span class="muted"><?= count($lerroak) ?> <?= count($lerroak) === 1 ? tr('producto', 'produktu') : tr('productos', 'produktu') ?></span>
    <form method="post" action="index.php?c=saskia&amp;a=vaciar">
        <?= csrfField() ?>
        <button class="btn secondary small" type="submit"><?= tr('Vaciar carrito', 'Saskia hustu') ?></button>
    </form>
</div>
<form method="post" action="index.php?c=saskia&amp;a=eguneratu" data-cart>
    <?= csrfField() ?>
    <table class="table">
        <thead><tr><th><?= tr('Producto', 'Produktua') ?></th><th><?= tr('Precio', 'Prezioa') ?></th><th><?= tr('Cantidad', 'Kopurua') ?></th><th><?= tr('Subtotal', 'Azpitotala') ?></th><th></th></tr></thead>
        <tbody>
        <?php foreach ($lerroak as $l): ?>
            <tr>
                <td>
                    <?= e(localizedProductName($l['izena'])) ?>
                    <span class="cart-stock"><?= tr('Stock', 'Stock-a') ?>: <?= (int) $l['stock'] ?></span>
                    <?php if ($l['stockNahikorikEz']): ?>
                        <span class="stock-warning"><?= tr('Stock insuficiente; reduce la cantidad o quita el producto.', 'Stock nahikorik ez; murriztu kopurua edo kendu produktua.') ?></span>
                    <?php endif; ?>
                </td>
                <td><?= prezioa((float) $l['prezioa']) ?></td>
                <td><input type="number" name="kopurua[<?= (int) $l['id'] ?>]" value="<?= (int) $l['kopurua'] ?>"
                           min="0" max="<?= (int) $l['stock'] ?>" step="1" class="qty" data-cart-quantity
                           aria-label="<?= tr('Cantidad de ', 'Kopurua: ') . e(localizedProductName($l['izena'])) ?>"></td>
                <td data-unit-price="<?= number_format((float) $l['prezioa'], 2, '.', '') ?>">
                    <span data-line-total><?= prezioa((float) $l['subtotala']) ?></span>
                </td>
                <td>
                    <button class="btn small danger" formmethod="post"
                            formaction="index.php?c=saskia&amp;a=kendu"
                            name="id" value="<?= (int) $l['id'] ?>"><?= tr('Quitar', 'Kendu') ?></button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot><tr><td colspan="3" class="right"><strong><?= tr('Total', 'Guztira') ?></strong></td>
            <td colspan="2"><strong data-cart-total><?= prezioa($guztira) ?></strong></td></tr></tfoot>
    </table>
    <button class="btn big-btn" type="submit" formaction="index.php?c=eskaera&amp;a=burutu" <?= $stockNahikorikEz ? 'disabled' : '' ?>><?= tr('Finalizar compra', 'Erosketa amaitu') ?></button>
</form>
<?php endif; ?>
<script src="js/carrito.js" defer></script>
