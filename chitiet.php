<?php
include "connect.php";

class OrderProduct {
    public $name;
    public $image;
    public $quantity;
    public $price;
    public $total;

    public function __construct($row) {
        $this->name = $row['Name'];
        $this->image = $row['Image'];
        $this->quantity = $row['SO_LUONG'];
        $this->price = $row['GIA_LUC_MUA'];
        $this->total = $row['THANH_TIEN'];
    }
}

class Order {
    public $data;
    public $products = [];
    public $totalPrice = 0;
    public $totalQuantity = 0;

    public function __construct($conn, $makh, $madh) {
        $this->fetchOrder($conn, $makh, $madh);
        if ($this->data) {
            $this->fetchProducts($conn, $madh);
        }
    }

    private function fetchOrder($conn, $makh, $madh) {
        $sql = "SELECT dh.*, kh.TEN_KH, kh.SO_DIEN_THOAI 
                FROM donhang dh 
                JOIN khachhang kh ON dh.MA_KH = kh.MA_KH 
                WHERE dh.MA_KH = ? AND dh.MA_DH = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Lỗi SQL: " . $conn->error);
        $stmt->bind_param("ii", $makh, $madh);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->data = $result->fetch_assoc();
        $stmt->close();
    }

    private function fetchProducts($conn, $madh) {
        $sql_sp = "SELECT sp.TEN_SP AS Name, 
                          sp.HINH_ANH AS Image, 
                          ch.SO_LUONG, 
                          ch.GIA_LUC_MUA, 
                          (ch.SO_LUONG * ch.GIA_LUC_MUA) AS THANH_TIEN
                   FROM chitietdonhang ch
                   JOIN sanpham sp ON ch.MA_SP = sp.MA_SP
                   WHERE ch.MA_DH = ?";
        $stmt_sp = $conn->prepare($sql_sp);
        $stmt_sp->bind_param("i", $madh);
        $stmt_sp->execute();
        $result_sp = $stmt_sp->get_result();

        while ($row = $result_sp->fetch_assoc()) {
            $product = new OrderProduct($row);
            $this->products[] = $product;
            $this->totalPrice += $product->total;
            $this->totalQuantity += $product->quantity;
        }
        $stmt_sp->close();
    }
}

// Khởi tạo biến mặc định
$order = null;

if (isset($_SESSION['makh']) && isset($_GET['madh']) && is_numeric($_GET['madh'])) {
    $makh = $_SESSION['makh'];
    $madh = (int) $_GET['madh'];
    $order = new Order($conn, $makh, $madh);
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
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />

  <link rel="stylesheet" href="assets/font-awesome-pro-v6-6.2.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/base.css" />
  <link rel="stylesheet" href="assets/css/style.css" />

  <title>Đặc sản 3 miền</title>
  <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
</head>

<body>
  <?php include_once "includes/headerlogin.php"; ?>

  <!-- ChiTiet -->
  <div class="chitiet">
    <div class="container">
      <?php
      // XỬ LÝ LỖI
      if (!isset($_SESSION['makh'])) {
        echo '<div class="alert alert-danger text-center">Vui lòng đăng nhập!</div>';
      } elseif (!isset($_GET['madh']) || !is_numeric($_GET['madh'])) {
        echo '<div class="alert alert-danger text-center">Đơn hàng không hợp lệ!</div>';
      } elseif (!$order || !$order->data) {
        echo '<div class="alert alert-danger text-center">Không tìm thấy đơn hàng!</div>';
      } else {
        // HIỂN THỊ NỘI DUNG
        $donhang = $order->data;
        ?>
        <div class="inner-chitiet">
          <div class="inner-tt">Chi tiết đơn hàng DH<?= sprintf("%04d", $donhang['MA_DH']) ?></div>
          <div class="inner-vc">Ngày đặt: <?= date("d-m-Y H:i:s", strtotime($donhang['NGAY_TAO'])) ?></div>
        </div>

        <div class="inner-trangthai">
          <div class="inner-ct">
            Trạng thái thanh toán: <i><?= htmlspecialchars($donhang['TINH_TRANG']) ?></i>
          </div>
        </div>

        <div class="row">
          <div class="col-xl-6">
            <div class="inner-diachi">
              <div class="inner-ten">ĐỊA CHỈ GIAO HÀNG</div>
              <div class="inner-gth">
                <div class="inner-ten"><?= htmlspecialchars($donhang['TEN_KH']) ?></div>
                <div class="inner-dc">Địa chỉ: <?= htmlspecialchars($donhang['DIA_CHI']) ?></div>
                <div class="inner-sdt">Số điện thoại: <?= htmlspecialchars($donhang['SO_DIEN_THOAI'] ?? 'Không có') ?>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
            <div class="inner-diachi">
              <div class="inner-ten">THANH TOÁN</div>
              <div class="inner-gth">
                <div class="inner-tt"><?= htmlspecialchars($donhang['PHUONG_THUC']); ?></div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
            <div class="inner-diachi">
              <div class="inner-ten">GHI CHÚ</div>
              <div class="inner-gth">
                <div class="inner-ghichu"><?= htmlspecialchars($donhang['GHI_CHU'] ?? 'Không có') ?></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sản phẩm -->
        <div class="inner-menu">
          <?php foreach ($order->products as $sp): ?>
            <div class="inner-item">
              <div class="inner-info">
                <div class="inner-img">
                  <img src="<?= htmlspecialchars($sp->image) ?>" width="80px" height="80px"
                    alt="<?= htmlspecialchars($sp->name) ?>" />
                </div>
                <div class="inner-chu">
                  <div class="inner-ten"><?= htmlspecialchars($sp->name) ?></div>
                  <div class="inner-sl">x<?= $sp->quantity ?></div>
                </div>
              </div>
              <div class="inner-gia"><?= number_format($sp->total, 0, ',', '.') ?>₫</div>
            </div>
          <?php endforeach; ?>

          <div class="inner-tonggia">
            <div class="inner-tien">
              <div class="inner-th">Tiền hàng <span><?= $order->totalQuantity ?> món</span></div>
              <div class="inner-st"><?= number_format($order->totalPrice, 0, ',', '.') ?>₫</div>
            </div>
            <div class="inner-vanchuyen">
              <span class="inner-vc1">Vận chuyển</span>
              <span class="inner-vc2">0₫</span>
            </div>
            <div class="inner-total">
              <span class="inner-tong1">Tổng tiền:</span>
              <span class="inner-tong2"><?= number_format($order->totalPrice, 0, ',', '.') ?>₫</span>
            </div>
          </div>
        </div>
        <?php
      } // Đóng else hiển thị nội dung
      ?>
    </div>
  </div>
  <!-- End ChiTiet -->

  <?php include_once "includes/footer.php"; ?>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
    integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
    crossorigin="anonymous"></script>

  <script src="assets/js/main.js"></script>
</body>

</html>