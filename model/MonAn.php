<?php

class MonAn {
    private $id;         // MA_SP
    private $ten;        // TEN_SP
    private $hinhAnh;    // HINH_ANH
    private $giaCa;      // GIA_CA
    private $moTa;       // MO_TA
    private $maLoaiSp;   // MA_LOAISP
    private $tinhTrang; // TINH_TRANG (1: active, 0: hidden, -1: deleted)

    public function __construct($id, $ten, $hinhAnh, $giaCa, $moTa, $maLoaiSp, $tinhTrang = 1) {
        $this->id = $id;
        $this->ten = $ten;
        $this->hinhAnh = $hinhAnh;
        $this->giaCa = $giaCa;
        $this->moTa = $moTa;
        $this->maLoaiSp = $maLoaiSp;
        $this->tinhTrang = $tinhTrang;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTen() {
        return $this->ten;
    }

    public function getHinhAnh() {
        return $this->hinhAnh;
    }

    public function getGiaCa() {
        return $this->giaCa;
    }

    public function getMoTa() {
        return $this->moTa;
    }

    public function getMaLoaiSp() {
        return $this->maLoaiSp;
    }

    public function getTinhTrang() {
        return $this->tinhTrang;
    }

    // Setters
    public function setTen($ten) {
        $this->ten = $ten;
    }

    public function setHinhAnh($hinhAnh) {
        $this->hinhAnh = $hinhAnh;
    }

    public function setGiaCa($giaCa) {
        if ($giaCa < 0) {
            throw new Exception("Giá không hợp lệ");
        }
        $this->giaCa = $giaCa;
    }

    public function setMoTa($moTa) {
        $this->moTa = $moTa;
    }

    public function setMaLoaiSp($maLoaiSp) {
        $this->maLoaiSp = $maLoaiSp;
    }

    public function setTinhTrang($tinhTrang) {
        $this->tinhTrang = $tinhTrang;
    }
}