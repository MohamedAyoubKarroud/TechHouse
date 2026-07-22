<?php
  $f = $form;
  $thresholdLabel = number_format($freeShippingThreshold, 2);
?>
<div class="checkout-head">
  <h1>Finaliser la commande</h1>
  <p class="checkout-sub">Quelques informations pour préparer et expédier votre commande en toute sécurité.</p>
</div>

<?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>

<div class="checkout-layout">
  <form method="post" action="<?= BASE_URL ?>/order/checkout" class="checkout-form" id="checkoutForm" novalidate>
    <input type="hidden" name="_token" value="<?= Security::csrfToken() ?>">

    <!-- ===== Contact ===== -->
    <section class="form-section">
      <header class="form-section-head">
        <span class="form-step">1</span>
        <div>
          <h3>Vos coordonnées</h3>
          <p>Nous utiliserons ces informations pour vous tenir informé de votre commande.</p>
        </div>
      </header>
      <div class="form-row">
        <label>Prénom *
          <input type="text" name="first_name" value="<?= e($f['first_name']) ?>" required autocomplete="given-name">
        </label>
        <label>Nom *
          <input type="text" name="last_name" value="<?= e($f['last_name']) ?>" required autocomplete="family-name">
        </label>
      </div>
      <div class="form-row">
        <label>Email *
          <input type="email" name="email" value="<?= e($f['email']) ?>" required autocomplete="email">
        </label>
        <label>Téléphone *
          <input type="tel" name="phone" value="<?= e($f['phone']) ?>" required autocomplete="tel" placeholder="+212 6 12 34 56 78">
        </label>
      </div>
    </section>

    <!-- ===== Shipping address ===== -->
    <section class="form-section">
      <header class="form-section-head">
        <span class="form-step">2</span>
        <div>
          <h3>Adresse de livraison</h3>
          <p>Où souhaitez-vous recevoir votre commande ?</p>
        </div>
      </header>
      <label>Adresse *
        <input type="text" name="address" value="<?= e($f['address']) ?>" required autocomplete="address-line1" placeholder="N° et nom de rue">
      </label>
      <label>Complément d'adresse
        <input type="text" name="address2" value="<?= e($f['address2']) ?>" autocomplete="address-line2" placeholder="Appartement, étage, bâtiment (optionnel)">
      </label>
      <div class="form-row">
        <label>Code postal *
          <input type="text" name="postal" value="<?= e($f['postal']) ?>" required autocomplete="postal-code">
        </label>
        <label>Ville *
          <input type="text" name="city" value="<?= e($f['city']) ?>" required autocomplete="address-level2">
        </label>
        <label>Pays *
          <select name="country" required autocomplete="country-name">
            <?php foreach (['Maroc','France','Belgique','Suisse','Canada','Tunisie','Algérie','Sénégal'] as $c): ?>
              <option value="<?= e($c) ?>" <?= $f['country'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </div>
    </section>

    <!-- ===== Shipping method ===== -->
    <section class="form-section">
      <header class="form-section-head">
        <span class="form-step">3</span>
        <div>
          <h3>Mode de livraison</h3>
          <p>Livraison gratuite à partir de <?= $thresholdLabel ?> DH (hors express).</p>
        </div>
      </header>
      <div class="choice-grid">
        <?php foreach ($shippingRates as $key => $opt):
          $isFree = ($subtotal >= $freeShippingThreshold && $key !== 'express');
          $displayFee = $isFree ? 0.0 : $opt['fee'];
        ?>
          <label class="choice-card">
            <input type="radio" name="shipping" value="<?= e($key) ?>"
                   data-fee="<?= e((string)$displayFee) ?>"
                   <?= $f['shipping'] === $key ? 'checked' : '' ?>>
            <div class="choice-card-body">
              <div class="choice-card-title">
                <strong><?= e($opt['label']) ?></strong>
                <span class="choice-card-price">
                  <?php if ($displayFee == 0): ?>
                    Gratuit
                  <?php else: ?>
                    <?= number_format($displayFee, 2) ?> DH
                  <?php endif; ?>
                </span>
              </div>
              <small><?= e($opt['eta']) ?></small>
            </div>
          </label>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- ===== Payment ===== -->
    <section class="form-section">
      <header class="form-section-head">
        <span class="form-step">4</span>
        <div>
          <h3>Mode de paiement</h3>
          <p>Toutes les transactions sont sécurisées et chiffrées.</p>
        </div>
      </header>
      <div class="choice-grid choice-grid-2">
        <?php
          $payIcons = [
            'cod'      => 'M21 8h-2V6a2 2 0 0 0-2-2H3a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h2v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2zM3 6h14v2H7a2 2 0 0 0-2 2v5H3V6zm18 14H7v-9h14v9z',
            'card'     => 'M3 4h18v2H3zM3 10h18v10H3z',
            'transfer' => 'M4 7h16M4 7l4-4M4 7l4 4M20 17H4M20 17l-4-4M20 17l-4 4',
            'paypal'   => 'M7 4h7a4 4 0 0 1 0 8h-4l-1 8H6L7 4z',
          ];
          foreach ($paymentMethods as $key => $label):
        ?>
          <label class="choice-card">
            <input type="radio" name="payment" value="<?= e($key) ?>" <?= $f['payment'] === $key ? 'checked' : '' ?>>
            <div class="choice-card-body">
              <div class="choice-card-title">
                <strong><?= e($label) ?></strong>
              </div>
              <small>
                <?php if ($key === 'cod'): ?>Payez en espèces à la réception.<?php endif; ?>
                <?php if ($key === 'card'): ?>Visa, Mastercard, CMI.<?php endif; ?>
                <?php if ($key === 'transfer'): ?>Coordonnées envoyées par email.<?php endif; ?>
                <?php if ($key === 'paypal'): ?>Compte ou carte via PayPal.<?php endif; ?>
              </small>
            </div>
          </label>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- ===== Notes ===== -->
    <section class="form-section">
      <header class="form-section-head">
        <span class="form-step">5</span>
        <div>
          <h3>Instructions (optionnel)</h3>
          <p>Précisez un horaire, une porte d'entrée, un cadeau…</p>
        </div>
      </header>
      <label>
        <textarea name="notes" rows="3" maxlength="500" placeholder="Notes pour le livreur ou pour TechHouse"><?= e($f['notes']) ?></textarea>
      </label>
    </section>

    <label class="checkbox-row">
      <input type="checkbox" name="terms" value="1" <?= $f['terms'] ? 'checked' : '' ?> required>
      <span>J'accepte les <a href="#" target="_blank">conditions générales de vente</a> et la <a href="#" target="_blank">politique de confidentialité</a>.</span>
    </label>

    <button class="btn btn-primary btn-block btn-lg" type="submit">
      Confirmer la commande — <span id="checkoutGrandTotal"><?= number_format($subtotal, 2) ?></span> DH
    </button>
    <p class="checkout-secure">
      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      Paiement chiffré · données protégées
    </p>
  </form>

  <!-- ===== Summary ===== -->
  <aside class="checkout-summary">
    <h3>Récapitulatif</h3>
    <ul class="summary-items">
      <?php foreach ($items as $it): ?>
        <li>
          <div class="summary-item-name">
            <strong><?= e($it['name']) ?></strong>
            <small>Quantité : <?= (int)$it['quantity'] ?></small>
          </div>
          <span class="summary-item-price"><?= number_format($it['price']*$it['quantity'], 2) ?> DH</span>
        </li>
      <?php endforeach; ?>
    </ul>

    <dl class="summary-totals">
      <div>
        <dt>Sous-total</dt>
        <dd><?= number_format($subtotal, 2) ?> DH</dd>
      </div>
      <?php if (!empty($promo) && $discount > 0): ?>
        <div class="summary-discount">
          <dt>Remise (<?= e($promo['code']) ?>)</dt>
          <dd>- <?= number_format($discount, 2) ?> DH</dd>
        </div>
      <?php elseif (!empty($promoFreeShipping)): ?>
        <div class="summary-discount">
          <dt>Code promo (<?= e($promo['code']) ?>)</dt>
          <dd>Livraison offerte</dd>
        </div>
      <?php endif; ?>
      <div>
        <dt>Livraison</dt>
        <dd id="checkoutShippingFee"
            data-subtotal="<?= e((string)$subtotal) ?>"
            data-discount="<?= e((string)$discount) ?>"
            data-threshold="<?= e((string)$freeShippingThreshold) ?>"
            data-freeship="<?= !empty($promoFreeShipping) ? '1' : '0' ?>">—</dd>
      </div>
      <div class="summary-grand">
        <dt>Total</dt>
        <dd><span id="checkoutTotal"><?= number_format(max(0, $subtotal - $discount), 2) ?></span> DH</dd>
      </div>
    </dl>

    <div class="summary-trust">
      <div>
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span>Paiement sécurisé</span>
      </div>
      <div>
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        <span>Retour sous 14 jours</span>
      </div>
      <div>
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        <span>Suivi en temps réel</span>
      </div>
    </div>
  </aside>
</div>
