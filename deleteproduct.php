<?php
include "./connect.php";

if (!$conn) {
    echo "error: Không thể kết nối đến cơ sở dữ liệu";
    exit;
}

if (isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Kiểm tra trạng thái hiện tại của sản phẩm
    $check_sql = "SELECT TINH_TRANG FROM sanpham WHERE MA_SP = '$id'";
    $check_result = mysqli_query($conn, $check_sql);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $row = mysqli_fetch_assoc($check_result);
        $status = $row['TINH_TRANG'];

        if ($action === 'hide' && $status == 1) {
            // Ẩn sản phẩm (TINH_TRANG = 0)
            $update_sql = "UPDATE sanpham SET TINH_TRANG = 0 WHERE MA_SP = '$id'";
            if (mysqli_query($conn, $update_sql)) {
                echo "success";
            } else {
                echo "error: " . mysqli_error($conn);
            }

        } elseif ($action === 'delete' && $status == 0) {
            // Xóa vĩnh viễn sản phẩm (TINH_TRANG = -1)
            $update_sql = "UPDATE sanpham SET TINH_TRANG = -1 WHERE MA_SP = '$id'";
            if (mysqli_query($conn, $update_sql)) {
                echo "success";
            } else {
                echo "error: " . mysqli_error($conn);
            }

        } else {
            echo "error: Hành động không hợp lệ hoặc trạng thái không khớp.";
        }
    } else {
        echo "error: Không tìm thấy sản phẩm";
    }
} else {
    echo "error: Không nhận được ID";
}

mysqli_close($conn);
?>
