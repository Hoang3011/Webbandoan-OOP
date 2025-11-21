-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 24, 2025 lúc 08:39 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `webbandoan222`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonhang`
--

CREATE TABLE `chitietdonhang` (
  `mactdh` int(255) NOT NULL,
  `madh` int(255) NOT NULL,
  `masp` int(255) NOT NULL,
  `soluong` int(255) NOT NULL,
  `giabanle` int(255) NOT NULL,
  `tongtien` int(11) GENERATED ALWAYS AS (`soluong` * `giabanle`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietdonhang`
--

INSERT INTO `chitietdonhang` (`mactdh`, `madh`, `masp`, `soluong`, `giabanle`) VALUES
(1, 1, 3, 1, 50000),
(2, 2, 2, 3, 20000),
(3, 6, 1, 1, 50000),
(4, 7, 1, 1, 50000),
(5, 7, 2, 1, 20000),
(7, 10, 14, 1, 10000),
(8, 11, 27, 1, 50000),
(9, 12, 2, 1, 20000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhang`
--

CREATE TABLE `donhang` (
  `madh` int(255) NOT NULL,
  `makh` int(255) NOT NULL,
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp(),
  `tongtien` int(255) NOT NULL,
  `ghichu` varchar(255) NOT NULL,
  `diachi` varchar(255) NOT NULL,
  `phuongthuc` enum('Tiền mặt','Chuyển khoản') NOT NULL,
  `trangthai` enum('Chưa xác nhận','Đã xác nhận','Đã giao thành công','Đã hủy đơn') DEFAULT 'Chưa xác nhận'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `donhang`
--

INSERT INTO `donhang` (`madh`, `makh`, `ngaytao`, `tongtien`, `ghichu`, `diachi`, `phuongthuc`, `trangthai`) VALUES
(1, 2, '2025-03-21 00:00:00', 50000, '', '', 'Tiền mặt', 'Đã hủy đơn'),
(2, 22, '2025-04-01 00:00:00', 60000, '', '', 'Tiền mặt', 'Đã giao thành công'),
(4, 22, '2025-04-19 05:12:27', 100000, '', '123 đường AB, Quận Bình Thạnh, Thành phố Hồ Chí Minh', 'Tiền mặt', 'Đã xác nhận'),
(5, 22, '2025-04-19 06:05:51', 99000, '', '321 CBA, Quận Bình Tân, Thành phố Hồ Chí Minh', 'Tiền mặt', 'Đã hủy đơn'),
(6, 13, '2025-04-19 08:05:29', 50000, '', '12345', 'Tiền mặt', 'Đã giao thành công'),
(7, 13, '2025-04-19 10:04:18', 70000, '', '12345', 'Tiền mặt', 'Chưa xác nhận'),
(8, 13, '2025-04-21 08:07:24', 50000, '1', '123 B, Quận 1, Thành phố Hồ Chí Minh', 'Tiền mặt', 'Chưa xác nhận'),
(10, 13, '2025-04-21 08:14:58', 10000, '', '12345', 'Tiền mặt', 'Đã giao thành công'),
(11, 13, '2025-04-21 08:22:47', 50000, '', '12345', 'Tiền mặt', 'Đã giao thành công'),
(12, 23, '2025-04-24 06:14:38', 20000, '', '321, Quận Tân Phú, Thành phố Hồ Chí Minh', 'Tiền mặt', 'Đã giao thành công');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giohang`
--

CREATE TABLE `giohang` (
  `magiohang` int(255) NOT NULL,
  `makh` int(11) NOT NULL,
  `masp` int(11) NOT NULL,
  `soluong` int(11) NOT NULL DEFAULT 1,
  `dongia` int(255) NOT NULL,
  `tongtien` int(255) GENERATED ALWAYS AS (`soluong` * `dongia`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `giohang`
--

INSERT INTO `giohang` (`magiohang`, `makh`, `masp`, `soluong`, `dongia`) VALUES
(1, 12, 2, 1, 20000),
(2, 12, 2, 1, 20000),
(3, 12, 2, 1, 20000),
(4, 12, 2, 1, 20000),
(5, 12, 3, 7, 50000),
(6, 12, 3, 7, 50000),
(7, 12, 10, 1, 40000),
(8, 0, 3, 1, 50000),
(9, 12, 20, 1, 55000),
(10, 12, 12, 1, 30000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `makh` int(255) NOT NULL,
  `tenkh` varchar(100) NOT NULL,
  `matkhau` varchar(20) NOT NULL,
  `diachi` varchar(100) NOT NULL,
  `sodienthoai` varchar(20) NOT NULL,
  `trangthai` enum('Locked','Active') NOT NULL DEFAULT 'Active',
  `ngaytao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`makh`, `tenkh`, `matkhau`, `diachi`, `sodienthoai`, `trangthai`, `ngaytao`) VALUES
(12, 'nhan', 'nhan1234', 'quan tan phu', '0775177636', 'Active', '2025-04-19'),
(13, 'KK', '12345', '12345', '12345', 'Active', '2025-04-19'),
(14, 'kkkkkk', '090909', '090909', '090909', 'Active', '2025-04-19'),
(22, 'Đăng Khoa', '0987', '123', '0987654321', 'Active', '2025-04-19'),
(23, 'nguyenvana', '321', '321', '321', 'Active', '0000-00-00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaisp`
--

CREATE TABLE `loaisp` (
  `maloai` varchar(255) NOT NULL,
  `tenloai` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `loaisp`
--

INSERT INTO `loaisp` (`maloai`, `tenloai`) VALUES
('L001', 'Món chay'),
('L002', 'Món mặn'),
('L003', 'Món lẩu'),
('L004', 'Món ăn vặt'),
('L005', 'Món tráng miệng'),
('L006', 'Nước uống'),
('L007', 'Hải sản');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien`
--

CREATE TABLE `nhanvien` (
  `manv` int(255) NOT NULL,
  `tennv` varchar(255) NOT NULL,
  `sodienthoai` varchar(255) NOT NULL,
  `mk` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhanvien`
--

INSERT INTO `nhanvien` (`manv`, `tennv`, `sodienthoai`, `mk`) VALUES
(1, 'Khoa', '12345', '12345');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

CREATE TABLE `sanpham` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Image` varchar(255) NOT NULL,
  `Price` int(255) NOT NULL,
  `Describtion` varchar(255) NOT NULL,
  `Type` enum('món chay','món mặn','món lẩu','món ăn vặt','món tráng miệng','nước uống','hải sản') NOT NULL,
  `Visible` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`ID`, `Name`, `Image`, `Price`, `Describtion`, `Type`, `Visible`) VALUES
(1, 'Phở Bò', 'assets\\img\\products\\phobo.jpg', 50000, 'Hương vị tinh túy của Việt Nam, với nước dùng đậm đà từ xương bò hầm kỹ, kết hợp cùng bánh phở mềm, thịt bò tái chín vừa tới và các loại rau thơm tạo nên một món ăn sáng hoàn hảo.', 'món mặn', 1),
(2, 'Bánh mì', 'assets\\img\\products\\banhmi.webp', 20000, 'Một trong những món ăn đường phố ngon nhất thế giới, với vỏ bánh giòn rụm, nhân đầy đặn từ pate, thịt nguội, trứng, kèm theo rau thơm và nước sốt đậm đà.', 'món mặn', 0),
(3, 'Bún chả Hà Nội', 'assets\\img\\products\\buncha.jpg', 50000, 'Thịt nướng thơm lừng, được ăn kèm với bún tươi, rau sống và nước mắm chua ngọt, tạo nên sự cân bằng hoàn hảo giữa vị ngọt, chua, mặn, cay.', 'món mặn', 0),
(4, 'Bánh xèo Miền Tây', 'assets\\img\\products\\banhxeo.jpg', 30000, ' Lớp vỏ giòn rụm, nhân đầy đặn từ tôm, thịt và giá đỗ, ăn kèm rau sống và chấm nước mắm chua ngọt, tạo nên hương vị khó quên.', 'món mặn', 1),
(5, 'Gỏi cuốn', 'assets\\img\\products\\goicuon.jpg', 30000, 'Món ăn thanh mát, với tôm, thịt, bún và rau cuốn trong bánh tráng, chấm kèm nước chấm bơ đậu phộng béo bùi.', 'món mặn', 1),
(6, 'Cao lầu', 'assets\\img\\products\\caolau.jpg', 40000, 'Đặc sản Hội An với sợi mì dai, thịt xá xíu đậm đà, rau sống tươi ngon và nước dùng đặc biệt.', 'món mặn', 1),
(7, 'Bún bò Huế', 'assets\\img\\products\\bunbohue.jpg', 50000, ' Nước dùng cay nồng, đậm đà với sả, thịt bò, chả cua, ăn kèm rau thơm và bún to.', 'món mặn', 1),
(8, 'Hủ tiếu', 'assets\\img\\products\\hutieu.jpg', 30000, 'Món ăn miền Nam với nước dùng ngọt thanh từ xương, sợi hủ tiếu dai mềm, ăn kèm tôm, thịt và rau sống.', 'món mặn', 1),
(9, 'Chả cá Lã Vọng', 'assets\\img\\products\\chaca.jpg', 40000, ' Cá lăng nướng nghệ, ăn kèm bún, rau thì là và mắm tôm, tạo nên hương vị độc đáo.', 'món mặn', 1),
(10, 'Mì Quảng', 'assets\\img\\products\\miquang.jpg', 40000, 'Món mì đặc trưng của Quảng Nam với sợi mì vàng, thịt tôm, trứng cút, nước dùng ít nhưng đậm vị.', 'món mặn', 1),
(11, 'Cơm tấm', 'assets\\img\\products\\comtam.jpeg', 50000, ' Món ăn bình dân nhưng đầy đủ dinh dưỡng với sườn nướng, bì, chả trứng, ăn kèm nước mắm pha.', 'món mặn', 1),
(12, 'Bánh bột lọc Huế', 'assets\\img\\products\\banhbotloc.webp', 30000, 'Lớp bột dẻo dai, nhân tôm thịt đậm đà, chấm nước mắm cay tạo nên món ăn vặt hấp dẫn.', 'món mặn', 1),
(13, 'Bánh cuốn', 'assets\\img\\products\\banhcuonv2.jpg', 30000, 'Lớp bánh mỏng dai, nhân thịt băm và mộc nhĩ thơm ngon, ăn kèm nước mắm tỏi ớt và chả lụa, tạo nên bữa sáng tinh tế.', 'món mặn', 1),
(14, 'Bánh ít', 'assets\\img\\products\\banhit.webp', 10000, 'Nhỏ nhắn nhưng đầy hương vị, vỏ bánh dẻo kết hợp nhân đậu xanh hoặc nhân dừa bùi béo.', 'món tráng miệng', 1),
(15, 'Gà nướng cơm lam', 'assets\\img\\products\\ganuongcomlam.jpg', 120000, 'Gà nướng vàng giòn, ăn kèm cơm lam dẻo thơm của vùng Tây Nguyên.', 'món mặn', 1),
(16, 'Bún cá Nha Trang', 'assets\\img\\products\\buncanhatrang.jpg', 60000, 'Nước dùng thanh ngọt từ cá biển, kết hợp với bún tươi và rau sống.', 'hải sản', 1),
(17, 'Bánh tráng cuốn thịt heo', 'assets\\img\\products\\banhtrangcuonthitheo.jpg', 75000, 'Thịt heo mềm thơm, ăn kèm rau sống và chấm mắm nêm đậm đà.', 'món mặn', 1),
(18, 'Bún sứa Nha Trang', 'assets\\img\\products\\bunsuanhatrang.jpg', 70000, 'Sứa giòn sần sật, nước dùng ngọt thanh tạo nên hương vị đặc trưng.', 'hải sản', 1),
(19, 'Bánh ép Huế', 'assets/img/products/1743773390_banhephue.jpeg', 20000, 'Bánh ép giòn rụm, nhân pate, trứng và thịt đầy đặn.', 'món ăn vặt', 1),
(20, 'Cháo lươn Nghệ An', 'assets\\img\\products\\chaoluonnghean.jpg', 55000, 'Cháo lươn thơm ngon, cay nồng đặc trưng của xứ Nghệ.', 'hải sản', 1),
(21, 'Mực nhồi thịt nướng', 'assets\\img\\products\\mucnhoithitnuong.jpg', 90000, 'Mực tươi nhồi thịt, nướng thơm lừng, chấm muối ớt xanh.', 'hải sản', 1),
(22, 'Cá bống kho tộ', 'assets\\img\\products\\cabongkhoto.jpg', 65000, 'Cá bống kho với nước màu dừa, thịt cá mềm ngọt.', 'hải sản', 1),
(23, 'Hến xào xúc bánh tráng', 'assets\\img\\products\\henxaoxucbanhtrang.jpg', 50000, 'Hến xào đậm vị, ăn kèm bánh tráng nướng giòn.', 'món mặn', 1),
(24, 'Bánh đập hến xào', 'assets\\img\\products\\banhdaphenxao.jpg', 45000, 'Bánh đập giòn tan kết hợp với hến xào thơm ngon.', 'món mặn', 1),
(25, 'Cá lóc nướng trui', 'assets\\img\\products\\calocnuongtrui.jpg', 110000, 'Cá lóc nướng nguyên con, ăn kèm rau sống và bún.', 'hải sản', 1),
(26, 'Lẩu mắm miền Tây', 'assets\\img\\products\\laumam.jpg', 180000, 'Lẩu mắm đậm đà, ăn kèm rau đồng quê và bún tươi.', 'hải sản', 1),
(27, 'Bánh tét lá cẩm', 'assets/img/products/1745057669_banhtetlacam.jpg', 50000, 'Bánh tét dẻo thơm, nhân đậu xanh và thịt ba rọi.', 'món tráng miệng', 1),
(28, 'Gỏi gà măng cụt', 'assets\\img\\products\\goigamangcut.jpg', 90000, 'Gà ta xé phay, trộn cùng măng cụt giòn ngọt.', 'món mặn', 1),
(29, 'Bánh xèo Nam Bộ', 'assets\\img\\products\\banhxeonambo.jpg', 70000, 'Bánh xèo vàng giòn, nhân đầy tôm, thịt và giá.', 'món mặn', 1),
(30, 'Gỏi củ hủ dừa', 'assets\\img\\products\\goicuhudua.jpg', 65000, 'Củ hủ dừa giòn ngọt, trộn cùng tôm thịt và rau thơm.', 'món mặn', 1),
(31, 'Bò lá lốt', 'assets\\img\\products\\bolalot.jpg', 75000, 'Thịt bò băm nhuyễn cuốn lá lốt, nướng thơm lừng.', 'món mặn', 1),
(32, 'Bún nước lèo Sóc Trăng', 'assets\\img\\products\\bunnuocleo.jpg', 70000, 'Bún nước lèo Sóc Trăng đặc trưng với mắm cá linh và cá lóc.', 'món mặn', 1),
(33, 'Cháo cá lóc rau đắng', 'assets\\img\\products\\chaocalocraudang.jpg', 60000, 'Cháo cá lóc nóng hổi ăn cùng rau đắng.', 'hải sản', 1),
(34, 'Cơm nị cà púa', 'assets\\img\\products\\comnica-pua.jpg', 85000, 'Món cơm kiểu Chăm, hương vị đậm đà của gia vị.', 'món chay', 1),
(35, 'Chuột đồng nướng lu', 'assets\\img\\products\\chuotdongnuong.jpg', 95000, 'Chuột đồng nướng giòn, chấm muối tiêu chanh.', 'món mặn', 1),
(36, 'Bánh tằm bì', 'assets\\img\\products\\banhtambi.jpg', 45000, 'Bánh tằm bì dẻo thơm, ăn kèm nước cốt dừa béo ngậy.', 'món tráng miệng', 1),
(37, 'Hủ tiếu Mỹ Tho', 'assets\\img\\products\\hutieumytho.jpg', 60000, 'Hủ tiếu dai ngon, nước dùng trong thanh.', 'món mặn', 1),
(38, 'Bánh canh Trảng Bàng', 'assets\\img\\products\\banhcanhtrangbang.jpg', 65000, 'Sợi bánh canh dai, nước dùng ngọt thanh, ăn kèm thịt heo.', 'món mặn', 1),
(39, 'Vịt quay Lạng Sơn', 'assets\\img\\products\\vitquaylangson.jpg', 150000, 'Vịt quay da giòn, ướp gia vị đặc trưng của vùng Lạng Sơn.', 'món mặn', 1),
(40, 'Nem nướng Nha Trang', 'assets\\img\\products\\nemnuongnhatrang.jpg', 80000, 'Nem nướng thơm lừng, ăn kèm rau sống và nước chấm đặc biệt.', 'món mặn', 1),
(41, 'Xôi xéo Hà Nội', 'assets\\img\\products\\xoixeohanoi.jpg', 30000, 'Xôi nếp dẻo, kết hợp đậu xanh nghiền và hành phi giòn.', 'món tráng miệng', 1),
(42, 'Bánh gai Hải Dương', 'assets\\img\\products\\banhgaiaidduong.jpg', 25000, 'Bánh gai dẻo thơm, nhân đậu xanh béo ngậy.', 'món tráng miệng', 1),
(43, 'Bánh tro', 'assets\\img\\products\\banhtro.jpg', 20000, 'Bánh tro mềm, ăn kèm mật mía ngọt thanh.', 'món tráng miệng', 1),
(44, 'Chả mực Hạ Long', 'assets\\img\\products\\chamuchalong.jpg', 120000, 'Chả mực giòn dai, chấm kèm tương ớt cay nồng.', 'món mặn', 1),
(45, 'Bánh dày giò', 'assets\\img\\products\\banhdaygio.jpg', 35000, 'Bánh dày dẻo dai, ăn kèm giò lụa mềm mịn.', 'món ăn vặt', 1),
(46, 'Ốc hương nướng bơ tỏi', 'assets\\img\\products\\ochuongnuong.jpg', 110000, 'Ốc hương nướng thơm lừng với bơ tỏi béo ngậy.', 'hải sản', 1),
(47, 'Lẩu cua đồng', 'assets\\img\\products\\laucuadong.jpg', 180000, 'Lẩu cua đồng thơm ngon, nước dùng đậm đà từ gạch cua.', 'món lẩu', 1),
(48, 'Bún riêu cua', 'assets\\img\\products\\bunrieucua.jpg', 65000, 'Bún riêu cua nước dùng chua ngọt, ăn kèm rau sống.', 'hải sản', 1),
(49, 'Bánh bông lan trứng muối', 'assets\\img\\products\\banhbonglantrungmuoi.jpg', 55000, 'Bánh mềm mịn, trứng muối mằn mặn, phô mai béo ngậy.', 'món tráng miệng', 1),
(50, 'Bánh da lợn', 'assets\\img\\products\\banhdalon.jpg', 30000, 'Bánh da lợn nhiều lớp, dẻo thơm từ lá dứa và đậu xanh.', 'món tráng miệng', 1),
(51, 'Bò kho', 'assets\\img\\products\\bokho.jpg', 90000, 'Bò kho đậm đà, ăn kèm bánh mì hoặc bún.', 'món mặn', 1),
(52, 'Xôi gấc', 'assets\\img\\products\\xoigac.jpg', 35000, 'Xôi gấc có màu đỏ cam rực rỡ, dẻo thơm.', 'món tráng miệng', 1),
(53, 'Gà hấp lá chanh', 'assets\\img\\products\\gahaplachanh.jpg', 120000, 'Gà hấp thơm lừng lá chanh, thịt mềm ngọt.', 'món mặn', 1),
(54, 'Canh chua cá lóc', 'assets\\img\\products\\canhchuacaloc.jpg', 75000, 'Canh chua thanh mát, cá lóc ngọt thịt, ăn kèm cơm.', 'hải sản', 1),
(55, 'Tôm rang me', 'assets\\img\\products\\tomrangme.jpg', 85000, 'Tôm rang me chua ngọt, ăn kèm cơm trắng hoặc bánh mì.', 'hải sản', 1),
(56, 'Bánh khọt', 'assets\\img\\products\\banhkhot.jpg', 50000, 'Bánh khọt giòn rụm, nhân tôm tươi, chấm nước mắm ngon.', 'món ăn vặt', 1),
(57, 'Súp cua', 'assets\\img\\products\\supcua.jpg', 70000, 'Súp cua thơm ngon, bổ dưỡng với trứng bắc thảo.', 'món ăn vặt', 1),
(58, 'Nem chua Thanh Hóa', 'assets\\img\\products\\nemchua.jpg', 60000, 'Nem chua lên men tự nhiên, chua nhẹ, cay nhẹ.', 'món ăn vặt', 1),
(59, 'Bánh giò', 'assets\\img\\products\\banhgio.jpg', 40000, 'Bánh giò mềm, nhân thịt băm đậm vị, ăn nóng ngon hơn.', 'món ăn vặt', 1),
(60, 'Cút lộn xào me', 'assets\\img\\products\\cutlonxaome.jpg', 55000, 'Trứng cút lộn xào với me chua ngọt, ăn kèm rau răm.', 'món ăn vặt', 1),
(61, 'Cà phê sữa đá ', 'assets\\img\\products\\caphe.jpg', 30000, 'Cà phê sữa đá là một trong những thức uống mang đậm bản sắc Việt Nam. Hương vị đậm đà của cà phê phin hòa quyện với vị ngọt béo của sữa đặc, tạo nên một thức uống vừa mạnh mẽ, vừa ngọt ngào. Uống một ly vào buổi sáng giúp tỉnh táo cả ngày!', 'nước uống', 1),
(62, 'Trà đá ', 'assets\\img\\products\\trada.jpg', 5000, ' Trà đá là thức uống phổ biến ở mọi quán ăn vỉa hè tại Việt Nam. Nó có vị thanh mát, giúp giải nhiệt và không chứa quá nhiều đường hay chất béo, rất tốt cho sức khỏe.', 'nước uống', 1),
(63, 'Nước sâm', 'assets\\img\\products\\nuocsam.jpg', 10000, 'Nước sâm có vị ngọt thanh tự nhiên, thơm nhẹ từ các loại thảo mộc. Đây là thức uống giải nhiệt cực tốt, thường được người miền Nam ưa chuộng, đặc biệt vào những ngày nắng nóng.', 'nước uống', 1),
(64, 'Nước mía', 'assets\\img\\products\\nuocmia.png', 10000, 'Nước mía có vị ngọt tự nhiên, mát lạnh, giúp giải khát nhanh chóng. Thêm một chút tắc hoặc quất sẽ tạo vị chua nhẹ, cân bằng hương vị.', 'nước uống', 1),
(65, 'Sinh tố bơ', 'assets\\img\\products\\sinhtobo.jpg', 25000, 'Sinh tố bơ có độ béo mịn đặc trưng, vị ngọt nhẹ và mùi thơm hấp dẫn. Đây là món sinh tố bổ dưỡng, tốt cho da và sức khỏe.', 'nước uống', 1),
(66, 'Bún riêu chay ', 'assets\\img\\products\\bunrieuchay.jpg', 30000, 'Nước dùng thanh ngọt từ cà chua, đậu hũ và nấm tạo nên vị chua nhẹ, thơm ngon. Món này vẫn giữ được hương vị đặc trưng của bún riêu truyền thống nhưng nhẹ nhàng hơn, thích hợp cho những ai muốn ăn thanh đạm.', 'món chay', 1),
(67, 'Hủ tiếu chay', 'assets\\img\\products\\hutieuchay.jpg', 30000, 'Nước dùng từ rau củ rất ngọt tự nhiên, kết hợp với nấm, tàu hũ ki và sợi hủ tiếu dai ngon. Món này không chỉ thơm ngon mà còn rất bổ dưỡng.', 'món chay', 1),
(68, 'Cơm tấm chay ', 'assets\\img\\products\\comtamchay.png', 30000, ' Món cơm tấm chay có đầy đủ các thành phần như chả chay, bì chay, nước mắm chay pha vừa miệng. Mặc dù không có thịt nhưng vẫn giữ được hương vị thơm ngon, hấp dẫn.', 'món chay', 1);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD PRIMARY KEY (`mactdh`);

--
-- Chỉ mục cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`madh`);

--
-- Chỉ mục cho bảng `giohang`
--
ALTER TABLE `giohang`
  ADD PRIMARY KEY (`magiohang`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`makh`);

--
-- Chỉ mục cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`manv`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  MODIFY `mactdh` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `donhang`
--
ALTER TABLE `donhang`
  MODIFY `madh` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `giohang`
--
ALTER TABLE `giohang`
  MODIFY `magiohang` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `makh` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;