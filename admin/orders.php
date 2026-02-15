<?php
require_once __DIR__ . '/includes/admin_header.php';

if (is_post() && isset($_POST['update_status'])) {
  $order_id = (int)($_POST['order_id'] ?? 0);
  $payment_status = trim($_POST['payment_status'] ?? 'pending');
  $order_status   = trim($_POST['order_status'] ?? 'pending');
  $allowedPay = ['pending','paid','failed'];
  $allowedOrd = ['pending','processing','completed','cancelled'];
  if (!in_array($payment_status, $allowedPay)) $payment_status = 'pending';
  if (!in_array($order_status, $allowedOrd)) $order_status = 'pending';
  if ($order_id > 0) {
    $stmt = db_prepare($conn, "UPDATE orders SET payment_status=?, order_status=? WHERE id=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'ssi', $payment_status, $order_status, $order_id);
    mysqli_stmt_execute($stmt);
    set_flash('success', 'Order status updated.');
  }
  redirect('orders.php');
}


$orders=[];
$res=mysqli_query($conn,"SELECT o.*, u.full_name, u.email FROM orders o LEFT JOIN users u ON u.id=o.user_id ORDER BY o.id DESC");
if($res){ while($r=mysqli_fetch_assoc($res)){ $orders[]=$r; } }
?>
<div class="card">
  <div class="card-header"><h3 class="card-title">Orders</h3></div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead><tr><th>ID</th><th>Order #</th><th>Customer</th><th>Total</th><th>Payment</th><th>Order</th><th>Date</th><th width="260">Update</th></tr></thead>
        <tbody>
          <?php foreach($orders as $o): ?>
            <tr>
              <td><?php echo (int)$o['id']; ?></td>
              <td><?php echo esc($o['order_number']); ?></td>
              <td><?php echo esc(($o['full_name'] ?? '') . ' (' . ($o['email'] ?? '') . ')'); ?></td>
              <td><?php echo money_tzs($o['total']); ?></td>
              <td><?php echo esc($o['payment_status']); ?></td>
              <td><?php echo esc($o['order_status']); ?></td>
              <td><?php echo esc($o['created_at']); ?></td>
              <td>
                <form method="post" class="d-flex" style="gap:8px; align-items:center;">
                  <input type="hidden" name="update_status" value="1">
                  <input type="hidden" name="order_id" value="<?php echo (int)$o['id']; ?>">

                  <select name="payment_status" class="form-control form-control-sm" style="min-width:120px;">
                    <option value="pending" <?php echo ($o['payment_status']=='pending')?'selected':''; ?>>Pending</option>
                    <option value="paid" <?php echo ($o['payment_status']=='paid')?'selected':''; ?>>Paid</option>
                    <option value="failed" <?php echo ($o['payment_status']=='failed')?'selected':''; ?>>Decline</option>
                  </select>

                  <select name="order_status" class="form-control form-control-sm" style="min-width:130px;">
                    <option value="pending" <?php echo ($o['order_status']=='pending')?'selected':''; ?>>Pending</option>
                    <option value="processing" <?php echo ($o['order_status']=='processing')?'selected':''; ?>>Processing</option>
                    <option value="completed" <?php echo ($o['order_status']=='completed')?'selected':''; ?>>Completed</option>
                    <option value="cancelled" <?php echo ($o['order_status']=='cancelled')?'selected':''; ?>>Decline</option>
                  </select>

                  <button class="btn btn-sm btn-primary" type="submit">Save</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if(empty($orders)): ?><tr><td colspan="8" class="text-center text-muted">No orders</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
