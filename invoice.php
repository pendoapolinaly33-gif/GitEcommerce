<?php
require_once __DIR__ . '/includes/header.php';
require_login();

$order_id=(int)($_GET['order_id'] ?? 0);
$uid=(int)$_SESSION['user_id'];

$stmt=db_prepare($conn,"SELECT * FROM orders WHERE id=? AND user_id=? LIMIT 1");
mysqli_stmt_bind_param($stmt,'ii',$order_id,$uid);
mysqli_stmt_execute($stmt);
$res=mysqli_stmt_get_result($stmt);
$order=$res?mysqli_fetch_assoc($res):null;
if(!$order){
  echo '<div class="container py-4"><div class="alert alert-warning">Invoice not found.</div></div>';
  require_once __DIR__ . '/includes/footer.php'; exit;
}

$stmt=db_prepare($conn,"SELECT oi.*, p.main_image FROM order_items oi LEFT JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?");
mysqli_stmt_bind_param($stmt,'i',$order_id);
mysqli_stmt_execute($stmt);
$res=mysqli_stmt_get_result($stmt);
$items=[];
if($res){ while($r=mysqli_fetch_assoc($res)){ $items[]=$r; } }
?>
<div class="container py-4" id="invoiceArea">
  <div class="bg-white border rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-1">Invoice</h4>
        <div class="text-muted small">Order: <?php echo esc($order['order_number']); ?></div>
        <div class="text-muted small">Payment: <strong><?php echo esc($order['payment_status']); ?></strong> | Status: <strong><?php echo esc($order['order_status']); ?></strong></div>
      </div>
      <div class="d-print-none">
        <button class="btn btn-outline-success" onclick="window.print()">Print</button>
        <a class="btn btn-outline-primary" href="shop.php">Continue Shopping</a>
      </div>
    </div>
    <hr>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
        <tbody>
          <?php foreach($items as $it): ?>
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <img src="<?php echo esc($it['main_image'] ?: 'https://via.placeholder.com/80'); ?>" style="width:50px;height:50px;object-fit:cover;border-radius:10px" alt="">
                  <div><?php echo esc($it['product_name']); ?></div>
                </div>
              </td>
              <td>TZS <?php echo money_tzs($it['unit_price']); ?></td>
              <td><?php echo (int)$it['qty']; ?></td>
              <td class="fw-bold">TZS <?php echo money_tzs($it['line_total']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="text-end">
      <div>Subtotal: <strong>TZS <?php echo money_tzs($order['subtotal']); ?></strong></div>
      <div>Total: <strong class="fs-5">TZS <?php echo money_tzs($order['total']); ?></strong></div>
    </div>
  </div>
</div>

<style>
@media print{
  nav, footer, .d-print-none{ display:none !important; }
  body{ background:#fff !important; }
}
</style>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
