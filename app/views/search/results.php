<h1>Recherche</h1>

<form class="advanced-search" method="get" action="<?= BASE_URL ?>/search">
  <input type="text" name="q" value="<?= e($keyword) ?>" placeholder="Mots-clés...">
  <select name="category_id">
    <option value="">Toutes les rubriques</option>
    <?php foreach ($categories as $c): ?>
      <option value="<?= (int)$c['id'] ?>" <?= ($filters['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <select name="brand">
    <option value="">Toutes les marques</option>
    <?php foreach ($brands as $b): ?>
      <option value="<?= e($b) ?>" <?= ($filters['brand'] ?? '') === $b ? 'selected' : '' ?>><?= e($b) ?></option>
    <?php endforeach; ?>
  </select>
  <input type="number" name="min_price" value="<?= e($filters['min_price']) ?>" placeholder="Min DH">
  <input type="number" name="max_price" value="<?= e($filters['max_price']) ?>" placeholder="Max DH">
  <button class="btn btn-primary" type="submit">Rechercher</button>
</form>

<?php if ($keyword === ''): ?>
  <p>Saisissez un mot-clé pour lancer la recherche.</p>
<?php elseif (!$results): ?>
  <p>Aucun résultat pour "<strong><?= e($keyword) ?></strong>".</p>
<?php else: ?>
  <p><?= count($results) ?> résultat(s) pour "<strong><?= e($keyword) ?></strong>"</p>
  <div class="product-grid">
    <?php foreach ($results as $p): ?>
      <a class="product-card" href="<?= BASE_URL ?>/product/show/<?= (int)$p['id'] ?>">
        <div class="product-image">
          <?php if (!empty($p['image']) && file_exists(UPLOADS . '/' . $p['image'])): ?>
            <img src="<?= BASE_URL ?>/public/uploads/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
          <?php else: ?>
            <div class="placeholder"><?= e($p['brand'] ?? 'TechHouse') ?></div>
          <?php endif; ?>
        </div>
        <h4><?= e($p['name']) ?></h4>
        <p class="brand"><?= e($p['brand']) ?></p>
        <p class="price"><?= number_format($p['price'], 2) ?> DH</p>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
