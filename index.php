<?php
// Front controller — all requests come here.

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/core/Security.php';
require_once __DIR__ . '/app/core/Auth.php';
require_once __DIR__ . '/app/core/Geolocation.php';
require_once __DIR__ . '/app/core/Model.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/App.php';
require_once __DIR__ . '/app/models/Cart.php';

// Log the visit (geolocation + page) — non-blocking failure-safe.
try {
    Geolocation::logVisit();
} catch (Throwable $e) {
    // Never break the page on analytics failure.
}

new App();
