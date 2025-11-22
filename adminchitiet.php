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
    <title>Admin - Chi tiết đơn hàng</title>
    <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
    <style>
        @media print {
            body * { visibility: hidden; }
            #order-content, #order-content * { visibility: visible; }
            #order-content { position: absolute; left: 0; top: 0; width: 100%; }
            .inner-capnhat, .inner-select { display: none; }
        }
    </style>
</head>
<body>
<?php
include_once "./connect.php";
include_once "./model/DonHang.php";

// Lấy MA_DH từ URL
$MA_DH = isset($_GET['madh']) ? intval($_GET['madh']) : 0;

// Khởi tạo biến
$donHang = null;
$order_info = null;
$order_details = [];
$total_items = 0;
$tongtien_dh = 0;
$phi_van_chuyen = 0;

// LẤY THÔNG TIN ĐƠN HÀNG
$sql = "SELECT dh.*, kh.TEN_KH, kh.SO_DIEN_THOAI
        FROM donhang dh
        JOIN khachhang kh ON dh.MA_KH = kh.MA_KH
        WHERE dh.MA_DH = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Lỗi prepare SQL: " . $conn->error);
}
$stmt->bind_param("i", $MA_DH);
$stmt->execute();
$result = $stmt->get_result();
$order_info = $result->fetch_assoc();
$stmt->close();

if ($order_info) {
    // Tạo đối tượng DonHang từ dữ liệu database
    $donHang = new DonHang(
        $order_info['MA_DH'],
        $order_info['MA_KH'],
        $order_info['NGAY_TAO'],
        $order_info['TONG_TIEN'],
        $order_info['GHI_CHU'],
        $order_info['DIA_CHI'],
        $order_info['MA_GH'] ?? null,
        $order_info['PHUONG_THUC'],
        $order_info['TINH_TRANG']
    );
    
    $tongtien_dh = $donHang->getTongTien();
    
    // LẤY CHI TIẾT SẢN PHẨM TỪ BẢNG chitietdonhang
    $sql_details = "SELECT ctdh.MA_SP, ctdh.SO_LUONG, ctdh.GIA_LUC_MUA, sp.TEN_SP, sp.HINH_ANH 
                    FROM chitietdonhang ctdh
                    JOIN sanpham sp ON ctdh.MA_SP = sp.MA_SP
                    WHERE ctdh.MA_DH = ?";
    $stmt_details = $conn->prepare($sql_details);
    $stmt_details->bind_param("i", $MA_DH);
    $stmt_details->execute();
    $result_details = $stmt_details->get_result();
    while ($row_detail = $result_details->fetch_assoc()) {
        $order_details[] = [
            'ten_sanpham' => $row_detail['TEN_SP'],
            'anh_sanpham' => $row_detail['HINH_ANH'],
            'soluong' => $row_detail['SO_LUONG'],
            'giabanle' => $row_detail['GIA_LUC_MUA']
        ];
        $total_items += $row_detail['SO_LUONG'];
    }
    $stmt_details->close();
    
    // Fallback sang GHI_CHU nếu không có chi tiết
    if (empty($order_details)) {
        $ghichu = trim($donHang->getGhiChu());
        $items = [];
        if (!empty($ghichu)) {
            $parts = explode(',', $ghichu);
            foreach ($parts as $part) {
                $data = explode(':', trim($part));
                if (count($data) === 3 && is_numeric($data[0]) && is_numeric($data[1]) && is_numeric($data[2])) {
                    $items[] = [
                        'MA_SP' => (int) $data[0],
                        'SO_LUONG' => (int) $data[1],
                        'GIA_CA' => (int) $data[2]
                    ];
                }
            }
        }
        
        if (!empty($items)) {
            $ma_sp_list = array_column($items, 'MA_SP');
            $ma_sp_str = implode(',', $ma_sp_list);
            $sql_sp = "SELECT MA_SP, TEN_SP, HINH_ANH FROM sanpham WHERE MA_SP IN ($ma_sp_str)";
            $result_sp = $conn->query($sql_sp);
            $sp_info = [];
            while ($row_sp = $result_sp->fetch_assoc()) {
                $sp_info[$row_sp['MA_SP']] = $row_sp;
            }
            foreach ($items as $item) {
                $info = $sp_info[$item['MA_SP']] ?? ['TEN_SP' => 'Sản phẩm không tồn tại', 'HINH_ANH' => 'assets/img/no-image.jpg'];
                $order_details[] = [
                    'ten_sanpham' => $info['TEN_SP'],
                    'anh_sanpham' => $info['HINH_ANH'],
                    'soluong' => $item['SO_LUONG'],
                    'giabanle' => $item['GIA_CA']
                ];
                $total_items += $item['SO_LUONG'];
            }
        }
    }
}
?>

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
            <li class="active"><a href="adminorder.php"><i class="fa-light fa-basket-shopping"></i> Đơn hàng</a></li>
            <li><a href="adminstatistical.php"><i class="fa-light fa-chart-simple"></i> Thống kê</a></li>
        </ul>
        <ul class="sidebar-list">
            <li class="sidebar-list-item user-logout">
                <a href="#" class="sidebar-link">
                    <div class="sidebar-icon"><i class="fa-light fa-circle-user"></i></div>
                    <div class="hidden-sidebar" id="name-acc">Khoa</div>
                </a>
            </li>
            <li class="sidebar-list-item user-logout">
                <a href="adminlogin.php" class="sidebar-link" id="logout-acc">
                    <div class="sidebar-icon"><i class="fa-light fa-arrow-right-from-bracket"></i></div>
                    <div class="hidden-sidebar">Đăng xuất</div>
                </a>
            </li>
        </ul>
    </nav>

    <div class="admin-chitiet">
        <div class="order-chitiet">
            <div class="row">
                <div class="col-12">
                    <div class="inner-head">
                        <div class="inner-title">Chi tiết đơn hàng #<?php echo $donHang ? $donHang->getId() : ''; ?></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="order-chitietstart">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6">
                                <?php if (!empty($order_details)): ?>
                                    <?php foreach ($order_details as $item): ?>
                                        <div class="inner-item">
                                            <div class="inner-info">
                                                <div class="inner-img">
                                                    <img src="<?php echo htmlspecialchars($item['anh_sanpham'] ?: 'assets/img/products/default.jpg'); ?>" alt="Product Image" />
                                                </div>
                                                <div class="inner-mota">
                                                    <div class="inner-ten"><?php echo htmlspecialchars($item['ten_sanpham'] ?: 'Sản phẩm không xác định'); ?></div>
                                                    <div class="inner-sl">SL: <?php echo intval($item['soluong']); ?></div>
                                                </div>
                                            </div>
                                            <div class="inner-gia"><?= number_format($item['giabanle'], 0, ',', '.') ?>₫</div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>Không tìm thấy chi tiết đơn hàng.</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6">
                                <?php if ($donHang): ?>
                                    <div class="inner-pt">
                                        <div class="inner-cachthuc"><i class="fa-regular fa-calendar-days"></i> Ngày đặt hàng</div>
                                        <div class="inner-ketqua"><?php echo date('d/m/Y', strtotime($donHang->getNgayTao())); ?></div>
                                    </div>
                                    <div class="inner-pt">
                                        <div class="inner-cachthuc"><i class="fa-regular fa-credit-card"></i> PT thanh toán</div>
                                        <div class="inner-ketqua"><?php echo htmlspecialchars($donHang->getPhuongThuc() ?: 'Không xác định'); ?></div>
                                    </div>
                                    <div class="inner-pt">
                                        <div class="inner-cachthuc"><i class="fa-solid fa-person"></i> Người nhận</div>
                                        <div class="inner-ketqua"><?php echo htmlspecialchars($order_info['TEN_KH'] ?: 'Không xác định'); ?></div>
                                    </div>
                                    <div class="inner-pt">
                                        <div class="inner-cachthuc"><i class="fa-solid fa-phone"></i> Số điện thoại</div>
                                        <div class="inner-ketqua"><?php echo htmlspecialchars($order_info['SO_DIEN_THOAI'] ?: 'Không xác định'); ?></div>
                                    </div>
                                    <div class="inner-diachi">
                                        <div class="inner-cachthuc"><i class="fa-solid fa-location-dot"></i> Địa chỉ nhận</div>
                                        <p class="inner-desc"><?php echo htmlspecialchars($donHang->getDiaChi() ?: 'Không xác định'); ?></p>
                                    </div>
                                    <div class="inner-diachi">
                                        <div class="inner-cachthuc"><i class="fa-light fa-note-sticky"></i> Ghi chú</div>
                                        <p class="inner-desc"><?php echo htmlspecialchars($donHang->getGhiChu() ?: ''); ?></p>
                                    </div>
                                <?php else: ?>
                                    <p>Không tìm thấy thông tin đơn hàng.</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-12">
                                <div class="order-chitietend">
                                    <?php if ($donHang): ?>
                                        <div class="inner-tien">
                                            <div class="inner-th">Tiền hàng <span><?php echo $total_items; ?> món</span></div>
                                            <div class="inner-st"><?= number_format($tongtien_dh, 0, ',', '.') ?>₫</div>
                                        </div>
                                        <div class="inner-vanchuyen">
                                            <span class="inner-vc1">Vận chuyển</span>
                                            <span class="inner-vc2"><?php echo number_format($phi_van_chuyen, 0, ',', '.'); ?>₫</span>
                                        </div>
                                        <div class="inner-tonggia">
                                            <div class="inner-giaca">
                                                <div class="inner-chu">Thành tiền</div>
                                                <div class="inner-so"><?= number_format($tongtien_dh + $phi_van_chuyen, 0, ',', '.') ?>₫</div>
                                            </div>
                                            <div class="inner-select">
                                                <label for="select">Trạng thái</label>
                                                <select name="trangthai" id="select">
                                                    <?php
                                                    $currentStatus = $donHang->getTinhTrang();
                                                    if ($currentStatus == 'Chưa xác nhận') {
                                                        echo '<option value="Chưa xác nhận" selected>Chưa xác nhận</option>';
                                                        echo '<option value="Đã xác nhận">Đã xác nhận</option>';
                                                    } elseif ($currentStatus == 'Đã xác nhận') {
                                                        echo '<option value="Đã xác nhận" selected>Đã xác nhận</option>';
                                                        echo '<option value="Đã giao thành công">Đã giao thành công</option>';
                                                        echo '<option value="Đã hủy đơn">Đã hủy đơn</option>';
                                                    } elseif ($currentStatus == 'Đã giao thành công') {
                                                        echo '<option value="Đã giao thành công" selected disabled>Đã giao thành công</option>';
                                                    } elseif ($currentStatus == 'Đã hủy đơn') {
                                                        echo '<option value="Đã hủy đơn" selected disabled>Đã hủy đơn</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="inner-capnhat">
                                            <button onclick="updateOrder(<?php echo $donHang->getId(); ?>)" class="inner-nut">
                                                <i class="fa-regular fa-floppy-disk"></i> Cập nhật trạng thái
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <p>Không thể hiển thị thông tin tổng kết đơn hàng.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="admin/js/bootstrap.min.js"></script>
<script src="admin/js/main.js"></script>
<script src="admin/js/popper.js"></script>
<script src="assets/js/admin.js"></script>
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

    function updateOrder(MA_DH) {
        const TINH_TRANG = document.getElementById('select').value;
        $.ajax({
            url: 'update_status.php',
            type: 'POST',
            data: { MA_DH: MA_DH, TINH_TRANG: TINH_TRANG },
            success: function(response) {
                const res = JSON.parse(response);
                if (res.status === 'success') {
                    window.location.href = 'adminorder.php';
                } else {
                    alert(res.message || 'Cập nhật trạng thái thất bại.');
                }
            },
            error: function() {
                alert('Đã xảy ra lỗi. Vui lòng thử lại.');
            }
        });
    }
</script>
</body>
</html>
<?php $conn->close(); ?>