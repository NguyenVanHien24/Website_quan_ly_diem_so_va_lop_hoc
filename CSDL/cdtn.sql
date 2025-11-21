-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2025 at 07:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cdtn`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `maAdmin` int(11) NOT NULL,
  `userId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bainop`
--

CREATE TABLE `bainop` (
  `maBaiNop` int(11) NOT NULL,
  `maHS` int(11) NOT NULL,
  `maTaiLieu` int(11) NOT NULL,
  `fileNop` varchar(255) DEFAULT NULL,
  `thoiGianNop` datetime DEFAULT NULL,
  `trangThai` varchar(20) DEFAULT NULL,
  `diem` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `bainop`
--
DELIMITER $$
CREATE TRIGGER `trg_bainop_after_insert` AFTER INSERT ON `bainop` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong, diaChiIP)
    VALUES (NEW.maHS, 'Nộp bài', 'BaiNop', NEW.maBaiNop, 'SYSTEM');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_bainop_delete` AFTER DELETE ON `bainop` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES (OLD.maHS, 'Xóa bài nộp', 'BaiNop', OLD.maBaiNop);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_bainop_update` AFTER UPDATE ON `bainop` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES (NEW.maHS, 'Cập nhật bài nộp', 'BaiNop', NEW.maBaiNop);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `baocao`
--

CREATE TABLE `baocao` (
  `maBaoCao` int(11) NOT NULL,
  `loaiBaoCao` varchar(100) DEFAULT NULL,
  `nguoiLapBC` varchar(255) DEFAULT NULL,
  `ngayTao` date DEFAULT NULL,
  `fileXuat` varchar(255) DEFAULT NULL,
  `namHoc` varchar(20) DEFAULT NULL,
  `hocKy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chuyencan`
--

CREATE TABLE `chuyencan` (
  `maDiemDanh` int(11) NOT NULL,
  `maHS` int(11) DEFAULT NULL,
  `maLop` int(11) DEFAULT NULL,
  `maMon` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `trangThai` varchar(255) DEFAULT NULL,
  `ghiChu` varchar(255) DEFAULT NULL,
  `ngay` datetime DEFAULT NULL,
  `hocKy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `chuyencan`
--
DELIMITER $$
CREATE TRIGGER `trg_chuyencan_after_insert` AFTER INSERT ON `chuyencan` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong, diaChiIP)
    VALUES (NEW.userId, 'Điểm danh', 'ChuyenCan', NEW.maDiemDanh, 'SYSTEM');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `diemso`
--

CREATE TABLE `diemso` (
  `maDiem` int(11) NOT NULL,
  `maHS` int(11) NOT NULL,
  `maMonHoc` int(11) DEFAULT NULL,
  `maLop` int(11) DEFAULT NULL,
  `loaiDiem` varchar(50) DEFAULT NULL,
  `giaTriDiem` float DEFAULT NULL CHECK (`giaTriDiem` between 0 and 10),
  `ngayGhiNhan` datetime DEFAULT current_timestamp(),
  `namHoc` varchar(20) DEFAULT NULL,
  `hocKy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `diemso`
--
DELIMITER $$
CREATE TRIGGER `trg_diem_after_insert` AFTER INSERT ON `diemso` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong, diaChiIP)
    VALUES (NULL, 'Ghi điểm', 'DiemSo', NEW.maDiem, 'SYSTEM');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_diem_after_update` AFTER UPDATE ON `diemso` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong, diaChiIP)
    VALUES (NULL, 'Cập nhật điểm', 'DiemSo', NEW.maDiem, 'SYSTEM');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ghilog`
--

CREATE TABLE `ghilog` (
  `maLog` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `hanhDong` varchar(255) DEFAULT NULL,
  `doiTuongTacDong` varchar(255) DEFAULT NULL,
  `maDoiTuong` int(11) DEFAULT NULL,
  `diaChiIP` varchar(255) DEFAULT NULL,
  `thoiGian` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `giaovien`
--

CREATE TABLE `giaovien` (
  `maGV` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `boMon` varchar(50) DEFAULT NULL,
  `trangThaiHoatDong` varchar(50) DEFAULT NULL,
  `namHoc` varchar(50) DEFAULT NULL,
  `kyHoc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `giaovien`
--
DELIMITER $$
CREATE TRIGGER `trg_gv_after_delete` AFTER DELETE ON `giaovien` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong, diaChiIP)
    VALUES (OLD.userId, 'Xóa giáo viên', 'GiaoVien', OLD.maGV, 'SYSTEM');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_gv_after_insert` AFTER INSERT ON `giaovien` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong, diaChiIP)
    VALUES (NEW.userId, 'Thêm giáo viên', 'GiaoVien', NEW.maGV, 'SYSTEM');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_gv_after_update` AFTER UPDATE ON `giaovien` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong, diaChiIP)
    VALUES (NEW.userId, 'Cập nhật giáo viên', 'GiaoVien', NEW.maGV, 'SYSTEM');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `hocsinh`
--

CREATE TABLE `hocsinh` (
  `maHS` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `maLopHienTai` int(11) DEFAULT NULL,
  `trangThaiHoatDong` varchar(50) DEFAULT NULL,
  `namHoc` varchar(20) DEFAULT NULL,
  `kyHoc` int(11) DEFAULT NULL,
  `chucVu` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `hocsinh`
--
DELIMITER $$
CREATE TRIGGER `trg_hs_after_delete` AFTER DELETE ON `hocsinh` FOR EACH ROW BEGIN
    IF OLD.maLopHienTai IS NOT NULL THEN
        UPDATE LopHoc
        SET siSo = siSo - 1
        WHERE maLop = OLD.maLopHienTai;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_hs_after_insert` AFTER INSERT ON `hocsinh` FOR EACH ROW BEGIN
    IF NEW.maLopHienTai IS NOT NULL THEN
        UPDATE LopHoc
        SET siSo = siSo + 1
        WHERE maLop = NEW.maLopHienTai;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_hs_after_update` AFTER UPDATE ON `hocsinh` FOR EACH ROW BEGIN
    IF OLD.maLopHienTai <> NEW.maLopHienTai THEN
        -- Trừ lớp cũ
        IF OLD.maLopHienTai IS NOT NULL THEN
            UPDATE LopHoc SET siSo = siSo - 1 WHERE maLop = OLD.maLopHienTai;
        END IF;

        -- Cộng lớp mới
        IF NEW.maLopHienTai IS NOT NULL THEN
            UPDATE LopHoc SET siSo = siSo + 1 WHERE maLop = NEW.maLopHienTai;
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_hs_delete` AFTER DELETE ON `hocsinh` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES (OLD.userId, 'Xóa học sinh', 'HocSinh', OLD.maHS);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_hs_insert` AFTER INSERT ON `hocsinh` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES (NEW.userId, 'Thêm học sinh', 'HocSinh', NEW.maHS);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_hs_update` AFTER UPDATE ON `hocsinh` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES (NEW.userId, 'Cập nhật học sinh', 'HocSinh', NEW.maHS);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `lophoc`
--

CREATE TABLE `lophoc` (
  `maLop` int(11) NOT NULL,
  `tenLop` varchar(50) NOT NULL,
  `khoiLop` varchar(10) DEFAULT NULL,
  `giaoVienPhuTrach` int(11) DEFAULT NULL,
  `siSo` int(11) DEFAULT NULL,
  `trangThai` varchar(20) DEFAULT NULL,
  `namHoc` varchar(20) DEFAULT NULL,
  `kyHoc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `lophoc`
--
DELIMITER $$
CREATE TRIGGER `trg_lophoc_delete` AFTER DELETE ON `lophoc` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES ('Xóa lớp học', 'LopHoc', OLD.maLop);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_lophoc_insert` AFTER INSERT ON `lophoc` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES ('Thêm lớp học', 'LopHoc', NEW.maLop);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_lophoc_update` AFTER UPDATE ON `lophoc` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES ('Cập nhật lớp học', 'LopHoc', NEW.maLop);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `monhoc`
--

CREATE TABLE `monhoc` (
  `maMon` int(11) NOT NULL,
  `tenMon` varchar(100) DEFAULT NULL,
  `moTa` varchar(255) DEFAULT NULL,
  `namHoc` varchar(20) DEFAULT NULL,
  `hocKy` int(11) DEFAULT NULL,
  `truongBoMon` varchar(50) DEFAULT NULL,
  `trangThai` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `monhoc`
--
DELIMITER $$
CREATE TRIGGER `trg_monhoc_delete` AFTER DELETE ON `monhoc` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES ('Xóa môn học', 'MonHoc', OLD.maMon);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_monhoc_insert` AFTER INSERT ON `monhoc` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES ('Thêm môn học', 'MonHoc', NEW.maMon);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_monhoc_update` AFTER UPDATE ON `monhoc` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES ('Cập nhật môn học', 'MonHoc', NEW.maMon);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tailieu`
--

CREATE TABLE `tailieu` (
  `maTaiLieu` int(11) NOT NULL,
  `maMon` int(11) NOT NULL,
  `tieuDe` varchar(255) DEFAULT NULL,
  `moTa` varchar(255) DEFAULT NULL,
  `fileTL` varchar(255) DEFAULT NULL,
  `ngayTao` datetime DEFAULT current_timestamp(),
  `hanNop` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `tailieu`
--
DELIMITER $$
CREATE TRIGGER `trg_tailieu_delete` AFTER DELETE ON `tailieu` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES ('Xóa tài liệu', 'TaiLieu', OLD.maTaiLieu);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tailieu_insert` AFTER INSERT ON `tailieu` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES ('Thêm tài liệu', 'TaiLieu', NEW.maTaiLieu);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tailieu_update` AFTER UPDATE ON `tailieu` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES ('Cập nhật tài liệu', 'TaiLieu', NEW.maTaiLieu);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `thongbao`
--

CREATE TABLE `thongbao` (
  `maThongBao` int(11) NOT NULL,
  `tieuDe` varchar(255) DEFAULT NULL,
  `noiDung` text DEFAULT NULL,
  `ngayGui` datetime DEFAULT current_timestamp(),
  `nguoiGui` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `thongbao`
--
DELIMITER $$
CREATE TRIGGER `trg_thongbao_after_insert` AFTER INSERT ON `thongbao` FOR EACH ROW BEGIN
    INSERT INTO ThongBaoUser(maTB, userId)
    SELECT NEW.maThongBao, userId FROM User;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `thongbaouser`
--

CREATE TABLE `thongbaouser` (
  `id` int(11) NOT NULL,
  `maTB` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `ngayNhan` datetime DEFAULT current_timestamp(),
  `trangThai` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userId` int(11) NOT NULL,
  `matKhau` varchar(255) NOT NULL,
  `hoVaTen` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `gioiTinh` varchar(10) DEFAULT NULL,
  `trangThai` tinyint(1) DEFAULT 1,
  `vaiTro` enum('Admin','GiaoVien','HocSinh') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `user`
--
DELIMITER $$
CREATE TRIGGER `trg_user_after_delete` AFTER DELETE ON `user` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong, diaChiIP)
    VALUES (OLD.userId, 'Xóa tài khoản', 'User', OLD.userId, 'SYSTEM');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_user_after_insert` AFTER INSERT ON `user` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong, diaChiIP)
    VALUES (NEW.userId, 'Tạo tài khoản', 'User', NEW.userId, 'SYSTEM');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_user_after_update` AFTER UPDATE ON `user` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(userId, hanhDong, doiTuongTacDong, maDoiTuong, diaChiIP)
    VALUES (NEW.userId, 'Cập nhật tài khoản', 'User', NEW.userId, 'SYSTEM');
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`maAdmin`),
  ADD UNIQUE KEY `userId` (`userId`);

--
-- Indexes for table `bainop`
--
ALTER TABLE `bainop`
  ADD PRIMARY KEY (`maBaiNop`),
  ADD KEY `maHS` (`maHS`),
  ADD KEY `maTaiLieu` (`maTaiLieu`);

--
-- Indexes for table `baocao`
--
ALTER TABLE `baocao`
  ADD PRIMARY KEY (`maBaoCao`);

--
-- Indexes for table `chuyencan`
--
ALTER TABLE `chuyencan`
  ADD PRIMARY KEY (`maDiemDanh`),
  ADD KEY `maHS` (`maHS`),
  ADD KEY `maLop` (`maLop`),
  ADD KEY `maMon` (`maMon`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `diemso`
--
ALTER TABLE `diemso`
  ADD PRIMARY KEY (`maDiem`),
  ADD KEY `maHS` (`maHS`),
  ADD KEY `maMonHoc` (`maMonHoc`),
  ADD KEY `maLop` (`maLop`);

--
-- Indexes for table `ghilog`
--
ALTER TABLE `ghilog`
  ADD PRIMARY KEY (`maLog`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `giaovien`
--
ALTER TABLE `giaovien`
  ADD PRIMARY KEY (`maGV`),
  ADD UNIQUE KEY `userId` (`userId`);

--
-- Indexes for table `hocsinh`
--
ALTER TABLE `hocsinh`
  ADD PRIMARY KEY (`maHS`),
  ADD UNIQUE KEY `userId` (`userId`),
  ADD KEY `maLopHienTai` (`maLopHienTai`);

--
-- Indexes for table `lophoc`
--
ALTER TABLE `lophoc`
  ADD PRIMARY KEY (`maLop`),
  ADD KEY `giaoVienPhuTrach` (`giaoVienPhuTrach`);

--
-- Indexes for table `monhoc`
--
ALTER TABLE `monhoc`
  ADD PRIMARY KEY (`maMon`),
  ADD UNIQUE KEY `tenMon` (`tenMon`);

--
-- Indexes for table `tailieu`
--
ALTER TABLE `tailieu`
  ADD PRIMARY KEY (`maTaiLieu`),
  ADD KEY `maMon` (`maMon`);

--
-- Indexes for table `thongbao`
--
ALTER TABLE `thongbao`
  ADD PRIMARY KEY (`maThongBao`),
  ADD KEY `nguoiGui` (`nguoiGui`);

--
-- Indexes for table `thongbaouser`
--
ALTER TABLE `thongbaouser`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maTB` (`maTB`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bainop`
--
ALTER TABLE `bainop`
  MODIFY `maBaiNop` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `baocao`
--
ALTER TABLE `baocao`
  MODIFY `maBaoCao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chuyencan`
--
ALTER TABLE `chuyencan`
  MODIFY `maDiemDanh` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `diemso`
--
ALTER TABLE `diemso`
  MODIFY `maDiem` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ghilog`
--
ALTER TABLE `ghilog`
  MODIFY `maLog` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lophoc`
--
ALTER TABLE `lophoc`
  MODIFY `maLop` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `monhoc`
--
ALTER TABLE `monhoc`
  MODIFY `maMon` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tailieu`
--
ALTER TABLE `tailieu`
  MODIFY `maTaiLieu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `thongbao`
--
ALTER TABLE `thongbao`
  MODIFY `maThongBao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `thongbaouser`
--
ALTER TABLE `thongbaouser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE;

--
-- Constraints for table `bainop`
--
ALTER TABLE `bainop`
  ADD CONSTRAINT `bainop_ibfk_1` FOREIGN KEY (`maHS`) REFERENCES `hocsinh` (`maHS`),
  ADD CONSTRAINT `bainop_ibfk_2` FOREIGN KEY (`maTaiLieu`) REFERENCES `tailieu` (`maTaiLieu`);

--
-- Constraints for table `chuyencan`
--
ALTER TABLE `chuyencan`
  ADD CONSTRAINT `chuyencan_ibfk_1` FOREIGN KEY (`maHS`) REFERENCES `hocsinh` (`maHS`),
  ADD CONSTRAINT `chuyencan_ibfk_2` FOREIGN KEY (`maLop`) REFERENCES `lophoc` (`maLop`),
  ADD CONSTRAINT `chuyencan_ibfk_3` FOREIGN KEY (`maMon`) REFERENCES `monhoc` (`maMon`),
  ADD CONSTRAINT `chuyencan_ibfk_4` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE SET NULL;

--
-- Constraints for table `diemso`
--
ALTER TABLE `diemso`
  ADD CONSTRAINT `diemso_ibfk_1` FOREIGN KEY (`maHS`) REFERENCES `hocsinh` (`maHS`) ON DELETE CASCADE,
  ADD CONSTRAINT `diemso_ibfk_2` FOREIGN KEY (`maMonHoc`) REFERENCES `monhoc` (`maMon`),
  ADD CONSTRAINT `diemso_ibfk_3` FOREIGN KEY (`maLop`) REFERENCES `lophoc` (`maLop`);

--
-- Constraints for table `ghilog`
--
ALTER TABLE `ghilog`
  ADD CONSTRAINT `ghilog_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE SET NULL;

--
-- Constraints for table `giaovien`
--
ALTER TABLE `giaovien`
  ADD CONSTRAINT `giaovien_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE;

--
-- Constraints for table `hocsinh`
--
ALTER TABLE `hocsinh`
  ADD CONSTRAINT `hocsinh_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE,
  ADD CONSTRAINT `hocsinh_ibfk_2` FOREIGN KEY (`maLopHienTai`) REFERENCES `lophoc` (`maLop`);

--
-- Constraints for table `lophoc`
--
ALTER TABLE `lophoc`
  ADD CONSTRAINT `lophoc_ibfk_1` FOREIGN KEY (`giaoVienPhuTrach`) REFERENCES `giaovien` (`maGV`);

--
-- Constraints for table `tailieu`
--
ALTER TABLE `tailieu`
  ADD CONSTRAINT `tailieu_ibfk_1` FOREIGN KEY (`maMon`) REFERENCES `monhoc` (`maMon`);

--
-- Constraints for table `thongbao`
--
ALTER TABLE `thongbao`
  ADD CONSTRAINT `thongbao_ibfk_1` FOREIGN KEY (`nguoiGui`) REFERENCES `user` (`userId`);

--
-- Constraints for table `thongbaouser`
--
ALTER TABLE `thongbaouser`
  ADD CONSTRAINT `thongbaouser_ibfk_1` FOREIGN KEY (`maTB`) REFERENCES `thongbao` (`maThongBao`) ON DELETE CASCADE,
  ADD CONSTRAINT `thongbaouser_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
