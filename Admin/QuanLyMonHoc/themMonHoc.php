<?php
require_once '../../csdl/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenMon = $_POST['tenMon'] ?? '';
    $truongBoMon = $_POST['truongBoMon'] ?? '';
    $moTa = $_POST['moTa'] ?? '';
    $namHoc = $_POST['namHoc'] ?? '';
    $hocKy = $_POST['hocKy'] ?? '';
    $trangThai = $_POST['trangThai'] ?? 'inactive';
    $maMon = $_POST['maMon'] ?? null; // từ form

    if (!$tenMon || !$maMon) {
        echo json_encode(['status' => 'error', 'msg' => 'Thiếu dữ liệu bắt buộc']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO monhoc(maMon, tenMon, truongBoMon, moTa, namHoc, hocKy, trangThai) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssis", $maMon, $tenMon, $truongBoMon, $moTa, $namHoc, $hocKy, $trangThai);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'msg' => 'Thêm môn học thành công', 'maMon' => $maMon]);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Lỗi khi thêm môn học']);
    }
}
?>
