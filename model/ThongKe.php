<?php

class ThongKe {
    private $conn; // Kết nối mysqli đến DB

    public function __construct($conn) {
        $this->conn = $conn;
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
}