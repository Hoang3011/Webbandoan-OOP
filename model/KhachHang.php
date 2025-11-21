<?php
require_once 'TaiKhoan.php';

class KhachHang extends TaiKhoan {
    private $diaChi;    // DIA_CHI
    private $trangThai; // TRANG_THAI ('Locked' hoặc 'Active')
    private $ngayTao;   // NGAY_TAO

    public function __construct($id, $ten, $matKhau, $diaChi, $soDienThoai, $trangThai = 'Active', $ngayTao = null) {
        parent::__construct($id, $ten, $matKhau, $soDienThoai);
        $this->diaChi = $diaChi;
        $this->trangThai = $trangThai;
        $this->ngayTao = $ngayTao ?? date('Y-m-d');
    }

    // Getters
    public function getDiaChi() {
        return $this->diaChi;
    }

    public function getTrangThai() {
        return $this->trangThai;
    }

    public function getNgayTao() {
        return $this->ngayTao;
    }

    // Setters
    public function setDiaChi($diaChi) {
        $this->diaChi = $diaChi;
    }

    public function setTrangThai($trangThai) {
        if (!in_array($trangThai, ['Locked', 'Active'])) {
            throw new Exception("Trạng thái không hợp lệ");
        }
        $this->trangThai = $trangThai;
    }

    public function setNgayTao($ngayTao) {
        $this->ngayTao = $ngayTao;
    }
}