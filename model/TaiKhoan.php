<?php

class TaiKhoan {
    protected $id;           // MA_KH hoặc MA_NV
    protected $ten;          // TEN_KH hoặc TEN_NV
    protected $matKhau;      // MAT_KHAU
    protected $soDienThoai;  // SO_DIEN_THOAI

    public function __construct($id, $ten, $matKhau, $soDienThoai) {
        $this->id = $id;
        $this->ten = $ten;
        $this->matKhau = $matKhau; // Không mã hóa mật khẩu
        $this->soDienThoai = $soDienThoai;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTen() {
        return $this->ten;
    }

    public function getMatKhau() {
        return $this->matKhau;
    }

    public function getSoDienThoai() {
        return $this->soDienThoai;
    }

    // Setters
    public function setTen($ten) {
        $this->ten = $ten;
    }

    public function setMatKhau($matKhau) {
        $this->matKhau = $matKhau;
    }

    public function setSoDienThoai($soDienThoai) {
        $this->soDienThoai = $soDienThoai;
    }

    // Phương thức kiểm tra mật khẩu (không mã hóa)
    public function verifyMatKhau($matKhau) {
        return $matKhau === $this->matKhau;
    }
}