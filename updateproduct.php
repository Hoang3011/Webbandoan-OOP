<?php
include "connect.php";
require_once "model/MonAn.php";

// Kiểm tra dữ liệu gửi từ form
if (isset($_POST['id'], $_POST['Name'], $_POST['Price'], $_POST['Describtion'], $_POST['Type'], $_POST['Visible'])) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['Name']);
    $price = (int)str_replace('.', '', $_POST['Price']); // Bỏ dấu chấm ngăn cách
    $desc = mysqli_real_escape_string($conn, $_POST['Describtion']);
    $type = mysqli_real_escape_string($conn, $_POST['Type']);
    $visible = intval($_POST['Visible']); // 1 = đang bán, 0 = ngừng

    // Xử lý ảnh
    if (isset($_FILES['Images']) && $_FILES['Images']['error'] == 0) {
        $image = $_FILES['Images'];
        $image_name = time() . "_" . basename($image['name']);
        $target_dir = "assets/img/products/";
        $target_file = $target_dir . $image_name;

        // Kiểm tra file có phải hình ảnh không
        $check = getimagesize($image['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($image['tmp_name'], $target_file)) {
                $image_path = $target_file;
            } else {
                echo "❌ Lỗi khi tải hình ảnh lên.";
                exit();
            }
        } else {
            echo "❌ Tệp không phải là hình ảnh.";
            exit();
        }
    } else {
        // Nếu không có ảnh mới → lấy lại ảnh cũ
        $sql_old = "SELECT HINH_ANH FROM sanpham WHERE MA_SP = ?";
        $stmt_old = $conn->prepare($sql_old);
        $stmt_old->bind_param("i", $id);
        $stmt_old->execute();
        $result_old = $stmt_old->get_result();
        if ($result_old && $row_old = $result_old->fetch_assoc()) {
            $image_path = $row_old['HINH_ANH'];
        } else {
            echo "❌ Không tìm thấy sản phẩm.";
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
    $stmt->bind_param("sisssii", $updatedMonAn->getTen(), $updatedMonAn->getGiaCa(), $updatedMonAn->getMoTa(), $updatedMonAn->getMaLoaiSp(), $updatedMonAn->getHinhAnh(), $updatedMonAn->getTinhTrang(), $updatedMonAn->getId());

    if ($stmt->execute()) {
        header("Location: adminproduct.php");
        exit();
    } else {
        echo "❌ Lỗi khi cập nhật dữ liệu: " . $stmt->error;
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