<?php
session_start();
include 'connect.php';
require_once "model/TaiKhoan.php";
require_once "model/QuanTriVien.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Truy vấn bảng nhanvien với prepared statement
    $sql = "SELECT * FROM nhanvien WHERE TEN_NV = ? AND MAT_KHAU = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $admin = new QuanTriVien($row['MA_NV'], $row['TEN_NV'], $row['MAT_KHAU'], $row['SO_DIEN_THOAI']);
        
        if ($admin->verifyMatKhau($password)) {
            $_SESSION['username'] = $row['TEN_NV'];
            $_SESSION['ma_nv'] = $row['MA_NV'];
            header("Location: admin.php");
            exit();
        } else {
            $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng!";
            header("Location: adminlogin.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng!";
        header("Location: adminlogin.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>