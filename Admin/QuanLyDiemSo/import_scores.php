<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../../config.php';
require_once '../../CSDL/db.php';
require_once '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'File upload error']);
    exit();
}

$path = $_FILES['file']['tmp_name'];
try {
    $spreadsheet = IOFactory::load($path);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);
    if (count($rows) < 2) {
        echo json_encode(['success' => false, 'message' => 'File không có dữ liệu']);
        exit();
    }
    $header = array_shift($rows); 
    $map = [];
    foreach ($header as $col => $val) {
        $k = trim(mb_strtolower((string)$val));
        $map[$k] = $col;
    }
    $find = function($candidates) use ($map) {
        foreach ($candidates as $cand) {
            $cand = trim(mb_strtolower($cand));
            if (isset($map[$cand])) return $map[$cand];
        }
        return null;
    };

    $col_maHS = $find(['mahs','ma_hs','ma hs','ma hoc sinh']);
    $col_maMon = $find(['ma_mon','ma_monhoc','mamon','ma mon','ma mon hoc']);
    $col_loai = $find(['loaidiem','loai_diem','loai diem','loai']);
    $col_gia = $find(['giatridiem','gia tri diem','gia','gia tri']);
    $col_nam = $find(['namhoc','nam_hoc','nam hoc']);
    $col_hk = $find(['hocky','hoc ky','hk']);
    $col_maLop = $find(['malop','ma_lop','ma lop']);

    $postedMaMon = isset($_POST['maMon']) ? trim($_POST['maMon']) : '';
    $postedMaLop = isset($_POST['maLop']) ? trim($_POST['maLop']) : '';

    if (!$col_maHS || (!$col_maMon && $postedMaMon === '') || !$col_loai || !$col_gia) {
        echo json_encode(['success' => false, 'message' => 'File thiếu cột bắt buộc: maHS, maMon (hoặc truyền maMon qua bộ lọc), loaiDiem, giaTriDiem']);
        exit();
    }

    $yearNow = date('Y');
    $monthNow = date('n');
    if ($monthNow >= 1 && $monthNow <= 6) {
        $defaultYear = ($yearNow - 1) . '-' . $yearNow;
    } else {
        $defaultYear = $yearNow . '-' . ($yearNow + 1);
    }

    $inserted = 0; $updated = 0; $errors = [];

    $sql_with_lop = "INSERT INTO diemso (maHS, maMonHoc, namHoc, hocKy, loaiDiem, giaTriDiem, maLop) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE giaTriDiem = VALUES(giaTriDiem), maLop = VALUES(maLop)";
    $stmt_with = $conn->prepare($sql_with_lop);
    $sql_no_lop = "INSERT INTO diemso (maHS, maMonHoc, namHoc, hocKy, loaiDiem, giaTriDiem) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE giaTriDiem = VALUES(giaTriDiem)";
    $stmt_no = $conn->prepare($sql_no_lop);

    $lookup_class_stmt = $conn->prepare("SELECT maLop FROM lophoc WHERE tenLop = ? LIMIT 1");
    $lookup_student_class_stmt = $conn->prepare("SELECT maLopHienTai FROM hocsinh WHERE maHS = ? LIMIT 1");

    foreach ($rows as $rnum => $row) {
        $maHS = isset($col_maHS) ? intval($row[$col_maHS]) : 0;
        if ($col_maMon) {
            $maMon = intval($row[$col_maMon]);
        } else {
            $maMon = $postedMaMon !== '' ? intval($postedMaMon) : 0;
        }
        $loai = isset($col_loai) ? trim($row[$col_loai]) : '';
        $gia = isset($col_gia) ? trim($row[$col_gia]) : '';
        $nam = $col_nam ? trim($row[$col_nam]) : $defaultYear;
        $hk = $col_hk ? intval($row[$col_hk]) : 1;

        if ($col_maLop) {
            $maLopRaw = trim($row[$col_maLop]);
        } else {
            $maLopRaw = $postedMaLop !== '' ? trim($postedMaLop) : '';
        }

        if ($maHS <= 0 || $maMon <= 0 || $loai === '') {
            $errors[] = ['row' => $rnum+1, 'message' => 'Thiếu thông tin bắt buộc'];
            continue;
        }
        $giaVal = is_numeric($gia) ? floatval($gia) : null;
        if ($giaVal === null) { $errors[] = ['row' => $rnum+1, 'message' => 'Giá trị điểm không hợp lệ']; continue; }

        $resolvedMaLop = null;
        if ($maLopRaw !== '') {
            if (is_numeric($maLopRaw)) {
                $resolvedMaLop = intval($maLopRaw);
            } else {
                if ($lookup_class_stmt) {
                    $lookup_class_stmt->bind_param('s', $maLopRaw);
                    $lookup_class_stmt->execute();
                    $lookup_class_stmt->bind_result($foundMaLop);
                    if ($lookup_class_stmt->fetch()) {
                        $resolvedMaLop = intval($foundMaLop);
                    }
                    $lookup_class_stmt->free_result();
                }
            }
        }

        if ($resolvedMaLop === null && $lookup_student_class_stmt && $maHS > 0) {
            $lookup_student_class_stmt->bind_param('i', $maHS);
            $lookup_student_class_stmt->execute();
            $lookup_student_class_stmt->bind_result($foundStudentClass);
            if ($lookup_student_class_stmt->fetch()) {
                $resolvedMaLop = $foundStudentClass !== null ? intval($foundStudentClass) : null;
            }
            $lookup_student_class_stmt->free_result();
        }

        if ($resolvedMaLop !== null && $stmt_with) {
            $stmt_with->bind_param('iisisdi', $maHS, $maMon, $nam, $hk, $loai, $giaVal, $resolvedMaLop);
            if ($stmt_with->execute()) {
                if ($conn->affected_rows > 0) $inserted++; else $updated++;
            } else {
                $errors[] = ['row' => $rnum+1, 'message' => $stmt_with->error];
            }
        } elseif ($stmt_no) {
            $stmt_no->bind_param('iisisd', $maHS, $maMon, $nam, $hk, $loai, $giaVal);
            if ($stmt_no->execute()) {
                if ($conn->affected_rows > 0) $inserted++; else $updated++;
            } else {
                $errors[] = ['row' => $rnum+1, 'message' => $stmt_no->error];
            }
        }
    }

    if ($stmt_with) $stmt_with->close();
    if ($stmt_no) $stmt_no->close();
    if ($lookup_class_stmt) $lookup_class_stmt->close();
    if ($lookup_student_class_stmt) $lookup_student_class_stmt->close();

    echo json_encode(['success' => true, 'inserted' => $inserted, 'updated' => $updated, 'errors' => $errors]);
    exit();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit();
}
