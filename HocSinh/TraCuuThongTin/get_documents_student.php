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
$maMon = isset($_GET['maMon']) ? (int)$_GET['maMon'] : 0;

if ($maMon <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid subject']);
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

$hasMaLop = false;
$colRes = $conn->query("SHOW COLUMNS FROM tailieu LIKE 'maLop'");
if ($colRes && $colRes->num_rows > 0) $hasMaLop = true;

if ($hasMaLop) {
    $sql = "SELECT t.maTaiLieu as id, t.tieuDe, t.moTa, t.fileTL, m.tenMon, u.hoVaTen, t.ngayTao
            FROM tailieu t
            JOIN monhoc m ON m.maMon = t.maMon
            LEFT JOIN giaovien g ON g.maGV = t.maGV
            LEFT JOIN user u ON u.userId = g.userId
            WHERE t.maMon = ? AND t.maLop = ?
            ORDER BY t.ngayTao DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param('ii', $maMon, $maLop);
} else {
        $sql = "SELECT t.maTaiLieu as id, t.tieuDe, t.moTa, t.fileTL, m.tenMon, NULL as hoVaTen, t.ngayTao
            FROM tailieu t
            JOIN monhoc m ON m.maMon = t.maMon
            WHERE t.maMon = ?
            ORDER BY t.ngayTao DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param('i', $maMon);
}
$stmt->execute();
$res = $stmt->get_result();
$documents = [];

while ($row = $res->fetch_assoc()) {
    $documents[] = $row;
}
$stmt->close();

echo json_encode(['success' => true, 'data' => $documents]);
exit();
?>
