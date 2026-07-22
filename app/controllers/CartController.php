<?php
require_once APP_ROOT . '/app/models/Cart.php';
require_once APP_ROOT . '/app/models/Promo.php';

class CartController extends Controller
{
    public function index()
    {
        $subtotal = Cart::total();
        $discount = Promo::discount($subtotal);
        $total    = $subtotal - $discount;
        if ($total < 0) {
            $total = 0;
        }
        $this->view('cart/index', array(
            'title'    => 'Votre panier',
            'items'    => Cart::items(),
            'subtotal' => $subtotal,
            'promo'    => Promo::applied(),
            'discount' => $discount,
            'total'    => $total,
            'catalog'  => Promo::catalog(),
        ));
    }

    public function applyPromo()
    {
        Security::verifyCsrf();
        $code = '';
        if (isset($_POST['code'])) {
            $code = $_POST['code'];
        }
        $err = Promo::apply($code);
        if ($err) {
            $_SESSION['flash'] = $err;
        } else {
            $_SESSION['flash'] = 'Code promo appliqué avec succès.';
        }
        $this->redirect('cart');
    }

    public function clearPromo()
    {
        Promo::clear();
        $_SESSION['flash'] = 'Code promo retiré.';
        $this->redirect('cart');
    }

    public function add()
    {
        Security::verifyCsrf();
        $id  = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $qty = isset($_POST['quantity'])   ? (int)$_POST['quantity']   : 1;
        if ($qty < 1) {
            $qty = 1;
        }

        $product = $this->model('Product')->find($id);
        if ($product) {
            Cart::add($product['id'], $product['name'], (float)$product['price'], $qty, $product['image']);
        }
        $this->redirect('cart');
    }

    public function update()
    {
        Security::verifyCsrf();
        $id  = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $qty = isset($_POST['quantity'])   ? (int)$_POST['quantity']   : 0;
        Cart::updateQty($id, $qty);
        $this->redirect('cart');
    }

    public function remove($id = null)
    {
        Cart::remove((int)$id);
        $this->redirect('cart');
    }
}
