<?php
include "../connect.php";

if (!$conn) {
    echo "error: Không thể kết nối đến cơ sở dữ liệu";
    exit;
}

if (isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    // Xóa sản phẩm từ cơ sở dữ liệu
    $sql = "DELETE FROM sanpham WHERE ID = '$id'";
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($conn);
    }
} else {
    echo "error: Không nhận được ID";
}

mysqli_close($conn);
?>