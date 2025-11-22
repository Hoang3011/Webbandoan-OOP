<?php
include_once "connect.php";
require_once "model/DonHang.php";

// Chỉ xử lý POST + AJAX
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

$MA_DH      = isset($_POST['MA_DH']) ? intval($_POST['MA_DH']) : 0;
$TINH_TRANG = $_POST['TINH_TRANG'] ?? '';

// Danh sách trạng thái hợp lệ theo đúng class DonHang của bạn
$allowedStatuses = [
    'Chưa xác nhận',
    'Đã xác nhận',
    'Đang giao hàng',
    'Đã giao thành công',
    'Đã hủy đơn'
];

if ($MA_DH <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Mã đơn hàng không hợp lệ']);
    exit();
}

if (!in_array($TINH_TRANG, $allowedStatuses)) {
    echo json_encode(['status' => 'error', 'message' => 'Trạng thái không hợp lệ']);
    exit();
}

// Kiểm tra đơn hàng có tồn tại không (tùy chọn, nhưng nên có)
$stmt_check = $conn->prepare("SELECT MA_DH FROM donhang WHERE MA_DH = ?");
$stmt_check->bind_param("i", $MA_DH);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows === 0) {
    $stmt_check->close();
    echo json_encode(['status' => 'error', 'message' => 'Đơn hàng không tồn tại']);
    exit();
}
$stmt_check->close();

// Cập nhật trạng thái bằng prepared statement (an toàn 100%)
$stmt = $conn->prepare("UPDATE donhang SET TINH_TRANG = ? WHERE MA_DH = ?");
$stmt->bind_param("si", $TINH_TRANG, $MA_DH);

if ($stmt->execute()) {
    // Thành công → trả JSON để AJAX xử lý mượt
    echo json_encode([
        'status' => 'success',
        'message' => 'Cập nhật trạng thái thành công!',
        'new_status' => $TINH_TRANG
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Cập nhật thất bại. Vui lòng thử lại.'
    ]);
}

$stmt->close();
$conn->close();
?>