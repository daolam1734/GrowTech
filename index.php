<?php
require_once __DIR__ . "/includes/header.php";
require_once __DIR__ . "/functions.php";

$q = $_GET["q"] ?? "";
$category = $_GET["category"] ?? "";
$brand = $_GET["brand"] ?? "";

// Simple search/filter logic
$sql = "SELECT p.*, pi.url as image_url 
        FROM products p 
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.position = 0
        WHERE p.is_active = 1";
$params = [];

if ($q) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($category) {
    $sql .= " AND p.category_id IN (SELECT id FROM categories WHERE slug = ?)";
    $params[] = $category;
}
if ($brand) {
    $sql .= " AND p.brand_id IN (SELECT id FROM brands WHERE name = ?)";
    $params[] = $brand;
}

$sql .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<style>
    .product-grid-item { transition: transform 0.2s, box-shadow 0.2s; border: 1px solid transparent; background: #fff; height: 100%; display: flex; flex-direction: column; border-radius: 4px; overflow: hidden; }
    .product-grid-item:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(211, 47, 47, 0.15); border-color: var(--tet-red, #d32f2f); }
    .product-grid-img { aspect-ratio: 1/1; object-fit: cover; width: 100%; }
    .product-grid-info { padding: 12px; flex-grow: 1; display: flex; flex-direction: column; }
    .product-grid-name { font-size: 13px; line-height: 18px; height: 36px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; color: #333; margin-bottom: 10px; text-decoration: none; }
    .product-grid-price { color: var(--tet-red, #d32f2f); font-size: 16px; font-weight: 600; }
    .product-grid-sold { font-size: 11px; color: #757575; }
    .tet-banner {
        background: linear-gradient(90deg, #b71c1c, #d32f2f);
        color: #ffc107;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        text-align: center;
        border: 2px solid #ffc107;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
</style>

<div class="row">
    <aside class="col-md-2 d-none d-md-block">
        <?php include __DIR__ . "/includes/sidebar.php"; ?>
    </aside>
    <main class="col-md-10">
        <div class="tet-banner">
            <h3 class="fw-bold mb-1">🧧 KHAI XUÂN NHƯ Ý - LÌ XÌ HẾT Ý 🧧</h3>
            <p class="mb-0">Giảm giá lên đến 50% cho tất cả dòng Laptop Gaming & Văn phòng</p>
        </div>

        <?php if ($q): ?>
            <div class="mb-3 text-muted">Kết quả tìm kiếm cho: <strong><?php echo htmlspecialchars($q); ?></strong></div>
        <?php endif; ?>

        <div class="row g-2">
            <?php foreach ($products as $p): 
                $img = $p["image_url"];
                if (!$img || (strpos($img, 'http') !== 0 && strpos($img, '/') !== 0)) {
                    if ($img && (preg_match('/^\d+x\d+/', $img) || strpos($img, 'text=') !== false)) {
                        $img = 'https://placehold.co/' . $img;
                    } else {
                        $img = 'https://placehold.co/600x400?text=No+Image';
                    }
                }
            ?>
                <div class="col-6 col-md-4 col-lg-2-4 mb-2" style="width: 20%;">
                    <a href="product.php?id=<?php echo $p["id"]; ?>" class="text-decoration-none">
                        <div class="product-grid-item">
                            <img src="<?php echo htmlspecialchars($img); ?>" class="product-grid-img" alt="<?php echo htmlspecialchars($p["name"]); ?>" loading="lazy">
                            <div class="product-grid-info">
                                <div class="product-grid-name"><?php echo htmlspecialchars($p["name"]); ?></div>
                                <div class="mt-auto">
                                    <div class="product-grid-price"><?php echo number_format($p["price"], 0, ",", "."); ?> đ</div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div class="small text-warning">
                                            <i class="bi bi-star-fill" style="font-size: 10px;"></i>
                                            <i class="bi bi-star-fill" style="font-size: 10px;"></i>
                                            <i class="bi bi-star-fill" style="font-size: 10px;"></i>
                                            <i class="bi bi-star-fill" style="font-size: 10px;"></i>
                                            <i class="bi bi-star-half" style="font-size: 10px;"></i>
                                        </div>
                                        <div class="product-grid-sold">Đã bán 100+</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
                <div class="col-12 text-center py-5">
                    <img src="https://deo.shopeemobile.com/shopee/shopee-pcmall-live-sg/assets/a60759ad1dabe909c46a817ecbf71878.png" style="width: 100px;" class="mb-3">
                    <p class="text-muted">Không tìm thấy sản phẩm nào phù hợp.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
