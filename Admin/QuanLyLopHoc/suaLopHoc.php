<?php
require_once '../../config.php';
require_once '../../csdl/db.php';

header('Content-Type: application/json');

$maLop = $_POST['maLop'];
$tenLop = $_POST['tenLop'];
$khoiLop = $_POST['khoiLop'];
$siSo = $_POST['siSo'];
$trangThai = $_POST['trangThai'];
$namHoc = $_POST['namHoc'];
$kyHoc = $_POST['kyHoc'];
$maGV = $_POST['giaoVien'] ?: null; // dùng trực tiếp maGV từ form, cho phép null

$sql = "UPDATE lophoc
        SET tenLop=?, khoiLop=?, giaoVienPhuTrach=?, siSo=?, trangThai=?, namHoc=?, kyHoc=?
        WHERE maLop=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sisisssi", $tenLop, $khoiLop, $maGV, $siSo, $trangThai, $namHoc, $kyHoc, $maLop);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "msg" => $stmt->error]);
}
?>
