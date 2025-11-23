<?php
require_once 'model/KhachHang.php';
include_once "connect.php";
include "includes/header.php";
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    crossorigin="anonymous" />
  <link rel="stylesheet" href="assets/font-awesome-pro-v6-6.2.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/base.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <title>Đăng ký - Đặc sản 3 miền</title>
  <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
  <style>
    .register {
      padding-top: 80px;
      padding-bottom: 48px;
    }

    .btn {
      background-color: var(--color-bg2);
      color: #fff;
    }
  </style>
</head>

<body>
  <div class="register">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-10">
          <h2 class="text-center mb-4">Đăng ký</h2>

          <?php
          $errorMsg = ''; // Biến lưu thông báo lỗi
          
          if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dangki'])) {
            // 1. Lấy dữ liệu và làm sạch
            $tenkh = trim($_POST['ten'] ?? '');
            $sdtkh = trim($_POST['sdt'] ?? '');
            $diachikh = trim($_POST['diachi'] ?? '');
            $pass = $_POST['password'] ?? '';
            $pass1 = $_POST['password1'] ?? '';

            // 2. Validate cơ bản
            if (empty($tenkh) || empty($sdtkh) || empty($diachikh) || empty($pass)) {
              $errorMsg = 'Vui lòng điền đầy đủ thông tin.';
            } elseif ($pass !== $pass1) {
              $errorMsg = 'Mật khẩu nhập lại không khớp!';
            } else {
              // 3. Kiểm tra số điện thoại đã tồn tại chưa
              $sql_check = "SELECT MA_KH FROM khachhang WHERE SO_DIEN_THOAI = ?";
              if ($stmt_check = $conn->prepare($sql_check)) {
                $stmt_check->bind_param("s", $sdtkh);
                $stmt_check->execute();
                $stmt_check->store_result(); // Lưu kết quả để đếm row
          
                if ($stmt_check->num_rows > 0) {
                  $errorMsg = 'Số điện thoại đã được đăng ký!';
                }
                $stmt_check->close();
              } else {
                $errorMsg = 'Lỗi kiểm tra tài khoản.';
              }
            }

            // 4. Nếu không có lỗi thì tiến hành Insert
            if (empty($errorMsg)) {

              // Khởi tạo đối tượng KhachHang (tham số theo lớp model)
              // __construct($id, $ten, $matKhau, $diaChi, $soDienThoai, $trangThai = 'Active', $ngayTao = null)
              // Lưu mật khẩu nguyên bản theo yêu cầu (không hash)
              $kh = new KhachHang(null, $tenkh, $pass, $diachikh, $sdtkh);

              $sql = "INSERT INTO khachhang (TEN_KH, MAT_KHAU, DIA_CHI, SO_DIEN_THOAI, NGAY_TAO, TRANG_THAI) 
                        VALUES (?, ?, ?, ?, CURDATE(), ?)";

              if ($stmt = $conn->prepare($sql)) {
                // Lấy các giá trị từ Object thông qua Getter
                $ten = $kh->getTen();
                $matKhau = $kh->getMatKhau();
                $diaChi = $kh->getDiaChi();
                $soDienThoai = $kh->getSoDienThoai();
                $trangThai = $kh->getTrangThai();

                // TRANG_THAI trong model là string ('Active'/'Locked') -> bind as string
                $stmt->bind_param("sssss", $ten, $matKhau, $diaChi, $soDienThoai, $trangThai);

                if ($stmt->execute()) {
                  // Đăng ký thành công -> Lưu session -> Chuyển hướng
                  if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                  }
                  $_SESSION['mySession'] = $kh->getTen();
                  $_SESSION['makh'] = $conn->insert_id; // Lấy ID vừa tạo
          
                  echo "<script> window.location.href='dangnhap.php';</script>";
                  exit();
                } else {
                  $errorMsg = 'Lỗi hệ thống: ' . $stmt->error;
                }
                $stmt->close();
              } else {
                $errorMsg = 'Không thể kết nối cơ sở dữ liệu.';
              }
            }
          }

          // Hiển thị lỗi nếu có
          if (!empty($errorMsg)) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($errorMsg) . '</div>';
          }
          ?>

          <form method="post" novalidate>
            <div class="form-group">
              <label for="ten">Tên đầy đủ</label>
              <input type="text" class="form-control" placeholder="Nhập tên đầy đủ" name="ten" id="ten"
                value="<?php echo isset($tenkh) ? htmlspecialchars($tenkh) : ''; ?>" required />
            </div>

            <div class="form-group">
              <label for="sdt">Số điện thoại</label>
              <input type="text" class="form-control" placeholder="Nhập số điện thoại" name="sdt" id="sdt"
                value="<?php echo isset($sdtkh) ? htmlspecialchars($sdtkh) : ''; ?>" required />
            </div>

            <div class="form-group">
              <label for="diachi">Địa chỉ</label>
              <input type="text" class="form-control" placeholder="Nhập địa chỉ" name="diachi" id="diachi"
                value="<?php echo isset($diachikh) ? htmlspecialchars($diachikh) : ''; ?>" required />
            </div>

            <div class="form-group position-relative">
              <label for="password">Mật khẩu</label>
              <input type="password" class="form-control" placeholder="Nhập mật khẩu" name="password" id="password"
                required />
              <button type="button" class="btn position-absolute"
                style="right: 0px; top: 73%; transform: translateY(-50%);"
                onclick="togglePassword('password', 'toggleIcon')">
                <i class="fa-solid fa-eye" id="toggleIcon"></i>
              </button>
            </div>

            <div class="form-group position-relative">
              <label for="password1">Nhập lại mật khẩu</label>
              <input type="password" class="form-control" placeholder="Nhập lại mật khẩu" name="password1"
                id="password1" required />
              <button type="button" class="btn position-absolute"
                style="right: 0px; top: 73%; transform: translateY(-50%);"
                onclick="togglePassword('password1', 'toggleIcon1')">
                <i class="fa-solid fa-eye" id="toggleIcon1"></i>
              </button>
            </div>

            <button type="submit" name="dangki" class="btn btn-block text-uppercase font-weight-bold">
              Đăng ký
            </button>

            <div class="text-center mt-3">
              <span>Bạn đã có tài khoản?</span>
              <a href="dangnhap.php" class="d-block text-primary">Đăng nhập tại đây</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php include_once "includes/footer.php"; ?>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

  <script>
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