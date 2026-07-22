<?php
// Session-based promo code helper.

class Promo
{
    public static function catalog()
    {
        return array(
            'BIENVENUE10' => array('type' => 'percent',  'value' => 10,  'min' => 0,    'cap' => 200, 'label' => '10% de remise (jusqu\'a 200 DH)'),
            'VINYLE5'     => array('type' => 'percent',  'value' => 5,   'min' => 0,    'cap' => 0,   'label' => '5% de remise sur tout le panier'),
            'STUDIO50'    => array('type' => 'fixed',    'value' => 50,  'min' => 300,  'cap' => 0,   'label' => '50 DH offerts des 300 DH'),
            'TECH100'     => array('type' => 'fixed',    'value' => 100, 'min' => 500,  'cap' => 0,   'label' => '100 DH offerts des 500 DH'),
            'FREESHIP'    => array('type' => 'shipping', 'value' => 0,   'min' => 0,    'cap' => 0,   'label' => 'Livraison standard offerte'),
        );
    }

    // Apply a code. Returns null on success, or an error string.
    public static function apply($code)
    {
        $code = strtoupper(trim($code));
        if ($code == '') {
            return 'Veuillez saisir un code promo.';
        }
        $codes = self::catalog();
        if (!isset($codes[$code])) {
            return 'Code promo invalide.';
        }
        $_SESSION['promo'] = $code;
        return null;
    }

    public static function clear()
    {
        unset($_SESSION['promo']);
    }

    // Currently-applied code with its rule, or null.
    public static function applied()
    {
        if (!isset($_SESSION['promo'])) {
            return null;
        }
        $code  = $_SESSION['promo'];
        $codes = self::catalog();
        if (!isset($codes[$code])) {
            self::clear();
            return null;
        }
        $rule = $codes[$code];
        $rule['code'] = $code;
        return $rule;
    }

    // Discount amount applied to subtotal.
    public static function discount($subtotal)
    {
        $p = self::applied();
        if (!$p) {
            return 0.0;
        }
        if ($subtotal < (float)$p['min']) {
            return 0.0;
        }
        $d = 0.0;
        if ($p['type'] === 'percent') {
            $d = $subtotal * ($p['value'] / 100);
            if ($p['cap'] > 0 && $d > $p['cap']) {
                $d = (float)$p['cap'];
            }
        } elseif ($p['type'] === 'fixed') {
            $d = (float)$p['value'];
        }
        if ($d > $subtotal) {
            $d = $subtotal;
        }
        return round($d, 2);
    }

    public static function isFreeShipping()
    {
        $p = self::applied();
        if ($p !== null && $p['type'] === 'shipping') {
            return true;
        }
        return false;
    }

    // True if applied code's min subtotal is met.
    public static function isActive($subtotal)
    {
        $p = self::applied();
        if (!$p) {
            return false;
        }
        if ($subtotal >= (float)$p['min']) {
            return true;
        }
        return false;
    }
}
