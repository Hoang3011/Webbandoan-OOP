<?php

class ThongKe {
    private $conn; // Kết nối mysqli đến DB

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function soLuongSanPham() {
    $sql = "SELECT COUNT(*) AS total FROM sanpham";
    $result = $this->conn->query($sql);
    $row = $result->fetch_assoc();
    return (int)$row['total'];
}

    // Tính tổng doanh thu
    public function tongDoanhThu($startDate = null, $endDate = null) {
        $sql = "SELECT SUM(TONG_TIEN) AS total FROM donhang WHERE TINH_TRANG = 'Đã giao thành công'";
        if ($startDate || $endDate) {
            if ($startDate) {
                $sql .= " AND NGAY_TAO >= ?";
            }
            if ($endDate) {
                $sql .= " AND NGAY_TAO <= ?";
            }
            $stmt = $this->conn->prepare($sql);
            if ($startDate && $endDate) {
                $stmt->bind_param("ss", $startDate, $endDate);
            } elseif ($startDate) {
                $stmt->bind_param("s", $startDate);
            } elseif ($endDate) {
                $stmt->bind_param("s", $endDate);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc()['total'] ?? 0;
        } else {
            $result = $this->conn->query($sql);
            return $result->fetch_assoc()['total'] ?? 0;
        }
    }

    // Số lượng khách hàng
    public function soLuongKhachHang() {
        $sql = "SELECT COUNT(*) AS total FROM khachhang";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // Thống kê đơn hàng theo trạng thái
    public function thongKeDonHang($tinhTrang) {
        $sql = "SELECT COUNT(*) AS total FROM donhang WHERE TINH_TRANG = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $tinhTrang);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // Thêm vào cuối class ThongKe, trước dấu }
public function thongKeKhachHangDoanhThu($options = []) {
    $search     = $options['search'] ?? '';
    $start_date = $options['start_date'] ?? '';
    $end_date   = $options['end_date'] ?? '';
    $sort       = $options['sort'] ?? 'DESC'; // ASC hoặc DESC

    $sql = "
        SELECT 
            k.MA_KH AS customerId, 
            k.TEN_KH AS customerName, 
            COUNT(DISTINCT d.MA_DH) AS orderCount, 
            COALESCE(SUM(d.TONG_TIEN), 0) AS total
        FROM khachhang k
        LEFT JOIN donhang d ON k.MA_KH = d.MA_KH AND d.TINH_TRANG = 'Đã giao thành công'
        WHERE 1=1
    ";

    $params = [];
    $types = '';

    if ($search !== '') {
        $sql .= " AND k.TEN_KH LIKE ?";
        $params[] = "%$search%";
        $types .= 's';
    }
    if ($start_date !== '') {
        $sql .= " AND DATE(d.NGAY_TAO) >= ?";
        $params[] = $start_date;
        $types .= 's';
    }
    if ($end_date !== '') {
        $sql .= " AND DATE(d.NGAY_TAO) <= ?";
        $params[] = $end_date;
        $types .= 's';
    }

    $sql .= " GROUP BY k.MA_KH, k.TEN_KH";
    $sql .= " ORDER BY total $sort";

    $stmt = $this->conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();

    return $data;
}

// Lấy thông tin khách hàng + tổng chi tiêu
public function layThongTinKhachHang($maKh) {
    $sql = "SELECT 
                k.TEN_KH,
                COALESCE(SUM(d.TONG_TIEN), 0) AS tong_chi_tieu
            FROM khachhang k
            LEFT JOIN donhang d ON k.MA_KH = d.MA_KH AND d.TINH_TRANG = 'Đã giao thành công'
            WHERE k.MA_KH = ?
            GROUP BY k.MA_KH, k.TEN_KH";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $maKh);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    return $data ?: false;
}

// Lấy danh sách đơn hàng của khách (đã thành công)
public function layDonHangCuaKhachHang($maKh, $options = []) {
    $search     = $options['search'] ?? '';
    $start_date = $options['start_date'] ?? '';
    $end_date   = $options['end_date'] ?? '';
    $sort       = $options['sort'] ?? 'DESC';

    $sql = "SELECT 
                d.MA_DH AS orderId,
                DATE_FORMAT(d.NGAY_TAO, '%d/%m/%Y') AS orderDate,
                d.TONG_TIEN AS total
            FROM donhang d
            WHERE d.MA_KH = ? AND d.TINH_TRANG = 'Đã giao thành công'";

    $params = [$maKh];
    $types  = 'i';

    if ($search !== '') {
        $sql .= " AND d.MA_DH LIKE ?";
        $params[] = "%$search%";
        $types .= 's';
    }
    if ($start_date !== '') {
        $sql .= " AND DATE(d.NGAY_TAO) >= ?";
        $params[] = $start_date;
        $types .= 's';
    }
    if ($end_date !== '') {
        $sql .= " AND DATE(d.NGAY_TAO) <= ?";
        $params[] = $end_date;
        $types .= 's';
    }

    $sql .= " ORDER BY d.TONG_TIEN $sort";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    return $data;
}

public function layChiTietDonHang($MA_DH) {
    // Lấy thông tin đơn hàng + khách hàng
    $sql = "SELECT 
                d.MA_DH AS orderId,
                d.MA_KH AS customerId,
                DATE_FORMAT(d.NGAY_TAO, '%d/%m/%Y') AS orderDate,
                d.TONG_TIEN,
                d.PHUONG_THUC AS paymentMethod,
                d.GHI_CHU AS note,
                d.DIA_CHI AS address,
                k.TEN_KH AS customerName,
                k.SO_DIEN_THOAI AS phone,
                d.TINH_TRANG AS shippingStatus
            FROM donhang d
            JOIN khachhang k ON d.MA_KH = k.MA_KH
            WHERE d.MA_DH = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $MA_DH);
    $stmt->execute();
    $result = $stmt->get_result();
    $info = $result->fetch_assoc();
    $stmt->close();

    if (!$info) return false;

    // Lấy chi tiết sản phẩm (ưu tiên bảng chitietdonhang)
    $items = [];
    $sql_items = "SELECT 
                    s.TEN_SP AS product,
                    s.HINH_ANH AS image,
                    ct.SO_LUONG AS quantity,
                    ct.GIA_LUC_MUA AS price
                  FROM chitietdonhang ct
                  JOIN sanpham s ON ct.MA_SP = s.MA_SP
                  WHERE ct.MA_DH = ?";
    $stmt_items = $this->conn->prepare($sql_items);
    $stmt_items->bind_param("i", $MA_DH);
    $stmt_items->execute();
    $result_items = $stmt_items->get_result();
    while ($row = $result_items->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt_items->close();

    // Fallback: parse GHI_CHU nếu không có chitietdonhang
    if (empty($items) && !empty($info['note'])) {
        $ghichu = trim($info['note']);
        $ghichu = str_replace('|| ', '', $ghichu);
        $parts = explode(',', $ghichu);
        $ma_sp_list = [];
        $temp = [];
        foreach ($parts as $part) {
            $data = explode(':', trim($part));
            if (count($data) >= 3 && is_numeric($data[0])) {
                $ma_sp = (int)$data[0];
                $ma_sp_list[] = $ma_sp;
                $temp[$ma_sp] = ['quantity' => (int)$data[1], 'price' => (int)$data[2]];
            }
        }
        if (!empty($ma_sp_list)) {
            $placeholders = str_repeat('?,', count($ma_sp_list) - 1) . '?';
            $sql_sp = "SELECT MA_SP, TEN_SP AS product, HINH_ANH AS image FROM sanpham WHERE MA_SP IN ($placeholders)";
            $stmt_sp = $this->conn->prepare($sql_sp);
            $stmt_sp->bind_param(str_repeat('i', count($ma_sp_list)), ...$ma_sp_list);
            $stmt_sp->execute();
            $res = $stmt_sp->get_result();
            while ($sp = $res->fetch_assoc()) {
                $t = $temp[$sp['MA_SP']];
                $items[] = [
                    'product'  => $sp['product'],
                    'image'    => $sp['image'],
                    'quantity' => $t['quantity'],
                    'price'    => $t['price']
                ];
            }
            $stmt_sp->close();
        }
    }

    return ['info' => $info, 'items' => $items];
}
}