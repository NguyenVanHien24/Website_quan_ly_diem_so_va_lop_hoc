<?php
// GiaoVien/ThongBao/get_notifications.php
session_start();

// Suppress direct PHP error output so we always emit valid JSON
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');
ob_start();

require_once '../../config.php';
require_once '../../CSDL/db.php';

if (!isset($_SESSION['userID'])) {
    $buf = ob_get_clean();
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập', 'raw' => $buf]);
    exit();
}

$userId = (int)$_SESSION['userID'];

// --- CÂU LỆNH SQL ĐÃ TỐI ƯU ---
$sql = "SELECT 
            tbu.id AS tbuId,
            tbu.maTB,
            tbu.trangThai,
            tbu.ngayNhan,
            tb.tieuDe,
            tb.noiDung,
            tb.ngayGui,
            u.hoVaTen AS tenNguoiGui
        FROM thongbaouser tbu
        JOIN thongbao tb ON tbu.maTB = tb.maThongBao
        LEFT JOIN `user` u ON tb.nguoiGui = u.userId
        WHERE tbu.userId = ?
        ORDER BY tbu.ngayNhan DESC
        LIMIT 0, 20";

try {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception('Lỗi SQL: ' . $conn->error);
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();

    $notifications = [];
    while ($r = $res->fetch_assoc()) {
        $notifications[] = [
            'tbuId'     => (int)$r['tbuId'],
            'maThongBao'=> (int)$r['maTB'],
            'tieuDe'    => $r['tieuDe'],
            'noiDung'   => $r['noiDung'],
            'ngayNhan'  => $r['ngayNhan'],
            'trangThai' => (int)$r['trangThai'],
            'ngayGui'   => $r['ngayGui'],
            'nguoiGui'  => $r['tenNguoiGui'] ? $r['tenNguoiGui'] : 'Quản trị viên'
        ];
    }
    $stmt->close();

    // Đếm số lượng chưa đọc
    $sqlCount = "SELECT COUNT(*) AS cnt FROM thongbaouser WHERE userId = ? AND trangThai = 0";
    $stmtCount = $conn->prepare($sqlCount);
    $unread = 0;
    if ($stmtCount) {
        $stmtCount->bind_param('i', $userId);
        $stmtCount->execute();
        $rc = $stmtCount->get_result();
        if ($row = $rc->fetch_assoc()) { $unread = (int)$row['cnt']; }
        $stmtCount->close();
    }

    $buf = ob_get_clean();
    $out = ['success' => true, 'notifications' => $notifications, 'unread' => $unread];
    if (!empty($buf)) $out['raw'] = $buf;
    echo json_encode($out);

} catch (Exception $e) {
    $buf = ob_get_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'raw' => $buf]);
}

?>