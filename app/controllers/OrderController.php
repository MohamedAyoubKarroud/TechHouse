<?php
require_once APP_ROOT . '/app/models/Promo.php';

class OrderController extends Controller
{
    public function checkout()
    {
        $this->requireAuth();
        $items = Cart::items();
        if (!$items) {
            $this->redirect('cart');
        }

        $subtotal = Cart::total();
        $promo    = Promo::applied();
        $discount = Promo::discount($subtotal);
        $promoFreeShipping = Promo::isFreeShipping();
        $freeShippingThreshold = 1000.0;

        $shippingRates = array(
            'standard' => array('label' => 'Standard',  'fee' => 50.0,  'eta' => '3-5 jours ouvrés'),
            'express'  => array('label' => 'Express',   'fee' => 100.0, 'eta' => '1-2 jours ouvrés'),
            'pickup'   => array('label' => 'Retrait en magasin', 'fee' => 0.0, 'eta' => 'sous 24 h'),
        );
        $paymentMethods = array(
            'cod'      => 'Paiement à la livraison',
            'card'     => 'Carte bancaire',
            'transfer' => 'Virement bancaire',
            'paypal'   => 'PayPal',
        );

        $error = null;

        // Pre-fill from the logged-in user (split name on the first space).
        $authName = '';
        $authEmail = '';
        $authUser = Auth::user();
        if ($authUser) {
            if (isset($authUser['name']))  { $authName  = trim($authUser['name']); }
            if (isset($authUser['email'])) { $authEmail = $authUser['email']; }
        }
        $firstName = '';
        $lastName  = '';
        if ($authName != '') {
            $space = strpos($authName, ' ');
            if ($space === false) {
                $firstName = $authName;
            } else {
                $firstName = substr($authName, 0, $space);
                $lastName  = trim(substr($authName, $space + 1));
            }
        }

        $form = array(
            'first_name'  => $firstName,
            'last_name'   => $lastName,
            'email'       => $authEmail,
            'phone'       => '',
            'address'     => '',
            'address2'    => '',
            'city'        => '',
            'postal'      => '',
            'country'     => 'Maroc',
            'shipping'    => 'standard',
            'payment'     => 'cod',
            'notes'       => '',
            'terms'       => false,
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf();

            // Read raw POST values (allowed methods only).
            $form['first_name'] = isset($_POST['first_name']) ? $_POST['first_name'] : '';
            $form['last_name']  = isset($_POST['last_name'])  ? $_POST['last_name']  : '';
            $form['email']      = isset($_POST['email'])      ? $_POST['email']      : '';
            $form['phone']      = isset($_POST['phone'])      ? $_POST['phone']      : '';
            $form['address']    = isset($_POST['address'])    ? $_POST['address']    : '';
            $form['address2']   = isset($_POST['address2'])   ? $_POST['address2']   : '';
            $form['city']       = isset($_POST['city'])       ? $_POST['city']       : '';
            $form['postal']     = isset($_POST['postal'])     ? $_POST['postal']     : '';
            $form['country']    = isset($_POST['country'])    ? $_POST['country']    : '';
            $form['shipping']   = isset($_POST['shipping'])   ? $_POST['shipping']   : '';
            $form['payment']    = isset($_POST['payment'])    ? $_POST['payment']    : '';
            $form['notes']      = isset($_POST['notes'])      ? $_POST['notes']      : '';
            $form['terms']      = isset($_POST['terms']);

            $errors = array();

            // Prénom : lettres uniquement.
            if ($form['first_name'] == '' || !preg_match("/^[a-zA-ZÀ-ÿ\s\-]+$/", $form['first_name'])) {
                $errors[] = 'Le prénom doit contenir uniquement des lettres.';
            }
            // Nom : lettres uniquement.
            if ($form['last_name'] == '' || !preg_match("/^[a-zA-ZÀ-ÿ\s\-]+$/", $form['last_name'])) {
                $errors[] = 'Le nom doit contenir uniquement des lettres.';
            }
            // Email.
            if ($form['email'] == '' || !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $form['email'])) {
                $errors[] = "L'adresse mail n'est pas valide.";
            }
            // Téléphone : chiffres et séparateurs, au moins 6 caractères.
            if ($form['phone'] == '' || !preg_match("/^[0-9\s+().\-]{6,}$/", $form['phone'])) {
                $errors[] = 'Le téléphone est invalide.';
            }
            // Adresse : au moins 5 caractères.
            if ($form['address'] == '' || strlen($form['address']) < 5) {
                $errors[] = "L'adresse doit contenir au moins 5 caractères.";
            }
            // Ville : lettres uniquement.
            if ($form['city'] == '' || !preg_match("/^[a-zA-ZÀ-ÿ\s\-]+$/", $form['city'])) {
                $errors[] = 'La ville est invalide.';
            }
            // Code postal : alphanumérique court.
            if ($form['postal'] == '' || !preg_match("/^[A-Za-z0-9 \-]{3,12}$/", $form['postal'])) {
                $errors[] = 'Le code postal est invalide.';
            }
            // Pays.
            if (!isset($_POST['country']) || $form['country'] == '') {
                $errors[] = 'Le pays doit être sélectionné.';
            }
            // Mode de livraison.
            if (!isset($_POST['shipping']) || !isset($shippingRates[$form['shipping']])) {
                $errors[] = 'Le mode de livraison doit être sélectionné.';
            }
            // Mode de paiement.
            if (!isset($_POST['payment']) || !isset($paymentMethods[$form['payment']])) {
                $errors[] = 'Le mode de paiement doit être sélectionné.';
            }
            // Notes : limite de 500 caractères.
            if ($form['notes'] != '' && strlen($form['notes']) > 500) {
                $errors[] = 'Les notes ne peuvent pas dépasser 500 caractères.';
            }
            // Conditions générales.
            if (!$form['terms']) {
                $errors[] = 'Vous devez accepter les conditions générales.';
            }

            if (!$errors) {
                $shippingFee = $shippingRates[$form['shipping']]['fee'];
                if ($subtotal >= $freeShippingThreshold && $form['shipping'] !== 'express') {
                    $shippingFee = 0.0;
                }
                if ($promoFreeShipping && $form['shipping'] === 'standard') {
                    $shippingFee = 0.0;
                }
                $afterDiscount = $subtotal - $discount;
                if ($afterDiscount < 0) {
                    $afterDiscount = 0;
                }
                $total = $afterDiscount + $shippingFee;

                $fullName = $form['first_name'] . ' ' . $form['last_name'];
                $line1 = $form['address'];
                if ($form['address2'] !== '') {
                    $line1 .= ', ' . $form['address2'];
                }
                $cityLine = $form['postal'] . ' ' . $form['city'] . ', ' . $form['country'];

                $addrParts = array(
                    $fullName,
                    'Tél: ' . $form['phone'],
                    $line1,
                    $cityLine,
                    'Livraison: ' . $shippingRates[$form['shipping']]['label'],
                    'Paiement: ' . $paymentMethods[$form['payment']],
                );
                if ($promo) {
                    $promoLine = 'Promo: ' . $promo['code'];
                    if ($discount > 0) {
                        $promoLine .= ' (-' . number_format($discount, 2) . ' DH)';
                    }
                    $addrParts[] = $promoLine;
                }
                if ($form['notes'] !== '') {
                    $addrParts[] = 'Notes: ' . substr($form['notes'], 0, 60);
                }
                $shippingAddr = substr(implode(' | ', $addrParts), 0, 255);

                $orderModel   = $this->model('Order');
                $productModel = $this->model('Product');

                $orderId = $orderModel->create(Auth::id(), $items, $shippingAddr, $total);
                foreach ($items as $it) {
                    $productModel->decrementStock((int)$it['id'], (int)$it['quantity']);
                }
                Cart::clear();
                Promo::clear();
                $this->redirect('order/confirmation/' . $orderId);
                return;
            }
            $error = implode(' ', $errors);
        }

        $viewTotal = $subtotal - $discount;
        if ($viewTotal < 0) {
            $viewTotal = 0;
        }

        $this->view('orders/checkout', array(
            'title'                 => 'Paiement',
            'items'                 => $items,
            'subtotal'              => $subtotal,
            'total'                 => $viewTotal,
            'shippingRates'         => $shippingRates,
            'paymentMethods'        => $paymentMethods,
            'freeShippingThreshold' => $freeShippingThreshold,
            'form'                  => $form,
            'error'                 => $error,
            'promo'                 => $promo,
            'discount'              => $discount,
            'promoFreeShipping'     => $promoFreeShipping,
        ));
    }

    public function confirmation($id = null)
    {
        $this->requireAuth();
        $order = $this->model('Order')->find((int)$id);
        if (!$order || $order['user_id'] != Auth::id()) {
            $this->redirect('');
        }
        $this->view('orders/confirmation', array(
            'title' => 'Confirmation de commande',
            'order' => $order,
            'items' => $this->model('Order')->items((int)$id),
        ));
    }

    public function history()
    {
        $this->requireAuth();
        $this->view('orders/history', array(
            'title'  => 'Mes commandes',
            'orders' => $this->model('Order')->forUser(Auth::id()),
        ));
    }

    public function track($code = null)
    {
        $order = null;
        $items = array();
        if ($code) {
            $order = $this->model('Order')->findByTracking($code);
            if ($order) {
                $items = $this->model('Order')->items((int)$order['id']);
            }
        }
        $this->view('orders/track', array(
            'title' => 'Suivre la commande',
            'order' => $order,
            'items' => $items,
            'code'  => $code,
        ));
    }
}
