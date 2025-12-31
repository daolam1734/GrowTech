<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/functions.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$product = getProduct($id);
$specs = getProductSpecs($id);

// handle add to cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["quantity"])) {
    if (!$product) {
        header("Location: index.php");
        exit;
    }
    $qty = max(1, (int)$_POST["quantity"]);
    if (!isset($_SESSION["cart"])) $_SESSION["cart"] = [];
    if (isset($_SESSION["cart"][$id])) {
        $_SESSION["cart"][$id] += $qty;
    } else {
        $_SESSION["cart"][$id] = $qty;
    }
    header("Location: cart.php");
    exit;
}

require_once __DIR__ . "/includes/header.php";

if (!$product) {
    echo "<div class='alert alert-danger'>Không tìm thấy sản phẩm.</div>";
    require_once __DIR__ . "/includes/footer.php";
    exit;
}
?>

<style>
    .product-detail-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee; }
    .product-price { font-size: 32px; color: var(--tet-red, #d32f2f); background: #fff5f5; padding: 15px 20px; margin: 20px 0; border-radius: 4px; font-weight: bold; }
    .spec-table th { width: 30%; background: #fdfdfd; color: #666; font-weight: 600; }
    .spec-table td { color: #333; }
    .btn-add-cart { border-color: var(--tet-red, #d32f2f); color: var(--tet-red, #d32f2f); transition: all 0.3s; }
    .btn-add-cart:hover { background: #fff5f5; border-color: var(--tet-red, #d32f2f); color: var(--tet-red, #d32f2f); }
    .btn-buy-now { background-color: var(--tet-red, #d32f2f); border: none; transition: all 0.3s; }
    .btn-buy-now:hover { background-color: var(--tet-dark-red, #b71c1c); transform: translateY(-2px); }
</style>

<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/weblaptop" class="text-decoration-none text-danger">Trang chủ</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product["name"]); ?></li>
        </ol>
    </nav>

    <div class="product-detail-card mb-4">
        <div class="row">
            <div class="col-md-5">
                <img src="<?php echo htmlspecialchars(getProductImage($product["id"])); ?>" class="img-fluid rounded border" alt="<?php echo htmlspecialchars($product["name"]); ?>">
            </div>
            <div class="col-md-7">
                <h2 class="fw-bold fs-4 mb-3"><?php echo htmlspecialchars($product["name"]); ?></h2>
                <div class="d-flex align-items-center mb-3">
                    <span class="text-warning me-2">4.5 <i class="bi bi-star-fill"></i></span>
                    <span class="border-start ps-2 text-muted">120 Đánh giá</span>
                    <span class="border-start ps-2 ms-2 text-muted">500 Đã bán</span>
                </div>

                <div class="product-price">
                    <?php echo number_format($product["price"], 0, ",", "."); ?> đ
                </div>

                <div class="mb-4">
                    <div class="row mb-3">
                        <div class="col-3 text-muted">Vận chuyển</div>
                        <div class="col-9">
                            <i class="bi bi-truck me-2 text-danger"></i> Miễn phí vận chuyển cho đơn hàng trên 10tr
                        </div>
                    </div>
                </div>

                <form method="post" class="mt-4">
                    <div class="row align-items-center mb-4">
                        <div class="col-3 text-muted">Số lượng</div>
                        <div class="col-9 d-flex align-items-center">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo (int)$product["stock"]; ?>" class="form-control w-auto me-3">
                            <span class="text-muted small"><?php echo (int)$product["stock"]; ?> sản phẩm có sẵn</span>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-outline-danger btn-add-cart px-4 py-2">
                            <i class="bi bi-cart-plus me-2"></i> Thêm Vào Giỏ Hàng
                        </button>
                        <button type="submit" class="btn btn-danger btn-buy-now px-5 py-2">Mua Ngay</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <div class="product-detail-card mb-4">
                <h5 class="bg-light p-3 mb-4">CHI TIẾT SẢN PHẨM</h5>
                <table class="table spec-table">
                    <tbody>
                        <?php if ($specs): ?>
                            <tr><th>CPU</th><td><?php echo htmlspecialchars($specs["cpu"]); ?></td></tr>
                            <tr><th>RAM</th><td><?php echo htmlspecialchars($specs["ram"]); ?></td></tr>
                            <tr><th>Ổ cứng</th><td><?php echo htmlspecialchars($specs["storage"]); ?></td></tr>
                            <tr><th>Card đồ họa</th><td><?php echo htmlspecialchars($specs["gpu"]); ?></td></tr>
                            <tr><th>Màn hình</th><td><?php echo htmlspecialchars($specs["screen"]); ?></td></tr>
                            <tr><th>Hệ điều hành</th><td><?php echo htmlspecialchars($specs["os"]); ?></td></tr>
                            <tr><th>Trọng lượng</th><td><?php echo htmlspecialchars($specs["weight"]); ?></td></tr>
                        <?php else: ?>
                            <tr><td colspan="2" class="text-muted">Đang cập nhật thông số...</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <h5 class="bg-light p-3 my-4">MÔ TẢ SẢN PHẨM</h5>
                <div class="px-3">
                    <?php echo nl2br(htmlspecialchars($product["description"])); ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="product-detail-card">
                <h6 class="mb-3">Sản phẩm tương tự</h6>
                <!-- Placeholder for similar products -->
                <div class="text-muted small">Đang cập nhật...</div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
