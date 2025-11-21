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

    <title>Đăng ký - Đặc sản 3 miền</title>
    <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
  </head>

  <body>
    <!-- Header -->
    <?php
    include "includes/header.php";
    ?>

    <!-- Inline CSS for consistent styling with login page -->
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

    <!-- Register Form -->
    <div class="register">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-6 col-md-8 col-sm-10">
            <h2 class="text-center mb-4">Đăng ký</h2>
            <?php
            include "connect.php";
            if (isset($_POST['dangki'])) {
                $tenkh = trim($_POST['ten'] ?? '');
                $sdtkh = trim($_POST['sdt'] ?? '');
                $diachikh = trim($_POST['diachi'] ?? '');
                $pass = $_POST['password'] ?? '';
                $pass1 = $_POST['password1'] ?? '';

                if ($pass !== $pass1) {
                    echo '<div class="alert alert-danger">Mật khẩu nhập lại không khớp!</div>';
                } else {
                    $sql_check = "SELECT SO_DIEN_THOAI FROM khachhang WHERE SO_DIEN_THOAI = ?";
                    $stmt_check = $conn->prepare($sql_check);
                    $stmt_check->bind_param("s", $sdtkh);
                    $stmt_check->execute();
                    $result_check = $stmt_check->get_result();

                    if ($result_check->num_rows > 0) {
                        echo '<div class="alert alert-danger">Số điện thoại đã được đăng ký!</div>';
                    } else {
                        $sql = "INSERT INTO khachhang (TEN_KH, MAT_KHAU, DIA_CHI, SO_DIEN_THOAI, NGAY_TAO) 
                                VALUES (?, ?, ?, ?, CURDATE())";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssss", $tenkh, $pass, $diachikh, $sdtkh);

                        if ($stmt->execute()) {
                            $_SESSION['mySession'] = $tenkh;
                            $_SESSION['makh'] = $conn->insert_id;
                            header("Location: dangnhap.php");
                            exit();
                        } else {
                            echo '<div class="alert alert-danger">Đăng ký thất bại: ' . $stmt->error . '</div>';
                        }
                        $stmt->close();
                    }
                    $stmt_check->close();
                }
                $conn->close();
            }
            ?>

            <form method="post">
              <div class="form-group">
                <label for="ten">Tên đầy đủ</label>
                <input type="text" class="form-control" placeholder="Nhập tên đầy đủ" name="ten" id="ten" required />
              </div>

              <div class="form-group">
                <label for="sdt">Số điện thoại</label>
                <input type="text" class="form-control" placeholder="Nhập số điện thoại" name="sdt" id="sdt" required />
              </div>

              <div class="form-group">
                <label for="diachi">Địa chỉ</label>
                <input type="text" class="form-control" placeholder="Nhập địa chỉ" name="diachi" id="diachi" required />
              </div>

              <div class="form-group position-relative">
                <label for="password">Mật khẩu</label>
                <input type="password" class="form-control" placeholder="Nhập mật khẩu" name="password" id="password" required />
                <button type="button" class="btn position-absolute"
                  style="right: 0px; top: 73%; transform: translateY(-50%);"
                  onclick="togglePassword('password', 'toggleIcon')">
                  <i class="fa-solid fa-eye" id="toggleIcon"></i>
                </button>
              </div>

              <div class="form-group position-relative">
                <label for="password1">Nhập lại mật khẩu</label>
                <input type="password" class="form-control" placeholder="Nhập lại mật khẩu" name="password1" id="password1" required />
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
    <!-- Close Footer -->

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <!-- Password toggle script -->
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