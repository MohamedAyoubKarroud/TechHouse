<h1>Mes commandes</h1>

<?php if (!$orders): ?>
  <p>Aucune commande pour le moment. <a href="<?= BASE_URL ?>/product">Commencer vos achats</a>.</p>
<?php else: ?>
  <?php $statusLabels = ['pending' => 'En attente', 'paid' => 'Payée', 'shipped' => 'Expédiée', 'delivered' => 'Livrée', 'cancelled' => 'Annulée']; ?>
  <table class="data-table">
    <thead><tr><th>Commande N°</th><th>Date</th><th>Total</th><th>Statut</th><th>Suivi</th></tr></thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td>#<?= (int)$o['id'] ?></td>
          <td><?= e($o['created_at']) ?></td>
          <td><?= number_format($o['total'], 2) ?> DH</td>
          <td><span class="status status-<?= e($o['status']) ?>"><?= e($statusLabels[$o['status']] ?? $o['status']) ?></span></td>
          <td><a href="<?= BASE_URL ?>/order/track/<?= e($o['tracking_code']) ?>"><?= e($o['tracking_code']) ?></a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
