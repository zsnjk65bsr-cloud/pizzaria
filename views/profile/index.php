<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0">Profil</h1>
    <a class="btn btn-outline-dark" href="menu.php">Retour menu</a>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h2 class="h5">Mes informations</h2>
                <form method="post">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input class="form-control" name="nom" required value="<?= h((string)($user['nom'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom *</label>
                            <input class="form-control" name="prenom" required value="<?= h((string)($user['prenom'] ?? '')) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input class="form-control" value="<?= h((string)($user['email'] ?? '')) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Téléphone</label>
                        <input class="form-control" name="telephone" value="<?= h((string)($user['telephone'] ?? '')) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adresse</label>
                        <input class="form-control" name="adresse" value="<?= h((string)($user['adresse'] ?? '')) ?>">
                    </div>

                    <button class="btn btn-dark w-100" type="submit">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <h2 class="h5">Historique commandes</h2>
                <?php if (empty($orders)): ?>
                    <div class="text-muted">Aucune commande.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Livraison</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td>#<?= (int)$o['id'] ?></td>
                                    <td><?= h((string)$o['date_commande']) ?></td>
                                    <td><span class="badge text-bg-secondary"><?= h((string)$o['statut']) ?></span></td>
                                    <td><?= h((string)$o['type_livraison']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
