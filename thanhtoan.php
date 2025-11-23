<?php
require_once "connect.php";

// --- Models ---
class Cart
{
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function getLatestCartId($customerId)
    {
        $stmt = $this->conn->prepare("SELECT MA_GH FROM giohang WHERE MA_KH = ? ORDER BY MA_GH DESC LIMIT 1");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart = $result->fetch_assoc();
        $stmt->close();
        return $cart ? $cart['MA_GH'] : null;
    }

    public function getCartItems($cartId)
    {
        $stmt = $this->conn->prepare("
            SELECT ct.SO_LUONG AS soluong, sp.TEN_SP AS Name, sp.HINH_ANH AS Image, sp.GIA_CA AS Price
            FROM chitietgiohang ct
            JOIN sanpham sp ON ct.MA_SP = sp.MA_SP
            WHERE ct.MA_GH = ?
        ");
        $stmt->bind_param("i", $cartId);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        return $items;
    }

    public function getCartSummary($cartId)
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(DISTINCT ct.MA_SP) AS tong_mon, SUM(ct.SO_LUONG * sp.GIA_CA) AS tong_tien
            FROM chitietgiohang ct
            JOIN sanpham sp ON ct.MA_SP = sp.MA_SP
            WHERE ct.MA_GH = ?
        ");
        $stmt->bind_param("i", $cartId);
        $stmt->execute();
        $result = $stmt->get_result();
        $summary = $result->fetch_assoc();
        $stmt->close();
        return $summary;
    }

    public function clearCart($cartId)
    {
        $stmt = $this->conn->prepare("DELETE FROM chitietgiohang WHERE MA_GH = ?");
        $stmt->bind_param("i", $cartId);
        $stmt->execute();
        $stmt->close();

        $stmt = $this->conn->prepare("DELETE FROM giohang WHERE MA_GH = ?");
        $stmt->bind_param("i", $cartId);
        $stmt->execute();
        $stmt->close();
    }
}

class Customer
{
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function getInfo($customerId)
    {
        $stmt = $this->conn->prepare("SELECT TEN_KH AS tenkh, DIA_CHI AS diachi, SO_DIEN_THOAI AS sodienthoai FROM khachhang WHERE MA_KH = ?");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $info = $result->fetch_assoc();
        $stmt->close();
        return $info;
    }
}

class Order
{
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function create($customerId, $cartId, $total, $address, $note, $method)
    {
        $stmt = $this->conn->prepare("INSERT INTO donhang (MA_KH, MA_GH, TONG_TIEN, DIA_CHI, GHI_CHU, PHUONG_THUC, TINH_TRANG) VALUES (?, ?, ?, ?, ?, ?, 'Chưa xác nhận')");
        $stmt->bind_param("iiisss", $customerId, $cartId, $total, $address, $note, $method);
        $stmt->execute();
        $orderId = $stmt->insert_id;
        $stmt->close();
        return $orderId;
    }

    public function copyCartDetails($orderId, $cartId)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO chitietdonhang (MA_DH, MA_SP, SO_LUONG, GIA_LUC_MUA)
            SELECT ?, ct.MA_SP, ct.SO_LUONG, sp.GIA_CA
            FROM chitietgiohang ct
            JOIN sanpham sp ON ct.MA_SP = sp.MA_SP
            WHERE ct.MA_GH = ?
        ");
        $stmt->bind_param("ii", $orderId, $cartId);
        $stmt->execute();
        $stmt->close();
    }
}

// --- Instantiate Models ---
$cartModel = new Cart($conn);
$customerModel = new Customer($conn);
$orderModel = new Order($conn);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />

    <link rel="stylesheet" href="assets/font-awesome-pro-v6-6.2.0/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/base.css" />
    <link rel="stylesheet" href="assets/css/style.css" />

    <title>Đặc sản 3 miền</title>
    <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
</head>

<body>
    <!-- Notification -->
    <div id="success-notification" class="notification success">
        <div class="notification-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="notification-content">
            <div class="notification-title">Thành công</div>
            <div class="notification-message">Đặt hàng thành công!</div>
        </div>
        <button class="notification-close" onclick="closeNotification()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Header -->
    <?php include "includes/headerlogin.php"; ?>

    <style>
        .form-check {
            padding: 0px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        input[type="radio"]:checked+label:after {
            top: 5px;
            left: 5.5px;
        }

        input[type="radio"]+label:before {
            top: 0px;
            left: 0px;
        }

        /* Notification styles */
        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 20px;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            align-items: center;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            min-width: 300px;
        }

        .notification.show {
            opacity: 1;
        }

        .notification-icon {
            margin-right: 10px;
        }

        .notification-icon i {
            color: #28a745;
            font-size: 24px;
        }

        .notification-content {
            flex-grow: 1;
        }

        .notification-title {
            font-weight: bold;
            font-size: 16px;
            color: #333;
        }

        .notification-message {
            font-size: 14px;
            color: #666;
        }

        .notification-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
        }

        .notification-close i {
            color: #999;
            font-size: 16px;
        }

        .notification-close:hover i {
            color: #333;
        }
    </style>

    <!-- Account -->
    <div class="ThongTin">
        <div class="container">
            <form action="thanhtoan.php" method="post">
                <div class="row">
                    <div class="col-xl-7 col-lg-7 col-md-6 col-sm-12">
                        <!-- Giỏ hàng -->
                        <div class="inner-item">
                            <div class="inner-tt">Giỏ hàng</div>
                            <?php
                            if (isset($_SESSION['makh'])) {
                                $makh = $_SESSION['makh'];
                                $ma_gh = $cartModel->getLatestCartId($makh);
                                if ($ma_gh) {
                                    $items = $cartModel->getCartItems($ma_gh);
                                    foreach ($items as $row) {
                                        $ten = htmlspecialchars($row['Name']);
                                        $soluong = $row['soluong'];
                                        $gia = number_format($row['Price'], 0, ',', '.') . "đ";
                                        $hinhanh = htmlspecialchars($row['Image']);
                                        echo '
                                        <div class="inner-gth">
                                            <div class="inner-img">
                                                <img src="' . $hinhanh . '" alt="' . $ten . '" />
                                            </div>
                                            <div class="inner-mota">
                                                <div class="inner-ten">' . $ten . '</div>
                                                <div class="inner-sl">Số lượng: ' . $soluong . '</div>
                                                <div class="inner-gia">' . $gia . '</div>
                                            </div>
                                        </div>';
                                    }
                                } else {
                                    echo '<p class="text-danger">Giỏ hàng trống.</p>';
                                }
                            } else {
                                echo '<p class="text-danger">Vui lòng đăng nhập để xem giỏ hàng.</p>';
                            }
                            ?>
                        </div>

                        <!-- Thông tin khách hàng -->
                        <div class="inner-item">
                            <div class="inner-tt">Thông tin khách hàng</div>
                            <div class="row">
                                <?php
                                if (isset($_SESSION['makh'])) {
                                    $makh = $_SESSION['makh'];
                                    $info = $customerModel->getInfo($makh);
                                    if ($info) {
                                        echo '
                                        <div class="col-xl-12">
                                            <div class="form-group">
                                                <label for="name">Họ và tên:</label>
                                                <input type="text" id="name" class="form-control" value="' . htmlspecialchars($info['tenkh']) . '" readonly />
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="form-group">
                                                <label for="sdt">Số điện thoại:</label>
                                                <input type="text" id="sdt" class="form-control" value="' . htmlspecialchars($info['sodienthoai']) . '" readonly />
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="form-group">
                                                <label for="diachi">Địa chỉ giao hàng:</label>
                                                <input type="text" id="diachi" name="diachi" class="form-control" value="' . htmlspecialchars($info['diachi']) . '" required />
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="form-group">
                                                <label for="ghichu">Ghi chú đơn hàng:</label>
                                                <textarea id="ghichu" class="form-control" placeholder="Ghi chú" name="ghichu"></textarea>
                                            </div>
                                        </div>';
                                    } else {
                                        echo '<p class="text-danger">Không tìm thấy thông tin khách hàng.</p>';
                                    }
                                } else {
                                    echo '<p class="text-danger">Vui lòng đăng nhập để tiếp tục.</p>';
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="inner-item">
                            <div class="PhuongThuc">
                                <div class="inner-tt">Phương thức thanh toán</div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pttt" id="pttt_tienmat"
                                        value="Tiền mặt" required checked />
                                    <label class="form-check-label" for="pttt_tienmat">Thanh toán tiền mặt khi nhận
                                        hàng</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pttt" id="pttt_chuyenkhoan"
                                        value="Chuyển khoản" />
                                    <label class="form-check-label" for="pttt_chuyenkhoan">Chuyển khoản ngân
                                        hàng</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tổng tiền -->
                    <div class="col-xl-5 col-lg-5 col-md-6 col-sm-12">
                        <?php
                        if (isset($_SESSION['makh'])) {
                            $makh = $_SESSION['makh'];
                            $ma_gh = $cartModel->getLatestCartId($makh);
                            if ($ma_gh) {
                                $summary = $cartModel->getCartSummary($ma_gh);
                                $tong_mon = $summary['tong_mon'] ?? 0;
                                $tien_so = $summary['tong_tien'] ?? 0;
                                $tong_tien = number_format($tien_so, 0, ',', '.') . "đ";
                                echo '
                                <div class="inner-item">
                                    <div class="inner-tien">
                                        <div class="inner-th">Tiền hàng <span>' . $tong_mon . ' món</span></div>
                                        <div class="inner-st">' . $tong_tien . '</div>
                                        <input type="hidden" name="tongtien" value="' . $tien_so . '" />
                                    </div>
                                    <div class="inner-tien">
                                        <div class="inner-pvc">Phí vận chuyển</div>
                                        <div class="inner-st">0₫</div>
                                    </div>
                                    <div class="inner-tientong">
                                        <div class="inner-tong">Tổng tiền</div>
                                        <div class="inner-total">' . $tong_tien . '</div>
                                    </div>
                                    <button type="submit" class="button" name="thanhtoan">Thanh toán</button>
                                </div>';
                            } else {
                                echo '<p class="text-danger">Giỏ hàng trống.</p>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- XỬ LÝ ĐẶT HÀNG -->
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['thanhtoan'])) {
        if (!isset($_SESSION['makh'])) {
            echo '<script>alert("Vui lòng đăng nhập!"); window.location="login.php";</script>';
            exit;
        }
        $makh = $_SESSION['makh'];
        $diachi = trim($_POST['diachi']);
        $ghichu = trim($_POST['ghichu'] ?? '');
        $pttt = $_POST['pttt'];

        $ma_gh = $cartModel->getLatestCartId($makh);
        $summary = $cartModel->getCartSummary($ma_gh);
        $tong_tien = $summary['tong_tien'] ?? 0;

        if (!$ma_gh || $tong_tien == 0) {
            echo '<script>alert("Giỏ hàng trống!"); history.back();</script>';
            exit;
        }

        $ma_dh = $orderModel->create($makh, $ma_gh, $tong_tien, $diachi, $ghichu, $pttt);
        $orderModel->copyCartDetails($ma_dh, $ma_gh);
        $cartModel->clearCart($ma_gh);

        echo '<script>
            document.getElementById("success-notification").classList.add("show");
            setTimeout(() => { window.location="login.php"; }, 2000);
        </script>';
        exit;
    }
    ?>

    <?php include "includes/footer.php"; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>