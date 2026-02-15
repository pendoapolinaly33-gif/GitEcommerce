<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$action = strtolower(trim($_POST['action'] ?? $_GET['action'] ?? 'add'));
$product_id = (int)($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
$qty = (int)($_POST['qty'] ?? $_GET['qty'] ?? 1);
if($qty<=0) $qty=1;
$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? 'cart.php';

cart_init();

function cart_fetch_product($conn,$id){
  $id=(int)$id;
  if($id<=0) return null;
  $stmt=db_prepare($conn,"SELECT id,name,price,stock_qty,status,main_image FROM products WHERE id=? LIMIT 1");
  mysqli_stmt_bind_param($stmt,'i',$id);
  mysqli_stmt_execute($stmt);
  $res=mysqli_stmt_get_result($stmt);
  return $res ? mysqli_fetch_assoc($res) : null;
}

if($action==='add'){
  $p = cart_fetch_product($conn,$product_id);
  if(!$p){ set_flash('error','Product not found.'); redirect($redirect); }
  if(($p['status'] ?? '')!=='active'){ set_flash('error','Product not available.'); redirect($redirect); }
  $stock=(int)($p['stock_qty'] ?? 0);
  if($stock<=0){ set_flash('error','Out of stock.'); redirect($redirect); }

  if(!isset($_SESSION['cart'][$product_id])){
    $_SESSION['cart'][$product_id]=[
      'product_id'=>(int)$p['id'],
      'name'=>$p['name'],
      'price'=>(float)$p['price'],
      'qty'=>0,
      'image'=>$p['main_image'] ?? ''
    ];
  }
  $newQty = (int)$_SESSION['cart'][$product_id]['qty'] + $qty;
  if($newQty>$stock) $newQty=$stock;
  $_SESSION['cart'][$product_id]['qty']=$newQty;

  set_flash('success','Added to cart.');
  redirect($redirect);
}

if($action==='update'){
  $qtys = $_POST['qty'] ?? [];
  if(is_array($qtys)){
    foreach($qtys as $pid=>$q){
      $pid=(int)$pid; $q=(int)$q;
      if($pid<=0) continue;
      if($q<=0){ unset($_SESSION['cart'][$pid]); continue; }
      $p = cart_fetch_product($conn,$pid);
      if($p){
        $stock=(int)($p['stock_qty'] ?? 0);
        if($stock>0 && $q>$stock) $q=$stock;
        if(isset($_SESSION['cart'][$pid])){
          $_SESSION['cart'][$pid]['qty']=$q;
          $_SESSION['cart'][$pid]['name']=$p['name'];
          $_SESSION['cart'][$pid]['price']=(float)$p['price'];
          $_SESSION['cart'][$pid]['image']=$p['main_image'] ?? '';
        }
      }
    }
  }
  set_flash('success','Cart updated.');
  redirect('cart.php');
}

if($action==='remove'){
  if($product_id>0) unset($_SESSION['cart'][$product_id]);
  set_flash('success','Item removed.');
  redirect('cart.php');
}

if($action==='clear'){
  $_SESSION['cart']=[];
  set_flash('success','Cart cleared.');
  redirect('cart.php');
}

set_flash('error','Invalid action.');
redirect($redirect);
