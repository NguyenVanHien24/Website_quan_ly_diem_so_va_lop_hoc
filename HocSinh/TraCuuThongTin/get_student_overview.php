<?php
// Returns subject-wise averages for the logged-in student for a given year & semester
header('Content-Type: application/json; charset=utf-8');
require_once '../../config.php';
require_once '../../CSDL/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();

$resp = ['success'=>false,'data'=>[], 'years'=>[]];
if (!isset($_SESSION['userID'])) { echo json_encode($resp); exit; }
$userId = (int)$_SESSION['userID'];

$namHoc = isset($_GET['namHoc']) ? trim($_GET['namHoc']) : '';
$hocKy = isset($_GET['hocKy']) ? (int)$_GET['hocKy'] : 0;

try {
    // List available years for this student
    $yrs = [];
    $r = $conn->query("SELECT DISTINCT namHoc FROM diemso WHERE maHS = (SELECT maHS FROM hocsinh WHERE userId = " . $userId . ") ORDER BY namHoc DESC");
    if ($r) {
        while ($row = $r->fetch_assoc()) $yrs[] = $row['namHoc'];
    }
    $resp['years'] = $yrs;

        // Build query to aggregate average per subject, separately for midterm (gk) and final (ck)
        $sql = "SELECT ds.maMonHoc AS maMon, m.tenMon,
               AVG(ds.giaTriDiem) AS avgScore,
               AVG(CASE WHEN LOWER(ds.loaiDiem) LIKE '%gk%' OR LOWER(ds.loaiDiem) LIKE '%giữa%' OR LOWER(ds.loaiDiem) LIKE '%giư%' THEN ds.giaTriDiem END) AS avg_gk,
               AVG(CASE WHEN LOWER(ds.loaiDiem) LIKE '%ck%' OR LOWER(ds.loaiDiem) LIKE '%cuối%' THEN ds.giaTriDiem END) AS avg_ck,
               COUNT(*) AS cnt
            FROM diemso ds
            LEFT JOIN monhoc m ON m.maMon = ds.maMonHoc
            WHERE ds.maHS = (SELECT maHS FROM hocsinh WHERE userId = ?)";
    $types = 'i'; $params = [$userId];
    if ($namHoc !== '') { $sql .= " AND ds.namHoc = ?"; $types .= 's'; $params[] = $namHoc; }
    if (!empty($hocKy)) { $sql .= " AND ds.hocKy = ?"; $types .= 'i'; $params[] = $hocKy; }
    $sql .= " GROUP BY ds.maMonHoc ORDER BY m.tenMon";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) {
            $resp['data'][] = [
                'maMon' => (int)$r['maMon'],
                    'tenMon' => $r['tenMon'],
                    'avgScore' => $r['avgScore'] !== null ? round($r['avgScore'],2) : null,
                    'avg_gk' => $r['avg_gk'] !== null ? round($r['avg_gk'],2) : null,
                    'avg_ck' => $r['avg_ck'] !== null ? round($r['avg_ck'],2) : null,
                    'count' => (int)$r['cnt']
            ];
        }
        $stmt->close();
    }
    $resp['success'] = true;
    echo json_encode($resp);
} catch (Exception $e) {
    echo json_encode($resp);
}

?>
