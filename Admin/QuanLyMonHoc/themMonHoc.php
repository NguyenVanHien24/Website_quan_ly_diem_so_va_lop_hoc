<?php
require_once '../../csdl/db.php';
if (isset($_GET['action']) && $_GET['action'] === 'getAllTeachers') {

    header('Content-Type: application/json; charset=UTF-8');

    $sql = "SELECT gv.maGV, u.hoVaTen
            FROM giaovien gv
            JOIN user u ON gv.userId = u.userId
            WHERE u.vaiTro='GiaoVien'
            ORDER BY u.hoVaTen ASC";

    $result = $conn->query($sql);
    $teachers = [];

    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }

    echo json_encode($teachers);
    exit;
}

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
