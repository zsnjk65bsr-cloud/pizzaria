-- Smart Pizzaria – Base de données FINALE

CREATE DATABASE smart_pizzaria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smart_pizzaria;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    role ENUM('client', 'admin') DEFAULT 'client',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    prix_s DECIMAL(8,2) NOT NULL,
    prix_m DECIMAL(8,2) NOT NULL,
    prix_l DECIMAL(8,2) NOT NULL,
    image VARCHAR(255),
    categorie_id INT,
    disponible TINYINT(1) DEFAULT 1,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id)
);

CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prix_supplementaire DECIMAL(5,2) DEFAULT 0.00,
    disponible TINYINT(1) DEFAULT 1
);

CREATE TABLE types_pate (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prix_supplementaire DECIMAL(5,2) DEFAULT 0.00
);

CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type_livraison ENUM('livraison', 'sur_place') NOT NULL DEFAULT 'livraison',
    adresse_livraison TEXT,
    telephone VARCHAR(20) NOT NULL,
    note_client TEXT,
    statut ENUM('en_attente','confirmée','en_livraison','livrée','annulée') DEFAULT 'en_attente',
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE commande_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT,
    taille ENUM('S', 'M', 'L') NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    est_personnalisee TINYINT(1) DEFAULT 0,
    FOREIGN KEY (commande_id) REFERENCES commandes(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);

CREATE TABLE pizza_personnalisee (
    id INT AUTO_INCREMENT PRIMARY KEY,
    detail_id INT NOT NULL,
    type_pate_id INT NOT NULL,
    FOREIGN KEY (detail_id) REFERENCES commande_details(id),
    FOREIGN KEY (type_pate_id) REFERENCES types_pate(id)
);

CREATE TABLE pizza_personnalisee_ingredients (
    pizza_perso_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    PRIMARY KEY (pizza_perso_id, ingredient_id),
    FOREIGN KEY (pizza_perso_id) REFERENCES pizza_personnalisee(id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
);

INSERT INTO categories (nom) VALUES 
('Pizza Classique'),
('Pizza Végétarienne'),
('Pizza Spéciale'),
('Pizza Personnalisée');

INSERT INTO ingredients (nom, prix_supplementaire) VALUES
('Mozzarella', 1.50),
('Jambon', 2.00),
('Champignons', 1.00),
('Poivrons', 0.80),
('Olives', 0.70),
('Thon', 2.00),
('Pepperoni', 2.50),
('Oignons', 0.50),
('Tomates cerises', 1.00),
('Basilic', 0.50);

INSERT INTO types_pate (nom, prix_supplementaire) VALUES
('Classique', 0.00),
('Fine', 0.00),
('Épaisse', 1.00),
('Fromage farci', 2.50);

INSERT INTO users (nom, prenom, email, mot_de_passe, telephone, adresse, role) VALUES
('Admin', 'Smart', 'admin@smartpizzaria.com', MD5('admin123'), '74000000', 'Sfax Centre', 'admin');

INSERT INTO users (nom, prenom, email, mot_de_passe, telephone, adresse) VALUES
('Ben Salah', 'Mohamed', 'mohamed@gmail.com', MD5('client123'), '22334455', 'Rue Menzel Chaker, Sfax');

INSERT INTO produits (nom, description, prix_s, prix_m, prix_l, image, categorie_id) VALUES
('Margherita',   'Tomate, Mozzarella, Basilic',                8.00,  11.00, 14.00, 'margherita.jpg', 1),
('4 Fromages',   'Mozzarella, Gorgonzola, Emmental, Parmesan', 10.00, 13.50, 17.00, '4fromages.jpg',  1),
('Végétarienne', 'Poivrons, Champignons, Oignons, Tomates',    9.00,  12.00, 15.00, 'vege.jpg',       2),
('Pepperoni',    'Tomate, Mozzarella, Pepperoni',              10.50, 14.00, 18.00, 'pepperoni.jpg',  3),
('Thon Oignons', 'Thon, Oignons, Olives, Mozzarella',         10.00, 13.00, 16.50, 'thon.jpg',       3);
