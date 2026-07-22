<div class="admin">
  <aside class="admin-nav">
    <h3>Admin</h3>
    <a href="<?= BASE_URL ?>/admin">Tableau de bord</a>
    <a href="<?= BASE_URL ?>/admin/products">Produits</a>
    <a href="<?= BASE_URL ?>/admin/users" class="active">Utilisateurs</a>
    <a href="<?= BASE_URL ?>/admin/orders">Commandes</a>
  </aside>

  <section class="admin-content">
    <h1>Utilisateurs</h1>
    <table class="data-table">
      <thead><tr><th>ID</th><th>Nom</th><th>E-mail</th><th>Rôle</th><th>Inscrit le</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td>#<?= (int)$u['id'] ?></td>
            <td><?= e($u['name']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td>
              <form method="post" action="<?= BASE_URL ?>/admin/userRole" class="inline">
                <input type="hidden" name="_token" value="<?= Security::csrfToken() ?>">
                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                <select name="role" onchange="this.form.submit()">
                  <option value="client" <?= $u['role'] === 'client' ? 'selected' : '' ?>>client</option>
                  <option value="admin"  <?= $u['role'] === 'admin'  ? 'selected' : '' ?>>admin</option>
                </select>
              </form>
            </td>
            <td><?= e($u['created_at']) ?></td>
            <td>
              <?php if ($u['id'] != Auth::id()): ?>
                <a href="<?= BASE_URL ?>/admin/userDelete/<?= (int)$u['id'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')" class="danger">supprimer</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</div>
