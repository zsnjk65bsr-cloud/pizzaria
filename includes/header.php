<?php

if (!isset($page_title)) {
    $page_title = 'Smart Pizzaria';
}

$flash_messages = flash_get_all();
$user = auth_user();

?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Smart Pizzaria</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="composer.php">Composer votre pizza</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php">Panier</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if ($user): ?>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    <?php foreach ($flash_messages as $m): ?>
        <div class="alert alert-<?= h($m['type']) ?>" role="alert"><?= h($m['message']) ?></div>
    <?php endforeach; ?>
