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
  <?php
    include_once "includes/header.php"; 
    ?>
    <!-- ChiTietSP -->
    <?php
include_once "includes/header.php";
include "connect.php";

// Kiểm tra có ID sản phẩm không
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Truy vấn sản phẩm theo MA_SP
    $sql = "SELECT sp.MA_SP, sp.TEN_SP, sp.HINH_ANH, sp.GIA_CA, sp.MO_TA, sp.MA_LOAISP, lsp.TEN_LOAISP
            FROM sanpham sp
            JOIN loaisp lsp ON sp.MA_LOAISP = lsp.MA_LOAISP
            WHERE sp.MA_SP = ? AND sp.TINH_TRANG = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<h2>Không tìm thấy sản phẩm!</h2>";
        exit();
    }
} else {
    echo "<h2>Không có sản phẩm nào được chọn!</h2>";
    exit();
}
?>

<div class="chitietSP">
  <div class="container">
    <div class="row">
      <div class="col-xl-9 col-lg-9">
        <div class="row">
          <div class="col-xl-5 col-lg-5">
            <div class="inner-image">
              <div class="inner-img">
                <img src="<?php echo htmlspecialchars($row['HINH_ANH']); ?>" alt="<?php echo htmlspecialchars($row['TEN_SP']); ?>" />
              </div>
            </div>
          </div>

          <div class="col-xl-7 col-lg-7">
            <div class="inner-content">
              <div class="inner-ten"><?php echo htmlspecialchars($row['TEN_SP']); ?></div>
              <div class="inner-tt">
                Trạng thái:
                <span class="inner-conhang">
                  <i class="fa-solid fa-check"></i> Còn món
                </span>
              </div>
              <div class="inner-gia"><?php echo number_format($row['GIA_CA'], 0, ',', '.'); ?>₫</div>
              <div class="inner-desc">
                <?php echo htmlspecialchars($row['MO_TA']); ?>
              </div>
            </div>

            <div class="inner-add">
              <div class="inner-sl">Số lượng:</div>
              <div class="inner-tanggiam">
                <span id="giam" onclick="giamsoluong()" class="inner-tru">-</span>
                <input id="tanggiam" type="text" value="1" class="inner-so" name="soluong" />
                <span id="tang" onclick="tangsoluong()" class="inner-cong">+</span>
              </div>
              <button type="button" onclick="notLogin()" class="inner-nut">
                Thêm vào giỏ hàng
              </button>
            </div>
          </div>

          <div class="col-xl-12">
            <div class="inner-thongtin">
              <div class="inner-nut">
                <button class="inner-mt inner-mt-active">Mô tả</button>
              </div>
              <div class="inner-mota">
                <div class="inner-nd">
                  <?php echo htmlspecialchars($row['MO_TA']); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-xl-3 col-lg-3">
        <div class="inner-danhmuc">
          <div class="inner-dm"><div class="inner-hinh"><img src="assets/img/service_1.webp" /></div><div class="inner-chu">GIAO HÀNG NHANH</div></div>
          <div class="inner-dm"><div class="inner-hinh"><img src="assets/img/service_2.png" /></div><div class="inner-chu">HOÀN TIỀN NẾU KHÔNG NGON</div></div>
          <div class="inner-dm"><div class="inner-hinh"><img src="assets/img/service_3.webp" /></div><div class="inner-chu">SẢN PHẨM AN TOÀN</div></div>
          <div class="inner-dm"><div class="inner-hinh"><img src="assets/img/service_4.webp" /></div><div class="inner-chu">HỖ TRỢ 24/7</div></div>
        </div>

        <?php
        // Sản phẩm nổi bật (chọn ngẫu nhiên từ sanpham có TINH_TRANG = 1)
        $sql_noibat = "SELECT MA_SP, TEN_SP, HINH_ANH, GIA_CA 
                       FROM sanpham WHERE TINH_TRANG = 1 
                       ORDER BY RAND() LIMIT 4";
        $res_noibat = $conn->query($sql_noibat);
        if ($res_noibat && $res_noibat->num_rows > 0) {
            echo "<div class='inner-noibat'><div class='inner-nb'>SẢN PHẨM NỔI BẬT</div><div class='inner-sp'>";
            while ($sp = $res_noibat->fetch_assoc()) {
                echo "<div class='inner-item'>
                        <a href='chitietsp.php?id={$sp['MA_SP']}' class='inner-anh'>
                          <img src='" . htmlspecialchars($sp['HINH_ANH']) . "' alt='" . htmlspecialchars($sp['TEN_SP']) . "'>
                        </a>
                        <div class='inner-mota'>
                          <div class='inner-ten'>" . htmlspecialchars($sp['TEN_SP']) . "</div>
                          <div class='inner-gia'>" . number_format($sp['GIA_CA'], 0, ',', '.') . " ₫</div>
                        </div>
                      </div>";
            }
            echo "</div></div>";
        }
        ?>
      </div>
    </div>
  </div>
</div>

<!-- Sản phẩm liên quan -->
<div class="SanPham">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="inner-head">
          <div class="inner-title">Sản phẩm liên quan</div>
          <p class="inner-desc">Có phải bạn đang tìm những sản phẩm dưới đây</p>
        </div>
      </div>

      <?php
      // Lấy 4 sản phẩm cùng loại, khác mã sản phẩm hiện tại
      $sql_lienquan = "SELECT MA_SP, TEN_SP, HINH_ANH, GIA_CA 
                       FROM sanpham 
                       WHERE TINH_TRANG = 1 AND MA_LOAISP = ? AND MA_SP != ? 
                       ORDER BY RAND() LIMIT 4";
      $stmt_lq = $conn->prepare($sql_lienquan);
      $stmt_lq->bind_param("si", $row['MA_LOAISP'], $id);
      $stmt_lq->execute();
      $res_lq = $stmt_lq->get_result();

      if ($res_lq->num_rows > 0) {
          while ($sp = $res_lq->fetch_assoc()) {
              echo '
              <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12">
                  <div class="inner-item">
                      <a href="chitietsp.php?id='.$sp['MA_SP'].'" class="inner-img">
                          <img src="'.htmlspecialchars($sp['HINH_ANH']).'" />
                      </a>
                      <div class="inner-info">
                          <div class="inner-ten">'.htmlspecialchars($sp['TEN_SP']).'</div>
                          <div class="inner-gia">'.number_format($sp['GIA_CA'], 0, ',', '.').' ₫</div>
                          <a href="chitietsp.php?id='.$sp['MA_SP'].'" class="inner-muahang">
                              <i class="fa-solid fa-cart-plus"></i> ĐẶT MÓN
                          </a>
                      </div>
                  </div>
              </div>';
          }
      } else {
          echo "<p>Không có sản phẩm liên quan.</p>";
      }
      ?>
    </div>
  </div>
</div>

<?php include_once "includes/footer.php"; ?>

<script>
function tangsoluong() {
  let input = document.getElementById("tanggiam");
  input.value = parseInt(input.value) + 1;
}
function giamsoluong() {
  let input = document.getElementById("tanggiam");
  if (parseInt(input.value) > 1) {
    input.value = parseInt(input.value) - 1;
  }
}
</script>

</html>