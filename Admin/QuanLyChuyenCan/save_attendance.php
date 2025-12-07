<?php
session_start();
require_once '../../config.php';
require_once '../../csdl/db.php';

if (!isset($_SESSION["userID"])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit();
}

$maHS = isset($_POST['maHS']) ? $_POST['maHS'] : '';
$date = isset($_POST['date']) ? $_POST['date'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';

header('Content-Type: application/json');

// Kiểm tra dữ liệu
if (empty($maHS) || empty($date) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit();
}

// Kiểm tra ngày
$dateObj = DateTime::createFromFormat('Y-m-d', $date);
$dayOfWeek = $dateObj->format('N'); // 1=Mon, 7=Sun
$isFuture = strtotime($date) > strtotime(date('Y-m-d'));
$isSunday = ($dayOfWeek == 7);

if ($isSunday) {
    echo json_encode(['success' => false, 'message' => 'Không thể điểm danh vào ngày Chủ nhật']);
    exit();
}

if ($isFuture) {
    echo json_encode(['success' => false, 'message' => 'Không thể điểm danh ngày tương lai']);
    exit();
}

// Kiểm tra giờ - chỉ cho phép điểm danh trong giờ hành chính (7h sáng đến 5h chiều)
$currentHour = date('H');
if ($currentHour < 7 || $currentHour >= 17) {
    echo json_encode(['success' => false, 'message' => 'Chỉ có thể điểm danh từ 7h sáng đến 5h chiều']);
    exit();
}

// Map status
$statusMap = [
    'present' => 1,
    'late' => 2,
    'absent' => 0
];

$trangThai = isset($statusMap[$status]) ? $statusMap[$status] : '';

// Kiểm tra nếu bản ghi đã tồn tại
$checkQuery = "SELECT maDiemDanh FROM chuyencan WHERE maHS = '" . $conn->real_escape_string($maHS) . "' AND ngayDiemDanh = '" . $conn->real_escape_string($date) . "'";
$checkRs = $conn->query($checkQuery);

if (!$checkRs) {
    echo json_encode(['success' => false, 'message' => 'Lỗi query: ' . $conn->error]);
    exit();
}

if ($checkRs->num_rows > 0) {
    // UPDATE
    $row = $checkRs->fetch_assoc();
    $maDiemDanh = $row['maDiemDanh'];
    $updateQuery = "UPDATE chuyencan SET trangThai = $trangThai, gioCapNhat = NOW() 
                   WHERE maDiemDanh = $maDiemDanh";
    
    if ($conn->query($updateQuery)) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật điểm danh thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $conn->error]);
    }
} else {
    // INSERT
    $insertQuery = "INSERT INTO chuyencan (maHS, ngayDiemDanh, trangThai, gioCapNhat) 
                   VALUES ('" . $conn->real_escape_string($maHS) . "', '" . $conn->real_escape_string($date) . "', $trangThai, NOW())";
    
    if ($conn->query($insertQuery)) {
        echo json_encode(['success' => true, 'message' => 'Điểm danh thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $conn->error]);
    }
}
?>
