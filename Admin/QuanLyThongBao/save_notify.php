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

$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$send_at = isset($_POST['send_at']) && $_POST['send_at'] !== '' ? $_POST['send_at'] : null; // datetime-local -> YYYY-MM-DDTHH:MM
$target_type = isset($_POST['target_type']) ? trim($_POST['target_type']) : 'all';
$target_value = isset($_POST['target_value']) ? trim($_POST['target_value']) : '';

$nguoiGui = (int)$_SESSION['userID'];

if ($title === '') {
    echo json_encode(['success' => false, 'message' => 'Tiêu đề không được để trống']);
    exit();
}
$send_at_db = null;
if ($send_at !== null && $send_at !== '') {
    // normalize datetime-local input (YYYY-MM-DDTHH:MM or YYYY-MM-DD HH:MM) to MySQL DATETIME (YYYY-MM-DD HH:MM:SS)
    $send_at_db = str_replace('T', ' ', $send_at);
    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $send_at_db)) {
        $send_at_db .= ':00';
    }
}

$t = $conn->real_escape_string($title);
$d = $conn->real_escape_string($content);
$sa = $send_at_db !== null ? ("'" . $conn->real_escape_string($send_at_db) . "'") : 'NULL';
$sqlIns = "INSERT INTO thongbao (tieuDe, noiDung, nguoiGui, send_at) VALUES ('" . $t . "', '" . $d . "', " . (int)$nguoiGui . ", " . $sa . ")";
if (!$conn->query($sqlIns)) {
    echo json_encode(['success' => false, 'message' => 'Insert error: ' . $conn->error]);
    exit();
}
$maTB = $conn->insert_id;

// Nếu bảng thongbao có cột lưu target_type/target_value thì cập nhật để lưu metadata
$hasTargetType = false;
$colsRs = $conn->query("SHOW COLUMNS FROM `thongbao` LIKE 'target_type'");
if ($colsRs && $colsRs->num_rows > 0) {
    $hasTargetType = true;
}
if ($hasTargetType) {
    $tv = $conn->real_escape_string($target_value);
    $tt = $conn->real_escape_string($target_type);
    $conn->query("UPDATE thongbao SET target_type = '" . $tt . "', target_value = '" . $tv . "' WHERE maThongBao = " . (int)$maTB);
}

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
            if (!empty($colAdded)) {
                $conn->query("UPDATE thongbao SET attachment = '" . $conn->real_escape_string($attachmentName) . "' WHERE maThongBao = " . (int)$maTB);
            }
        }
    }
}

$userIds = [];

if ($target_type === 'all') {
    $rs = $conn->query("SELECT userId FROM `user`");
    while ($r = $rs->fetch_assoc()) $userIds[] = (int)$r['userId'];
} elseif ($target_type === 'role') {
    $role = $conn->real_escape_string($target_value);
    $rs = $conn->query("SELECT userId FROM `user` WHERE vaiTro = '" . $role . "'");
    while ($r = $rs->fetch_assoc()) $userIds[] = (int)$r['userId'];
} elseif ($target_type === 'class') {
    $maLop = (int)$target_value;
    $sql = "SELECT u.userId FROM `user` u JOIN hocsinh hs ON u.userId = hs.userId WHERE hs.maLopHienTai = '" . $maLop . "'";
    $rs = $conn->query($sql);
    while ($r = $rs->fetch_assoc()) $userIds[] = (int)$r['userId'];
} elseif ($target_type === 'users') {
    $arr = json_decode($target_value, true);
    if (is_array($arr)) {
        foreach ($arr as $v) $userIds[] = (int)$v;
    }
}

$shouldDistribute = true;
if ($send_at_db !== null && strtotime($send_at_db) > time()) {
    $shouldDistribute = false;
}

$inserted = 0;
if ($shouldDistribute && !empty($userIds)) {
    $conn->begin_transaction();
    $stmt = $conn->prepare("INSERT INTO thongbaouser (maTB, userId, trangThai) VALUES (?, ?, 0)");
    $errors = [];
    if ($stmt) {
        foreach ($userIds as $uid) {
            $uid = (int)$uid;
            if ($stmt->bind_param('ii', $maTB, $uid)) {
                if ($stmt->execute()) {
                    $inserted++;
                } else {
                    $errors[] = ['userId' => $uid, 'error' => $stmt->error];
                }
            } else {
                $errors[] = ['userId' => $uid, 'error' => $stmt->error ?: $conn->error];
            }
        }
        $stmt->close();
    } else {
        $errors[] = ['prepare_error' => $conn->error];
    }
    // If we actually distributed recipients immediately, mark send_at cleared and set ngayGui
    if ($inserted > 0) {
        $conn->query("UPDATE thongbao SET send_at = NULL, ngayGui = NOW() WHERE maThongBao = " . (int)$maTB);
    }
    $conn->commit();
}
if ($shouldDistribute) {
    if ($inserted === 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Thông báo đã được lưu nhưng chưa có người nhận (Chưa phân phối).',
            'maThongBao' => $maTB,
            'target_type' => $target_type,
            'target_value' => $target_value,
            'recipients' => $inserted
        ]);
        exit();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Đã gửi thông báo',
        'maThongBao' => $maTB,
        'target_type' => $target_type,
        'target_value' => $target_value,
        'recipients' => $inserted
    ]);
    exit();

} else {
    echo json_encode([
        'success' => true,
        'message' => 'Thông báo đã được lưu và lên lịch',
        'maThongBao' => $maTB,
        'target_type' => $target_type,
        'target_value' => $target_value,
        'recipients' => 0
    ]);
    exit();
}
