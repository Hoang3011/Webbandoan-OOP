<?php
header("Content-Type: application/json; charset=utf-8");
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

include "../connect.php";
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Kết nối thất bại: " . $conn->connect_error]));
}

$search = isset($_POST["search"]) ? trim($_POST["search"]) : '';
$category = isset($_POST["category"]) ? trim($_POST["category"]) : '';
$page = isset($_POST["page"]) ? (int)$_POST["page"] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// ✅ JOIN loaisp để lấy tên loại
$sql = "SELECT sanpham.*, loaisp.TEN_LOAISP 
        FROM sanpham 
        JOIN loaisp ON sanpham.MA_LOAISP = loaisp.MA_LOAISP 
        WHERE sanpham.TINH_TRANG != -1";

$conditions = [];
$params = [];
$types = "";

// Tìm kiếm theo tên
if (!empty($search)) {
    $conditions[] = "sanpham.TEN_SP LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

// Lọc theo loại
if (!empty($category) && $category !== "Tất cả") {
    $conditions[] = "sanpham.MA_LOAISP = ?";
    $params[] = $category;
    $types .= "s";
}

// Gắn điều kiện
if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

// Đếm tổng số sản phẩm
$total_sql = str_replace("SELECT sanpham.*, loaisp.TEN_LOAISP", "SELECT COUNT(*) AS total", $sql);
$total_stmt = $conn->prepare($total_sql);
if (!empty($params)) $total_stmt->bind_param($types, ...$params);
$total_stmt->execute();
$total_products = $total_stmt->get_result()->fetch_assoc()["total"];
$total_pages = ceil($total_products / $limit);
$total_stmt->close();

// Thêm phân trang
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

// Thực thi truy vấn chính
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "Lỗi truy vấn: " . $conn->error]));
}
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = [
        "MA_SP" => $row["MA_SP"],
        "TEN_SP" => $row["TEN_SP"],
        "MO_TA" => $row["MO_TA"],
        "LOAI" => $row["TEN_LOAISP"], // ✅ giờ có dữ liệu đúng
        "HINH_ANH" => "http://localhost/Webbandoan2/" . str_replace("\\", "/", $row["HINH_ANH"]),
        "TINH_TRANG" => (int)$row["TINH_TRANG"],
        "GIA_BAN" => (int)$row["GIA_CA"],
    ];
}

echo json_encode([
    "products" => $products,
    "total_pages" => $total_pages
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$stmt->close();
$conn->close();
?>
