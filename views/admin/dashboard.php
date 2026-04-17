<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0">Dashboard Admin</h1>
    <a class="btn btn-outline-dark" href="/pizzaria/auth/index.php">Aller au site</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Utilisateurs</div>
                <div class="h3 mb-0"><?= (int) $stats['users'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Commandes</div>
                <div class="h3 mb-0"><?= (int) $stats['orders'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Produits</div>
                <div class="h3 mb-0"><?= (int) $stats['products'] ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h2 class="h5">Dernières commandes</h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Statut</th>
                        <th>Livraison</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $o): ?>
                        <tr>
                            <td>#<?= (int) $o['id'] ?></td>
                            <td><?= h((string) $o['date_commande']) ?></td>
                            <td><?= h((string) $o['prenom'] . ' ' . (string) $o['nom']) ?></td>
                            <td><span class="badge text-bg-secondary"><?= h((string) $o['statut']) ?></span></td>
                            <td><?= h((string) $o['type_livraison']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>