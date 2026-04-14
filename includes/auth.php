<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return auth_user() !== null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function is_admin(): bool
{
    $user = auth_user();
    if (!$user) {
        return false;
    }
    $role = strtolower(trim((string)($user['role'] ?? 'client')));
    return $role === 'admin';
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        flash_set('danger', 'Accès refusé.');
        redirect('index.php');
    }
}

function login_by_email_password(string $email, string $password): bool
{
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        return false;
    }

    $hash = $user['mot_de_passe'] ?? '';

    // Compatible with provided seed data using MD5
    if (strtolower($hash) === strtolower(md5($password))) {
        unset($user['mot_de_passe']);
        $_SESSION['user'] = $user;
        return true;
    }

    return false;
}

function logout(): void
{
    unset($_SESSION['user']);
    if (isset($_SESSION['flash'])) {
        unset($_SESSION['flash']);
    }
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}
