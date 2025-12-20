<?php
require_once '../../CSDL/db.php';
header('Content-Type: application/json; charset=utf-8');

$resp = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $resp['message'] = 'Yêu cầu không hợp lệ';
    echo json_encode($resp); exit;
}

$userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;
$role = isset($_POST['role']) ? trim($_POST['role']) : '';

$allowed = ['Admin', 'GiaoVien', 'HocSinh'];
if ($userId <= 0 || !in_array($role, $allowed)) {
    $resp['message'] = 'Dữ liệu không hợp lệ';
    echo json_encode($resp); exit;
}

$conn->begin_transaction();
try {
    if ($role === 'HocSinh') {
        $del = $conn->prepare("DELETE FROM hocsinh WHERE userId = ?");
        if ($del) { $del->bind_param('i', $userId); $del->execute(); $del->close(); }
    }

    if ($role === 'GiaoVien') {
        $del2 = $conn->prepare("DELETE FROM giaovien WHERE userId = ?");
        if ($del2) { $del2->bind_param('i', $userId); $del2->execute(); $del2->close(); }
    }

    $stmt = $conn->prepare("UPDATE `user` SET vaiTro = ? WHERE userId = ?");
    if (!$stmt) {
        throw new Exception('Lỗi chuẩn bị truy vấn: ' . $conn->error);
    }
    $stmt->bind_param('si', $role, $userId);
    if (!$stmt->execute()) {
        throw new Exception('Lỗi khi cập nhật cơ sở dữ liệu: ' . $stmt->error);
    }
    $stmt->close();

    $conn->commit();
    $resp['success'] = true;
    $resp['message'] = 'Cập nhật phân quyền thành công';
} catch (Exception $ex) {
    $conn->rollback();
    $resp['message'] = $ex->getMessage();
}

echo json_encode($resp);

?>
