<div class="admin">
  <aside class="admin-nav">
    <h3>Admin</h3>
    <a href="<?= BASE_URL ?>/admin">Tableau de bord</a>
    <a href="<?= BASE_URL ?>/admin/products" class="active">Produits</a>
    <a href="<?= BASE_URL ?>/admin/users">Utilisateurs</a>
    <a href="<?= BASE_URL ?>/admin/orders">Commandes</a>
  </aside>

  <section class="admin-content">
    <h1><?= $product ? 'Modifier le produit' : 'Nouveau produit' ?></h1>
    <?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="admin-form">
      <input type="hidden" name="_token" value="<?= Security::csrfToken() ?>">

      <label>Nom <input type="text" name="name" required value="<?= e($product['name'] ?? '') ?>"></label>
      <label>Slug (laisser vide pour génération automatique) <input type="text" name="slug" value="<?= e($product['slug'] ?? '') ?>"></label>

      <div class="form-row">
        <label>Catégorie
          <select name="category_id">
            <option value="">— Laisser l'IA décider —</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= (int)$c['id'] ?>" <?= ($product['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Marque <input type="text" name="brand" value="<?= e($product['brand'] ?? '') ?>"></label>
        <label>Couleur <input type="text" name="color" value="<?= e($product['color'] ?? '') ?>"></label>
      </div>

      <div class="form-row">
        <label>Prix (DH) <input type="number" name="price" step="0.01" min="0" required value="<?= e($product['price'] ?? '') ?>"></label>
        <label>Stock <input type="number" name="stock" min="0" required value="<?= e($product['stock'] ?? '0') ?>"></label>
        <label class="checkbox"><input type="checkbox" name="is_new" value="1" <?= !empty($product['is_new']) ? 'checked' : '' ?>> Nouvelle collection</label>
      </div>

      <label>Description <textarea name="description" rows="5"><?= e($product['description'] ?? '') ?></textarea></label>

      <label>Image (jpg/png/webp) <input type="file" name="image" accept="image/*"></label>
      <?php if (!empty($product['image'])): ?>
        <p>Actuelle : <code><?= e($product['image']) ?></code></p>
      <?php endif; ?>

      <p class="hint">Si aucune catégorie n'est sélectionnée, le classificateur IA (heuristique par défaut) en suggérera une à partir du nom et de la description.</p>

      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        <a class="btn btn-ghost" href="<?= BASE_URL ?>/admin/products">Annuler</a>
      </div>
    </form>
  </section>
</div>
