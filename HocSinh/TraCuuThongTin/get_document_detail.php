<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../config.php';
require_once '../../CSDL/db.php';

if (!isset($_SESSION['userID']) || $_SESSION['vaiTro'] !== 'HocSinh') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$userID = $_SESSION['userID'];
$maTaiLieu = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($maTaiLieu <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid document']);
    exit();
}

// Get student's class
$stmt = $conn->prepare("SELECT maLopHienTai as maLop FROM hocsinh WHERE userId = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $conn->error]);
    exit();
}
$stmt->bind_param('i', $userID);
$stmt->execute();
$res = $stmt->get_result();
$student = $res->fetch_assoc();
$stmt->close();

if (!$student) {
    echo json_encode(['success' => false, 'message' => 'Student not found']);
    exit();
}

$maLop = $student['maLop'];

// If `tailieu` has maLop column, enforce class matching; otherwise allow by document id (subject-level)
$colRes = $conn->query("SHOW COLUMNS FROM tailieu LIKE 'maLop'");
$hasMaLop = ($colRes && $colRes->num_rows > 0);

if ($hasMaLop) {
    $sql = "SELECT t.maTaiLieu as id, t.tieuDe, t.moTa, t.fileTL, m.tenMon, u.hoVaTen, t.ngayTao
            FROM tailieu t
            JOIN monhoc m ON m.maMon = t.maMon
            JOIN giaovien g ON g.maGV = t.maGV
            JOIN user u ON u.userId = g.userId
            WHERE t.maTaiLieu = ? AND t.maLop = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param('ii', $maTaiLieu, $maLop);
} else {
    // Fallback: fetch by id only
    $sql = "SELECT t.maTaiLieu as id, t.tieuDe, t.moTa, t.fileTL, m.tenMon, NULL as hoVaTen, t.ngayTao
            FROM tailieu t
            JOIN monhoc m ON m.maMon = t.maMon
            WHERE t.maTaiLieu = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param('i', $maTaiLieu);
}
$stmt->execute();
$res = $stmt->get_result();
$document = $res->fetch_assoc();
$stmt->close();

if (!$document) {
    echo json_encode(['success' => false, 'message' => 'Document not found']);
    exit();
}

echo json_encode(['success' => true, 'data' => $document]);
exit();
?>
