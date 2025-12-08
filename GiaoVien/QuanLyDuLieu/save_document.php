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
$ok = $res && $res->num_rows > 0;
$stmt->close();

if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thêm tài liệu cho lớp và môn này']);
    exit();
}

if ($maTaiLieu > 0) {
    // Update
    $sql = "UPDATE tailieu SET tieuDe = ?, moTa = ?, fileTL = ? WHERE maTaiLieu = ? AND maMon = ? AND maLop = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare error: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param('ssiiii', $tieuDe, $moTa, $fileTL, $maTaiLieu, $maMon, $maLop);
} else {
    // Insert
    $sql = "INSERT INTO tailieu (maLop, maMon, tieuDe, moTa, fileTL) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare error: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param('iisss', $maLop, $maMon, $tieuDe, $moTa, $fileTL);
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
