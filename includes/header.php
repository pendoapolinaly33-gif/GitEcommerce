<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/functions.php';
$flash = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ecommerce System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    .product-card{border:1px solid #eee;border-radius:14px;padding:12px;background:#fff;height:100%;}
    .product-card img{width:100%;height:190px;object-fit:cover;border-radius:12px;}
    .product-title{font-size:1rem;font-weight:600;margin:10px 0 6px;}
    .price{font-weight:700;}
  </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Ecommerce</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
        <li class="nav-item"></li>
      </ul>
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item">
          <a class="nav-link position-relative" href="cart.php">
            <i class="bi bi-cart3"></i> Cart
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
              <?php echo (int)cart_count_items(); ?>
            </span>
          </a>
        </li>
        <?php if(user_is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="account.php">My Account</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="admin/login.php">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-3">
  <?php if($flash): ?>
    <div class="alert alert-<?php echo esc($flash['type']==='error'?'danger':$flash['type']); ?> alert-dismissible fade show" role="alert">
      <?php echo esc($flash['message']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
</div>
