<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../config.php';
require_once '../../CSDL/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

if (!isset($_SESSION['userID']) || $_SESSION['vaiTro'] !== 'GiaoVien') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$userID = $_SESSION['userID'];
$maTaiLieu = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$tieuDe = isset($_POST['title']) ? trim($_POST['title']) : '';
$moTa = isset($_POST['desc']) ? trim($_POST['desc']) : '';
$maMon = isset($_POST['maMon']) ? (int)$_POST['maMon'] : 0;
$maLop = isset($_POST['maLop']) ? (int)$_POST['maLop'] : 0;
$fileTL = isset($_POST['fileName']) ? trim($_POST['fileName']) : '';

$uploadedFileName = null;
$uploadsDir = realpath(__DIR__ . '/../../uploads/documents');
if (!$uploadsDir) {
    @mkdir(__DIR__ . '/../../uploads/documents', 0755, true);
    $uploadsDir = realpath(__DIR__ . '/../../uploads/documents');
}
if (isset($_FILES['file']) && isset($_FILES['file']['error']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['file']['tmp_name'];
    $origName = $_FILES['file']['name'];
    $ext = pathinfo($origName, PATHINFO_EXTENSION);
    $base = pathinfo($origName, PATHINFO_FILENAME);
    $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
    $newName = $safeBase . '_' . time() . ($ext ? '.' . $ext : '');
    if ($uploadsDir) {
        $target = $uploadsDir . DIRECTORY_SEPARATOR . $newName;
        if (move_uploaded_file($tmp, $target)) {
            $uploadedFileName = $newName;
        }
    }
}

if (empty($tieuDe) || $maMon <= 0 || $maLop <= 0) {
    echo json_encode(['success' => false, 'message' => 'Tiêu đề, lớp và môn học không được để trống']);
    exit();
}

$stmt = $conn->prepare("SELECT g.maGV FROM giaovien g JOIN phan_cong p ON p.maGV = g.maGV 
                        WHERE g.userId = ? AND p.maMon = ? AND p.maLop = ? LIMIT 1");
$stmt->bind_param('iii', $userID, $maMon, $maLop);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;
$ok = $row && isset($row['maGV']);
$maGV = $ok ? (int)$row['maGV'] : 0;
$stmt->close();

$colRes = $conn->query("SHOW COLUMNS FROM tailieu LIKE 'maGV'");
$hasMaGV = ($colRes && $colRes->num_rows > 0);

if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thêm tài liệu cho lớp và môn này']);
    exit();
}

if ($maTaiLieu > 0) {
    if (!$uploadedFileName && empty($fileTL)) {
        $sel = $conn->prepare("SELECT fileTL FROM tailieu WHERE maTaiLieu = ? LIMIT 1");
        if ($sel) {
            $sel->bind_param('i', $maTaiLieu);
            $sel->execute();
            $r = $sel->get_result();
            $rr = $r ? $r->fetch_assoc() : null;
            if ($rr && !empty($rr['fileTL'])) $fileTL = $rr['fileTL'];
            $sel->close();
        }
    }
}
if ($uploadedFileName) {
    $fileTL = $uploadedFileName;
}

if ($maTaiLieu > 0) {
    // Update
    if ($hasMaGV) {
        $sql = "UPDATE tailieu SET tieuDe = ?, moTa = ?, fileTL = ?, maGV = ? WHERE maTaiLieu = ? AND maMon = ? AND maLop = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Prepare error: ' . $conn->error]);
            exit();
        }
        $stmt->bind_param('sssiiii', $tieuDe, $moTa, $fileTL, $maGV, $maTaiLieu, $maMon, $maLop);
    } else {
        $sql = "UPDATE tailieu SET tieuDe = ?, moTa = ?, fileTL = ? WHERE maTaiLieu = ? AND maMon = ? AND maLop = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Prepare error: ' . $conn->error]);
            exit();
        }
        $stmt->bind_param('sssiii', $tieuDe, $moTa, $fileTL, $maTaiLieu, $maMon, $maLop);
    }
} else {
    // Insert
    if ($hasMaGV) {
        $sql = "INSERT INTO tailieu (maLop, maMon, maGV, tieuDe, moTa, fileTL) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Prepare error: ' . $conn->error]);
            exit();
        }
        $stmt->bind_param('iiisss', $maLop, $maMon, $maGV, $tieuDe, $moTa, $fileTL);
    } else {
        $sql = "INSERT INTO tailieu (maLop, maMon, tieuDe, moTa, fileTL) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Prepare error: ' . $conn->error]);
            exit();
        }
        $stmt->bind_param('iisss', $maLop, $maMon, $tieuDe, $moTa, $fileTL);
    }
}

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Execute error: ' . $stmt->error]);
    $stmt->close();
    exit();
}

$isInsert = ($maTaiLieu <= 0);
$newId = $isInsert ? $conn->insert_id : $maTaiLieu;

$stmt->close();

// Nếu là thêm mới tài liệu cho lớp thì tạo thông báo gửi tới tất cả học sinh của lớp
if ($isInsert && $newId > 0) {
    $title = 'Tài liệu mới: ' . $tieuDe;
    $content = 'Có tài liệu mới được thêm cho lớp. Tiêu đề: ' . $tieuDe . '. Mô tả: ' . $moTa . '.';

    // Người gửi (giáo viên)
    $nguoiGui = isset($_SESSION['userID']) ? (int)$_SESSION['userID'] : null;

    // Lấy danh sách userId của học sinh trong lớp (maLop)
    $userIds = [];
    $r = $conn->prepare("SELECT u.userId FROM `user` u JOIN hocsinh hs ON u.userId = hs.userId WHERE hs.maLopHienTai = ?");
    if ($r) {
        $r->bind_param('i', $maLop);
        if ($r->execute()) {
            $res = $r->get_result();
            while ($rr = $res->fetch_assoc()) {
                $userIds[] = (int)$rr['userId'];
            }
        }
        $r->close();
    }

    if (!empty($userIds)) {
        $conn->begin_transaction();
        $sIns = $conn->prepare("INSERT INTO thongbao (tieuDe, noiDung, nguoiGui) VALUES (?, ?, ?)");
        if ($sIns) {
            $sIns->bind_param('ssi', $title, $content, $nguoiGui);
            if ($sIns->execute()) {
                $maTB = $conn->insert_id;
                $sRel = $conn->prepare("INSERT INTO thongbaouser (maTB, userId, trangThai) VALUES (?, ?, 0)");
                if ($sRel) {
                    foreach ($userIds as $uid) {
                        $sRel->bind_param('ii', $maTB, $uid);
                        $sRel->execute();
                    }
                    $sRel->close();
                }
            }
            $sIns->close();
        }
        $conn->commit();
    }
}

echo json_encode(['success' => true, 'message' => $isInsert ? 'Thêm tài liệu thành công' : 'Cập nhật thành công']);
exit();
?>
