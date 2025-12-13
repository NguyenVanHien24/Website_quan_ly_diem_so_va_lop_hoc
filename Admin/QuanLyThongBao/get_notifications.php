<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../../config.php';
require_once '../../CSDL/db.php';

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit();
}
$userId = (int)$_SESSION['userID'];

$sql = "SELECT tbu.id AS tbuId, tbu.maTB, tbu.trangThai, tb.tieuDe, tb.noiDung, tb.send_at, tb.ngayGui, u.hoVaTen AS nguoiGui
        FROM thongbaouser tbu
        JOIN thongbao tb ON tbu.maTB = tb.maThongBao
        LEFT JOIN `user` u ON tb.nguoiGui = u.userId
        WHERE tbu.userId = " . $userId . "
        ORDER BY tbu.id DESC
        LIMIT 0,20";

$rows = [];
$rs = $conn->query($sql);
if ($rs) {
    while ($r = $rs->fetch_assoc()) {
        $rows[] = $r;
    }
}

$cntRs = $conn->query("SELECT COUNT(*) AS cnt FROM thongbaouser WHERE userId = " . $userId . " AND trangThai = 0");
$unread = 0;
if ($cntRs) { $c = $cntRs->fetch_assoc(); $unread = (int)($c['cnt'] ?? 0); }

echo json_encode(['success'=>true,'notifications'=>$rows,'unread'=>$unread]);
exit();

?>