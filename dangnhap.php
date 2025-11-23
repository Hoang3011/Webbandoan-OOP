<?php
require_once 'model/KhachHang.php';
include_once "connect.php";
include "includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    crossorigin="anonymous" />

  <link rel="stylesheet" href="assets/font-awesome-pro-v6-6.2.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/base.css" />
  <link rel="stylesheet" href="assets/css/style.css" />

  <title>Đặc sản 3 miền</title>
  <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
</head>

<body>
  <!-- Header -->
  <?php
  // header already included above
  ?>
  <!-- Close Header -->

  <style>
    .login {
      padding-top: 80px;
      padding-bottom: 48px;
    }

    .btn {
      background-color: var(--color-bg2);
      color: #fff;
    }
  </style>

  <!-- Login Form -->
  <div class="login">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-10">
          <h2 class="text-center mb-4">Đăng nhập</h2>

          <?php
          $errorMsg = '';
          if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dangnhap'])) {
            $phonenumber = trim($_POST['sdt'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($phonenumber === '' || $password === '') {
              $errorMsg = 'Vui lòng nhập số điện thoại và mật khẩu.';
            } else {
              $sql = "SELECT * FROM khachhang WHERE SO_DIEN_THOAI = ? LIMIT 1";
              if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $phonenumber);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows === 1) {
                  $row = $result->fetch_assoc();

                  // Khởi tạo object KhachHang từ DB
                  $kh = new KhachHang(
                    $row['MA_KH'],
                    $row['TEN_KH'],
                    $row['MAT_KHAU'],
                    $row['DIA_CHI'],
                    $row['SO_DIEN_THOAI'],
                    $row['TRANG_THAI'],
                    $row['NGAY_TAO']
                  );

                  // So sánh mật khẩu nguyên bản theo yêu cầu (không hash)
                  if ($password !== $kh->getMatKhau()) {
                    $errorMsg = 'Sai tài khoản hoặc mật khẩu';
                  } else {
                    if ($kh->getTrangThai() === 'Locked') {
                      $errorMsg = 'Tài khoản đã bị khóa';
                    } else {
                      if (session_status() === PHP_SESSION_NONE)
                        session_start();
                      $_SESSION['sodienthoai'] = $kh->getSoDienThoai();
                      $_SESSION['mySession'] = $kh->getTen();
                      $_SESSION['makh'] = $kh->getId();
                      header("Location: login.php");
                      exit();
                    }
                  }
                } else {
                  $errorMsg = 'Sai tài khoản hoặc mật khẩu';
                }

                $stmt->close();
              } else {
                $errorMsg = 'Lỗi kết nối cơ sở dữ liệu.';
              }
            }
          }

          if (!empty($errorMsg)) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($errorMsg) . '</div>';
          }
          ?>

          <form method="post">
            <div class="form-group">
              <label for="sdt">Số điện thoại</label>
              <input type="text" class="form-control" placeholder="Nhập số điện thoại" name="sdt" id="sdt"
                value="<?php echo isset($phonenumber) ? htmlspecialchars($phonenumber) : ''; ?>" required />
            </div>

            <div class="form-group position-relative">
              <label for="password">Mật khẩu</label>
              <input type="password" class="form-control" placeholder="Nhập mật khẩu" name="password" id="password"
                required />
              <button type="button" class="btn position-absolute"
                style="right: 0px; top: 73%; transform: translateY(-50%);" onclick="togglePassword()">
                <i class="fa-solid fa-eye" id="toggleIcon"></i>
              </button>
            </div>

            <button type="submit" name="dangnhap" class="btn btn-block text-uppercase font-weight-bold">
              Đăng nhập
            </button>

            <div class="text-center mt-3">
              <span>Bạn chưa có tài khoản?</span>
              <a href="dangky.php" class="d-block text-primary">Đăng ký tại đây</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php include_once "includes/footer.php"; ?>
  <!-- Close Footer -->

  <!-- Bootstrap JS and dependencies -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

  <!-- Password toggle script -->
  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('toggleIcon');
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