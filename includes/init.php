<?php

session_start();

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$config = require __DIR__ . '/../config/config.php';

cart_init();
