<?php
require_once __DIR__ . '/../includes/init.php';

logout();
flash_set('success', 'Vous êtes déconnecté.');
redirect('/pizzaria/auth/login.php');
