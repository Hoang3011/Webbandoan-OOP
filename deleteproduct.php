<?php
include "./connect.php";
require_once "model/MonAn.php";

if (!$conn) {
    echo "error: Không thể kết nối đến cơ sở dữ liệu";
    exit;
}
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    // Kiểm tra trạng thái hiện tại của sản phẩm
    $sql = "SELECT * FROM sanpham WHERE MA_SP = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $monAn = new MonAn(
            $row['MA_SP'],
            $row['TEN_SP'],
            $row['HINH_ANH'],
            $row['GIA_CA'],
            $row['MO_TA'],
            $row['MA_LOAISP'],
            $row['TINH_TRANG']
        );
        $status = $monAn->getTinhTrang();
        if ($action === 'hide' && $status == 1) {
            // Ẩn sản phẩm (TINH_TRANG = 0)
            $update_sql = "UPDATE sanpham SET TINH_TRANG = 0 WHERE MA_SP = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("s", $id);
            if ($update_stmt->execute()) {
                echo "success";
            } else {
                echo "error: " . $conn->error;
            }
            $update_stmt->close();
        } elseif ($action === 'delete' && $status == 0) {
            // Xóa vĩnh viễn sản phẩm (TINH_TRANG = -1)
            $update_sql = "UPDATE sanpham SET TINH_TRANG = -1 WHERE MA_SP = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("s", $id);
            if ($update_stmt->execute()) {
                echo "success";
            } else {
                echo "error: " . $conn->error;
            }
            $update_stmt->close();
        } else {
            echo "error: Hành động không hợp lệ hoặc trạng thái không khớp.";
        }
    } else {
        echo "error: Không tìm thấy sản phẩm";
    }
    $stmt->close();
} else {
    echo "error: Không nhận được ID";
}

$conn->close();
?>