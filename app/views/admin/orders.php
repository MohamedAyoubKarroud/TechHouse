<div class="admin">
  <aside class="admin-nav">
    <h3>Admin</h3>
    <a href="<?= BASE_URL ?>/admin">Tableau de bord</a>
    <a href="<?= BASE_URL ?>/admin/products">Produits</a>
    <a href="<?= BASE_URL ?>/admin/users">Utilisateurs</a>
    <a href="<?= BASE_URL ?>/admin/orders" class="active">Commandes</a>
  </aside>

  <section class="admin-content">
    <h1>Commandes</h1>
    <table class="data-table">
      <thead><tr><th>#</th><th>Client</th><th>Total</th><th>Statut</th><th>Suivi</th><th>Passée le</th></tr></thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td>#<?= (int)$o['id'] ?></td>
            <td><?= e($o['user_name']) ?> <small>(<?= e($o['user_email']) ?>)</small></td>
            <td><?= number_format($o['total'], 2) ?> DH</td>
            <td>
              <form method="post" action="<?= BASE_URL ?>/admin/orderStatus" class="inline">
                <input type="hidden" name="_token" value="<?= Security::csrfToken() ?>">
                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                <select name="status" onchange="this.form.submit()">
                  <?php
                    $statusLabels = ['pending' => 'En attente', 'paid' => 'Payée', 'shipped' => 'Expédiée', 'delivered' => 'Livrée', 'cancelled' => 'Annulée'];
                  ?>
                  <?php foreach ($statusLabels as $s => $label): ?>
                    <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= $label ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            </td>
            <td><code><?= e($o['tracking_code']) ?></code></td>
            <td><?= e($o['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</div>
