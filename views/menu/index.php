<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0">Menu</h1>
    <a class="btn btn-outline-dark" href="/pizzaria/Client/cart.php">Voir panier (<?= number_format(cart_total(), 2) ?>
        DT)</a>
</div>

<div class="row g-4">
    <?php foreach ($products as $p): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <?php if (!empty($p['image'])): ?>
                    <img class="card-img-top" alt="<?= h($p['nom']) ?>" src="/pizzaria/assets/images/<?= h($p['image']) ?>">
                <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted">No image
                    </div>
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-1"><?= h($p['nom']) ?></h5>
                            <div class="text-muted small"><?= h($p['categorie_nom'] ?? '') ?></div>
                        </div>
                        <span class="badge text-bg-dark">S <?= number_format((float) $p['prix_s'], 2) ?> DT</span>
                    </div>
                    <p class="card-text mt-2 mb-3"><?= h($p['description'] ?? '') ?></p>

                    <form class="mt-auto" method="post">
                        <input type="hidden" name="action" value="add_product">
                        <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                        <div class="row g-2">
                            <div class="col-5">
                                <select name="taille" class="form-select" required>
                                    <option value="S">S (<?= number_format((float) $p['prix_s'], 2) ?>)</option>
                                    <option value="M" selected>M (<?= number_format((float) $p['prix_m'], 2) ?>)</option>
                                    <option value="L">L (<?= number_format((float) $p['prix_l'], 2) ?>)</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="number" name="qty" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="col-4 d-grid">
                                <button class="btn btn-dark" type="submit">Ajouter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>