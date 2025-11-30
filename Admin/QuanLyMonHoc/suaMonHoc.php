<?php
require_once '../../csdl/db.php';

// Xử lý AJAX để lấy giáo viên theo môn
if (isset($_GET['action']) && $_GET['action'] === 'getTeachersBySubject') {
    header('Content-Type: application/json; charset=UTF-8');

    $maMon = isset($_POST['maMon']) ? intval($_POST['maMon']) : 0;

    if ($maMon > 0) {
        // Lấy tên môn từ bảng monhoc
        $stmt = $conn->prepare("SELECT tenMon FROM monhoc WHERE maMon = ?");
        $stmt->bind_param("i", $maMon);
        $stmt->execute();
        $stmt->bind_result($tenMon);
        $stmt->fetch();
        $stmt->close();

        if ($tenMon) {
            // Lấy giáo viên dạy môn đó
            $stmt2 = $conn->prepare("
                SELECT gv.maGV, u.hoVaTen
                FROM giaovien gv
                JOIN user u ON gv.userId = u.userId
                WHERE gv.boMon = ?
                ORDER BY u.hoVaTen ASC
            ");
            $stmt2->bind_param("s", $tenMon);
            $stmt2->execute();
            $result = $stmt2->get_result();

            $teachers = [];
            while ($row = $result->fetch_assoc()) {
                $teachers[] = $row;
            }

            echo json_encode($teachers);
            exit;
        }
    }

    echo json_encode([]);
    exit;
}

// Xử lý POST cập nhật môn học
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maMon = $_POST['maMon'] ?? null;
    $tenMon = $_POST['tenMon'] ?? '';
    $truongBoMon = $_POST['truongBoMon'] ?? '';
    $moTa = $_POST['moTa'] ?? '';
    $namHoc = $_POST['namHoc'] ?? '';
    $hocKy = $_POST['hocKy'] ?? '';
    $trangThai = $_POST['trangThai'] ?? 'inactive';

    if (!$maMon || !$tenMon) {
        echo json_encode(['status' => 'error', 'msg' => 'Thiếu dữ liệu bắt buộc']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE monhoc SET tenMon=?, truongBoMon=?, moTa=?, namHoc=?, hocKy=?, trangThai=? WHERE maMon=?");
    $stmt->bind_param("ssssssi", $tenMon, $truongBoMon, $moTa, $namHoc, $hocKy, $trangThai, $maMon);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'msg' => 'Cập nhật môn học thành công']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Lỗi khi cập nhật môn học']);
    }
}
?>
