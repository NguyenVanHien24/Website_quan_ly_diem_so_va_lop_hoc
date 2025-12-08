<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../config.php';
require_once '../../CSDL/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Phương thức không hợp lệ']);
    exit();
}
if (!isset($_SESSION['userID']) || $_SESSION['vaiTro'] !== 'GiaoVien') {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit();
}

$userID = $_SESSION['userID'];
$maHS = isset($_POST['maHS']) ? (int)$_POST['maHS'] : 0;
$maLop = isset($_POST['maLop']) ? (int)$_POST['maLop'] : 0;
$maMon = isset($_POST['maMon']) ? (int)$_POST['maMon'] : 0;
date_default_timezone_set('Asia/Ho_Chi_Minh');
$date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
$trangThai = isset($_POST['status']) ? $_POST['status'] : null; // '1' present, '2' late, '0' absent

if ($maHS <= 0 || $maLop <= 0 || $maMon <= 0 || !$trangThai) {
    echo json_encode(['success'=>false,'message'=>'Dữ liệu không hợp lệ']);
    exit();
}

if (strtotime($date) > strtotime(date('Y-m-d'))) {
    echo json_encode(['success'=>false,'message'=>'Không thể điểm danh ngày tương lai']);
    exit();
}

$weekday = date('w', strtotime($date));
if ($weekday == 0) {
    echo json_encode(['success'=>false,'message'=>'Không thể điểm danh vào Chủ nhật']);
    exit();
}

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

$dateStart = $date . ' 00:00:00';
$dateEnd = $date . ' 23:59:59';
$check = "SELECT maDiemDanh FROM chuyencan WHERE maHS = '". $conn->real_escape_string($maHS) ."' AND maLop = '". $conn->real_escape_string($maLop) ."' AND maMon = '". $conn->real_escape_string($maMon) ."' AND DATE(ngayDIemDanh) = '". $conn->real_escape_string($date) ."' LIMIT 1";
$rs = $conn->query($check);
if ($rs && $rs->num_rows > 0) {
    // update
    $row = $rs->fetch_assoc();
    $id = $row['maDiemDanh'];
    $upd = "UPDATE chuyencan SET trangThai = '". $conn->real_escape_string($trangThai) ."', userId = '". $conn->real_escape_string($userID) ."', gioCapNhat = NOW() WHERE maDiemDanh = '". $conn->real_escape_string($id) ."'";
    if ($conn->query($upd)) {
        echo json_encode(['success'=>true,'message'=>'Cập nhật điểm danh thành công']);
        exit();
    } else {
        echo json_encode(['success'=>false,'message'=>'Lỗi cập nhật: '.$conn->error]);
        exit();
    }
} else {
    // insert
    $ins = "INSERT INTO chuyencan (maHS, maLop, maMon, userId, trangThai, ngayDIemDanh) VALUES ('".
            $conn->real_escape_string($maHS)."','".
            $conn->real_escape_string($maLop)."','".
            $conn->real_escape_string($maMon)."','".
            $conn->real_escape_string($userID)."','".
            $conn->real_escape_string($trangThai)."','".
            $conn->real_escape_string($date)." 00:00:00')";
    if ($conn->query($ins)) {
        echo json_encode(['success'=>true,'message'=>'Điểm danh thành công']);
        exit();
    } else {
        echo json_encode(['success'=>false,'message'=>'Lỗi ghi: '.$conn->error]);
        exit();
    }
}

