<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../config.php';
require_once '../../CSDL/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

if (!isset($_SESSION['userID']) || $_SESSION['vaiTro'] !== 'GiaoVien') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$userID = $_SESSION['userID'];
$maHS = isset($_POST['maHS']) ? (int)$_POST['maHS'] : 0;
$maMon = isset($_POST['maMon']) ? (int)$_POST['maMon'] : 0;
$maLop = isset($_POST['maLop']) ? (int)$_POST['maLop'] : 0;
$namHoc = isset($_POST['namHoc']) ? trim($_POST['namHoc']) : '';
$hocKy = isset($_POST['hocKy']) ? (int)$_POST['hocKy'] : 1;
$mouth = isset($_POST['mouth']) ? trim($_POST['mouth']) : '';
$m45 = isset($_POST['45m']) ? trim($_POST['45m']) : '';
$gk = isset($_POST['gk']) ? trim($_POST['gk']) : '';
$ck = isset($_POST['ck']) ? trim($_POST['ck']) : '';

if ($maHS <= 0 || $maMon <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

$stmt = $conn->prepare("SELECT g.maGV FROM giaovien g JOIN phan_cong p ON p.maGV = g.maGV WHERE g.userId = ? AND p.maMon = ? LIMIT 1");
$stmt->bind_param('ii', $userID, $maMon);
$stmt->execute();
$res = $stmt->get_result();
$ok = $res && $res->num_rows > 0;
$stmt->close();

if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized for this subject']);
    exit();
}

function reverseMapLoaiDiem($key) {
    switch($key) {
        case 'mouth': return 'Điểm miệng';
        case '45m': return 'Điểm 1 tiết';
        case 'gk': return 'Điểm giữa kỳ';
        case 'ck': return 'Điểm cuối kỳ';
        default: return $key;
    }
}

$scoreData = ['mouth' => $mouth, '45m' => $m45, 'gk' => $gk, 'ck' => $ck];

foreach ($scoreData as $key => $value) {
    if (empty($value)) continue;
    
    $loaiDiem = reverseMapLoaiDiem($key);
    
    $sql = "INSERT INTO diemso (maHS, maMonHoc, maLop, loaiDiem, giaTriDiem, namHoc, hocKy, ngayGhiNhan) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
            giaTriDiem = VALUES(giaTriDiem),
            maLop = VALUES(maLop),
            namHoc = VALUES(namHoc),
            hocKy = VALUES(hocKy),
            ngayGhiNhan = NOW()";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare error: ' . $conn->error]);
        exit();
    }
    
    $stmt->bind_param('iiisisi', $maHS, $maMon, $maLop, $loaiDiem, $value, $namHoc, $hocKy);
    
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Execute error: ' . $stmt->error]);
        $stmt->close();
        exit();
    }
    
    $stmt->close();
}

echo json_encode(['success' => true, 'message' => 'Lưu điểm thành công']);
exit();
