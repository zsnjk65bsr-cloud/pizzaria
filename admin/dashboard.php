<?php
require_once __DIR__ . '/../includes/init.php';

require_admin();

$page_title = 'Dashboard Admin - Smart Pizzaria';
require_once __DIR__ . '/../views/layout/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Dashboard Admin</h1>
            
            <!-- Liens rapides -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Gestion rapide</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="products.php" class="btn btn-outline-primary btn-lg w-100">
                                        <i class="fas fa-pizza-slice"></i> Gestion des pizzas
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="orders.php" class="btn btn-outline-success btn-lg w-100">
                                        <i class="fas fa-shopping-cart"></i> Gestion des commandes
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="users.php" class="btn btn-outline-info btn-lg w-100">
                                        <i class="fas fa-users"></i> Gestion des utilisateurs
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="ingredients.php" class="btn btn-outline-warning btn-lg w-100">
                                        <i class="fas fa-cheese"></i> Gestion des ingrédients
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../views/layout/footer.php'; ?>
