<?php
require_once '../../config.php';
require_once '../../csdl/db.php';
header('Content-Type: application/json');

$maMon = isset($_GET['maMon']) ? (int)$_GET['maMon'] : 0;
if ($maMon <= 0) {
    echo json_encode(['success' => false, 'message' => 'maMon không hợp lệ']);
    exit();
}

$sql = "SELECT g.maGV, u.hoVaTen FROM giaovien g 
        JOIN giaovien_monhoc gm ON gm.idGV = g.maGV 
        JOIN `user` u ON u.userId = g.userId 
        WHERE gm.idMon = '" . $conn->real_escape_string($maMon) . "' AND g.trangThaiHoatDong = 'Hoạt động'";

$rs = $conn->query($sql);
if (!$rs) {
    echo json_encode(['success' => false, 'message' => 'Lỗi DB: ' . $conn->error]);
    exit();
}

$teachers = [];
while ($r = $rs->fetch_assoc()) {
    $teachers[] = ['maGV' => $r['maGV'], 'hoVaTen' => $r['hoVaTen']];
}

echo json_encode(['success' => true, 'data' => $teachers]);
