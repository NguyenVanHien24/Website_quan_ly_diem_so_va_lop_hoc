<?php
ob_start();
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

require_once '../../config.php';
require_once '../../CSDL/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();

$resp = ['success' => false, 'notifications' => [], 'unread' => 0];
try {
    if (!isset($_SESSION['userID'])) {
        echo json_encode($resp);
        exit;
    }
    $userId = (int)$_SESSION['userID'];

    $sql = "SELECT tbu.id AS tbuId, tbu.maTB, tbu.trangThai, COALESCE(tb.send_at, tb.ngayGui) AS ngayGui, tb.tieuDe, tb.noiDung
            FROM thongbaouser tbu
            JOIN thongbao tb ON tbu.maTB = tb.maThongBao
            WHERE tbu.userId = ?
            ORDER BY tbu.id DESC
            LIMIT 200";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $notes = [];
    while ($r = $res->fetch_assoc()) {
        $notes[] = [
            'tbuId' => (int)$r['tbuId'],
            'maTB' => $r['maTB'],
            'tieuDe' => $r['tieuDe'],
            'noiDung' => $r['noiDung'],
            'ngayGui' => $r['ngayGui'],
            'trangThai' => (int)$r['trangThai']
        ];
    }
    $stmt->close();

    $cstmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM thongbaouser WHERE userId = ? AND trangThai = 0");
    $cstmt->bind_param('i', $userId);
    $cstmt->execute();
    $cres = $cstmt->get_result();
    $crow = $cres->fetch_assoc();
    $unread = (int)($crow['cnt'] ?? 0);
    $cstmt->close();

    $raw = trim(ob_get_clean());

    $resp['success'] = true;
    $resp['notifications'] = $notes;
    $resp['unread'] = $unread;
    if ($raw !== '') $resp['raw'] = $raw;

    echo json_encode($resp);
} catch (Exception $e) {
    ob_get_clean();
    echo json_encode($resp);
}

?>
