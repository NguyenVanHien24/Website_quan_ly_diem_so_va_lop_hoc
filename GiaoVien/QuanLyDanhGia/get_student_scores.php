<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../config.php';
require_once '../../CSDL/db.php';

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid request method']);
    exit();
}
if (!isset($_SESSION['userID']) || $_SESSION['vaiTro'] !== 'GiaoVien') {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit();
}

$userID = $_SESSION['userID'];
$maHS = isset($_POST['maHS']) ? (int)$_POST['maHS'] : 0;
$maMon = isset($_POST['maMon']) ? (int)$_POST['maMon'] : 0;
$maLop = isset($_POST['maLop']) ? (int)$_POST['maLop'] : 0;
$namHoc = isset($_POST['namHoc']) ? trim($_POST['namHoc']) : '';
$hocKy = isset($_POST['hocKy']) ? (int)$_POST['hocKy'] : 1;

if ($maHS <= 0 || $maMon <= 0) {
    echo json_encode(['success'=>false,'message'=>'Invalid parameters']);
    exit();
}

// Validate teacher assignment
$stmt = $conn->prepare("SELECT g.maGV FROM giaovien g JOIN phan_cong p ON p.maGV = g.maGV WHERE g.userId = ? AND p.maMon = ? LIMIT 1");
$stmt->bind_param('ii', $userID, $maMon);
$stmt->execute();
$res = $stmt->get_result();
$ok = $res && $res->num_rows > 0;
$stmt->close();
if (!$ok) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized for this subject']);
    exit();
}

// Function to map Vietnamese score names to keys
function mapLoaiDiem($loai) {
    $loai = mb_strtolower(trim($loai), 'UTF-8');
    if (mb_strpos($loai, 'miệng') !== false) return 'mouth';
    if (mb_strpos($loai, '1 tiết') !== false || mb_strpos($loai, '1tiết') !== false) return '45m';
    if (mb_strpos($loai, 'giữa kỳ') !== false || mb_strpos($loai, 'gk') !== false) return 'gk';
    if (mb_strpos($loai, 'cuối kỳ') !== false || mb_strpos($loai, 'ck') !== false) return 'ck';
    return $loai;
}

// Fetch scores for this student and subject
$scores = ['mouth' => '', '45m' => '', 'gk' => '', 'ck' => ''];
$sql = "SELECT loaiDiem, giaTriDiem FROM diemso WHERE maHS = '" . $conn->real_escape_string($maHS) . "' AND maMonHoc = '" . $conn->real_escape_string($maMon) . "'";
if (!empty($namHoc)) {
    $sql .= " AND namHoc = '" . $conn->real_escape_string($namHoc) . "'";
}
if (!empty($hocKy)) {
    $sql .= " AND hocKy = '" . $conn->real_escape_string($hocKy) . "'";
}
$rs = $conn->query($sql);
if ($rs) {
    while ($row = $rs->fetch_assoc()) {
        $key = mapLoaiDiem($row['loaiDiem']);
        $scores[$key] = $row['giaTriDiem'];
    }
}

echo json_encode(['success'=>true,'scores'=>$scores]);
exit();
