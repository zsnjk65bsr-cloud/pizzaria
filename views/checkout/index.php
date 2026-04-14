<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0">Commande</h1>
    <div class="text-muted">Total: <span class="fw-semibold"><?= number_format(cart_total(), 2) ?> DT</span></div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="action" value="place_order">

                    <div class="mb-3">
                        <label class="form-label">Type de livraison</label>
                        <select class="form-select" name="type_livraison">
                            <option value="livraison">Livraison</option>
                            <option value="sur_place">Sur place</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adresse de livraison</label>
                        <input name="adresse_livraison" class="form-control" value="<?= h((string)($user['adresse'] ?? '')) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Téléphone *</label>
                        <input name="telephone" class="form-control" required value="<?= h((string)($user['telephone'] ?? '')) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note_client" class="form-control" rows="3"></textarea>
                    </div>

                    <button class="btn btn-dark btn-lg w-100" type="submit">Valider la commande</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h2 class="h5">Récapitulatif</h2>
                <hr>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fw-semibold"><?= h((string)($item['name'] ?? '')) ?></div>
                            <div class="text-muted small"><?= h((string)($item['size'] ?? '')) ?> • x<?= (int)($item['qty'] ?? 1) ?></div>
                        </div>
                        <div class="fw-semibold"><?= number_format(((float)($item['unit_price'] ?? 0)) * (int)($item['qty'] ?? 1), 2) ?> DT</div>
                    </div>
                    <div class="small text-muted mb-2">
                        <?php if (($item['type'] ?? '') === 'custom'): ?>
                            Pâte: <?= h((string)($item['type_pate_name'] ?? '')) ?><br>
                            Ingrédients: <?= h(implode(', ', $item['ingredient_names'] ?? [])) ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between">
                    <div>Total</div>
                    <div class="h5 mb-0"><?= number_format(cart_total(), 2) ?> DT</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
