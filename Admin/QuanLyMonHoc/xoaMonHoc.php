<?php
require_once '../../csdl/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Lấy mã môn và cast về số nguyên
    $maMon = isset($_POST['maMon']) ? intval($_POST['maMon']) : 0;

    if ($maMon <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Mã môn học không hợp lệ']);
        exit;
    }

    // 2. Chuẩn bị câu lệnh DELETE
    $stmt = $conn->prepare("DELETE FROM monhoc WHERE maMon = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'msg' => 'Lỗi chuẩn bị câu lệnh: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $maMon);

    // 3. Thực thi và kiểm tra kết quả
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'msg' => 'Xóa môn học thành công']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Không tìm thấy môn học này hoặc đã bị xóa']);
        }
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Xóa môn học thất bại: ' . $stmt->error]);
    }

    $stmt->close();
}
?>
