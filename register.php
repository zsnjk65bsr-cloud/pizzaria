<?php
require_once __DIR__ . '/includes/init.php';

if (is_logged_in()) {
    redirect('index.php');
}

if (is_post()) {
    $nom = trim((string)post('nom', ''));
    $prenom = trim((string)post('prenom', ''));
    $email = trim((string)post('email', ''));
    $password = (string)post('password', '');
    $telephone = trim((string)post('telephone', ''));
    $adresse = trim((string)post('adresse', ''));

    if ($nom === '' || $prenom === '' || $email === '' || $password === '') {
        flash_set('warning', 'Veuillez remplir les champs obligatoires.');
    } else {
        try {
            $pdo = db();
            $stmt = $pdo->prepare('INSERT INTO users (nom, prenom, email, mot_de_passe, telephone, adresse, role) VALUES (?,?,?,?,?,?,\'client\')');
            $stmt->execute([$nom, $prenom, $email, md5($password), $telephone ?: null, $adresse ?: null]);
            flash_set('success', 'Compte créé. Vous pouvez vous connecter.');
            redirect('login.php');
        } catch (Throwable $e) {
            flash_set('danger', 'Impossible de créer le compte (email peut-être déjà utilisé).');
        }
    }
}

$page_title = 'Inscription — Smart Pizzaria';

render('auth/register', compact('page_title'));
