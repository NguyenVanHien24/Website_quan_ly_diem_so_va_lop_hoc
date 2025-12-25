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

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
$offset = ($page - 1) * $limit;

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

$countSql = "SELECT COUNT(*) as total FROM tailieu WHERE maMon = ? AND maLop = ?";
$cstmt = $conn->prepare($countSql);
$cstmt->bind_param('ii', $maMon, $maLop);
$cstmt->execute();
$total = $cstmt->get_result()->fetch_assoc()['total'] ?? 0;
$cstmt->close();

$sql = "SELECT maTaiLieu, tieuDe, moTa, fileTL, ngayTao, hanNop 
        FROM tailieu 
        WHERE maMon = ? AND maLop = ?
        ORDER BY ngayTao DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiii', $maMon, $maLop, $limit, $offset);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
$stt = $offset + 1;
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

$totalPages = ($limit > 0) ? (int)ceil($total / $limit) : 1;

echo json_encode([
    'success' => true,
    'data' => $data,
    'meta' => [
        'total' => (int)$total,
        'page' => (int)$page,
        'limit' => (int)$limit,
        'totalPages' => (int)$totalPages,
        'start' => ($total > 0) ? ($offset + 1) : 0,
        'end' => min($offset + $limit, $total)
    ]
]);
exit();
?>
