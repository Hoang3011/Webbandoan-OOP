<?php
session_start();
include 'connect.php';

// === BẢO MẬT: BẮT BUỘC ĐĂNG NHẬP ===
if (!isset($_SESSION['makh'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập!']);
    exit;
}

$makh = intval($_SESSION['makh']);
$masp = intval($_POST['masp'] ?? 0);
$soluong = intval($_POST['soluong'] ?? 0);

if ($masp <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không hợp lệ!']);
    exit;
}

// === 1. LẤY MA_GH CỦA KHÁCH ===
$sql_gh = "SELECT MA_GH FROM giohang WHERE MA_KH = ? LIMIT 1";
$stmt_gh = $conn->prepare($sql_gh);
$stmt_gh->bind_param("i", $makh);
$stmt_gh->execute();
$result_gh = $stmt_gh->get_result();

if ($result_gh->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy giỏ hàng!']);
    exit;
}

$ma_gh = $result_gh->fetch_assoc()['MA_GH'];

// === 2. XỬ LÝ: CẬP NHẬT HOẶC XÓA ===
if ($soluong <= 0) {
    // XÓA SẢN PHẨM KHỎI GIỎ
    $sql_del = "DELETE FROM chitietgiohang WHERE MA_GH = ? AND MA_SP = ?";
    $stmt_del = $conn->prepare($sql_del);
    $stmt_del->bind_param("ii", $ma_gh, $masp);
    $stmt_del->execute();
    $message = "Đã xóa sản phẩm khỏi giỏ hàng!";
} else {
    // CẬP NHẬT HOẶC THÊM MỚI
    $sql_up = "
        INSERT INTO chitietgiohang (MA_GH, MA_SP, SO_LUONG) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE SO_LUONG = ?
    ";
    $stmt_up = $conn->prepare($sql_up);
    $stmt_up->bind_param("iiii", $ma_gh, $masp, $soluong, $soluong);
    $stmt_up->execute();
    $message = "Cập nhật giỏ hàng thành công!";
}

// === 3. CẬP NHẬT TONG_TIEN ===
$sql_tong = "
    UPDATE giohang gh
    LEFT JOIN (
        SELECT MA_GH, COALESCE(SUM(ct.SO_LUONG * sp.GIA_CA), 0) AS tong
        FROM chitietgiohang ct
        JOIN sanpham sp ON ct.MA_SP = sp.MA_SP
        WHERE ct.MA_GH = ?
        GROUP BY MA_GH
    ) t ON gh.MA_GH = t.MA_GH
    SET gh.TONG_TIEN = COALESCE(t.tong, 0)
    WHERE gh.MA_GH = ?
";
$stmt_tong = $conn->prepare($sql_tong);
$stmt_tong->bind_param("ii", $ma_gh, $ma_gh);
$stmt_tong->execute();

// === 4. TRẢ KẾT QUẢ ===
$tongtien = $conn->query("SELECT TONG_TIEN FROM giohang WHERE MA_GH = $ma_gh")->fetch_assoc()['TONG_TIEN'];

echo json_encode([
    'status' => 'success',
    'message' => $message,
    'tongtien' => number_format($tongtien, 0, ',', '.'),
    'soluong_hien_tai' => $soluong
]);

$conn->close();
?>