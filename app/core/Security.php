<?php
// CSRF tokens + output escaping helpers.

class Security
{
    public static function csrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf()
    {
        $token = '';
        if (isset($_POST['_token'])) {
            $token = $_POST['_token'];
        } elseif (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        }
        $valid = !empty($_SESSION['csrf_token'])
              && hash_equals($_SESSION['csrf_token'], $token);
        if (!$valid) {
            http_response_code(419);
            echo 'CSRF token mismatch.';
            exit;
        }
        return true;
    }

    public static function e($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function clean($value)
    {
        return trim(strip_tags($value));
    }
}

// Convenience global for views.
function e($v) { return Security::e($v); }
