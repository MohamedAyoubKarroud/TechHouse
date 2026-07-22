<?php
// Application configuration constants.

define('APP_NAME',  'TechHouse');
define('APP_ROOT',  dirname(__DIR__));
define('BASE_URL',  '/techhouse');   // matches RewriteBase in .htaccess
define('UPLOADS',   APP_ROOT . '/public/uploads');

// Default controller / action used by the router.
define('DEFAULT_CONTROLLER', 'home');
define('DEFAULT_ACTION',     'index');

// Optional API key for AI categorization (OpenAI / Anthropic). Leave empty to use heuristic fallback.
define('AI_API_KEY',  '');
define('AI_PROVIDER', 'heuristic'); // 'heuristic' | 'openai' | 'anthropic'

// ---------- OAUTH (Google + Facebook social login) ----------
// Register your apps and paste the credentials below. Leave empty to hide the buttons.
//
// GOOGLE  -> https://console.cloud.google.com/apis/credentials
//   Authorized redirect URI to register: http://localhost/techhouse/auth/googleCallback
//
// FACEBOOK -> https://developers.facebook.com/apps
//   Valid OAuth Redirect URI to register: http://localhost/techhouse/auth/facebookCallback
//
define('GOOGLE_CLIENT_ID',     '1022655846706-cehf40e582akfkane7hij274uj3bds1d.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI',  'http://localhost' . BASE_URL . '/auth/googleCallback');

define('FACEBOOK_APP_ID',      '');
define('FACEBOOK_APP_SECRET',  '');
define('FACEBOOK_REDIRECT_URI','http://localhost' . BASE_URL . '/auth/facebookCallback');

// Error reporting — disable in production.
error_reporting(E_ALL);
ini_set('display_errors', '1');
