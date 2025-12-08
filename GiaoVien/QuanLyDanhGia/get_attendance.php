<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../config.php';
require_once '../../CSDL/db.php';

if (!isset($_SESSION['userID']) || $_SESSION['vaiTro'] !== 'GiaoVien') {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit();
}

$userID = $_SESSION['userID'];
$maLop = isset($_GET['maLop']) ? (int)$_GET['maLop'] : 0;
$maMon = isset($_GET['maMon']) ? (int)$_GET['maMon'] : 0;
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$stmt = $conn->prepare("SELECT g.maGV FROM giaovien g JOIN phan_cong p ON p.maGV = g.maGV WHERE g.userId = ? AND p.maLop = ? AND p.maMon = ? LIMIT 1");
$stmt->bind_param('iii', $userID, $maLop, $maMon);
$stmt->execute();
$res = $stmt->get_result();
$ok = $res && $res->num_rows > 0;
$stmt->close();
if (!$ok) {
    echo json_encode(['success'=>false,'message'=>'Bạn không được phân công cho lớp/môn này']);
    exit();
}

// Lấy danh sách học sinh của lớp
$students = [];
$sql = "SELECT h.maHS, u.hoVaTen, l.tenLop
        FROM hocsinh h
        LEFT JOIN `user` u ON u.userId = h.userId
        LEFT JOIN lophoc l ON l.maLop = h.maLopHienTai
        WHERE h.maLopHienTai = '".$conn->real_escape_string($maLop)."' ORDER BY u.hoVaTen";
$rs = $conn->query($sql);
if (!$rs) {
    echo json_encode(['success'=>false,'message'=>'Lỗi truy vấn danh sách học sinh: '.$conn->error]);
    exit();
}
while ($r = $rs->fetch_assoc()) {
    $students[] = $r;
}

// Lấy trạng thái điểm danh cho ngày này (theo date, maLop, maMon)
$attendanceMap = []; // maHS => trangThai
$sql2 = "SELECT maHS, trangThai FROM chuyencan WHERE DATE(ngayDIemDanh) = '". $conn->real_escape_string($date) ."' AND maLop = '". $conn->real_escape_string($maLop) ."' AND maMon = '". $conn->real_escape_string($maMon) ."'";
$rs2 = $conn->query($sql2);
if (!$rs2) {
    echo json_encode(['success'=>false,'message'=>'Lỗi truy vấn điểm danh: '.$conn->error]);
    exit();
}
while ($r2 = $rs2->fetch_assoc()) {
    $attendanceMap[$r2['maHS']] = $r2['trangThai'];
}

echo json_encode(['success'=>true,'students'=>$students,'attendance'=>$attendanceMap]);
exit();
