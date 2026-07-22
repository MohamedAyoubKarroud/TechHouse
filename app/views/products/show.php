<div class="product-detail">
  <div class="product-detail-image">
    <?php if (!empty($product['image']) && file_exists(UPLOADS . '/' . $product['image'])): ?>
      <img src="<?= BASE_URL ?>/public/uploads/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
    <?php else: ?>
      <div class="placeholder big"><?= e($product['brand'] ?? 'TechHouse') ?></div>
    <?php endif; ?>
  </div>

  <div class="product-detail-info">
    <a class="back" href="<?= BASE_URL ?>/product/index/<?= e($product['category_slug']) ?>">&larr; <?= e($product['category_name']) ?></a>
    <h1><?= e($product['name']) ?></h1>
    <p class="brand"><?= e($product['brand']) ?> &middot; <?= e($product['color']) ?></p>
    <p class="price big"><?= number_format($product['price'], 2) ?> DH</p>
    <p class="stock <?= $product['stock'] > 0 ? 'in' : 'out' ?>">
      <?= $product['stock'] > 0 ? 'En stock (' . (int)$product['stock'] . ')' : 'Rupture de stock' ?>
    </p>

    <p class="description"><?= nl2br(e($product['description'])) ?></p>

    <?php if ($product['stock'] > 0): ?>
      <form class="add-to-cart" method="post" action="<?= BASE_URL ?>/cart/add">
        <input type="hidden" name="_token" value="<?= Security::csrfToken() ?>">
        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
        <label>Quantité
          <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>">
        </label>
        <button class="btn btn-primary" type="submit">Ajouter au panier</button>
      </form>
    <?php endif; ?>

    <?php if (!empty($product['ai_tags'])): ?>
      <p class="ai-tags"><strong>Tags IA :</strong> <?= e($product['ai_tags']) ?></p>
    <?php endif; ?>
  </div>
</div>
