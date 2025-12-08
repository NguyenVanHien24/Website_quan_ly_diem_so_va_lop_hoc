<?php
require_once '../../config.php';
require_once '../../csdl/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit();
}

$maLop = isset($_POST['maLop']) ? (int)$_POST['maLop'] : 0;
$maMon = isset($_POST['maMon']) ? (int)$_POST['maMon'] : 0;
$maGV = isset($_POST['maGV']) ? (int)$_POST['maGV'] : 0;

if ($maLop <= 0 || $maMon <= 0 || $maGV <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit();
}

// Tạo table phan_cong nếu chưa tồn tại
$createSql = "CREATE TABLE IF NOT EXISTS phan_cong (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    maLop INT(11) NOT NULL,
    maMon INT(11) NOT NULL,
    maGV INT(11) NOT NULL,
    namHoc VARCHAR(20) DEFAULT NULL,
    kyHoc INT DEFAULT NULL,
    UNIQUE KEY uniq_lp_mon (maLop, maMon)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($createSql);

// Kiểm tra trùng
$check = "SELECT id FROM phan_cong WHERE maLop = '" . $conn->real_escape_string($maLop) . "' AND maMon = '" . $conn->real_escape_string($maMon) . "'";
$rs = $conn->query($check);
if ($rs && $rs->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Đã tồn tại phân công cho lớp và môn này']);
    exit();
}

$insert = "INSERT INTO phan_cong (maLop, maMon, maGV) VALUES ('" . $conn->real_escape_string($maLop) . "', '" . $conn->real_escape_string($maMon) . "', '" . $conn->real_escape_string($maGV) . "')";
if ($conn->query($insert)) {
    echo json_encode(['success' => true, 'message' => 'Phân công thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $conn->error]);
}
