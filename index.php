<?php
require_once __DIR__ . '/includes/header.php';
$products=[];
$res = mysqli_query($conn, "SELECT id, name, price, stock_qty, main_image FROM products WHERE status='active' ORDER BY id DESC LIMIT 8");
if($res){ while($r=mysqli_fetch_assoc($res)){ $products[]=$r; } }
?>
<div class="container py-4">
  <div class="p-4 p-md-5 mb-4 bg-white rounded-4 border">
    <div class="row align-items-center g-4">
      <div class="col-md-8">
        <h1 class="fw-bold">Welcome</h1>
        <p class="text-muted mb-3">Shop quality products, add them to your cart, and place orders easily. Admin can manage products, categories, and order status from the dashboard.</p>
        <a class="btn btn-primary text-decoration-none" href="shop.php">Shop Now</a>
      </div>
      <div class="col-md-4 text-center">
        <i class="bi bi-bag-check" style="font-size:64px"></i>
      </div>
    </div>
  </div>

  <h4 class="mb-3">Latest Products</h4>
  <?php if(empty($products)): ?>
    <div class="alert alert-secondary">Not yet</div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach($products as $p): ?>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="product-card">
          <a href="product.php?id=<?php echo (int)$p['id']; ?>" class="text-decoration-none text-dark">
            <img src="<?php echo esc($p['main_image'] ?: 'https://via.placeholder.com/600x400?text=No+Image'); ?>" alt="">
            <div class="product-title"><?php echo esc($p['name']); ?></div>
          </a>
          <div class="d-flex justify-content-between align-items-center">
            <div class="price">TZS <?php echo money_tzs($p['price']); ?></div>
            <form method="post" action="cart_action.php" class="m-0">
              <input type="hidden" name="action" value="add">
              <input type="hidden" name="product_id" value="<?php echo (int)$p['id']; ?>">
              <input type="hidden" name="qty" value="1">
              <input type="hidden" name="redirect" value="index.php">
              <button class="btn btn-outline-primary btn-sm" <?php echo ((int)$p['stock_qty']<=0)?'disabled':''; ?>>
                <i class="bi bi-cart-plus"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
