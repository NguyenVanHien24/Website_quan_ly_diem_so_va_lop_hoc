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

if (!isset($_SESSION['userID']) || $_SESSION['vaiTro'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$ids = [];
if (isset($_POST['ids'])) {
    $arr = json_decode($_POST['ids'], true);
    if (is_array($arr)) foreach ($arr as $v) $ids[] = (int)$v;
}

if (empty($ids)) {
    echo json_encode(['success' => false, 'message' => 'No ids provided']);
    exit();
}

// Fetch attachments to delete files
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$colChk = $conn->query("SHOW COLUMNS FROM `thongbao` LIKE 'attachment'");
if ($colChk && $colChk->num_rows > 0) {
    $stmt = $conn->prepare("SELECT maThongBao, attachment FROM thongbao WHERE maThongBao IN ($placeholders)");
} else {
    $stmt = $conn->prepare("SELECT maThongBao FROM thongbao WHERE maThongBao IN ($placeholders)");
}
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare error: '.$conn->error]);
    exit();
}
// bind params dynamically
$types = str_repeat('i', count($ids));
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$res = $stmt->get_result();
$attachments = [];
while ($r = $res->fetch_assoc()) {
    if (!empty($r['attachment'])) $attachments[] = $r['attachment'];
}
$stmt->close();

// delete rows
$conn->begin_transaction();
$delStmt = $conn->prepare("DELETE FROM thongbao WHERE maThongBao IN ($placeholders)");
if ($delStmt) {
    $delStmt->bind_param($types, ...$ids);
    $ok = $delStmt->execute();
    $delStmt->close();
    // delete related thongbaouser
    $del2 = $conn->prepare("DELETE FROM thongbaouser WHERE maTB IN ($placeholders)");
    if ($del2) {
        $del2->bind_param($types, ...$ids);
        $del2->execute();
        $del2->close();
    }
    $conn->commit();
    // remove files
    $uploadDir = __DIR__ . '/../../uploads/documents/';
    foreach ($attachments as $a) {
        $p = $uploadDir . $a;
        if (is_file($p)) @unlink($p);
    }
    echo json_encode(['success' => true, 'message' => 'Deleted']);
    exit();
} else {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Delete prepare error: '.$conn->error]);
    exit();
}

?>