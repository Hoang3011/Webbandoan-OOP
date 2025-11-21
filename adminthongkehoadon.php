<?php
session_start();

// Kết nối CSDL
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=webbandoan8;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}

// Lấy mã đơn hàng từ URL
$MA_DH = isset($_GET['madh']) ? (int)$_GET['madh'] : 0;

if ($MA_DH <= 0) {
    die("Mã đơn hàng không hợp lệ.");
}

// Lấy thông tin đơn hàng
$sql_order = "
    SELECT 
        d.MA_DH AS orderId,
        d.MA_KH AS customerId,
        DATE_FORMAT(d.NGAY_TAO, '%d/%m/%Y') AS orderDate,
        d.TONG_TIEN,
        d.PHUONG_THUC AS paymentMethod,
        d.GHI_CHU AS note,
        d.DIA_CHI AS address,
        k.TEN_KH AS customerName,
        k.SO_DIEN_THOAI AS phone,
        d.TINH_TRANG AS shippingStatus
    FROM donhang d
    JOIN khachhang k ON d.MA_KH = k.MA_KH
    WHERE d.MA_DH = :MA_DH
";
$stmt_order = $pdo->prepare($sql_order);
$stmt_order->bindValue(':MA_DH', $MA_DH, PDO::PARAM_INT);
$stmt_order->execute();
$order = $stmt_order->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Không tìm thấy hóa đơn này.");
}

// Lấy chi tiết sản phẩm từ chitietdonhang (ưu tiên)
$sql_items = "
    SELECT 
        s.TEN_SP AS product,
        s.HINH_ANH AS image,
        ct.SO_LUONG AS quantity,
        ct.GIA_LUC_MUA AS price
    FROM chitietdonhang ct
    JOIN sanpham s ON ct.MA_SP = s.MA_SP
    WHERE ct.MA_DH = :MA_DH
";
$stmt_items = $pdo->prepare($sql_items);
$stmt_items->bindValue(':MA_DH', $MA_DH, PDO::PARAM_INT);
$stmt_items->execute();
$items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

// Nếu không có chitietdonhang, fallback parse GHI_CHU
if (empty($items) && !empty($order['note'])) {
    $ghichu = trim($order['note']);
    $ghichu = str_replace('|| ', '', $ghichu);
    $parts = explode(',', $ghichu);
    $ma_sp_list = [];
    $temp_items = [];
    foreach ($parts as $part) {
        $data = explode(':', trim($part));
        if (count($data) >= 3 && is_numeric($data[0])) {
            $ma_sp = (int)$data[0];
            $ma_sp_list[] = $ma_sp;
            $temp_items[$ma_sp] = ['quantity' => (int)$data[1], 'price' => (int)$data[2]];
        }
    }

    if (!empty($ma_sp_list)) {
        $placeholders = implode(',', array_fill(0, count($ma_sp_list), '?'));
        $sql_sp = "SELECT MA_SP, TEN_SP AS product, HINH_ANH AS image FROM sanpham WHERE MA_SP IN ($placeholders)";
        $stmt_sp = $pdo->prepare($sql_sp);
        $stmt_sp->execute($ma_sp_list);
        while ($sp = $stmt_sp->fetch(PDO::FETCH_ASSOC)) {
            $info = $temp_items[$sp['MA_SP']];
            $items[] = [
                'product' => $sp['product'],
                'image' => $sp['image'],
                'quantity' => $info['quantity'],
                'price' => $info['price']
            ];
        }
    }
}

// Tính tổng tiền
$itemCount = array_sum(array_column($items, 'quantity'));
$subtotal = array_sum(array_map(fn($i) => $i['quantity'] * $i['price'], $items));
$shippingCost = 0;
$total = $subtotal + $shippingCost;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous" />
    <link rel="stylesheet" href="assets/font-awesome-pro-v6-6.2.0/css/all.min.css" />
    <link rel="stylesheet" href="admin/css/style.css" />
    <link rel="stylesheet" href="assets/css/base.css" />
    <link rel="stylesheet" href="assets/css/admin.css" />
    <title>Chi Tiết Hóa Đơn</title>
    <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
</head>
<body>
    <div class="wrapper d-flex align-items-stretch">
      <nav id="sidebar">
        <div class="custom-menu">
          <button
            type="button"
            id="sidebarCollapse"
            class="btn btn-primary"
          ></button>
        </div>
        <div class="img bg-wrap text-center py-4">
          <div class="user-logo">
            <div class="inner-logo">
              <img src="assets/img/logo.png" alt="logo" />
            </div>
          </div>
        </div>
        <ul class="list-unstyled components mb-5">
          <li>
            <a href="admin.php"
              ><i class="fa-light fa-house"></i> Trang tổng quan</a
            >
          </li>
          <li>
            <a href="adminproduct.php"
              ><i class="fa-light fa-pot-food"></i> Sản phẩm</a
            >
          </li>
          <li>
            <a href="admincustomer.php"
              ><i class="fa-light fa-users"></i> Khách hàng</a
            >
          </li>
          <li>
            <a href="adminorder.php"
              ><i class="fa-light fa-basket-shopping"></i> Đơn hàng</a
            >
          </li>
          <li class="active">
            <a href="adminstatistical.php"
              ><i class="fa-light fa-chart-simple"></i> Thống kê</a
            >
          </li>
        </ul>

        <ul class="sidebar-list">
         <!-- Assuming this is part of a larger admin.php file -->
          <li class="sidebar-list-item user-logout">
            <a href="#" class="sidebar-link">
              <div class="sidebar-icon"><i class="fa-light fa-circle-user"></i></div>
              <div class="hidden-sidebar" id="name-acc">Khoa</div>
            </a>
          </li>

          <script>
            // Function to get cookie by name
            function getCookie(name) {
              const nameEQ = name + "=";
              const ca = document.cookie.split(';');
              for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                  c = c.substring(1, c.length);
                }
                if (c.indexOf(nameEQ) == 0) {
                  return c.substring(nameEQ.length, c.length);
                }
              }
              return null;
            }

            // Update username display from cookie
            window.onload = function() {
              const username = getCookie("username");
              const nameElement = document.getElementById("name-acc");
              if (username && nameElement) {
                nameElement.textContent = username;
              }
            };
          </script>
          <li class="sidebar-list-item user-logout">
            <a href="adminlogin.php" class="sidebar-link" id="logout-acc">
              <div class="sidebar-icon"><i class="fa-light fa-arrow-right-from-bracket"></i></div>
              <div class="hidden-sidebar">Đăng xuất</div>
            </a>
          </li>
        </ul>
      </nav>

      <script>
  const sidebarItems = document.querySelectorAll('#sidebar .components li');
  const currentPath = window.location.pathname;

  // Kiểm tra URL hiện tại để đặt "active" khi tải trang
  sidebarItems.forEach(item => {
    const link = item.querySelector('a').getAttribute('href');
    if (currentPath.includes(link)) {
      sidebarItems.forEach(i => i.classList.remove('active'));
      item.classList.add('active');
    }

    // Xử lý sự kiện click
    item.addEventListener('click', function() {
      sidebarItems.forEach(i => i.classList.remove('active'));
      this.classList.add('active');
    });
  });
</script>

        <div class="admin-hoadon">
            <div class="hoadon">
                <div class="inner-head">
                    <div class="inner-title">Chi tiết hóa đơn</div>
                </div>
                <div class="container" id="order-content">
                    <?php if ($order): ?>
                        <div class="inner-chitiet">
                            <div class="inner-tt">Chi tiết đơn hàng <?php echo htmlspecialchars($order['orderId']); ?></div>
                            <div class="inner-vc">Ngày đặt hàng: <?php echo htmlspecialchars($order['orderDate']); ?></div>
                        </div>
                        <div class="inner-trangthai">
                            <div class="inner-ct">Trạng thái thanh toán: <i>Đã thanh toán</i></div>
                            <div class="inner-ngay">Trạng thái vận chuyển: <i><?php echo htmlspecialchars($order['shippingStatus']); ?></i></div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="inner-diachi">
                                    <div class="inner-ten">ĐỊA CHỈ GIAO HÀNG</div>
                                    <div class="inner-gth">
                                        <div class="inner-ten"><?php echo htmlspecialchars(strtoupper($order['customerName'])); ?></div>
                                        <div class="inner-dc">Địa chỉ: <?php echo htmlspecialchars($order['address'] ?: 'Không có địa chỉ'); ?></div>
                                        <div class="inner-sdt">Số điện thoại: <?php echo htmlspecialchars($order['phone']); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
                                <div class="inner-diachi">
                                    <div class="inner-ten">THANH TOÁN</div>
                                    <div class="inner-gth">
                                        <div class="inner-tt"><?php echo htmlspecialchars($order['paymentMethod']); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
                                <div class="inner-diachi">
                                    <div class="inner-ten">GHI CHÚ</div>
                                    <div class="inner-gth">
                                        <div class="inner-ghichu"><?php echo htmlspecialchars($order['note'] ?: 'Không có ghi chú'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="inner-menu">
                            <?php foreach ($items as $item): ?>
                                <div class="inner-item">
                                    <div class="inner-info">
                                        <div class="inner-img">
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" width="80px" height="80px" />
                                        </div>
                                        <div class="inner-chu">
                                            <div class="inner-ten"><?php echo htmlspecialchars($item['product']); ?></div>
                                            <div class="inner-sl">x<?php echo $item['quantity']; ?></div>
                                        </div>
                                    </div>
                                    <div class="inner-gia"><?php echo number_format($item['quantity'] * $item['price'], 0, ',', '.'); ?> ₫</div>
                                </div>
                            <?php endforeach; ?>
                            <div class="inner-tonggia">
                                <div class="inner-tien">
                                    <div class="inner-th">Tiền hàng <span><?php echo $itemCount; ?> món</span></div>
                                    <div class="inner-st"><?php echo number_format($subtotal, 0, ',', '.'); ?> ₫</div>
                                </div>
                                <div class="inner-vanchuyen">
                                    <span class="inner-vc1">Vận chuyển</span>
                                    <span class="inner-vc2"><?php echo number_format($shippingCost, 0, ',', '.'); ?>₫</span>
                                </div>
                                <div class="inner-total">
                                    <span class="inner-tong1">Tổng tiền:</span>
                                    <span class="inner-tong2"><?php echo number_format($total, 0, ',', '.'); ?> ₫</span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>Không tìm thấy hóa đơn.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="admin/js/jquery.min.js"></script>
    <script src="admin/js/popper.js"></script>
    <script src="admin/js/bootstrap.min.js"></script>
    <script src="admin/js/main.js"></script>
    <script src="assets/js/admin.js"></script>
</body>
</html>