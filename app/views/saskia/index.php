<div class="cart-heading">
    <div>
        <p class="eyebrow">Tu compra</p>
        <h1>Carrito</h1>
    </div>
    <a class="btn secondary small" href="index.php">Seguir comprando</a>
</div>

<?php if (!$lerroak): ?>
    <div class="empty-catalog cart-empty">
        <strong>Tu carrito está vacío.</strong>
        <p class="muted">Explora el catálogo y añade los servicios que quieras comprar.</p>
        <a class="btn" href="index.php">Explorar catálogo</a>
    </div>
<?php else: ?>
<div class="cart-toolbar">
    <span class="muted"><?= count($lerroak) ?> <?= count($lerroak) === 1 ? 'producto' : 'productos' ?></span>
    <form method="post" action="index.php?c=saskia&amp;a=vaciar">
        <?= csrfField() ?>
        <button class="btn secondary small" type="submit">Vaciar carrito</button>
    </form>
</div>
<form method="post" action="index.php?c=saskia&amp;a=eguneratu">
    <?= csrfField() ?>
    <table class="table">
        <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($lerroak as $l): ?>
            <tr>
                <td><?= e($l['izena']) ?></td>
                <td><?= prezioa((float) $l['prezioa']) ?></td>
                <td><input type="number" name="kopurua[<?= (int) $l['id'] ?>]" value="<?= (int) $l['kopurua'] ?>"
                           min="0" max="<?= (int) $l['stock'] ?>" class="qty"></td>
                <td><?= prezioa((float) $l['subtotala']) ?></td>
                <td>
                    <button class="btn small danger" formmethod="post"
                            formaction="index.php?c=saskia&amp;a=kendu"
                            name="id" value="<?= (int) $l['id'] ?>">Quitar</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot><tr><td colspan="3" class="right"><strong>Total</strong></td>
            <td colspan="2"><strong><?= prezioa($guztira) ?></strong></td></tr></tfoot>
    </table>
    <button class="btn secondary" type="submit">Actualizar cantidades</button>
</form>

<form method="post" action="index.php?c=eskaera&amp;a=burutu" class="right">
    <?= csrfField() ?>
    <button class="btn big-btn" type="submit">Finalizar compra</button>
</form>
<?php endif; ?>
