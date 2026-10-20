<h1><?= e($titulua) ?></h1>

<?php if (!empty($errorak)): ?>
    <div class="flash err"><ul><?php foreach ($errorak as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="post" class="form"
      action="index.php?c=produktua&amp;a=<?= e($ekintza) ?><?= $id ? '&amp;id=' . (int) $id : '' ?>">
    <?= csrfField() ?>

    <label>Izena
        <input type="text" name="izena" maxlength="150" required value="<?= e($p['izena'] ?? '') ?>">
    </label>

    <label>Deskribapena
        <textarea name="deskribapena" rows="4"><?= e($p['deskribapena'] ?? '') ?></textarea>
    </label>

    <div class="two">
        <label>Prezioa (€)
            <input type="number" step="0.01" min="0.01" name="prezioa" required value="<?= e((string) ($p['prezioa'] ?? '')) ?>">
        </label>
        <label>Stock
            <input type="number" min="0" name="stock" required value="<?= e((string) ($p['stock'] ?? 0)) ?>">
        </label>
    </div>

    <label>Kategoria
        <select name="kategoria_id">
            <option value="">— Kategoriarik gabe —</option>
            <?php foreach ($kategoriak as $k): ?>
                <option value="<?= (int) $k['id'] ?>"
                    <?= (string) ($p['kategoria_id'] ?? '') === (string) $k['id'] ? 'selected' : '' ?>>
                    <?= e($k['izena']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>URL de la imagen
        <input type="text" name="irudia" value="<?= e($p['irudia'] ?? '') ?>" placeholder="images/logos/index.php?marca=netflix">
        <span class="muted">Puedes mantener la imagen actual o introducir la ruta compartida del logo.</span>
    </label>

    <div class="row-between">
        <a href="index.php?c=produktua">Utzi</a>
        <button class="btn">Gorde</button>
    </div>
</form>
