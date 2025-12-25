<?php
session_start();
require_once '../../config.php'; // Chắc chắn đã require các file cần thiết
require_once '../../csdl/db.php';

// 1. Kiểm tra session và phương thức POST
if (!isset($_SESSION["userID"]) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../../dangnhap.php");
    exit();
}

// 2. Lấy năm học và học kỳ hiện tại (Copy từ file gốc)
$yearNow = date('Y');
$monthNow = date('n');
if ($monthNow >= 1 && $monthNow <= 6) {
    $currentSemester = 2;
    $currentYear = ($yearNow - 1) . '-' . $yearNow;
} else {
    $currentSemester = 1;
    $currentYear = $yearNow . '-' . ($yearNow + 1);
}

// 3. Lấy dữ liệu cần thiết từ POST
$maHS = filter_input(INPUT_POST, 'maHS', FILTER_VALIDATE_INT);
$maMon = filter_input(INPUT_POST, 'maMon', FILTER_VALIDATE_INT);
$maLop = filter_input(INPUT_POST, 'maLop', FILTER_VALIDATE_INT);

if (!$maHS || !$maMon || !$maLop) {
    // Xử lý lỗi nếu thiếu mã học sinh hoặc mã môn, mã lớp
    die("Lỗi: Thông tin học sinh, môn học hoặc mã lớp bị thiếu.");
}

// 4. Định nghĩa Ánh xạ loại điểm (Đảm bảo lưu đúng tên Việt Nam)
$score_type_map = [
    1 => [
        'mouth' => ['loaiDiem' => 'Điểm miệng', 'hocKy' => 1],
        '45m'   => ['loaiDiem' => 'Điểm 1 tiết', 'hocKy' => 1],
        'gk'    => ['loaiDiem' => 'Điểm giữa kỳ', 'hocKy' => 1],
        'ck'    => ['loaiDiem' => 'Điểm cuối kỳ', 'hocKy' => 1],
    ],
    2 => [
        'mouth' => ['loaiDiem' => 'Điểm miệng', 'hocKy' => 2],
        '45m'   => ['loaiDiem' => 'Điểm 1 tiết', 'hocKy' => 2],
        'gk'    => ['loaiDiem' => 'Điểm giữa kỳ', 'hocKy' => 2],
        'ck'    => ['loaiDiem' => 'Điểm cuối kỳ', 'hocKy' => 2],
    ],
];

// 5. Chuẩn bị truy vấn UPSERT (INSERT OR UPDATE)
$query = "INSERT INTO diemso (maHS, maMonHoc, namHoc, hocKy, loaiDiem, giaTriDiem, maLop) 
          VALUES (?, ?, ?, ?, ?, ?, ?)
          ON DUPLICATE KEY UPDATE
          giaTriDiem = VALUES(giaTriDiem),
          maLop = VALUES(maLop)";

// 6. Thực hiện lặp qua các điểm đã gửi và lưu vào CSDL
$success = true;
$types_to_process = ['mouth', '45m', 'gk', 'ck']; 
$updatedEntries = [];

foreach ([1, 2] as $hk) { // Lặp qua Học kỳ 1 và Học kỳ 2
    foreach ($types_to_process as $type_key) {
        $input_name = "s{$hk}_{$type_key}";
        $score = filter_input(INPUT_POST, $input_name, FILTER_VALIDATE_FLOAT);
        
        // Chỉ lưu điểm nếu nó không rỗng và là số hợp lệ
        if ($score !== false && $score !== null) {
            $loaiDiem = $score_type_map[$hk][$type_key]['loaiDiem'];

            // Chuẩn bị và thực thi Prepared Statement
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iisisdi", $maHS, $maMon, $currentYear, $hk, $loaiDiem, $score, $maLop);
            
            if (!$stmt->execute()) {
                $success = false;
                // Ghi log lỗi hoặc thông báo
                error_log("Lỗi khi cập nhật điểm: " . $stmt->error);
            }
            $stmt->close();
            // Nếu thành công, ghi lại mục đã cập nhật
            if ($success) {
                $updatedEntries[] = [
                    'hocKy' => $hk,
                    'loaiDiem' => $loaiDiem,
                    'giaTri' => $score
                ];
            }
        }
    }
}

// 7. Kết thúc và chuyển hướng
if ($success) {
    $_SESSION['message'] = "Cập nhật điểm thành công!";

    // Nếu có mục điểm đã cập nhật thì tạo thông báo cho học sinh
    if (!empty($updatedEntries)) {
        // Lấy userId của học sinh
        $userId = null;
        $stmtU = $conn->prepare("SELECT userId FROM hocsinh WHERE maHS = ? LIMIT 1");
        if ($stmtU) {
            $stmtU->bind_param('i', $maHS);
            if ($stmtU->execute()) {
                $res = $stmtU->get_result();
                if ($row = $res->fetch_assoc()) {
                    $userId = (int)$row['userId'];
                }
            }
            $stmtU->close();
        }

        // Lấy tên môn
        $tenMon = '';
        $stmtM = $conn->prepare("SELECT tenMon FROM monhoc WHERE maMon = ? LIMIT 1");
        if ($stmtM) {
            $stmtM->bind_param('i', $maMon);
            if ($stmtM->execute()) {
                $resM = $stmtM->get_result();
                if ($r = $resM->fetch_assoc()) $tenMon = $r['tenMon'];
            }
            $stmtM->close();
        }

        if ($userId !== null) {
            $title = 'Cập nhật điểm: ' . ($tenMon !== '' ? $tenMon : 'Môn học');
            $parts = [];
            foreach ($updatedEntries as $e) {
                $parts[] = 'Học kỳ ' . $e['hocKy'] . ' - ' . $e['loaiDiem'] . ': ' . $e['giaTri'];
            }
            $content = 'Điểm của bạn đã được cập nhật. ' . implode('; ', $parts);

            $conn->begin_transaction();
            $stmtIns = $conn->prepare("INSERT INTO thongbao (tieuDe, noiDung, nguoiGui) VALUES (?, ?, ?)");
            if ($stmtIns) {
                $nguoiGui = isset($_SESSION['userID']) ? (int)$_SESSION['userID'] : null;
                $stmtIns->bind_param('ssi', $title, $content, $nguoiGui);
                if ($stmtIns->execute()) {
                    $maTB = $conn->insert_id;
                    $stmtRel = $conn->prepare("INSERT INTO thongbaouser (maTB, userId, trangThai) VALUES (?, ?, 0)");
                    if ($stmtRel) {
                        $stmtRel->bind_param('ii', $maTB, $userId);
                        $stmtRel->execute();
                        $stmtRel->close();
                    }
                }
                $stmtIns->close();
            }
            $conn->commit();
        }
    }

} else {
    $_SESSION['error'] = "Đã xảy ra lỗi khi cập nhật điểm.";
}

// Chuyển hướng về trang bảng điểm để thấy kết quả
header("Location: " . $_SERVER['HTTP_REFERER']); 
exit();
?>