<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); exit;
}

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Sản phẩm</h2>
  <div>
    <a href="orders.php" class="btn btn-outline-primary me-2">Quản lý đơn hàng</a>
    <a href="add_product.php" class="btn btn-success">Thêm sản phẩm</a>
  </div>
</div>
<table class="table align-middle">
  <thead><tr><th>ID</th><th>Ảnh</th><th>Tên</th><th>Giá</th><th>Kho</th><th>Hành động</th></tr></thead>
  <tbody>
    <?php foreach ($products as $p): 
      $img = getProductImage($p['id']);
    ?>
      <tr>
        <td><?php echo $p['id']; ?></td>
        <td><img src="<?php echo htmlspecialchars($img); ?>" width="50" height="40" style="object-fit: cover;" class="rounded border"></td>
        <td><?php echo htmlspecialchars($p['name']); ?></td>
        <td><?php echo number_format($p['price'], 0, ',', '.'); ?> đ</td>
        <td><?php echo $p['stock']; ?></td>
        <td>
          <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">Sửa</a>
          <a href="delete_product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">Xóa</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>