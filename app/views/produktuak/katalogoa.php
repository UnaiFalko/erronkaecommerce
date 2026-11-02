<section class="catalog-hero">
    <p class="eyebrow">WinaDenda · <?= tr('Servicios digitales', 'Zerbitzu digitalak') ?></p>
    <h1><?= tr('Recargas, boosting y cuentas', 'Kargak, boosting-a eta kontuak') ?></h1>
    <p><?= tr('Encuentra servicios digitales para tus juegos y plataformas favoritas.', 'Aurkitu zure joko eta plataforma gogokoenetarako zerbitzu digitalak.') ?></p>
</section>

<?php
$parametrosCatalogo = ['q' => $bilaketa, 'orden' => $orden];
if ($soloFavoritos) $parametrosCatalogo['favoritos'] = 1;
$favoritosUrl = 'index.php?' . http_build_query($parametrosCatalogo + ['kategoria' => $kategoriaId]);
?>
<form class="filters" method="get" action="index.php">
    <label class="filter-control filter-search">
        <?= tr('Buscar servicios', 'Zerbitzuak bilatu') ?>
        <input type="search" name="q" placeholder="<?= tr('Ej. monedas, cuenta, boosting', 'Adib. txanponak, kontua, boosting-a') ?>" value="<?= e($bilaketa) ?>">
    </label>
    <label class="filter-control">
        <?= tr('Categoría', 'Kategoria') ?>
        <select name="kategoria">
            <option value=""><?= tr('Todas las categorías', 'Kategoria guztiak') ?></option>
            <?php foreach ($kategoriak as $k): ?>
                <option value="<?= (int) $k['id'] ?>" <?= $kategoriaId === (int) $k['id'] ? 'selected' : '' ?>>
                    <?= e(categoryLabel($k['izena'])) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label class="filter-control">
        <?= tr('Ordenar por', 'Ordenatu') ?>
        <select name="orden">
            <option value="recientes" <?= $orden === 'recientes' ? 'selected' : '' ?>><?= tr('Más recientes', 'Berrienak') ?></option>
            <option value="precio_menor" <?= $orden === 'precio_menor' ? 'selected' : '' ?>><?= tr('Precio: menor a mayor', 'Prezioa: txikienetik handienera') ?></option>
            <option value="precio_mayor" <?= $orden === 'precio_mayor' ? 'selected' : '' ?>><?= tr('Precio: mayor a menor', 'Prezioa: handienetik txikienera') ?></option>
            <option value="nombre" <?= $orden === 'nombre' ? 'selected' : '' ?>><?= tr('Nombre: A-Z', 'Izena: A-Z') ?></option>
        </select>
    </label>
    <?php if ($soloFavoritos): ?><input type="hidden" name="favoritos" value="1"><?php endif; ?>
    <div class="filter-actions">
        <button class="btn" type="submit"><?= tr('Aplicar filtros', 'Iragazkiak aplikatu') ?></button>
        <?php if ($bilaketa !== '' || $kategoriaId || $orden !== 'recientes' || $soloFavoritos): ?>
            <a class="reset-link" href="index.php"><?= tr('Restablecer', 'Berrezarri') ?></a>
        <?php endif; ?>
    </div>
</form>

<nav class="category-links" aria-label="<?= tr('Categorías del catálogo', 'Katalogoko kategoriak') ?>">
    <?php $todosUrl = 'index.php?' . http_build_query($parametrosCatalogo); ?>
    <a class="category-pill <?= !$kategoriaId ? 'active' : '' ?>" href="<?= e($todosUrl) ?>"><?= tr('Todo', 'Dena') ?></a>
    <?php foreach ($kategoriak as $k): ?>
        <?php $categoryParams = $parametrosCatalogo + ['kategoria' => (int) $k['id']]; ?>
        <a class="category-pill <?= $kategoriaId === (int) $k['id'] ? 'active' : '' ?>"
           href="index.php?<?= e(http_build_query($categoryParams)) ?>"><?= e(categoryLabel($k['izena'])) ?></a>
    <?php endforeach; ?>
    <a class="category-pill favorites-pill <?= $soloFavoritos ? 'active' : '' ?>" href="<?= e($favoritosUrl) ?>">
        <?= tr('Mis favoritos', 'Nire gogokoak') ?> <span><?= count($favoritos) ?></span>
    </a>
</nav>

<div class="result-bar">
    <div>
        <h2><?= $soloFavoritos ? tr('Mis favoritos', 'Nire gogokoak') : tr('Servicios disponibles', 'Zerbitzu erabilgarriak') ?></h2>
        <p><?= count($produktuak) ?> <?= count($produktuak) === 1 ? tr('resultado', 'emaitza') : tr('resultados', 'emaitza') ?></p>
    </div>
    <?php if ($soloFavoritos): ?><a class="text-link" href="index.php"><?= tr('Volver al catálogo', 'Katalogora itzuli') ?></a><?php endif; ?>
</div>

<?php if (!$produktuak): ?>
    <div class="empty-catalog">
        <strong><?= $soloFavoritos ? tr('Todavía no tienes favoritos aquí.', 'Oraindik ez duzu gogokorik hemen.') : tr('No se encontraron servicios.', 'Ez da zerbitzurik aurkitu.') ?></strong>
        <p class="muted"><?= $soloFavoritos ? tr('Guarda productos con el botón de estrella para encontrarlos rápido.', 'Gorde produktuak izar-botoiarekin azkar aurkitzeko.') : tr('Prueba otra búsqueda o cambia los filtros.', 'Saiatu beste bilaketa batekin edo aldatu iragazkiak.') ?></p>
        <a class="btn secondary small" href="index.php"><?= tr('Ver todo el catálogo', 'Katalogo osoa ikusi') ?></a>
    </div>
<?php else: ?>
    <?php foreach ($kategoriak as $seccion): ?>
        <?php
        $productosSeccion = array_filter(
            $produktuak,
            static fn($producto) => (int) $producto['kategoria_id'] === (int) $seccion['id']
        );
        ?>
        <?php if ($productosSeccion): ?>
            <section class="catalog-section" id="categoria-<?= (int) $seccion['id'] ?>">
                <?php if (!$kategoriaId && !$soloFavoritos): ?>
                    <div class="section-heading">
                        <p class="eyebrow"><?= tr('Explora WinaDenda', 'Arakatu WinaDenda') ?></p>
                        <h2><?= e(categoryLabel($seccion['izena'])) ?></h2>
                    </div>
                <?php endif; ?>
                <div class="grid">
                    <?php foreach ($productosSeccion as $p): ?>
                        <?php $esFavorito = in_array((int) $p['id'], $favoritos, true); ?>
                        <article class="card">
                            <div class="card-media">
                                <?php if ($p['irudia']): ?>
                                    <img src="<?= e($p['irudia']) ?>" alt="<?= e(localizedProductName($p['izena'])) ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="noimg"><?= tr('Imagen no disponible', 'Irudia ez dago erabilgarri') ?></div>
                                <?php endif; ?>
                                <form class="favorite-form" method="post" action="index.php?c=home&amp;a=favorito">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                    <input type="hidden" name="q" value="<?= e($bilaketa) ?>">
                                    <input type="hidden" name="kategoria" value="<?= (int) ($kategoriaId ?? 0) ?>">
                                    <input type="hidden" name="orden" value="<?= e($orden) ?>">
                                    <input type="hidden" name="favoritos" value="<?= $soloFavoritos ? '1' : '0' ?>">
                                    <button class="favorite-btn <?= $esFavorito ? 'saved' : '' ?>" type="submit"
                                            aria-label="<?= $esFavorito ? tr('Quitar de favoritos', 'Gogokoetatik kendu') : tr('Añadir a favoritos', 'Gogokoetara gehitu') ?>"
                                            title="<?= $esFavorito ? tr('Quitar de favoritos', 'Gogokoetatik kendu') : tr('Añadir a favoritos', 'Gogokoetara gehitu') ?>">
                                        <span aria-hidden="true"><?= $esFavorito ? '&#9733;' : '&#9734;' ?></span>
                                    </button>
                                </form>
                            </div>
                            <div class="card-body">
                                <div class="card-meta">
                                    <span class="tag"><?= e($p['kategoria']) ?></span>
                                    <?php if ($p['stock'] > 0): ?>
                                        <span class="availability"><?= tr('Disponible', 'Erabilgarri') ?></span>
                                    <?php else: ?>
                                        <span class="unavailable"><?= tr('Agotado', 'Agortuta') ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3><a href="index.php?c=produktua&amp;a=xehetasunak&amp;id=<?= (int) $p['id'] ?>"><?= e(localizedProductName($p['izena'])) ?></a></h3>
                                <p class="card-description"><?= e(localizedProductDescription($p['deskribapena'])) ?></p>
                                <p class="price"><?= prezioa((float) $p['prezioa']) ?></p>
                                <div class="card-actions">
                                    <a class="btn secondary small" href="index.php?c=produktua&amp;a=xehetasunak&amp;id=<?= (int) $p['id'] ?>"><?= tr('Ver detalles', 'Xehetasunak ikusi') ?></a>
                                    <?php if ($p['stock'] > 0): ?>
                                        <form method="post" action="index.php?c=saskia&amp;a=gehitu">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                            <input type="hidden" name="kopurua" value="1">
                                            <input type="hidden" name="jatorria" value="katalogoa">
                                            <input type="hidden" name="kategoria" value="<?= (int) ($kategoriaId ?? 0) ?>">
                                            <input type="hidden" name="q" value="<?= e($bilaketa) ?>">
                                            <input type="hidden" name="orden" value="<?= e($orden) ?>">
                                            <input type="hidden" name="favoritos" value="<?= $soloFavoritos ? '1' : '0' ?>">
                                            <button class="btn small" type="submit"><?= tr('Añadir al carrito', 'Saskira gehitu') ?></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>