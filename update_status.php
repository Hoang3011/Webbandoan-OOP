<?php
include_once "./connect.php"; // Kết nối database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $MA_DH = isset($_POST['MA_DH']) ? intval($_POST['MA_DH']) : 0;
    $TINH_TRANG = isset($_POST['TINH_TRANG']) ? $_POST['TINH_TRANG'] : '';
    // Kiểm tra dữ liệu hợp lệ
    $valid_statuses = ['Chưa xác nhận', 'Đã xác nhận', 'Đã giao thành công', 'Đã hủy đơn'];
    if ($MA_DH > 0 && in_array($TINH_TRANG, $valid_statuses)) {
        // Cập nhật trạng thái vào bảng donhang
        $sql = "UPDATE donhang SET TINH_TRANG = ? WHERE MA_DH = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $TINH_TRANG, $MA_DH);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $stmt->close();
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
$conn->close();
?>