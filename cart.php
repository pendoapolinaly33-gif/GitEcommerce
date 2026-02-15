<?php
require_once __DIR__ . '/includes/header.php';
cart_init();
$items = $_SESSION['cart'];
?>
<div class="container py-4">
  <h3 class="mb-3">Your Cart</h3>

  <?php if(empty($items)): ?>
    <div class="alert alert-secondary">Your cart is empty.</div>
    <a class="btn btn-outline-primary" href="shop.php">Continue Shopping</a>
  <?php else: ?>
    <form method="post" action="cart_action.php">
      <input type="hidden" name="action" value="update">
      <div class="table-responsive bg-white border rounded-4">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Product</th>
              <th style="width:140px">Price</th>
              <th style="width:140px">Qty</th>
              <th style="width:160px">Subtotal</th>
              <th style="width:90px"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($items as $it): $sub=(float)$it['price']*(int)$it['qty']; ?>
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <img src="<?php echo esc($it['image'] ?: 'https://via.placeholder.com/80'); ?>" style="width:56px;height:56px;object-fit:cover;border-radius:10px" alt="">
                    <div class="fw-semibold"><?php echo esc($it['name']); ?></div>
                  </div>
                </td>
                <td>TZS <?php echo money_tzs($it['price']); ?></td>
                <td>
                  <input class="form-control" type="number" min="1" name="qty[<?php echo (int)$it['product_id']; ?>]" value="<?php echo (int)$it['qty']; ?>">
                </td>
                <td class="fw-bold">TZS <?php echo money_tzs($sub); ?></td>
                <td>
                  <a class="btn btn-outline-danger btn-sm" href="cart_action.php?action=remove&product_id=<?php echo (int)$it['product_id']; ?>">Remove</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-3">
        <div class="d-flex gap-2">
          <button class="btn btn-primary" type="submit">Update Cart</button>
          <a class="btn btn-outline-secondary" href="cart_action.php?action=clear">Clear Cart</a>
        </div>
        <div class="bg-white border rounded-4 p-3">
          <div class="d-flex justify-content-between"><span>Subtotal</span><strong>TZS <?php echo money_tzs(cart_total()); ?></strong></div>
          <div class="mt-2">
            <a class="btn btn-outline-primary w-100" href="checkout.php">Checkout</a>
          </div>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
