<?php
require_once __DIR__ . '/includes/header.php';
require_login();

cart_init();
if (empty($_SESSION['cart'])) {
  set_flash('error', 'Your cart is empty.');
  redirect('cart.php');
}

$user = current_user($conn);

if (is_post()) {
  $notes = trim($_POST['notes'] ?? '');
  $payment_method = trim($_POST['payment_method'] ?? 'Cash');

  $subtotal = cart_total();
  $shipping = 0;
  $total = $subtotal + $shipping;
  $order_number = 'ORD' . date('YmdHis') . mt_rand(100, 999);

  mysqli_begin_transaction($conn);
  try {
    $stmt = db_prepare($conn, "INSERT INTO orders(user_id, order_number, subtotal, shipping, total, payment_method, payment_status, order_status, notes)
                               VALUES (?,?,?,?,?,?, 'pending','pending',?)");
    $uid = (int)$_SESSION['user_id'];
    mysqli_stmt_bind_param($stmt, 'isdddss', $uid, $order_number, $subtotal, $shipping, $total, $payment_method, $notes);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);

    $stmtItem = db_prepare($conn, "INSERT INTO order_items(order_id, product_id, product_name, unit_price, qty, line_total, selected_image)
                                   VALUES (?,?,?,?,?,?,?)");
    $stmtStock = db_prepare($conn, "UPDATE products SET stock_qty = stock_qty - ? WHERE id=?");

    foreach ($_SESSION['cart'] as $it) {
      $pid = (int)$it['product_id'];
      $qty = (int)$it['qty'];

      $p = get_product($conn, $pid);
      if (!$p) { throw new Exception('Product not found in cart.'); }
      if (($p['status'] ?? '') !== 'active') { throw new Exception('Some items are not available.'); }

      $stock = (int)($p['stock_qty'] ?? 0);
      if ($stock < $qty) { throw new Exception('Some items are out of stock.'); }

      $unit = (float)$p['price'];
      $line = $unit * $qty;
      $img  = $p['main_image'] ?? '';

      mysqli_stmt_bind_param($stmtItem, 'iisdids', $order_id, $pid, $p['name'], $unit, $qty, $line, $img);
      mysqli_stmt_execute($stmtItem);

      mysqli_stmt_bind_param($stmtStock, 'ii', $qty, $pid);
      mysqli_stmt_execute($stmtStock);
    }

    mysqli_commit($conn);
    $_SESSION['cart'] = [];
    set_flash('success', 'Order placed successfully.');
    redirect('order_success.php?order_id=' . (int)$order_id);
  } catch (Exception $e) {
    mysqli_rollback($conn);
    set_flash('error', $e->getMessage());
    redirect('cart.php');
  }
}
?>
<div class="container py-4">
  <h3 class="mb-3">Checkout</h3>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="bg-white border rounded-4 p-4">
        <h5 class="mb-3">Customer Details</h5>
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Full Name</label>
            <input class="form-control" value="<?php echo esc($user['full_name'] ?? ''); ?>" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input class="form-control" value="<?php echo esc($user['email'] ?? ''); ?>" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input class="form-control" value="<?php echo esc($user['phone'] ?? ''); ?>" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Address</label>
            <input class="form-control" value="<?php echo esc($user['address'] ?? ''); ?>" disabled>
          </div>
        </div>

        <form method="post" class="mt-4">
          <div class="mb-2">
            <label class="form-label">Payment Method</label>
            <select class="form-select" name="payment_method">
              <option value="Cash">Cash</option>
              <option value="Mobile Money">Mobile Money</option>
              <option value="Card">Card</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Order Notes (optional)</label>
            <textarea class="form-control" name="notes" rows="3" placeholder="Any delivery notes..."></textarea>
          </div>
          <button class="btn btn-primary w-100" type="submit">Place Order</button>
        </form>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="bg-white border rounded-4 p-4">
        <h5 class="mb-3">Order Summary</h5>
        <div class="d-flex justify-content-between"><span>Subtotal</span><strong>TZS <?php echo money_tzs(cart_total()); ?></strong></div>
        <div class="d-flex justify-content-between"><span>Shipping</span><strong>TZS 0</strong></div>
        <hr>
        <div class="d-flex justify-content-between"><span>Total</span><strong class="fs-5">TZS <?php echo money_tzs(cart_total()); ?></strong></div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
