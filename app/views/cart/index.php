<h1>Votre panier</h1>

<?php if (!$items): ?>
  <p>Votre panier est vide. <a href="<?= BASE_URL ?>/product">Continuer vos achats</a>.</p>
<?php else: ?>
  <table class="cart-table">
    <thead>
      <tr><th>Produit</th><th>Prix</th><th>Qté</th><th>Sous-total</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($items as $it): ?>
        <tr>
          <td><a href="<?= BASE_URL ?>/product/show/<?= (int)$it['id'] ?>"><?= e($it['name']) ?></a></td>
          <td><?= number_format($it['price'], 2) ?> DH</td>
          <td>
            <form method="post" action="<?= BASE_URL ?>/cart/update" class="inline">
              <input type="hidden" name="_token" value="<?= Security::csrfToken() ?>">
              <input type="hidden" name="product_id" value="<?= (int)$it['id'] ?>">
              <input type="number" name="quantity" value="<?= (int)$it['quantity'] ?>" min="0" style="width:60px;">
              <button type="submit" class="btn-link">mettre à jour</button>
            </form>
          </td>
          <td><?= number_format($it['price'] * $it['quantity'], 2) ?> DH</td>
          <td><a class="btn-link danger" href="<?= BASE_URL ?>/cart/remove/<?= (int)$it['id'] ?>">retirer</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="cart-bottom">
    <section class="promo-box">
      <?php if ($promo): $isActive = $discount > 0 || $promo['type'] === 'shipping'; ?>
        <div class="promo-applied <?= $isActive ? 'is-active' : 'is-pending' ?>">
          <div class="promo-applied-info">
            <span class="promo-tag">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 13.42 20.58a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
              <strong><?= e($promo['code']) ?></strong>
            </span>
            <div class="promo-applied-meta">
              <strong><?= e($promo['label']) ?></strong>
              <?php if (!$isActive && $promo['type'] !== 'shipping'): ?>
                <small>Ajoutez <?= number_format($promo['min'] - $subtotal, 2) ?> DH pour activer ce code.</small>
              <?php elseif ($promo['type'] === 'shipping'): ?>
                <small>La livraison standard sera offerte au paiement.</small>
              <?php else: ?>
                <small>Vous économisez <?= number_format($discount, 2) ?> DH</small>
              <?php endif; ?>
            </div>
          </div>
          <a class="btn-link danger" href="<?= BASE_URL ?>/cart/clearpromo">retirer</a>
        </div>
      <?php else: ?>
        <form method="post" action="<?= BASE_URL ?>/cart/applypromo" class="promo-form">
          <input type="hidden" name="_token" value="<?= Security::csrfToken() ?>">
          <label for="promoCode">Code promo</label>
          <div class="promo-input-row">
            <input type="text" name="code" id="promoCode" placeholder="Entrez votre code" autocomplete="off" spellcheck="false">
            <button type="submit" class="btn btn-primary">Appliquer</button>
          </div>
          <details class="promo-hint">
            <summary>Voir les codes disponibles</summary>
            <ul>
              <?php foreach ($catalog as $code => $rule): ?>
                <li><code><?= e($code) ?></code> — <?= e($rule['label']) ?></li>
              <?php endforeach; ?>
            </ul>
          </details>
        </form>
      <?php endif; ?>
    </section>

    <aside class="cart-totals">
      <dl>
        <div><dt>Sous-total</dt><dd><?= number_format($subtotal, 2) ?> DH</dd></div>
        <?php if ($discount > 0): ?>
          <div class="cart-totals-discount">
            <dt>Remise (<?= e($promo['code']) ?>)</dt>
            <dd>- <?= number_format($discount, 2) ?> DH</dd>
          </div>
        <?php endif; ?>
        <div class="cart-totals-grand">
          <dt>Total</dt>
          <dd><?= number_format($total, 2) ?> DH</dd>
        </div>
      </dl>
      <div class="cart-actions">
        <a class="btn btn-ghost" href="<?= BASE_URL ?>/product">Continuer</a>
        <a class="btn btn-primary" href="<?= BASE_URL ?>/order/checkout">Commander</a>
      </div>
    </aside>
  </div>
<?php endif; ?>
