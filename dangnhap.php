<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />

    <!-- Bootstrap CSS -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
      crossorigin="anonymous"
    />

    <link
      rel="stylesheet"
      href="assets/font-awesome-pro-v6-6.2.0/css/all.min.css"
    />
    <link rel="stylesheet" href="assets/css/base.css" />
    <link rel="stylesheet" href="assets/css/style.css" />

    <title>Đặc sản 3 miền</title>
    <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
  </head>

  <body>
    <!-- Header --> 
    <?php
    include "includes/header.php";
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
            <form method="post">
              <div class="form-group">
                <label for="sdt">Số điện thoại</label>
                <input type="text" class="form-control" placeholder="Nhập số điện thoại" name="sdt" id="sdt" required />
              </div>

              <div class="form-group position-relative">
                <label for="password">Mật khẩu</label>
                <input type="password" class="form-control" placeholder="Nhập mật khẩu" name="password" id="password" required />
                <button type="button" class="btn position-absolute"
                  style="right: 0px; top: 73%; transform: translateY(-50%);"
                  onclick="togglePassword()">
                  <i class="fa-solid fa-eye" id="toggleIcon"></i>
                </button>
              </div>

              <?php
              if (isset($_POST['dangnhap'])) {
                  include "connect.php";
                  $phonenumber = $_POST['sdt'];
                  $password = $_POST['password'];
                  
                  $sql = "SELECT * FROM khachhang WHERE SO_DIEN_THOAI = ? AND MAT_KHAU = ?";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("ss", $phonenumber, $password);
                  $stmt->execute();
                  $result = $stmt->get_result();

                  if ($result->num_rows == 1) {
                      $row = $result->fetch_assoc();
                      if ($row['TRANG_THAI'] == 'Locked') {
                          echo '<div class="alert alert-danger">Tài khoản đã bị khóa</div>';
                      } elseif ($row['TRANG_THAI'] == 'Active') {
                          $_SESSION['sodienthoai'] = $row['SO_DIEN_THOAI'];
                          $_SESSION['mySession'] = $row['TEN_KH'];
                          $_SESSION['makh'] = $row['MA_KH'];
                          header("Location: login.php");
                          exit();
                      }
                  } else {
                      echo '<div class="alert alert-danger">Sai tài khoản hoặc mật khẩu</div>';
                  }
                  $stmt->close();
                  $conn->close();
              }
              ?>

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
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

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