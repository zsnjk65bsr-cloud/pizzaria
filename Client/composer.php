<?php
require_once __DIR__ . '/../includes/init.php';

require_login();

$pdo = db();

$ingredients = $pdo->query('SELECT * FROM ingredients WHERE disponible = 1 ORDER BY nom ASC')->fetchAll();
$types_pate = $pdo->query('SELECT * FROM types_pate ORDER BY nom ASC')->fetchAll();

$config = require __DIR__ . '/../config/config.php';
$base_prices = $config['custom_pizza_base_price'];

if (is_post() && post('action') === 'add_custom') {
    $taille = (string)post('taille', 'M');
    $taille = in_array($taille, ['S', 'M', 'L'], true) ? $taille : 'M';

    $type_pate_id = (int)post('type_pate_id', 0);
    $selected_ingredients = post('ingredients', []);
    if (!is_array($selected_ingredients)) {
        $selected_ingredients = [];
    }
    $selected_ingredients = array_values(array_unique(array_map('intval', $selected_ingredients)));

    $qty = max(1, (int)post('qty', 1));

    $stmt = $pdo->prepare('SELECT * FROM types_pate WHERE id = ?');
    $stmt->execute([$type_pate_id]);
    $pate = $stmt->fetch();
    if (!$pate) {
        flash_set('warning', 'Veuillez choisir un type de pâte.');
        redirect('composer.php');
    }

    $unit_price = (float)$base_prices[$taille];
    $unit_price += (float)($pate['prix_supplementaire'] ?? 0);

    $ingredient_rows = [];
    $ingredient_sum = 0.0;

    if (count($selected_ingredients) > 0) {
        $in = implode(',', array_fill(0, count($selected_ingredients), '?'));
        $stmt = $pdo->prepare("SELECT * FROM ingredients WHERE id IN ($in) AND disponible = 1");
        $stmt->execute($selected_ingredients);
        $ingredient_rows = $stmt->fetchAll();
        foreach ($ingredient_rows as $ing) {
            $ingredient_sum += (float)($ing['prix_supplementaire'] ?? 0);
        }
    }

    $unit_price += $ingredient_sum;
    $unit_price = round($unit_price, 2);

    cart_init();

    $key_parts = [
        'custom',
        $taille,
        $type_pate_id,
        implode('-', $selected_ingredients),
    ];
    $key = implode(':', $key_parts);

    if (!isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key] = [
            'type' => 'custom',
            'name' => 'Pizza personnalisée',
            'size' => $taille,
            'type_pate_id' => $type_pate_id,
            'type_pate_name' => $pate['nom'],
            'ingredient_ids' => $selected_ingredients,
            'ingredient_names' => array_map(fn($r) => $r['nom'], $ingredient_rows),
            'unit_price' => $unit_price,
            'qty' => 0,
        ];
    }

    $_SESSION['cart'][$key]['qty'] += $qty;

    flash_set('success', 'Pizza personnalisée ajoutée au panier.');
    redirect('composer.php');
}

$page_title = 'Composer votre pizza — Smart Pizzaria';

render('composer/index', compact('page_title', 'ingredients', 'types_pate', 'base_prices'));
