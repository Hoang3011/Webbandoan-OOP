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

// Nhận dữ liệu từ request
$name = isset($_POST["name"]) ? trim($_POST["name"]) : '';
$category = isset($_POST["category"]) ? trim($_POST["category"]) : '';
$min_price_raw = isset($_POST["min_price"]) ? $_POST["min_price"] : '';
$max_price_raw = isset($_POST["max_price"]) ? $_POST["max_price"] : '';

// Xử lý định dạng: loại bỏ dấu . và 3 số 0 cuối, sau đó ép kiểu số
function clean_price($raw_price) {
    $numeric = str_replace('.', '', $raw_price); // Bỏ dấu chấm
    $trimmed = preg_replace('/000$/', '', $numeric); // Bỏ 3 số 0 cuối nếu có
    return is_numeric($trimmed) ? (int)$trimmed : null;
}

$min_price = clean_price($min_price_raw);
$max_price = clean_price($max_price_raw);
$sort_order = filter_input(INPUT_POST, "sort_order", FILTER_VALIDATE_INT);
$page = isset($_POST["page"]) ? (int)$_POST["page"] : 1;
$limit = 12; // Số sản phẩm mỗi trang
$offset = ($page - 1) * $limit;

// Chuẩn bị truy vấn SQL
$sql = "SELECT * FROM sanpham";
$conditions = [];
$params = [];
$types = "";

// Tìm kiếm theo tên
if (!empty($name)) {
    $conditions[] = "(Name LIKE ?)";
    $params[] = "%$name%";
    $types .= "s";
}

// Lọc theo danh mục
if (!empty($category) && $category !== "Tất cả") {
    $conditions[] = "Type = ?";
    $params[] = $category;
    $types .= "s";
}

// Lọc theo giá
if ($min_price !== false && $min_price !== null) {
    $conditions[] = "Price >= ?";
    $params[] = $min_price;
    $types .= "d";
}
if ($max_price !== false && $max_price !== null) {
    $conditions[] = "Price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

// Ghép điều kiện vào SQL
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Sắp xếp theo giá
if ($sort_order == 1) {
    $sql .= " ORDER BY price ASC";
} elseif ($sort_order == 2) {
    $sql .= " ORDER BY price DESC";
}

// Tính tổng số sản phẩm
$total_sql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
$total_stmt = $conn->prepare($total_sql);
if (!empty($params)) {
    $total_stmt->bind_param($types, ...$params);
}
$total_stmt->execute();
$total_result = $total_stmt->get_result()->fetch_assoc();
$total_products = $total_result['total'];
$total_pages = ceil($total_products / $limit);
$total_stmt->close();

// Thêm phân trang
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

// Chuẩn bị và thực thi truy vấn
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "Lỗi truy vấn: " . $conn->error]));
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$products = [];

while ($row = $result->fetch_assoc()) {
    $row["Image"] = "http://localhost/Webbandoan2/" . str_replace("\\", "/", $row["Image"]);
    $products[] = $row;
}

// Trả về JSON hợp lệ
echo json_encode([
    "products" => $products,
    "total_pages" => $total_pages
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$stmt->close();
$conn->close();
?>
