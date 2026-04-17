<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

$message = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $order_id = intval($_POST['order_id']);
        $new_status = $_POST['statut'];
        
        $pdo = db();
        $stmt = $pdo->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
        if ($stmt->execute([$new_status, $order_id])) {
            $message = "Statut de la commande mis à jour.";
        } else {
            $message = "Erreur lors de la mise à jour du statut.";
        }
    }
    
    if (isset($_POST['delete_order'])) {
        $order_id = intval($_POST['order_id']);
        $pdo = db();
        
        // Supprimer d'abord les détails de commande
        $stmt = $pdo->prepare("DELETE FROM commande_details WHERE commande_id = ?");
        $stmt->execute([$order_id]);
        
        // Puis supprimer la commande
        $stmt = $pdo->prepare("DELETE FROM commandes WHERE id = ?");
        if ($stmt->execute([$order_id])) {
            $message = "Commande supprimée.";
        } else {
            $message = "Erreur lors de la suppression de la commande.";
        }
    }
}

// Récupérer toutes les commandes avec détails
$pdo = db();
$stmt = $pdo->query("
    SELECT c.*, u.nom as client_nom, u.prenom as client_prenom, u.email as client_email, u.telephone as client_telephone
    FROM commandes c
    JOIN users u ON c.user_id = u.id
    ORDER BY c.date_commande DESC
");
$orders = $stmt->fetchAll();

// Récupérer les détails de chaque commande
$order_details = [];
foreach ($orders as $order) {
    $stmt = $pdo->prepare("SELECT cd.*, p.nom as produit_nom, p.prix_s, p.prix_m, p.prix_l
        FROM commande_details cd
        LEFT JOIN produits p ON cd.produit_id = p.id
        WHERE cd.commande_id = ?
    ");
    $stmt->execute([$order['id']]);
    $order_details[$order['id']] = $stmt->fetchAll();
}

$statuses = [
    'en_attente' => 'En attente',
    'confirmée' => 'Confirmée',
    'en_livraison' => 'En livraison',
    'livrée' => 'Livrée',
    'annulée' => 'Annulée'
];

$status_colors = [
    'en_attente' => 'warning',
    'confirmée' => 'info',
    'en_livraison' => 'primary',
    'livrée' => 'success',
    'annulée' => 'danger'
];

$page_title = 'Gestion des commandes - Admin';
require_once __DIR__ . '/../views/layout/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Gestion des commandes</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-info"><?= h($message) ?></div>
            <?php endif; ?>
            
            <!-- Liste des commandes -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Liste des commandes</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                        <p class="text-muted">Aucune commande trouvée.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>N° Commande</th>
                                        <th>Client</th>
                                        <th>Date</th>
                                        <th>Produits</th>
                                        <th>Total</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><strong>#<?= $order['id'] ?></strong></td>
                                            <td>
                                                <?= h($order['client_prenom'] . ' ' . $order['client_nom']) ?>
                                                <br>
                                                <small class="text-muted"><?= h($order['client_email']) ?></small>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y H:i', strtotime($order['date_commande'])) ?>
                                            </td>
                                            <td>
                                                <?php if (isset($order_details[$order['id']])): ?>
                                                    <?php foreach ($order_details[$order['id']] as $detail): ?>
                                                        <div class="small">
                                                            <?= h($detail['produit_nom'] ?: 'Pizza personnalisée') ?>
                                                            <?php if ($detail['est_personnalisee']): ?>
                                                                <span class="badge bg-info">Perso</span>
                                                            <?php endif; ?>
                                                            <br>
                                                            Taille: <?= $detail['taille'] ?> x <?= $detail['quantite'] ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Aucun produit</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $total = 0;
                                                if (isset($order_details[$order['id']])) {
                                                    foreach ($order_details[$order['id']] as $detail) {
                                                        $price = 0.0;
                                                        if (!empty($detail['produit_id'])) {
                                                            if ($detail['taille'] === 'S') {
                                                                $price = (float)($detail['prix_s'] ?? 0);
                                                            } elseif ($detail['taille'] === 'L') {
                                                                $price = (float)($detail['prix_l'] ?? 0);
                                                            } else {
                                                                $price = (float)($detail['prix_m'] ?? 0);
                                                            }
                                                        } elseif ($detail['est_personnalisee']) {
                                                            $base_prices = $config['custom_pizza_base_price'] ?? ['S' => 8.00, 'M' => 11.00, 'L' => 14.00];
                                                            $price = (float)($base_prices[$detail['taille']] ?? 0);
                                                            $stmtDetail = $pdo->prepare('SELECT pp.id, tp.prix_supplementaire FROM pizza_personnalisee pp JOIN types_pate tp ON pp.type_pate_id = tp.id WHERE pp.detail_id = ?');
                                                            $stmtDetail->execute([$detail['id']]);
                                                            $pizzaPerso = $stmtDetail->fetch();
                                                            if ($pizzaPerso) {
                                                                $price += (float)($pizzaPerso['prix_supplementaire'] ?? 0);
                                                                $stmtIng = $pdo->prepare('SELECT SUM(i.prix_supplementaire) FROM pizza_personnalisee_ingredients ppi JOIN ingredients i ON ppi.ingredient_id = i.id WHERE ppi.pizza_perso_id = ?');
                                                                $stmtIng->execute([$pizzaPerso['id']]);
                                                                $price += (float)$stmtIng->fetchColumn();
                                                            }
                                                        }
                                                        $total += $price * $detail['quantite'];
                                                    }
                                                }
                                                ?>
                                                <strong><?= number_format($total, 2) ?> DT</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $status_colors[$order['statut']] ?>">
                                                    <?= $statuses[$order['statut']] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <form method="post" class="d-inline mb-1">
                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                    <select name="statut" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                                                        <?php foreach ($statuses as $key => $label): ?>
                                                            <option value="<?= $key ?>" <?= $order['statut'] === $key ? 'selected' : '' ?>>
                                                                <?= $label ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <button type="submit" name="update_status" class="btn btn-sm btn-outline-primary ms-1">
                                                        Mettre à jour
                                                    </button>
                                                </form>
                                                <form method="post" class="d-inline" 
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')">
                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                    <button type="submit" name="delete_order" class="btn btn-sm btn-outline-danger">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </td>
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
</div>

<?php require_once __DIR__ . '/../views/layout/footer.php'; ?>
