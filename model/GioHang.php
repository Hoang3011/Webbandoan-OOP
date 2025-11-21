<?php

class GioHang {
    private $id;       // MA_GH
    private $maKh;     // MA_KH
    private $tongTien; // TONG_TIEN
    private $items = []; // Mảng chi tiết giỏ hàng (MA_SP => SO_LUONG)

    public function __construct($id, $maKh, $tongTien = 0) {
        $this->id = $id;
        $this->maKh = $maKh;
        $this->tongTien = $tongTien;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getMaKh() {
        return $this->maKh;
    }

    public function getTongTien() {
        return $this->tongTien;
    }

    public function getItems() {
        return $this->items;
    }

    // Setters
    public function setMaKh($maKh) {
        $this->maKh = $maKh;
    }

    public function setTongTien($tongTien) {
        $this->tongTien = $tongTien;
    }

    // Phương thức thêm item
    public function addItem($maSp, $soLuong) {
        if (isset($this->items[$maSp])) {
            $this->items[$maSp] += $soLuong;
        } else {
            $this->items[$maSp] = $soLuong;
        }
        // Cập nhật tổng tiền (giả sử cần tính lại từ DB hoặc khác)
    }

    // Phương thức xóa item
    public function removeItem($maSp) {
        unset($this->items[$maSp]);
    }
}