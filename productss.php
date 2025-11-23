<?php
include "connect.php";
class Order
{
    private $conn;
    private $customerId;

    public function __construct($conn, $customerId)
    {
        $this->conn = $conn;
        $this->customerId = $customerId;
    }

    public function getOrders()
    {
        $sql = "SELECT MA_DH, NGAY_TAO, DIA_CHI, TONG_TIEN, PHUONG_THUC, TINH_TRANG
                FROM donhang
                WHERE MA_KH = ?
                ORDER BY NGAY_TAO DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("i", $this->customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        $stmt->close();
        return $orders;
    }

    public function renderOrders()
    {
        $orders = $this->getOrders();
        if ($orders === false) {
            echo '<tr><td colspan="6" class="text-danger">Lỗi hệ thống. Vui lòng thử lại sau.</td></tr>';
            return;
        }
        if (count($orders) === 0) {
            echo '<tr><td colspan="6" class="no-order">
                    <i class="fas fa-box-open fa-2x mb-3"></i><br>
                    Bạn chưa có đơn hàng nào.
                  </td></tr>';
            return;
        }
        foreach ($orders as $row) {
            $ngay = $row['NGAY_TAO'] ? date("d-m-Y H:i", strtotime($row['NGAY_TAO'])) : 'Chưa xác định';
            $tongtien = number_format($row['TONG_TIEN'], 0, ',', '.');
            $trangthai = $row['TINH_TRANG'];
            $class = '';
            switch ($trangthai) {
                case 'Chưa xác nhận':
                    $class = 'status-chua';
                    break;
                case 'Đã xác nhận':
                    $class = 'status-xacnhan';
                    break;
                case 'Đã giao thành công':
                    $class = 'status-giao';
                    break;
                case 'Đã hủy đơn':
                    $class = 'status-huy';
                    break;
            }
            echo '<tr>
                    <td><a class="font-weight-bold text-primary" href="chitiet.php?madh=' . $row['MA_DH'] . '">
                        DH' . sprintf("%04d", $row['MA_DH']) . '
                        <i class="fas fa-external-link-alt fa-xs"></i>
                    </a></td>
                    <td class="text-center">' . $ngay . '</td>
                    <td>' . htmlspecialchars($row['DIA_CHI']) . '</td>
                    <td class="font-weight-bold text-danger">' . $tongtien . '₫</td>
                    <td>' . htmlspecialchars($row['PHUONG_THUC']) . '</td>
                    <td class="' . $class . '">' . htmlspecialchars($trangthai) . '</td>
                </tr>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/font-awesome-pro-v6-6.2.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/base.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <title>Đơn hàng của tôi - Đặc sản 3 miền</title>
  <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
  <style>
    .inner-title {
      font-size: 28px;
      font-weight: 700;
      color: #333;
      margin-bottom: 10px;
    }

    .inner-desc {
      color: #666;
      margin-bottom: 20px;
    }

    .table th {
      background: #f8f9fa;
      text-align: center;
    }

    .table td {
      vertical-align: middle;
    }

    .status-chua {
      color: #dc3545;
      font-weight: 600;
    }

    .status-xacnhan {
      color: #ffc107;
      font-weight: 600;
    }

    .status-giao {
      color: #28a745;
      font-weight: 600;
    }

    .status-huy {
      color: #6c757d;
      font-weight: 600;
      text-decoration: line-through;
    }

    .no-order {
      text-align: center;
      padding: 40px;
      color: #999;
      font-size: 18px;
    }
  </style>
</head>

<body>

  <?php include_once "includes/headerlogin.php"; ?>

  <div class="products">
    <div class="container">
      <div class="row">
        <div class="col-xl-12">
          <div class="inner-title">Quản lý đơn hàng của bạn</div>
          <div class="inner-desc">
            Xem chi tiết, trạng thái của những đơn hàng đã đặt.
          </div>
        </div>
      </div>

      <div class="inner-menu">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Đơn hàng</th>
              <th>Ngày đặt</th>
              <th>Địa chỉ</th>
              <th>Giá trị</th>
              <th>Thanh toán</th>
              <th>Trạng thái</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (!isset($_SESSION['makh'])) {
              echo '<tr><td colspan="6" class="no-order">
                      <i class="fas fa-sign-in-alt fa-2x mb-3"></i><br>
                      Bạn cần <a href="login.php">đăng nhập</a> để xem đơn hàng.
                    </td></tr>';
            } else {
              $order = new Order($conn, $_SESSION['makh']);
              $order->renderOrders();
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <?php include_once "includes/footer.php"; ?>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>