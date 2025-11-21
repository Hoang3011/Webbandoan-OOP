<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />

    <!-- Bootstrap CSS -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
      crossorigin="anonymous"
    />

    <link
      rel="stylesheet"
      href="assets/font-awesome-pro-v6-6.2.0/css/all.min.css"
    />
    <link rel="stylesheet" href="assets/css/base.css" />
    <link rel="stylesheet" href="assets/css/style.css" />

    <title>Đặc sản 3 miền</title>
    <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
  </head>

  <body>
  <?php
  include_once "includes/headerlogin.php";
  ?>
    <!-- Banner -->

    <div class="Banner">
      <div class="container">
        <div class="inner-img">
          <img src="assets/img/banner.jpg" alt="banner" />
        </div>
      </div>
    </div>

    <!-- End Banner -->

    <!-- Service -->

    <div class="home-service" id="home-service">
      <div class="container">
        <div class="row">
          <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="inner-item">
              <div class="inner-icon">
                <i class="fa-solid fa-truck-fast"></i>
              </div>
              <div class="inner-info">
                <div class="inner-chu1">GIAO HÀNG NHANH</div>
                <div class="inner-chu2">Cho tất cả đơn hàng</div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="inner-item">
              <div class="inner-icon">
                <i class="fa-solid fa-shield-heart"></i>
              </div>
              <div class="inner-info">
                <div class="inner-chu1">SẢN PHẨM AN TOÀN</div>
                <div class="inner-chu2">Cam kết chất lượng</div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="inner-item">
              <div class="inner-icon">
                <i class="fa-solid fa-headset"></i>
              </div>
              <div class="inner-info">
                <div class="inner-chu1">HỖ TRỢ 24/7</div>
                <div class="inner-chu2">Tất cả ngày trong tuần</div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="inner-item">
              <div class="inner-icon">
                <i class="fa-solid fa-coins"></i>
              </div>
              <div class="inner-info">
                <div class="inner-chu1">HOÀN LẠI TIỀN</div>
                <div class="inner-chu2">Nếu không hài lòng</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<!-- End Service -->
    <?php
// Số sản phẩm trên mỗi trang
$limit = 12;

// Trang hiện tại
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Nhận tham số tìm kiếm
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (int)$_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (int)$_GET['max_price'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$Type = isset($_GET['Type']) ? trim($_GET['Type']) : '';

// Xây dựng truy vấn
$sql = "SELECT * FROM sanpham WHERE TINH_TRANG = 1";
$params = [];
$types = "";

// Tìm kiếm theo tên
if (!empty($keyword)) {
    $sql .= " AND TEN_SP LIKE ?";
    $params[] = "%$keyword%";
    $types .= "s";
}

// Tìm kiếm theo loại
if (!empty($category)) {
    $sql .= " AND MA_LOAISP = ?";
    $params[] = $category;
    $types .= "s";
} elseif (!empty($Type)) {
    $sql .= " AND MA_LOAISP = ?";
    $params[] = $Type;
    $types .= "s";
}

// Lọc theo giá
if ($min_price !== '') {
    $sql .= " AND GIA_CA >= ?";
    $params[] = $min_price;
    $types .= "i";
}
if ($max_price !== '') {
    $sql .= " AND GIA_CA <= ?";
    $params[] = $max_price;
    $types .= "i";
}

// Sắp xếp
if ($sort === 'asc') {
    $sql .= " ORDER BY GIA_CA ASC";
} elseif ($sort === 'desc') {
    $sql .= " ORDER BY GIA_CA DESC";
}

// Phân trang
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

// Thực thi
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Lấy tổng sản phẩm
$total_sql = "SELECT COUNT(*) as total FROM sanpham WHERE TINH_TRANG = 1";
$total_params = [];
$total_types = "";

if (!empty($keyword)) {
    $total_sql .= " AND TEN_SP LIKE ?";
    $total_params[] = "%$keyword%";
    $total_types .= "s";
}
if (!empty($category)) {
    $total_sql .= " AND MA_LOAISP = ?";
    $total_params[] = $category;
    $total_types .= "s";
} elseif (!empty($Type)) {
    $total_sql .= " AND MA_LOAISP = ?";
    $total_params[] = $Type;
    $total_types .= "s";
}
if ($min_price !== '') {
    $total_sql .= " AND GIA_CA >= ?";
    $total_params[] = $min_price;
    $total_types .= "i";
}
if ($max_price !== '') {
    $total_sql .= " AND GIA_CA <= ?";
    $total_params[] = $max_price;
    $total_types .= "i";
}

$total_stmt = $conn->prepare($total_sql);
if (!empty($total_params)) {
    $total_stmt->bind_param($total_types, ...$total_params);
}
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_products = $total_row['total'];
$total_pages = ($total_products > 0) ? ceil($total_products / $limit) : 1;

$is_search = !empty($keyword) || !empty($category) || !empty($Type) || $min_price !== '' || $max_price !== '';
?>

<div class="Products" id="product-list">
  <div class="container">
    <div class="row">
      <?php if ($total_products > 0): ?>
        <div class="col-xl-12">
          <div class="inner-title">
            <?= $is_search ? 'Kết quả tìm kiếm' : 'Khám phá thực đơn của chúng tôi'; ?>
          </div>
        </div>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12">
            <div class="inner-item">
              <a href="chitietsp-login.php?id=<?= $row['MA_SP']; ?>" class="inner-img">
                <img src="<?= htmlspecialchars($row['HINH_ANH']); ?>" />
              </a>
              <div class="inner-info">
                <div class="inner-ten"><?= htmlspecialchars($row['TEN_SP']); ?></div>
                <div class="inner-gia"><?= number_format($row['GIA_CA'], 0, ',', '.'); ?>₫</div>
                <a href="chitietsp-login.php?id=<?= $row['MA_SP']; ?>" class="inner-muahang">
                  <i class="fa-solid fa-cart-plus"></i> ĐẶT MÓN
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-xl-12">
          <div class="no-result">
            <div class="no-result-h">Không tìm thấy sản phẩm</div>
            <div class="no-result-p">Rất tiếc, không có kết quả nào phù hợp với tìm kiếm của bạn.</div>
            <div class="no-result-i"><i class="fa-light fa-face-sad-cry"></i></div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Phân trang -->
<div id="pagination" class="Pagination">
  <div class="container">
    <ul>
      <?php
      $base_url = 'login.php?';
      $url_params = [];
      if (!empty($keyword)) $url_params[] = 'keyword=' . urlencode($keyword);
      if (!empty($category)) $url_params[] = 'category=' . urlencode($category);
      if (!empty($Type)) $url_params[] = 'Type=' . urlencode($Type);
      if ($min_price !== '') $url_params[] = 'min_price=' . $min_price;
      if ($max_price !== '') $url_params[] = 'max_price=' . $max_price;
      if (!empty($sort)) $url_params[] = 'sort=' . $sort;

      $base_url .= implode('&', $url_params);

      for ($i = 1; $i <= $total_pages; $i++) {
          $active_class = ($i == $page) ? 'trang-chinh' : '';
          $page_url = $base_url . (empty($url_params) ? '' : '&') . 'page=' . $i;
          echo '<li><a href="' . htmlspecialchars($page_url) . '" class="inner-trang ' . $active_class . '">' . $i . '</a></li>';
      }
      ?>
    </ul>
  </div>
</div>

    <?php if ($is_search && $total_products > 0): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('home-service').scrollIntoView({ behavior: 'smooth' });
            });
        </script>
    <?php endif; ?>

<?php include_once "includes/footer.php"; ?>

    <!-- JavaScript -->
    <script>
        function submitSearchForm() {
            document.getElementById('search-form').submit();
        }

        // Synchronize keyword from main search bar to advanced search form
        document.getElementById('advanced-search-form').addEventListener('submit', function() {
            const searchInput = document.getElementById('search-input').value;
            document.getElementById('advanced-keyword').value = searchInput;
        });
    </script>
</body>
</html>