<?php
include_once 'connect.php';
require_once 'model/DonHang.php';   // Class DonHang của bạn

// Lấy dữ liệu POST
$status    = $_POST['status']    ?? '';
$startDate = $_POST['startDate'] ?? '';
$endDate   = $_POST['endDate']   ?? '';
$province  = $_POST['province']  ?? '';
$district  = $_POST['district']  ?? '';

// Xây dựng câu SQL
$sql = "SELECT dh.*, kh.TEN_KH 
        FROM donhang dh 
        JOIN khachhang kh ON dh.MA_KH = kh.MA_KH 
        WHERE 1=1";

if ($status !== '') {
    $status = mysqli_real_escape_string($conn, $status);
    $sql .= " AND dh.TINH_TRANG = '$status'";
}
if ($startDate !== '') {
    $startDate = mysqli_real_escape_string($conn, $startDate);
    $sql .= " AND DATE(dh.NGAY_TAO) >= '$startDate'";
}
if ($endDate !== '') {
    $endDate = mysqli_real_escape_string($conn, $endDate);
    $sql .= " AND DATE(dh.NGAY_TAO) <= '$endDate'";
}
if ($district !== '' && $province !== '') {
    $search = mysqli_real_escape_string($conn, "$district%$province%");
    $sql .= " AND dh.DIA_CHI LIKE '%$search%'";
} elseif ($province !== '') {
    $search = mysqli_real_escape_string($conn, $province);
    $sql .= " AND dh.DIA_CHI LIKE '%$search%'";
}

$sql .= " ORDER BY dh.NGAY_TAO DESC";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<tr><td colspan='6'>Không tìm thấy đơn hàng nào</td></tr>";
    exit();
}

// Tạo mảng lưu tên khách hàng (vì class DonHang chưa có thuộc tính này)
$tenKH = [];
$orders = [];

while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = new DonHang(
        $row['MA_DH'],
        $row['MA_KH'],
        $row['NGAY_TAO'],
        $row['TONG_TIEN'],
        $row['GHI_CHU'] ?? '',
        $row['DIA_CHI'] ?? '',
        $row['MA_GH'] ?? null,
        $row['PHUONG_THUC'] ?? 'Tiền mặt',
        $row['TINH_TRANG']
    );
    $tenKH[$row['MA_DH']] = $row['TEN_KH'];   // Lưu tên KH theo MA_DH
}

// Xuất HTML giống hệt bản cũ
foreach ($orders as $order) {
    $madh     = "DH" . $order->getId();
    $ngaydat  = date('d/m/Y', strtotime($order->getNgayTao()));
    $tongtien = number_format((int)$order->getTongTien(), 0, ',', '.') . " ₫";

    $status_class = match ($order->getTinhTrang()) {
        'Chưa xác nhận'      => 'status-no-complete',
        'Đã xác nhận'        => 'status-middle-complete',
        'Đã giao thành công' => 'status-complete',
        'Đã hủy đơn'         => 'status-destroy-complete',
        default              => '',
    };
    ?>
    <tr>
        <td><?php echo $madh; ?></td>
        <td><?php echo htmlspecialchars($tenKH[$order->getId()] ?? 'Khách lẻ'); ?></td>
        <td><?php echo $ngaydat; ?></td>
        <td><?php echo $tongtien; ?></td>
        <td><span class="<?php echo $status_class; ?>"><?php echo $order->getTinhTrang(); ?></span></td>
        <td class="control">
            <a href="adminchitiet.php?madh=<?php echo $order->getId(); ?>" class="btn-detail">
                <i class="fa-regular fa-eye"></i> Chi tiết
            </a>
        </td>
    </tr>
    <?php
}

mysqli_close($conn);
?>