<?php
include '../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $madh = $_POST['madh'] ?? null;
    $newStatus = $_POST['newStatus'] ?? null;

    if (!$madh || !$newStatus) {
        echo "Dữ liệu không hợp lệ!";
        exit;
    }

    // Danh sách trạng thái hợp lệ và hướng chuyển đổi hợp lệ
    $allowedTransitions = [
        "Chưa xác nhận" => ["Đã xác nhận"],
        "Đã xác nhận" => ["Đã giao thành công", "Đã hủy đơn"]
    ];

    // Lấy trạng thái hiện tại của đơn hàng
    $query = "SELECT trangthai FROM donhang WHERE madh = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $madh);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    if (!$order) {
        echo "Đơn hàng không tồn tại!";
        exit;
    }

    $currentStatus = $order['trangthai'];

    // Kiểm tra nếu trạng thái mới có hợp lệ không
    if (!isset($allowedTransitions[$currentStatus]) || !in_array($newStatus, $allowedTransitions[$currentStatus])) {
        echo "Chuyển đổi trạng thái không hợp lệ!";
        exit;
    }

    // Cập nhật trạng thái đơn hàng
    $updateQuery = "UPDATE donhang SET trangthai = ? WHERE madh = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $newStatus, $madh);

    if ($updateStmt->execute()) {
        echo "success";
    } else {
        echo "Lỗi khi cập nhật đơn hàng!";
    }

    $updateStmt->close();
}

$conn->close();
?>
