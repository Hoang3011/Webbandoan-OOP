<?php
include "connect.php";
require_once "model/MonAn.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Name'], $_POST['Price'], $_POST['Describtion'], $_POST['Type'])) {
    $name = mysqli_real_escape_string($conn, $_POST['Name']);

    // Xử lý giá: bỏ dấu chấm, ép kiểu số
    $price_input = $_POST['Price'];
    $price_cleaned = str_replace('.', '', $price_input);
    $price = (int)$price_cleaned;

    $desc = mysqli_real_escape_string($conn, $_POST['Describtion']);
    $type = mysqli_real_escape_string($conn, $_POST['Type']);

    // Kiểm tra và xử lý hình ảnh
    if (isset($_FILES['Images']) && $_FILES['Images']['error'] == 0) {
        $image = $_FILES['Images'];
        $image_name = time() . "_" . basename($image['name']);
        $target_dir = "assets/img/products/";
        $target_file = $target_dir . $image_name;

        // Kiểm tra định dạng ảnh
        $check = getimagesize($image['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($image['tmp_name'], $target_file)) {
                $image_path = $target_file;
            } else {
                echo "❌ Lỗi khi tải hình ảnh lên.";
                exit();
            }
        } else {
            echo "❌ Tệp tải lên không phải là hình ảnh.";
            exit();
        }
    } else {
        echo "❌ Vui lòng chọn hình ảnh.";
        exit();
    }

    $monAn = new MonAn(null, $name, $image_path, $price, $desc, $type, 1);

    // Thêm dữ liệu vào bảng `sanpham`
    $stmt = $conn->prepare("INSERT INTO sanpham (TEN_SP, GIA_CA, MO_TA, MA_LOAISP, HINH_ANH, TINH_TRANG) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("sisss", $monAn->getTen(), $monAn->getGiaCa(), $monAn->getMoTa(), $monAn->getMaLoaiSp(), $monAn->getHinhAnh());

    if ($stmt->execute()) {
        header("Location: adminproduct.php");
        exit();
    } else {
        echo "❌ Lỗi khi thêm dữ liệu: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "⚠️ Dữ liệu không hợp lệ!";
    echo "<pre>";
    print_r($_POST);
    print_r($_FILES);
    echo "</pre>";
}

mysqli_close($conn);
?>