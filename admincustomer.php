<?php
include 'connect.php';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if ($action == 'lock') {
        $sql = "UPDATE khachhang SET TRANG_THAI = 'Locked' WHERE MA_KH = '$id'";
    } elseif ($action == 'unlock') {
        $sql = "UPDATE khachhang SET TRANG_THAI = 'Active' WHERE MA_KH = '$id'";
    }

    if (isset($sql)) {
        mysqli_query($conn, $sql);
        header("Location: admincustomer.php");
        exit();
    }
}

if (isset($_POST['save_customer'])) {
    $makh = $_POST['makh'];
    $tenkh = $_POST['tenkh'];
    $sodienthoai = $_POST['sodienthoai'];
    $diachi = $_POST['diachi'];
    $matkhau = $_POST['matkhau'];

    $sql = "UPDATE khachhang SET 
            TEN_KH = '$tenkh',
            SO_DIEN_THOAI = '$sodienthoai',
            DIA_CHI = '$diachi',
            MAT_KHAU = '$matkhau'
            WHERE MA_KH = '$makh'";

    mysqli_query($conn, $sql);
    header("Location: admincustomer.php");
    exit();
}
?>
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
    <title>Quản Lý Khách Hàng</title>
    <link href="./assets/img/logo.png" rel="icon" type="image/x-icon" />
  </head>
  <body>
    <?php
    include_once "includes/headeradmin.php";
    if (isset($_POST['addcustomer'])) {
        $tenkh = $_POST['tenkh'];
        $matkhau = $_POST['matkhau'];
        $diachi = $_POST['diachi'];
        $sodienthoai = $_POST['sodienthoai'];
        $ngaytao = date('Y-m-d');  // Set NGAY_TAO to current date

        $sql = "INSERT INTO khachhang(TEN_KH, MAT_KHAU, DIA_CHI, SO_DIEN_THOAI, TRANG_THAI, NGAY_TAO)
                VALUES('$tenkh','$matkhau','$diachi','$sodienthoai', 'Active', '$ngaytao')";
        mysqli_query($conn, $sql);
    }
    ?>
    <div class="admin-customer">
      <div class="admin-control">
        <div class="admin-control-left">
          <select name="tinh-trang-user" id="tinh-trang-user" onchange="showUser()">
            <option value="0">Tất cả</option>
            <option value="1">Hoạt động</option>
            <option value="2">Bị khóa</option>
          </select>
        </div>
        <div class="admin-control-center">
          <form onsubmit="event.preventDefault(); showUser();">
            <span class="search-btn" onclick="showUser()"><i class="fa-light fa-magnifying-glass"></i></span>
            <input id="form-search-product" type="text" class="form-search-input" placeholder="Tìm kiếm khách hàng..." />
          </form>
        </div>
        <div class="admin-control-right">
          <form class="fillter-date">
            <div>
              <label for="time-start">Từ</label>
              <input type="date" class="form-control-date" id="time-start-user" onchange="showUser()" />
            </div>
            <div>
              <label for="time-end">Đến</label>
              <input type="date" class="form-control-date" id="time-end-user" onchange="showUser()" />
            </div>
          </form>
          <a href="admincustomer.php" class="reset-order"><i class="fa-light fa-arrow-rotate-right"></i></a>
          <button class="inner-nut" data-toggle="modal" data-target="#exampleModal">
            <i class="fa-light fa-plus"></i> <span>Thêm khách hàng</span>
          </button>
        </div>
      </div>

      <div class="table">
        <table width="100%">
          <thead>
            <tr>
              <td>STT</td>
              <td>Họ và tên</td>
              <td>Liên hệ</td>
              <td>Mật khẩu</td>
              <td>Tình trạng</td>
              <td></td>
            </tr>
          </thead>
          <tbody id="customer-table-body">
            <?php
            $sql = "SELECT * FROM khachhang";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_array($result)) {
                $modalId = "editModal-" . $row['MA_KH'];
            ?>
            <tr>
              <td><?php echo $row['MA_KH'] ?></td>
              <td><?php echo $row['TEN_KH'] ?></td>
              <td><?php echo $row['SO_DIEN_THOAI'] ?></td>
              <td><?php echo $row['MAT_KHAU'] ?></td>
              <td>
                <?php if ($row['TRANG_THAI'] == 'Locked'): ?>
                  <span class="status-no-complete">Bị khóa</span>
                <?php else: ?>
                  <span class="status-complete">Đang hoạt động</span>
                <?php endif; ?>
              </td>
              <td class="control control-table">
                <a href="#" class="btn-edit" data-toggle="modal" data-target="#<?php echo $modalId ?>">
                  <i class="fa-light fa-pen-to-square"></i>
                </a>
                <?php if ($row['TRANG_THAI'] == 'Locked'): ?>
                  <a href="admincustomer.php?action=unlock&id=<?php echo $row['MA_KH'] ?>" class="btn-delete">
                    <i class="fa-solid fa-lock-open"></i>
                  </a>
                <?php else: ?>
                  <a href="admincustomer.php?action=lock&id=<?php echo $row['MA_KH'] ?>" class="btn-delete">
                    <i class="fa-solid fa-lock"></i>
                  </a>
                <?php endif; ?>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

      <!-- Modal Add Customer -->
      <div class="modal fade modal-form" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <div class="inner-title">THÊM KHÁCH HÀNG MỚI</div>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="admincustomer.php" method="post">
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for="tenkh">Tên đầy đủ</label>
                      <input type="text" name="tenkh" class="form-control" placeholder="VD: Thành Đại" />
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <label for="sodienthoai">Số điện thoại</label>
                      <input type="text" name="sodienthoai" class="form-control" placeholder="Nhập số điện thoại" />
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <label for="diachi">Địa chỉ</label>
                      <input type="text" name="diachi" class="form-control" placeholder="Nhập địa chỉ" />
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <label for="matkhau">Mật khẩu</label>
                      <input type="text" name="matkhau" class="form-control" placeholder="Nhập mật khẩu" />
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="inner-add">
                      <button type="submit" class="inner-nut" name="addcustomer">Đăng ký</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Change Customer -->
      <?php
      $sql = "SELECT * FROM khachhang";
      $result = mysqli_query($conn, $sql);
      while ($row = mysqli_fetch_array($result)) {
          $modalId = "editModal-" . $row['MA_KH'];
      ?>
      <div class="modal fade modal-form" id="<?php echo $modalId ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <div class="inner-title">CHỈNH SỬA THÔNG TIN</div>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="admincustomer.php" method="post">
                <input type="hidden" name="makh" value="<?php echo $row['MA_KH'] ?>">
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for="tenkh">Tên đầy đủ</label>
                      <input type="text" id="tenkh" name="tenkh" class="form-control" value="<?php echo $row['TEN_KH'] ?>" />
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <label for="sodienthoai">Số điện thoại</label>
                      <input type="text" id="sodienthoai" name="sodienthoai" class="form-control" value="<?php echo $row['SO_DIEN_THOAI'] ?>" />
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <label for="diachi">Địa chỉ</label>
                      <input type="text" id="diachi" name="diachi" class="form-control" value="<?php echo $row['DIA_CHI'] ?>" />
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <label for="matkhau">Mật khẩu</label>
                      <input type="text" id="matkhau" name="matkhau" class="form-control" value="<?php echo $row['MAT_KHAU'] ?>" />
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="inner-add">
                      <button type="submit" class="inner-nut" name="save_customer">
                        <i class="fa-regular fa-floppy-disk"></i> Lưu thông tin
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>

    <script src="admin/js/jquery.min.js"></script>
    <script src="admin/js/popper.js"></script>
    <script src="admin/js/bootstrap.min.js"></script>
    <script src="admin/js/main.js"></script>
    <script src="assets/js/admin.js"></script>
    <script>
      // Sample customer data (for initial render and filtering)
      const customers = [
        <?php
        $sql = "SELECT MA_KH, TEN_KH, SO_DIEN_THOAI, MAT_KHAU, DIA_CHI, TRANG_THAI, NGAY_TAO FROM khachhang";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_array($result)) {
            echo "{
                makh: '" . $row['MA_KH'] . "',
                tenkh: '" . addslashes($row['TEN_KH']) . "',
                sodienthoai: '" . $row['SO_DIEN_THOAI'] . "',
                matkhau: '" . $row['MAT_KHAU'] . "',
                diachi: '" . addslashes($row['DIA_CHI']) . "',
                trangthai: '" . $row['TRANG_THAI'] . "',
                ngaytao: '" . $row['NGAY_TAO'] . "'
            },";
        }
        ?>
      ];

      function showUser() {
        const searchQuery = document.getElementById("form-search-product").value.toLowerCase();
        const statusFilter = document.getElementById("tinh-trang-user").value;
        const startDate = document.getElementById("time-start-user").value;
        const endDate = document.getElementById("time-end-user").value;

        // Filter customers
        let filteredCustomers = customers.filter(customer => {
          // Search filter (match makh, tenkh, or sodienthoai)
          const matchesSearch = (
            customer.makh.toLowerCase().includes(searchQuery) ||
            customer.tenkh.toLowerCase().includes(searchQuery) ||
            customer.sodienthoai.toLowerCase().includes(searchQuery)
          );

          // Status filter
          const matchesStatus = (
            statusFilter === "0" ||
            (statusFilter === "1" && customer.trangthai === "Active") ||
            (statusFilter === "2" && customer.trangthai === "Locked")
          );

          // Date filter (using NGAY_TAO)
          const customerDate = new Date(customer.ngaytao);
          const start = startDate ? new Date(startDate) : null;
          const end = endDate ? new Date(endDate) : null;
          const matchesDate = (
            (!start || customerDate >= start) &&
            (!end || customerDate <= end)
          );

          // Return true only if all filters match
          return matchesSearch && matchesStatus && matchesDate;
        });

        // Render table
        const tbody = document.getElementById("customer-table-body");
        tbody.innerHTML = "";
        if (filteredCustomers.length === 0) {
          tbody.innerHTML = '<tr><td colspan="6">Không tìm thấy khách hàng.</td></tr>';
        } else {
          filteredCustomers.forEach(customer => {
            const statusLabel = customer.trangthai === "Locked" ? "Bị khóa" : "Đang hoạt động";
            const statusClass = customer.trangthai === "Locked" ? "status-no-complete" : "status-complete";
            const actionIcon = customer.trangthai === "Locked" ? "fa-solid fa-lock-open" : "fa-solid fa-lock";
            const actionLink = customer.trangthai === "Locked" ? `admincustomer.php?action=unlock&id=${customer.makh}` : `admincustomer.php?action=lock&id=${customer.makh}`;
            const row = `
              <tr>
                <td>${customer.makh}</td>
                <td>${customer.tenkh}</td>
                <td>${customer.sodienthoai}</td>
                <td>${customer.matkhau}</td>
                <td><span class="${statusClass}">${statusLabel}</span></td>
                <td class="control control-table">
                  <a href="#" class="btn-edit" data-toggle="modal" data-target="#editModal-${customer.makh}">
                    <i class="fa-light fa-pen-to-square"></i>
                  </a>
                  <a href="${actionLink}" class="btn-delete">
                    <i class="${actionIcon}"></i>
                  </a>
                </td>
              </tr>
            `;
            tbody.innerHTML += row;
          });
        }
      }

      // Initial render
      showUser();
    </script>
  </body>
</html>