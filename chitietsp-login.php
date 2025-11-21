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
</head>

<body>
  <?php include_once "includes/headerlogin.php"; ?>

  <?php
  include "connect.php";

  // Kiểm tra đăng nhập
  if (!isset($_SESSION['makh'])) {
    header("Location: login.php");
    exit;
  }

  $makh = $_SESSION['makh'];
  $masp = isset($_GET['id']) ? intval($_GET['id']) : 0;

  // === LẤY THÔNG TIN SẢN PHẨM ===
  $sql_sp = "SELECT MA_SP, TEN_SP, HINH_ANH, GIA_CA, MO_TA, TINH_TRANG 
           FROM sanpham WHERE MA_SP = ? AND TINH_TRANG = 1";
  $stmt_sp = $conn->prepare($sql_sp);
  $stmt_sp->bind_param("i", $masp);
  $stmt_sp->execute();
  $result_sp = $stmt_sp->get_result();

  if ($result_sp->num_rows == 0) {
    echo "<h2 class='text-center mt-5'>Không tìm thấy sản phẩm!</h2>";
    exit;
  }
  $sp = $result_sp->fetch_assoc();
  ?>

  <div class="chitietSP">
    <div class="container">
      <div class="row">
        <div class="col-xl-9 col-lg-9">
          <div class="row">
            <div class="col-xl-5 col-lg-5">
              <div class="inner-image">
                <div class="inner-img">
                  <img src="<?= htmlspecialchars($sp['HINH_ANH']) ?>" alt="<?= htmlspecialchars($sp['TEN_SP']) ?>" />
                </div>
              </div>
            </div>
            <div class="col-xl-7 col-lg-7">
              <div class="inner-content">
                <div class="inner-ten"><?= htmlspecialchars($sp['TEN_SP']) ?></div>
                <div class="inner-tt">
                  Trạng thái: <span class="inner-conhang"><i class="fa-solid fa-check"></i>Còn món</span>
                </div>
                <form method="post" action="">
                  <div class="inner-gia"><?= number_format($sp['GIA_CA'], 0, ',', '.') ?> ₫</div>
                  <div class="inner-desc"><?= nl2br(htmlspecialchars($sp['MO_TA'])) ?></div>
                  <div class="inner-add">
                    <div class="inner-sl">Số lượng:</div>
                    <div class="inner-tanggiam">
                      <span onclick="giamsoluong()" class="inner-tru">-</span>
                      <input id="tanggiam" type="number" name="soluong" value="1" min="1" class="inner-so" />
                      <span onclick="tangsoluong()" class="inner-cong">+</span>
                    </div>
                    <button type="submit" onclick="thongbao()" class="inner-nut" name="addProduct">
                    Thêm vào giỏ hàng 
                  </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
              <div class="col-xl-12">
                <div class="inner-thongtin">
                  <div class="inner-nut"><button class="inner-mt inner-mt-active">Mô tả</button></div>
                  <div class="inner-mota">
                    <div class="inner-nd"><?= nl2br(htmlspecialchars($sp['MO_TA'])) ?></div>
                  </div>
                </div>
              </div>
        </div>

        <!-- PHẦN DỊCH VỤ -->
        <div class="col-xl-3 col-lg-3">
          <div class="inner-danhmuc">
            <div class="inner-dm">
              <div class="inner-hinh"><img src="assets/img/service_1.webp" /></div>
              <div class="inner-chu">GIAO HÀNG NHANH</div>
            </div>
            <div class="inner-dm">
              <div class="inner-hinh"><img src="assets/img/service_2.png" /></div>
              <div class="inner-chu">HOÀN TIỀN NẾU KHÔNG NGON</div>
            </div>
            <div class="inner-dm">
              <div class="inner-hinh"><img src="assets/img/service_3.webp" /></div>
              <div class="inner-chu">SẢN PHẨM AN TOÀN</div>
            </div>
            <div class="inner-dm">
              <div class="inner-hinh"><img src="assets/img/service_4.webp" /></div>
              <div class="inner-chu">HỖ TRỢ 24/7</div>
            </div>
          </div>

          <!-- SẢN PHẨM NỔI BẬT -->
          <?php
          $sql_noibat = "SELECT MA_SP, TEN_SP, HINH_ANH, GIA_CA FROM sanpham WHERE TINH_TRANG = 1 ORDER BY RAND() LIMIT 4";
          $result_noibat = $conn->query($sql_noibat);
          if ($result_noibat && $result_noibat->num_rows > 0):
            ?>
            <div class="inner-noibat">
              <div class="inner-nb">SẢN PHẨM NỔI BẬT</div>
              <div class="inner-sp">
                <?php while ($item = $result_noibat->fetch_assoc()): ?>
                  <div class="inner-item">
                    <a href="chitietsp-login.php?id=<?= $item['MA_SP'] ?>" class="inner-anh">
                      <img src="<?= htmlspecialchars($item['HINH_ANH']) ?>" alt="<?= htmlspecialchars($item['TEN_SP']) ?>">
                    </a>
                    <div class="inner-mota">
                      <div class="inner-ten"><?= htmlspecialchars($item['TEN_SP']) ?></div>
                      <div class="inner-gia"><?= number_format($item['GIA_CA'], 0, ',', '.') ?> ₫</div>
                    </div>
                  </div>
                <?php endwhile; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- SẢN PHẨM LIÊN QUAN -->
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
        $sql_lienquan = "SELECT MA_SP, TEN_SP, HINH_ANH, GIA_CA FROM sanpham WHERE TINH_TRANG = 1 AND MA_SP != ? ORDER BY RAND() LIMIT 4";
        $stmt_lq = $conn->prepare($sql_lienquan);
        $stmt_lq->bind_param("i", $masp);
        $stmt_lq->execute();
        $result_lq = $stmt_lq->get_result();

        if ($result_lq->num_rows > 0):
          while ($row = $result_lq->fetch_assoc()):
            ?>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12">
              <div class="inner-item">
                <a href="chitietsp-login.php?id=<?= $row['MA_SP'] ?>" class="inner-img">
                  <img src="<?= htmlspecialchars($row['HINH_ANH']) ?>" />
                </a>
                <div class="inner-info">
                  <div class="inner-ten"><?= htmlspecialchars($row['TEN_SP']) ?></div>
                  <div class="inner-gia"><?= number_format($row['GIA_CA'], 0, ',', '.') ?> ₫</div>
                  <a href="chitietsp-login.php?id=<?= $row['MA_SP'] ?>" class="inner-muahang">
                    <i class="fa-solid fa-cart-plus"></i> ĐẶT MÓN
                  </a>
                </div>
              </div>
            </div>
            <?php
          endwhile;
        else:
          echo "<p class='col-12 text-center'>Không có sản phẩm liên quan!</p>";
        endif;
        ?>
      </div>
    </div>
  </div>

  <?php include_once "includes/footer.php"; ?>

  <!-- XỬ LÝ THÊM VÀO GIỎ HÀNG -->
  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addProduct'])) {
    $soluong = max(1, intval($_POST['soluong']));

    // 1. Lấy MA_GH của khách
    $sql_gh = "SELECT MA_GH FROM giohang WHERE MA_KH = ? LIMIT 1";
    $stmt_gh = $conn->prepare($sql_gh);
    $stmt_gh->bind_param("i", $makh);
    $stmt_gh->execute();
    $result_gh = $stmt_gh->get_result();

    if ($result_gh->num_rows == 0) {
      // Tạo giỏ hàng mới
      $insert_gh = "INSERT INTO giohang (MA_KH, TONG_TIEN) VALUES (?, 0)";
      $stmt_new = $conn->prepare($insert_gh);
      $stmt_new->bind_param("i", $makh);
      $stmt_new->execute();
      $ma_gh = $conn->insert_id;
    } else {
      $row = $result_gh->fetch_assoc();
      $ma_gh = $row['MA_GH'];
    }

    // 2. Kiểm tra sản phẩm trong chitietgiohang
    $sql_ct = "SELECT SO_LUONG FROM chitietgiohang WHERE MA_GH = ? AND MA_SP = ?";
    $stmt_ct = $conn->prepare($sql_ct);
    $stmt_ct->bind_param("ii", $ma_gh, $masp);
    $stmt_ct->execute();
    $result_ct = $stmt_ct->get_result();

    if ($result_ct->num_rows > 0) {
      // Cập nhật số lượng
      $row = $result_ct->fetch_assoc();
      $soluong_moi = $row['SO_LUONG'] + $soluong;
      $update_ct = "UPDATE chitietgiohang SET SO_LUONG = ? WHERE MA_GH = ? AND MA_SP = ?";
      $stmt_up = $conn->prepare($update_ct);
      $stmt_up->bind_param("iii", $soluong_moi, $ma_gh, $masp);
      $stmt_up->execute();
    } else {
      // Thêm mới
      $insert_ct = "INSERT INTO chitietgiohang (MA_GH, MA_SP, SO_LUONG) VALUES (?, ?, ?)";
      $stmt_ins = $conn->prepare($insert_ct);
      $stmt_ins->bind_param("iii", $ma_gh, $masp, $soluong);
      $stmt_ins->execute();
    }

    // Cập nhật TONG_TIEN trong giohang
    $sql_tong = "UPDATE giohang gh
                 JOIN chitietgiohang ct ON gh.MA_GH = ct.MA_GH
                 JOIN sanpham sp ON ct.MA_SP = sp.MA_SP
                 SET gh.TONG_TIEN = (SELECT SUM(ct.SO_LUONG * sp.GIA_CA) FROM chitietgiohang ct JOIN sanpham sp ON ct.MA_SP = sp.MA_SP WHERE ct.MA_GH = gh.MA_GH)
                 WHERE gh.MA_GH = ?";
    $stmt_tong = $conn->prepare($sql_tong);
    $stmt_tong->bind_param("i", $ma_gh);
    $stmt_tong->execute();

    $_SESSION['magh'] = $ma_gh;

    echo "<script>window.location='chitietsp-login.php?id=$masp';</script>";

  }
  ?>

  <script>
    function tangsoluong() {
      let input = document.getElementById("tanggiam");
      input.value = parseInt(input.value) + 1;
    }
    function giamsoluong() {
      let input = document.getElementById("tanggiam");
      if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
    }
  </script>
</body>

</html>