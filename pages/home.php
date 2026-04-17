<?php
require_once __DIR__ . '/../includes/init.php';

$page_title = 'Accueil - Smart Pizzaria';
require_once __DIR__ . '/../views/layout/header.php';
?>

<div class="p-4 p-md-5 mb-4 hero">
    <div class="col-lg-8 px-0">
        <h1 class="display-6 fw-bold">Smart Pizzaria</h1>
        <p class="lead my-3">E-commerce de pizzas avec une fonctionnalité unique: composer votre pizza personnalisée.
        </p>
        <div class="d-flex gap-2">
            <a class="btn btn-light btn-lg" href="/pizzaria/Client/menu.php">Commander</a>
            <a class="btn btn-outline-light btn-lg" href="/pizzaria/Client/composer.php">Composer votre pizza</a>
        </div>
        <?php if (!is_logged_in()): ?>
            <div class="mt-3 small">Login obligatoire pour commander. <a class="link-light"
                    href="/pizzaria/auth/login.php">Se connecter</a></div>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Menu dynamique</h5>
                <p class="card-text">Les produits sont chargés depuis MySQL.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Composer pizza</h5>
                <p class="card-text">Personnalisez votre pizza avec les ingrédients de votre choix.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Panier & Commande</h5>
                <p class="card-text">Session panier et validation de commande.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../views/layout/footer.php'; ?>