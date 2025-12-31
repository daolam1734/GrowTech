<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if ($stmt->execute([$status, $order_id])) {
        set_flash("Cập nhật trạng thái đơn hàng #$order_id thành công.", "success");
    } else {
        set_flash("Lỗi khi cập nhật trạng thái.", "danger");
    }
    header("Location: orders.php");
    exit;
}

// Fetch orders
$stmt = $pdo->query("
    SELECT o.*, u.full_name as customer_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý đơn hàng</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="products.php">Sản phẩm</a></li>
                <li class="breadcrumb-item active">Đơn hàng</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thanh toán</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td><strong>#<?php echo $o['id']; ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($o['customer_name'] ?: 'Khách vãng lai'); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($o['phone']); ?></small>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>
                                <td class="text-danger fw-bold"><?php echo number_format($o['total_amount'], 0, ',', '.'); ?> đ</td>
                                <td>
                                    <?php
                                    $status_class = 'secondary';
                                    switch($o['status']) {
                                        case 'pending': $status_class = 'warning'; break;
                                        case 'confirmed': $status_class = 'info'; break;
                                        case 'shipping': $status_class = 'primary'; break;
                                        case 'completed': $status_class = 'success'; break;
                                        case 'cancelled': $status_class = 'danger'; break;
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $status_class; ?>">
                                        <?php echo ucfirst($o['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-uppercase"><?php echo $o['payment_method']; ?></small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Xử lý
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form method="POST" class="px-3 py-1">
                                                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                                    <select name="status" class="form-select form-select-sm mb-2" onchange="this.form.submit()">
                                                        <option value="pending" <?php echo $o['status'] == 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                                        <option value="confirmed" <?php echo $o['status'] == 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                                        <option value="shipping" <?php echo $o['status'] == 'shipping' ? 'selected' : ''; ?>>Đang giao</option>
                                                        <option value="completed" <?php echo $o['status'] == 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                                                        <option value="cancelled" <?php echo $o['status'] == 'cancelled' ? 'selected' : ''; ?>>Hủy đơn</option>
                                                    </select>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="order_detail.php?id=<?php echo $o['id']; ?>">Xem chi tiết</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Chưa có đơn hàng nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
