<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: adminlogin.php");
    exit();
}

include_once "connect.php";
require_once "model/ThongKe.php";

// Khởi tạo đối tượng ThongKe
$thongKe = new ThongKe($conn);

// Lấy tham số lọc
$search      = $_GET['search'] ?? '';
$start_date  = $_GET['start_date'] ?? '';
$end_date    = $_GET['end_date'] ?? '';
$sort_order  = (int)($_GET['sort'] ?? 2); // 1: tăng dần, 2: giảm dần
$page        = max(1, (int)($_GET['page'] ?? 1));
$items_per_page = 5;

// === GỌI HÀM TỪ CLASS THONGKE ĐỂ LẤY DỮ LIỆU ===
$customers = $thongKe->thongKeKhachHangDoanhThu([
    'search'      => $search,
    'start_date'  => $start_date,
    'end_date'    => $end_date,
    'sort'        => $sort_order == 1 ? 'ASC' : 'DESC'
]);

$total_customers = count($customers);
$total_revenue   = array_sum(array_column($customers, 'total'));

// Phân trang
$offset = ($page - 1) * $items_per_page;
$paginated_customers = array_slice($customers, $offset, $items_per_page);
$total_pages = ceil($total_customers / $items_per_page);
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
    <title>Thống Kê Khách Hàng</title>
    <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
</head>
<body>
    <?php include_once "includes/headeradmin.php"; ?>

    <div class="admin-statistical">
        <div class="admin-control">
            <div class="admin-control-left"></div>
            <div class="admin-control-center">
                <form action="" class="form-search" method="GET">
                    <span onclick="this.parentNode.submit();" class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                    <input id="form-search-tk" name="search" type="text" class="form-search-input" placeholder="Tìm kiếm tên khách hàng..." value="<?php echo htmlspecialchars($search); ?>" />
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
                    <input type="hidden" name="sort" value="<?php echo $sort_order; ?>" />
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>" />
                </form>
                <a href="?sort=1&search=<?php echo urlencode($search); ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="reset-order">
                    <i class="fa-regular fa-arrow-up-short-wide"></i>
                </a>
                <a href="?sort=2&search=<?php echo urlencode($search); ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="reset-order">
                    <i class="fa-regular fa-arrow-down-wide-short"></i>
                </a>
                <a href="adminstatistical.php" class="reset-order">
                    <i class="fa-light fa-arrow-rotate-right"></i>
                </a>
            </div>
        </div>

        <div class="order-statistical">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <div class="order-statistical-item">
                        <div class="order-statistical-item-content">
                            <p class="order-statistical-item-content-desc">Tổng số khách hàng</p>
                            <h4 class="order-statistical-item-content-h"><?php echo $total_customers; ?></h4>
                        </div>
                        <div class="order-statistical-item-icon">
                            <i class="fa-light fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <div class="order-statistical-item">
                        <div class="order-statistical-item-content">
                            <p class="order-statistical-item-content-desc">Tổng doanh thu</p>
                            <h4 class="order-statistical-item-content-h"><?php echo number_format($total_revenue, 0, ',', '.'); ?> ₫</h4>
                        </div>
                        <div class="order-statistical-item-icon">
                            <i class="fa-light fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table">
            <table width="100%">
                <thead>
                    <tr>
                        <td>STT</td>
                        <td>Tên khách hàng</td>
                        <td>Số đơn hàng</td>
                        <td>Tổng tiền mua</td>
                        <td>Chi tiết</td>
                    </tr>
                </thead>
                <tbody id="showTk">
                    <?php foreach ($paginated_customers as $index => $customer): ?>
                        <tr>
                            <td><?php echo $offset + $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($customer['customerName']); ?></td>
                            <td><?php echo $customer['orderCount']; ?></td>
                            <td><?php echo number_format($customer['total'], 0, ',', '.'); ?> ₫</td>
                            <td>
                                <a href="adminthongkechitiet.php?customerId=<?php echo $customer['customerId']; ?>" class="btn-detail">
                                    <i class="fa-regular fa-eye"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="Pagination">
            <div class="container">
                <ul id="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li>
                            <a href="?page=<?php echo $i; ?>&sort=<?php echo $sort_order; ?>&search=<?php echo urlencode($search); ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
                               class="inner-trang <?php echo $i == $page ? 'trang-chinh' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
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