<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../config.php';
require_once '../../CSDL/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();

$resp = ['success' => false, 'scores' => []];
if (!isset($_SESSION['userID'])) {
    echo json_encode($resp);
    exit;
}
$userId = (int)$_SESSION['userID'];

$maMon = isset($_POST['maMon']) ? (int)$_POST['maMon'] : 0;
$namHoc = isset($_POST['namHoc']) ? trim($_POST['namHoc']) : '';
$hocKy = isset($_POST['hocKy']) ? (int)$_POST['hocKy'] : 0;

if ($maMon <= 0) {
    echo json_encode($resp);
    exit;
}

$rs = $conn->query("SELECT maHS FROM hocsinh WHERE userId = " . $userId . " LIMIT 1");
if (!$rs || $rs->num_rows === 0) {
    echo json_encode($resp);
    exit;
}
$row = $rs->fetch_assoc();
$maHS = (int)$row['maHS'];

$sql = "SELECT loaiDiem, giaTriDiem, ngayGhiNhan FROM diemso WHERE maHS = ? AND maMonHoc = ?";
$types = 'ii';
$params = [$maHS, $maMon];
if ($namHoc !== '') {
    $sql .= " AND namHoc = ?";
    $types .= 's';
    $params[] = $namHoc;
}
if (!empty($hocKy)) {
    $sql .= " AND hocKy = ?";
    $types .= 'i';
    $params[] = $hocKy;
}
$sql .= " ORDER BY ngayGhiNhan ASC";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $resp['scores'][] = [
            'loaiDiem' => $r['loaiDiem'],
            'giaTriDiem' => $r['giaTriDiem'],
            'ngayGhiNhan' => $r['ngayGhiNhan']
        ];
    }
    $stmt->close();
}

$resp['success'] = true;
echo json_encode($resp);
exit;
