<!-- Lista de cuentas y acciones disponibles solo para el administrador. -->
<div class="row-between">
    <h1><?= tr('Gestionar usuarios', 'Erabiltzaileak kudeatu') ?></h1>
    <a class="btn" href="index.php?c=erabiltzailea&amp;a=sortu">
        + <?= tr('Nuevo usuario', 'Erabiltzaile berria') ?>
    </a>
</div>

<?php if (!$erabiltzaileak): ?>
    <p class="muted"><?= tr('No hay usuarios registrados.', 'Ez dago erregistratutako erabiltzailerik.') ?></p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th><?= tr('Nombre', 'Izena') ?></th>
                <th><?= tr('Correo electrónico', 'Emaila') ?></th>
                <th><?= tr('Rol', 'Rola') ?></th>
                <th><?= tr('Fecha de registro', 'Erregistro-data') ?></th>
                <th><?= tr('Acciones', 'Ekintzak') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($erabiltzaileak as $usuario): ?>
            <tr>
                <td><?= (int) $usuario['id'] ?></td>
                <td><?= e($usuario['izena']) ?></td>
                <td><?= e($usuario['email']) ?></td>
                <td><?= $usuario['rola'] === 'admin' ? tr('Administrador', 'Administratzailea') : tr('Cliente', 'Bezeroa') ?></td>
                <td><?= e($usuario['sortze_data']) ?></td>
                <td class="actions">
                    <a class="btn small" href="index.php?c=erabiltzailea&amp;a=editatu&amp;id=<?= (int) $usuario['id'] ?>">
                        <?= tr('Editar', 'Editatu') ?>
                    </a>
                    <?php if ((int) $usuario['id'] !== (int) Controller::user()['id']): ?>
                        <form method="post" action="index.php?c=erabiltzailea&amp;a=ezabatu" class="inline"
                              onsubmit="return confirm('<?= tr('¿Seguro que quieres eliminar esta cuenta?', 'Ziur kontu hau ezabatu nahi duzula?') ?>');">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">
                            <button class="btn small danger" type="submit"><?= tr('Eliminar', 'Ezabatu') ?></button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
