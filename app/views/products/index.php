<div class="listing-layout">
  <aside class="filters">
    <h3>Filtres</h3>
    <form method="get" action="">
      <label>Marque
        <select name="brand">
          <option value="">Toutes les marques</option>
          <?php foreach ($brands as $b): ?>
            <option value="<?= e($b) ?>" <?= ($filters['brand'] ?? '') === $b ? 'selected' : '' ?>><?= e($b) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Couleur
        <select name="color">
          <option value="">Toutes les couleurs</option>
          <?php foreach ($colors as $c): ?>
            <option value="<?= e($c) ?>" <?= ($filters['color'] ?? '') === $c ? 'selected' : '' ?>><?= e($c) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <?php
        $pbMin = (int)$priceBounds['min'];
        $pbMax = (int)$priceBounds['max'];
        $curMin = $filters['min_price'] !== '' ? max($pbMin, (int)$filters['min_price']) : $pbMin;
        $curMax = $filters['max_price'] !== '' ? min($pbMax, (int)$filters['max_price']) : $pbMax;
      ?>
      <div class="price-slider"
           data-price-slider
           data-min="<?= $pbMin ?>"
           data-max="<?= $pbMax ?>">
        <div class="price-slider-head">
          <span>Prix</span>
          <span class="price-slider-values">
            <strong data-price-min-label><?= number_format($curMin, 0, ',', ' ') ?></strong>
            <span>–</span>
            <strong data-price-max-label><?= number_format($curMax, 0, ',', ' ') ?></strong>
            DH
          </span>
        </div>
        <div class="price-slider-track">
          <div class="price-slider-fill" data-price-fill></div>
          <input type="range" class="price-slider-range price-slider-range-min"
                 min="<?= $pbMin ?>" max="<?= $pbMax ?>" value="<?= $curMin ?>" step="1"
                 data-price-min aria-label="Prix minimum">
          <input type="range" class="price-slider-range price-slider-range-max"
                 min="<?= $pbMin ?>" max="<?= $pbMax ?>" value="<?= $curMax ?>" step="1"
                 data-price-max aria-label="Prix maximum">
        </div>
        <div class="price-slider-bounds">
          <span><?= number_format($pbMin, 0, ',', ' ') ?> DH</span>
          <span><?= number_format($pbMax, 0, ',', ' ') ?> DH</span>
        </div>
        <input type="hidden" name="min_price" value="<?= $curMin ?>" data-price-min-input>
        <input type="hidden" name="max_price" value="<?= $curMax ?>" data-price-max-input>
      </div>

      <label class="checkbox">
        <input type="checkbox" name="is_new" value="1" <?= !empty($filters['is_new']) ? 'checked' : '' ?>>
        Nouvelles collections uniquement
      </label>

      <label>Trier par
        <select name="sort">
          <option value="newest"     <?= $filters['sort']==='newest'     ? 'selected' : '' ?>>Plus récents</option>
          <option value="price_asc"  <?= $filters['sort']==='price_asc'  ? 'selected' : '' ?>>Prix : croissant</option>
          <option value="price_desc" <?= $filters['sort']==='price_desc' ? 'selected' : '' ?>>Prix : décroissant</option>
          <option value="name"       <?= $filters['sort']==='name'       ? 'selected' : '' ?>>Nom (A–Z)</option>
        </select>
      </label>

      <button class="btn btn-primary" type="submit">Appliquer</button>
      <a class="btn btn-ghost" href="?">Réinitialiser</a>
    </form>
  </aside>

  <section class="listing">
    <h1><?= e($category['name'] ?? 'Tous les produits') ?> <small>(<?= count($products) ?>)</small></h1>

    <?php if (!$products): ?>
      <p>Aucun produit ne correspond à vos filtres.</p>
    <?php else: ?>
      <div class="product-grid">
        <?php foreach ($products as $p): ?>
          <a class="product-card" href="<?= BASE_URL ?>/product/show/<?= (int)$p['id'] ?>">
            <div class="product-image">
              <?php if (!empty($p['image']) && file_exists(UPLOADS . '/' . $p['image'])): ?>
                <img src="<?= BASE_URL ?>/public/uploads/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
              <?php else: ?>
                <div class="placeholder"><?= e($p['brand'] ?? 'TechHouse') ?></div>
              <?php endif; ?>
              <?php if (!empty($p['is_new'])): ?><span class="badge-new">NOUVEAU</span><?php endif; ?>
            </div>
            <h4><?= e($p['name']) ?></h4>
            <p class="brand"><?= e($p['brand']) ?></p>
            <p class="price"><?= number_format($p['price'], 2) ?> DH</p>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</div>
