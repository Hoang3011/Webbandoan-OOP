<?php

class DonHang
{
    private $id;          // MA_DH
    private $maKh;        // MA_KH
    private $ngayTao;     // NGAY_TAO
    private $tongTien;    // TONG_TIEN
    private $ghiChu;      // GHI_CHU
    private $diaChi;      // DIA_CHI
    private $maGh;        // MA_GH
    private $phuongThuc;  // PHUONG_THUC ('Tiền mặt' hoặc 'Chuyển khoản')
    private $tinhTrang;   // TINH_TRANG

    public function __construct($id, $maKh, $ngayTao, $tongTien, $ghiChu, $diaChi, $maGh, $phuongThuc, $tinhTrang = 'Chưa xác nhận')
    {
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
    public function getId(): mixed
    {
        return $this->id;
    }

    public function getMaKh(): mixed
    {
        return $this->maKh;
    }

    public function getNgayTao(): mixed
    {
        return $this->ngayTao;
    }

    public function getTongTien(): mixed
    {
        return $this->tongTien;
    }

    public function getGhiChu(): mixed
    {
        return $this->ghiChu;
    }

    public function getDiaChi(): mixed
    {
        return $this->diaChi;
    }

    public function getMaGh(): mixed
    {
        return $this->maGh;
    }

    public function getPhuongThuc(): mixed
    {
        return $this->phuongThuc;
    }

    public function getTinhTrang(): mixed
    {
        return $this->tinhTrang;
    }

    // Setters
    public function setMaKh($maKh): void
    {
        $this->maKh = $maKh;
    }

    public function setNgayTao($ngayTao): void
    {
        $this->ngayTao = $ngayTao;
    }

    public function setTongTien($tongTien): void
    {
        $this->tongTien = $tongTien;
    }

    public function setGhiChu($ghiChu): void
    {
        $this->ghiChu = $ghiChu;
    }

    public function setDiaChi($diaChi): void
    {
        $this->diaChi = $diaChi;
    }

    public function setMaGh($maGh): void
    {
        $this->maGh = $maGh;
    }

    public function setPhuongThuc($phuongThuc): void
    {
        if (!in_array($phuongThuc, ['Tiền mặt', 'Chuyển khoản'])) {
            throw new Exception("Phương thức không hợp lệ");
        }
        $this->phuongThuc = $phuongThuc;
    }

    public function setTinhTrang($tinhTrang): void
    {
        if (!in_array($tinhTrang, ['Chưa xác nhận', 'Đã xác nhận', 'Đã giao thành công', 'Đã hủy đơn'])) {
            throw new Exception("Tình trạng không hợp lệ");
        }
        $this->tinhTrang = $tinhTrang;
    }
}