<div class="confirmation">
  <h1>Merci ! Votre commande est confirmée.</h1>
  <?php $statusLabel = ['pending' => 'En attente', 'paid' => 'Payée', 'shipped' => 'Expédiée', 'delivered' => 'Livrée', 'cancelled' => 'Annulée'][$order['status']] ?? $order['status']; ?>
  <p>Commande <strong>#<?= (int)$order['id'] ?></strong> &mdash; statut : <strong><?= e($statusLabel) ?></strong></p>
  <p>Code de suivi : <code><?= e($order['tracking_code']) ?></code> &nbsp;
     <a class="btn btn-ghost" href="<?= BASE_URL ?>/order/track/<?= e($order['tracking_code']) ?>">Suivre la commande</a></p>

  <h3>Articles</h3>
  <ul>
    <?php foreach ($items as $it): ?>
      <li><?= e($it['name']) ?> &times; <?= (int)$it['quantity'] ?> — <?= number_format($it['unit_price']*$it['quantity'], 2) ?> DH</li>
    <?php endforeach; ?>
  </ul>
  <p class="total">Total payé : <strong><?= number_format($order['total'], 2) ?> DH</strong></p>
  <a class="btn btn-primary" href="<?= BASE_URL ?>/order/history">Voir toutes mes commandes</a>
</div>
