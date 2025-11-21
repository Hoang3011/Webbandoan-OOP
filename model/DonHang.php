<?php

class DonHang {
    private $id;          // MA_DH
    private $maKh;        // MA_KH
    private $ngayTao;     // NGAY_TAO
    private $tongTien;    // TONG_TIEN
    private $ghiChu;      // GHI_CHU
    private $diaChi;      // DIA_CHI
    private $maGh;        // MA_GH
    private $phuongThuc;  // PHUONG_THUC ('Tiền mặt' hoặc 'Chuyển khoản')
    private $tinhTrang;   // TINH_TRANG

    public function __construct($id, $maKh, $ngayTao, $tongTien, $ghiChu, $diaChi, $maGh, $phuongThuc, $tinhTrang = 'Chưa xác nhận') {
        $this->id = $id;
        $this->maKh = $maKh;
        $this->ngayTao = $ngayTao;
        $this->tongTien = $tongTien;
        $this->ghiChu = $ghiChu;
        $this->diaChi = $diaChi;
        $this->maGh = $maGh;
        $this->phuongThuc = $phuongThuc;
        $this->tinhTrang = $tinhTrang;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getMaKh() {
        return $this->maKh;
    }

    public function getNgayTao() {
        return $this->ngayTao;
    }

    public function getTongTien() {
        return $this->tongTien;
    }

    public function getGhiChu() {
        return $this->ghiChu;
    }

    public function getDiaChi() {
        return $this->diaChi;
    }

    public function getMaGh() {
        return $this->maGh;
    }

    public function getPhuongThuc() {
        return $this->phuongThuc;
    }

    public function getTinhTrang() {
        return $this->tinhTrang;
    }

    // Setters
    public function setMaKh($maKh) {
        $this->maKh = $maKh;
    }

    public function setNgayTao($ngayTao) {
        $this->ngayTao = $ngayTao;
    }

    public function setTongTien($tongTien) {
        $this->tongTien = $tongTien;
    }

    public function setGhiChu($ghiChu) {
        $this->ghiChu = $ghiChu;
    }

    public function setDiaChi($diaChi) {
        $this->diaChi = $diaChi;
    }

    public function setMaGh($maGh) {
        $this->maGh = $maGh;
    }

    public function setPhuongThuc($phuongThuc) {
        if (!in_array($phuongThuc, ['Tiền mặt', 'Chuyển khoản'])) {
            throw new Exception("Phương thức không hợp lệ");
        }
        $this->phuongThuc = $phuongThuc;
    }

    public function setTinhTrang($tinhTrang) {
        if (!in_array($tinhTrang, ['Chưa xác nhận', 'Đã xác nhận', 'Đã giao thành công', 'Đã hủy đơn'])) {
            throw new Exception("Tình trạng không hợp lệ");
        }
        $this->tinhTrang = $tinhTrang;
    }
}