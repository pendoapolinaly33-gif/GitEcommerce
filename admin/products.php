<?php
require_once __DIR__ . '/includes/admin_header.php';

if(isset($_GET['delete'])){
  $id = (int)$_GET['delete'];

  // Collect file paths (main + gallery) for storage delete
  $paths = [];

  $stmt = db_prepare($conn, "SELECT main_image FROM products WHERE id=? LIMIT 1");
  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  if($res && ($row = mysqli_fetch_assoc($res))){
    if(!empty($row['main_image'])) $paths[] = $row['main_image'];
  }

  $stmt = db_prepare($conn, "SELECT image_path FROM product_images WHERE product_id=?");
  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  if($res){
    while($r = mysqli_fetch_assoc($res)){
      if(!empty($r['image_path'])) $paths[] = $r['image_path'];
    }
  }

  // Delete DB rows
  $stmt = db_prepare($conn, "DELETE FROM product_images WHERE product_id=?");
  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);

  $stmt = db_prepare($conn, "DELETE FROM products WHERE id=? LIMIT 1");
  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);

  // Delete files from storage (optional)
  foreach($paths as $p){
    $abs = __DIR__ . '/../' . ltrim($p, '/');
    if(is_file($abs)) { @unlink($abs); }
  }

  set_flash('success','Product deleted permanently.');
  redirect('products.php');
}


$rows=[];
$res=mysqli_query($conn,"SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC");
if($res){ while($r=mysqli_fetch_assoc($res)){ $rows[]=$r; } }
?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title">Products</h3>
    <a class="btn btn-primary btn-sm" href="product_form.php">Add Product</a>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr><th>ID</th><th>Name</th><th>Brand</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th width="160">Action</th></tr>
        </thead>
        <tbody>
          <?php foreach($rows as $p): ?>
          <tr>
            <td><?php echo (int)$p['id']; ?></td>
            <td><?php echo esc($p['name']); ?></td>
            <td><?php echo esc($p['brand']); ?></td>
            <td><?php echo esc($p['category_name'] ?? ''); ?></td>
            <td><?php echo money_tzs($p['price']); ?></td>
            <td><?php echo (int)$p['stock_qty']; ?></td>
            <td><?php echo esc($p['status']); ?></td>
            <td>
              <a class="btn btn-sm btn-info" href="product_form.php?id=<?php echo (int)$p['id']; ?>">Edit</a>
              <a class="btn btn-sm btn-danger" href="products.php?delete=<?php echo (int)$p['id']; ?>" onclick="return confirm('Delete this product?')">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($rows)): ?><tr><td colspan="8" class="text-center text-muted">No products</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
