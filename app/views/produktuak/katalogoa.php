<section class="catalog-hero">
    <p class="eyebrow">WinaDenda · Servicios digitales</p>
    <h1>Recargas, boosting y cuentas</h1>
    <p>Encuentra servicios digitales para tus juegos y plataformas favoritas.</p>
</section>

<?php
$parametrosCatalogo = ['q' => $bilaketa, 'orden' => $orden];
if ($soloFavoritos) $parametrosCatalogo['favoritos'] = 1;
$favoritosUrl = 'index.php?' . http_build_query($parametrosCatalogo + ['kategoria' => $kategoriaId]);
?>
<form class="filters" method="get" action="index.php">
    <label class="filter-control filter-search">
        Buscar servicios
        <input type="search" name="q" placeholder="Ej. monedas, cuenta, boosting" value="<?= e($bilaketa) ?>">
    </label>
    <label class="filter-control">
        Categoría
        <select name="kategoria">
            <option value="">Todas las categorías</option>
            <?php foreach ($kategoriak as $k): ?>
                <option value="<?= (int) $k['id'] ?>" <?= $kategoriaId === (int) $k['id'] ? 'selected' : '' ?>>
                    <?= e($k['izena']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label class="filter-control">
        Ordenar por
        <select name="orden">
            <option value="recientes" <?= $orden === 'recientes' ? 'selected' : '' ?>>Más recientes</option>
            <option value="precio_menor" <?= $orden === 'precio_menor' ? 'selected' : '' ?>>Precio: menor a mayor</option>
            <option value="precio_mayor" <?= $orden === 'precio_mayor' ? 'selected' : '' ?>>Precio: mayor a menor</option>
            <option value="nombre" <?= $orden === 'nombre' ? 'selected' : '' ?>>Nombre: A-Z</option>
        </select>
    </label>
    <?php if ($soloFavoritos): ?><input type="hidden" name="favoritos" value="1"><?php endif; ?>
    <div class="filter-actions">
        <button class="btn" type="submit">Aplicar filtros</button>
        <?php if ($bilaketa !== '' || $kategoriaId || $orden !== 'recientes' || $soloFavoritos): ?>
            <a class="reset-link" href="index.php">Restablecer</a>
        <?php endif; ?>
    </div>
</form>

<nav class="category-links" aria-label="Categorías del catálogo">
    <?php $todosUrl = 'index.php?' . http_build_query($parametrosCatalogo); ?>
    <a class="category-pill <?= !$kategoriaId ? 'active' : '' ?>" href="<?= e($todosUrl) ?>">Todo</a>
    <?php foreach ($kategoriak as $k): ?>
        <?php $categoryParams = $parametrosCatalogo + ['kategoria' => (int) $k['id']]; ?>
        <a class="category-pill <?= $kategoriaId === (int) $k['id'] ? 'active' : '' ?>"
           href="index.php?<?= e(http_build_query($categoryParams)) ?>"><?= e($k['izena']) ?></a>
    <?php endforeach; ?>
    <a class="category-pill favorites-pill <?= $soloFavoritos ? 'active' : '' ?>" href="<?= e($favoritosUrl) ?>">
        Mis favoritos <span><?= count($favoritos) ?></span>
    </a>
</nav>

<div class="result-bar">
    <div>
        <h2><?= $soloFavoritos ? 'Mis favoritos' : 'Servicios disponibles' ?></h2>
        <p><?= count($produktuak) ?> <?= count($produktuak) === 1 ? 'resultado' : 'resultados' ?></p>
    </div>
    <?php if ($soloFavoritos): ?><a class="text-link" href="index.php">Volver al catálogo</a><?php endif; ?>
</div>

<?php if (!$produktuak): ?>
    <div class="empty-catalog">
        <strong><?= $soloFavoritos ? 'Todavía no tienes favoritos aquí.' : 'No se encontraron servicios.' ?></strong>
        <p class="muted"><?= $soloFavoritos ? 'Guarda productos con el botón de estrella para encontrarlos rápido.' : 'Prueba otra búsqueda o cambia los filtros.' ?></p>
        <a class="btn secondary small" href="index.php">Ver todo el catálogo</a>
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
                        <p class="eyebrow">Explora WinaDenda</p>
                        <h2><?= e($seccion['izena']) ?></h2>
                    </div>
                <?php endif; ?>
                <div class="grid">
                    <?php foreach ($productosSeccion as $p): ?>
                        <?php $esFavorito = in_array((int) $p['id'], $favoritos, true); ?>
                        <article class="card">
                            <div class="card-media">
                                <?php if ($p['irudia']): ?>
                                    <img class="brand-logo" src="<?= e($p['irudia']) ?>" alt="Logo de <?= e($p['izena']) ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="noimg">Imagen no disponible</div>
                                <?php endif; ?>
                                <form class="favorite-form" method="post" action="index.php?c=home&amp;a=favorito">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                    <input type="hidden" name="q" value="<?= e($bilaketa) ?>">
                                    <input type="hidden" name="kategoria" value="<?= (int) ($kategoriaId ?? 0) ?>">
                                    <input type="hidden" name="orden" value="<?= e($orden) ?>">
                                    <input type="hidden" name="favoritos" value="<?= $soloFavoritos ? '1' : '0' ?>">
                                    <button class="favorite-btn <?= $esFavorito ? 'saved' : '' ?>" type="submit"
                                            aria-label="<?= $esFavorito ? 'Quitar de favoritos' : 'Añadir a favoritos' ?>"
                                            title="<?= $esFavorito ? 'Quitar de favoritos' : 'Añadir a favoritos' ?>">
                                        <span aria-hidden="true"><?= $esFavorito ? '&#9733;' : '&#9734;' ?></span>
                                    </button>
                                </form>
                            </div>
                            <div class="card-body">
                                <div class="card-meta">
                                    <span class="tag"><?= e($p['kategoria']) ?></span>
                                    <?php if ($p['stock'] > 0): ?>
                                        <span class="availability">Disponible</span>
                                    <?php else: ?>
                                        <span class="unavailable">Agotado</span>
                                    <?php endif; ?>
                                </div>
                                <h3><a href="index.php?c=produktua&amp;a=xehetasunak&amp;id=<?= (int) $p['id'] ?>"><?= e($p['izena']) ?></a></h3>
                                <p class="card-description"><?= e($p['deskribapena']) ?></p>
                                <p class="price"><?= prezioa((float) $p['prezioa']) ?></p>
                                <div class="card-actions">
                                    <a class="btn secondary small" href="index.php?c=produktua&amp;a=xehetasunak&amp;id=<?= (int) $p['id'] ?>">Ver detalles</a>
                                    <?php if ($p['stock'] > 0): ?>
                                        <form method="post" action="index.php?c=saskia&amp;a=gehitu">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                            <input type="hidden" name="kopurua" value="1">
                                            <button class="btn small" type="submit">Añadir al carrito</button>
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