<?php
require_once '../../csdl/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maMon = $_POST['maMon'] ?? null;
    $tenMon = $_POST['tenMon'] ?? '';
    $truongBoMon = $_POST['truongBoMon'] ?? '';
    $moTa = $_POST['moTa'] ?? '';
    $namHoc = $_POST['namHoc'] ?? '';
    $hocKy = $_POST['hocKy'] ?? '';
    $trangThai = $_POST['trangThai'] ?? 'inactive';

    if (!$maMon || !$tenMon) {
        echo json_encode(['status' => 'error', 'msg' => 'Thiếu dữ liệu bắt buộc']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE monhoc SET tenMon=?, truongBoMon=?, moTa=?, namHoc=?, hocKy=?, trangThai=? WHERE maMon=?");
    $stmt->bind_param("ssssssi", $tenMon, $truongBoMon, $moTa, $namHoc, $hocKy, $trangThai, $maMon);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'msg' => 'Cập nhật môn học thành công']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Lỗi khi cập nhật môn học']);
    }
}
?>
