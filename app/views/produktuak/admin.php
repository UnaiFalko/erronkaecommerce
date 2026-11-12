<!-- Lista de productos y acciones disponibles para el administrador. -->
<div class="row-between">
    <h1><?= tr('Gestionar productos', 'Produktuak kudeatu') ?></h1>
    <a class="btn" href="index.php?c=produktua&amp;a=sortu">+ <?= tr('Nuevo producto', 'Produktu berria') ?></a>
</div>

<table class="table">
    <thead>
        <tr><th>ID</th><th><?= tr('Nombre', 'Izena') ?></th><th><?= tr('Categoría', 'Kategoria') ?></th><th><?= tr('Precio', 'Prezioa') ?></th><th>Stock</th><th><?= tr('Acciones', 'Ekintzak') ?></th></tr>
    </thead>
    <tbody>
    <?php foreach ($produktuak as $p): ?>
        <tr>
            <td><?= (int) $p['id'] ?></td>
            <td><?= e(localizedProductName($p['izena'])) ?></td>
            <td><?= e(categoryLabel($p['kategoria'] ?? null)) ?></td>
            <td><?= prezioa((float) $p['prezioa']) ?></td>
            <td><?= (int) $p['stock'] ?></td>
            <td class="actions">
                <a class="btn small" href="index.php?c=produktua&amp;a=editatu&amp;id=<?= (int) $p['id'] ?>"><?= tr('Editar', 'Editatu') ?></a>
                <?php if (($p['kategoria'] ?? '') === 'Cuentas'): ?>
                    <a class="btn small" href="index.php?c=produktua&amp;a=cuentas&amp;id=<?= (int) $p['id'] ?>"><?= tr('Cuentas', 'Kontuak') ?></a>
                <?php endif; ?>
                <form method="post" action="index.php?c=produktua&amp;a=ezabatu" class="inline"
                      onsubmit="return confirm('<?= tr('¿Seguro que quieres eliminarlo?', 'Ziur ezabatu nahi duzula?') ?>');">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button class="btn small danger"><?= tr('Eliminar', 'Ezabatu') ?></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
