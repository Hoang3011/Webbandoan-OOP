<?php
session_start();

// Database connection
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=webbandoan8;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Parameters for filtering and pagination
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$sort_order = isset($_GET['sort']) ? (int)$_GET['sort'] : 2; // 1: ASC, 2: DESC
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 5;

// Build SQL query for customer statistics
$sql = "
    SELECT 
        k.MA_KH AS customerId, 
        k.TEN_KH AS customerName, 
        COUNT(DISTINCT d.MA_DH) AS orderCount, 
        COALESCE(SUM(d.TONG_TIEN), 0) AS total
    FROM khachhang k
    LEFT JOIN donhang d ON k.MA_KH = d.MA_KH
    WHERE d.TINH_TRANG = 'Đã giao thành công'
";

// Add search filter
if ($search) {
    $sql .= " AND k.TEN_KH LIKE :search";
}

// Add date filters
if ($start_date) {
    $sql .= " AND DATE(d.NGAY_TAO) >= :start_date";
}
if ($end_date) {
    $sql .= " AND DATE(d.NGAY_TAO) <= :end_date";
}

$sql .= " GROUP BY k.MA_KH, k.TEN_KH";

// Add sorting
$sql .= $sort_order == 1 ? " ORDER BY total ASC" : " ORDER BY total DESC";

// Pagination
$offset = ($page - 1) * $items_per_page;
$sql_paginated = $sql . " LIMIT :offset, :items_per_page";

// Prepare and execute query for total customers and revenue
$stmt = $pdo->prepare($sql);
if ($search) {
    $stmt->bindValue(':search', "%$search%");
}
if ($start_date) {
    $stmt->bindValue(':start_date', $start_date);
}
if ($end_date) {
    $stmt->bindValue(':end_date', $end_date);
}
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total customers and revenue
$total_customers = count($customers);
$total_revenue = array_sum(array_column($customers, 'total'));

// Prepare and execute paginated query
$stmt_paginated = $pdo->prepare($sql_paginated);
if ($search) {
    $stmt_paginated->bindValue(':search', "%$search%");
}
if ($start_date) {
    $stmt_paginated->bindValue(':start_date', $start_date);
}
if ($end_date) {
    $stmt_paginated->bindValue(':end_date', $end_date);
}
$stmt_paginated->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt_paginated->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
$stmt_paginated->execute();
$paginated_customers = $stmt_paginated->fetchAll(PDO::FETCH_ASSOC);

// Calculate total pages
$total_pages = ceil($total_customers / $items_per_page);
$page = max(1, min($page, $total_pages));
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