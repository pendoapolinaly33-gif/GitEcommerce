<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_admin()) { redirect('index.php'); }

if (is_post()) {
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';
  if ($email=='' || $pass=='') {
    set_flash('error','Enter email and password.');
    redirect('login.php');
  }
  $stmt=db_prepare($conn,"SELECT id, full_name, password_hash, role FROM users WHERE email=? LIMIT 1");
  mysqli_stmt_bind_param($stmt,'s',$email);
  mysqli_stmt_execute($stmt);
  $res=mysqli_stmt_get_result($stmt);
  $u=$res?mysqli_fetch_assoc($res):null;
  if(!$u || !password_verify($pass,$u['password_hash']) || $u['role']!=='admin'){
    set_flash('error','Invalid admin credentials.');
    redirect('login.php');
  }
  $_SESSION['user_id']=(int)$u['id'];
  $_SESSION['user_name']=$u['full_name'];
  $_SESSION['user_role']=$u['role'];
  redirect('index.php');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center"><b>Admin</b> Login</div>
    <div class="card-body">
      <?php $f=get_flash(); if($f): ?>
        <div class="alert alert-<?php echo esc($f['type']==='error'?'danger':$f['type']); ?>"><?php echo esc($f['message']); ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="Admin Email" name="email" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="password" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
        <a href="../index.php" class="btn btn-secondary btn-block">Cancel</a>
      </form>
      <p class="text-muted mt-3 mb-0 small">First registered user becomes admin.</p>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
