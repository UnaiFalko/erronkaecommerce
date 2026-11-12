<?php // Cabecera común: usuario, navegación, idioma y avisos. ?>
<?php
$u = Controller::user();
$saskiaKop = array_sum($_SESSION['saskia'] ?? []);
$lang = currentLanguage();
$langTarget = $lang === 'eu' ? 'es' : 'eu';
$langParams = $_GET;
$langParams['lang'] = $langTarget;
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(localizedProductName($titulua ?? APP_NAME)) ?> · <?= APP_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topbar">
    <div class="wrap nav">
        <a class="brand" href="index.php"><?= APP_NAME ?></a>
        <div class="nav-controls">
            <nav>
                <a href="index.php"><?= tr('Catálogo', 'Katalogoa') ?></a>
                <a href="index.php?c=saskia"><?= tr('Carrito', 'Saskia') ?> (<?= $saskiaKop ?>)</a>
                <?php if ($u): ?>
                    <a href="index.php?c=eskaera"><?= tr('Pedidos', 'Eskaerak') ?></a>
                    <?php if ($u['rola'] === 'admin'): ?>
                        <a href="index.php?c=produktua"><?= tr('Administración', 'Administrazioa') ?></a>
                        <a href="index.php?c=erabiltzailea"><?= tr('Usuarios', 'Erabiltzaileak') ?></a>
                    <?php endif; ?>
                    <span class="user"><?= e($u['izena']) ?></span>
                    <a href="index.php?c=auth&amp;a=logout"><?= tr('Salir', 'Amaitu saioa') ?></a>
                <?php else: ?>
                    <a href="index.php?c=auth&amp;a=login"><?= tr('Iniciar sesión', 'Saioa hasi') ?></a>
                    <a href="index.php?c=auth&amp;a=register"><?= tr('Registrarse', 'Erregistratu') ?></a>
                <?php endif; ?>
            </nav>
            <div class="language-switch" aria-label="<?= tr('Seleccionar idioma', 'Hizkuntza hautatu') ?>">
                <a href="index.php?<?= e(http_build_query($langParams)) ?>" lang="<?= e($langTarget) ?>" hreflang="<?= e($langTarget) ?>">
                    <?= $langTarget === 'eu' ? 'Euskara' : 'Español' ?>
                </a>
                <span aria-hidden="true">·</span>
                <strong aria-current="true"><?= $lang === 'eu' ? 'Euskara' : 'Español' ?></strong>
            </div>
        </div>
    </div>
</header>
<main class="wrap">
<?php if ($f = flash()): ?>
    <div class="flash <?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
<?php endif; ?>
