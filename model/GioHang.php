<?php

class GioHang
{
    private $id;
    private $maKh;
    private $tongTien;
    private $items = [];

    public function __construct($id, $maKh, $tongTien = 0)
    {
        $this->id = $id;
        $this->maKh = $maKh;
        $this->tongTien = $tongTien;
    }

    // Getters
    public function getId(): mixed { return $this->id; }
    public function getMaKh(): mixed { return $this->maKh; }
    public function getTongTien(): mixed { return $this->tongTien; }
    public function getItems(): array { return $this->items; }

    // Setters
    public function setMaKh($maKh): void { $this->maKh = $maKh; }
    public function setTongTien($tongTien): void { $this->tongTien = $tongTien; }

    // Load items from DB
    public function loadItems($conn): void
    {
        $sql = "
            SELECT 
                ct.MA_SP, ct.SO_LUONG,
                sp.TEN_SP AS Name, sp.HINH_ANH AS Image,
                sp.GIA_CA AS dongia,
                (ct.SO_LUONG * sp.GIA_CA) AS tongtien
            FROM chitietgiohang ct
            JOIN sanpham sp ON ct.MA_SP = sp.MA_SP
            WHERE ct.MA_GH = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->items = [];
        while ($row = $result->fetch_assoc()) {
            $this->items[] = $row;
        }
        $stmt->close();
    }

    // Add/Update/Remove items
    public function addItem($maSp, $soLuong): void
    {
        foreach ($this->items as &$item) {
            if ($item['MA_SP'] == $maSp) {
                $item['SO_LUONG'] += $soLuong;
                return;
            }
        }
        $this->items[] = ['MA_SP' => $maSp, 'SO_LUONG' => $soLuong];
    }

    public function removeItem($maSp): void
    {
        $this->items = array_filter($this->items, fn($item) => $item['MA_SP'] != $maSp);
    }

    // Static method to get cart by user
    public static function getCartByUser($conn, $maKh): ?GioHang
    {
        $sql = "SELECT MA_GH, TONG_TIEN FROM giohang WHERE MA_KH = ? ORDER BY MA_GH DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $maKh);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row || $row['TONG_TIEN'] == 0) {
            return null;
        }
        $gioHang = new GioHang($row['MA_GH'], $maKh, $row['TONG_TIEN']);
        $gioHang->loadItems($conn);
        return $gioHang;
    }
}