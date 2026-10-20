<h1><?= e($titulua) ?></h1>
<?php if (!$eskaerak): ?>
    <p class="muted">No tienes pedidos.</p>
<?php else: ?>
<table class="table">
    <thead><tr><th>#</th><th>Fecha</th><?php if ($admin): ?><th>Cliente</th><?php endif; ?><th>Total</th><th>Estado</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach ($eskaerak as $es): ?>
        <tr>
            <td><?= (int) $es['id'] ?></td>
            <td><?= e($es['data']) ?></td>
            <?php if ($admin): ?><td><?= e($es['bezeroa']) ?></td><?php endif; ?>
            <td><?= prezioa((float) $es['guztira']) ?></td>
            <td><?= e($es['egoera']) ?></td>
            <td class="actions">
                <a href="index.php?c=eskaera&amp;a=xehetasunak&amp;id=<?= (int) $es['id'] ?>">Ver</a>
                <?php if ($admin): ?>
                    <form method="post" action="index.php?c=eskaera&amp;a=ezabatu" class="inline"
                          onsubmit="return confirm('¿Eliminar el pedido #<?= (int) $es['id'] ?>? Esta accion no se puede deshacer.');">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int) $es['id'] ?>">
                        <button class="btn small danger" type="submit">Eliminar</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
