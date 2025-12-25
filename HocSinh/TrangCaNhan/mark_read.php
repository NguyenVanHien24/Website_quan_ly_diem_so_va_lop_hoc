<?php
ob_start();
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

require_once '../../config.php';
require_once '../../CSDL/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();

$out = ['success' => false, 'unread' => 0];
try {
    if (!isset($_SESSION['userID'])) {
        echo json_encode($out);
        exit;
    }
    $userId = (int)$_SESSION['userID'];

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if (!is_array($data)) $data = $_POST;

    if (!empty($data['all'])) {
        $stmt = $conn->prepare("UPDATE thongbaouser SET trangThai = 1 WHERE userId = ? AND trangThai = 0");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->close();
    } elseif (!empty($data['tbuId'])) {
        $tbuId = (int)$data['tbuId'];
        $stmt = $conn->prepare("UPDATE thongbaouser SET trangThai = 1 WHERE id = ? AND userId = ?");
        $stmt->bind_param('ii', $tbuId, $userId);
        $stmt->execute();
        $stmt->close();
    }

    $cstmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM thongbaouser WHERE userId = ? AND trangThai = 0");
    $cstmt->bind_param('i', $userId);
    $cstmt->execute();
    $cres = $cstmt->get_result();
    $crow = $cres->fetch_assoc();
    $unread = (int)($crow['cnt'] ?? 0);
    $cstmt->close();

    $out['success'] = true;
    $out['unread'] = $unread;
    ob_get_clean();
    echo json_encode($out);
} catch (Exception $e) {
    ob_get_clean();
    echo json_encode($out);
}

?>
