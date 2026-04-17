<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

$message = '';
$action = $_GET['action'] ?? 'list';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadedImage = '';
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/pizzaria/images/';
        $filename = basename($_FILES['image']['name']);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Vérifier que c'est bien une image et pas un PDF ou dossier
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($extension, $allowedExtensions, true) || !in_array($mimeType, $allowedMimeTypes, true)) {
            $message = 'Format d\'image non supporté. Utilisez uniquement des fichiers image (jpg, jpeg, png, gif).';
        } else {
            $targetName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $targetName)) {
                $uploadedImage = $targetName;
            } else {
                $message = 'Impossible de transférer l\'image.';
            }
        }
    }

    if (isset($_POST['add_product'])) {
        $nom = trim($_POST['nom']);
        $prix_s = intval($_POST['prix_s']);
        $prix_m = intval($_POST['prix_m']);
        $prix_l = intval($_POST['prix_l']);
        $description = trim($_POST['description']);
        $categorie_id = intval($_POST['categorie_id']);
        $image = $uploadedImage ?: trim($_POST['image']);
        
        // Validation des prix
        if (empty($nom)) {
            $message = "Le nom du produit est requis.";
        } elseif ($prix_s <= 0 || $prix_m <= 0 || $prix_l <= 0) {
            $message = "Tous les prix doivent être des nombres entiers positifs.";
        } elseif ($prix_s >= $prix_m) {
            $message = "Le prix Small doit être inférieur au prix Medium.";
        } elseif ($prix_m >= $prix_l) {
            $message = "Le prix Medium doit être inférieur au prix Large.";
        } elseif (!empty($_FILES['image']['name']) && !$uploadedImage) {
            $message = "Erreur lors de l'upload de l'image.";
        } else {
            $pdo = db();
            try {
                $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix_s, prix_m, prix_l, categorie_id, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nom, $description, $prix_s, $prix_m, $prix_l, $categorie_id, $image]);
                $message = "Produit ajouté avec succès. ID: " . $pdo->lastInsertId();
                header('Location: products.php?success=1');
                exit;
            } catch (Exception $e) {
                $message = "Erreur lors de l'ajout du produit: " . $e->getMessage();
            }
        }
    }
    
    if (isset($_POST['edit_product'])) {
        $id = intval($_POST['id']);
        $nom = trim($_POST['nom']);
        $prix_s = intval($_POST['prix_s']);
        $prix_m = intval($_POST['prix_m']);
        $prix_l = intval($_POST['prix_l']);
        $description = trim($_POST['description']);
        $categorie_id = intval($_POST['categorie_id']);
        $currentImage = trim($_POST['existing_image'] ?? '');
        $image = $uploadedImage ?: $currentImage;
        $disponible = isset($_POST['disponible']) ? 1 : 0;
        
        // Validation des prix
        if (empty($nom)) {
            $message = "Le nom du produit est requis.";
        } elseif ($prix_s <= 0 || $prix_m <= 0 || $prix_l <= 0) {
            $message = "Tous les prix doivent être des nombres entiers positifs.";
        } elseif ($prix_s >= $prix_m) {
            $message = "Le prix Small doit être inférieur au prix Medium.";
        } elseif ($prix_m >= $prix_l) {
            $message = "Le prix Medium doit être inférieur au prix Large.";
        } elseif (!empty($_FILES['image']['name']) && !$uploadedImage) {
            $message = "Erreur lors de l'upload de l'image.";
        } else {
            $pdo = db();
            try {
                $stmt = $pdo->prepare("UPDATE produits SET nom = ?, description = ?, prix_s = ?, prix_m = ?, prix_l = ?, categorie_id = ?, image = ?, disponible = ? WHERE id = ?");
                $stmt->execute([$nom, $description, $prix_s, $prix_m, $prix_l, $categorie_id, $image, $disponible, $id]);
                $message = "Produit modifié avec succès.";
                header('Location: products.php?success=1');
                exit;
            } catch (Exception $e) {
                $message = "Erreur lors de la modification du produit: " . $e->getMessage();
            }
        }
    }
    
    if (isset($_POST['delete_product'])) {
        $id = intval($_POST['id']);
        $pdo = db();
        
        // Vérifier si le produit est dans des commandes
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM commande_details WHERE produit_id = ?");
        $stmt->execute([$id]);
        $usage_count = $stmt->fetchColumn();
        
        if ($usage_count > 0) {
            $message = "Ce produit est dans $usage_count commande(s). Impossible de le supprimer.";
        } else {
            $pdo = db();
            try {
                $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
                $stmt->execute([$id]);
                $message = "Produit supprimé avec succès.";
                // header('Location: products.php?success=1');
                // exit;
            } catch (Exception $e) {
                $message = "Erreur lors de la suppression du produit: " . $e->getMessage();
            }
        }
    }
}

// Gestion des filtres
$search = $_GET['search'] ?? '';
$categorie_filter = $_GET['categorie'] ?? '';

// Récupérer tous les produits avec leurs catégories (avec filtres)
$pdo = db();
$query = "
    SELECT p.*, c.nom as categorie_nom 
    FROM produits p 
    LEFT JOIN categories c ON p.categorie_id = c.id 
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $query .= " AND p.nom LIKE ?";
    $params[] = '%' . $search . '%';
}

if (!empty($categorie_filter)) {
    $query .= " AND p.categorie_id = ?";
    $params[] = $categorie_filter;
}

$query .= " ORDER BY p.nom";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Récupérer toutes les catégories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY nom");
$categories = $stmt->fetchAll();

// Récupérer un produit spécifique pour l'édition
$edit_product = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$id]);
    $edit_product = $stmt->fetch();
}

$page_title = 'Gestion des produits - Admin';
require_once __DIR__ . '/../views/layout/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Gestion des produits</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-info"><?= h($message) ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Opération réussie !</div>
            <?php endif; ?>
            

            <!-- Formulaire d'ajout/modification -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><?= $edit_product ? 'Modifier un produit' : 'Ajouter un produit' ?></h5>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <?php if ($edit_product): ?>
                            <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
                            <input type="hidden" name="existing_image" value="<?= h($edit_product['image'] ?? '') ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Nom du produit</label>
                                    <input type="text" name="nom" class="form-control" 
                                           value="<?= $edit_product ? h($edit_product['nom']) : '' ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Prix S</label>
                                    <input type="number" name="prix_s" class="form-control" 
                                           step="1" min="1" 
                                           value="<?= $edit_product ? h($edit_product['prix_s']) : '' ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Prix M</label>
                                    <input type="number" name="prix_m" class="form-control" 
                                           step="1" min="1" 
                                           value="<?= $edit_product ? h($edit_product['prix_m']) : '' ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Prix L</label>
                                    <input type="number" name="prix_l" class="form-control" 
                                           step="1" min="1" 
                                           value="<?= $edit_product ? h($edit_product['prix_l']) : '' ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Catégorie</label>
                                    <select name="categorie_id" class="form-select" required>
                                        <option value="">Sélectionner une catégorie</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>" 
                                                    <?= ($edit_product && $edit_product['categorie_id'] == $category['id']) ? 'selected' : '' ?>>
                                                <?= h($category['nom']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3"><?= $edit_product ? h($edit_product['description']) : '' ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Image</label>
                                    <input type="file" name="image" class="form-control">
                                    <?php if ($edit_product && $edit_product['image']): ?>
                                        <div class="mt-2">
                                            <img src="../images/<?= h($edit_product['image']) ?>" alt="<?= h($edit_product['nom']) ?>" style="width:100px;height:100px;object-fit:cover;">
                                        </div>
                                        <small class="text-muted">Laisser vide pour conserver l'image actuelle.</small>
                                    <?php else: ?>
                                        <small class="text-muted">Téléchargez un fichier image (jpg, png, gif).</small>
                                    <?php endif; ?>
                                </div>
                                <?php if ($edit_product): ?>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="disponible" class="form-check-input" 
                                                   id="disponible" <?= $edit_product['disponible'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="disponible">
                                                Disponible
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <button type="submit" name="<?= $edit_product ? 'edit_product' : 'add_product' ?>" 
                                class="btn btn-primary" id="submitBtn" onclick="return validatePrices()">
                            <?= $edit_product ? 'Modifier' : 'Ajouter' ?>
                        </button>
                        <?php if ($edit_product): ?>
                            <a href="products.php" class="btn btn-secondary">Annuler</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <!-- Liste des produits -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des produits</h5>
                    
                    <!-- Filtres -->
                    <div class="d-flex gap-2">
                        <form method="get" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   placeholder="Rechercher par nom..." 
                                   value="<?= h($search) ?>" style="width: 200px;">
                            
                            <select name="categorie" class="form-select form-select-sm" style="width: 150px;">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" 
                                            <?= $categorie_filter == $category['id'] ? 'selected' : '' ?>>
                                        <?= h($category['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <button type="submit" class="btn btn-sm btn-outline-primary">Filtrer</button>
                            
                            <?php if (!empty($search) || !empty($categorie_filter)): ?>
                                <a href="products.php" class="btn btn-sm btn-outline-secondary">Effacer</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($products)): ?>
                        <p class="text-muted">Aucun produit trouvé.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Catégorie</th>
                                        <th>Prix</th>
                                        <th>Image</th>
                                        <th>Disponible</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?= h($product['nom']) ?></td>
                                            <td><?= h($product['categorie_nom'] ?: 'Non catégorisé') ?></td>
                                            <td>
                                                S <?= intval($product['prix_s']) ?> / 
                                                M <?= intval($product['prix_m']) ?> / 
                                                L <?= intval($product['prix_l']) ?> DT
                                            </td>
                                            <td>
                                                <?php if ($product['image']): ?>

                                                    <img src="../images/<?= h($product['image']) ?>" 
                                                         alt="<?= h($product['nom']) ?>" 
                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                         onerror="this.style.display='none'">
                                                <?php else: ?>
                                                    <span class="text-muted">Aucune</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $product['disponible'] ? 'success' : 'danger' ?>">
                                                    <?= $product['disponible'] ? 'Oui' : 'Non' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="?action=edit&id=<?= $product['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary">Modifier</a>
                                                <form method="post" class="d-inline" 
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                                    <button type="submit" name="delete_product" 
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

<script>
function validatePrices() {
    const prixS = parseInt(document.querySelector('input[name="prix_s"]').value);
    const prixM = parseInt(document.querySelector('input[name="prix_m"]').value);
    const prixL = parseInt(document.querySelector('input[name="prix_l"]').value);
    
    if (isNaN(prixS) || isNaN(prixM) || isNaN(prixL)) {
        alert('Tous les prix doivent être des nombres entiers.');
        return false;
    }
    
    if (prixS <= 0 || prixM <= 0 || prixL <= 0) {
        alert('Tous les prix doivent être positifs.');
        return false;
    }
    
    if (prixS >= prixM) {
        alert('Le prix Small doit être inférieur au prix Medium.');
        return false;
    }
    
    if (prixM >= prixL) {
        alert('Le prix Medium doit être inférieur au prix Large.');
        return false;
    }
    
    return true;
}

// Validation en temps réel des prix
document.addEventListener('DOMContentLoaded', function() {
    const priceInputs = document.querySelectorAll('input[name="prix_s"], input[name="prix_m"], input[name="prix_l"]');
    
    priceInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Forcer les valeurs entières
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
});
</script>

<?php require_once __DIR__ . '/../views/layout/footer.php'; ?>
