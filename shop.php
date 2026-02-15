<?php
require_once __DIR__ . '/includes/header.php';

$categories = fetch_categories($conn);
$keyword = trim($_GET['q'] ?? '');
$category_id = (int)($_GET['cat'] ?? 0);

$products = search_products($conn, ['keyword'=>$keyword, 'category_id'=>$category_id]);
?>
<div class="container py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h3 class="m-0">Shop</h3>
    <form class="d-flex gap-2" method="get" action="shop.php">
      <input class="form-control" type="search" name="q" placeholder="Search products..." value="<?php echo esc($keyword); ?>">
      <select class="form-select" name="cat">
        <option value="0">All Categories</option>
        <?php foreach($categories as $c): ?>
          <option value="<?php echo (int)$c['id']; ?>" <?php echo ($category_id===(int)$c['id'])?'selected':''; ?>>
            <?php echo esc($c['name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-outline-dark" type="submit">Filter</button>
    </form>
  </div>

  <?php if(empty($products)): ?>
    <div class="alert alert-secondary">No product yet</div>
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
              <input type="hidden" name="redirect" value="shop.php?<?php echo esc(http_build_query($_GET)); ?>">
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
