<?php
require_once __DIR__ . '/includes/admin_header.php';

$counts = [
  'products' => (int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM products"))['c'] ?? 0),
  'categories' => (int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM categories"))['c'] ?? 0),
  'orders' => (int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM orders"))['c'] ?? 0),
  'users' => (int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM users"))['c'] ?? 0),
];
?>
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner"><h3><?php echo $counts['products']; ?></h3><p>Products</p></div>
      <div class="icon"><i class="fas fa-box"></i></div>
      <a href="products.php" class="small-box-footer">More <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner"><h3><?php echo $counts['categories']; ?></h3><p>Categories</p></div>
      <div class="icon"><i class="fas fa-tags"></i></div>
      <a href="categories.php" class="small-box-footer">More <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner"><h3><?php echo $counts['orders']; ?></h3><p>Orders</p></div>
      <div class="icon"><i class="fas fa-receipt"></i></div>
      <a href="orders.php" class="small-box-footer">More <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-danger">
      <div class="inner"><h3><?php echo $counts['users']; ?></h3><p>Users</p></div>
      <div class="icon"><i class="fas fa-users"></i></div>
      <a href="users.php" class="small-box-footer">More <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
