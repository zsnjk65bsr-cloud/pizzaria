<?php
require_once __DIR__ . '/../includes/init.php';

require_login();
cart_init();

if (empty($_SESSION['cart'])) {
    flash_set('info', 'Votre panier est vide.');
    redirect('menu.php');
}

$user = auth_user();
$pdo = db();

if (is_post() && post('action') === 'place_order') {
    $type_livraison = (string)post('type_livraison', 'livraison');
    $type_livraison = in_array($type_livraison, ['livraison', 'sur_place'], true) ? $type_livraison : 'livraison';

    $adresse = trim((string)post('adresse_livraison', ''));
    $telephone = trim((string)post('telephone', ''));
    $note = trim((string)post('note_client', ''));

    if ($telephone === '') {
        flash_set('warning', 'Veuillez saisir votre téléphone.');
        redirect('checkout.php');
    }

    if ($type_livraison === 'livraison' && $adresse === '') {
        flash_set('warning', 'Veuillez saisir une adresse de livraison.');
        redirect('checkout.php');
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare('INSERT INTO commandes (user_id, type_livraison, adresse_livraison, telephone, note_client) VALUES (?,?,?,?,?)');
        $stmt->execute([(int)$user['id'], $type_livraison, $type_livraison === 'livraison' ? $adresse : null, $telephone, $note ?: null]);
        $commande_id = (int)$pdo->lastInsertId();

        foreach ($_SESSION['cart'] as $item) {
            $qty = max(1, (int)($item['qty'] ?? 1));
            $taille = (string)($item['size'] ?? 'M');
            $taille = in_array($taille, ['S', 'M', 'L'], true) ? $taille : 'M';

            if (($item['type'] ?? '') === 'product') {
                $stmt = $pdo->prepare('INSERT INTO commande_details (commande_id, produit_id, taille, quantite, est_personnalisee) VALUES (?,?,?,?,0)');
                $stmt->execute([$commande_id, (int)$item['product_id'], $taille, $qty]);
                continue;
            }

            if (($item['type'] ?? '') === 'custom') {
                $stmt = $pdo->prepare('INSERT INTO commande_details (commande_id, produit_id, taille, quantite, est_personnalisee) VALUES (?,?,?,?,1)');
                $stmt->execute([$commande_id, null, $taille, $qty]);
                $detail_id = (int)$pdo->lastInsertId();

                $stmt = $pdo->prepare('INSERT INTO pizza_personnalisee (detail_id, type_pate_id) VALUES (?,?)');
                $stmt->execute([$detail_id, (int)$item['type_pate_id']]);
                $pizza_perso_id = (int)$pdo->lastInsertId();

                $ingredient_ids = $item['ingredient_ids'] ?? [];
                if (is_array($ingredient_ids) && count($ingredient_ids) > 0) {
                    $stmt = $pdo->prepare('INSERT INTO pizza_personnalisee_ingredients (pizza_perso_id, ingredient_id) VALUES (?,?)');
                    foreach ($ingredient_ids as $ing_id) {
                        $stmt->execute([$pizza_perso_id, (int)$ing_id]);
                    }
                }
                continue;
            }
        }

        $pdo->commit();

        $_SESSION['cart'] = [];
        flash_set('success', 'Commande enregistrée. Merci!');
        redirect('profile.php');
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        flash_set('danger', 'Erreur lors de la validation de la commande.');
        redirect('checkout.php');
    }
}

$page_title = 'Checkout — Smart Pizzaria';

render('checkout/index', compact('page_title', 'user'));
