<?php
session_start();
ob_start();

// Hiển thị lỗi khi debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hàm làm sạch giá nhập
function cleanPrice($price) {
    if (empty($price)) return '';
    $cleaned = preg_replace('/[^0-9]/', '', $price);
    return $cleaned !== '' ? (int)$cleaned : '';
}

// Xử lý dữ liệu GET
$min_price = '';
$max_price = '';
$keyword = '';
$category = '';
$sort = '';
$page = 1;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $min_price = isset($_GET['min_price']) ? cleanPrice($_GET['min_price']) : '';
    $max_price = isset($_GET['max_price']) ? cleanPrice($_GET['max_price']) : '';
    $keyword = isset($_GET['keyword']) ? trim(htmlspecialchars($_GET['keyword'])) : '';
    $category = isset($_GET['category']) ? trim(htmlspecialchars($_GET['category'])) : '';
    $sort = isset($_GET['sort']) && in_array($_GET['sort'], ['asc', 'desc']) ? $_GET['sort'] : '';
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
}
?>

<header>
    <div class="header-middle">
        <div class="container">
            <div class="header-middle-left">
                <div class="header-logo">
                    <a href="login.php">
                        <img src="./assets/img/logo.png" alt="" class="header-logo-img" />
                    </a>
                </div>
            </div>

            <div class="header-middle-center">
                <form id="search-form" class="form-search" method="GET" action="login.php">
                    <span class="search-btn" onclick="submitSearchForm(event)">
                        <i class="fa-light fa-magnifying-glass"></i>
                    </span>
                    <input type="text" class="form-search-input" id="search-input" name="keyword"
                           placeholder="Tìm kiếm món ăn..." 
                           value="<?= htmlspecialchars($keyword) ?>">
                </form>
            </div>

            <button class="filter-btn" id="toggle-filter-btn" type="button">
                <i class="fa-light fa-filter-list"></i><span>Lọc</span>
            </button>

            <div class="header-middle-right">
                <ul class="header-middle-right-list">
                    <li class="header-middle-right-item dropdown open">
                        <i class="fa-light fa-user"></i>
                        <div class="auth-container">
                            <span class="text-dndk">Tài khoản</span>
                            <span class="text-tk">
                                <?php
                                if (!isset($_SESSION['mySession'])) {
                                    echo '<a href="index.php">Đăng nhập</a>';
                                } else {
                                    echo htmlspecialchars($_SESSION['mySession'], ENT_QUOTES, 'UTF-8');
                                }
                                ?>
                                <i class="fa-sharp fa-solid fa-caret-down"></i>
                            </span>
                        </div>
                        <ul class="header-middle-right-menu">
                            <li><a class="dropdown-item" href="account.php">
                                <i class="fa-regular fa-circle-user"></i> Tài khoản của tôi</a></li>
                            <li><a class="dropdown-item" 
                                   href="<?= isset($_SESSION['makh']) ? 'productss.php?makh=' . urlencode($_SESSION['makh']) : 'login.php'; ?>">
                                <i class="fa-solid fa-cart-shopping"></i> Đơn hàng đã mua</a></li>
                            <li><a href="logout.php" class="dropdown-item bordera">
                                <i class="fa-solid fa-right-from-bracket"></i> Thoát tài khoản</a></li>
                        </ul>
                    </li>

                    <li class="header-middle-right-item">
                        <a href="<?= isset($_SESSION['makh']) ? 'giohang.php?makh=' . urlencode($_SESSION['makh']) : 'login.php'; ?>" class="cart-link">
                            <div class="cart-icon-menu">
                                <i class="fa-light fa-basket-shopping"></i>
                                <span class="count-product-cart">
    <?php
    include "connect.php";
    $total_quantity = 0;

    if (isset($_SESSION['magh'])) {
        $magh = $_SESSION['magh'];
        $sql = "SELECT SUM(SO_LUONG) AS total_quantity FROM chitietgiohang WHERE MA_GH = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $magh);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_quantity = $row['total_quantity'] ?? 0;
        $stmt->close();
    }

    echo htmlspecialchars($total_quantity);
    ?>
</span>

                            </div>
                        </a>
                        <span>Giỏ hàng</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<nav class="header-bottom">
    <div class="container">
        <ul class="menu-list">
            <li class="menu-list-item"><a href="login.php" class="menu-link">Trang chủ</a></li>
            <li class="menu-list-item"><a href="login.php?Type=L001#scroll" class="menu-link">Món chay</a></li>
            <li class="menu-list-item"><a href="login.php?Type=L002#scroll" class="menu-link">Món mặn</a></li>
            <li class="menu-list-item"><a href="login.php?Type=L003#scroll" class="menu-link">Món lẩu</a></li>
            <li class="menu-list-item"><a href="login.php?Type=L004#scroll" class="menu-link">Món ăn vặt</a></li>
            <li class="menu-list-item"><a href="login.php?Type=L005#scroll" class="menu-link">Món tráng miệng</a></li>
            <li class="menu-list-item"><a href="login.php?Type=L006#scroll" class="menu-link">Nước uống</a></li>
            <li class="menu-list-item"><a href="login.php?Type=L007#scroll" class="menu-link">Hải sản</a></li>
        </ul>
    </div>
</nav>

<!-- Advanced Search -->
<div class="advanced-search" id="advanced-search">
    <form id="advanced-search-form" method="GET" action="login.php" class="container">
        <div class="advanced-search-category">
            <span>Phân loại</span>
            <select id="advanced-search-category-select" name="category">
                <option value="">Tất cả</option>
                <option value="L001" <?= ($category == 'L001') ? 'selected' : ''; ?>>Món chay</option>
                <option value="L002" <?= ($category == 'L002') ? 'selected' : ''; ?>>Món mặn</option>
                <option value="L003" <?= ($category == 'L003') ? 'selected' : ''; ?>>Món lẩu</option>
                <option value="L004" <?= ($category == 'L004') ? 'selected' : ''; ?>>Món ăn vặt</option>
                <option value="L005" <?= ($category == 'L005') ? 'selected' : ''; ?>>Món tráng miệng</option>
                <option value="L006" <?= ($category == 'L006') ? 'selected' : ''; ?>>Nước uống</option>
                <option value="L007" <?= ($category == 'L007') ? 'selected' : ''; ?>>Hải sản</option>
            </select>
        </div>

        <div class="advanced-search-price">
            <span>Giá từ</span>
            <input type="text" placeholder="tối thiểu" id="min-price" name="min_price" 
                   value="<?= $min_price !== '' ? number_format($min_price, 0, ',', '.') : ''; ?>">
            <span>đến</span>
            <input type="text" placeholder="tối đa" id="max-price" name="max_price" 
                   value="<?= $max_price !== '' ? number_format($max_price, 0, ',', '.') : ''; ?>">
            <button type="submit" id="advanced-search-price-btn">
                <i class="fa-light fa-magnifying-glass-dollar"></i>
            </button>
        </div>

        <div class="advanced-search-control">
            <button type="submit" name="sort" value="asc" title="Giá tăng dần">
                <i class="fa-regular fa-arrow-up-short-wide"></i>
            </button>
            <button type="submit" name="sort" value="desc" title="Giá giảm dần">
                <i class="fa-regular fa-arrow-down-wide-short"></i>
            </button>
            <button><a href="login.php"><i class="fa-light fa-arrow-rotate-right"></i></a></button>
            <button type="button" onclick="closeSearchAdvanced()"><i class="fa-light fa-xmark"></i></button>
        </div>

        <input type="hidden" id="advanced-keyword" name="keyword" value="<?= htmlspecialchars($keyword); ?>">
        <input type="hidden" name="page" value="<?= htmlspecialchars($page); ?>">
    </form>
</div>

<script>
function submitSearchForm(event) {
    event.preventDefault();
    const searchInput = document.getElementById('search-input').value;
    document.getElementById('advanced-keyword').value = searchInput;
    document.getElementById('search-form').submit();
}

// Định dạng giá tiền khi nhập
function formatPrice(input) {
    let value = input.value.replace(/\D/g, '');
    input.value = value ? parseInt(value).toLocaleString('vi-VN') : '';
}
function getRawPrice(value) { return value.replace(/\D/g, ''); }

document.getElementById('advanced-search-form').addEventListener('submit', function() {
    const minPrice = document.getElementById('min-price');
    const maxPrice = document.getElementById('max-price');
    const searchInput = document.getElementById('search-input').value;
    minPrice.value = getRawPrice(minPrice.value);
    maxPrice.value = getRawPrice(maxPrice.value);
    document.getElementById('advanced-keyword').value = searchInput;
});

document.getElementById('min-price').addEventListener('input', function(){ formatPrice(this); });
document.getElementById('max-price').addEventListener('input', function(){ formatPrice(this); });

function closeSearchAdvanced() {
    document.getElementById('advanced-search').style.display = 'none';
}
</script>
