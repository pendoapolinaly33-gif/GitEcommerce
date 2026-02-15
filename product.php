<?php
require_once __DIR__ . '/includes/header.php';
$id = (int)($_GET['id'] ?? 0);
$product = get_product($conn, $id);
if(!$product){
  echo '<div class="container py-4"><div class="alert alert-warning">Product not found.</div></div>';
  require_once __DIR__ . '/includes/footer.php';
  exit;
}
$gallery = get_product_images($conn, $id);
$main = $product['main_image'] ?: 'https://via.placeholder.com/900x600?text=No+Image';
?>
<div class="container py-4">
  <div class="bg-white border rounded-4 p-3 p-md-4">
    <div class="row g-4">
      <div class="col-md-6">
        <img id="mainImg" src="<?php echo esc($main); ?>" class="w-100 rounded-4" style="height:360px;object-fit:cover" alt="">
        <?php if(!empty($gallery)): ?>
          <div class="d-flex gap-2 mt-2 flex-wrap">
            <?php foreach($gallery as $img): ?>
              <img src="<?php echo esc($img); ?>" class="rounded-3" style="width:80px;height:70px;object-fit:cover;cursor:pointer"
                   onclick="document.getElementById('mainImg').src=this.src" alt="">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <h3 class="fw-bold"><?php echo esc($product['name']); ?></h3>
        <div class="text-muted mb-2"><?php echo esc($product['category_name'] ?? ''); ?></div>
        <h4 class="mb-3">TZS <?php echo money_tzs($product['price']); ?></h4>
        <p class="text-muted"><?php echo nl2br(esc($product['description'] ?? '')); ?></p>

        <form method="post" action="cart_action.php" class="mt-3">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
          <input type="hidden" name="redirect" value="product.php?id=<?php echo (int)$product['id']; ?>">
          <div class="d-flex gap-2 align-items-center">
            <input type="number" class="form-control" name="qty" value="1" min="1" style="max-width:120px">
            <button class="btn btn-primary" type="submit" <?php echo ((int)$product['stock_qty']<=0)?'disabled':''; ?>>
              <i class="bi bi-cart-plus"></i> Add to Cart
            </button>
          </div>
          <?php if((int)$product['stock_qty']<=0): ?>
            <div class="text-danger small mt-2">Out of stock</div>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
