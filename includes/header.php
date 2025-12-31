<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../functions.php";
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GrowTech - Chuẩn công nghệ – vững niềm tin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/weblaptop/assets/css/style.css" rel="stylesheet">
  <style>
    :root { 
      --tet-red: #d32f2f; 
      --tet-gold: #ffc107;
      --tet-dark-red: #b71c1c;
    }
    .tet-header { 
      background: linear-gradient(135deg, var(--tet-red), var(--tet-dark-red)); 
      color: #fff; 
      border-bottom: 4px solid var(--tet-gold);
      position: relative;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .tet-header a { color: #fff; text-decoration: none; font-size: 13px; }
    .tet-header a:hover { color: var(--tet-gold); }
    .search-bar-container { background: #fff; border-radius: 25px; padding: 3px; display: flex; flex-grow: 1; margin: 0 50px; box-shadow: inset 0 2px 5px rgba(0,0,0,0.1); }
    .search-input { border: none; flex-grow: 1; padding: 10px 20px; outline: none; border-radius: 25px 0 0 25px; }
    .search-btn { background: var(--tet-red); color: #fff; border: none; padding: 0 25px; border-radius: 0 25px 25px 0; transition: all 0.3s; }
    .search-btn:hover { background: var(--tet-dark-red); transform: scale(1.05); }
    .cart-icon { font-size: 28px; position: relative; margin-left: 20px; color: var(--tet-gold) !important; transition: transform 0.3s; }
    .cart-icon:hover { transform: scale(1.1); }
    .cart-badge { position: absolute; top: -5px; right: -10px; background: var(--tet-gold); color: var(--tet-red); border-radius: 10px; padding: 0 6px; font-size: 12px; font-weight: bold; border: 1px solid var(--tet-red); }
    .nav-top { font-size: 13px; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .logo-text { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; letter-spacing: 1px; text-shadow: 1px 1px 2px rgba(0,0,0,0.3); }
    .slogan { font-size: 0.8rem; color: var(--tet-gold); font-style: italic; margin-top: -5px; }
    .tet-decoration { position: absolute; pointer-events: none; opacity: 0.15; font-size: 2rem; }
    
    #search-suggestions {
      position: absolute; z-index: 1200; left: 0; right: 0; top: 100%; background: #fff; border: 1px solid #e9e9e9; border-radius: 6px; display: none; max-height: 420px; overflow: auto; box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    #search-suggestions.show { display: block; }
    .suggestion-item { text-decoration: none; color: inherit; border-bottom: 1px solid #f5f5f5; }
    .suggestion-item:hover, .suggestion-item.active { background: #f7f9ff; }

    /* Falling blossoms effect */
    .blossom {
      position: fixed;
      top: -50px;
      pointer-events: none;
      z-index: 9999;
      user-select: none;
      animation: fall linear infinite;
    }
    @keyframes fall {
      0% { 
        transform: translateY(0) translateX(0) rotate(0deg); 
        opacity: 0; 
      }
      10% { opacity: 1; }
      90% { opacity: 1; }
      100% { 
        transform: translateY(105vh) translateX(100px) rotate(360deg); 
        opacity: 0; 
      }
    }
  </style>
</head>
<body>

<header class="tet-header pb-3">
  <!-- Decorative elements -->
  <div class="tet-decoration" style="top: 10px; left: 5%;">🌸</div>
  <div class="tet-decoration" style="bottom: 10px; right: 5%;">🧧</div>

  <div class="container">
    <!-- Top Nav -->
    <div class="d-flex justify-content-between nav-top">
      <div class="d-flex gap-3">
        <a href="/weblaptop/admin/products.php"><i class="bi bi-shop"></i> Kênh Người Bán</a>
        <a href="#"><i class="bi bi-phone"></i> Tải ứng dụng</a>
        <a href="#">Kết nối <i class="bi bi-facebook"></i> <i class="bi bi-instagram"></i></a>
      </div>
      <div class="d-flex gap-3 align-items-center">
        <a href="/weblaptop/notifications.php"><i class="bi bi-bell"></i> Thông Báo</a>
        <a href="/weblaptop/contact.php"><i class="bi bi-question-circle"></i> Hỗ Trợ</a>
        <?php if (!empty($_SESSION["user_id"])): ?>
          <div class="dropdown">
            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"><?php echo htmlspecialchars($_SESSION["user_name"] ?? "Tài khoản"); ?></a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item text-dark" href="/weblaptop/account.php">Hồ sơ</a></li>
              <li><a class="dropdown-item text-dark" href="/weblaptop/orders.php">Đơn mua</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-dark" href="/weblaptop/auth/logout.php">Đăng xuất</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="/weblaptop/auth/register.php" class="fw-bold">Đăng Ký</a>
          <div style="width: 1px; height: 13px; background: rgba(255,255,255,.4);"></div>
          <a href="/weblaptop/auth/login.php" class="fw-bold">Đăng Nhập</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Main Header -->
    <div class="d-flex align-items-center mt-3">
      <a href="/weblaptop" class="d-flex flex-column text-decoration-none">
        <div class="fs-2 fw-bold d-flex align-items-center logo-text text-white">
          <i class="bi bi-cpu-fill me-2 text-warning"></i> GrowTech
        </div>
        <span class="slogan">Chuẩn công nghệ – vững niềm tin</span>
      </a>
      
      <div class="search-bar-container" id="header-search">
        <form action="/weblaptop/index.php" method="get" class="d-flex w-100">
          <input type="text" name="q" id="header-search-input" class="search-input" placeholder="Tìm kiếm sản phẩm công nghệ đón Tết...">
          <button type="submit" class="search-btn"><i class="bi bi-search"></i></button>
        </form>
        <div id="search-suggestions"></div>
      </div>

      <a href="/weblaptop/cart.php" class="cart-icon" id="header-cart-btn">
        <i class="bi bi-cart3"></i>
        <span class="cart-badge"><?php echo isset($_SESSION["cart"]) ? array_sum($_SESSION["cart"]) : 0; ?></span>
      </a>
    </div>
  </div>
</header>

<?php if (function_exists("display_flash")) display_flash(); ?>

<div class="container mt-4">
