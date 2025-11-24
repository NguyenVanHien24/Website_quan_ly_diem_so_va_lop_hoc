<?php
require_once '../../config.php';
require_once '../../csdl/db.php';

header('Content-Type: application/json');

// Nhận ID (1 hoặc nhiều)
$ids = $_POST['ids'] ?? null;

if (!$ids) {
    echo json_encode(["status" => "error", "msg" => "Không có lớp cần xóa"]);
    exit;
}

if (is_array($ids)) {
    // Xóa nhiều lớp
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $sql = "DELETE FROM lophoc WHERE maLop IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$ids);
} else {
    // Xóa 1 lớp
    $sql = "DELETE FROM lophoc WHERE maLop = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ids);
}

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "msg" => $stmt->error]);
}
?>