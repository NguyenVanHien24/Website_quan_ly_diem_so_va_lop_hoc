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

if (!isset($_SESSION['userID']) || $_SESSION['vaiTro'] !== 'GiaoVien') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$userID = $_SESSION['userID'];
$maTaiLieu = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$tieuDe = isset($_POST['title']) ? trim($_POST['title']) : '';
$moTa = isset($_POST['desc']) ? trim($_POST['desc']) : '';
$maMon = isset($_POST['maMon']) ? (int)$_POST['maMon'] : 0;
$maLop = isset($_POST['maLop']) ? (int)$_POST['maLop'] : 0;
$fileTL = isset($_POST['fileName']) ? trim($_POST['fileName']) : '';

// Handle uploaded file (from teacher). Save into uploads/documents and set $fileTL to the stored filename.
$uploadedFileName = null;
$uploadsDir = realpath(__DIR__ . '/../../uploads/documents');
if (!$uploadsDir) {
    // try to create
    @mkdir(__DIR__ . '/../../uploads/documents', 0755, true);
    $uploadsDir = realpath(__DIR__ . '/../../uploads/documents');
}
if (isset($_FILES['file']) && isset($_FILES['file']['error']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['file']['tmp_name'];
    $origName = $_FILES['file']['name'];
    $ext = pathinfo($origName, PATHINFO_EXTENSION);
    $base = pathinfo($origName, PATHINFO_FILENAME);
    $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
    $newName = $safeBase . '_' . time() . ($ext ? '.' . $ext : '');
    if ($uploadsDir) {
        $target = $uploadsDir . DIRECTORY_SEPARATOR . $newName;
        if (move_uploaded_file($tmp, $target)) {
            $uploadedFileName = $newName;
        }
    }
}

if (empty($tieuDe) || $maMon <= 0 || $maLop <= 0) {
    echo json_encode(['success' => false, 'message' => 'Tiêu đề, lớp và môn học không được để trống']);
    exit();
}

// Validate teacher assignment for this class and subject
$stmt = $conn->prepare("SELECT g.maGV FROM giaovien g JOIN phan_cong p ON p.maGV = g.maGV 
                        WHERE g.userId = ? AND p.maMon = ? AND p.maLop = ? LIMIT 1");
$stmt->bind_param('iii', $userID, $maMon, $maLop);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;
$ok = $row && isset($row['maGV']);
$maGV = $ok ? (int)$row['maGV'] : 0;
$stmt->close();

// Check whether `tailieu` table has `maGV` column. If not, we'll skip saving it to avoid SQL errors.
$colRes = $conn->query("SHOW COLUMNS FROM tailieu LIKE 'maGV'");
$hasMaGV = ($colRes && $colRes->num_rows > 0);

if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thêm tài liệu cho lớp và môn này']);
    exit();
}

// If editing and no new uploaded file and no fileName provided, preserve existing fileTL from DB
if ($maTaiLieu > 0) {
    if (!$uploadedFileName && empty($fileTL)) {
        $sel = $conn->prepare("SELECT fileTL FROM tailieu WHERE maTaiLieu = ? LIMIT 1");
        if ($sel) {
            $sel->bind_param('i', $maTaiLieu);
            $sel->execute();
            $r = $sel->get_result();
            $rr = $r ? $r->fetch_assoc() : null;
            if ($rr && !empty($rr['fileTL'])) $fileTL = $rr['fileTL'];
            $sel->close();
        }
    }
}

// If a new file was uploaded, override $fileTL
if ($uploadedFileName) {
    $fileTL = $uploadedFileName;
}

if ($maTaiLieu > 0) {
    // Update
    if ($hasMaGV) {
        $sql = "UPDATE tailieu SET tieuDe = ?, moTa = ?, fileTL = ?, maGV = ? WHERE maTaiLieu = ? AND maMon = ? AND maLop = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Prepare error: ' . $conn->error]);
            exit();
        }
        $stmt->bind_param('sssiiii', $tieuDe, $moTa, $fileTL, $maGV, $maTaiLieu, $maMon, $maLop);
    } else {
        $sql = "UPDATE tailieu SET tieuDe = ?, moTa = ?, fileTL = ? WHERE maTaiLieu = ? AND maMon = ? AND maLop = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Prepare error: ' . $conn->error]);
            exit();
        }
        $stmt->bind_param('sssiii', $tieuDe, $moTa, $fileTL, $maTaiLieu, $maMon, $maLop);
    }
} else {
    // Insert
    if ($hasMaGV) {
        $sql = "INSERT INTO tailieu (maLop, maMon, maGV, tieuDe, moTa, fileTL) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Prepare error: ' . $conn->error]);
            exit();
        }
        $stmt->bind_param('iiisss', $maLop, $maMon, $maGV, $tieuDe, $moTa, $fileTL);
    } else {
        $sql = "INSERT INTO tailieu (maLop, maMon, tieuDe, moTa, fileTL) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Prepare error: ' . $conn->error]);
            exit();
        }
        $stmt->bind_param('iisss', $maLop, $maMon, $tieuDe, $moTa, $fileTL);
    }
}

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Execute error: ' . $stmt->error]);
    $stmt->close();
    exit();
}

$stmt->close();
echo json_encode(['success' => true, 'message' => $maTaiLieu > 0 ? 'Cập nhật thành công' : 'Thêm tài liệu thành công']);
exit();
?>
