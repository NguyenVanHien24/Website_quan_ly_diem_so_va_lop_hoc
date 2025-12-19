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
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$send_at = isset($_POST['send_at']) && $_POST['send_at'] !== '' ? $_POST['send_at'] : null;
$target_type = isset($_POST['target_type']) ? trim($_POST['target_type']) : 'all';
$target_value = isset($_POST['target_value']) ? trim($_POST['target_value']) : '';

$attachmentName = null;
if (!empty($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
    $colRs = $conn->query("SHOW COLUMNS FROM `thongbao` LIKE 'attachment'");
    if (!$colRs || $colRs->num_rows === 0) {
        if (!$conn->query("ALTER TABLE thongbao ADD COLUMN attachment VARCHAR(255) DEFAULT NULL")) {
            $alterError = $conn->error;
            $colAdded = false;
        } else {
            $colAdded = true;
        }
    } else {
        $colAdded = true;
    }
    $uploadDir = __DIR__ . '/../../uploads/documents/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $f = $_FILES['attachment'];
    if ($f['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
        $base = uniqid('tb_') . '.' . $ext;
        $dest = $uploadDir . $base;
        if (move_uploaded_file($f['tmp_name'], $dest)) {
            $attachmentName = $base;
        }
    }
}

// normalize incoming datetime-local so it's stored as 'Y-m-d H:i:s' (preserve local time)
if ($send_at !== null && $send_at !== '') {
    if (strpos($send_at, 'T') !== false) {
        $send_at = str_replace('T', ' ', $send_at);
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $send_at)) $send_at .= ':00';
    }
}

if ($ma <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid maThongBao']);
    exit();
}

$params = [];
$sets = [];
if ($title !== '') { $sets[] = "tieuDe = ?"; $params[] = $title; }
if ($content !== '') { $sets[] = "noiDung = ?"; $params[] = $content; }
if ($send_at !== null) { $sets[] = "send_at = ?"; $params[] = $send_at; }

if (!empty($sets)) {
    $sql = "UPDATE thongbao SET " . implode(', ', $sets) . " WHERE maThongBao = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) { echo json_encode(['success'=>false,'message'=>'Prepare error: '.$conn->error]); exit(); }
    
    $types = str_repeat('s', count($params)) . 'i';
    $params[] = $ma;
    $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) { echo json_encode(['success'=>false,'message'=>'Execute error: '.$stmt->error]); $stmt->close(); exit(); }
    $stmt->close();
}

$colsRs = $conn->query("SHOW COLUMNS FROM `thongbao` LIKE 'target_type'");
if ($colsRs && $colsRs->num_rows > 0) {
    $tt = $conn->real_escape_string($target_type);
    $tv = $conn->real_escape_string($target_value);
    $conn->query("UPDATE thongbao SET target_type='".$tt."', target_value='".$tv."' WHERE maThongBao=".(int)$ma);
}

if ($attachmentName !== null) {
    $check = $conn->query("SHOW COLUMNS FROM `thongbao` LIKE 'attachment'");
    if ($check && $check->num_rows > 0) {
        $conn->query("UPDATE thongbao SET attachment='".$conn->real_escape_string($attachmentName)."' WHERE maThongBao=".(int)$ma);
    }
}

$conn->begin_transaction();
$conn->query("DELETE FROM thongbaouser WHERE maTB = " . (int)$ma);
$userIds = [];
if ($target_type === 'all') {
    $rs = $conn->query("SELECT userId FROM `user`");
    while ($r = $rs->fetch_assoc()) $userIds[] = (int)$r['userId'];
} elseif ($target_type === 'role') {
    $role = $conn->real_escape_string($target_value);
    $rs = $conn->query("SELECT userId FROM `user` WHERE vaiTro = '".$role."'");
    while ($r = $rs->fetch_assoc()) $userIds[] = (int)$r['userId'];
} elseif ($target_type === 'class') {
    $maLop = (int)$target_value;
    $rs = $conn->query("SELECT u.userId FROM `user` u JOIN hocsinh hs ON u.userId = hs.userId WHERE hs.maLopHienTai = '".$maLop."'");
    while ($r = $rs->fetch_assoc()) $userIds[] = (int)$r['userId'];
} elseif ($target_type === 'users') {
    $arr = json_decode($target_value, true);
    if (is_array($arr)) foreach ($arr as $v) $userIds[] = (int)$v;
}

$current_send_at = null;
if ($send_at !== null) {
    $current_send_at = $send_at;
} else {
    $rsx = $conn->query("SELECT send_at FROM thongbao WHERE maThongBao = " . (int)$ma);
    if ($rsx && $rx = $rsx->fetch_assoc()) $current_send_at = $rx['send_at'];
}

$shouldDistribute = true;
if ($current_send_at !== null && $current_send_at !== '' && strtotime($current_send_at) > time()) {
    $shouldDistribute = false;
}

$inserted = 0; $errors = [];
if ($shouldDistribute && !empty($userIds)) {
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
}

// If we inserted recipients immediately, clear send_at so UI marks as sent
if ($inserted > 0) {
    $conn->query("UPDATE thongbao SET send_at = NULL, ngayGui = NOW() WHERE maThongBao = " . (int)$ma);
}

$conn->commit();

echo json_encode(['success'=>true,'message'=>'Cập nhật xong','maThongBao'=>$ma,'recipients'=>$inserted,'errors'=>$errors]);
exit();

?>
