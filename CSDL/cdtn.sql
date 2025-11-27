-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2025 at 07:23 PM
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
    INSERT INTO GhiLog(hoatDong, thoiGian)
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
    INSERT INTO GhiLog(hoatDong, thoiGian)
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
(35, NULL, 'Xóa tài khoản', 'User', 7, 'SYSTEM', '2025-11-23 01:21:36');

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
(1, 2, 'Toán Học', 'Đại học', '', 'Hoạt động', NULL, NULL);

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

-- --------------------------------------------------------

--
-- Table structure for table `hocsinh`
--

CREATE TABLE `hocsinh` (
  `maHS` int(11) NOT NULL AUTO_INCREMENT,
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
(0, 3, NULL, 'Hoạt động', NULL, NULL, NULL);

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
CREATE TRIGGER `trg_hs_after_insert_diemso` AFTER INSERT ON `hocsinh` FOR EACH ROW BEGIN
    IF NEW.maLopHienTai IS NOT NULL THEN
        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS, lm.maMon, lm.maLop, 'Điểm miệng', NULL, lm.namHoc, lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai;

        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS, lm.maMon, lm.maLop, 'Điểm 15 phút', NULL, lm.namHoc, lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai;

        INSERT INTO diemso(maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy)
        SELECT NEW.maHS, lm.maMon, lm.maLop, 'Điểm 1 tiết', NULL, lm.namHoc, lm.hocKy
        FROM lop_monhoc lm
        WHERE lm.maLop = NEW.maLopHienTai;
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
        INSERT INTO GhiLog(hoatDong, thoiGian)
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
        INSERT INTO GhiLog(hoatDong, thoiGian)
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

    INSERT INTO GhiLog(hoatDong, thoiGian)
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
  `trangThai` varchar(20) DEFAULT 'Chưa phân công'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

    INSERT INTO GhiLog(hoatDong, thoiGian)
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
(3, '12345678', 'hs1', 'hs1@gmail.com', NULL, NULL, 'HocSinh');

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
  MODIFY `maDiem` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ghilog`
--
ALTER TABLE `ghilog`
  MODIFY `maLog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `giaovien`
--
ALTER TABLE `giaovien`
  MODIFY `maGV` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `giaovien_monhoc`
--
ALTER TABLE `giaovien_monhoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lophoc`
--
ALTER TABLE `lophoc`
  MODIFY `maLop` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lop_monhoc`
--
ALTER TABLE `lop_monhoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

-- AUTO_INCREMENT for table `hocsinh`
ALTER TABLE `hocsinh`
  MODIFY `maHS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

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
  ADD CONSTRAINT `chuyencan_ibfk_1` FOREIGN KEY (`maHS`) REFERENCES `hocsinh` (`maHS`),
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
  ADD CONSTRAINT `fk_tbu_user` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `thongbaouser_ibfk_1` FOREIGN KEY (`maTB`) REFERENCES `thongbao` (`maThongBao`) ON DELETE CASCADE,
  ADD CONSTRAINT `thongbaouser_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
