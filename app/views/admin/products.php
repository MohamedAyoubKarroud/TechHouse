<div class="admin">
  <aside class="admin-nav">
    <h3>Admin</h3>
    <a href="<?= BASE_URL ?>/admin">Tableau de bord</a>
    <a href="<?= BASE_URL ?>/admin/products" class="active">Produits</a>
    <a href="<?= BASE_URL ?>/admin/users">Utilisateurs</a>
    <a href="<?= BASE_URL ?>/admin/orders">Commandes</a>
  </aside>

  <section class="admin-content">
    <div class="row-between">
      <h1>Produits</h1>
      <a class="btn btn-primary" href="<?= BASE_URL ?>/admin/productNew">+ Nouveau produit</a>
    </div>

    <table class="data-table">
      <thead><tr><th>ID</th><th>Nom</th><th>Marque</th><th>Prix</th><th>Stock</th><th>Nouveau</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td>#<?= (int)$p['id'] ?></td>
            <td><?= e($p['name']) ?></td>
            <td><?= e($p['brand']) ?></td>
            <td><?= number_format($p['price'], 2) ?> DH</td>
            <td><?= (int)$p['stock'] ?></td>
            <td><?= !empty($p['is_new']) ? 'Oui' : '—' ?></td>
            <td>
              <a href="<?= BASE_URL ?>/admin/productEdit/<?= (int)$p['id'] ?>">modifier</a> |
              <a href="<?= BASE_URL ?>/admin/productDelete/<?= (int)$p['id'] ?>" onclick="return confirm('Supprimer ce produit ?')" class="danger">supprimer</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</div>
