<?php
require_once 'model/MonAn.php';
require_once 'model/KhachHang.php';
include_once "connect.php";
include_once "includes/headerlogin.php";

// ProductSearch class for handling product queries
class ProductSearch {
    private $conn;
    private $limit;
    private $page;
    private $offset;
    private $keyword;
    private $category;
    private $min_price;
    private $max_price;
    private $sort;
    private $type;

    public function __construct($conn, $params) {
        $this->conn = $conn;
        $this->limit = $params['limit'] ?? 12;
        $this->page = max($params['page'] ?? 1, 1);
        $this->offset = ($this->page - 1) * $this->limit;
        $this->keyword = trim($params['keyword'] ?? '');
        $this->category = trim($params['category'] ?? '');
        $this->min_price = is_numeric($params['min_price'] ?? '') ? (int)$params['min_price'] : '';
        $this->max_price = is_numeric($params['max_price'] ?? '') ? (int)$params['max_price'] : '';
        $this->sort = $params['sort'] ?? '';
        $this->type = trim($params['Type'] ?? '');
    }

    public function getProducts() {
        $sql = "SELECT * FROM sanpham WHERE TINH_TRANG = 1";
        $params = [];
        $types = "";

        if (!empty($this->keyword)) {
            $sql .= " AND TEN_SP LIKE ?";
            $params[] = "%{$this->keyword}%";
            $types .= "s";
        }
        if (!empty($this->category)) {
            $sql .= " AND MA_LOAISP = ?";
            $params[] = $this->category;
            $types .= "s";
        } elseif (!empty($this->type)) {
            $sql .= " AND MA_LOAISP = ?";
            $params[] = $this->type;
            $types .= "s";
        }
        if ($this->min_price !== '') {
            $sql .= " AND GIA_CA >= ?";
            $params[] = $this->min_price;
            $types .= "i";
        }
        if ($this->max_price !== '') {
            $sql .= " AND GIA_CA <= ?";
            $params[] = $this->max_price;
            $types .= "i";
        }
        if ($this->sort === 'asc') {
            $sql .= " ORDER BY GIA_CA ASC";
        } elseif ($this->sort === 'desc') {
            $sql .= " ORDER BY GIA_CA DESC";
        }
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $this->limit;
        $params[] = $this->offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . htmlspecialchars($this->conn->error));
        }
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getTotalProducts() {
        $sql = "SELECT COUNT(*) as total FROM sanpham WHERE TINH_TRANG = 1";
        $params = [];
        $types = "";

        if (!empty($this->keyword)) {
            $sql .= " AND TEN_SP LIKE ?";
            $params[] = "%{$this->keyword}%";
            $types .= "s";
        }
        if (!empty($this->category)) {
            $sql .= " AND MA_LOAISP = ?";
            $params[] = $this->category;
            $types .= "s";
        } elseif (!empty($this->type)) {
            $sql .= " AND MA_LOAISP = ?";
            $params[] = $this->type;
            $types .= "s";
        }
        if ($this->min_price !== '') {
            $sql .= " AND GIA_CA >= ?";
            $params[] = $this->min_price;
            $types .= "i";
        }
        if ($this->max_price !== '') {
            $sql .= " AND GIA_CA <= ?";
            $params[] = $this->max_price;
            $types .= "i";
        }

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . htmlspecialchars($this->conn->error));
        }
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    public function getTotalPages($total_products) {
        return ($total_products > 0) ? ceil($total_products / $this->limit) : 1;
    }

    public function isSearch() {
        return !empty($this->keyword) || !empty($this->category) || !empty($this->type) || $this->min_price !== '' || $this->max_price !== '';
    }

    public function getPage() {
        return $this->page;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function getParams() {
        return [
            'keyword' => $this->keyword,
            'category' => $this->category,
            'Type' => $this->type,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'sort' => $this->sort
        ];
    }
}

// Gather GET parameters
$params = [
    'limit' => 12,
    'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1,
    'keyword' => $_GET['keyword'] ?? '',
    'category' => $_GET['category'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'sort' => $_GET['sort'] ?? '',
    'Type' => $_GET['Type'] ?? ''
];

$productSearch = new ProductSearch($conn, $params);

try {
    $result = $productSearch->getProducts();
    $total_products = $productSearch->getTotalProducts();
    $total_pages = $productSearch->getTotalPages($total_products);
    $is_search = $productSearch->isSearch();
    $page = $productSearch->getPage();
    $limit = $productSearch->getLimit();
} catch (Exception $e) {
    die($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    crossorigin="anonymous" />

  <link rel="stylesheet" href="assets/font-awesome-pro-v6-6.2.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/base.css" />
  <link rel="stylesheet" href="assets/css/style.css" />

  <title>Đặc sản 3 miền</title>
  <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
</head>

<body>
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

  <div class="Products" id="product-list">
    <div class="container">
      <div class="row">
        <?php if ($total_products > 0): ?>
          <div class="col-xl-12">
            <div class="inner-title">
              <?= $is_search ? 'Kết quả tìm kiếm' : 'Khám phá thực đơn của chúng tôi'; ?>
            </div>
          </div>
          <?php
          while ($row = $result->fetch_assoc()):
            $monAn = new MonAn(
              $row['MA_SP'],
              $row['TEN_SP'],
              $row['HINH_ANH'],
              $row['GIA_CA'],
              $row['MO_TA'] ?? '',
              $row['MA_LOAISP'],
              $row['TINH_TRANG'] ?? 1
            );
            ?>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12">
              <div class="inner-item">
                <a href="chitietsp-login.php?id=<?= htmlspecialchars($monAn->getId()); ?>" class="inner-img">
                  <img src="<?= htmlspecialchars($monAn->getHinhAnh()); ?>"
                    alt="<?= htmlspecialchars($monAn->getTen()); ?>" />
                </a>
                <div class="inner-info">
                  <div class="inner-ten"><?= htmlspecialchars($monAn->getTen()); ?></div>
                  <div class="inner-gia"><?= number_format($monAn->getGiaCa(), 0, ',', '.'); ?>₫</div>
                  <a href="chitietsp-login.php?id=<?= htmlspecialchars($monAn->getId()); ?>" class="inner-muahang">
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
        foreach ($productSearch->getParams() as $key => $value) {
          if ($value !== '' && $value !== null) {
            $url_params[] = $key . '=' . urlencode($value);
          }
        }
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
      document.addEventListener('DOMContentLoaded', function () {
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

    // Nếu form nâng cao tồn tại, đồng bộ từ thanh tìm kiếm chính
    const advForm = document.getElementById('advanced-search-form');
    if (advForm) {
      advForm.addEventListener('submit', function () {
        const searchInput = document.getElementById('search-input') ? document.getElementById('search-input').value : '';
        const advKeyword = document.getElementById('advanced-keyword');
        if (advKeyword) advKeyword.value = searchInput;
      });
    }
  </script>
</body>

</html>