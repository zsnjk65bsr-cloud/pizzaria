<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

$message = '';
$action = $_GET['action'] ?? 'list';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_ingredient'])) {
        $nom = trim($_POST['nom']);
        $prix = floatval($_POST['prix_supplementaire']);
        
        if (empty($nom)) {
            $message = "Le nom de l'ingrédient est requis.";
        } else {
            $pdo = db();
            $stmt = $pdo->prepare("INSERT INTO ingredients (nom, prix_supplementaire) VALUES (?, ?)");
            if ($stmt->execute([$nom, $prix])) {
                $message = "Ingrédient ajouté avec succès.";
                header('Location: ingredients.php?success=1');
                exit;
            } else {
                $message = "Erreur lors de l'ajout de l'ingrédient.";
            }
        }
    }
    
    if (isset($_POST['edit_ingredient'])) {
        $id = intval($_POST['id']);
        $nom = trim($_POST['nom']);
        $prix = floatval($_POST['prix_supplementaire']);
        $disponible = isset($_POST['disponible']) ? 1 : 0;
        
        if (empty($nom)) {
            $message = "Le nom de l'ingrédient est requis.";
        } else {
            $pdo = db();
            $stmt = $pdo->prepare("UPDATE ingredients SET nom = ?, prix_supplementaire = ?, disponible = ? WHERE id = ?");
            if ($stmt->execute([$nom, $prix, $disponible, $id])) {
                $message = "Ingrédient modifié avec succès.";
                header('Location: ingredients.php?success=1');
                exit;
            } else {
                $message = "Erreur lors de la modification de l'ingrédient.";
            }
        }
    }
    
    if (isset($_POST['delete_ingredient'])) {
        $id = intval($_POST['id']);
        $pdo = db();
        
        // Vérifier si l'ingrédient est utilisé dans des pizzas personnalisées
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM pizza_personnalisee_ingredients WHERE ingredient_id = ?");
        $stmt->execute([$id]);
        $usage_count = $stmt->fetchColumn();
        
        if ($usage_count > 0) {
            $message = "Cet ingrédient est utilisé dans $usage_count pizza(s) personnalisée(s). Impossible de le supprimer.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM ingredients WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = "Ingrédient supprimé avec succès.";
                header('Location: ingredients.php?success=1');
                exit;
            } else {
                $message = "Erreur lors de la suppression de l'ingrédient.";
            }
        }
    }
}

// Récupérer tous les ingrédients
$pdo = db();
$stmt = $pdo->query("SELECT * FROM ingredients ORDER BY nom");
$ingredients = $stmt->fetchAll();

// Récupérer un ingrédient spécifique pour l'édition
$edit_ingredient = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM ingredients WHERE id = ?");
    $stmt->execute([$id]);
    $edit_ingredient = $stmt->fetch();
}

$page_title = 'Gestion des ingrédients - Admin';
require_once __DIR__ . '/../views/layout/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Gestion des ingrédients</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-info"><?= h($message) ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Opération réussie !</div>
            <?php endif; ?>
            
            <!-- Formulaire d'ajout/modification -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><?= $edit_ingredient ? 'Modifier un ingrédient' : 'Ajouter un ingrédient' ?></h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <?php if ($edit_ingredient): ?>
                            <input type="hidden" name="id" value="<?= $edit_ingredient['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nom de l'ingrédient</label>
                                    <input type="text" name="nom" class="form-control" 
                                           value="<?= $edit_ingredient ? h($edit_ingredient['nom']) : '' ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Prix supplémentaire (DT)</label>
                                    <input type="number" name="prix_supplementaire" class="form-control" 
                                           step="0.01" min="0" 
                                           value="<?= $edit_ingredient ? h($edit_ingredient['prix_supplementaire']) : '0.00' ?>" 
                                           required>
                                </div>
                            </div>
                            <?php if ($edit_ingredient): ?>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <div class="form-check mt-4">
                                            <input type="checkbox" name="disponible" class="form-check-input" 
                                                   id="disponible" <?= $edit_ingredient['disponible'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="disponible">
                                                Disponible
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" name="<?= $edit_ingredient ? 'edit_ingredient' : 'add_ingredient' ?>" 
                                class="btn btn-primary">
                            <?= $edit_ingredient ? 'Modifier' : 'Ajouter' ?>
                        </button>
                        <?php if ($edit_ingredient): ?>
                            <a href="ingredients.php" class="btn btn-secondary">Annuler</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <!-- Liste des ingrédients -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Liste des ingrédients</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($ingredients)): ?>
                        <p class="text-muted">Aucun ingrédient trouvé.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prix supplémentaire</th>
                                        <th>Disponible</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ingredients as $ingredient): ?>
                                        <tr>
                                            <td><?= h($ingredient['nom']) ?></td>
                                            <td><?= number_format($ingredient['prix_supplementaire'], 2) ?> DT</td>
                                            <td>
                                                <span class="badge bg-<?= $ingredient['disponible'] ? 'success' : 'danger' ?>">
                                                    <?= $ingredient['disponible'] ? 'Oui' : 'Non' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="?action=edit&id=<?= $ingredient['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary">Modifier</a>
                                                <form method="post" class="d-inline" 
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet ingrédient ?')">
                                                    <input type="hidden" name="id" value="<?= $ingredient['id'] ?>">
                                                    <button type="submit" name="delete_ingredient" 
                                                            class="btn btn-sm btn-outline-danger">Supprimer</button>
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
