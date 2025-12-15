<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
require_once '../../config.php';
require_once '../../CSDL/db.php';
require_once '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['userID'])) {
    http_response_code(403);
    echo 'Unauthorized';
    exit();
}

// Support two modes:
// - POST with 'selected[]' entries (format: maHS|maMon|maLop|namHoc|hocKy)
// - GET filters maLop, maMon, namHoc, hocKy
$maLop = isset($_GET['maLop']) ? (int)$_GET['maLop'] : 0;
$maMon = isset($_GET['maMon']) ? (int)$_GET['maMon'] : 0;
$namHoc = isset($_GET['namHoc']) ? trim($_GET['namHoc']) : '';
$hocKy = isset($_GET['hocKy']) ? (int)$_GET['hocKy'] : 0;

$selectedPost = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['selected'])) {
    $selectedPost = $_POST['selected'];
}

// Basic query: export rows of diemso joined with student name
// Build base SQL
$sql = "SELECT d.maDiem, d.maHS, hs.userId, u.hoVaTen, d.maMonHoc, d.loaiDiem, d.giaTriDiem, d.namHoc, d.hocKy, d.maLop
        FROM diemso d
        LEFT JOIN hocsinh hs ON d.maHS = hs.maHS
        LEFT JOIN `user` u ON hs.userId = u.userId
        WHERE 1=1";

// If selected rows are posted, build OR conditions for them
if (!empty($selectedPost)) {
    $conds = [];
    foreach ($selectedPost as $sel) {
        $parts = explode('|', $sel);
        $p_maHS = $conn->real_escape_string(trim($parts[0] ?? ''));
        $p_maMon = $conn->real_escape_string(trim($parts[1] ?? ''));
        $p_maLop = $conn->real_escape_string(trim($parts[2] ?? ''));
        $p_namHoc = $conn->real_escape_string(trim($parts[3] ?? ''));
        $p_hocKy = $conn->real_escape_string(trim($parts[4] ?? ''));
        if ($p_maHS === '' || $p_maMon === '') continue;
        $partCond = "(d.maHS = '$p_maHS' AND d.maMonHoc = '$p_maMon'";
        if ($p_maLop !== '') $partCond .= " AND d.maLop = '$p_maLop'";
        if ($p_namHoc !== '') $partCond .= " AND d.namHoc = '$p_namHoc'";
        if ($p_hocKy !== '') $partCond .= " AND d.hocKy = '$p_hocKy'";
        $partCond .= ")";
        $conds[] = $partCond;
    }
    if (!empty($conds)) {
        $sql .= " AND (" . implode(' OR ', $conds) . ")";
    }
} else {
    // fallback to GET filters
    if ($maLop > 0) { $sql .= " AND d.maLop = " . intval($maLop); }
    if ($maMon > 0) { $sql .= " AND d.maMonHoc = " . intval($maMon); }
    if ($namHoc !== '') { $sql .= " AND d.namHoc = '" . $conn->real_escape_string($namHoc) . "'"; }
    if ($hocKy > 0) { $sql .= " AND d.hocKy = " . intval($hocKy); }
}

$sql .= " ORDER BY d.maHS, d.maMonHoc, d.hocKy, d.loaiDiem";

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        $types = str_repeat('i', count(array_filter($params, 'is_int')));
        // mix types possible; bind dynamically
        $bind_names[] = '';
        $i = 0; foreach ($params as $p) { $bind_names[] = $p; }
        // simpler: use call_user_func_array with refs
        $refs = [];
        foreach ($params as $k => $v) { $refs[$k] = &$params[$k]; }
        // but need types string: build as all 'i' except namHoc string
    }
}
// Simpler approach: execute without prepared when params are few and sanitized
$sqlExec = $sql;
if ($maLop > 0) $sqlExec = str_replace('d.maLop = ?', 'd.maLop = ' . intval($maLop), $sqlExec);
if ($maMon > 0) $sqlExec = str_replace('d.maMonHoc = ?', 'd.maMonHoc = ' . intval($maMon), $sqlExec);
if ($namHoc !== '') $sqlExec = str_replace('d.namHoc = ?', "'" . $conn->real_escape_string($namHoc) . "'", $sqlExec);
if ($hocKy > 0) $sqlExec = str_replace('d.hocKy = ?', intval($hocKy), $sqlExec);

$res = $conn->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('DiemSo');
$headers = ['maDiem','maHS','userId','hoVaTen','maMonHoc','loaiDiem','giaTriDiem','namHoc','hocKy','maLop'];
$col = 1; foreach ($headers as $h) { $sheet->setCellValueByColumnAndRow($col++, 1, $h); }

$rowNum = 2;
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValueByColumnAndRow($col++, $rowNum, $r[$h] ?? '');
        }
        $rowNum++;
    }
}

$filename = 'diemso_export_' . date('Ymd_His') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
