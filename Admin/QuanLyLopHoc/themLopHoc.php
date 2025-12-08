<?php
require_once '../../config.php';
require_once '../../csdl/db.php';

header('Content-Type: application/json');

// Nhận dữ liệu từ AJAX
$tenLop = $_POST['tenLop'] ?? null;
$khoiLop = $_POST['khoiLop'] ?? null;
$siSo = $_POST['siSo'] ?? null;
$trangThai = $_POST['trangThai'] ?? null;
$namHoc = $_POST['namHoc'] ?? null;
$kyHoc = $_POST['kyHoc'] ?? null;
$maGV = $_POST['giaoVien'] ?: null;

if (!$tenLop) {
    echo json_encode(["status" => "error", "msg" => "Dữ liệu không hợp lệ"]);
    exit;
}

$sql = "INSERT INTO lophoc (tenLop, khoiLop, giaoVienPhuTrach, siSo, trangThai, namHoc, kyHoc)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sisisss", $tenLop, $khoiLop, $maGV, $siSo, $trangThai, $namHoc, $kyHoc);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "msg" => $stmt->error]);
}
?>