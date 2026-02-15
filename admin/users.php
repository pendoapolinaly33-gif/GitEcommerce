<?php
require_once __DIR__ . '/includes/admin_header.php';

if(isset($_GET['delete'])){
  $id=(int)$_GET['delete'];
  $stmt=db_prepare($conn,"SELECT role FROM users WHERE id=? LIMIT 1");
  mysqli_stmt_bind_param($stmt,'i',$id);
  mysqli_stmt_execute($stmt);
  $res=mysqli_stmt_get_result($stmt);
  $u=$res?mysqli_fetch_assoc($res):null;
  if(!$u){ set_flash('error','User not found.'); }
  else if(($u['role'] ?? '')==='admin'){ set_flash('error','You cannot delete an admin.'); }
  else{
    $stmt=db_prepare($conn,"DELETE FROM users WHERE id=? LIMIT 1");
    mysqli_stmt_bind_param($stmt,'i',$id);
    mysqli_stmt_execute($stmt);
    set_flash('success','User deleted.');
  }
  redirect('users.php');
}

$users=[];
$res=mysqli_query($conn,"SELECT id, full_name, email, phone, role, status, created_at FROM users ORDER BY id DESC");
if($res){ while($r=mysqli_fetch_assoc($res)){ $users[]=$r; } }
?>
<div class="card">
  <div class="card-header"><h3 class="card-title">Users</h3></div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Date</th><th width="120">Action</th></tr></thead>
        <tbody>
          <?php foreach($users as $u): ?>
            <tr>
              <td><?php echo (int)$u['id']; ?></td>
              <td><?php echo esc($u['full_name']); ?></td>
              <td><?php echo esc($u['email']); ?></td>
              <td><?php echo esc($u['phone']); ?></td>
              <td><?php echo esc($u['role']); ?></td>
              <td><?php echo esc($u['status']); ?></td>
              <td><?php echo esc($u['created_at']); ?></td>
              <td>
                <?php if(($u['role'] ?? '')!=='admin'): ?>
                  <a class="btn btn-sm btn-danger" href="users.php?delete=<?php echo (int)$u['id']; ?>" onclick="return confirm('Delete this user?')">Delete</a>
                <?php else: ?>
                  <span class="text-muted small">Admin</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if(empty($users)): ?><tr><td colspan="8" class="text-center text-muted">No users</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
