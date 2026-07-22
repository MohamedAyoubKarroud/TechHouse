<?php
// Session-based cart. Stateless — no DB persistence.

class Cart
{
    public static function items()
    {
        if (isset($_SESSION['cart'])) {
            return $_SESSION['cart'];
        }
        return array();
    }

    public static function add($productId, $name, $price, $qty = 1, $image = null)
    {
        if (isset($_SESSION['cart'])) {
            $cart = $_SESSION['cart'];
        } else {
            $cart = array();
        }
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $qty;
        } else {
            $cart[$productId] = array(
                'id'       => $productId,
                'name'     => $name,
                'price'    => $price,
                'image'    => $image,
                'quantity' => $qty,
            );
        }
        $_SESSION['cart'] = $cart;
    }

    public static function updateQty($productId, $qty)
    {
        if ($qty <= 0) {
            self::remove($productId);
            return;
        }
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = $qty;
        }
    }

    public static function remove($productId)
    {
        unset($_SESSION['cart'][$productId]);
    }

    public static function clear()
    {
        $_SESSION['cart'] = array();
    }

    public static function total()
    {
        $total = 0.0;
        foreach (self::items() as $it) {
            $total += $it['price'] * $it['quantity'];
        }
        return $total;
    }

    public static function count()
    {
        $n = 0;
        foreach (self::items() as $it) {
            $n += (int)$it['quantity'];
        }
        return $n;
    }
}
