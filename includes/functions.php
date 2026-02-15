<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// -------------------- Basic helpers --------------------
function esc($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function is_post(){ return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'; }
function redirect($url='index.php'){ header("Location: ".$url); exit; }
function money_tzs($amount){ return number_format((float)$amount, 0, '.', ','); }

// -------------------- DB helpers --------------------
function db_prepare($conn, $sql){
  $stmt = mysqli_prepare($conn, $sql);
  if(!$stmt){
    die("SQL Prepare failed: " . mysqli_error($conn) . " Query: " . $sql);
  }
  return $stmt;
}

// -------------------- Flash --------------------
function set_flash($type, $message){
  $_SESSION['flash'] = ['type'=>$type, 'message'=>$message, 'time'=>time()];
}
function get_flash(){
  if (!empty($_SESSION['flash'])) {
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
  }
  return null;
}

// -------------------- Auth helpers --------------------
function user_is_logged_in(){ return !empty($_SESSION['user_id']); }
function is_admin(){ return (($_SESSION['user_role'] ?? '') === 'admin'); }

function require_login(){
  if (!user_is_logged_in()){
    set_flash('error','Please log in first.');
    redirect('login.php');
  }
}
function require_admin_login(){
  if (!is_admin()){
    set_flash('error','Admin access required.');
    redirect('login.php');
  }
}

function current_user($conn){
  $uid = (int)($_SESSION['user_id'] ?? 0);
  if ($uid <= 0) return null;
  $stmt = db_prepare($conn, "SELECT id, full_name, email, phone, address, role FROM users WHERE id=? LIMIT 1");
  mysqli_stmt_bind_param($stmt, 'i', $uid);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  return $res ? mysqli_fetch_assoc($res) : null;
}

// First registered user becomes admin automatically
function ensure_first_user_admin($conn, $newUserId){
  $res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM users");
  $row = $res ? mysqli_fetch_assoc($res) : ['c'=>0];
  if ((int)$row['c'] === 1){
    $id = (int)$newUserId;
    mysqli_query($conn, "UPDATE users SET role='admin' WHERE id={$id} LIMIT 1");
  }
}

// -------------------- Cart --------------------
function cart_init(){
  if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) $_SESSION['cart'] = [];
}
function cart_count_items(){
  $c=0;
  foreach(($_SESSION['cart'] ?? []) as $it){ $c += (int)($it['qty'] ?? 0); }
  return $c;
}
function cart_total(){
  $t=0;
  foreach(($_SESSION['cart'] ?? []) as $it){ $t += ((float)$it['price'] * (int)$it['qty']); }
  return $t;
}

// -------------------- Categories --------------------
function fetch_categories($conn){
  $cats=[];
  $res = mysqli_query($conn, "SELECT id, name FROM categories WHERE status='active' ORDER BY name ASC");
  if($res){ while($r=mysqli_fetch_assoc($res)){ $cats[]=$r; } }
  return $cats;
}

// -------------------- Products --------------------
function search_products($conn, $params=[]){
  $keyword = trim($params['keyword'] ?? '');
  $category_id = (int)($params['category_id'] ?? 0);

  $sql = "SELECT p.*, c.name AS category_name
          FROM products p
          LEFT JOIN categories c ON c.id=p.category_id
          WHERE p.status='active'";
  $types = "";
  $bind = [];

  if ($keyword !== '') {
    $sql .= " AND (p.name LIKE ? OR p.brand LIKE ?)";
    $kw = "%".$keyword."%";
    $types .= "ss";
    $bind[] = $kw; $bind[] = $kw;
  }
  if ($category_id > 0) {
    $sql .= " AND p.category_id=?";
    $types .= "i";
    $bind[] = $category_id;
  }
  $sql .= " ORDER BY p.id DESC";

  $stmt = db_prepare($conn, $sql);
  if ($types !== "") { mysqli_stmt_bind_param($stmt, $types, ...$bind); }
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);

  $out=[];
  if($res){ while($r=mysqli_fetch_assoc($res)){ $out[]=$r; } }
  return $out;
}

function get_product($conn, $id){
  $id=(int)$id;
  if($id<=0) return null;
  $stmt = db_prepare($conn, "SELECT p.*, c.name AS category_name
                             FROM products p
                             LEFT JOIN categories c ON c.id=p.category_id
                             WHERE p.id=? LIMIT 1");
  mysqli_stmt_bind_param($stmt,'i',$id);
  mysqli_stmt_execute($stmt);
  $res=mysqli_stmt_get_result($stmt);
  return $res ? mysqli_fetch_assoc($res) : null;
}

function get_product_images($conn, $product_id){
  $imgs=[];
  $pid=(int)$product_id;
  if($pid<=0) return $imgs;
  $stmt = db_prepare($conn, "SELECT image_path FROM product_images WHERE product_id=? ORDER BY id ASC");
  mysqli_stmt_bind_param($stmt,'i',$pid);
  mysqli_stmt_execute($stmt);
  $res=mysqli_stmt_get_result($stmt);
  if($res){
    while($r=mysqli_fetch_assoc($res)){
      if(!empty($r['image_path'])) $imgs[]=$r['image_path'];
    }
  }
  return $imgs;
}

// -------------------- Uploads --------------------
function upload_one_image($field, $destRel='uploads/products'){
  if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return '';
  $name = $_FILES[$field]['name'] ?? 'image.jpg';
  $tmp  = $_FILES[$field]['tmp_name'];
  $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  if(!in_array($ext, ['jpg','jpeg','png','webp','gif'])) return '';
  $destAbs = __DIR__ . '/../' . trim($destRel,'/');
  if(!is_dir($destAbs)) @mkdir($destAbs, 0775, true);
  $safe = preg_replace('/[^a-zA-Z0-9_\-]/','_', pathinfo($name, PATHINFO_FILENAME));
  $fname = $safe.'_'.date('Ymd_His').'_'.mt_rand(1000,9999).'.'.$ext;
  $full  = $destAbs.'/'.$fname;
  if(move_uploaded_file($tmp, $full)) return trim($destRel,'/').'/'.$fname;
  return '';
}

function upload_many_images($field, $destRel='uploads/products'){
  $out=[];
  if(empty($_FILES[$field]) || !is_array($_FILES[$field]['tmp_name'])) return $out;

  $destAbs = __DIR__ . '/../' . trim($destRel,'/');
  if(!is_dir($destAbs)) @mkdir($destAbs, 0775, true);

  $n=count($_FILES[$field]['tmp_name']);
  for($i=0;$i<$n;$i++){
    if(($_FILES[$field]['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) continue;
    $name=$_FILES[$field]['name'][$i] ?? ('img'.$i.'.jpg');
    $tmp =$_FILES[$field]['tmp_name'][$i];
    $ext=strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if(!in_array($ext, ['jpg','jpeg','png','webp','gif'])) continue;
    $safe=preg_replace('/[^a-zA-Z0-9_\-]/','_', pathinfo($name, PATHINFO_FILENAME));
    $fname=$safe.'_'.date('Ymd_His').'_'.mt_rand(1000,9999).'.'.$ext;
    $full=$destAbs.'/'.$fname;
    if(move_uploaded_file($tmp,$full)) $out[]=trim($destRel,'/').'/'.$fname;
  }
  return $out;
}
?>
