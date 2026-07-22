<div class="auth-split">
  <aside class="auth-brand">
    <div class="auth-brand-inner">
      <a class="auth-logo" href="<?= BASE_URL ?>/">Tech<span>House</span></a>
      <h2>Le son qui vous fait vibrer.</h2>
      <p>Rejoignez des milliers de producteurs, DJs et collectionneurs qui construisent leur set-up de rêve avec TechHouse.</p>
      <ul class="auth-perks">
        <li><span>01</span> Instruments &amp; vinyles rares sélectionnés</li>
        <li><span>02</span> Retours gratuits sur chaque commande</li>
        <li><span>03</span> Accès anticipé aux nouvelles collections</li>
      </ul>
    </div>
  </aside>

  <div class="auth-card">
    <h1>Créer votre compte</h1>
    <p class="auth-sub">Une minute suffit. Aucune carte bancaire requise.</p>
    <?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="<?= BASE_URL ?>/auth/register">
      <input type="hidden" name="_token" value="<?= Security::csrfToken() ?>">
      <label>Nom complet <input type="text" name="name" required placeholder="Jean Dupont"></label>
      <label>E-mail <input type="email" name="email" required placeholder="vous@exemple.com"></label>
      <div class="form-row">
        <label>Mot de passe <input type="password" name="password" required minlength="6" placeholder="6 caractères minimum"></label>
        <label>Confirmer le mot de passe <input type="password" name="confirm" required minlength="6" placeholder="Répéter le mot de passe"></label>
      </div>
      <button class="btn btn-primary btn-block" type="submit">S'inscrire</button>
    </form>

    <?php require APP_ROOT . '/app/views/auth/_social.php'; ?>

    <p class="auth-foot">Déjà un compte ? <a href="<?= BASE_URL ?>/auth/login">Se connecter</a>.</p>
  </div>
</div>
