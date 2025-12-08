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

if ($maTaiLieu <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid document']);
    exit();
}

// Verify teacher owns this document (check if teacher teaches the subject)
$stmt = $conn->prepare("SELECT t.maTaiLieu, t.maMon, t.maLop 
                        FROM tailieu t
                        WHERE t.maTaiLieu = ?");
$stmt->bind_param('i', $maTaiLieu);
$stmt->execute();
$res = $stmt->get_result();
$doc = $res->fetch_assoc();
$stmt->close();

if (!$doc) {
    echo json_encode(['success' => false, 'message' => 'Document not found']);
    exit();
}

// Validate teacher assignment for this class and subject
$stmt = $conn->prepare("SELECT g.maGV FROM giaovien g JOIN phan_cong p ON p.maGV = g.maGV 
                        WHERE g.userId = ? AND p.maMon = ? AND p.maLop = ? LIMIT 1");
$stmt->bind_param('iii', $userID, $doc['maMon'], $doc['maLop']);
$stmt->execute();
$res = $stmt->get_result();
$ok = $res && $res->num_rows > 0;
$stmt->close();

if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized to delete this document']);
    exit();
}

// Delete document
$sql = "DELETE FROM tailieu WHERE maTaiLieu = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $maTaiLieu);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    $stmt->close();
    exit();
}

$stmt->close();
echo json_encode(['success' => true, 'message' => 'Xóa tài liệu thành công']);
exit();
?>
