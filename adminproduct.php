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
    include_once "includes/headeradmin.php"; 
    require_once "model/MonAn.php";
    ?>

<div class="admin-product">
  <div class="admin-control">
    <div class="admin-control-left">
      <select name="the-loai" id="the-loai" onchange="fetchProducts()">
        <option value="">Tất cả</option>
        <option value="L001">Món chay</option>
        <option value="L002">Món mặn</option>
        <option value="L003">Món lẩu</option>
        <option value="L004">Món ăn vặt</option>
        <option value="L005">Món tráng miệng</option>
        <option value="L006">Nước uống</option>
        <option value="L007">Hải sản</option>
      </select>
    </div>
    <div class="admin-control-center">
      <form onsubmit="fetchProducts(); return false;">
        <span onclick="fetchProducts()" class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
        <input id="form-search-product" type="text" class="form-search-input" placeholder="Tìm kiếm tên món..." />
      </form>
    </div>
    <div class="admin-control-right">
      <a href="adminproduct.php" class="inner-nut"><i class="fa-light fa-rotate-right"></i> Làm mới</a>
      <a href="adminaddproduct.php" class="inner-nut"><i class="fa-light fa-plus"></i> Thêm món mới</a>
    </div>
  </div>

  <div id="product-results" style="display:none;"></div>

  <div id="default-product-list" class="show-product">
    <div class="row">
      <?php
      include "./connect.php";
      $limit = 12;
      $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
      $start = ($page - 1) * $limit;

      $total_query = "SELECT COUNT(*) AS total FROM sanpham";
      $total_result = mysqli_query($conn, $total_query);
      $total_row = mysqli_fetch_assoc($total_result);
      $total_products = $total_row['total'];
      $total_pages = ceil($total_products / $limit);

      // join để lấy tên loại sản phẩm
      $sql = "SELECT sanpham.*, loaisp.TEN_LOAISP 
              FROM sanpham 
              JOIN loaisp ON sanpham.MA_LOAISP = loaisp.MA_LOAISP
              LIMIT $start, $limit";
      $result = mysqli_query($conn, $sql);

      while ($row = mysqli_fetch_assoc($result)) {
        if ($row['TINH_TRANG'] == -1) continue; // bỏ qua sản phẩm bị xóa
        $monAn = new MonAn(
            $row['MA_SP'],
            $row['TEN_SP'],
            $row['HINH_ANH'],
            $row['GIA_CA'],
            $row['MO_TA'],
            $row['MA_LOAISP'],
            $row['TINH_TRANG']
        );
        $borderStyle = ($monAn->getTinhTrang() == 0) ? 'style="border: 1px solid red;"' : '';
        $formattedPrice = number_format($monAn->getGiaCa(), 0, ',', '.') . 'đ';
      ?>
      <div class="col-12">
        <div class="list" data-id="<?= $monAn->getId(); ?>" <?= $borderStyle; ?>>
          <div class="list-left">
            <img src="<?= $monAn->getHinhAnh(); ?>" alt="<?= htmlspecialchars($monAn->getTen()); ?>" />
            <div class="list-info">
              <h4><?= htmlspecialchars($monAn->getTen()); ?></h4>
              <p><?= htmlspecialchars($monAn->getMoTa()); ?></p>
              <div class="list-category"><?= htmlspecialchars($row['TEN_LOAISP']); ?></div>
            </div>
          </div>
          <div class="list-right">
            <div class="list-price"><?= $formattedPrice; ?></div>
            <div class="list-control">
              <div class="list-tool">
                <a href="adminchangeproduct.php?id=<?= $monAn->getId(); ?>" class="btn-edit">
                  <i class="fa-light fa-pen-to-square"></i>
                </a>
                <button class="btn-delete" onclick="confirmDelete(<?= $monAn->getId(); ?>)">
                  <i class="fa-regular fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="Pagination">
      <div class="container">
        <ul>
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li><a href="?page=<?= $i; ?>" class="inner-trang <?= $i == $page ? 'trang-chinh' : ''; ?>"><?= $i; ?></a></li>
          <?php endfor; ?>
        </ul>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

    <script src="admin/js/jquery.min.js"></script>
    <script src="admin/js/bootstrap.min.js"></script>
    <script src="admin/js/main.js"></script>
    <script src="admin/js/popper.js"></script>
    <script src="assets/js/admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // Format giá tiền
  function formatPrice(price) {
    return parseInt(price).toLocaleString('vi-VN') + 'đ';
  }

  function fetchProducts() {
    fetchProductsPage(1);
  }

  function fetchProductsPage(page) {
    var category = document.getElementById("the-loai").value;
    var searchTerm = document.getElementById("form-search-product").value;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "./includes/searchadmin.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        try {
          var response = JSON.parse(xhr.responseText);
          var products = response.products || [];
          var total_pages = response.total_pages || 1;

          var html = '<div class="row">';
          if (products.length === 0) {
            html += '<p>Không tìm thấy sản phẩm nào.</p>';
          } else {
            products.forEach(function (product) {
              // Bỏ qua nếu đã xóa hoàn toàn
              if (product.TINH_TRANG == -1) return;

              var formattedPrice = formatPrice(product.GIA_BAN);
              html += `
                <div class="col-12">
                  <div class="list" style="${product.TINH_TRANG == 0 ? 'border: 1px solid red;' : ''}" data-id="${product.MA_SP}">
                    <div class="list-left">
                      <img src="${product.HINH_ANH}" alt="${product.TEN_SP}" />
                      <div class="list-info">
                        <h4>${product.TEN_SP}</h4>
                        <p>${product.MO_TA}</p>
                        <div class="list-category">${product.LOAI}</div>
                      </div>
                    </div>
                    <div class="list-right">
                      <div class="list-price">${formattedPrice}</div>
                      <div class="list-control">
                        <div class="list-tool">
                          <a href="adminchangeproduct.php?id=${product.MA_SP}" class="btn-edit">
                            <i class="fa-light fa-pen-to-square"></i>
                          </a>
                          <button class="btn-delete" onclick="confirmDelete(${product.MA_SP})">
                            <i class="fa-regular fa-trash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              `;
            });
          }
          html += '</div>';

          if (total_pages > 1) {
            html += '<div class="Pagination"><div class="container"><ul>';
            for (var i = 1; i <= total_pages; i++) {
              var activeClass = i === page ? 'trang-chinh' : '';
              html += `<li><a href="#" class="inner-trang ${activeClass}" onclick="fetchProductsPage(${i}); return false;">${i}</a></li>`;
            }
            html += '</ul></div></div>';
          }

          document.getElementById("product-results").innerHTML = html;
          document.getElementById("product-results").style.display = "block";
          document.getElementById("default-product-list").style.display = "none";
        } catch (e) {
          console.error("Error parsing JSON:", e);
          document.getElementById("product-results").innerHTML = "<p>Lỗi khi tải sản phẩm.</p>";
          document.getElementById("product-results").style.display = "block";
          document.getElementById("default-product-list").style.display = "none";
        }
      }
    };

    xhr.send("category=" + encodeURIComponent(category) + "&search=" + encodeURIComponent(searchTerm) + "&page=" + page);
  }

  // Reset tìm kiếm
  function resetSearch() {
    document.getElementById("form-search-product").value = "";
    document.getElementById("the-loai").value = "";
    document.getElementById("product-results").innerHTML = "";
    document.getElementById("product-results").style.display = "none";
    document.getElementById("default-product-list").style.display = "block";
  }

  // Xác nhận ẩn / xóa sản phẩm
  function confirmDelete(productId) {
    var xhrCheck = new XMLHttpRequest();
    xhrCheck.open("POST", "check_visible.php", true);
    xhrCheck.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhrCheck.onreadystatechange = function () {
      if (xhrCheck.readyState == 4 && xhrCheck.status == 200) {
        var response = xhrCheck.responseText.trim();

        if (response === "visible") {
          // Trường hợp TINH_TRANG = 1 → ẩn
          Swal.fire({
            title: "Bạn có muốn ẩn món ăn?",
            text: "Món ăn này sẽ bị ẩn khỏi hệ thống!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ẩn",
            cancelButtonText: "Hủy",
          }).then((result) => {
            if (result.isConfirmed) {
              var xhr = new XMLHttpRequest();
              xhr.open("POST", "deleteproduct.php", true);
              xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
              xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200 && xhr.responseText.trim() === "success") {
                  Swal.fire("Đã ẩn!", "Món ăn đã được ẩn thành công.", "success").then(() => {
                    const productElement = document.querySelector(`.list[data-id="${productId}"]`);
                    if (productElement) productElement.style.border = "1px solid red";
                  });
                } else if (xhr.readyState == 4) {
                  Swal.fire("Lỗi!", "Không thể ẩn món ăn: " + xhr.responseText, "error");
                }
              };
              xhr.send("id=" + productId + "&action=hide");
            }
          });
        } else if (response === "hidden") {
          // Trường hợp TINH_TRANG = 0 → xóa vĩnh viễn
          Swal.fire({
            title: "Bạn có chắc chắn?",
            text: "Món ăn này sẽ bị xóa vĩnh viễn!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Xóa",
            cancelButtonText: "Hủy",
          }).then((result) => {
            if (result.isConfirmed) {
              var xhr = new XMLHttpRequest();
              xhr.open("POST", "deleteproduct.php", true);
              xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
              xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200 && xhr.responseText.trim() === "success") {
                  Swal.fire("Đã xóa!", "Món ăn đã được xóa thành công.", "success").then(() => {
                    document.querySelector(`.list[data-id="${productId}"]`)?.remove();
                  });
                } else if (xhr.readyState == 4) {
                  Swal.fire("Lỗi!", "Không thể xóa món ăn: " + xhr.responseText, "error");
                }
              };
              xhr.send("id=" + productId + "&action=delete");
            }
          });
        } else {
          Swal.fire("Lỗi!", "Không thể kiểm tra trạng thái món ăn: " + response, "error");
        }
      }
    };

    xhrCheck.send("id=" + productId);
  }
</script>

  </body>
</html>