<?php
session_start();
include 'connect.php';

// === BẢO MẬT: BẮT BUỘC ĐĂNG NHẬP ===
if (!isset($_SESSION['makh'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng đăng nhập!',
        'discontinued' => []
    ]);
    exit;
}

$makh = intval($_SESSION['makh']);
$response = [
    'status' => 'success',
    'discontinued' => []
];

// === KIỂM TRA SẢN PHẨM NGỪNG KINH DOANH TRONG GIỎ ===
$sql = "
    SELECT sp.TEN_SP
    FROM giohang gh
    JOIN chitietgiohang ct ON gh.MA_GH = ct.MA_GH
    JOIN sanpham sp ON ct.MA_SP = sp.MA_SP
    WHERE gh.MA_KH = ? AND sp.TINH_TRANG = 0
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $makh);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $response['status'] = 'error';
    $response['discontinued'][] = $row['TEN_SP'];
}

$stmt->close();
$conn->close();

// === TRẢ KẾT QUẢ JSON ===
header('Content-Type: application/json');
echo json_encode($response);
?>