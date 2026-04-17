<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

$message = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = intval($_POST['user_id']);
        
        // Ne pas supprimer l'admin connecté
        if ($user_id === $_SESSION['user']['id']) {
            $message = "Vous ne pouvez pas supprimer votre propre compte.";
        } else {
            $pdo = db();
            
            // Vérifier si l'utilisateur a des commandes
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM commandes WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $order_count = $stmt->fetchColumn();
            
            if ($order_count > 0) {
                $message = "Cet utilisateur a $order_count commande(s). Impossible de le supprimer.";
            } else {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                if ($stmt->execute([$user_id])) {
                    $message = "Utilisateur supprimé.";
                } else {
                    $message = "Erreur lors de la suppression de l'utilisateur.";
                }
            }
        }
    }
    
    if (isset($_POST['toggle_role'])) {
        $user_id = intval($_POST['user_id']);
        
        // Ne pas modifier son propre rôle
        if ($user_id === $_SESSION['user']['id']) {
            $message = "Vous ne pouvez pas modifier votre propre rôle.";
        } else {
            $pdo = db();
            
            // Récupérer le rôle actuel
            $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if ($user) {
                $new_role = $user['role'] === 'admin' ? 'client' : 'admin';
                $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
                if ($stmt->execute([$new_role, $user_id])) {
                    $message = "Rôle de l'utilisateur mis à jour.";
                } else {
                    $message = "Erreur lors de la mise à jour du rôle.";
                }
            }
        }
    }
}

// Récupérer tous les utilisateurs
$pdo = db();
$stmt = $pdo->query("SELECT id, nom, prenom, email, telephone, role, date_inscription FROM users ORDER BY date_inscription DESC");
$users = $stmt->fetchAll();

$page_title = 'Gestion des utilisateurs - Admin';
require_once __DIR__ . '/../views/layout/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Gestion des utilisateurs</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-info"><?= h($message) ?></div>
            <?php endif; ?>
            
            <!-- Liste des utilisateurs -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Liste des utilisateurs</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($users)): ?>
                        <p class="text-muted">Aucun utilisateur trouvé.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Rôle</th>
                                        <th>Date d'inscription</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <strong><?= h($user['prenom'] . ' ' . $user['nom']) ?></strong>
                                                <?php if ($user['id'] === $_SESSION['user']['id']): ?>
                                                    <span class="badge bg-info ms-1">Vous</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= h($user['email']) ?></td>
                                            <td><?= h($user['telephone'] ?: 'Non renseigné') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                                    <?= $user['role'] === 'admin' ? 'Admin' : 'Client' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y', strtotime($user['date_inscription'])) ?>
                                            </td>
                                            <td>
                                                <?php if ($user['id'] !== $_SESSION['user']['id']): ?>
                                                    <form method="post" class="d-inline mb-1">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" name="toggle_role" 
                                                                class="btn btn-sm btn-outline-warning">
                                                            <?= $user['role'] === 'admin' ? 'Rétrograder' : 'Promouvoir' ?>
                                                        </button>
                                                    </form>
                                                    <form method="post" class="d-inline" 
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" name="delete_user" 
                                                                class="btn btn-sm btn-outline-danger">
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">Votre compte</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                Total: <?= count($users) ?> utilisateur(s) | 
                                Admins: <?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?> | 
                                Clients: <?= count(array_filter($users, fn($u) => $u['role'] === 'client')) ?>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../views/layout/footer.php'; ?>
