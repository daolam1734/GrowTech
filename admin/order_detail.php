<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch order info
$stmt = $pdo->prepare("
    SELECT o.*, u.full_name as customer_name, u.email as customer_email
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    set_flash("Không tìm thấy đơn hàng.", "danger");
    header("Location: orders.php");
    exit;
}

// Fetch order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name as product_name, p.sku
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <div class="mb-4">
        <a href="orders.php" class="btn btn-link p-0 text-decoration-none"><i class="bi bi-arrow-left"></i> Quay lại danh sách</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Chi tiết đơn hàng #<?php echo $order['id']; ?></h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Giá</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $it): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($it['product_name']); ?></div>
                                            <small class="text-muted">SKU: <?php echo htmlspecialchars($it['sku']); ?></small>
                                        </td>
                                        <td class="text-center"><?php echo number_format($it['price'], 0, ',', '.'); ?> đ</td>
                                        <td class="text-center"><?php echo $it['quantity']; ?></td>
                                        <td class="text-end fw-bold"><?php echo number_format($it['price'] * $it['quantity'], 0, ',', '.'); ?> đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end">Tổng cộng:</td>
                                    <td class="text-end text-danger fw-bold fs-5"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Thông tin khách hàng</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Họ tên:</strong> <?php echo htmlspecialchars($order['customer_name'] ?: 'Khách vãng lai'); ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email'] ?: 'N/A'); ?></p>
                    <p class="mb-1"><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                    <hr>
                    <p class="mb-1"><strong>Địa chỉ giao hàng:</strong></p>
                    <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Trạng thái & Thanh toán</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Trạng thái:</strong> 
                        <span class="badge bg-info"><?php echo ucfirst($order['status']); ?></span>
                    </p>
                    <p class="mb-2">
                        <strong>Phương thức:</strong> 
                        <span class="text-uppercase"><?php echo $order['payment_method']; ?></span>
                    </p>
                    <p class="mb-0">
                        <strong>Ngày đặt:</strong><br>
                        <small class="text-muted"><?php echo date('d/m/Y H:i:s', strtotime($order['created_at'])); ?></small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
