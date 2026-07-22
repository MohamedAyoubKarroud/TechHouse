<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? APP_NAME) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<?php $__cssV = @filemtime(__DIR__ . '/../../../public/css/style.css') ?: time(); ?>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css?v=<?= $__cssV ?>">
<script>
(function(){try{var t=localStorage.getItem('theme');if(!t)t=window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';document.documentElement.setAttribute('data-theme',t);}catch(e){}})();
</script>
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a class="logo" href="<?= BASE_URL ?>/">Tech<span>House</span></a>

    <form class="search" action="<?= BASE_URL ?>/search" method="get">
      <input type="text" name="q" placeholder="Rechercher des instruments, marques, disques..." value="<?= e($_GET['q'] ?? '') ?>">
      <button type="submit">Rechercher</button>
    </form>

    <nav class="main-nav">
      <a href="<?= BASE_URL ?>/product">Produits</a>
      <a href="<?= BASE_URL ?>/product/index/instruments">Instruments</a>
      <a href="<?= BASE_URL ?>/product/index/dj-equipment">DJ</a>
      <a href="<?= BASE_URL ?>/product/index/studio-gear">Studio</a>
      <a href="<?= BASE_URL ?>/product/index/vinyl">Vinyle</a>
    </nav>

    <div class="user-actions">
      <button type="button" id="themeToggle" class="theme-toggle" aria-label="Basculer le thème" title="Basculer le thème">
        <svg class="theme-icon theme-icon-moon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        <svg class="theme-icon theme-icon-sun" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
      </button>
      <a class="cart-link" href="<?= BASE_URL ?>/cart">Panier (<?= Cart::count() ?>)</a>
      <?php if (Auth::check()):
        $__u = Auth::user();
        $__name = trim((string)($__u['name'] ?? ''));
        $__parts = preg_split('/\s+/', $__name) ?: [];
        $__initials = mb_strtoupper(mb_substr($__parts[0] ?? '', 0, 1) . mb_substr($__parts[1] ?? '', 0, 1));
        if ($__initials === '') { $__initials = mb_strtoupper(mb_substr($__u['email'] ?? '?', 0, 1)); }
      ?>
        <div class="profile-menu" data-profile-menu>
          <button type="button" class="profile-trigger" id="profileTrigger" aria-haspopup="menu" aria-expanded="false" aria-label="Mon compte">
            <span class="profile-avatar"><?= e($__initials) ?></span>
          </button>
          <div class="profile-dropdown" role="menu" aria-labelledby="profileTrigger" hidden>
            <div class="profile-head">
              <span class="profile-avatar profile-avatar-lg"><?= e($__initials) ?></span>
              <div class="profile-head-info">
                <strong><?= e($__u['name']) ?></strong>
                <span><?= e($__u['email']) ?></span>
              </div>
            </div>
            <a href="<?= BASE_URL ?>/order/history" role="menuitem">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="13" y2="17"/></svg>
              Mes commandes
            </a>
            <a href="<?= BASE_URL ?>/order/track" role="menuitem">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
              Suivre une commande
            </a>
            <a href="<?= BASE_URL ?>/cart" role="menuitem">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
              Mon panier
            </a>
            <?php if (Auth::isAdmin()): ?>
              <div class="profile-divider"></div>
              <a href="<?= BASE_URL ?>/admin" role="menuitem">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Administration
              </a>
            <?php endif; ?>
            <div class="profile-divider"></div>
            <a href="<?= BASE_URL ?>/auth/logout" role="menuitem" class="profile-danger">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Déconnexion
            </a>
          </div>
        </div>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/auth/login">Connexion</a>
        <a class="btn-sm" href="<?= BASE_URL ?>/auth/register">Inscription</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="container main">
<?php if (!empty($_SESSION['flash'])): ?>
  <div class="flash"><?= e($_SESSION['flash']) ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
