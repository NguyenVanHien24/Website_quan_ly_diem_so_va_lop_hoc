<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../config.php';
require_once '../../CSDL/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

if (!isset($_SESSION['userID']) || $_SESSION['vaiTro'] !== 'GiaoVien') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$userID = $_SESSION['userID'];
$maLop = isset($_GET['maLop']) ? (int)$_GET['maLop'] : 0;
$maMon = isset($_GET['maMon']) ? (int)$_GET['maMon'] : 0;

if ($maMon <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid subject']);
    exit();
}

$stmt = $conn->prepare("SELECT g.maGV FROM giaovien g JOIN phan_cong p ON p.maGV = g.maGV 
                        WHERE g.userId = ? AND p.maMon = ? LIMIT 1");
$stmt->bind_param('ii', $userID, $maMon);
$stmt->execute();
$res = $stmt->get_result();
$ok = $res && $res->num_rows > 0;
$stmt->close();

if (!$ok) {
    echo json_encode(['success' => true, 'data' => []]);
    exit();
}

$sql = "SELECT maTaiLieu, tieuDe, moTa, fileTL, ngayTao, hanNop 
        FROM tailieu 
        WHERE maMon = ? AND maLop = ?
        ORDER BY ngayTao DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $maMon, $maLop);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
$stt = 1;
while ($row = $res->fetch_assoc()) {
    $data[] = [
        'stt' => $stt++,
        'id' => $row['maTaiLieu'],
        'tieuDe' => $row['tieuDe'],
        'moTa' => $row['moTa'],
        'fileTL' => $row['fileTL'],
        'ngayTao' => $row['ngayTao'],
        'hanNop' => $row['hanNop']
    ];
}
$stmt->close();

echo json_encode(['success' => true, 'data' => $data]);
exit();
?>
