<?php
include_once 'connect.php';

$status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : '';
$orderId = isset($_POST['orderId']) ? (int)$_POST['orderId'] : '';
$startDate = isset($_POST['startDate']) ? mysqli_real_escape_string($conn, $_POST['startDate']) : '';
$endDate = isset($_POST['endDate']) ? mysqli_real_escape_string($conn, $_POST['endDate']) : '';
$province = isset($_POST['province']) ? mysqli_real_escape_string($conn, $_POST['province']) : '';
$district = isset($_POST['district']) ? mysqli_real_escape_string($conn, $_POST['district']) : '';

$sql = "SELECT dh.MA_DH, dh.MA_KH, dh.NGAY_TAO, dh.TONG_TIEN, dh.TINH_TRANG, kh.TEN_KH 
        FROM donhang dh 
        JOIN khachhang kh ON dh.MA_KH = kh.MA_KH 
        WHERE 1=1";

if ($status) {
    $sql .= " AND dh.TINH_TRANG = '$status'";
}
if ($orderId) {
    $sql .= " AND dh.MA_DH = $orderId";
}
if ($startDate) {
    $sql .= " AND DATE(dh.NGAY_TAO) >= '$startDate'";
}
if ($endDate) {
    $sql .= " AND DATE(dh.NGAY_TAO) <= '$endDate'";
}
if ($district && $province) {
    $sql .= " AND dh.DIA_CHI LIKE '%$district%$province%'";
} elseif ($province) {
    $sql .= " AND dh.DIA_CHI LIKE '%$province%'";
}

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $madh = "DH" . $row['MA_DH'];
        $ngaydat = date('d/m/Y', strtotime($row['NGAY_TAO']));
        $tongtien = number_format((int)$row['TONG_TIEN'], 0, ',', '.') . " ₫";
        $status_class = '';
        switch ($row['TINH_TRANG']) {
            case 'Chưa xác nhận':
                $status_class = 'status-no-complete';
                break;
            case 'Đã xác nhận':
                $status_class = 'status-middle-complete';
                break;
            case 'Đã giao thành công':
                $status_class = 'status-complete';
                break;
            case 'Đã hủy đơn':
                $status_class = 'status-destroy-complete';
                break;
        }
?>
        <tr>
            <td><?php echo $madh; ?></td>
            <td><?php echo htmlspecialchars($row['TEN_KH']); ?></td>
            <td><?php echo $ngaydat; ?></td>
            <td><?php echo $tongtien; ?></td>
            <td><span class="<?php echo $status_class; ?>"><?php echo $row['TINH_TRANG']; ?></span></td>
            <td class="control">
                <a href="adminchitiet.php?madh=<?php echo $row['MA_DH']; ?>" class="btn-detail">
                    <i class="fa-regular fa-eye"></i> Chi tiết
                </a>
            </td>
        </tr>
<?php
    }
} else {
    echo "<tr><td colspan='6'>Không tìm thấy đơn hàng nào</td></tr>";
}

mysqli_close($conn);
?>