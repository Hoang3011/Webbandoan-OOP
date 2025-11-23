<?php
include "connect.php";
require_once "model/GioHang.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
    <link rel="stylesheet" href="assets/font-awesome-pro-v6-6.2.0/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/base.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <title>Đặc sản 3 miền</title>
    <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
    <style>
        .giohang {
            padding-top: 70px;
            padding-bottom: 30px;
        }

        .btn {
            background: var(--color-bg2);
            color: #fff;
        }

        .remove {
            border: 0;
            color: var(--color-bg2);
            background: #fff;
            cursor: pointer;
        }

        .btn-reduce,
        .btn-increment {
            width: 30px;
            height: 30px;
            border: 1px solid #ccc;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
        }

        .button-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .button-quantity input {
            width: 50px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            height: 30px;
        }

        .price {
            font-weight: 500;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .bg-light-gray {
            background-color: #f6f6f6;
        }

        .bg-cream {
            background-color: #f7f4ef;
        }

        .bg-success {
            height: 4px;
        }

        .link_404 {
            display: inline-block;
            padding: 10px 20px;
            background: var(--color-bg2);
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
        }

        .link_404:hover {
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php include "includes/headerlogin.php"; ?>
    <div class="giohang">
        <div class="container">
            <?php
            // Kiểm tra đăng nhập
            if (session_status() === PHP_SESSION_NONE)
                session_start();
            if (!isset($_SESSION['makh'])) {
                header("Location: login.php");
                exit;
            }

            $ma_kh = (int) $_SESSION['makh'];

            // Sử dụng OOP để lấy giỏ hàng
            $gioHangObj = GioHang::getCartByUser($conn, $ma_kh);

            if (!$gioHangObj || $gioHangObj->getTongTien() == 0 || count($gioHangObj->getItems()) == 0) {
                $has_items = false;
            } else {
                $has_items = true;
            }
            ?>

            <?php if (empty($has_items)): ?>
                <!-- ...existing empty cart HTML... -->
            <?php else: ?>
                <h2 class="text-3xl font-semibold text-center mb-5">Giỏ Hàng</h2>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <table class="table mb-0 table-borderless">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($gioHangObj->getItems() as $row):
                                    ?>
                                        <tr data-id="<?= htmlspecialchars($row['MA_SP']) ?>">
                                            <td>
                                                <div class="d-flex align-items-center p-3">
                                                    <div class="w-25 mr-3">
                                                        <img class="img-fluid rounded"
                                                            src="<?= htmlspecialchars($row['Image']) ?>"
                                                            alt="<?= htmlspecialchars($row['Name']) ?>">
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-uppercase mb-1">
                                                            <?= htmlspecialchars($row['Name']) ?>
                                                        </p>
                                                        <span
                                                            class="text-sm"><?= number_format($row['dongia'], 0, ',', '.') ?>đ</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="button-quantity">
                                                    <button type="button" class="btn-reduce">-</button>
                                                    <input type="number" class="qty" value="<?= (int) $row['SO_LUONG'] ?>"
                                                        min="1">
                                                    <button type="button" class="btn-increment">+</button>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="price"><?= number_format($row['tongtien']) ?>đ</div>
                                            </td>
                                            <td>
                                                <button type="button" class="remove p-0">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-cream p-4 mb-4">
                            <h5 class="text-uppercase font-weight-medium text-sm">MIỄN PHÍ VẬN CHUYỂN MỪNG LỄ 30/4 – CHO TẤT
                                CẢ ĐƠN HÀNG </h5>
                            <p class="text-sm mt-2">Chúc mừng! Bạn được miễn phí vận chuyển nhân dịp lễ 30/4!</p>
                            <div class="bg-success w-100 mt-3"></div>
                        </div>
                        <div class="card bg-light-gray p-4">
                            <span>Mã giảm giá</span>
                            <p class="mt- DELTA text-sm text-muted">* Giảm giá sẽ được tính và áp dụng khi thanh toán</p>
                            <input class="form-control h-10 mb-4" placeholder="Coupon code" type="text">
                            <p class="font-weight-bold">Total: <?= number_format($gioHangObj->getTongTien()) ?>đ</p>
                            <form id="checkout-form" action="thanhtoan.php?magh=<?= $gioHangObj->getId() ?>&makh=<?= $gioHangObj->getMaKh() ?>"
                                method="post">
                                <button type="submit" class="btn btn-block mt-4 rounded-pill">Thanh toán</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include "includes/footer.php"; ?>
</html>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            function updateCart(masp, soluong) {
                $.post('capnhat_giohang.php', { masp: masp, soluong: soluong }, function (res) {
                    if (res.status === 'success') {
                        location.reload();
                    }
                }, 'json');
            }

            $('.btn-increment').click(function () {
                let row = $(this).closest('tr');
                let input = row.find('.qty');
                let qty = parseInt(input.val()) || 1;
                qty += 1;
                input.val(qty);
                updateCart(row.data('id'), qty);
            });

            $('.btn-reduce').click(function () {
                let row = $(this).closest('tr');
                let input = row.find('.qty');
                let qty = parseInt(input.val()) || 1;
                if (qty > 1) qty -= 1;
                input.val(qty);
                updateCart(row.data('id'), qty);
            });

            $('.qty').change(function () {
                let row = $(this).closest('tr');
                let qty = parseInt($(this).val());
                if (qty < 1) qty = 1;
                $(this).val(qty);
                updateCart(row.data('id'), qty);
            });

            $('.remove').click(function () {
                let row = $(this).closest('tr');
                updateCart(row.data('id'), 0);
            });

            $('#checkout-form').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: 'checkvisible.php',
                    method: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'error') {
                            let msg = 'Các sản phẩm sau đã ngừng kinh doanh:\n\n';
                            res.discontinued.forEach(item => msg += '- ' + item + '\n');
                            alert(msg);
                        } else {
                            $('#checkout-form').unbind('submit').submit();
                        }
                    },
                    error: function () {
                        alert('Lỗi kiểm tra giỏ hàng!');
                    }
                });
            });
        });
    </script>
</body>

</html>