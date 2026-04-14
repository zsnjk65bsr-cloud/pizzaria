<?php
require_once __DIR__ . '/includes/init.php';

require_login();

$user = auth_user();
$pdo = db();

if (is_post() && post('action') === 'update_profile') {
    $nom = trim((string)post('nom', ''));
    $prenom = trim((string)post('prenom', ''));
    $telephone = trim((string)post('telephone', ''));
    $adresse = trim((string)post('adresse', ''));

    if ($nom === '' || $prenom === '') {
        flash_set('warning', 'Nom et prénom sont obligatoires.');
        redirect('profile.php');
    }

    try {
        $stmt = $pdo->prepare('UPDATE users SET nom=?, prenom=?, telephone=?, adresse=? WHERE id=?');
        $stmt->execute([$nom, $prenom, $telephone ?: null, $adresse ?: null, (int)$user['id']]);

        $_SESSION['user']['nom'] = $nom;
        $_SESSION['user']['prenom'] = $prenom;
        $_SESSION['user']['telephone'] = $telephone;
        $_SESSION['user']['adresse'] = $adresse;

        flash_set('success', 'Profil mis à jour.');
        redirect('profile.php');
    } catch (Throwable $e) {
        flash_set('danger', 'Erreur lors de la mise à jour du profil.');
        redirect('profile.php');
    }
}

$stmt = $pdo->prepare('SELECT * FROM commandes WHERE user_id = ? ORDER BY date_commande DESC');
$stmt->execute([(int)$user['id']]);
$orders = $stmt->fetchAll();

$page_title = 'Profil — Smart Pizzaria';

render('profile/index', compact('page_title', 'user', 'orders'));
