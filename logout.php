<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
session_destroy();
session_start();
$_SESSION['cart'] = [];
set_flash('success','You have logged out.');
redirect('index.php');
