<h1><?= e($titulua) ?></h1>
<?php if (!$eskaerak) { ?>
    <p class="muted"><?= tr('No hay pedidos.', 'Ez dago eskaerarik.') ?></p>
<?php } else { ?>
<table class="table">
    <thead><tr><th>#</th><th><?= tr('Fecha', 'Data') ?></th><?php if ($admin) { ?><th><?= tr('Cliente', 'Bezeroa') ?></th><?php } ?><th><?= tr('Total', 'Guztira') ?></th><th><?= tr('Estado', 'Egoera') ?></th><th></th></tr></thead>
    <tbody>
    <?php foreach ($eskaerak as $es): ?>
        <tr>
            <td><?= (int) $es['id'] ?></td>
            <td><?= e($es['data']) ?></td>
            <?php if ($admin) { ?><td><?= e($es['bezeroa']) ?></td><?php } ?>
            <td><?= prezioa((float) $es['guztira']) ?></td>
            <td><?= e(orderStatusLabel($es['egoera'])) ?></td>
            <td><a href="index.php?c=eskaera&amp;a=xehetasunak&amp;id=<?= (int) $es['id'] ?>"><?= tr('Ver', 'Ikusi') ?></a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php } ?>
