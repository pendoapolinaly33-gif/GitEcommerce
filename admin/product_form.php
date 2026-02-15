<?php
require_once __DIR__ . '/includes/admin_header.php';

$categories = fetch_categories($conn);
$id = (int)($_GET['id'] ?? 0);
$editing = $id > 0;

$product = [
  'name'=>'','brand'=>'','category_id'=>0,'price'=>0,'stock_qty'=>0,'status'=>'active','description'=>'','main_image'=>''
];

if ($editing) {
  $p = get_product($conn, $id);
  if (!$p) { set_flash('error','Product not found.'); redirect('products.php'); }
  $product = array_merge($product, $p);
}

if (is_post()) {
  $name = trim($_POST['name'] ?? '');
  $brand = trim($_POST['brand'] ?? '');
  $category_id = (int)($_POST['category_id'] ?? 0);
  $price = (float)($_POST['price'] ?? 0);
  $stock = (int)($_POST['stock_qty'] ?? 0);
  $status = strtolower(trim($_POST['status'] ?? 'active'));
  $description = trim($_POST['description'] ?? '');

  if ($name=='' || $category_id<=0) {
    set_flash('error','Name and category are required.');
    redirect($editing ? ('product_form.php?id='.$id) : 'product_form.php');
  }

  $main_image = $product['main_image'] ?? '';
  $upMain = upload_one_image('main_image');
  if ($upMain !== '') { $main_image = $upMain; }

  if ($editing) {
    $stmt = db_prepare($conn, "UPDATE products SET name=?, brand=?, category_id=?, price=?, stock_qty=?, status=?, description=?, main_image=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'ssidissssi', $name, $brand, $category_id, $price, $stock, $status, $description, $main_image, $id);
    mysqli_stmt_execute($stmt);
  } else {
    $stmt = db_prepare($conn, "INSERT INTO products(name,brand,category_id,price,stock_qty,status,description,main_image) VALUES (?,?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'ssidisss', $name, $brand, $category_id, $price, $stock, $status, $description, $main_image);
    mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($conn);
    $editing = true;
  }

  $gallery = upload_many_images('gallery_images');
  foreach ($gallery as $img) {
    $stmt = db_prepare($conn, "INSERT INTO product_images(product_id,image_path) VALUES (?,?)");
    mysqli_stmt_bind_param($stmt, 'is', $id, $img);
    mysqli_stmt_execute($stmt);
  }

  set_flash('success','Product saved.');
  redirect('products.php');
}

$existingGallery = $editing ? get_product_images($conn, $id) : [];
?>
<div class="card">
  <div class="card-header"><h3 class="card-title"><?php echo $editing?'Edit Product':'Add Product'; ?></h3></div>
  <div class="card-body">
    <?php if(empty($categories)): ?>
      <div class="alert alert-warning">
        Please add at least one category first.
        <a href="categories.php" class="btn btn-sm btn-primary ml-2">Add Category</a>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Name *</label>
            <input class="form-control" name="name" value="<?php echo esc($product['name']); ?>" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Brand</label>
            <input class="form-control" name="brand" value="<?php echo esc($product['brand']); ?>">
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Category *</label>
            <select class="form-control" name="category_id" required>
              <option value="">Select</option>
              <?php foreach($categories as $c): ?>
                <option value="<?php echo (int)$c['id']; ?>" <?php echo ((int)$product['category_id']===(int)$c['id'])?'selected':''; ?>>
                  <?php echo esc($c['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Price (TZS)</label>
            <input class="form-control" type="number" step="0.01" name="price" value="<?php echo esc($product['price']); ?>">
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Stock Qty</label>
            <input class="form-control" type="number" name="stock_qty" value="<?php echo esc($product['stock_qty']); ?>">
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status">
              <option value="active" <?php echo (strtolower($product['status'])==='active')?'selected':''; ?>>Active</option>
              <option value="inactive" <?php echo (strtolower($product['status'])==='inactive')?'selected':''; ?>>Inactive</option>
            </select>
          </div>
        </div>

        <div class="col-md-8">
          <div class="form-group">
            <label>Main Image</label>
            <input class="form-control" type="file" name="main_image" accept="image/*">
            <?php if(!empty($product['main_image'])): ?>
              <div class="mt-2"><img src="../<?php echo esc($product['main_image']); ?>" style="height:70px;border-radius:8px;object-fit:cover" alt=""></div>
            <?php endif; ?>
          </div>
        </div>

        <div class="col-12">
          <div class="form-group">
            <label>Gallery Images (multiple)</label>
            <input class="form-control" type="file" name="gallery_images[]" accept="image/*" multiple>
            <?php if(!empty($existingGallery)): ?>
              <div class="d-flex flex-wrap mt-2" style="gap:8px;">
                <?php foreach($existingGallery as $img): ?>
                  <img src="../<?php echo esc($img); ?>" style="height:60px;width:60px;object-fit:cover;border-radius:8px" alt="">
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="col-12">
          <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" name="description" rows="4"><?php echo esc($product['description']); ?></textarea>
          </div>
        </div>
      </div>

      <button class="btn btn-primary"><?php echo $editing?'Update':'Save'; ?></button>
      <a class="btn btn-secondary" href="products.php">Cancel</a>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
