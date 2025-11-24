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
$giaoVien = $_POST['giaoVien'] ?? null;

if (!$tenLop) {
    echo json_encode(["status" => "error", "msg" => "Dữ liệu không hợp lệ"]);
    exit;
}

// Lấy mã giáo viên theo tên
$sqlGV = "SELECT maGV FROM giaovien gv 
          JOIN user u ON gv.userId = u.userId 
          WHERE u.hoVaTen = ?";
$stmtGV = $conn->prepare($sqlGV);
$stmtGV->bind_param("s", $giaoVien);
$stmtGV->execute();
$resGV = $stmtGV->get_result();

$maGV = null; // Mặc định null nếu không tìm thấy
if ($resGV && $resGV->num_rows > 0) {
    $maGV = $resGV->fetch_assoc()['maGV'];
}

// INSERT
$sql = "INSERT INTO lophoc (tenLop, khoiLop, giaoVienPhuTrach, siSo, trangThai, namHoc, kyHoc)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiisss", $tenLop, $khoiLop, $maGV, $siSo, $trangThai, $namHoc, $kyHoc);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "msg" => $stmt->error]);
}
?>