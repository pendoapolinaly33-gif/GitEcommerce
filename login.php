<?php
require_once __DIR__ . '/includes/header.php';
if(user_is_logged_in()){ redirect('account.php'); }

if(is_post()){
  $email=trim($_POST['email'] ?? '');
  $pass=$_POST['password'] ?? '';
  if($email=='' || $pass==''){
    set_flash('error','Please enter email and password.');
    redirect('login.php');
  }
  $stmt=db_prepare($conn,"SELECT id, full_name, password_hash, role FROM users WHERE email=? LIMIT 1");
  mysqli_stmt_bind_param($stmt,'s',$email);
  mysqli_stmt_execute($stmt);
  $res=mysqli_stmt_get_result($stmt);
  $u=$res?mysqli_fetch_assoc($res):null;
  if(!$u || !password_verify($pass,$u['password_hash'])){
    set_flash('error','Invalid login credentials.');
    redirect('login.php');
  }
  $_SESSION['user_id']=(int)$u['id'];
  $_SESSION['user_name']=$u['full_name'];
  $_SESSION['user_role']=$u['role'];
  set_flash('success','Welcome back!');
  redirect('account.php');
}
?>
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="bg-white border rounded-4 p-4">
        <h4 class="mb-3">Login</h4>
        <form method="post">
          <div class="mb-2">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required>
          </div>
          <button class="btn btn-primary w-100" type="submit">Login</button>
          <div class="text-center mt-3">
            <a href="register.php">Create account</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
