<!-- Detalle del pedido, sus importes y las cuentas digitales entregadas. -->
<a href="index.php?c=eskaera">&larr; <?= tr('Pedidos', 'Eskaerak') ?></a>
<h1><?= tr('Pedido', 'Eskaera') ?> #<?= (int) $e['id'] ?></h1>
<p class="muted"><?= e($e['data']) ?> · <?= tr('Estado', 'Egoera') ?>: <strong><?= e(orderStatusLabel($e['egoera'])) ?></strong></p>

<table class="table">
    <thead><tr><th><?= tr('Servicio', 'Zerbitzua') ?></th><th><?= tr('Precio', 'Prezioa') ?></th><th><?= tr('Cantidad', 'Kopurua') ?></th><th><?= tr('Subtotal', 'Azpitotala') ?></th></tr></thead>
    <tbody>
    <?php foreach ($lerroak as $l): ?>
        <tr>
            <td>
                <?= e(localizedProductName($l['izena'])) ?>
                <?php if (!empty($l['cuentas'])): ?>
                    <details>
                        <summary><?= tr('Ver cuenta entregada', 'Emandako kontua ikusi') ?></summary>
                        <?php foreach ($l['cuentas'] as $cuenta): ?>
                            <p>
                                <strong><?= tr('Usuario', 'Erabiltzailea') ?>:</strong> <?= e($cuenta['usuario']) ?><br>
                                <strong><?= tr('Contraseña', 'Pasahitza') ?>:</strong> <?= e($cuenta['contrasena']) ?>
                            </p>
                        <?php endforeach; ?>
                    </details>
                <?php endif; ?>
            </td>
            <td><?= prezioa((float) $l['prezioa_unitarioa']) ?></td>
            <td><?= (int) $l['kopurua'] ?></td>
            <td><?= prezioa((float) $l['prezioa_unitarioa'] * $l['kopurua']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot><tr><td colspan="3" class="right"><strong><?= tr('Total', 'Guztira') ?></strong></td>
        <td><strong><?= prezioa((float) $e['guztira']) ?></strong></td></tr></tfoot>
</table>
