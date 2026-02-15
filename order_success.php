<?php
require_once __DIR__ . '/includes/header.php';
require_login();

$order_id = (int)($_GET['order_id'] ?? 0);
$uid = (int)$_SESSION['user_id'];

$stmt = db_prepare($conn, "SELECT * FROM orders WHERE id=? AND user_id=? LIMIT 1");
mysqli_stmt_bind_param($stmt,'ii',$order_id,$uid);
mysqli_stmt_execute($stmt);
$res=mysqli_stmt_get_result($stmt);
$order=$res?mysqli_fetch_assoc($res):null;

if(!$order){
  echo '<div class="container py-4"><div class="alert alert-warning">Order not found.</div></div>';
  require_once __DIR__ . '/includes/footer.php'; exit;
}
?>
<div class="container py-5">
  <div class="bg-white border rounded-4 p-4 text-center">
    <i class="bi bi-check-circle-fill text-success" style="font-size:64px"></i>
    <h3 class="mt-2">Order Successful</h3>
    <p class="text-muted mb-3">Your order <strong><?php echo esc($order['order_number']); ?></strong> has been placed.</p>
    <div class="d-flex justify-content-center gap-2">
      <a class="btn btn-outline-primary" href="shop.php">Continue Shopping</a>
      <a class="btn btn-outline-success" href="invoice.php?order_id=<?php echo (int)$order['id']; ?>">Print Invoice</a>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
