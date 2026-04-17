<?php
require_once __DIR__ . '/../includes/init.php';

require_login();

cart_init();

if (is_post()) {
    $action = (string)post('action', '');

    if ($action === 'update_qty') {
        $key = (string)post('key', '');
        $qty = max(0, (int)post('qty', 0));
        if (isset($_SESSION['cart'][$key])) {
            if ($qty === 0) {
                unset($_SESSION['cart'][$key]);
            } else {
                $_SESSION['cart'][$key]['qty'] = $qty;
            }
        }
        redirect('cart.php');
    }

    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        flash_set('success', 'Panier vidé.');
        redirect('cart.php');
    }
}

$page_title = 'Panier — Smart Pizzaria';

render('cart/index', compact('page_title'));
