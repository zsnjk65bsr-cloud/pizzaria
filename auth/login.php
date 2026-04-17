<?php
require_once __DIR__ . '/../includes/init.php';

if (is_logged_in()) {
    redirect('/pizzaria/auth/index.php');
}

if (is_post()) {
    $email = trim((string) post('email', ''));
    $password = (string) post('password', '');

    if ($email === '' || $password === '') {
        flash_set('warning', 'Veuillez remplir tous les champs.');
    } elseif (login_by_email_password($email, $password)) {
        flash_set('success', 'Connexion réussie.');
        if (is_admin()) {
            redirect('/pizzaria/admin/dashboard.php');
        }
        redirect('/pizzaria/Client/menu.php');
    } else {
        flash_set('danger', 'Email ou mot de passe incorrect.');
    }
}

$page_title = 'Login — Smart Pizzaria';

render('auth/login', compact('page_title'));
