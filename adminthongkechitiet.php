<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: adminlogin.php");
    exit();
}

include_once "connect.php";
require_once "model/ThongKe.php";

$thongKe = new ThongKe($conn);

// Lấy tham số
$customerId = isset($_GET['customerId']) ? (int)$_GET['customerId'] : 0;
$search     = $_GET['search'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date   = $_GET['end_date'] ?? '';
$sort_order = (int)($_GET['sort'] ?? 2); // 1: ASC, 2: DESC
$page       = max(1, (int)($_GET['page'] ?? 1));
$items_per_page = 5;

if ($customerId <= 0) {
    die("Khách hàng không hợp lệ.");
}

// Dùng class để lấy thông tin khách + đơn hàng
$customer = $thongKe->layThongTinKhachHang($customerId);
if (!$customer) {
    die("Khách hàng không tồn tại.");
}

$orders = $thongKe->layDonHangCuaKhachHang($customerId, [
    'search'     => $search,
    'start_date' => $start_date,
    'end_date'   => $end_date,
    'sort'       => $sort_order == 1 ? 'ASC' : 'DESC'
]);

$total_orders = count($orders);
$total_pages  = ceil($total_orders / $items_per_page);
$offset       = ($page - 1) * $items_per_page;
$paginated_orders = array_slice($orders, $offset, $items_per_page);
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
    <title>Chi Tiết Đơn Hàng Khách Hàng</title>
    <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
</head>
<body>
<div class="wrapper d-flex align-items-stretch">
      <nav id="sidebar">
        <div class="custom-menu">
          <button type="button" id="sidebarCollapse" class="btn btn-primary"></button>
        </div>
        <div class="img bg-wrap text-center py-4">
          <div class="user-logo">
            <div class="inner-logo">
              <img src="assets/img/logo.png" alt="logo" />
            </div>
          </div>
        </div>
        <ul class="list-unstyled components mb-5">
          <li><a href="admin.php"><i class="fa-light fa-house"></i> Trang tổng quan</a></li>
          <li><a href="adminproduct.php"><i class="fa-light fa-pot-food"></i> Sản phẩm</a></li>
          <li><a href="admincustomer.php"><i class="fa-light fa-users"></i> Khách hàng</a></li>
          <li><a href="adminorder.php"><i class="fa-light fa-basket-shopping"></i> Đơn hàng</a></li>
          <li class="active"><a href="adminstatistical.php"><i class="fa-light fa-chart-simple"></i> Thống kê</a></li>
        </ul>

        <ul class="sidebar-list">
          <li class="sidebar-list-item user-logout">
            <a href="#" class="sidebar-link">
              <div class="sidebar-icon"><i class="fa-light fa-circle-user"></i></div>
              <div class="hidden-sidebar" id="name-acc">Khoa</div>
            </a>
          </li>
          <script>
            function getCookie(name) {
              const nameEQ = name + "=";
              const ca = document.cookie.split(';');
              for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
              }
              return null;
            }
            window.onload = function() {
              const username = getCookie("username");
              const nameElement = document.getElementById("name-acc");
              if (username && nameElement) nameElement.textContent = username;
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
        sidebarItems.forEach(item => {
          const link = item.querySelector('a').getAttribute('href');
          if (currentPath.includes(link)) {
            sidebarItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
          }
          item.addEventListener('click', function() {
            sidebarItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
          });
        });
      </script>

        <div class="adminthongkechitiet">
            <div class="admin-control">
                <div class="admin-control-center">
                    <form action="" class="form-search" method="GET">
                        <span onclick="this.parentNode.submit();" class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                        <input id="form-search-tk" name="search" type="text" class="form-search-input" placeholder="Tìm kiếm hóa đơn..." value="<?php echo htmlspecialchars($search); ?>" />
                        <input type="hidden" name="customerId" value="<?php echo $customerId; ?>" />
                        <input type="hidden" name="sort" value="<?php echo $sort_order; ?>" />
                        <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" />
                        <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" />
                    </form>
                </div>
                <div class="admin-control-right">
                    <form action="" class="fillter-date" method="GET">
                        <div>
                            <label for="time-start">Từ</label>
                            <input type="date" class="form-control-date" id="time-start-tk" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" onchange="this.form.submit();" />
                        </div>
                        <div>
                            <label for="time-end">Đến</label>
                            <input type="date" class="form-control-date" id="time-end-tk" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" onchange="this.form.submit();" />
                        </div>
                        <input type="hidden" name="customerId" value="<?php echo $customerId; ?>" />
                        <input type="hidden" name="sort" value="<?php echo $sort_order; ?>" />
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>" />
                    </form>
                    <a href="?customerId=<?php echo $customerId; ?>&sort=1&search=<?php echo urlencode($search); ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="reset-order">
                        <i class="fa-regular fa-arrow-up-short-wide"></i>
                    </a>
                    <a href="?customerId=<?php echo $customerId; ?>&sort=2&search=<?php echo urlencode($search); ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="reset-order">
                        <i class="fa-regular fa-arrow-down-wide-short"></i>
                    </a>
                    <a href="adminthongkechitiet.php?customerId=<?php echo $customerId; ?>" class="reset-order">
                        <i class="fa-light fa-arrow-rotate-right"></i>
                    </a>
                </div>
            </div>

            <div class="table">
                <table width="100%">
                    <thead>
                        <tr>
                            <td>Hóa đơn</td>
                            <td>Ngày đặt</td>
                            <td>Tổng tiền</td>
                            <td>Thao tác</td>
                        </tr>
                    </thead>
                    <tbody id="showOrder">
                        <?php if (empty($paginated_orders)): ?>
                            <tr><td colspan="4">Không có đơn hàng nào.</td></tr>
                        <?php else: ?>
                            <?php foreach ($paginated_orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['orderId']); ?></td>
                                    <td><?php echo htmlspecialchars($order['orderDate']); ?></td>
                                    <td><?php echo number_format($order['total'], 0, ',', '.'); ?> ₫</td>
                                    <td class="control">
                                        <a href="adminthongkehoadon.php?madh=<?php echo $order['orderId']; ?>" class="btn-detail">
                                            <i class="fa-regular fa-eye"></i> Chi tiết
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="Pagination">
                <div class="container">
                    <ul id="pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li>
                                <a href="?customerId=<?php echo $customerId; ?>&page=<?php echo $i; ?>&sort=<?php echo $sort_order; ?>&search=<?php echo urlencode($search); ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
                                   class="inner-trang <?php echo $i == $page ? 'trang-chinh' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
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