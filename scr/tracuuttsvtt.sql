-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 07, 2025 lúc 06:02 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `tracuuttsvtt`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_vi`
--

CREATE TABLE `don_vi` (
  `ma_don_vi` varchar(10) NOT NULL,
  `ten_don_vi` varchar(50) DEFAULT NULL,
  `dia_chi` varchar(100) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `loai_don_vi` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `don_vi`
--

INSERT INTO `don_vi` (`ma_don_vi`, `ten_don_vi`, `dia_chi`, `so_dien_thoai`, `email`, `loai_don_vi`) VALUES
('DV01', 'Công ty TNHH Công nghệ Trà Vinh', '123 Đường Nguyễn Văn Cừ, Thành phố Trà Vinh, Tỉnh Trà Vinh', '0294 123 4567', 'contact@techtravin.vn', 'Ngoài trường'),
('DV02', 'Khoa Công nghệ Thông tin, Trường Đại học Trà Vinh', '126 Nguyễn Thiện Thành, P5, TP Trà Vinh, tỉnh Trà Vinh', '(+84) 294.38552', 'phong.cntt@tvu.edu.vn', 'Trong trường');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dot_thuc_tap`
--

CREATE TABLE `dot_thuc_tap` (
  `ma_dot_thuc_tap` varchar(10) NOT NULL,
  `ten_dot` varchar(50) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `ngay_bat_dau` date DEFAULT NULL,
  `ngay_ket_thuc` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `dot_thuc_tap`
--

INSERT INTO `dot_thuc_tap` (`ma_dot_thuc_tap`, `ten_dot`, `mo_ta`, `ngay_bat_dau`, `ngay_ket_thuc`) VALUES
('DTT01', 'Thực tập đồ án cơ sở ngành', 'Đợt thực tập này nhằm giúp sinh viên nắm bắt các kiến thức ...', '2024-01-01', '2024-06-30'),
('DTT02', 'Thực tập đồ án chuyên ngành', 'Đợt thực tập này sẽ giúp sinh viên hiểu thêm về việc ứng dụng ...', '2024-02-15', '2024-08-15'),
('DTT03', 'Thực tập chuyên ngành', '...', '2024-03-15', '2024-09-15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lop`
--

CREATE TABLE `lop` (
  `ma_xac_thuc_lop` int(11) NOT NULL,
  `ma_lop` varchar(1000) NOT NULL,
  `ten_lop` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lop`
--

INSERT INTO `lop` (`ma_xac_thuc_lop`, `ma_lop`, `ten_lop`) VALUES
(1, 'DA22TTD', 'Công nghệ thông tin D'),
(2, 'DA22TTA', 'Công nghệ thông tin A'),
(3, 'DA22TTC', 'Công nghệ thông tin C'),
(4, 'DA22TTB', 'Công nghệ thông tin B');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_huong_dan`
--

CREATE TABLE `nguoi_huong_dan` (
  `ma_nguoi_huong_dan` varchar(10) NOT NULL,
  `ma_loai_nguoi_huong_dan` varchar(10) DEFAULT NULL,
  `ten` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `thong_tin_tai_khoan` varchar(100) DEFAULT NULL,
  `cccd` varchar(20) DEFAULT NULL,
  `chuc_vu` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_huong_dan`
--

INSERT INTO `nguoi_huong_dan` (`ma_nguoi_huong_dan`, `ma_loai_nguoi_huong_dan`, `ten`, `email`, `so_dien_thoai`, `thong_tin_tai_khoan`, `cccd`, `chuc_vu`) VALUES
('HD01', '1', 'Đặng Vinh Hiển', '', '09131392909', '', '', 'Giám Đốc'),
('HD02', '1', 'Hồ Đỗ Tiền Vàng', '', '09448441236', '', '', 'Phó Phòng'),
('HD03', '1', 'Huỳnh Sa Quang', '', '09117591344', '', '', 'Phó Hiệu Trưởng'),
('HD04', '1', 'Lê Phong Dũ', '', '091425657817', '', '', 'Phó Hiệu Trưởng'),
('HD05', '1', 'Lư Chí Thương', '', '091 95049197', '', '', 'Phó Giám Đốc'),
('HD06', '1', 'Nguyễn Phương Thanh', '', '088934911115', '', '', 'Nhân Viên'),
('HD07', '1', 'Nguyễn Thanh Tùng', '', '09649780998', '', '', 'Chuyên Viên Kỹ Thuật'),
('HD08', '1', 'Nguyễn Tuấn Vũ', '', '097274737416', '', '', 'Phó Giám Đốc'),
('HD09', '1', 'Nguyễn Văn Đệ', '', '09888111415', '', '', 'Trưởng Phòng'),
('HD10', '1', 'Nguyễn Văn Tý An', '', '037471190810', '', '', 'Nhân Viên'),
('HD11', '1', 'Nguyễn Văn Vẹn', '', '093973499713', '', '', 'Nhân Viên'),
('HD12', '1', 'Phạm Hải Thuy', '', '090920710512', '', '', 'Giám Đốc'),
('HD13', '2', 'Nguyễn Trần Diễm Hạnh', '', '', '', '', 'Phó Trưởng Bộ Môn'),
('HD14', '2', 'Nguyễn Bảo Ân', '', '', '', '', 'Phó Trưởng Bộ Môn'),
('HD15', '2', 'Nguyễn Bá Nhiệm', '', '', '', '', 'Phó Trưởng Bộ Môn'),
('HD16', '2', 'Phạm Thị Trúc Mai', '', '', '', '', 'Giảng Viên'),
('HD17', '2', 'Nguyễn Ngọc Đan Thanh', '', '', '', '', 'Giảng Viên'),
('HD18', '2', 'Đoàn Phước Miền', '', '', '', '', 'Giảng Viên'),
('HD19', '2', 'Võ Thành C', '', '', '', '', 'Giảng Viên');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quan_tri`
--

CREATE TABLE `quan_tri` (
  `id` int(11) NOT NULL,
  `ten_quan_tri` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `vai_tro` varchar(50) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `quan_tri`
--

INSERT INTO `quan_tri` (`id`, `ten_quan_tri`, `email`, `mat_khau`, `ngay_tao`, `vai_tro`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$to9tvJ8wKQLPH/LYQZtlh.SVGayCC6TvKSyG1mAULc9gVTT33HEk.', '2025-01-06 03:06:57', '1'),
(2, 'admin2', 'admin2@gmail.com', '$2y$10$Rfq6gMxcWZRj.WPfhz73De5Ow8CEnpvCdv4LNLUfmiI/CLz1Ihx7W', '2025-01-06 03:25:37', '0');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sinh_vien`
--

CREATE TABLE `sinh_vien` (
  `ma_sinh_vien` varchar(10) NOT NULL,
  `ho_ten` varchar(50) DEFAULT NULL,
  `lop` varchar(10) DEFAULT NULL,
  `khoa` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sinh_vien`
--

INSERT INTO `sinh_vien` (`ma_sinh_vien`, `ho_ten`, `lop`, `khoa`, `email`, `so_dien_thoai`) VALUES
('110122033', 'Nguyễn Hữu Anh', 'DA22TTA', '2022', 'huuanh@gmail.com', '0310122033'),
('110122068', 'Võ Chí Hải', 'DA22TTD', '2022', 'haivo@gmail.com', '0813981039'),
('110122070', 'Đỗ Gia Hào', 'DA22TTD', '2022', 'haodo@gmail.com', '0310122070'),
('110122086', 'Lê Tuấn Kha', 'DA22TTD', '2022', 'khale@gmail.com', '0979776630'),
('110122090', 'La Thuấn Khang', 'DA22TTD', '2022', 'khang@gmail.com', '0310122090'),
('110122092', 'Ngô Huỳnh Quốc Khang', 'DA22TTD', '2022', 'khangngo@gmail.com', '0310122092'),
('110122099', 'Hoàng Tuấn Kiệt', 'DA22TTD', '2022', 'kiethoang@gmail.com', '0310122099'),
('110122103', 'Hà Gia Lộc', 'DA22TTD', '2022', 'locha@gmail.com', '0310122103'),
('110122105', 'Nguyễn Đỗ Thành Lộc', 'DA22TTD', '2022', 'thanhloc@gmail.com', '0310122105'),
('110122243', 'Phạm Duy Tân', 'DA22TTD', '2022', 'tanduy@gmail.com', '0310122243');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thuc_tap`
--

CREATE TABLE `thuc_tap` (
  `ma_thuc_tap` int(10) UNSIGNED NOT NULL,
  `ma_sinh_vien` varchar(10) NOT NULL,
  `ma_dot_thuc_tap` varchar(10) NOT NULL,
  `ma_don_vi` varchar(10) NOT NULL,
  `ma_nguoi_huong_dan_don_vi` varchar(10) DEFAULT NULL,
  `ma_nguoi_huong_dan_truong` varchar(10) DEFAULT NULL,
  `nhiem_vu` text DEFAULT NULL,
  `diem_danh_gia` float DEFAULT NULL,
  `ten_de_tai_thuc_tap` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thuc_tap`
--

INSERT INTO `thuc_tap` (`ma_thuc_tap`, `ma_sinh_vien`, `ma_dot_thuc_tap`, `ma_don_vi`, `ma_nguoi_huong_dan_don_vi`, `ma_nguoi_huong_dan_truong`, `nhiem_vu`, `diem_danh_gia`, `ten_de_tai_thuc_tap`) VALUES
(26, '110122033', 'DTT01', 'DV02', '', 'HD17', '', 0, 'hh'),
(27, '110122068', 'DTT01', 'DV02', '', 'HD17', '', 0, 'hhh'),
(28, '110122070', 'DTT01', 'DV02', '', 'HD17', '', 0, 'hh'),
(29, '110122086', 'DTT01', 'DV02', '', 'HD17', '', 0, 'hh'),
(30, '110122090', 'DTT01', 'DV02', '', 'HD17', '', 0, 'hh'),
(31, '110122092', 'DTT01', 'DV02', '', 'HD17', '', 0, 'hh'),
(32, '110122099', 'DTT01', 'DV02', '', 'HD17', '', 0, 'hh'),
(33, '110122103', 'DTT01', 'DV02', '', 'HD17', '', 0, 'hh'),
(34, '110122105', 'DTT01', 'DV02', '', 'HD17', '', 0, 'hh'),
(36, '110122243', 'DTT01', 'DV02', '', 'HD17', '', 0, 'hh');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `don_vi`
--
ALTER TABLE `don_vi`
  ADD PRIMARY KEY (`ma_don_vi`);

--
-- Chỉ mục cho bảng `dot_thuc_tap`
--
ALTER TABLE `dot_thuc_tap`
  ADD PRIMARY KEY (`ma_dot_thuc_tap`);

--
-- Chỉ mục cho bảng `lop`
--
ALTER TABLE `lop`
  ADD PRIMARY KEY (`ma_xac_thuc_lop`);

--
-- Chỉ mục cho bảng `nguoi_huong_dan`
--
ALTER TABLE `nguoi_huong_dan`
  ADD PRIMARY KEY (`ma_nguoi_huong_dan`);

--
-- Chỉ mục cho bảng `quan_tri`
--
ALTER TABLE `quan_tri`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `sinh_vien`
--
ALTER TABLE `sinh_vien`
  ADD PRIMARY KEY (`ma_sinh_vien`);

--
-- Chỉ mục cho bảng `thuc_tap`
--
ALTER TABLE `thuc_tap`
  ADD PRIMARY KEY (`ma_thuc_tap`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `lop`
--
ALTER TABLE `lop`
  MODIFY `ma_xac_thuc_lop` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `quan_tri`
--
ALTER TABLE `quan_tri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `thuc_tap`
--
ALTER TABLE `thuc_tap`
  MODIFY `ma_thuc_tap` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
