-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 07, 2025 at 04:09 AM
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

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`maAdmin`, `userId`) VALUES
(0, 1);

--
-- Triggers `admin`
--
DELIMITER $$
CREATE TRIGGER `trg_admin_delete` AFTER DELETE ON `admin` FOR EACH ROW BEGIN
    INSERT INTO ghilog(userId, hanhDong, doiTuongTacDong, maDoiTuong, diaChiIP)
    VALUES (OLD.userId, 'Xóa admin', 'Admin', OLD.maAdmin, 'SYSTEM');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_admin_insert` AFTER INSERT ON `admin` FOR EACH ROW BEGIN
    INSERT INTO ghilog(userId, hanhDong, doiTuongTacDong, maDoiTuong, diaChiIP)
    VALUES (NEW.userId, 'Thêm admin', 'Admin', NEW.maAdmin, 'SYSTEM');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_admin_update` AFTER UPDATE ON `admin` FOR EACH ROW BEGIN
    INSERT INTO ghilog(userId, hanhDong, doiTuongTacDong, maDoiTuong, diaChiIP)
    VALUES (NEW.userId, 'Cập nhật admin', 'Admin', NEW.maAdmin, 'SYSTEM');
END
$$
DELIMITER ;

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
CREATE TRIGGER `trg_bainop_check_deadline` BEFORE INSERT ON `bainop` FOR EACH ROW BEGIN
    DECLARE deadline DATETIME;

    SELECT hanNop
    INTO deadline
    FROM tailieu
    WHERE maTaiLieu = NEW.maTaiLieu;

    IF deadline IS NOT NULL AND NOW() > deadline THEN
        SET NEW.trangThai = 'Nộp trễ';
    END IF;
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
DELIMITER $$
CREATE TRIGGER `trg_bainop_update_status` BEFORE INSERT ON `bainop` FOR EACH ROW BEGIN
    SET NEW.trangThai = 'Đã nộp';
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
  `nguoiLapBC` int(11) DEFAULT NULL,
  `ngayTao` date DEFAULT NULL,
  `fileXuat` varchar(255) DEFAULT NULL,
  `namHoc` varchar(20) DEFAULT NULL,
  `hocKy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `baocao`
--
DELIMITER $$
CREATE TRIGGER `trg_baocao_delete` AFTER DELETE ON `baocao` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hoatDong, thoiGian)
    VALUES (CONCAT('Xóa báo cáo ID ', OLD.maBaoCao), NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_baocao_insert` AFTER INSERT ON `baocao` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hoatDong, thoiGian)
    VALUES (CONCAT('Thêm báo cáo ID ', NEW.maBaoCao), NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_baocao_update` AFTER UPDATE ON `baocao` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hoatDong, thoiGian)
    VALUES (CONCAT('Cập nhật báo cáo ID ', OLD.maBaoCao), NOW());
END
$$
DELIMITER ;

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
DELIMITER $$
CREATE TRIGGER `trg_chuyencan_delete` AFTER DELETE ON `chuyencan` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hanhDong, thoiGian)
    VALUES (CONCAT('Xóa chuyên cần ID ', OLD.userID), NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_chuyencan_update` AFTER UPDATE ON `chuyencan` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hoatDong, thoiGian)
    VALUES (CONCAT('Cập nhật chuyên cần ID ', OLD.userID), NOW());
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
-- Dumping data for table `diemso`
--

INSERT INTO `diemso` (`maDiem`, `maHS`, `maMonHoc`, `maLop`, `loaiDiem`, `giaTriDiem`, `ngayGhiNhan`, `namHoc`, `hocKy`) VALUES
(173, 1, 3, 12, 'Điểm miệng', 7, '2025-12-06 21:50:51', '2025-2026', 1),
(174, 1, 3, 12, 'Điểm 1 tiết', 8, '2025-12-06 21:50:51', '2025-2026', 1),
(175, 1, 3, 12, 'Điểm giữa kỳ', 8, '2025-12-06 21:50:51', '2025-2026', 1),
(176, 1, 3, 12, 'Điểm cuối kỳ', 8, '2025-12-06 21:50:51', '2025-2026', 1);

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
DELIMITER $$
CREATE TRIGGER `trg_diemso_delete` AFTER DELETE ON `diemso` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hanhDong, thoiGian)
    VALUES (CONCAT('Xóa điểm ID ', OLD.maHS), NOW());
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

--
-- Dumping data for table `ghilog`
--

INSERT INTO `ghilog` (`maLog`, `userId`, `hanhDong`, `doiTuongTacDong`, `maDoiTuong`, `diaChiIP`, `thoiGian`) VALUES
(1, NULL, 'Tạo tài khoản', 'User', 1, 'SYSTEM', '2025-11-22 11:24:08'),
(4, NULL, 'Cập nhật tài khoản', 'User', 1, 'SYSTEM', '2025-11-22 11:28:54'),
(5, NULL, 'Cập nhật tài khoản', 'User', 1, 'SYSTEM', '2025-11-22 11:29:10'),
(7, NULL, 'Xóa tài khoản', 'User', 1, 'SYSTEM', '2025-11-22 11:42:59'),
(8, 1, 'Tạo tài khoản', 'User', 1, 'SYSTEM', '2025-11-22 11:43:36'),
(9, 1, 'Thêm admin', 'Admin', 0, 'SYSTEM', '2025-11-22 11:43:36'),
(12, 2, 'Tạo tài khoản', 'User', 2, 'SYSTEM', '2025-11-22 11:54:15'),
(13, 2, 'Thêm giáo viên', 'GiaoVien', 0, 'SYSTEM', '2025-11-22 11:54:15'),
(14, 3, 'Tạo tài khoản', 'User', 3, 'SYSTEM', '2025-11-22 11:55:10'),
(15, 3, 'Thêm học sinh', 'HocSinh', 0, NULL, '2025-11-22 11:55:10'),
(16, 1, 'Cập nhật tài khoản', 'User', 1, 'SYSTEM', '2025-11-22 12:23:02'),
(17, 1, 'Cập nhật tài khoản', 'User', 1, 'SYSTEM', '2025-11-22 12:24:43'),
(18, 1, 'Cập nhật tài khoản', 'User', 1, 'SYSTEM', '2025-11-22 12:25:28'),
(22, NULL, 'Tạo tài khoản', 'User', 7, 'SYSTEM', '2025-11-23 00:34:28'),
(23, NULL, 'Thêm giáo viên', 'GiaoVien', 2, 'SYSTEM', '2025-11-23 00:34:28'),
(24, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-23 01:10:07'),
(25, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-23 01:10:07'),
(26, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-23 01:14:30'),
(27, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-23 01:14:30'),
(28, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-23 01:14:47'),
(29, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-23 01:14:47'),
(30, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-23 01:14:56'),
(31, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-23 01:14:56'),
(32, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-23 01:15:06'),
(33, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-23 01:15:06'),
(35, NULL, 'Xóa tài khoản', 'User', 7, 'SYSTEM', '2025-11-23 01:21:36'),
(37, 3, 'Cập nhật tài khoản', 'User', 3, 'SYSTEM', '2025-11-23 10:55:26'),
(38, 3, 'Cập nhật học sinh', 'HocSinh', 0, NULL, '2025-11-23 10:55:27'),
(39, 3, 'Cập nhật tài khoản', 'User', 3, 'SYSTEM', '2025-11-23 10:55:36'),
(40, 3, 'Cập nhật học sinh', 'HocSinh', 0, NULL, '2025-11-23 10:55:36'),
(48, NULL, 'Tạo tài khoản', 'User', 17, 'SYSTEM', '2025-11-23 12:13:58'),
(49, NULL, 'Thêm học sinh', 'HocSinh', 2, NULL, '2025-11-23 12:13:58'),
(50, NULL, 'Cập nhật tài khoản', 'User', 17, 'SYSTEM', '2025-11-23 12:14:09'),
(51, NULL, 'Cập nhật học sinh', 'HocSinh', 2, NULL, '2025-11-23 12:14:10'),
(52, NULL, 'Cập nhật tài khoản', 'User', 17, 'SYSTEM', '2025-11-23 12:14:16'),
(53, NULL, 'Cập nhật học sinh', 'HocSinh', 2, NULL, '2025-11-23 12:14:17'),
(54, NULL, 'Cập nhật tài khoản', 'User', 17, 'SYSTEM', '2025-11-23 12:14:23'),
(55, NULL, 'Cập nhật học sinh', 'HocSinh', 2, NULL, '2025-11-23 12:14:23'),
(56, NULL, 'Xóa học sinh', 'HocSinh', 2, NULL, '2025-11-23 12:14:58'),
(61, NULL, 'Thêm lớp học', 'LopHoc', 5, NULL, '2025-11-24 22:44:43'),
(62, NULL, 'Tạo lớp 5 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-24 22:44:43'),
(63, NULL, 'Thêm lớp học', 'LopHoc', 6, NULL, '2025-11-24 22:46:50'),
(64, NULL, 'Tạo lớp 6 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-24 22:46:50'),
(65, NULL, 'Cập nhật lớp học', 'LopHoc', 5, NULL, '2025-11-24 22:47:06'),
(66, NULL, 'Xóa lớp học', 'LopHoc', 5, NULL, '2025-11-24 22:48:23'),
(67, NULL, 'Xóa lớp học', 'LopHoc', 6, NULL, '2025-11-24 22:50:38'),
(68, NULL, 'Thêm lớp học', 'LopHoc', 1, NULL, '2025-11-24 22:51:52'),
(69, NULL, 'Tạo lớp 1 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-24 22:51:52'),
(70, NULL, 'Thêm lớp học', 'LopHoc', 2, NULL, '2025-11-24 22:51:52'),
(71, NULL, 'Tạo lớp 2 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-24 22:51:52'),
(72, NULL, 'Thêm lớp học', 'LopHoc', 3, NULL, '2025-11-24 22:52:19'),
(73, NULL, 'Tạo lớp 3 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-24 22:52:19'),
(74, NULL, 'Thêm lớp học', 'LopHoc', 4, NULL, '2025-11-24 22:52:19'),
(75, NULL, 'Tạo lớp 4 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-24 22:52:19'),
(76, NULL, 'Thêm lớp học', 'LopHoc', 5, NULL, '2025-11-24 22:52:42'),
(77, NULL, 'Tạo lớp 5 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-24 22:52:42'),
(78, NULL, 'Thêm lớp học', 'LopHoc', 6, NULL, '2025-11-24 22:52:42'),
(79, NULL, 'Tạo lớp 6 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-24 22:52:42'),
(80, NULL, 'Cập nhật lớp học', 'LopHoc', 6, NULL, '2025-11-24 22:53:00'),
(81, NULL, 'Cập nhật lớp học', 'LopHoc', 6, NULL, '2025-11-24 22:53:10'),
(82, NULL, 'Cập nhật lớp học', 'LopHoc', 6, NULL, '2025-11-24 22:53:23'),
(83, NULL, 'Thêm lớp học', 'LopHoc', 7, NULL, '2025-11-24 22:54:50'),
(84, NULL, 'Tạo lớp 7 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-24 22:54:50'),
(85, NULL, 'Cập nhật lớp học', 'LopHoc', 6, NULL, '2025-11-24 22:58:53'),
(86, NULL, 'Cập nhật lớp học', 'LopHoc', 6, NULL, '2025-11-24 22:59:06'),
(87, NULL, 'Cập nhật lớp học', 'LopHoc', 5, NULL, '2025-11-24 22:59:10'),
(88, NULL, 'Cập nhật lớp học', 'LopHoc', 4, NULL, '2025-11-24 22:59:16'),
(89, NULL, 'Cập nhật lớp học', 'LopHoc', 3, NULL, '2025-11-24 22:59:20'),
(90, NULL, 'Cập nhật lớp học', 'LopHoc', 1, NULL, '2025-11-24 22:59:26'),
(91, NULL, 'Cập nhật lớp học', 'LopHoc', 5, NULL, '2025-11-24 22:59:40'),
(92, NULL, 'Cập nhật lớp học', 'LopHoc', 4, NULL, '2025-11-24 22:59:53'),
(93, NULL, 'Cập nhật lớp học', 'LopHoc', 3, NULL, '2025-11-24 22:59:59'),
(94, NULL, 'Cập nhật lớp học', 'LopHoc', 2, NULL, '2025-11-24 23:00:06'),
(95, NULL, 'Cập nhật lớp học', 'LopHoc', 1, NULL, '2025-11-24 23:00:13'),
(96, NULL, 'Cập nhật lớp học', 'LopHoc', 1, NULL, '2025-11-24 23:01:03'),
(97, NULL, 'Cập nhật lớp học', 'LopHoc', 7, NULL, '2025-11-24 23:02:49'),
(98, NULL, 'Cập nhật lớp học', 'LopHoc', 7, NULL, '2025-11-24 23:02:59'),
(99, NULL, 'Cập nhật lớp học', 'LopHoc', 7, NULL, '2025-11-24 23:05:34'),
(100, NULL, 'Cập nhật lớp học', 'LopHoc', 7, NULL, '2025-11-24 23:09:41'),
(101, NULL, 'Cập nhật lớp học', 'LopHoc', 7, NULL, '2025-11-24 23:09:54'),
(102, NULL, 'Cập nhật lớp học', 'LopHoc', 7, NULL, '2025-11-24 23:10:03'),
(103, NULL, 'Cập nhật lớp học', 'LopHoc', 7, NULL, '2025-11-24 23:10:09'),
(104, NULL, 'Xóa lớp học', 'LopHoc', 5, NULL, '2025-11-24 23:10:30'),
(105, NULL, 'Xóa lớp học', 'LopHoc', 6, NULL, '2025-11-24 23:10:30'),
(106, NULL, 'Xóa lớp học', 'LopHoc', 4, NULL, '2025-11-24 23:15:49'),
(107, NULL, 'Thêm môn học', 'MonHoc', 1, NULL, '2025-11-24 23:30:01'),
(108, NULL, 'Cập nhật môn học', 'MonHoc', 1, NULL, '2025-11-24 23:36:41'),
(109, NULL, 'Cập nhật môn học', 'MonHoc', 1, NULL, '2025-11-24 23:36:50'),
(110, NULL, 'Cập nhật môn học', 'MonHoc', 1, NULL, '2025-11-24 23:37:31'),
(111, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:40:00'),
(112, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-24 23:40:01'),
(113, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:41:42'),
(114, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-24 23:41:43'),
(115, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:45:45'),
(116, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-24 23:45:45'),
(117, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:46:07'),
(118, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-24 23:46:07'),
(119, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:49:02'),
(120, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:50:45'),
(121, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:50:46'),
(122, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:50:47'),
(123, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:52:30'),
(124, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-24 23:52:30'),
(125, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:52:44'),
(126, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-24 23:52:45'),
(127, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:52:59'),
(128, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-24 23:52:59'),
(129, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:53:11'),
(130, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-24 23:53:11'),
(131, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:57:00'),
(132, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-24 23:57:00'),
(133, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:57:17'),
(134, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-24 23:57:17'),
(135, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-24 23:57:29'),
(136, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-24 23:57:29'),
(137, 18, 'Tạo tài khoản', 'User', 18, 'SYSTEM', '2025-11-25 00:03:11'),
(138, 18, 'Thêm giáo viên', 'GiaoVien', 3, 'SYSTEM', '2025-11-25 00:03:11'),
(139, 18, 'Cập nhật tài khoản', 'User', 18, 'SYSTEM', '2025-11-25 00:03:19'),
(140, 18, 'Cập nhật tài khoản', 'User', 18, 'SYSTEM', '2025-11-25 00:05:38'),
(141, 18, 'Cập nhật giáo viên', 'GiaoVien', 3, 'SYSTEM', '2025-11-25 00:05:38'),
(142, 18, 'Cập nhật tài khoản', 'User', 18, 'SYSTEM', '2025-11-25 00:05:46'),
(143, 18, 'Cập nhật giáo viên', 'GiaoVien', 3, 'SYSTEM', '2025-11-25 00:05:46'),
(144, 18, 'Cập nhật tài khoản', 'User', 18, 'SYSTEM', '2025-11-25 00:07:49'),
(145, 18, 'Cập nhật giáo viên', 'GiaoVien', 3, 'SYSTEM', '2025-11-25 00:07:49'),
(146, 18, 'Cập nhật tài khoản', 'User', 18, 'SYSTEM', '2025-11-25 00:07:53'),
(147, 18, 'Cập nhật giáo viên', 'GiaoVien', 3, 'SYSTEM', '2025-11-25 00:07:53'),
(148, 3, 'Cập nhật tài khoản', 'User', 3, 'SYSTEM', '2025-11-25 00:07:59'),
(149, 3, 'Cập nhật học sinh', 'HocSinh', 1, NULL, '2025-11-25 00:07:59'),
(150, 3, 'Cập nhật tài khoản', 'User', 3, 'SYSTEM', '2025-11-25 00:08:04'),
(151, 3, 'Cập nhật học sinh', 'HocSinh', 1, NULL, '2025-11-25 00:08:04'),
(152, 3, 'Cập nhật tài khoản', 'User', 3, 'SYSTEM', '2025-11-25 00:10:00'),
(153, 3, 'Cập nhật học sinh', 'HocSinh', 1, NULL, '2025-11-25 00:10:00'),
(154, NULL, 'Xóa tài khoản', 'User', 17, 'SYSTEM', '2025-11-25 23:24:57'),
(155, 20, 'Tạo tài khoản', 'User', 20, 'SYSTEM', '2025-11-25 23:25:07'),
(156, 20, 'Thêm học sinh', 'HocSinh', 4, NULL, '2025-11-25 23:25:07'),
(157, 20, 'Cập nhật tài khoản', 'User', 20, 'SYSTEM', '2025-11-25 23:25:13'),
(158, 20, 'Cập nhật học sinh', 'HocSinh', 4, NULL, '2025-11-25 23:25:13'),
(159, 21, 'Tạo tài khoản', 'User', 21, 'SYSTEM', '2025-11-25 23:25:28'),
(160, 21, 'Thêm học sinh', 'HocSinh', 6, NULL, '2025-11-25 23:25:28'),
(161, 21, 'Cập nhật tài khoản', 'User', 21, 'SYSTEM', '2025-11-25 23:26:21'),
(162, 21, 'Cập nhật học sinh', 'HocSinh', 6, NULL, '2025-11-25 23:26:21'),
(163, 22, 'Tạo tài khoản', 'User', 22, 'SYSTEM', '2025-11-25 23:31:28'),
(164, 22, 'Thêm học sinh', 'HocSinh', 8, NULL, '2025-11-25 23:31:28'),
(165, 22, 'Cập nhật học sinh', 'HocSinh', 8, NULL, '2025-11-25 23:31:28'),
(166, 21, 'Cập nhật tài khoản', 'User', 21, 'SYSTEM', '2025-11-25 23:31:37'),
(167, 21, 'Cập nhật học sinh', 'HocSinh', 6, NULL, '2025-11-25 23:31:37'),
(168, 21, 'Cập nhật tài khoản', 'User', 21, 'SYSTEM', '2025-11-25 23:31:53'),
(169, 22, 'Cập nhật tài khoản', 'User', 22, 'SYSTEM', '2025-11-25 23:31:57'),
(170, 23, 'Tạo tài khoản', 'User', 23, 'SYSTEM', '2025-11-25 23:32:41'),
(171, 23, 'Thêm học sinh', 'HocSinh', 9, NULL, '2025-11-25 23:32:41'),
(172, 23, 'Cập nhật học sinh', 'HocSinh', 9, NULL, '2025-11-25 23:32:42'),
(173, 24, 'Tạo tài khoản', 'User', 24, 'SYSTEM', '2025-11-25 23:37:06'),
(174, 24, 'Thêm học sinh', 'HocSinh', 10, NULL, '2025-11-25 23:37:06'),
(175, 24, 'Cập nhật học sinh', 'HocSinh', 10, NULL, '2025-11-25 23:37:06'),
(176, 21, 'Cập nhật tài khoản', 'User', 21, 'SYSTEM', '2025-11-25 23:37:21'),
(177, 21, 'Cập nhật học sinh', 'HocSinh', 6, NULL, '2025-11-25 23:37:21'),
(178, 21, 'Cập nhật tài khoản', 'User', 21, 'SYSTEM', '2025-11-25 23:37:25'),
(179, 21, 'Cập nhật học sinh', 'HocSinh', 6, NULL, '2025-11-25 23:37:25'),
(180, 23, 'Cập nhật tài khoản', 'User', 23, 'SYSTEM', '2025-11-25 23:37:41'),
(181, 23, 'Cập nhật học sinh', 'HocSinh', 9, NULL, '2025-11-25 23:37:41'),
(182, 24, 'Cập nhật tài khoản', 'User', 24, 'SYSTEM', '2025-11-25 23:37:51'),
(183, 24, 'Cập nhật học sinh', 'HocSinh', 10, NULL, '2025-11-25 23:37:51'),
(184, 26, 'Tạo tài khoản', 'User', 26, 'SYSTEM', '2025-11-25 23:38:11'),
(185, 26, 'Thêm học sinh', 'HocSinh', 11, NULL, '2025-11-25 23:38:11'),
(186, 26, 'Cập nhật học sinh', 'HocSinh', 11, NULL, '2025-11-25 23:38:11'),
(187, 27, 'Tạo tài khoản', 'User', 27, 'SYSTEM', '2025-11-25 23:40:19'),
(188, 27, 'Thêm học sinh', 'HocSinh', 12, NULL, '2025-11-25 23:40:19'),
(189, 27, 'Cập nhật học sinh', 'HocSinh', 12, NULL, '2025-11-25 23:40:19'),
(190, 27, 'Cập nhật tài khoản', 'User', 27, 'SYSTEM', '2025-11-25 23:40:28'),
(191, 27, 'Cập nhật học sinh', 'HocSinh', 12, NULL, '2025-11-25 23:40:28'),
(192, 27, 'Cập nhật tài khoản', 'User', 27, 'SYSTEM', '2025-11-25 23:41:41'),
(193, 27, 'Cập nhật học sinh', 'HocSinh', 12, NULL, '2025-11-25 23:41:41'),
(194, 18, 'Cập nhật tài khoản', 'User', 18, 'SYSTEM', '2025-11-25 23:52:48'),
(195, 18, 'Cập nhật giáo viên', 'GiaoVien', 3, 'SYSTEM', '2025-11-25 23:52:48'),
(196, 28, 'Tạo tài khoản', 'User', 28, 'SYSTEM', '2025-11-26 00:36:46'),
(197, 28, 'Thêm giáo viên', 'GiaoVien', 5, 'SYSTEM', '2025-11-26 00:36:46'),
(198, 28, 'Cập nhật tài khoản', 'User', 28, 'SYSTEM', '2025-11-26 00:36:54'),
(199, 28, 'Cập nhật giáo viên', 'GiaoVien', 5, 'SYSTEM', '2025-11-26 00:36:54'),
(200, NULL, 'Thêm môn học', 'MonHoc', 2, NULL, '2025-11-26 00:37:21'),
(201, NULL, 'Thêm môn học', 'MonHoc', 3, NULL, '2025-11-26 00:37:31'),
(202, NULL, 'Thêm môn học', 'MonHoc', 4, NULL, '2025-11-26 00:37:44'),
(203, NULL, 'Thêm môn học', 'MonHoc', 5, NULL, '2025-11-26 00:37:54'),
(204, NULL, 'Cập nhật môn học', 'MonHoc', 2, NULL, '2025-11-26 00:39:02'),
(205, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 00:43:30'),
(206, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-26 00:43:30'),
(207, 18, 'Cập nhật tài khoản', 'User', 18, 'SYSTEM', '2025-11-26 00:43:37'),
(208, 18, 'Cập nhật giáo viên', 'GiaoVien', 3, 'SYSTEM', '2025-11-26 00:43:37'),
(209, 28, 'Cập nhật tài khoản', 'User', 28, 'SYSTEM', '2025-11-26 00:43:43'),
(210, 28, 'Cập nhật giáo viên', 'GiaoVien', 5, 'SYSTEM', '2025-11-26 00:43:43'),
(211, NULL, 'Cập nhật môn học', 'MonHoc', 2, NULL, '2025-11-26 00:43:57'),
(212, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 00:56:33'),
(213, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-26 00:56:33'),
(214, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 00:56:41'),
(215, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-26 00:56:41'),
(216, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 00:57:15'),
(217, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-26 00:57:15'),
(218, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 00:58:18'),
(219, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-26 00:58:18'),
(220, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 00:59:00'),
(221, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-26 00:59:00'),
(222, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 00:59:05'),
(223, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-26 00:59:05'),
(224, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 01:07:21'),
(226, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 01:07:29'),
(228, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 01:07:44'),
(230, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 01:08:26'),
(232, 18, 'Cập nhật tài khoản', 'User', 18, 'SYSTEM', '2025-11-26 01:08:56'),
(234, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 01:13:05'),
(235, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-26 01:13:06'),
(236, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 01:13:14'),
(237, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-26 01:13:14'),
(238, 18, 'Cập nhật tài khoản', 'User', 18, 'SYSTEM', '2025-11-26 01:13:19'),
(239, 18, 'Cập nhật giáo viên', 'GiaoVien', 3, 'SYSTEM', '2025-11-26 01:13:19'),
(240, 28, 'Cập nhật tài khoản', 'User', 28, 'SYSTEM', '2025-11-26 01:13:25'),
(241, 28, 'Cập nhật giáo viên', 'GiaoVien', 5, 'SYSTEM', '2025-11-26 01:13:25'),
(242, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 01:15:14'),
(243, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-26 01:15:14'),
(244, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 01:15:22'),
(245, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-26 01:15:22'),
(246, 2, 'Cập nhật tài khoản', 'User', 2, 'SYSTEM', '2025-11-26 01:15:31'),
(247, 2, 'Cập nhật giáo viên', 'GiaoVien', 1, 'SYSTEM', '2025-11-26 01:15:31'),
(248, 18, 'Cập nhật tài khoản', 'User', 18, 'SYSTEM', '2025-11-26 01:15:37'),
(249, 18, 'Cập nhật giáo viên', 'GiaoVien', 3, 'SYSTEM', '2025-11-26 01:15:37'),
(250, 29, 'Tạo tài khoản', 'User', 29, 'SYSTEM', '2025-11-26 01:15:58'),
(251, 29, 'Thêm giáo viên', 'GiaoVien', 7, 'SYSTEM', '2025-11-26 01:15:58'),
(252, 29, 'Cập nhật tài khoản', 'User', 29, 'SYSTEM', '2025-11-26 01:16:07'),
(253, 29, 'Cập nhật giáo viên', 'GiaoVien', 7, 'SYSTEM', '2025-11-26 01:16:07'),
(254, 30, 'Tạo tài khoản', 'User', 30, 'SYSTEM', '2025-11-26 01:16:28'),
(255, 30, 'Thêm giáo viên', 'GiaoVien', 9, 'SYSTEM', '2025-11-26 01:16:28'),
(256, 31, 'Tạo tài khoản', 'User', 31, 'SYSTEM', '2025-11-26 01:18:52'),
(257, 31, 'Thêm giáo viên', 'GiaoVien', 11, 'SYSTEM', '2025-11-26 01:18:52'),
(258, 30, 'Cập nhật tài khoản', 'User', 30, 'SYSTEM', '2025-11-26 01:22:35'),
(259, 30, 'Cập nhật giáo viên', 'GiaoVien', 9, 'SYSTEM', '2025-11-26 01:22:35'),
(260, NULL, 'Tạo tài khoản', 'User', 32, 'SYSTEM', '2025-11-26 01:24:11'),
(261, NULL, 'Thêm giáo viên', 'GiaoVien', 13, 'SYSTEM', '2025-11-26 01:24:11'),
(262, NULL, 'Tạo tài khoản', 'User', 33, 'SYSTEM', '2025-11-26 01:24:29'),
(263, NULL, 'Thêm giáo viên', 'GiaoVien', 15, 'SYSTEM', '2025-11-26 01:24:29'),
(264, NULL, 'Tạo tài khoản', 'User', 34, 'SYSTEM', '2025-11-26 01:28:07'),
(265, NULL, 'Thêm giáo viên', 'GiaoVien', 17, 'SYSTEM', '2025-11-26 01:28:07'),
(266, 35, 'Tạo tài khoản', 'User', 35, 'SYSTEM', '2025-11-26 01:28:31'),
(267, 35, 'Thêm giáo viên', 'GiaoVien', 19, 'SYSTEM', '2025-11-26 01:28:31'),
(269, NULL, 'Xóa tài khoản', 'User', 33, 'SYSTEM', '2025-11-26 01:29:05'),
(271, NULL, 'Xóa tài khoản', 'User', 34, 'SYSTEM', '2025-11-26 01:29:28'),
(273, NULL, 'Xóa tài khoản', 'User', 32, 'SYSTEM', '2025-11-26 01:29:34'),
(274, 36, 'Tạo tài khoản', 'User', 36, 'SYSTEM', '2025-11-26 22:28:42'),
(275, 36, 'Thêm giáo viên', 'GiaoVien', 21, 'SYSTEM', '2025-11-26 22:28:42'),
(276, 37, 'Tạo tài khoản', 'User', 37, 'SYSTEM', '2025-11-26 22:28:59'),
(277, 37, 'Thêm giáo viên', 'GiaoVien', 23, 'SYSTEM', '2025-11-26 22:28:59'),
(278, 30, 'Cập nhật tài khoản', 'User', 30, 'SYSTEM', '2025-11-27 00:14:31'),
(279, 30, 'Cập nhật giáo viên', 'GiaoVien', 9, 'SYSTEM', '2025-11-27 00:14:31'),
(280, 29, 'Cập nhật tài khoản', 'User', 29, 'SYSTEM', '2025-11-27 00:14:58'),
(281, 29, 'Cập nhật giáo viên', 'GiaoVien', 7, 'SYSTEM', '2025-11-27 00:14:58'),
(282, 29, 'Cập nhật tài khoản', 'User', 29, 'SYSTEM', '2025-11-27 00:15:12'),
(283, 29, 'Cập nhật giáo viên', 'GiaoVien', 7, 'SYSTEM', '2025-11-27 00:15:12'),
(284, NULL, 'Tạo tài khoản', 'User', 38, 'SYSTEM', '2025-11-27 00:30:02'),
(285, NULL, 'Thêm giáo viên', 'GiaoVien', 25, 'SYSTEM', '2025-11-27 00:30:02'),
(287, NULL, 'Xóa tài khoản', 'User', 38, 'SYSTEM', '2025-11-27 00:32:00'),
(288, NULL, 'Tạo tài khoản', 'User', 39, 'SYSTEM', '2025-11-27 00:32:11'),
(289, NULL, 'Thêm giáo viên', 'GiaoVien', 27, 'SYSTEM', '2025-11-27 00:32:11'),
(291, NULL, 'Xóa tài khoản', 'User', 39, 'SYSTEM', '2025-11-27 00:34:33'),
(292, NULL, 'Tạo tài khoản', 'User', 40, 'SYSTEM', '2025-11-27 00:34:46'),
(293, NULL, 'Thêm giáo viên', 'GiaoVien', 29, 'SYSTEM', '2025-11-27 00:34:46'),
(295, NULL, 'Xóa tài khoản', 'User', 40, 'SYSTEM', '2025-11-27 00:36:52'),
(296, NULL, 'Tạo tài khoản', 'User', 41, 'SYSTEM', '2025-11-27 00:37:02'),
(297, NULL, 'Thêm giáo viên', 'GiaoVien', 31, 'SYSTEM', '2025-11-27 00:37:02'),
(299, NULL, 'Xóa tài khoản', 'User', 41, 'SYSTEM', '2025-11-27 00:41:49'),
(300, NULL, 'Tạo tài khoản', 'User', 42, 'SYSTEM', '2025-11-27 00:42:00'),
(301, NULL, 'Thêm giáo viên', 'GiaoVien', 32, 'SYSTEM', '2025-11-27 00:42:00'),
(303, NULL, 'Xóa tài khoản', 'User', 42, 'SYSTEM', '2025-11-27 00:43:17'),
(304, NULL, 'Tạo tài khoản', 'User', 43, 'SYSTEM', '2025-11-27 00:43:23'),
(305, NULL, 'Thêm giáo viên', 'GiaoVien', 34, 'SYSTEM', '2025-11-27 00:43:23'),
(307, NULL, 'Xóa tài khoản', 'User', 43, 'SYSTEM', '2025-11-27 00:44:08'),
(308, NULL, 'Tạo tài khoản', 'User', 44, 'SYSTEM', '2025-11-27 00:44:15'),
(309, NULL, 'Thêm giáo viên', 'GiaoVien', 35, 'SYSTEM', '2025-11-27 00:44:15'),
(310, NULL, 'Tạo tài khoản', 'User', 45, 'SYSTEM', '2025-11-27 22:22:30'),
(311, NULL, 'Thêm giáo viên', 'GiaoVien', 37, 'SYSTEM', '2025-11-27 22:22:30'),
(313, NULL, 'Xóa tài khoản', 'User', 44, 'SYSTEM', '2025-11-27 22:22:36'),
(315, NULL, 'Xóa tài khoản', 'User', 45, 'SYSTEM', '2025-11-27 22:26:46'),
(316, 46, 'Tạo tài khoản', 'User', 46, 'SYSTEM', '2025-11-27 22:26:53'),
(317, 46, 'Thêm giáo viên', 'GiaoVien', 39, 'SYSTEM', '2025-11-27 22:26:53'),
(318, 46, 'Cập nhật giáo viên', 'GiaoVien', 39, 'SYSTEM', '2025-11-27 22:26:53'),
(319, 31, 'Cập nhật tài khoản', 'User', 31, 'SYSTEM', '2025-11-27 22:30:21'),
(320, 31, 'Cập nhật giáo viên', 'GiaoVien', 11, 'SYSTEM', '2025-11-27 22:30:21'),
(321, 36, 'Cập nhật tài khoản', 'User', 36, 'SYSTEM', '2025-11-27 22:30:27'),
(322, 36, 'Cập nhật giáo viên', 'GiaoVien', 21, 'SYSTEM', '2025-11-27 22:30:27'),
(323, 37, 'Cập nhật tài khoản', 'User', 37, 'SYSTEM', '2025-11-27 22:30:33'),
(324, 37, 'Cập nhật giáo viên', 'GiaoVien', 23, 'SYSTEM', '2025-11-27 22:30:33'),
(325, 35, 'Cập nhật tài khoản', 'User', 35, 'SYSTEM', '2025-11-27 22:30:42'),
(326, 35, 'Cập nhật giáo viên', 'GiaoVien', 19, 'SYSTEM', '2025-11-27 22:30:42'),
(327, NULL, 'Thêm lớp học', 'LopHoc', 8, NULL, '2025-11-27 22:37:32'),
(328, NULL, 'Tạo lớp 8 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-27 22:37:32'),
(329, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-27 22:37:59'),
(330, NULL, 'Thêm lớp học', 'LopHoc', 9, NULL, '2025-11-27 22:39:07'),
(331, NULL, 'Tạo lớp 9 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-27 22:39:07'),
(332, NULL, 'Xóa lớp học', 'LopHoc', 9, NULL, '2025-11-27 22:39:20'),
(333, NULL, 'Thêm lớp học', 'LopHoc', 10, NULL, '2025-11-27 22:41:21'),
(334, NULL, 'Tạo lớp 10 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-27 22:41:21'),
(335, NULL, 'Xóa lớp học', 'LopHoc', 10, NULL, '2025-11-27 22:41:29'),
(336, NULL, 'Thêm lớp học', 'LopHoc', 11, NULL, '2025-11-27 22:42:54'),
(337, NULL, 'Tạo lớp 11 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-27 22:42:54'),
(338, NULL, 'Lớp 7 thay đổi giáo viên chủ nhiệm từ 1 → 19', NULL, NULL, NULL, '2025-11-27 22:44:39'),
(339, NULL, 'Cập nhật lớp học', 'LopHoc', 7, NULL, '2025-11-27 22:44:39'),
(340, NULL, 'Lớp 7 thay đổi giáo viên chủ nhiệm từ 19 → 5', NULL, NULL, NULL, '2025-11-27 22:44:47'),
(341, NULL, 'Cập nhật lớp học', 'LopHoc', 7, NULL, '2025-11-27 22:44:47'),
(342, NULL, 'Cập nhật lớp học', 'LopHoc', 3, NULL, '2025-11-27 22:44:54'),
(343, NULL, 'Cập nhật lớp học', 'LopHoc', 2, NULL, '2025-11-27 22:45:00'),
(344, NULL, 'Lớp 8 thay đổi giáo viên chủ nhiệm từ 3 → 9', NULL, NULL, NULL, '2025-11-27 22:45:08'),
(345, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-27 22:45:08'),
(346, NULL, 'Cập nhật lớp học', 'LopHoc', 1, NULL, '2025-11-27 22:45:15'),
(347, NULL, 'Xóa lớp học', 'LopHoc', 7, NULL, '2025-11-27 22:46:25'),
(348, NULL, 'Xóa lớp học', 'LopHoc', 1, NULL, '2025-11-27 22:46:29'),
(349, NULL, 'Xóa lớp học', 'LopHoc', 2, NULL, '2025-11-27 22:46:33'),
(350, NULL, 'Xóa lớp học', 'LopHoc', 3, NULL, '2025-11-27 22:46:38'),
(351, NULL, 'Thêm lớp học', 'LopHoc', 12, NULL, '2025-11-27 22:46:49'),
(352, NULL, 'Tạo lớp 12 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-27 22:46:49'),
(353, NULL, 'Thêm lớp học', 'LopHoc', 13, NULL, '2025-11-27 22:47:04'),
(354, NULL, 'Tạo lớp 13 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-27 22:47:04'),
(355, NULL, 'Thêm lớp học', 'LopHoc', 14, NULL, '2025-11-27 22:47:16'),
(356, NULL, 'Tạo lớp 14 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-27 22:47:16'),
(357, NULL, 'Thêm lớp học', 'LopHoc', 15, NULL, '2025-11-27 22:47:29'),
(358, NULL, 'Tạo lớp 15 → sinh các môn của năm học & kỳ học', NULL, NULL, NULL, '2025-11-27 22:47:29'),
(359, 3, 'Cập nhật tài khoản', 'User', 3, 'SYSTEM', '2025-11-27 22:50:58'),
(360, 3, 'Cập nhật học sinh', 'HocSinh', 1, NULL, '2025-11-27 22:50:58'),
(361, 20, 'Cập nhật tài khoản', 'User', 20, 'SYSTEM', '2025-11-27 22:51:21'),
(362, 20, 'Cập nhật học sinh', 'HocSinh', 4, NULL, '2025-11-27 22:51:21'),
(363, NULL, 'Tạo tài khoản', 'User', 47, 'SYSTEM', '2025-11-27 22:51:46'),
(364, NULL, 'Thêm học sinh', 'HocSinh', 13, NULL, '2025-11-27 22:51:46'),
(365, NULL, 'Cập nhật học sinh', 'HocSinh', 13, NULL, '2025-11-27 22:51:46'),
(366, NULL, 'Cập nhật tài khoản', 'User', 47, 'SYSTEM', '2025-11-27 22:59:55'),
(368, NULL, 'Tạo tài khoản', 'User', 48, 'SYSTEM', '2025-11-27 23:00:17'),
(369, NULL, 'Thêm học sinh', 'HocSinh', 14, NULL, '2025-11-27 23:00:17'),
(370, NULL, 'Cập nhật học sinh', 'HocSinh', 14, NULL, '2025-11-27 23:00:17'),
(371, 27, 'Cập nhật tài khoản', 'User', 27, 'SYSTEM', '2025-11-27 23:07:01'),
(372, 27, 'Cập nhật học sinh', 'HocSinh', 12, NULL, '2025-11-27 23:07:01'),
(373, 26, 'Cập nhật tài khoản', 'User', 26, 'SYSTEM', '2025-11-27 23:07:23'),
(374, 26, 'Cập nhật học sinh', 'HocSinh', 11, NULL, '2025-11-27 23:07:23'),
(375, 24, 'Cập nhật tài khoản', 'User', 24, 'SYSTEM', '2025-11-27 23:07:30'),
(376, 24, 'Cập nhật học sinh', 'HocSinh', 10, NULL, '2025-11-27 23:07:30'),
(377, 21, 'Cập nhật tài khoản', 'User', 21, 'SYSTEM', '2025-11-27 23:12:13'),
(378, 21, 'Cập nhật học sinh', 'HocSinh', 6, NULL, '2025-11-27 23:12:13'),
(379, NULL, 'Tạo tài khoản', 'User', 49, 'SYSTEM', '2025-11-27 23:13:22'),
(380, NULL, 'Thêm học sinh', 'HocSinh', 15, NULL, '2025-11-27 23:13:22'),
(381, NULL, 'Cập nhật học sinh', 'HocSinh', 15, NULL, '2025-11-27 23:13:22'),
(388, NULL, 'Cập nhật tài khoản', 'User', 49, 'SYSTEM', '2025-11-27 23:32:57'),
(389, NULL, 'Cập nhật học sinh', 'HocSinh', 15, NULL, '2025-11-27 23:32:58'),
(390, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-27 23:56:36'),
(391, NULL, 'Xóa học sinh', 'HocSinh', 15, NULL, '2025-11-27 23:56:36'),
(392, NULL, 'Xóa tài khoản', 'User', 49, 'SYSTEM', '2025-11-27 23:56:36'),
(393, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-27 23:57:05'),
(394, NULL, 'Xóa học sinh', 'HocSinh', 14, NULL, '2025-11-27 23:57:05'),
(395, NULL, 'Xóa tài khoản', 'User', 48, 'SYSTEM', '2025-11-27 23:57:05'),
(396, NULL, 'Cập nhật lớp học', 'LopHoc', 14, NULL, '2025-11-27 23:57:23'),
(397, NULL, 'Xóa học sinh', 'HocSinh', 13, NULL, '2025-11-27 23:57:23'),
(398, NULL, 'Xóa tài khoản', 'User', 47, 'SYSTEM', '2025-11-27 23:57:23'),
(399, NULL, 'Tạo tài khoản', 'User', 50, 'SYSTEM', '2025-11-27 23:57:31'),
(400, NULL, 'Thêm học sinh', 'HocSinh', 16, NULL, '2025-11-27 23:57:31'),
(401, NULL, 'Cập nhật học sinh', 'HocSinh', 16, NULL, '2025-11-27 23:57:31'),
(404, 51, 'Tạo tài khoản', 'User', 51, 'SYSTEM', '2025-11-28 00:00:46'),
(405, 51, 'Thêm học sinh', 'HocSinh', 17, NULL, '2025-11-28 00:00:46'),
(406, 51, 'Cập nhật học sinh', 'HocSinh', 17, NULL, '2025-11-28 00:00:46'),
(407, 52, 'Tạo tài khoản', 'User', 52, 'SYSTEM', '2025-11-30 20:07:24'),
(408, 52, 'Thêm giáo viên', 'GiaoVien', 40, 'SYSTEM', '2025-11-30 20:07:24'),
(409, 52, 'Cập nhật giáo viên', 'GiaoVien', 40, 'SYSTEM', '2025-11-30 20:07:24'),
(410, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 20:08:31'),
(411, NULL, 'Cập nhật lớp học', 'LopHoc', 13, NULL, '2025-11-30 20:08:46'),
(412, NULL, 'Cập nhật lớp học', 'LopHoc', 14, NULL, '2025-11-30 20:08:57'),
(413, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 20:22:30'),
(414, NULL, 'Xóa học sinh', 'HocSinh', 16, NULL, '2025-11-30 20:22:30'),
(415, NULL, 'Xóa tài khoản', 'User', 50, 'SYSTEM', '2025-11-30 20:22:30'),
(416, 22, 'Cập nhật tài khoản', 'User', 22, 'SYSTEM', '2025-11-30 20:37:22'),
(417, 22, 'Cập nhật học sinh', 'HocSinh', 8, NULL, '2025-11-30 20:37:22'),
(418, 22, 'Cập nhật tài khoản', 'User', 22, 'SYSTEM', '2025-11-30 20:40:44'),
(420, NULL, 'Cập nhật lớp học', 'LopHoc', 15, NULL, '2025-11-30 20:40:44'),
(421, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 20:40:44'),
(422, NULL, 'Cập nhật lớp học', 'LopHoc', 15, NULL, '2025-11-30 20:41:09'),
(423, 23, 'Cập nhật tài khoản', 'User', 23, 'SYSTEM', '2025-11-30 20:41:18'),
(424, 23, 'Cập nhật học sinh', 'HocSinh', 9, NULL, '2025-11-30 20:41:18'),
(425, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 20:41:18'),
(426, 3, 'Cập nhật tài khoản', 'User', 3, 'SYSTEM', '2025-11-30 20:41:39'),
(428, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 20:41:40'),
(429, NULL, 'Cập nhật lớp học', 'LopHoc', 15, NULL, '2025-11-30 20:41:40'),
(430, NULL, 'Tạo tài khoản', 'User', 53, 'SYSTEM', '2025-11-30 20:42:14'),
(431, NULL, 'Thêm học sinh', 'HocSinh', 18, NULL, '2025-11-30 20:42:14'),
(432, NULL, 'Cập nhật học sinh', 'HocSinh', 18, NULL, '2025-11-30 20:42:14'),
(433, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 20:42:14'),
(434, NULL, 'Cập nhật môn học', 'MonHoc', 2, NULL, '2025-11-30 21:42:31'),
(435, NULL, 'Thêm môn học', 'MonHoc', 6, NULL, '2025-11-30 21:42:51'),
(436, 46, 'Cập nhật tài khoản', 'User', 46, 'SYSTEM', '2025-11-30 21:43:17'),
(437, 46, 'Cập nhật giáo viên', 'GiaoVien', 39, 'SYSTEM', '2025-11-30 21:43:17'),
(438, NULL, 'Cập nhật môn học', 'MonHoc', 6, NULL, '2025-11-30 21:43:29'),
(439, NULL, 'Thêm môn học', 'MonHoc', 7, NULL, '2025-11-30 21:43:45'),
(443, NULL, 'Xóa môn học', 'MonHoc', 7, NULL, '2025-11-30 21:55:24'),
(444, NULL, 'Xóa phân công môn 7 trong giaovien_monhoc', NULL, NULL, NULL, '2025-11-30 21:55:24'),
(445, NULL, 'Thêm môn học', 'MonHoc', 7, NULL, '2025-11-30 21:55:36'),
(446, NULL, 'Xóa môn học', 'MonHoc', 7, NULL, '2025-11-30 21:55:48'),
(447, NULL, 'Xóa phân công môn 7 trong giaovien_monhoc', NULL, NULL, NULL, '2025-11-30 21:55:48'),
(448, 3, 'Cập nhật tài khoản', 'User', 3, 'SYSTEM', '2025-11-30 22:07:11'),
(450, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 22:07:11'),
(451, NULL, 'Cập nhật lớp học', 'LopHoc', 13, NULL, '2025-11-30 22:07:11'),
(452, 3, 'Cập nhật tài khoản', 'User', 3, 'SYSTEM', '2025-11-30 22:07:20'),
(454, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 22:07:20'),
(455, NULL, 'Cập nhật lớp học', 'LopHoc', 13, NULL, '2025-11-30 22:07:20'),
(456, 3, 'Cập nhật tài khoản', 'User', 3, 'SYSTEM', '2025-11-30 22:07:33'),
(458, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 22:07:33'),
(459, NULL, 'Cập nhật lớp học', 'LopHoc', 13, NULL, '2025-11-30 22:07:33'),
(460, 21, 'Cập nhật tài khoản', 'User', 21, 'SYSTEM', '2025-11-30 22:08:01'),
(462, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 22:08:01'),
(463, NULL, 'Cập nhật lớp học', 'LopHoc', 13, NULL, '2025-11-30 22:08:01'),
(464, NULL, 'Cập nhật tài khoản', 'User', 53, 'SYSTEM', '2025-11-30 22:08:19'),
(466, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 22:08:19'),
(467, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 22:08:19'),
(468, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 22:10:46'),
(469, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 22:11:04'),
(470, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 22:11:15'),
(471, NULL, 'Cập nhật lớp học', 'LopHoc', 13, NULL, '2025-11-30 22:11:30'),
(472, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 22:12:02'),
(473, NULL, 'Xóa học sinh', 'HocSinh', 18, NULL, '2025-11-30 22:12:02'),
(474, NULL, 'Xóa tài khoản', 'User', 53, 'SYSTEM', '2025-11-30 22:12:02'),
(475, NULL, 'Tạo tài khoản', 'User', 54, 'SYSTEM', '2025-11-30 22:16:07'),
(476, NULL, 'Thêm học sinh', 'HocSinh', 19, NULL, '2025-11-30 22:16:07'),
(477, NULL, 'Cập nhật học sinh', 'HocSinh', 19, NULL, '2025-11-30 22:16:07'),
(478, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 22:16:07'),
(479, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 22:16:14'),
(480, NULL, 'Xóa học sinh', 'HocSinh', 19, NULL, '2025-11-30 22:16:14'),
(481, NULL, 'Xóa tài khoản', 'User', 54, 'SYSTEM', '2025-11-30 22:16:14'),
(482, NULL, 'Tạo tài khoản', 'User', 55, 'SYSTEM', '2025-11-30 22:16:22'),
(483, NULL, 'Thêm học sinh', 'HocSinh', 20, NULL, '2025-11-30 22:16:22'),
(484, NULL, 'Cập nhật học sinh', 'HocSinh', 20, NULL, '2025-11-30 22:16:22'),
(485, NULL, 'Cập nhật tài khoản', 'User', 55, 'SYSTEM', '2025-11-30 22:16:39'),
(486, NULL, 'Cập nhật học sinh', 'HocSinh', 20, NULL, '2025-11-30 22:16:39'),
(487, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 22:16:39'),
(488, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 22:17:22'),
(489, NULL, 'Xóa học sinh', 'HocSinh', 20, NULL, '2025-11-30 22:17:22'),
(490, NULL, 'Xóa tài khoản', 'User', 55, 'SYSTEM', '2025-11-30 22:17:22'),
(491, NULL, 'Tạo tài khoản', 'User', 56, 'SYSTEM', '2025-11-30 22:18:53'),
(492, NULL, 'Thêm học sinh', 'HocSinh', 21, NULL, '2025-11-30 22:18:53'),
(493, NULL, 'Cập nhật học sinh', 'HocSinh', 21, NULL, '2025-11-30 22:18:53'),
(494, NULL, 'Xóa học sinh', 'HocSinh', 21, NULL, '2025-11-30 22:19:01'),
(495, NULL, 'Xóa tài khoản', 'User', 56, 'SYSTEM', '2025-11-30 22:19:01'),
(496, 51, 'Cập nhật tài khoản', 'User', 51, 'SYSTEM', '2025-11-30 22:37:36'),
(498, NULL, 'Cập nhật lớp học', 'LopHoc', 14, NULL, '2025-11-30 22:37:36'),
(499, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 22:37:36'),
(500, NULL, 'Cập nhật lớp học', 'LopHoc', 14, NULL, '2025-11-30 22:45:42'),
(501, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 22:45:52'),
(502, 51, 'Cập nhật tài khoản', 'User', 51, 'SYSTEM', '2025-11-30 22:46:02'),
(504, NULL, 'Cập nhật lớp học', 'LopHoc', 14, NULL, '2025-11-30 22:46:02'),
(505, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 22:46:02'),
(506, 51, 'Cập nhật tài khoản', 'User', 51, 'SYSTEM', '2025-11-30 22:48:32'),
(508, NULL, 'Cập nhật lớp học', 'LopHoc', 14, NULL, '2025-11-30 22:48:32'),
(509, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 22:48:32'),
(510, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 22:51:09'),
(511, NULL, 'Cập nhật lớp học', 'LopHoc', 14, NULL, '2025-11-30 22:51:16'),
(512, 51, 'Cập nhật tài khoản', 'User', 51, 'SYSTEM', '2025-11-30 22:58:42'),
(514, NULL, 'Cập nhật lớp học', 'LopHoc', 14, NULL, '2025-11-30 22:58:42'),
(515, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 22:58:42'),
(516, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 22:59:35'),
(517, NULL, 'Cập nhật lớp học', 'LopHoc', 14, NULL, '2025-11-30 22:59:39'),
(519, 51, 'Cập nhật học sinh', 'HocSinh', 17, NULL, '2025-11-30 23:08:03'),
(520, NULL, 'Cập nhật lớp học', 'LopHoc', 14, NULL, '2025-11-30 23:08:03'),
(521, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 23:08:03'),
(522, NULL, 'Ghi điểm', 'DiemSo', 46, 'SYSTEM', '2025-11-30 23:08:03'),
(523, NULL, 'Ghi điểm', 'DiemSo', 47, 'SYSTEM', '2025-11-30 23:08:03'),
(524, NULL, 'Ghi điểm', 'DiemSo', 48, 'SYSTEM', '2025-11-30 23:08:03'),
(525, NULL, 'Ghi điểm', 'DiemSo', 49, 'SYSTEM', '2025-11-30 23:08:03'),
(526, NULL, 'Ghi điểm', 'DiemSo', 50, 'SYSTEM', '2025-11-30 23:08:03'),
(527, NULL, 'Ghi điểm', 'DiemSo', 53, 'SYSTEM', '2025-11-30 23:08:03'),
(528, NULL, 'Ghi điểm', 'DiemSo', 54, 'SYSTEM', '2025-11-30 23:08:03'),
(529, NULL, 'Ghi điểm', 'DiemSo', 55, 'SYSTEM', '2025-11-30 23:08:03'),
(530, NULL, 'Ghi điểm', 'DiemSo', 56, 'SYSTEM', '2025-11-30 23:08:03'),
(531, NULL, 'Ghi điểm', 'DiemSo', 57, 'SYSTEM', '2025-11-30 23:08:03'),
(532, NULL, 'Ghi điểm', 'DiemSo', 60, 'SYSTEM', '2025-11-30 23:08:03'),
(533, NULL, 'Ghi điểm', 'DiemSo', 61, 'SYSTEM', '2025-11-30 23:08:03'),
(534, NULL, 'Ghi điểm', 'DiemSo', 62, 'SYSTEM', '2025-11-30 23:08:03'),
(535, NULL, 'Ghi điểm', 'DiemSo', 63, 'SYSTEM', '2025-11-30 23:08:03'),
(536, NULL, 'Ghi điểm', 'DiemSo', 64, 'SYSTEM', '2025-11-30 23:08:03'),
(537, NULL, 'Học sinh 17 chuyển lớp 14 → 8', NULL, NULL, NULL, '2025-11-30 23:08:03'),
(538, NULL, 'Cập nhật lớp học', 'LopHoc', 14, NULL, '2025-11-30 23:08:03'),
(539, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 23:08:03'),
(540, NULL, 'Cập nhật lớp học', 'LopHoc', 14, NULL, '2025-11-30 23:08:51'),
(541, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 23:09:36'),
(600, 31, 'Cập nhật tài khoản', 'User', 31, 'SYSTEM', '2025-11-30 23:18:31'),
(601, 31, 'Cập nhật giáo viên', 'GiaoVien', 11, 'SYSTEM', '2025-11-30 23:18:31'),
(604, NULL, 'Tạo tài khoản', 'User', 58, 'SYSTEM', '2025-11-30 23:23:42'),
(605, NULL, 'Thêm học sinh', 'HocSinh', 24, NULL, '2025-11-30 23:23:42'),
(606, NULL, 'Cập nhật học sinh', 'HocSinh', 24, NULL, '2025-11-30 23:23:42'),
(607, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 23:23:42'),
(608, 26, 'Cập nhật học sinh', 'HocSinh', 11, NULL, '2025-11-30 23:24:30'),
(609, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 23:24:30'),
(610, NULL, 'Cập nhật lớp học', 'LopHoc', 15, NULL, '2025-11-30 23:24:30'),
(611, NULL, 'Ghi điểm', 'DiemSo', 109, 'SYSTEM', '2025-11-30 23:24:30'),
(612, NULL, 'Ghi điểm', 'DiemSo', 110, 'SYSTEM', '2025-11-30 23:24:30'),
(613, NULL, 'Ghi điểm', 'DiemSo', 111, 'SYSTEM', '2025-11-30 23:24:30'),
(614, NULL, 'Ghi điểm', 'DiemSo', 112, 'SYSTEM', '2025-11-30 23:24:30'),
(615, NULL, 'Ghi điểm', 'DiemSo', 113, 'SYSTEM', '2025-11-30 23:24:30'),
(616, NULL, 'Ghi điểm', 'DiemSo', 116, 'SYSTEM', '2025-11-30 23:24:30'),
(617, NULL, 'Ghi điểm', 'DiemSo', 117, 'SYSTEM', '2025-11-30 23:24:30'),
(618, NULL, 'Ghi điểm', 'DiemSo', 118, 'SYSTEM', '2025-11-30 23:24:30'),
(619, NULL, 'Ghi điểm', 'DiemSo', 119, 'SYSTEM', '2025-11-30 23:24:30'),
(620, NULL, 'Ghi điểm', 'DiemSo', 120, 'SYSTEM', '2025-11-30 23:24:30'),
(621, NULL, 'Ghi điểm', 'DiemSo', 123, 'SYSTEM', '2025-11-30 23:24:30'),
(622, NULL, 'Ghi điểm', 'DiemSo', 124, 'SYSTEM', '2025-11-30 23:24:30'),
(623, NULL, 'Ghi điểm', 'DiemSo', 125, 'SYSTEM', '2025-11-30 23:24:30'),
(624, NULL, 'Ghi điểm', 'DiemSo', 126, 'SYSTEM', '2025-11-30 23:24:30'),
(625, NULL, 'Ghi điểm', 'DiemSo', 127, 'SYSTEM', '2025-11-30 23:24:30'),
(626, NULL, 'Học sinh 11 chuyển lớp 12 → 15', NULL, NULL, NULL, '2025-11-30 23:24:30'),
(627, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 23:24:30'),
(628, NULL, 'Cập nhật lớp học', 'LopHoc', 15, NULL, '2025-11-30 23:24:30'),
(629, NULL, 'Cập nhật lớp học', 'LopHoc', 8, NULL, '2025-11-30 23:27:15'),
(630, NULL, 'Xóa học sinh', 'HocSinh', 24, NULL, '2025-11-30 23:27:15'),
(631, NULL, 'Xóa tài khoản', 'User', 58, 'SYSTEM', '2025-11-30 23:27:15'),
(632, NULL, 'Tạo tài khoản', 'User', 59, 'SYSTEM', '2025-11-30 23:27:37'),
(633, NULL, 'Thêm học sinh', 'HocSinh', 25, NULL, '2025-11-30 23:27:37'),
(634, NULL, 'Cập nhật học sinh', 'HocSinh', 25, NULL, '2025-11-30 23:27:37'),
(635, NULL, 'Xóa học sinh', 'HocSinh', 25, NULL, '2025-11-30 23:30:45'),
(636, NULL, 'Xóa tài khoản', 'User', 59, 'SYSTEM', '2025-11-30 23:30:45'),
(637, NULL, 'Tạo tài khoản', 'User', 60, 'SYSTEM', '2025-11-30 23:30:52'),
(638, NULL, 'Thêm học sinh', 'HocSinh', 26, NULL, '2025-11-30 23:30:52'),
(639, NULL, 'Cập nhật học sinh', 'HocSinh', 26, NULL, '2025-11-30 23:30:52'),
(640, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 23:30:52'),
(641, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 23:39:49'),
(642, NULL, 'Xóa học sinh', 'HocSinh', 26, NULL, '2025-11-30 23:39:49'),
(643, NULL, 'Xóa tài khoản', 'User', 60, 'SYSTEM', '2025-11-30 23:39:49'),
(644, NULL, 'Tạo tài khoản', 'User', 61, 'SYSTEM', '2025-11-30 23:39:58'),
(645, NULL, 'Thêm học sinh', 'HocSinh', 27, NULL, '2025-11-30 23:39:58'),
(646, NULL, 'Cập nhật học sinh', 'HocSinh', 27, NULL, '2025-11-30 23:39:58'),
(647, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 23:39:58'),
(648, 26, 'Cập nhật học sinh', 'HocSinh', 11, NULL, '2025-11-30 23:42:02'),
(649, NULL, 'Cập nhật lớp học', 'LopHoc', 15, NULL, '2025-11-30 23:42:02'),
(650, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 23:42:02'),
(651, NULL, 'Học sinh 11 chuyển lớp 15 → 11', NULL, NULL, NULL, '2025-11-30 23:42:02'),
(652, NULL, 'Cập nhật lớp học', 'LopHoc', 15, NULL, '2025-11-30 23:42:02'),
(653, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 23:42:02'),
(654, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 23:43:35'),
(655, NULL, 'Xóa học sinh', 'HocSinh', 27, NULL, '2025-11-30 23:43:35'),
(656, NULL, 'Xóa tài khoản', 'User', 61, 'SYSTEM', '2025-11-30 23:43:35'),
(657, NULL, 'Tạo tài khoản', 'User', 62, 'SYSTEM', '2025-11-30 23:43:43'),
(658, NULL, 'Thêm học sinh', 'HocSinh', 28, NULL, '2025-11-30 23:43:43'),
(659, NULL, 'Cập nhật học sinh', 'HocSinh', 28, NULL, '2025-11-30 23:43:43'),
(660, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 23:43:43'),
(661, NULL, 'Cập nhật học sinh', 'HocSinh', 28, NULL, '2025-11-30 23:44:18'),
(662, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 23:44:18'),
(663, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 23:44:18'),
(664, NULL, 'Ghi điểm', 'DiemSo', 130, 'SYSTEM', '2025-11-30 23:44:18'),
(665, NULL, 'Ghi điểm', 'DiemSo', 131, 'SYSTEM', '2025-11-30 23:44:18'),
(666, NULL, 'Ghi điểm', 'DiemSo', 132, 'SYSTEM', '2025-11-30 23:44:18'),
(667, NULL, 'Ghi điểm', 'DiemSo', 133, 'SYSTEM', '2025-11-30 23:44:18'),
(668, NULL, 'Ghi điểm', 'DiemSo', 134, 'SYSTEM', '2025-11-30 23:44:18'),
(669, NULL, 'Ghi điểm', 'DiemSo', 137, 'SYSTEM', '2025-11-30 23:44:18'),
(670, NULL, 'Ghi điểm', 'DiemSo', 138, 'SYSTEM', '2025-11-30 23:44:18'),
(671, NULL, 'Ghi điểm', 'DiemSo', 139, 'SYSTEM', '2025-11-30 23:44:18'),
(672, NULL, 'Ghi điểm', 'DiemSo', 140, 'SYSTEM', '2025-11-30 23:44:18'),
(673, NULL, 'Ghi điểm', 'DiemSo', 141, 'SYSTEM', '2025-11-30 23:44:18'),
(674, NULL, 'Ghi điểm', 'DiemSo', 144, 'SYSTEM', '2025-11-30 23:44:18'),
(675, NULL, 'Ghi điểm', 'DiemSo', 145, 'SYSTEM', '2025-11-30 23:44:18'),
(676, NULL, 'Ghi điểm', 'DiemSo', 146, 'SYSTEM', '2025-11-30 23:44:18'),
(677, NULL, 'Ghi điểm', 'DiemSo', 147, 'SYSTEM', '2025-11-30 23:44:18'),
(678, NULL, 'Ghi điểm', 'DiemSo', 148, 'SYSTEM', '2025-11-30 23:44:18'),
(679, NULL, 'Học sinh 28 chuyển lớp 11 → 12', NULL, NULL, NULL, '2025-11-30 23:44:18'),
(680, NULL, 'Cập nhật lớp học', 'LopHoc', 11, NULL, '2025-11-30 23:44:18'),
(681, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 23:44:18'),
(682, NULL, 'Cập nhật lớp học', 'LopHoc', 12, NULL, '2025-11-30 23:44:35'),
(683, NULL, 'Xóa học sinh', 'HocSinh', 28, NULL, '2025-11-30 23:44:35'),
(684, NULL, 'Xóa tài khoản', 'User', 62, 'SYSTEM', '2025-11-30 23:44:35'),
(685, NULL, 'Cập nhật điểm', 'DiemSo', 46, 'SYSTEM', '2025-12-01 23:50:50'),
(686, NULL, 'Ghi điểm', 'DiemSo', 149, 'SYSTEM', '2025-12-02 23:56:36'),
(687, NULL, 'Ghi điểm', 'DiemSo', 150, 'SYSTEM', '2025-12-02 23:56:36'),
(688, NULL, 'Ghi điểm', 'DiemSo', 151, 'SYSTEM', '2025-12-02 23:56:36'),
(689, NULL, 'Ghi điểm', 'DiemSo', 152, 'SYSTEM', '2025-12-02 23:56:36'),
(690, NULL, 'Ghi điểm', 'DiemSo', 169, 'SYSTEM', '2025-12-06 21:19:39'),
(691, NULL, 'Ghi điểm', 'DiemSo', 170, 'SYSTEM', '2025-12-06 21:19:39'),
(692, NULL, 'Ghi điểm', 'DiemSo', 171, 'SYSTEM', '2025-12-06 21:19:39'),
(693, NULL, 'Ghi điểm', 'DiemSo', 172, 'SYSTEM', '2025-12-06 21:19:39'),
(694, NULL, 'Xóa điểm ID 1', NULL, NULL, NULL, '2025-12-06 21:45:38'),
(695, NULL, 'Xóa điểm ID 1', NULL, NULL, NULL, '2025-12-06 21:45:40'),
(696, NULL, 'Xóa điểm ID 1', NULL, NULL, NULL, '2025-12-06 21:45:42'),
(697, NULL, 'Xóa điểm ID 1', NULL, NULL, NULL, '2025-12-06 21:45:44'),
(698, NULL, 'Xóa điểm ID 1', NULL, NULL, NULL, '2025-12-06 21:45:45'),
(699, NULL, 'Xóa điểm ID 1', NULL, NULL, NULL, '2025-12-06 21:45:47'),
(700, NULL, 'Xóa điểm ID 1', NULL, NULL, NULL, '2025-12-06 21:45:49'),
(701, NULL, 'Xóa điểm ID 1', NULL, NULL, NULL, '2025-12-06 21:45:51'),
(702, NULL, 'Ghi điểm', 'DiemSo', 173, 'SYSTEM', '2025-12-06 21:50:51'),
(703, NULL, 'Ghi điểm', 'DiemSo', 174, 'SYSTEM', '2025-12-06 21:50:51'),
(704, NULL, 'Ghi điểm', 'DiemSo', 175, 'SYSTEM', '2025-12-06 21:50:51'),
(705, NULL, 'Ghi điểm', 'DiemSo', 176, 'SYSTEM', '2025-12-06 21:50:51'),
(706, NULL, 'Cập nhật điểm', 'DiemSo', 173, 'SYSTEM', '2025-12-06 22:17:06'),
(707, NULL, 'Cập nhật điểm', 'DiemSo', 174, 'SYSTEM', '2025-12-06 22:17:06'),
(708, NULL, 'Cập nhật điểm', 'DiemSo', 175, 'SYSTEM', '2025-12-06 22:17:06'),
(709, NULL, 'Cập nhật điểm', 'DiemSo', 176, 'SYSTEM', '2025-12-06 22:17:06'),
(710, NULL, 'Cập nhật điểm', 'DiemSo', 174, 'SYSTEM', '2025-12-06 23:56:45'),
(711, NULL, 'Cập nhật điểm', 'DiemSo', 174, 'SYSTEM', '2025-12-06 23:57:33'),
(712, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:08:16'),
(713, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:08:19'),
(714, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:08:22'),
(715, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:08:24'),
(716, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:08:26'),
(717, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:08:30'),
(718, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:08:32'),
(719, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:08:34'),
(720, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:08:36'),
(721, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:08:38'),
(722, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:08:54'),
(723, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:08:56'),
(724, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:08:58'),
(725, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:09:00'),
(726, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:09:02'),
(727, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:09:04'),
(728, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:09:06'),
(729, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:09:08'),
(730, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:09:10'),
(731, NULL, 'Xóa điểm ID 17', NULL, NULL, NULL, '2025-12-07 10:09:11'),
(732, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:09:13'),
(733, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:09:15'),
(734, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:09:17'),
(735, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:09:19'),
(736, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:09:21'),
(737, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:09:22'),
(738, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:09:24'),
(739, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:09:30'),
(740, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:09:34'),
(741, NULL, 'Xóa điểm ID 11', NULL, NULL, NULL, '2025-12-07 10:09:35');

-- --------------------------------------------------------

--
-- Table structure for table `giaovien`
--

CREATE TABLE `giaovien` (
  `maGV` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `boMon` varchar(50) DEFAULT NULL,
  `trinhDo` varchar(50) NOT NULL,
  `phongBan` varchar(50) NOT NULL,
  `trangThaiHoatDong` varchar(50) DEFAULT NULL,
  `namHoc` varchar(50) DEFAULT NULL,
  `kyHoc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `giaovien`
--

INSERT INTO `giaovien` (`maGV`, `userId`, `boMon`, `trinhDo`, `phongBan`, `trangThaiHoatDong`, `namHoc`, `kyHoc`) VALUES
(1, 2, 'Toán học', '', '', 'Hoạt động', '2024-2025', 1),
(3, 18, 'Vật lý', '', '', 'Hoạt động', '2024-2025', 1),
(5, 28, 'Ngữ Văn', '', '', 'Hoạt động', '2024-2025', 1),
(7, 29, 'Sinh học', '', '', 'Hoạt động', '2024-2025', 1),
(9, 30, 'Ngữ Văn', '', '', 'Hoạt động', '2024-2025', 1),
(11, 31, 'Tiếng Anh', '', '', 'Hoạt động', '2024-2025', 1),
(19, 35, 'Sinh học', '', '', 'Hoạt động', '2024-2025', 1),
(21, 36, 'Toán học', '', '', 'Hoạt động', '2024-2025', 1),
(23, 37, 'Vật lý', '', '', 'Hoạt động', '2024-2025', 1),
(39, 46, 'Tiếng Anh', '', '', 'Hoạt động', '2024-2025', 1),
(40, 52, 'Hóa học', '', '', 'Hoạt động', '2024-2025', 1);

--
-- Triggers `giaovien`
--
DELIMITER $$
CREATE TRIGGER `trg_giaovien_insert_monhoc` AFTER INSERT ON `giaovien` FOR EACH ROW BEGIN
  -- Nếu có bộ môn, chèn phân công theo maMon tương ứng
  IF NEW.boMon IS NOT NULL THEN
    -- Thử chèn tất cả môn khớp tên + năm học + học kỳ
    INSERT INTO giaovien_monhoc (idGV, idMon)
    SELECT NEW.maGV, maMon
    FROM monhoc
    WHERE tenMon = NEW.boMon
      AND namHoc = NEW.namHoc
      AND hocKy = NEW.kyHoc;

    -- Nếu chưa chèn được (không có bản ghi tương ứng với năm/học kỳ), fallback theo tên môn
    IF ROW_COUNT() = 0 THEN
      INSERT INTO giaovien_monhoc (idGV, idMon)
      SELECT NEW.maGV, maMon
      FROM monhoc
      WHERE tenMon = NEW.boMon
      LIMIT 1;
    END IF;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_giaovien_update_monhoc` AFTER UPDATE ON `giaovien` FOR EACH ROW BEGIN
    DECLARE monId INT;
  IF NEW.boMon IS NOT NULL AND NEW.boMon <> OLD.boMon THEN
    -- Xóa các phân công môn cũ của giáo viên
    DELETE FROM giaovien_monhoc WHERE idGV = NEW.maGV;

    -- Thêm tất cả môn khớp tên + năm học + học kỳ
    INSERT INTO giaovien_monhoc (idGV, idMon)
    SELECT NEW.maGV, maMon
    FROM monhoc
    WHERE tenMon = NEW.boMon
      AND namHoc = NEW.namHoc
      AND hocKy = NEW.kyHoc;

    -- Nếu không có bản ghi khớp theo năm/học kỳ, fallback theo tên môn (1 bản ghi)
    IF ROW_COUNT() = 0 THEN
      INSERT INTO giaovien_monhoc (idGV, idMon)
      SELECT NEW.maGV, maMon
      FROM monhoc
      WHERE tenMon = NEW.boMon
      LIMIT 1;
    END IF;
  END IF;
END
$$
DELIMITER ;
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
DELIMITER $$
CREATE TRIGGER `trg_gv_delete_monhoc` AFTER DELETE ON `giaovien` FOR EACH ROW BEGIN
    DELETE FROM giaovien_monhoc WHERE idGV = OLD.maGV;

    INSERT INTO GhiLog(hoatDong, thoiGian)
    VALUES (CONCAT('Xóa toàn bộ phân công môn của giáo viên ', OLD.maGV), NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `giaovien_monhoc`
--

CREATE TABLE `giaovien_monhoc` (
  `id` int(11) NOT NULL,
  `idGV` int(11) NOT NULL,
  `idMon` int(11) NOT NULL,
  `ghiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `giaovien_monhoc`
--

INSERT INTO `giaovien_monhoc` (`id`, `idGV`, `idMon`, `ghiChu`) VALUES
(12, 5, 5, NULL),
(15, 1, 1, NULL),
(16, 3, 2, NULL),
(17, 9, 5, NULL),
(18, 7, 4, NULL);

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
-- Dumping data for table `hocsinh`
--

INSERT INTO `hocsinh` (`maHS`, `userId`, `maLopHienTai`, `trangThaiHoatDong`, `namHoc`, `kyHoc`, `chucVu`) VALUES
(1, 3, 12, 'Hoạt động', NULL, NULL, 'Lớp trưởng'),
(4, 20, 13, 'Hoạt động', NULL, NULL, 'Thành viên'),
(6, 21, 12, 'Hoạt động', NULL, NULL, 'Thành viên'),
(8, 22, 15, 'Hoạt động', '2025-2026', 1, 'Thành viên'),
(9, 23, 11, 'Hoạt động', '2025-2026', 1, 'Thành viên'),
(10, 24, 12, 'Hoạt động', '2025-2026', 1, 'Thành viên'),
(11, 26, 11, 'Hoạt động', '2025-2026', 1, 'Thành viên'),
(12, 27, 14, 'Hoạt động', '2025-2026', 1, 'Thành viên'),
(17, 51, 8, 'Hoạt động', '2025-2026', 1, 'Thành viên');

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
    -- Increment class count for new class and create diemso records
    IF NEW.maLopHienTai IS NOT NULL THEN
        UPDATE LopHoc SET siSo = siSo + 1 WHERE maLop = NEW.maLopHienTai;

        -- Insert "Điểm miệng" records for new class
        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS,
                lm.maMon,
                lm.maLop,
                'Điểm miệng',
                NULL,
                lm.namHoc,
                lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai
            AND NOT EXISTS (
                SELECT 1 FROM diemso d
                WHERE d.maHS = NEW.maHS
                AND d.maMonHoc = lm.maMon
                AND d.loaiDiem = 'Điểm miệng'
                AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
            );

        -- Insert "Điểm 1 tiết" records for new class
        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS,
                lm.maMon,
                lm.maLop,
                'Điểm 1 tiết',
                NULL,
                lm.namHoc,
                lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai
            AND NOT EXISTS (
                SELECT 1 FROM diemso d
                WHERE d.maHS = NEW.maHS
                AND d.maMonHoc = lm.maMon
                AND d.loaiDiem = 'Điểm 1 tiết'
                AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
            );
            
            -- Insert "Điểm gk" records for new class
        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS,
                lm.maMon,
                lm.maLop,
                'Điểm giữa kỳ',
                NULL,
                lm.namHoc,
                lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai
            AND NOT EXISTS (
                SELECT 1 FROM diemso d
                WHERE d.maHS = NEW.maHS
                AND d.maMonHoc = lm.maMon
                AND d.loaiDiem = 'Điểm giữa kỳ'
                AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
            );
            
            -- Insert "Điểm cuối kỳ" records for new class
        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS,
                lm.maMon,
                lm.maLop,
                'Điểm cuối kỳ',
                NULL,
                lm.namHoc,
                lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai
            AND NOT EXISTS (
                SELECT 1 FROM diemso d
                WHERE d.maHS = NEW.maHS
                AND d.maMonHoc = lm.maMon
                AND d.loaiDiem = 'Điểm cuối kỳ'
                AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
            );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_hs_after_insert_diemso` AFTER INSERT ON `hocsinh` FOR EACH ROW BEGIN
    IF NEW.maLopHienTai IS NOT NULL THEN
        -- Insert "Điểm miệng" (oral scores)
        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS, lm.maMon, lm.maLop, 'Điểm miệng', NULL, lm.namHoc, lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai
          AND NOT EXISTS (
              SELECT 1 FROM diemso d
              WHERE d.maHS = NEW.maHS
                AND d.maMonHoc = lm.maMon
                AND d.loaiDiem = 'Điểm miệng'
                AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
          );

        -- Insert "Điểm 1 tiết" (1-period test scores)
        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS, lm.maMon, lm.maLop, 'Điểm 1 tiết', NULL, lm.namHoc, lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai
          AND NOT EXISTS (
              SELECT 1 FROM diemso d
              WHERE d.maHS = NEW.maHS
                AND d.maMonHoc = lm.maMon
                AND d.loaiDiem = 'Điểm 1 tiết'
                AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
          );
          
          -- Insert "Điểm gk" 
        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS, lm.maMon, lm.maLop, 'Điểm giữa kỳ', NULL, lm.namHoc, lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai
          AND NOT EXISTS (
              SELECT 1 FROM diemso d
              WHERE d.maHS = NEW.maHS
                AND d.maMonHoc = lm.maMon
                AND d.loaiDiem = 'Điểm giữa kỳ'
                AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
          );
          
          -- Insert "Điểm ck" 
        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS, lm.maMon, lm.maLop, 'Điểm cuối kỳ', NULL, lm.namHoc, lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai
          AND NOT EXISTS (
              SELECT 1 FROM diemso d
              WHERE d.maHS = NEW.maHS
                AND d.maMonHoc = lm.maMon
                AND d.loaiDiem = 'Điểm cuối kỳ'
                AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
          );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_hs_after_update` AFTER UPDATE ON `hocsinh` FOR EACH ROW BEGIN
    IF OLD.maLopHienTai <> NEW.maLopHienTai THEN
        -- Decrement class count for old class
        IF OLD.maLopHienTai IS NOT NULL THEN
            UPDATE LopHoc SET siSo = siSo - 1 WHERE maLop = OLD.maLopHienTai;
        END IF;

        -- Increment class count for new class and create diemso records
        IF NEW.maLopHienTai IS NOT NULL THEN
            UPDATE LopHoc SET siSo = siSo + 1 WHERE maLop = NEW.maLopHienTai;

            -- Insert "Điểm miệng" records for new class
            INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
            SELECT NEW.maHS,
                 lm.maMon,
                 lm.maLop,
                 'Điểm miệng',
                 NULL,
                 lm.namHoc,
                 lm.hocKy
            FROM lop_monhoc lm
            WHERE lm.maLop = NEW.maLopHienTai
              AND NOT EXISTS (
                  SELECT 1 FROM diemso d
                  WHERE d.maHS = NEW.maHS
                    AND d.maMonHoc = lm.maMon
                    AND d.loaiDiem = 'Điểm miệng'
                    AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                    AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
              );

            -- Insert "Điểm 1 tiết" records for new class
            INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
            SELECT NEW.maHS,
                 lm.maMon,
                 lm.maLop,
                 'Điểm 1 tiết',
                 NULL,
                 lm.namHoc,
                 lm.hocKy
            FROM lop_monhoc lm
            WHERE lm.maLop = NEW.maLopHienTai
              AND NOT EXISTS (
                  SELECT 1 FROM diemso d
                  WHERE d.maHS = NEW.maHS
                    AND d.maMonHoc = lm.maMon
                    AND d.loaiDiem = 'Điểm 1 tiết'
                    AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                    AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
              );
              
              
            -- Insert "Điểm giữa kỳ" records for new class
            INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
            SELECT NEW.maHS,
                 lm.maMon,
                 lm.maLop,
                 'Điểm giữa kỳ',
                 NULL,
                 lm.namHoc,
                 lm.hocKy
            FROM lop_monhoc lm
            WHERE lm.maLop = NEW.maLopHienTai
              AND NOT EXISTS (
                  SELECT 1 FROM diemso d
                  WHERE d.maHS = NEW.maHS
                    AND d.maMonHoc = lm.maMon
                    AND d.loaiDiem = 'Điểm giữa kỳ'
                    AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                    AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
              );
              
              
            -- Insert "Điểm cuối kỳ" records for new class
            INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
            SELECT NEW.maHS,
                 lm.maMon,
                 lm.maLop,
                 'Điểm cuối kỳ',
                 NULL,
                 lm.namHoc,
                 lm.hocKy
            FROM lop_monhoc lm
            WHERE lm.maLop = NEW.maLopHienTai
              AND NOT EXISTS (
                  SELECT 1 FROM diemso d
                  WHERE d.maHS = NEW.maHS
                    AND d.maMonHoc = lm.maMon
                    AND d.loaiDiem = 'Điểm cuối kỳ'
                    AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                    AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
              );
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_hs_after_update_diemso` AFTER UPDATE ON `hocsinh` FOR EACH ROW BEGIN
    -- Nếu học sinh đổi lớp hoặc gán lớp mới
    IF OLD.maLopHienTai <> NEW.maLopHienTai AND NEW.maLopHienTai IS NOT NULL THEN
        -- Điểm miệng
        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS, lm.maMon, lm.maLop, 'Điểm miệng', NULL, lm.namHoc, lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai
          AND NOT EXISTS (
              SELECT 1 FROM diemso d
              WHERE d.maHS = NEW.maHS
                AND d.maMonHoc = lm.maMon
                AND d.loaiDiem = 'Điểm miệng'
                AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
          );

        -- Điểm 1 tiết
        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS, lm.maMon, lm.maLop, 'Điểm 1 tiết', NULL, lm.namHoc, lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai
          AND NOT EXISTS (
              SELECT 1 FROM diemso d
              WHERE d.maHS = NEW.maHS
                AND d.maMonHoc = lm.maMon
                AND d.loaiDiem = 'Điểm 1 tiết'
                AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
          );
          
          -- Điểm giữa kỳ
        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS, lm.maMon, lm.maLop, 'Điểm giữa kỳ', NULL, lm.namHoc, lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai
          AND NOT EXISTS (
              SELECT 1 FROM diemso d
              WHERE d.maHS = NEW.maHS
                AND d.maMonHoc = lm.maMon
                AND d.loaiDiem = 'Điểm giữa kỳ'
                AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
          );
          
          -- Điểm cuối kỳ
        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS, lm.maMon, lm.maLop, 'Điểm cuối kỳ', NULL, lm.namHoc, lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai
          AND NOT EXISTS (
              SELECT 1 FROM diemso d
              WHERE d.maHS = NEW.maHS
                AND d.maMonHoc = lm.maMon
                AND d.loaiDiem = 'Điểm cuối kỳ'
                AND ((d.namHoc = lm.namHoc) OR (d.namHoc IS NULL AND lm.namHoc IS NULL))
                AND ((d.hocKy = lm.hocKy) OR (d.hocKy IS NULL AND lm.hocKy IS NULL))
          );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_hs_cascade_delete` AFTER DELETE ON `hocsinh` FOR EACH ROW BEGIN
    DELETE FROM bainop WHERE maHS = OLD.maHS;
    DELETE FROM diemso WHERE maHS = OLD.maHS;
    DELETE FROM chuyencan WHERE maHS = OLD.maHS;
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
DELIMITER $$
CREATE TRIGGER `trg_hs_update_lop` AFTER UPDATE ON `hocsinh` FOR EACH ROW BEGIN
    IF OLD.maLopHienTai <> NEW.maLopHienTai THEN
        INSERT INTO GhiLog(hanhDong, thoiGian)
        VALUES (
            CONCAT(
                'Học sinh ', NEW.maHS,
                ' chuyển lớp ', OLD.maLopHienTai,
                ' → ', NEW.maLopHienTai
            ), NOW()
        );
    END IF;
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
-- Dumping data for table `lophoc`
--

INSERT INTO `lophoc` (`maLop`, `tenLop`, `khoiLop`, `giaoVienPhuTrach`, `siSo`, `trangThai`, `namHoc`, `kyHoc`) VALUES
(8, '10A5', '10', 9, 1, 'active', '2024-2025', 1),
(11, '10A6', '10', 19, 2, 'active', '2024-2025', 1),
(12, '10A1', '10', 1, 3, 'active', '2024-2025', 1),
(13, '10A2', '10', 3, 1, 'active', '2024-2025', 1),
(14, '10A3', '10', 5, 1, 'active', '2024-2025', 1),
(15, '10A4', '10', 7, 1, 'active', '2024-2025', 1);

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
CREATE TRIGGER `trg_lophoc_gvphutrach_update` AFTER UPDATE ON `lophoc` FOR EACH ROW BEGIN
    IF OLD.giaoVienPhuTrach <> NEW.giaoVienPhuTrach THEN
        INSERT INTO GhiLog(hanhDong, thoiGian)
        VALUES (
            CONCAT('Lớp ', NEW.maLop, ' thay đổi giáo viên chủ nhiệm từ ', OLD.giaoVienPhuTrach, ' → ', NEW.giaoVienPhuTrach),
            NOW()
        );
    END IF;
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
CREATE TRIGGER `trg_lophoc_insert_monhoc` AFTER INSERT ON `lophoc` FOR EACH ROW BEGIN
    INSERT INTO lop_monhoc(maLop, maMon, namHoc, hocKy)
    SELECT NEW.maLop, monhoc.maMon, monhoc.namHoc, monhoc.hocKy
    FROM monhoc
    WHERE monhoc.namHoc = NEW.namHoc
      AND monhoc.hocKy = NEW.kyHoc;

    INSERT INTO GhiLog(hanhDong, thoiGian)
    VALUES (CONCAT('Tạo lớp ', NEW.maLop, ' → sinh các môn của năm học & kỳ học'), NOW());
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
-- Table structure for table `lop_monhoc`
--

CREATE TABLE `lop_monhoc` (
  `id` int(11) NOT NULL,
  `maLop` int(11) NOT NULL,
  `maMon` int(11) NOT NULL,
  `namHoc` varchar(20) DEFAULT NULL,
  `hocKy` int(11) DEFAULT NULL,
  `maGV` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lop_monhoc`
--

INSERT INTO `lop_monhoc` (`id`, `maLop`, `maMon`, `namHoc`, `hocKy`, `maGV`) VALUES
(1, 8, 1, '2024-2025', 1, 0),
(2, 8, 2, '2024-2025', 1, 0),
(3, 8, 3, '2024-2025', 1, 0),
(4, 8, 4, '2024-2025', 1, 0),
(5, 8, 5, '2024-2025', 1, 0),
(22, 11, 1, '2024-2025', 1, 0),
(23, 11, 2, '2024-2025', 1, 0),
(24, 11, 3, '2024-2025', 1, 0),
(25, 11, 4, '2024-2025', 1, 0),
(26, 11, 5, '2024-2025', 1, 0),
(29, 12, 1, '2024-2025', 1, 0),
(30, 12, 2, '2024-2025', 1, 0),
(31, 12, 3, '2024-2025', 1, 0),
(32, 12, 4, '2024-2025', 1, 0),
(33, 12, 5, '2024-2025', 1, 0),
(36, 13, 1, '2024-2025', 1, 0),
(37, 13, 2, '2024-2025', 1, 0),
(38, 13, 3, '2024-2025', 1, 0),
(39, 13, 4, '2024-2025', 1, 0),
(40, 13, 5, '2024-2025', 1, 0),
(43, 14, 1, '2024-2025', 1, 0),
(44, 14, 2, '2024-2025', 1, 0),
(45, 14, 3, '2024-2025', 1, 0),
(46, 14, 4, '2024-2025', 1, 0),
(47, 14, 5, '2024-2025', 1, 0),
(50, 15, 1, '2024-2025', 1, 0),
(51, 15, 2, '2024-2025', 1, 0),
(52, 15, 3, '2024-2025', 1, 0),
(53, 15, 4, '2024-2025', 1, 0),
(54, 15, 5, '2024-2025', 1, 0);

--
-- Triggers `lop_monhoc`
--
DELIMITER $$
CREATE TRIGGER `trg_lop_monhoc_insert_diemso` AFTER INSERT ON `lop_monhoc` FOR EACH ROW BEGIN
    INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
    SELECT h.maHS,
           NEW.maMon,
           NEW.maLop,
           'Điểm miệng',
           NULL,
           NEW.namHoc,
           NEW.hocKy
    FROM hocsinh h
    WHERE h.maLopHienTai = NEW.maLop;

    INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
    SELECT h.maHS,
           NEW.maMon,
           NEW.maLop,
           'Điểm 15 phút',
           NULL,
           NEW.namHoc,
           NEW.hocKy
    FROM hocsinh h
    WHERE h.maLopHienTai = NEW.maLop;

    INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
    SELECT h.maHS,
           NEW.maMon,
           NEW.maLop,
           'Điểm 1 tiết',
           NULL,
           NEW.namHoc,
           NEW.hocKy
    FROM hocsinh h
    WHERE h.maLopHienTai = NEW.maLop;
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
-- Dumping data for table `monhoc`
--

INSERT INTO `monhoc` (`maMon`, `tenMon`, `moTa`, `namHoc`, `hocKy`, `truongBoMon`, `trangThai`) VALUES
(1, 'Toán học', '', '2024-2025', 1, 'gv1', 'active'),
(2, 'Vật lý', '', '2024-2025', 1, 'gv2', 'active'),
(3, 'Hóa học', '', '2024-2025', 1, '', 'active'),
(4, 'Sinh học', '', '2024-2025', 1, '', 'active'),
(5, 'Ngữ Văn', '', '2024-2025', 1, '', 'active'),
(6, 'Tiếng Anh', '', '2024-2025', 1, 'gvtest', 'active');

--
-- Triggers `monhoc`
--
DELIMITER $$
CREATE TRIGGER `trg_monhoc_cascade` AFTER DELETE ON `monhoc` FOR EACH ROW BEGIN
    DELETE FROM tailieu WHERE maMon = OLD.maMon;
    DELETE FROM diemso WHERE maMonHoc = OLD.maMon;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_monhoc_delete` AFTER DELETE ON `monhoc` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hanhDong, doiTuongTacDong, maDoiTuong)
    VALUES ('Xóa môn học', 'MonHoc', OLD.maMon);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_monhoc_delete_gvmon` AFTER DELETE ON `monhoc` FOR EACH ROW BEGIN
    DELETE FROM giaovien_monhoc WHERE idMon = OLD.maMon;

    INSERT INTO GhiLog(hanhDong, thoiGian)
    VALUES (CONCAT('Xóa phân công môn ', OLD.maMon, ' trong giaovien_monhoc'), NOW());
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

--
-- Triggers `thongbaouser`
--
DELIMITER $$
CREATE TRIGGER `trg_thongbaouser_delete` AFTER DELETE ON `thongbaouser` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hoatDong, thoiGian)
    VALUES (
        CONCAT('Xóa thông báo ID ', OLD.maTB, ' của user ', OLD.userID),
        NOW()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_thongbaouser_insert` AFTER INSERT ON `thongbaouser` FOR EACH ROW BEGIN
    INSERT INTO GhiLog(hoatDong, thoiGian)
    VALUES (
        CONCAT('Gửi thông báo ID ', NEW.maTB, ' cho user ', NEW.userID), 
        NOW()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_thongbaouser_update` AFTER UPDATE ON `thongbaouser` FOR EACH ROW BEGIN
    IF NEW.trangThai <> OLD.trangThai THEN
        INSERT INTO GhiLog(hoatDong, thoiGian)
        VALUES (
            CONCAT('User ', NEW.userID, ' đã thay đổi trạng thái thông báo ID ', NEW.maTB),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

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
  `vaiTro` enum('Admin','GiaoVien','HocSinh') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userId`, `matKhau`, `hoVaTen`, `email`, `sdt`, `gioiTinh`, `vaiTro`) VALUES
(1, '12345678', 'admin', 'admin@gmail.com', NULL, NULL, 'Admin'),
(2, '12345678', 'gv1', 'gv1@gmail.com', '0987654320', 'Nam', 'GiaoVien'),
(3, '12345678', 'hs1', 'hs1@gmail.com', '0978888801', 'Nam', 'HocSinh'),
(18, '12345678', 'gv2', 'gv2@gmail.com', '0987654321', 'Nam', 'GiaoVien'),
(20, '12345678', 'hs2', 'hs2@gmail.com', '0978888881', 'Nam', 'HocSinh'),
(21, '12345678', 'hs3', 'hs3@gmail.com', '0978888812', 'Nam', 'HocSinh'),
(22, '12345678', 'hs4', 'hs4@gmail.com', '0978888883', 'Nam', 'HocSinh'),
(23, '12345678', 'hs5', 'hs5@gmail.com', '0978888882', 'Nam', 'HocSinh'),
(24, '12345678', 'hs6', 'hs6@gmail.com', '0978888886', 'Nam', 'HocSinh'),
(26, '12345678', 'hs7', 'hs7@gmail.com', '0978888817', 'Nam', 'HocSinh'),
(27, '12345678', 'hs8', 'hs8@gmail.com', '0978888887', 'Nam', 'HocSinh'),
(28, '12345678', 'gv3', 'gv3@gmail.com', '0987654322', 'Nam', 'GiaoVien'),
(29, '12345678', 'gv4', 'gv4@gmail.com', '0987654323', 'Nam', 'GiaoVien'),
(30, '12345678', 'gv5', 'gv5@gmail.com', '0987654324', 'Nam', 'GiaoVien'),
(31, '12345678', 'gv6', 'gv6@gmail.com', '0987654325', 'Nam', 'GiaoVien'),
(35, '12345678', 'gv10', 'gv10@gmail.com', '0987654310', 'Nam', 'GiaoVien'),
(36, '12345678', 'gv7', 'gv7@gmail.com', '0987654317', 'Nam', 'GiaoVien'),
(37, '12345678', 'gv8', 'gv8@gmail.com', '0987654318', 'Nam', 'GiaoVien'),
(46, '12345678', 'gvtest', 'gvtest@gmail.com', '0988888889', 'Nam', 'GiaoVien'),
(51, '12345678', 'hs9', 'hs9@gmail.com', '0978888819', 'Nam', 'HocSinh'),
(52, '12345678', 'gv9', 'gv9@gmail.com', '0987654319', 'Nam', 'GiaoVien');

--
-- Triggers `user`
--
DELIMITER $$
CREATE TRIGGER `trg_user_after_delete` AFTER DELETE ON `user` FOR EACH ROW BEGIN
    INSERT INTO ghilog(
        userId,      -- phải NULL để tránh FK lỗi
        hanhDong,
        doiTuongTacDong,
        maDoiTuong,
        diaChiIP
    )
    VALUES (
        NULL,        -- KHÔNG được dùng OLD.userId
        'Xóa tài khoản',
        'User',
        OLD.userId,
        'SYSTEM'
    );
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
DELIMITER $$
CREATE TRIGGER `trg_user_insert_admin` AFTER INSERT ON `user` FOR EACH ROW BEGIN
    IF NEW.vaiTro = 'Admin' THEN
        INSERT INTO admin(userId) VALUES (NEW.userId);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_user_insert_giaovien` AFTER INSERT ON `user` FOR EACH ROW BEGIN
    IF NEW.vaiTro = 'GiaoVien' THEN
        INSERT INTO giaovien(userId, boMon, trangThaiHoatDong, namHoc, kyHoc)
        VALUES (NEW.userId, NULL, 'Hoạt động', NULL, NULL);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_user_insert_hocsinh` AFTER INSERT ON `user` FOR EACH ROW BEGIN
    IF NEW.vaiTro = 'HocSinh' THEN
        INSERT INTO hocsinh(userId, maLopHienTai, trangThaiHoatDong, namHoc, kyHoc)
        VALUES (NEW.userId, NULL, 'Hoạt động', NULL, NULL);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_user_update_role` AFTER UPDATE ON `user` FOR EACH ROW BEGIN
    -- Chuyển thành giáo viên
    IF NEW.vaiTro = 'GiaoVien' AND OLD.vaiTro <> 'GiaoVien' THEN
        INSERT INTO giaovien(userId, boMon, trangThaiHoatDong, namHoc, kyHoc)
        VALUES (NEW.userId, NULL, 'Hoạt động', NULL, NULL);
    END IF;

    -- Chuyển thành học sinh
    IF NEW.vaiTro = 'HocSinh' AND OLD.vaiTro <> 'HocSinh' THEN
        INSERT INTO hocsinh(userId, maLopHienTai, trangThaiHoatDong, namHoc, kyHoc)
        VALUES (NEW.userId, NULL, 'Hoạt động', NULL, NULL);
    END IF;

    -- Nếu mất vai trò giáo viên → xóa khỏi giaovien
    IF OLD.vaiTro = 'GiaoVien' AND NEW.vaiTro <> 'GiaoVien' THEN
        DELETE FROM giaovien WHERE userId = OLD.userId;
    END IF;

    -- Nếu mất vai trò học sinh → xóa khỏi hocsinh
    IF OLD.vaiTro = 'HocSinh' AND NEW.vaiTro <> 'HocSinh' THEN
        DELETE FROM hocsinh WHERE userId = OLD.userId;
    END IF;
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
  ADD KEY `fk_bainop_hs` (`maHS`),
  ADD KEY `fk_bainop_tailieu` (`maTaiLieu`);

--
-- Indexes for table `baocao`
--
ALTER TABLE `baocao`
  ADD PRIMARY KEY (`maBaoCao`),
  ADD KEY `fk_bc_user` (`nguoiLapBC`);

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
  ADD UNIQUE KEY `unique_diem` (`maHS`,`maMonHoc`,`namHoc`,`hocKy`,`loaiDiem`),
  ADD KEY `maLop` (`maLop`),
  ADD KEY `fk_diem_hs` (`maHS`),
  ADD KEY `fk_diem_mon` (`maMonHoc`);

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
-- Indexes for table `giaovien_monhoc`
--
ALTER TABLE `giaovien_monhoc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gvmonhoc_gv` (`idGV`),
  ADD KEY `fk_gvmonhoc_mon` (`idMon`);

--
-- Indexes for table `hocsinh`
--
ALTER TABLE `hocsinh`
  ADD PRIMARY KEY (`maHS`),
  ADD UNIQUE KEY `userId` (`userId`),
  ADD KEY `fk_hs_lop` (`maLopHienTai`);

--
-- Indexes for table `lophoc`
--
ALTER TABLE `lophoc`
  ADD PRIMARY KEY (`maLop`),
  ADD KEY `fk_lop_gv` (`giaoVienPhuTrach`);

--
-- Indexes for table `lop_monhoc`
--
ALTER TABLE `lop_monhoc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maLop` (`maLop`),
  ADD KEY `maMon` (`maMon`);

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
  ADD KEY `tailieu_ibfk_1` (`maMon`);

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
  ADD KEY `fk_tbu_user` (`userId`);

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
  MODIFY `maDiem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT for table `ghilog`
--
ALTER TABLE `ghilog`
  MODIFY `maLog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=742;

--
-- AUTO_INCREMENT for table `giaovien`
--
ALTER TABLE `giaovien`
  MODIFY `maGV` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `giaovien_monhoc`
--
ALTER TABLE `giaovien_monhoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `hocsinh`
--
ALTER TABLE `hocsinh`
  MODIFY `maHS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `lophoc`
--
ALTER TABLE `lophoc`
  MODIFY `maLop` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `lop_monhoc`
--
ALTER TABLE `lop_monhoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `monhoc`
--
ALTER TABLE `monhoc`
  MODIFY `maMon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

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
  ADD CONSTRAINT `fk_bainop_hs` FOREIGN KEY (`maHS`) REFERENCES `hocsinh` (`maHS`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bainop_tailieu` FOREIGN KEY (`maTaiLieu`) REFERENCES `tailieu` (`maTaiLieu`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `baocao`
--
ALTER TABLE `baocao`
  ADD CONSTRAINT `fk_bc_user` FOREIGN KEY (`nguoiLapBC`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chuyencan`
--
ALTER TABLE `chuyencan`
  ADD CONSTRAINT `chuyencan_ibfk_1` FOREIGN KEY (`maHS`) REFERENCES `hocsinh` (`maHS`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chuyencan_ibfk_2` FOREIGN KEY (`maLop`) REFERENCES `lophoc` (`maLop`),
  ADD CONSTRAINT `chuyencan_ibfk_3` FOREIGN KEY (`maMon`) REFERENCES `monhoc` (`maMon`),
  ADD CONSTRAINT `chuyencan_ibfk_4` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE SET NULL;

--
-- Constraints for table `diemso`
--
ALTER TABLE `diemso`
  ADD CONSTRAINT `diemso_ibfk_3` FOREIGN KEY (`maLop`) REFERENCES `lophoc` (`maLop`),
  ADD CONSTRAINT `fk_diem_hs` FOREIGN KEY (`maHS`) REFERENCES `hocsinh` (`maHS`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_diem_mon` FOREIGN KEY (`maMonHoc`) REFERENCES `monhoc` (`maMon`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ghilog`
--
ALTER TABLE `ghilog`
  ADD CONSTRAINT `ghilog_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE SET NULL;

--
-- Constraints for table `giaovien`
--
ALTER TABLE `giaovien`
  ADD CONSTRAINT `fk_gv_user` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `giaovien_monhoc`
--
ALTER TABLE `giaovien_monhoc`
  ADD CONSTRAINT `fk_gv_monhoc_giaovien` FOREIGN KEY (`idGV`) REFERENCES `giaovien` (`maGV`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gv_monhoc_monhoc` FOREIGN KEY (`idMon`) REFERENCES `monhoc` (`maMon`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hocsinh`
--
ALTER TABLE `hocsinh`
  ADD CONSTRAINT `fk_hs_lop` FOREIGN KEY (`maLopHienTai`) REFERENCES `lophoc` (`maLop`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hs_user` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lophoc`
--
ALTER TABLE `lophoc`
  ADD CONSTRAINT `fk_lop_gv` FOREIGN KEY (`giaoVienPhuTrach`) REFERENCES `giaovien` (`maGV`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `lop_monhoc`
--
ALTER TABLE `lop_monhoc`
  ADD CONSTRAINT `lop_monhoc_ibfk_1` FOREIGN KEY (`maLop`) REFERENCES `lophoc` (`maLop`) ON DELETE CASCADE,
  ADD CONSTRAINT `lop_monhoc_ibfk_2` FOREIGN KEY (`maMon`) REFERENCES `monhoc` (`maMon`) ON DELETE CASCADE;

--
-- Constraints for table `tailieu`
--
ALTER TABLE `tailieu`
  ADD CONSTRAINT `tailieu_ibfk_1` FOREIGN KEY (`maMon`) REFERENCES `monhoc` (`maMon`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `thongbao`
--
ALTER TABLE `thongbao`
  ADD CONSTRAINT `thongbao_ibfk_1` FOREIGN KEY (`nguoiGui`) REFERENCES `user` (`userId`);

--
-- Constraints for table `thongbaouser`
--
ALTER TABLE `thongbaouser`
  ADD CONSTRAINT `fk_tbu_user` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
