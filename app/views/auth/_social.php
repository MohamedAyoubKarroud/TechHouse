<?php
require_once APP_ROOT . '/app/services/OAuth.php';
$googleOn   = OAuth::googleEnabled();
$facebookOn = OAuth::facebookEnabled();
if (!$googleOn && !$facebookOn) return;
?>
<div class="oauth-divider"><span>ou continuer avec</span></div>
<div class="oauth-buttons">
  <?php if ($googleOn): ?>
    <a class="btn-oauth btn-google" href="<?= BASE_URL ?>/auth/google">
      <svg viewBox="0 0 48 48" width="20" height="20" aria-hidden="true">
        <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3c-1.6 4.7-6.1 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.2 6.1 29.4 4 24 4 13 4 4 13 4 24s9 20 20 20 20-9 20-20c0-1.2-.1-2.3-.4-3.5z"/>
        <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 16 19 13 24 13c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.2 6.1 29.4 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/>
        <path fill="#4CAF50" d="M24 44c5.3 0 10.1-2 13.7-5.3l-6.3-5.3c-2 1.4-4.6 2.3-7.4 2.3-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.5 39.6 16.2 44 24 44z"/>
        <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.3 4.2-4.3 5.6l6.3 5.3C40.9 36.4 44 30.7 44 24c0-1.2-.1-2.3-.4-3.5z"/>
      </svg>
      <span>Google</span>
    </a>
  <?php endif; ?>
  <?php if ($facebookOn): ?>
    <a class="btn-oauth btn-facebook" href="<?= BASE_URL ?>/auth/facebook">
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
        <path fill="#1877F2" d="M24 12a12 12 0 1 0-13.9 11.9v-8.4H7v-3.5h3.1V9.4c0-3 1.8-4.7 4.6-4.7 1.3 0 2.7.2 2.7.2v3h-1.5c-1.5 0-2 .9-2 1.9v2.2h3.4l-.5 3.5h-2.9V24A12 12 0 0 0 24 12z"/>
      </svg>
      <span>Facebook</span>
    </a>
  <?php endif; ?>
</div>
