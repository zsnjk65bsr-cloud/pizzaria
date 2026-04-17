<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0">Composer votre pizza</h1>
    <a class="btn btn-outline-dark" href="/pizzaria/Client/cart.php">Panier (<?= number_format(cart_total(), 2) ?>
        DT)</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="action" value="add_custom">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Taille</label>
                    <select class="form-select" name="taille" required>
                        <option value="S">S (base <?= number_format((float) $base_prices['S'], 2) ?> DT)</option>
                        <option value="M" selected>M (base <?= number_format((float) $base_prices['M'], 2) ?> DT)
                        </option>
                        <option value="L">L (base <?= number_format((float) $base_prices['L'], 2) ?> DT)</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Type de pâte</label>
                    <select class="form-select" name="type_pate_id" required>
                        <option value="">-- Choisir --</option>
                        <?php foreach ($types_pate as $t): ?>
                            <option value="<?= (int) $t['id'] ?>"><?= h($t['nom']) ?>
                                (+<?= number_format((float) $t['prix_supplementaire'], 2) ?> DT)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Quantité</label>
                    <input type="number" name="qty" class="form-control" min="1" value="1" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Ingrédients</label>
                    <div class="row g-2">
                        <?php foreach ($ingredients as $ing): ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ingredients[]"
                                        value="<?= (int) $ing['id'] ?>" id="ing<?= (int) $ing['id'] ?>">
                                    <label class="form-check-label" for="ing<?= (int) $ing['id'] ?>">
                                        <?= h($ing['nom']) ?> (+<?= number_format((float) $ing['prix_supplementaire'], 2) ?>
                                        DT)
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-text">Le prix est calculé automatiquement au moment de l'ajout au panier.</div>
                </div>

                <div class="col-12 d-grid">
                    <button class="btn btn-dark btn-lg" type="submit">Ajouter au panier</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>