<?php
require_once 'TaiKhoan.php';

class QuanTriVien extends TaiKhoan {
    public function __construct($id, $ten, $matKhau, $soDienThoai) {
        parent::__construct($id, $ten, $matKhau, $soDienThoai);
    }
}