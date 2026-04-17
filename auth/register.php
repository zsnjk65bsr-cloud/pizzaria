<?php
require_once __DIR__ . '/../includes/init.php';

if (is_logged_in()) {
    redirect('/pizzaria/auth/index.php');
}

if (is_post()) {
    $nom = trim((string) post('nom', ''));
    $prenom = trim((string) post('prenom', ''));
    $email = trim((string) post('email', ''));
    $password = (string) post('password', '');
    $telephone = trim((string) post('telephone', ''));
    $adresse = trim((string) post('adresse', ''));

    if ($nom === '' || $prenom === '' || $email === '' || $password === '') {
        flash_set('warning', 'Veuillez remplir les champs obligatoires.');
    } else {
        try {
            $pdo = db();
            $stmt = $pdo->prepare('INSERT INTO users (nom, prenom, email, mot_de_passe, telephone, adresse, role) VALUES (?,?,?,?,?,?,\'client\')');
            $stmt->execute([$nom, $prenom, $email, md5($password), $telephone ?: null, $adresse ?: null]);
            flash_set('success', 'Compte créé. Vous pouvez vous connecter.');
            redirect('/pizzaria/auth/login.php');
        } catch (Throwable $e) {
            flash_set('danger', 'Impossible de créer le compte (email peut-être déjà utilisé).');
        }
    }
}

$page_title = 'Inscription - Smart Pizzaria';

require_once __DIR__ . '/../views/layout/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="h4 mb-3">Inscription</h2>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea name="adresse" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">S'inscrire</button>
                    </form>
                    <div class="mt-3 small">Déjà un compte? <a href="/pizzaria/auth/login.php">Se connecter</a></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../views/layout/footer.php'; ?>
