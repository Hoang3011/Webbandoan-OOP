<?php

class PhuongThucThanhToan {
    const TIEN_MAT = 'Tiền mặt';
    const CHUYEN_KHOAN = 'Chuyển khoản';

    private $phuongThuc;

    public function __construct($phuongThuc) {
        if (!in_array($phuongThuc, [self::TIEN_MAT, self::CHUYEN_KHOAN])) {
            throw new Exception("Phương thức thanh toán không hợp lệ");
        }
        $this->phuongThuc = $phuongThuc;
    }

    public function getPhuongThuc() {
        return $this->phuongThuc;
    }

    public function xuLyThanhToan($tongTien) {
        // Logic xử lý thanh toán (ví dụ: kết nối API ngân hàng cho chuyển khoản)
        echo "Xử lý thanh toán bằng {$this->phuongThuc} với số tiền {$tongTien}.";
    }
}