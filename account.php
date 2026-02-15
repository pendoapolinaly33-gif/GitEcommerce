<?php
require_once __DIR__ . '/includes/header.php';
require_login();

$uid=(int)$_SESSION['user_id'];
$stmt=db_prepare($conn,"SELECT * FROM orders WHERE user_id=? ORDER BY id DESC");
mysqli_stmt_bind_param($stmt,'i',$uid);
mysqli_stmt_execute($stmt);
$res=mysqli_stmt_get_result($stmt);
$orders=[];
if($res){ while($r=mysqli_fetch_assoc($res)){ $orders[]=$r; } }
?>
<div class="container py-4">
  <div class="bg-white border rounded-4 p-4">
    <h4 class="mb-3">My Account</h4>
    <div class="mb-3 text-muted">Logged in as <strong><?php echo esc($_SESSION['user_name'] ?? ''); ?></strong></div>

    <h5 class="mt-4">My Orders</h5>
    <?php if(empty($orders)): ?>
      <div class="alert alert-secondary">No orders yet.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table">
          <thead><tr><th>#</th><th>Date</th><th>Total</th><th>Payment</th><th>Order</th><th></th></tr></thead>
          <tbody>
          <?php foreach($orders as $o): ?>
            <tr>
              <td><?php echo esc($o['order_number']); ?></td>
              <td><?php echo esc($o['created_at']); ?></td>
              <td>TZS <?php echo money_tzs($o['total']); ?></td>
              <td><?php echo esc($o['payment_status']); ?></td>
              <td><?php echo esc($o['order_status']); ?></td>
              <td><a class="btn btn-outline-primary btn-sm" href="invoice.php?order_id=<?php echo (int)$o['id']; ?>">View Invoice</a></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
