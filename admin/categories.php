<?php
require_once __DIR__ . '/includes/admin_header.php';

if (isset($_GET['delete'])) {
  $id=(int)$_GET['delete'];

  $stmt=db_prepare($conn,"SELECT COUNT(*) c FROM products WHERE category_id=?");
  mysqli_stmt_bind_param($stmt,'i',$id);
  mysqli_stmt_execute($stmt);
  $res=mysqli_stmt_get_result($stmt);
  $c=(int)(($res && ($r=mysqli_fetch_assoc($res))) ? $r['c'] : 0);

  if($c>0){
    set_flash('error','Cannot delete category with products. Move products first.');
  }else{
    $stmt=db_prepare($conn,"DELETE FROM categories WHERE id=? LIMIT 1");
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    set_flash('success','Category deleted.');
  }
  redirect('categories.php');
}

if (is_post()) {
  $name=trim($_POST['name'] ?? '');
  if($name===''){ set_flash('error','Enter category name.'); redirect('categories.php'); }
  $stmt=db_prepare($conn,"INSERT INTO categories(name,status) VALUES (?, 'active')");
  mysqli_stmt_bind_param($stmt,'s',$name);
  mysqli_stmt_execute($stmt);
  set_flash('success','Category added.');
  redirect('categories.php');
}

$cats=[];
$res=mysqli_query($conn,"SELECT id,name,status,created_at FROM categories ORDER BY id DESC");
if($res){ while($r=mysqli_fetch_assoc($res)){ $cats[]=$r; } }
?>
<div class="card">
  <div class="card-header"><h3 class="card-title">Categories</h3></div>
  <div class="card-body">
    <form method="post" class="form-inline mb-3">
      <input class="form-control mr-2" name="name" placeholder="Category name" required>
      <button class="btn btn-primary" type="submit">Add</button>
    </form>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead><tr><th>ID</th><th>Name</th><th>Status</th><th>Date</th><th width="120">Action</th></tr></thead>
        <tbody>
          <?php foreach($cats as $c): ?>
            <tr>
              <td><?php echo (int)$c['id']; ?></td>
              <td><?php echo esc($c['name']); ?></td>
              <td><?php echo esc($c['status']); ?></td>
              <td><?php echo esc($c['created_at']); ?></td>
              <td>
                <a class="btn btn-sm btn-danger" href="categories.php?delete=<?php echo (int)$c['id']; ?>" onclick="return confirm('Delete this category?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if(empty($cats)): ?><tr><td colspan="5" class="text-center text-muted">No categories</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
