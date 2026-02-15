<?php
require_once __DIR__ . '/includes/header.php';
if(user_is_logged_in()){ redirect('account.php'); }

if(is_post()){
  $full_name=trim($_POST['full_name'] ?? '');
  $email=trim($_POST['email'] ?? '');
  $phone=trim($_POST['phone'] ?? '');
  $address=trim($_POST['address'] ?? '');
  $pass=$_POST['password'] ?? '';

  if($full_name=='' || $email=='' || $pass==''){
    set_flash('error','Please fill required fields.');
    redirect('register.php');
  }

  $stmt=db_prepare($conn,"SELECT id FROM users WHERE email=? LIMIT 1");
  mysqli_stmt_bind_param($stmt,'s',$email);
  mysqli_stmt_execute($stmt);
  $res=mysqli_stmt_get_result($stmt);
  if($res && mysqli_fetch_assoc($res)){
    set_flash('error','Email already exists.');
    redirect('register.php');
  }

  $hash=password_hash($pass,PASSWORD_BCRYPT);
  $role='customer';
  $stmt=db_prepare($conn,"INSERT INTO users(full_name,email,phone,address,password_hash,role,status) VALUES (?,?,?,?,?,?, 'active')");
  mysqli_stmt_bind_param($stmt,'ssssss',$full_name,$email,$phone,$address,$hash,$role);
  mysqli_stmt_execute($stmt);
  $uid=mysqli_insert_id($conn);
  ensure_first_user_admin($conn,$uid);

  set_flash('success','Account created. Please login.');
  redirect('login.php');
}
?>
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="bg-white border rounded-4 p-4">
        <h4 class="mb-3">Create Account</h4>
        <form method="post">
          <div class="mb-2">
            <label class="form-label">Full Name *</label>
            <input class="form-control" name="full_name" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Email *</label>
            <input class="form-control" type="email" name="email" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Phone</label>
            <input class="form-control" name="phone">
          </div>
          <div class="mb-2">
            <label class="form-label">Address</label>
            <input class="form-control" name="address">
          </div>
          <div class="mb-3">
            <label class="form-label">Password *</label>
            <input class="form-control" type="password" name="password" required>
          </div>
          <button class="btn btn-primary w-100" type="submit">Register</button>
          <div class="text-center mt-3">
            <a href="login.php">Already have an account? Login</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
