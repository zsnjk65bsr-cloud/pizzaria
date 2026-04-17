# Structure du projet Smart Pizzaria

## Structure actuelle (problématique)
```
pizzaria/
- admin/ (pages admin)
- cart.php (panier)
- checkout.php (commande)
- composer.php (composer pizza)
- index.php (accueil)
- login.php (connexion)
- logout.php (déconnexion)
- menu.php (menu)
- profile.php (profil)
- register.php (inscription)
- views/ (templates)
- includes/ (fonctions)
- config/ (configuration)
- db/ (base de données)
- assets/ (CSS, JS, images)
```

## Nouvelle structure proposée (claire et organisée)
```
pizzaria/
- index.php (point d'entrée principal)
- config/
  - database.php (configuration DB)
  - config.php (config générale)
- includes/
  - init.php (initialisation)
  - functions.php (fonctions utilitaires)
  - auth.php (authentification)
- pages/
  - home.php (accueil)
  - auth/
    - login.php (connexion)
    - register.php (inscription)
    - logout.php (déconnexion)
  - client/
    - menu.php (menu client)
    - cart.php (panier)
    - checkout.php (commande)
    - composer.php (composer pizza)
    - profile.php (profil client)
  - admin/
    - dashboard.php (dashboard admin)
    - products.php (gestion produits)
    - orders.php (gestion commandes)
    - users.php (gestion utilisateurs)
    - ingredients.php (gestion ingrédients)
- views/
  - layout/
    - header.php
    - footer.php
  - home/
    - index.php
  - auth/
    - login.php
    - register.php
  - client/
    - menu.php
    - cart.php
    - composer.php
    - profile.php
  - admin/
    - dashboard.php
    - products.php
    - orders.php
    - users.php
    - ingredients.php
- assets/
  - css/
  - js/
  - images/
- db/
  - smart_pizzaria.sql
```

## Avantages de la nouvelle structure:
1. **Séparation claire**: Pages client vs admin
2. **Logique**: Fonctionnalités regroupées par type
3. **Maintenabilité**: Facile à trouver et modifier
4. **Scalabilité**: Simple à ajouter de nouvelles fonctionnalités
5. **Sécurité**: Structure plus facile à sécuriser
