<?php $u = Controller::user(); $saskiaKop = array_sum($_SESSION['saskia'] ?? []); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulua ?? APP_NAME) ?> · <?= APP_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topbar">
    <div class="wrap nav">
        <a class="brand" href="index.php"><?= APP_NAME ?></a>
        <nav>
            <a href="index.php">Catálogo</a>
            <a href="index.php?c=saskia">Carrito (<?= $saskiaKop ?>)</a>
            <?php if ($u): ?>
                <a href="index.php?c=eskaera">Pedidos</a>
                <?php if ($u['rola'] === 'admin'): ?>
                    <a href="index.php?c=produktua">Administración</a>
                <?php endif; ?>
                <span class="user"><?= e($u['izena']) ?></span>
                <a href="index.php?c=auth&amp;a=logout">Salir</a>
            <?php else: ?>
                <a href="index.php?c=auth&amp;a=login">Iniciar sesión</a>
                <a href="index.php?c=auth&amp;a=register">Registrarse</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="wrap">
<?php if ($f = flash()): ?>
    <div class="flash <?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
<?php endif; ?>
