<div class="row-between">
    <h1>Produktuak kudeatu</h1>
    <a class="btn" href="index.php?c=produktua&amp;a=sortu">+ Produktu berria</a>
</div>

<table class="table">
    <thead>
        <tr><th>ID</th><th>Izena</th><th>Kategoria</th><th>Prezioa</th><th>Stock</th><th>Ekintzak</th></tr>
    </thead>
    <tbody>
    <?php foreach ($produktuak as $p): ?>
        <tr>
            <td><?= (int) $p['id'] ?></td>
            <td><?= e($p['izena']) ?></td>
            <td><?= e($p['kategoria'] ?? '-') ?></td>
            <td><?= prezioa((float) $p['prezioa']) ?></td>
            <td><?= (int) $p['stock'] ?></td>
            <td class="actions">
                <a class="btn small" href="index.php?c=produktua&amp;a=editatu&amp;id=<?= (int) $p['id'] ?>">Editatu</a>
                <form method="post" action="index.php?c=produktua&amp;a=ezabatu" class="inline"
                      onsubmit="return confirm('Ziur zaude?');">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button class="btn small danger">Ezabatu</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
