<div class="auth-split">
  <aside class="auth-brand">
    <div class="auth-brand-inner">
      <a class="auth-logo" href="<?= BASE_URL ?>/">Tech<span>House</span></a>
      <h2>Bon retour parmi nous.</h2>
      <p>Reprenez là où vous vous êtes arrêté — votre panier, vos commandes, vos favoris.</p>
      <ul class="auth-perks">
        <li><span>01</span> Recommandez vos produits favoris en un clic</li>
        <li><span>02</span> Suivez chaque commande en temps réel</li>
        <li><span>03</span> Accès exclusif aux nouveautés et réassorts</li>
      </ul>
    </div>
  </aside>

  <div class="auth-card">
    <h1>Connexion</h1>
    <p class="auth-sub">Entrez vos identifiants pour accéder à votre compte.</p>
    <?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="<?= BASE_URL ?>/auth/login">
      <input type="hidden" name="_token" value="<?= Security::csrfToken() ?>">
      <label>E-mail
        <input type="email" name="email" required autocomplete="email" placeholder="vous@exemple.com">
      </label>
      <label>Mot de passe
        <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
      </label>
      <button class="btn btn-primary btn-block" type="submit">Se connecter</button>
    </form>

    <?php require APP_ROOT . '/app/views/auth/_social.php'; ?>

    <p class="auth-foot">Pas encore de compte ? <a href="<?= BASE_URL ?>/auth/register">Créer un compte</a>.</p>
  </div>
</div>
