<?php
include "./connect.php";

if (!$conn) {
    echo "error: Không thể kết nối đến cơ sở dữ liệu";
    exit;
}

if (isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    // Kiểm tra trạng thái TINH_TRANG của sản phẩm
    $sql = "SELECT TINH_TRANG FROM sanpham WHERE MA_SP = '$id'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['TINH_TRANG'] == 1) {
            echo "visible"; // đang hiển thị
        } elseif ($row['TINH_TRANG'] == 0) {
            echo "hidden"; // đã ẩn
        } else {
            echo "deleted"; // -1: đã xóa vĩnh viễn
        }
    } else {
        echo "error: Không tìm thấy sản phẩm";
    }
} else {
    echo "error: Không nhận được ID";
}

mysqli_close($conn);
?>
