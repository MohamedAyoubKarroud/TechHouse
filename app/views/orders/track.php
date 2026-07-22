<h1>Suivre une commande</h1>

<form class="track-form" onsubmit="event.preventDefault(); window.location='<?= BASE_URL ?>/order/track/' + encodeURIComponent(document.getElementById('tcode').value);">
  <input type="text" id="tcode" value="<?= e($code ?? '') ?>" placeholder="Code de suivi" required>
  <button class="btn btn-primary" type="submit">Suivre</button>
</form>

<?php if ($code && !$order): ?>
  <p class="alert error">Aucune commande trouvée pour le code <code><?= e($code) ?></code>.</p>
<?php endif; ?>

<?php if ($order): ?>
  <div class="track-result">
    <h2>Commande #<?= (int)$order['id'] ?></h2>
    <?php $statusLabel = ['pending' => 'En attente', 'paid' => 'Payée', 'shipped' => 'Expédiée', 'delivered' => 'Livrée', 'cancelled' => 'Annulée'][$order['status']] ?? $order['status']; ?>
    <p>Statut : <span class="status status-<?= e($order['status']) ?>"><?= e($statusLabel) ?></span></p>
    <p>Passée le : <?= e($order['created_at']) ?> &middot; Mise à jour : <?= e($order['updated_at']) ?></p>
    <p>Total : <?= number_format($order['total'], 2) ?> DH</p>
    <h3>Articles</h3>
    <ul>
      <?php foreach ($items as $it): ?>
        <li><?= e($it['name']) ?> &times; <?= (int)$it['quantity'] ?></li>
      <?php endforeach; ?>
    </ul>
    <div class="tracking-timeline">
      <?php
        $stages = ['pending','paid','shipped','delivered'];
        $labels = ['pending' => 'En attente', 'paid' => 'Payée', 'shipped' => 'Expédiée', 'delivered' => 'Livrée'];
        $cur = array_search($order['status'], $stages);
      ?>
      <?php foreach ($stages as $i => $s): ?>
        <div class="stage <?= ($cur !== false && $i <= $cur) ? 'done' : '' ?>"><?= $labels[$s] ?></div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>
