<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous" />
  <link rel="stylesheet" href="assets/font-awesome-pro-v6-6.2.0/css/all.min.css" />
  <link rel="stylesheet" href="admin/css/style.css" />
  <link rel="stylesheet" href="assets/css/base.css" />
  <link rel="stylesheet" href="assets/css/admin.css" />
  <title>Admin</title>
  <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
</head>
<body>
<?php
session_start();
include_once "connect.php";
// Include các class cần thiết
require_once "model/TaiKhoan.php";
require_once "model/QuanTriVien.php";
require_once "model/ThongKe.php";
// Hàm lấy cookie
function getCookie($name) {
  $nameEQ = $name . "=";
  $ca = explode(';', $_SERVER['HTTP_COOKIE'] ?? '');
  foreach ($ca as $c) {
    $c = trim($c);
    if (strpos($c, $nameEQ) === 0) {
      return substr($c, strlen($nameEQ));
    }
  }
  return null;
}
// Kiểm tra session hoặc cookie
$username = $_SESSION['username'] ?? getCookie("username");
$password = getCookie("password");
// Nếu không có session/cookie thì quay về đăng nhập
if (!$username || !$password) {
  header("Location: adminlogin.php");
  exit();
}
// Truy vấn xác thực tài khoản nhân viên
$sql = "SELECT MA_NV, TEN_NV, MAT_KHAU, SO_DIEN_THOAI FROM nhanvien WHERE TEN_NV = ? AND MAT_KHAU = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();
$adminData = $result->fetch_assoc();
if (!$adminData) {
  // Không tìm thấy tài khoản
  header("Location: adminlogin.php");
  exit();
}
// Tạo instance QuanTriVien
$admin = new QuanTriVien(
    $adminData['MA_NV'],
    $adminData['TEN_NV'],
    $adminData['MAT_KHAU'],
    $adminData['SO_DIEN_THOAI']
);
// Verify mật khẩu (vì không mã hóa, sử dụng verifyMatKhau)
if (!$admin->verifyMatKhau($password)) {
  header("Location: adminlogin.php");
  exit();
}
include_once "includes/headeradmin.php";
// Tạo instance ThongKe
$thongKe = new ThongKe($conn);
// 1. Tổng số khách hàng
$total_khachhang = $thongKe->soLuongKhachHang();
// 2. Tổng số sản phẩm (thêm phương thức mới vào ThongKe nếu cần, ở đây giả sử thêm)
$total_sanpham = $thongKe->soLuongSanPham();
// 3. Tổng doanh thu
$total_doanhthu = $thongKe->tongDoanhThu();
$total_doanhthu_formatted = number_format($total_doanhthu, 0, ',', '.') . "₫";
?>
<div class="Tongquan">
  <div class="inner-gth pt-3 pb-2 mb-3">
    <h1 class="page-title">Trang tổng quát của cửa hàng Đặc sản 3 miền</h1>
  </div>
  <div class="giaodien">
    <div class="row">
      <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="inner-item">
          <div class="inner-box">
            <div class="inner-so"><?php echo $total_khachhang; ?></div>
            <div class="inner-img"><img src="assets/img/admin/s1.png" /></div>
            <div class="inner-title">Khách hàng</div>
            <p class="inner-desc">Khách hàng mục tiêu là nhóm đối tượng khách hàng trong phân khúc thị trường mục tiêu mà doanh nghiệp bạn đang hướng tới.</p>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="inner-item">
          <div class="inner-box">
            <div class="inner-so"><?php echo $total_sanpham; ?></div>
            <div class="inner-img"><img src="assets/img/admin/s2.png" /></div>
            <div class="inner-title">Sản phẩm</div>
            <p class="inner-desc">Sản phẩm là bất cứ cái gì có thể đưa vào thị trường để tạo sự chú ý, mua sắm, sử dụng hay tiêu dùng nhằm thỏa mãn một nhu cầu hay ước muốn.</p>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="inner-item">
          <div class="inner-box">
            <div class="inner-so"><?php echo $total_doanhthu_formatted; ?></div>
            <div class="inner-img"><img src="assets/img/admin/s3.png" /></div>
            <div class="inner-title">Doanh thu</div>
            <p class="inner-desc">Doanh thu là toàn bộ số tiền thu được từ việc tiêu thụ sản phẩm hoặc cung cấp dịch vụ.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="admin/js/jquery.min.js"></script>
<script src="admin/js/bootstrap.min.js"></script>
<script src="admin/js/main.js"></script>
<script src="admin/js/popper.js"></script>
<script src="assets/js/admin.js"></script>
</body>
</html>