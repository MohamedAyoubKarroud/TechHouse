<section class="hero">
  <div class="hero-text">
    <span class="hero-eyebrow"><span class="hero-eyebrow-dot"></span> Nouveau · Collection 2026</span>
    <h1>Le son qui vous <em>fait vibrer.</em></h1>
    <p>Instruments, équipements DJ, matériel de studio et vinyles rares — sélectionnés pour tous, des producteurs amateurs aux pros en tournée.</p>
    <div class="hero-cta">
      <a class="btn btn-primary" href="<?= BASE_URL ?>/product">Découvrir le matériel <span aria-hidden="true">→</span></a>
      <a class="btn btn-outline-light" href="<?= BASE_URL ?>/product/index/vinyl">Explorer les vinyles</a>
    </div>
    <ul class="hero-trust">
      <li><strong>50+</strong><span>marques premium</span></li>
      <li><strong>24 h</strong><span>livraison express</span></li>
      <li><strong>30 j</strong><span>retours gratuits</span></li>
    </ul>
  </div>

  <div class="hero-visual" aria-hidden="true">
    <div class="hero-eq">
      <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
    </div>
    <div class="hero-disc">
      <div class="hero-disc-grooves"></div>
      <div class="hero-disc-label">
        <span class="hero-disc-label-top">Tech<i>House</i></span>
        <span class="hero-disc-label-bot">Vol. 01</span>
      </div>
      <div class="hero-disc-hole"></div>
    </div>
    <div class="hero-arm">
      <div class="hero-arm-pivot"></div>
      <div class="hero-arm-rod"></div>
      <div class="hero-arm-head"></div>
    </div>
  </div>

  <a class="hero-scroll" href="#categories" aria-label="Faire défiler">
    <span>Défiler</span>
    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>
  </a>
</section>

<section id="categories" class="rubrics">
  <h2>Parcourir par rubrique</h2>
  <div class="rubric-grid">
    <?php foreach ($categories as $c): ?>
      <a class="rubric-card" href="<?= BASE_URL ?>/product/index/<?= e($c['slug']) ?>">
        <h3><?= e($c['name']) ?></h3>
        <p><?= e($c['description']) ?></p>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="featured">
  <h2>Nouveautés</h2>
  <div class="product-grid">
    <?php foreach ($featured as $p): ?>
      <a class="product-card" href="<?= BASE_URL ?>/product/show/<?= (int)$p['id'] ?>">
        <div class="product-image">
          <?php if (!empty($p['image']) && file_exists(UPLOADS . '/' . $p['image'])): ?>
            <img src="<?= BASE_URL ?>/public/uploads/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
          <?php else: ?>
            <div class="placeholder"><?= e($p['brand'] ?? 'TechHouse') ?></div>
          <?php endif; ?>
          <?php if (!empty($p['is_new'])): ?><span class="badge-new">NOUVEAU</span><?php endif; ?>
        </div>
        <h4><?= e($p['name']) ?></h4>
        <p class="brand"><?= e($p['brand']) ?></p>
        <p class="price"><?= number_format($p['price'], 2) ?> DH</p>
      </a>
    <?php endforeach; ?>
  </div>
</section>
