<?php
require_once __DIR__ . '/../includes/init.php';

require_admin();

$pdo = db();

$stats = [
    'users' => (int)$pdo->query("SELECT COUNT(*) AS c FROM users")->fetch()['c'],
    'orders' => (int)$pdo->query("SELECT COUNT(*) AS c FROM commandes")->fetch()['c'],
    'products' => (int)$pdo->query("SELECT COUNT(*) AS c FROM produits")->fetch()['c'],
];

$recent_orders = $pdo->query("SELECT c.*, u.nom, u.prenom FROM commandes c JOIN users u ON u.id = c.user_id ORDER BY c.date_commande DESC LIMIT 10")->fetchAll();

$page_title = 'Admin Dashboard — Smart Pizzaria';

render('admin/dashboard', compact('page_title', 'stats', 'recent_orders'));
