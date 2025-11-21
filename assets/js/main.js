// Open Search Advanced
document.querySelector(".filter-btn").addEventListener("click", (e) => {
  e.preventDefault();
  document.querySelector(".advanced-search").classList.toggle("open");
  document.getElementById("home-service").scrollIntoView();
});

document.querySelector(".form-search-input").addEventListener("click", (e) => {
  e.preventDefault();
  document.getElementById("home-service").scrollIntoView();
});

function closeSearchAdvanced() {
  document.querySelector(".advanced-search").classList.toggle("open");
}

window.addEventListener("DOMContentLoaded", () => {
  if (window.location.hash === "#scroll") {
    const section = document.getElementById("home-service");
    if (section) {
      section.scrollIntoView({ behavior: "smooth" });
    }
  }
});

document.querySelectorAll(".inner-img a").forEach(function (item) {
  item.addEventListener("click", function (event) {
    event.preventDefault();
  });
});

function dangKi() {
  alert("Đăng kí thành công");
}

function thanhToan() {
  alert("Món ăn đã được thêm vào giỏ hàng !");
}

let lastScrollY = window.scrollY;
const headerBottom = document.querySelector(".header-bottom");

window.addEventListener("scroll", () => {
  if (window.scrollY > lastScrollY) {
    headerBottom.classList.add("hide");
    headerBottom.classList.remove("show");
  } else {
    headerBottom.classList.add("show");
    headerBottom.classList.remove("hide");
  }
  lastScrollY = window.scrollY;
});

function capNhat() {
  alert("Cập nhật thông tin thành công !");
}

function thayDoi() {
  alert("Đổi mật khẩu thành công !");
}

function tinhTien() {
  alert("Thanh toán thành công !");
}

function notLogin() {
  alert("Chưa đăng nhập tài khoản !");
}

document.addEventListener("DOMContentLoaded", function () {
  // Gán sự kiện mở/tắt bộ lọc
  let toggleFilterBtn = document.getElementById("toggle-filter-btn");
  let advancedSearch = document.getElementById("advanced-search"); // Sửa lỗi querySelector

  if (toggleFilterBtn && advancedSearch) {
    toggleFilterBtn.addEventListener("click", function () {
      advancedSearch.classList.toggle("active");
    });
  }

  // Gán sự kiện khi nhấn nút tìm kiếm
  let searchBtn = document.getElementById("advanced-search-price-btn");
  if (searchBtn) {
    searchBtn.addEventListener("click", function () {
      searchProducts();
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  // Lấy tất cả các nút accordion
  const accordionButtons = document.querySelectorAll(".PhuongThuc .btn");

  accordionButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Xóa lớp grayscale khỏi tất cả các nút
      accordionButtons.forEach((btn) => btn.classList.remove("grayscale"));

      // Áp dụng lớp grayscale cho tất cả các nút trừ nút đang được nhấn
      accordionButtons.forEach((btn) => {
        if (btn !== this) {
          btn.classList.add("grayscale");
        }
      });
    });
  });
});

function searchProducts(sortOrder = 0, page = 1) {
  let name = document.getElementById("search-input")?.value.trim() || "";
  let category =
    document.getElementById("advanced-search-category-select")?.value || "";
  let minPrice = document.getElementById("min-price")?.value;
  let maxPrice = document.getElementById("max-price")?.value;

  minPrice = minPrice ? parseFloat(minPrice) : null;
  maxPrice = maxPrice ? parseFloat(maxPrice) : null;

  let formData = new FormData();
  formData.append("name", name);
  formData.append("category", category);
  if (minPrice !== null) formData.append("min_price", minPrice);
  if (maxPrice !== null) formData.append("max_price", maxPrice);
  formData.append("sort_order", sortOrder);
  formData.append("page", page);

  fetch("includes/search.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      let resultContainer = document.getElementById("product-list");
      let paginationContainer = document.getElementById("pagination");

      resultContainer.innerHTML = "";
      paginationContainer.innerHTML = "";

      if (!Array.isArray(data.products) || data.products.length === 0) {
        resultContainer.innerHTML = `<div class="home-products" id="home-products"><div class="no-result"><div class="no-result-h">Tìm kiếm không có kết quả</div><div class="no-result-p">Xin lỗi, chúng tôi không thể tìm được kết quả hợp với tìm kiếm của bạn</div><div class="no-result-i"><i class="fa-light fa-face-sad-cry"></i></div></div></div>`;
        return;
      }

      let productsHTML = `
    <div class="container">
      <div class="row">
        <div class="col-xl-12">
          <div class="inner-title">Kết quả tìm kiếm</div>
        </div>`;

      productsHTML += data.products
        .map(
          (product) => `
        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12">
          <div class="inner-item">
            <a href="chitietsp-login.php?id=${product.ID}" class="inner-img">
              <img src="${product.Image}" alt="${product.Name}" />
            </a>
            <div class="inner-info">
              <div class="inner-ten">${product.Name}</div>
              <div class="inner-gia">${product.Price}.000 ₫</div>
              <a href="chitietsp-login.php?id=${product.ID}" class="inner-muahang">
                <i class="fa-solid fa-cart-plus"></i> ĐẶT MÓN
              </a>
            </div>
          </div>
        </div>
      `
        )
        .join("");

      productsHTML += `</div></div>`;
      resultContainer.innerHTML = productsHTML;

      // Cập nhật phân trang
      let totalPages = data.total_pages;
      if (totalPages > 1) {
        let paginationHTML = "";
        for (let i = 1; i <= totalPages; i++) {
          let activeClass = i === page ? "trang-chinh" : "";
          paginationHTML += `<li><a href="#" onclick="searchProducts(${sortOrder}, ${i})" class="inner-trang ${activeClass}">${i}</a></li>`;
        }
        paginationContainer.innerHTML = `<ul>${paginationHTML}</ul>`;
      }
    })
    .catch((error) => console.error("Lỗi khi tìm kiếm sản phẩm:", error));
}

function dangNhap(event) {
  event.preventDefault(); // Ngăn chặn form submit bình thường

  // Lấy giá trị từ form
  var sdt = document.getElementById("sdt").value;
  var password = document.getElementById("mk").value;
  var errorDiv = document.getElementById("loginError");

  // Gửi dữ liệu bằng AJAX
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      // Xử lý phản hồi từ server
      if (xhr.responseText.includes("window.location.href")) {
        // Đăng nhập thành công
        eval(xhr.responseText);
      } else {
        // Hiển thị lỗi
        errorDiv.innerHTML = xhr.responseText;
        errorDiv.style.display = "block";
      }
    }
  };

  xhr.send(
    "dangnhap=true&sdt=" +
      encodeURIComponent(sdt) +
      "&password=" +
      encodeURIComponent(password)
  );

  return false;
}
