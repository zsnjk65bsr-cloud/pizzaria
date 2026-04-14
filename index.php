<?php
require_once __DIR__ . '/includes/init.php';

$page_title = 'Accueil — Smart Pizzaria';

render('home/index', compact('page_title'));
