<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: login.php'); exit; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?"); $stmt->execute([$id]); $p = $stmt->fetch();
if (!$p) { echo '<div class="alert alert-danger">Không tìm thấy</div>'; require_once __DIR__ . '/../includes/footer.php'; exit; }

// Get current image
$stmt_img = $pdo->prepare("SELECT url FROM product_images WHERE product_id = ? AND position = 0");
$stmt_img->execute([$id]);
$current_image = $stmt_img->fetchColumn() ?: '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $desc = $_POST['description'] ?? '';
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $image = $_POST['image'] ?? '';
    
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, stock=? WHERE id=?");
        $stmt->execute([$name, $desc, $price, $stock, $id]);
        
        // Update or insert image
        $stmt_check = $pdo->prepare("SELECT id FROM product_images WHERE product_id = ? AND position = 0");
        $stmt_check->execute([$id]);
        $img_id = $stmt_check->fetchColumn();
        
        if ($img_id) {
            $stmt_upd = $pdo->prepare("UPDATE product_images SET url = ? WHERE id = ?");
            $stmt_upd->execute([$image, $img_id]);
        } elseif ($image) {
            $stmt_ins = $pdo->prepare("INSERT INTO product_images (product_id, url, position) VALUES (?, ?, 0)");
            $stmt_ins->execute([$id, $image]);
        }
        
        $pdo->commit();
        header('Location: products.php'); exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Lỗi: " . $e->getMessage();
    }
}
?>
<div class="row">
  <div class="col-md-8">
    <h2>Sửa sản phẩm</h2>
    <form method="post">
      <div class="mb-3"><label>Tên</label><input class="form-control" name="name" value="<?php echo htmlspecialchars($p['name']); ?>" required></div>
      <div class="mb-3"><label>Mô tả</label><textarea class="form-control" name="description"><?php echo htmlspecialchars($p['description']); ?></textarea></div>
      <div class="mb-3"><label>Giá</label><input type="number" step="0.01" class="form-control" name="price" value="<?php echo $p['price']; ?>" required></div>
      <div class="mb-3"><label>Số lượng</label><input type="number" class="form-control" name="stock" value="<?php echo $p['stock']; ?>" required></div>
      <div class="mb-3"><label>URL ảnh</label><input class="form-control" name="image" value="<?php echo htmlspecialchars($current_image); ?>"></div>
      <button class="btn btn-primary">Lưu</button>
      <a href="products.php" class="btn btn-secondary">Quay lại</a>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>