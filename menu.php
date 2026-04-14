<?php
require_once __DIR__ . '/includes/init.php';

require_login();

$pdo = db();

$products = $pdo->query('SELECT p.*, c.nom AS categorie_nom FROM produits p LEFT JOIN categories c ON c.id = p.categorie_id WHERE p.disponible = 1 ORDER BY p.id DESC')->fetchAll();

if (is_post() && post('action') === 'add_product') {
    $product_id = (int)post('product_id', 0);
    $taille = (string)post('taille', 'M');
    $qty = max(1, (int)post('qty', 1));

    $stmt = $pdo->prepare('SELECT * FROM produits WHERE id = ? AND disponible = 1');
    $stmt->execute([$product_id]);
    $p = $stmt->fetch();

    if (!$p) {
        flash_set('danger', 'Produit introuvable.');
        redirect('menu.php');
    }

    $taille = in_array($taille, ['S', 'M', 'L'], true) ? $taille : 'M';
    $unit_price = (float)($taille === 'S' ? $p['prix_s'] : ($taille === 'L' ? $p['prix_l'] : $p['prix_m']));

    cart_init();

    $key = 'prod:' . $product_id . ':' . $taille;

    if (!isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key] = [
            'type' => 'product',
            'product_id' => $product_id,
            'name' => $p['nom'],
            'size' => $taille,
            'unit_price' => $unit_price,
            'qty' => 0,
        ];
    }

    $_SESSION['cart'][$key]['qty'] += $qty;

    flash_set('success', 'Ajouté au panier.');
    redirect('menu.php');
}

$page_title = 'Menu — Smart Pizzaria';

render('menu/index', compact('page_title', 'products'));
