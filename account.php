<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />

    <link rel="stylesheet" href="assets/font-awesome-pro-v6-6.2.0/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/base.css" />
    <link rel="stylesheet" href="assets/css/style.css" />

    <title>Đặc sản 3 miền</title>
    <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
</head>
<body>
    <?php

    include_once "includes/headerlogin.php";
    include "connect.php";

    // Kiểm tra phiên
    if (!isset($_SESSION['sodienthoai'])) {
        header('location:login.php');
        exit();
    }

    // Xử lý yêu cầu AJAX
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        $sodienthoai = $_SESSION['sodienthoai'];

        if ($_POST['action'] == 'update') {
            // Xử lý cập nhật thông tin
            $name = $_POST['name'] ?? '';
            $diachi = $_POST['diachi'] ?? '';

            // Kiểm tra dữ liệu đầu vào
            if (empty($name) || empty($diachi)) {
                echo "Họ tên và địa chỉ không được để trống!";
                exit();
            }

            // Cập nhật thông tin
            $sql = "UPDATE khachhang SET TEN_KH = ?, DIA_CHI = ? WHERE SO_DIEN_THOAI = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $name, $diachi, $sodienthoai);

            if ($stmt->execute()) {
                $_SESSION['mySession'] = $name; // Cập nhật tên trong phiên
                echo "Cập nhật thông tin thành công!";
            } else {
                echo "Lỗi khi cập nhật thông tin!";
            }

            $stmt->close();
        } elseif ($_POST['action'] == 'change_password') {
            // Xử lý đổi mật khẩu
            $currentPassword = $_POST['currentPassword'] ?? '';
            $newPassword = $_POST['newPassword'] ?? '';

            // Kiểm tra dữ liệu đầu vào
            if (empty($currentPassword) || empty($newPassword)) {
                echo "Vui lòng điền đầy đủ các trường mật khẩu!";
                exit();
            }

            // Kiểm tra mật khẩu hiện tại
            $sql = "SELECT MAT_KHAU FROM khachhang WHERE SO_DIEN_THOAI = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $sodienthoai);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if ($currentPassword === $row['MAT_KHAU']) {
                    // Cập nhật mật khẩu mới
                    $sql_update = "UPDATE khachhang SET MAT_KHAU = ? WHERE SO_DIEN_THOAI = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("ss", $newPassword, $sodienthoai);

                    if ($stmt_update->execute()) {
                        echo "Đổi mật khẩu thành công!";
                    } else {
                        echo "Lỗi khi đổi mật khẩu!";
                    }
                    $stmt_update->close();
                } else {
                    echo "Mật khẩu hiện tại không đúng!";
                }
            } else {
                echo "Không tìm thấy tài khoản!";
            }

            $stmt->close();
        }

        $conn->close();
        exit();
    }

    // Lấy thông tin người dùng
    $phone = $_SESSION['sodienthoai'];
    $sql = "SELECT * FROM khachhang WHERE SO_DIEN_THOAI = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy thông tin người dùng!";
        exit();
    }
    $stmt->close();
    $conn->close();
    ?>

    <style>
        .btn {
          background: var(--color-bg2);
          color: #fff;
        }
    </style>

    <!-- Account -->
    <div class="Account">
        <div class="container">
            <form action="">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="inner-title">Thông tin tài khoản của bạn</div>
                        <div class="inner-desc">Quản lý thông tin để bảo mật tài khoản</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="form-group">
                            <label for="name1">Họ và tên</label>
                            <input type="text" id="name1" class="form-control" value="<?php echo htmlspecialchars($row['TEN_KH']); ?>" />
                        </div>
                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" id="phone" class="form-control" value="<?php echo htmlspecialchars($row['SO_DIEN_THOAI']); ?>" disabled />
                        </div>
                        <div class="form-group">
                            <label for="diachi">Địa chỉ</label>
                            <input type="text" id="diachi" class="form-control" value="<?php echo htmlspecialchars($row['DIA_CHI']); ?>" />
                        </div>
                        <button type="button" class="button" onclick="capNhat()">
                            <i class="fa-regular fa-floppy-disk"></i> Lưu thay đổi
                        </button>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="form-group position-relative">
                            <label for="mk1">Mật khẩu hiện tại</label>
                            <input type="password" id="mk1" class="form-control" placeholder="Nhập mật khẩu hiện tại" />
                            <button type="button" class="btn position-absolute" style="right: 0px; top: 71.7%; transform: translateY(-50%);" onclick="togglePassword('mk1', 'toggleIcon1')">
                                <i class="fa-solid fa-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                        <div class="form-group position-relative">
                            <label for="mk3">Mật khẩu mới</label>
                            <input type="password" id="mk3" class="form-control" placeholder="Nhập mật khẩu mới" />
                            <button type="button" class="btn position-absolute" style="right: 0px; top: 71.7%; transform: translateY(-50%);" onclick="togglePassword('mk3', 'toggleIcon3')">
                                <i class="fa-solid fa-eye" id="toggleIcon3"></i>
                            </button>
                        </div>
                        <div class="form-group position-relative cach-duoi">
                            <label for="mk4">Xác nhận mật khẩu mới</label>
                            <input type="password" id="mk4" class="form-control" placeholder="Nhập lại mật khẩu mới" />
                            <button type="button" class="btn position-absolute" style="right: 0px; top: 71.7%; transform: translateY(-50%);" onclick="togglePassword('mk4', 'toggleIcon4')">
                                <i class="fa-solid fa-eye" id="toggleIcon4"></i>
                            </button>
                        </div>
                        <button type="button" class="button" onclick="thayDoi()">
                            <i class="fa-solid fa-key"></i> Đổi mật khẩu
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End Account -->

    <?php include_once "includes/footer.php"; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <!-- JavaScript for handling updates and password change -->
    <script>
        // Hàm cập nhật thông tin tài khoản
        function capNhat() {
            var name = document.getElementById('name1').value;
            var diachi = document.getElementById('diachi').value;

            // Kiểm tra dữ liệu đầu vào
            if (!name || !diachi) {
                alert("Vui lòng điền đầy đủ họ tên và địa chỉ!");
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "account.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert("Cập nhật thông tin thành công!");
                }
            };
            xhr.send("action=update&name=" + encodeURIComponent(name) + "&diachi=" + encodeURIComponent(diachi));
        }

        // Hàm thay đổi mật khẩu
        function thayDoi() {
            var currentPassword = document.getElementById('mk1').value;
            var newPassword = document.getElementById('mk3').value;
            var confirmPassword = document.getElementById('mk4').value;

            // Kiểm tra dữ liệu đầu vào
            if (!currentPassword || !newPassword || !confirmPassword) {
                alert("Vui lòng điền đầy đủ các trường mật khẩu!");
                return;
            }

            if (newPassword !== confirmPassword) {
                alert("Mật khẩu mới và xác nhận mật khẩu không khớp!");
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "account.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert("Đổi mật khẩu thành công!");
                    // Xóa các trường nhập liệu sau khi đổi mật khẩu thành công
                    if (xhr.responseText.includes("thành công")) {
                        document.getElementById('mk1').value = '';
                        document.getElementById('mk3').value = '';
                        document.getElementById('mk4').value = '';
                    }
                }
            };
            xhr.send("action=change_password&currentPassword=" + encodeURIComponent(currentPassword) + "&newPassword=" + encodeURIComponent(newPassword));
        }

        // Hàm hiển thị/ẩn mật khẩu
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>