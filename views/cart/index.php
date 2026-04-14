<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0">Panier</h1>
    <form method="post">
        <input type="hidden" name="action" value="clear">
        <button class="btn btn-outline-danger" type="submit" <?= empty($_SESSION['cart']) ? 'disabled' : '' ?>>Vider</button>
    </form>
</div>

<?php if (empty($_SESSION['cart'])): ?>
    <div class="alert alert-info">Votre panier est vide. <a href="menu.php">Voir le menu</a></div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Taille</th>
                        <th>Pâte</th>
                        <th>Ingrédients</th>
                        <th class="text-end">Prix</th>
                        <th style="width:160px;" class="text-end">Qté</th>
                        <th class="text-end">Sous-total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($_SESSION['cart'] as $key => $item): ?>
                        <?php
                        $qty = (int)($item['qty'] ?? 1);
                        $unit = (float)($item['unit_price'] ?? 0);
                        $subtotal = round($qty * $unit, 2);
                        ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= h($item['name'] ?? '') ?></div>
                                <div class="text-muted small"><?= h($item['type'] === 'custom' ? 'Personnalisée' : 'Menu') ?></div>
                            </td>
                            <td><?= h((string)($item['size'] ?? '')) ?></td>
                            <td><?= h((string)($item['type'] === 'custom' ? ($item['type_pate_name'] ?? '') : '-')) ?></td>
                            <td class="small text-muted">
                                <?php if (($item['type'] ?? '') === 'custom'): ?>
                                    <?= h(implode(', ', $item['ingredient_names'] ?? [])) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-end"><?= number_format($unit, 2) ?> DT</td>
                            <td class="text-end">
                                <form method="post" class="d-flex justify-content-end gap-2">
                                    <input type="hidden" name="action" value="update_qty">
                                    <input type="hidden" name="key" value="<?= h((string)$key) ?>">
                                    <input type="number" name="qty" class="form-control" style="max-width:90px" min="0" value="<?= (int)$qty ?>">
                                    <button class="btn btn-outline-dark" type="submit">OK</button>
                                </form>
                            </td>
                            <td class="text-end fw-semibold"><?= number_format($subtotal, 2) ?> DT</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">Total</div>
                <div class="h5 mb-0"><?= number_format(cart_total(), 2) ?> DT</div>
            </div>

            <div class="d-grid mt-3">
                <a class="btn btn-dark btn-lg" href="checkout.php">Commander</a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
