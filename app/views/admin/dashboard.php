<div class="admin">
  <aside class="admin-nav">
    <h3>Admin</h3>
    <a href="<?= BASE_URL ?>/admin">Tableau de bord</a>
    <a href="<?= BASE_URL ?>/admin/products">Produits</a>
    <a href="<?= BASE_URL ?>/admin/users">Utilisateurs</a>
    <a href="<?= BASE_URL ?>/admin/orders">Commandes</a>
  </aside>

  <section class="admin-content">
    <h1>Tableau de bord</h1>

    <div class="stat-grid">
      <div class="stat"><span class="stat-label">Produits</span><span class="stat-value"><?= (int)$totalProducts ?></span></div>
      <div class="stat"><span class="stat-label">Utilisateurs</span><span class="stat-value"><?= (int)$totalUsers ?></span></div>
      <div class="stat"><span class="stat-label">Commandes</span><span class="stat-value"><?= (int)$totalOrders ?></span></div>
      <div class="stat"><span class="stat-label">Chiffre d'affaires</span><span class="stat-value"><?= number_format($revenue, 2) ?> DH</span></div>
      <div class="stat"><span class="stat-label">Visites totales</span><span class="stat-value"><?= (int)$totalVisits ?></span></div>
      <div class="stat"><span class="stat-label">Visiteurs uniques</span><span class="stat-value"><?= (int)$uniqueVisitors ?></span></div>
    </div>

    <div class="admin-grid-2">
      <div class="admin-card">
        <h3>Visites — 7 derniers jours</h3>
        <table class="data-table">
          <thead><tr><th>Date</th><th>Visites</th></tr></thead>
          <tbody>
            <?php foreach ($visits7d as $row): ?>
              <tr><td><?= e($row['d']) ?></td><td><?= (int)$row['c'] ?></td></tr>
            <?php endforeach; ?>
            <?php if (!$visits7d): ?><tr><td colspan="2">Aucune donnée pour le moment.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="admin-card">
        <h3>Pages les plus consultées</h3>
        <table class="data-table">
          <thead><tr><th>Page</th><th>Vues</th></tr></thead>
          <tbody>
            <?php foreach ($topPages as $row): ?>
              <tr><td><?= e($row['page']) ?></td><td><?= (int)$row['c'] ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="admin-card">
        <h3>Produits les plus populaires</h3>
        <table class="data-table">
          <thead><tr><th>Produit</th><th>Vues</th></tr></thead>
          <tbody>
            <?php foreach ($topProducts as $row): ?>
              <tr><td><a href="<?= BASE_URL ?>/product/show/<?= (int)$row['id'] ?>"><?= e($row['name']) ?></a></td><td><?= (int)$row['views'] ?></td></tr>
            <?php endforeach; ?>
            <?php if (!$topProducts): ?><tr><td colspan="2">Aucune vue pour le moment.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="admin-card">
        <h3>Visiteurs par pays (géolocalisation)</h3>
        <table class="data-table">
          <thead><tr><th>Pays</th><th>Visites</th></tr></thead>
          <tbody>
            <?php foreach ($countries as $row): ?>
              <tr><td><?= e($row['country']) ?></td><td><?= (int)$row['c'] ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>
