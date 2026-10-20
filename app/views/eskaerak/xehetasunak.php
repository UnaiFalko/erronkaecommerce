<a href="index.php?c=eskaera">&larr; Pedidos</a>
<h1>Pedido #<?= (int) $e['id'] ?></h1>
<p class="muted"><?= e($e['data']) ?> · Estado: <strong><?= e($e['egoera']) ?></strong></p>

<table class="table">
    <thead><tr><th>Servicio</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
    <tbody>
    <?php foreach ($lerroak as $l): ?>
        <tr>
            <td><?= e($l['izena']) ?></td>
            <td><?= prezioa((float) $l['prezioa_unitarioa']) ?></td>
            <td><?= (int) $l['kopurua'] ?></td>
            <td><?= prezioa((float) $l['prezioa_unitarioa'] * $l['kopurua']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot><tr><td colspan="3" class="right"><strong>Total</strong></td>
        <td><strong><?= prezioa((float) $e['guztira']) ?></strong></td></tr></tfoot>
</table>
