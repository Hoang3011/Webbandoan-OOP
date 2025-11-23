<?php

class MonAn
{
    private $id;         // MA_SP
    private $ten;        // TEN_SP
    private $hinhAnh;    // HINH_ANH
    private $giaCa;      // GIA_CA
    private $moTa;       // MO_TA
    private $maLoaiSp;   // MA_LOAISP
    private $tinhTrang; // TINH_TRANG (1: active, 0: hidden, -1: deleted)

    public function __construct($id, $ten, $hinhAnh, $giaCa, $moTa, $maLoaiSp, $tinhTrang = 1)
    {
        $this->id = $id;
        $this->ten = $ten;
        $this->hinhAnh = $hinhAnh;
        $this->giaCa = $giaCa;
        $this->moTa = $moTa;
        $this->maLoaiSp = $maLoaiSp;
        $this->tinhTrang = $tinhTrang;
    }

    // Getters
    public function getId(): mixed
    {
        return $this->id;
    }

    public function getTen(): mixed
    {
        return $this->ten;
    }

    public function getHinhAnh(): mixed
    {
        return $this->hinhAnh;
    }

    public function getGiaCa(): mixed
    {
        return $this->giaCa;
    }

    public function getMoTa(): mixed
    {
        return $this->moTa;
    }

    public function getMaLoaiSp(): mixed
    {
        return $this->maLoaiSp;
    }

    public function getTinhTrang(): mixed
    {
        return $this->tinhTrang;
    }

    // Setters
    public function setTen($ten): void
    {
        $this->ten = $ten;
    }

    public function setHinhAnh($hinhAnh): void
    {
        $this->hinhAnh = $hinhAnh;
    }

    public function setGiaCa($giaCa): void
    {
        if ($giaCa < 0) {
            throw new Exception("Giá không hợp lệ");
        }
        $this->giaCa = $giaCa;
    }

    public function setMoTa($moTa): void
    {
        $this->moTa = $moTa;
    }

    public function setMaLoaiSp($maLoaiSp): void
    {
        $this->maLoaiSp = $maLoaiSp;
    }

    public function setTinhTrang($tinhTrang): void
    {
        $this->tinhTrang = $tinhTrang;
    }
}