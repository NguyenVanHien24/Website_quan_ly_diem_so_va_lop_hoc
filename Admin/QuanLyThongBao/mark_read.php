<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../../config.php';
require_once '../../CSDL/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid request']);
    exit();
}
if (!isset($_SESSION['userID'])) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit();
}
$userId = (int)$_SESSION['userID'];

$tbuId = isset($_POST['tbuId']) ? (int)$_POST['tbuId'] : 0;
if ($tbuId > 0) {
    $stmt = $conn->prepare("UPDATE thongbaouser SET trangThai = 1 WHERE id = ? AND userId = ?");
    if ($stmt) {
        $stmt->bind_param('ii', $tbuId, $userId);
        $stmt->execute();
        $stmt->close();
        $cntRs = $conn->query("SELECT COUNT(*) AS cnt FROM thongbaouser WHERE userId = " . $userId . " AND trangThai = 0");
        $unread = 0;
        if ($cntRs) { $c = $cntRs->fetch_assoc(); $unread = (int)($c['cnt'] ?? 0); }
        echo json_encode(['success'=>true, 'unread'=>$unread]);
        exit();
    }
}

if (isset($_POST['all']) && $_POST['all']) {
    $conn->query("UPDATE thongbaouser SET trangThai = 1 WHERE userId = " . $userId);
    $cntRs = $conn->query("SELECT COUNT(*) AS cnt FROM thongbaouser WHERE userId = " . $userId . " AND trangThai = 0");
    $unread = 0;
    if ($cntRs) { $c = $cntRs->fetch_assoc(); $unread = (int)($c['cnt'] ?? 0); }
    echo json_encode(['success'=>true, 'unread'=>$unread]);
    exit();
}

echo json_encode(['success'=>false,'message'=>'No-op']);
exit();
?>