<!-- Formulario compartido para crear o editar un producto. -->
<h1><?= e($titulua) ?></h1>

<?php if (!empty($errorak)): ?>
    <div class="flash err"><ul><?php foreach ($errorak as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="post" class="form"
      action="index.php?c=produktua&amp;a=<?= e($ekintza) ?><?= $id ? '&amp;id=' . (int) $id : '' ?>">
    <?= csrfField() ?>

    <label><?= tr('Nombre', 'Izena') ?>
        <input type="text" name="izena" maxlength="150" required value="<?= e($p['izena'] ?? '') ?>">
    </label>

    <label><?= tr('Descripción', 'Deskribapena') ?>
        <textarea name="deskribapena" rows="4"><?= e($p['deskribapena'] ?? '') ?></textarea>
    </label>

    <div class="two">
        <label><?= tr('Precio (€)', 'Prezioa (€)') ?>
            <input type="number" step="0.01" min="0.01" name="prezioa" required value="<?= e((string) ($p['prezioa'] ?? '')) ?>">
        </label>
        <?php if (!empty($cuentaDigital)): ?>
            <label><?= tr('Cuentas disponibles', 'Kontu erabilgarriak') ?>
                <input type="number" value="<?= (int) ($p['stock'] ?? 0) ?>" readonly>
            </label>
        <?php else: ?>
            <label>Stock
                <input type="number" min="0" name="stock" required value="<?= e((string) ($p['stock'] ?? 0)) ?>">
                <span class="muted"><?= tr('En productos de cuentas, la disponibilidad se calcula al cargar credenciales.', 'Kontu-produktuetan, erabilgarritasuna kredentzialak gehitzean kalkulatzen da.') ?></span>
            </label>
        <?php endif; ?>
    </div>

    <label><?= tr('Categoría', 'Kategoria') ?>
        <select name="kategoria_id">
            <option value="">— <?= tr('Sin categoría', 'Kategoriarik gabe') ?> —</option>
            <?php foreach ($kategoriak as $k): ?>
                <option value="<?= (int) $k['id'] ?>"
                    <?= (string) ($p['kategoria_id'] ?? '') === (string) $k['id'] ? 'selected' : '' ?>>
                    <?= e(categoryLabel($k['izena'])) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label><?= tr('URL de la imagen', 'Irudiaren URLa') ?>
        <input type="text" name="irudia" value="<?= e($p['irudia'] ?? '') ?>" placeholder="images/logos/index.php?marca=netflix">
        <span class="muted"><?= tr('Puedes mantener la imagen actual o introducir la ruta compartida del logo.', 'Uneko irudia mantendu edo logoaren bide partekatua sar dezakezu.') ?></span>
    </label>

    <div class="row-between">
        <a href="index.php?c=produktua"><?= tr('Cancelar', 'Utzi') ?></a>
        <button class="btn"><?= tr('Guardar', 'Gorde') ?></button>
    </div>
</form>
