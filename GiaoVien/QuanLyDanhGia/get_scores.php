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
$requestedHocKy = isset($_GET['hocKy']) ? (int)$_GET['hocKy'] : 0;
$requestedNamHoc = isset($_GET['namHoc']) ? trim($_GET['namHoc']) : '';

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

$classInfo = ['namHoc' => '', 'hocKy' => 0];
$stmt = $conn->prepare("SELECT namHoc, kyHoc FROM lophoc WHERE maLop = ?");
$stmt->bind_param('i', $maLop);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows > 0) {
    $raw = $res->fetch_assoc();
    $classInfo['namHoc'] = $raw['namHoc'] ?? '';
    $classInfo['hocKy'] = isset($raw['kyHoc']) ? (int)$raw['kyHoc'] : 0;
}
$stmt->close();

if (!empty($requestedNamHoc)) {
    $classInfo['namHoc'] = $requestedNamHoc;
}
if (!empty($requestedHocKy)) {
    $classInfo['hocKy'] = $requestedHocKy;
}
if (empty($classInfo['namHoc'])) {
    $classInfo['namHoc'] = '2025-2026';
}

function mapLoaiDiem($loai) {
    $loai = mb_strtolower(trim($loai), 'UTF-8');
    if (mb_strpos($loai, 'miệng') !== false) return 'mouth';
    if (mb_strpos($loai, '1 tiết') !== false || mb_strpos($loai, '1tiết') !== false) return '45m';
    if (mb_strpos($loai, 'giữa kỳ') !== false || mb_strpos($loai, 'gk') !== false) return 'gk';
    if (mb_strpos($loai, 'cuối kỳ') !== false || mb_strpos($loai, 'ck') !== false) return 'ck';
    return $loai;
}

// Lấy danh sách học sinh của lớp
$students = [];
$sql = "SELECT h.maHS, u.userId, u.hoVaTen, l.tenLop
        FROM hocsinh h
        LEFT JOIN `user` u ON u.userId = h.userId
        LEFT JOIN lophoc l ON l.maLop = h.maLopHienTai
        WHERE h.maLopHienTai = '" . $conn->real_escape_string($maLop) . "' ORDER BY u.hoVaTen";
$rs = $conn->query($sql);
if (!$rs) {
    echo json_encode(['success'=>false,'message'=>'Lỗi truy vấn danh sách học sinh: '.$conn->error]);
    exit();
}
while ($r = $rs->fetch_assoc()) {
    $students[] = $r;
}

// Lấy điểm của các học sinh cho môn này — thu thập theo học kỳ
$scoresData = []; 
    $studentIds = array_map(function($s) { return (int)$s['maHS']; }, $students);
    if (count($studentIds) > 0) {
        $extra = '';
        if (!empty($classInfo['namHoc'])) {
            $extra .= " AND namHoc = '" . $conn->real_escape_string($classInfo['namHoc']) . "'";
        }

        $sql2 = "SELECT maHS, hocKy, loaiDiem, giaTriDiem FROM diemso WHERE maMonHoc = '" . $conn->real_escape_string($maMon) . "' AND maHS IN (" . implode(',', $studentIds) . ")" . $extra;
        $rs2 = $conn->query($sql2);
        if ($rs2) {
            while ($r2 = $rs2->fetch_assoc()) {
                $maHS = $r2['maHS'];
                $hk = isset($r2['hocKy']) ? (int)$r2['hocKy'] : 0;
                if (!isset($scoresData[$maHS])) $scoresData[$maHS] = [];
                if (!isset($scoresData[$maHS][$hk])) $scoresData[$maHS][$hk] = ['mouth' => '', '45m' => '', 'gk' => '', 'ck' => ''];
                $key = mapLoaiDiem($r2['loaiDiem']);
                $scoresData[$maHS][$hk][$key] = $r2['giaTriDiem'];
            }
        }
    }
$data = [];
foreach ($students as $idx => $s) {
    $weights = ['mouth' => 1, '45m' => 2, 'gk' => 2, 'ck' => 3];
    $computeWeighted = function($arr) use ($weights) {
        $num = 0.0;
        $den = 0.0;
        foreach ($weights as $k => $w) {
            if (isset($arr[$k]) && $arr[$k] !== '' && is_numeric($arr[$k])) {
                $num += floatval($arr[$k]) * $w;
                $den += $w;
            }
        }
        if ($den <= 0) return '';
        return number_format($num / $den, 2);
    };

    $hk1_scores = $scoresData[$s['maHS']][1] ?? ['mouth' => '', '45m' => '', 'gk' => '', 'ck' => ''];
    $hk2_scores = $scoresData[$s['maHS']][2] ?? ['mouth' => '', '45m' => '', 'gk' => '', 'ck' => ''];

    $avgHK1 = $computeWeighted($hk1_scores);
    $avgHK2 = $computeWeighted($hk2_scores);

    $avg = '';
    $avail = [];
    if ($avgHK1 !== '') $avail[] = floatval($avgHK1);
    if ($avgHK2 !== '') $avail[] = floatval($avgHK2);
    if (count($avail) > 0) {
        $avg = number_format(array_sum($avail) / count($avail), 2);
    }
    
    $data[] = [
        'stt' => $idx + 1,
        'maHS' => $s['maHS'],
        'hoVaTen' => htmlspecialchars($s['hoVaTen'] ?? ''),
        'tenLop' => htmlspecialchars($s['tenLop'] ?? ''),
        'tenMon' => '', 
        // Trả về các điểm tương ứng với học kỳ hiện tại của lớp/hs
        'mouth' => ($classInfo['hocKy'] == 2) ? ($hk2_scores['mouth'] ?? '') : ($hk1_scores['mouth'] ?? ''),
        '45m' => ($classInfo['hocKy'] == 2) ? ($hk2_scores['45m'] ?? '') : ($hk1_scores['45m'] ?? ''),
        'gk' => ($classInfo['hocKy'] == 2) ? ($hk2_scores['gk'] ?? '') : ($hk1_scores['gk'] ?? ''),
        'ck' => ($classInfo['hocKy'] == 2) ? ($hk2_scores['ck'] ?? '') : ($hk1_scores['ck'] ?? ''),
        'avgHK1' => $avgHK1,
        'avgHK2' => $avgHK2,
        'avg' => $avg
    ];
}

echo json_encode(['success'=>true,'data'=>$data,'classInfo'=>$classInfo]);
exit();
