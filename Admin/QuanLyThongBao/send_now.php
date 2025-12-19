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

$ma = isset($_POST['maThongBao']) ? (int)$_POST['maThongBao'] : 0;
if ($ma <= 0) { echo json_encode(['success'=>false,'message'=>'Invalid id']); exit(); }

// Load target info
$rs = $conn->query("SELECT target_type, target_value, send_at FROM thongbao WHERE maThongBao = " . (int)$ma . " LIMIT 1");
if (!$rs || $rs->num_rows === 0) { echo json_encode(['success'=>false,'message'=>'Not found']); exit(); }
$row = $rs->fetch_assoc();
$target_type = $row['target_type'] ?? 'all';
$target_value = $row['target_value'] ?? '';

// Build user list
$userIds = [];
if ($target_type === 'all') {
    $r2 = $conn->query("SELECT userId FROM `user`");
    if ($r2) while ($u = $r2->fetch_assoc()) $userIds[] = (int)$u['userId'];
} elseif ($target_type === 'role') {
    $role = $conn->real_escape_string($target_value);
    $r2 = $conn->query("SELECT userId FROM `user` WHERE vaiTro = '".$role."'");
    if ($r2) while ($u = $r2->fetch_assoc()) $userIds[] = (int)$u['userId'];
} elseif ($target_type === 'class') {
    $maLop = (int)$target_value;
    $r2 = $conn->query("SELECT u.userId FROM `user` u JOIN hocsinh hs ON u.userId = hs.userId WHERE hs.maLopHienTai = '".$maLop."'");
    if ($r2) while ($u = $r2->fetch_assoc()) $userIds[] = (int)$u['userId'];
} elseif ($target_type === 'users') {
    $arr = json_decode($target_value, true);
    if (is_array($arr)) foreach ($arr as $v) $userIds[] = (int)$v;
}

$inserted = 0; $errors = [];
if (!empty($userIds)) {
    $conn->begin_transaction();
    $stmt = $conn->prepare("INSERT INTO thongbaouser (maTB, userId, trangThai) VALUES (?, ?, 0)");
    if ($stmt) {
        foreach ($userIds as $uid) {
            $uid = (int)$uid;
            if ($stmt->bind_param('ii', $ma, $uid) && $stmt->execute()) {
                $inserted++;
            } else {
                $errors[] = ['userId'=>$uid, 'error'=>$stmt->error ?: $conn->error];
            }
        }
        $stmt->close();
    } else {
        $errors[] = ['prepare_error' => $conn->error];
    }
    if ($inserted > 0) {
        $conn->query("UPDATE thongbao SET send_at = NULL, ngayGui = NOW() WHERE maThongBao = " . (int)$ma);
    }
    $conn->commit();
}

echo json_encode(['success'=>true,'inserted'=>$inserted,'errors'=>$errors]);
exit();

?>