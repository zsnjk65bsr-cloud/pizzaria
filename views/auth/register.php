<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card">
            <div class="card-body">
                <h2 class="h4 mb-3">Inscription</h2>
                <form method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom *</label>
                            <input name="prenom" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mot de passe *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <input name="telephone" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Adresse</label>
                            <input name="adresse" class="form-control">
                        </div>
                    </div>
                    <button class="btn btn-dark w-100" type="submit">Créer le compte</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
