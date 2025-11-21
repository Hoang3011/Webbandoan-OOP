<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Ngăn SQL injection
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Truy vấn bảng nhanvien
    $query = "SELECT * FROM nhanvien WHERE TEN_NV = '$username' AND MAT_KHAU = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $row['TEN_NV'];
        $_SESSION['ma_nv'] = $row['MA_NV'];
        header("Location: admin.php");
        exit();
    } else {
        $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng!";
        header("Location: adminlogin.php");
        exit();
    }

    mysqli_close($conn);
}
?>
