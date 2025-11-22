<?php
include "./connect.php";
require_once "model/MonAn.php";

if (!$conn) {
    echo "error: Không thể kết nối đến cơ sở dữ liệu";
    exit;
}

if (isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    // Kiểm tra trạng thái TINH_TRANG của sản phẩm
    $sql = "SELECT * FROM sanpham WHERE MA_SP = '$id'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $monAn = new MonAn(
            $row['MA_SP'],
            $row['TEN_SP'],
            $row['HINH_ANH'],
            $row['GIA_CA'],
            $row['MO_TA'],
            $row['MA_LOAISP'],
            $row['TINH_TRANG']
        );
        if ($monAn->getTinhTrang() == 1) {
            echo "visible"; // đang hiển thị
        } elseif ($monAn->getTinhTrang() == 0) {
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