<?php
// Session-based authentication helpers.

class Auth
{
    public static function login($user)
    {
        session_regenerate_id(true);
        $_SESSION['user'] = array(
            'id'    => (int)$user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        );
    }

    public static function logout()
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    public static function check()
    {
        return isset($_SESSION['user']);
    }

    public static function user()
    {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        }
        return null;
    }

    public static function id()
    {
        if (isset($_SESSION['user']['id'])) {
            return $_SESSION['user']['id'];
        }
        return null;
    }

    public static function isAdmin()
    {
        if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
            return true;
        }
        return false;
    }
}
