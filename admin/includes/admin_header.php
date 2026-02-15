<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_admin_login();
$flash = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
  <style>
    .main-header.navbar{ position:fixed; top:0; left:0; right:0; z-index:1030; }
    .main-sidebar{ position:fixed; top:56px; bottom:0; }
    .content-wrapper{ padding-top:70px; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
    <li class="nav-item d-none d-sm-inline-block"><a href="../index.php" class="nav-link">View Site</a></li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
  </ul>
</nav>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="index.php" class="brand-link"><span class="brand-text font-weight-light">Admin Panel</span></a>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
        <li class="nav-item"><a href="index.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        <li class="nav-item"><a href="categories.php" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>Categories</p></a></li>
        <li class="nav-item"><a href="products.php" class="nav-link"><i class="nav-icon fas fa-box"></i><p>Products</p></a></li>
        <li class="nav-item"><a href="orders.php" class="nav-link"><i class="nav-icon fas fa-receipt"></i><p>Orders</p></a></li>
        <li class="nav-item"><a href="users.php" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Users</p></a></li>
      </ul>
    </nav>
  </div>
</aside>

<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <?php if($flash): ?>
        <div class="alert alert-<?php echo esc($flash['type']==='error'?'danger':$flash['type']); ?>">
          <?php echo esc($flash['message']); ?>
        </div>
      <?php endif; ?>
