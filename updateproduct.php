<?php
include "connect.php";
require_once "model/MonAn.php";

// Kiểm tra dữ liệu gửi từ form
if (isset($_POST['id'], $_POST['Name'], $_POST['Price'], $_POST['Describtion'], $_POST['Type'], $_POST['Visible'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['Name']);
    // Loại bỏ mọi ký tự không phải số khỏi giá (vd: dấu chấm, dấu cách)
    $price = (int) preg_replace('/\D+/', '', $_POST['Price']);
    $desc = trim($_POST['Describtion']);
    $type = trim($_POST['Type']);
    $visible = intval($_POST['Visible']); // 1 = đang bán, 0 = ngừng

    // Xử lý ảnh
    $image_path = null;
    if (isset($_FILES['Images']) && $_FILES['Images']['error'] == 0) {
        $image = $_FILES['Images'];
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        $check = getimagesize($image['tmp_name']);

        if ($check === false) {
            echo "❌ Tệp không phải là hình ảnh.";
            exit();
        }
        if (!in_array($ext, $allowed_ext)) {
            echo "❌ Định dạng ảnh không được hỗ trợ.";
            exit();
        }
        if ($image['size'] > $max_size) {
            echo "❌ Kích thước ảnh quá lớn (tối đa 5MB).";
            exit();
        }

        $image_name = time() . "_" . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($image['name']));
        $target_dir = "assets/img/products/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $target_file = $target_dir . $image_name;

        if (!move_uploaded_file($image['tmp_name'], $target_file)) {
            echo "❌ Lỗi khi tải hình ảnh lên.";
            exit();
        }
        $image_path = $target_file;
    } else {
        // Nếu không có ảnh mới → lấy lại ảnh cũ
        $sql_old = "SELECT HINH_ANH FROM sanpham WHERE MA_SP = ?";
        $stmt_old = $conn->prepare($sql_old);
        if ($stmt_old === false) {
            echo "❌ Lỗi hệ thống: " . htmlspecialchars($conn->error);
            exit();
        }
        $stmt_old->bind_param("i", $id);
        $stmt_old->execute();
        $result_old = $stmt_old->get_result();
        if ($result_old && $row_old = $result_old->fetch_assoc()) {
            $image_path = $row_old['HINH_ANH'];
        } else {
            echo "❌ Không tìm thấy sản phẩm.";
            $stmt_old->close();
            exit();
        }
        $stmt_old->close();
    }

    $updatedMonAn = new MonAn($id, $name, $image_path, $price, $desc, $type, $visible);

    // Cập nhật dữ liệu đúng cột trong DB
    $stmt = $conn->prepare("
        UPDATE sanpham 
        SET TEN_SP = ?, GIA_CA = ?, MO_TA = ?, MA_LOAISP = ?, HINH_ANH = ?, TINH_TRANG = ? 
        WHERE MA_SP = ?
    ");
    if ($stmt === false) {
        echo "❌ Lỗi hệ thống: " . htmlspecialchars($conn->error);
        exit();
    }

    // Bind param types: s = string, i = integer
    $stmt->bind_param(
        "sisssii",
        $updatedMonAn->getTen(),
        $updatedMonAn->getGiaCa(),
        $updatedMonAn->getMoTa(),
        $updatedMonAn->getMaLoaiSp(),
        $updatedMonAn->getHinhAnh(),
        $updatedMonAn->getTinhTrang(),
        $updatedMonAn->getId()
    );

    if ($stmt->execute()) {
        header("Location: adminproduct.php");
        exit();
    } else {
        echo "❌ Lỗi khi cập nhật dữ liệu: " . htmlspecialchars($stmt->error);
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