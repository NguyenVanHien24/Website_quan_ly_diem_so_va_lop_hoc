<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../config.php';
require_once '../../CSDL/db.php';

// --- HÀM GHI LOG (Để debug lỗi) ---
function writeLog($message) {
    file_put_contents('debug_log.txt', date('H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
}

writeLog("--- BẮT ĐẦU REQUEST ---");

// 1. Nhận dữ liệu
$jsonInput = json_decode(file_get_contents('php://input'), true);
if (is_array($jsonInput)) {
    $_POST = array_merge($_POST, $jsonInput);
}

writeLog("Dữ liệu nhận được: " . print_r($_POST, true));

if (!isset($_SESSION['userID'])) {
    writeLog("Lỗi: Không tìm thấy User ID trong Session");
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$userId = (int)$_SESSION['userID'];
writeLog("User ID: " . $userId);

try {
    $tbuId = isset($_POST['tbuId']) ? (int)$_POST['tbuId'] : 0;
    
    // Kiểm tra kỹ biến 'all'
    $isAll = false;
    if (isset($_POST['all'])) {
        $val = $_POST['all'];
        if ($val === '1' || $val === 1 || $val === 'true' || $val === true) {
            $isAll = true;
        }
    }
    
    writeLog("Trạng thái isAll: " . ($isAll ? "TRUE" : "FALSE"));

    if ($tbuId > 0) {
        // ... Logic update 1 tin ...
        $stmt = $conn->prepare("UPDATE thongbaouser SET trangThai = 1 WHERE id = ? AND userId = ?");
        $stmt->bind_param('ii', $tbuId, $userId);
        $stmt->execute();
    } 
    elseif ($isAll) { 
        // === SỬA ĐOẠN NÀY ĐỂ BẮT LỖI CỤ THỂ ===
        
        $sql = "UPDATE thongbaouser SET trangThai = 1 WHERE userId = ? AND trangThai = 0";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
             writeLog("Lỗi PREPARE SQL: " . $conn->error); // Lỗi cú pháp SQL
        } else {
            $stmt->bind_param('i', $userId);
            
            if (!$stmt->execute()) {
                // NẾU CÓ LỖI, NÓ SẼ HIỆN RA Ở ĐÂY
                writeLog("Lỗi EXECUTE SQL: " . $stmt->error); 
            } else {
                $affected = $stmt->affected_rows;
                writeLog("Thực thi thành công. Số dòng update: " . $affected);
                
                // Logic Insert bổ sung (như cũ)
                if ($affected == 0) {
                     writeLog("Không có dòng update. Đang thử Insert...");
                     // ... (giữ nguyên đoạn code Insert ở dưới) ...
                     $sqlInsert = "INSERT INTO thongbaouser (maTB, userId, trangThai, ngayNhan)
                                   SELECT t.maThongBao, ?, 1, NOW()
                                   FROM thongbao t
                                   WHERE t.target_type = 'all'
                                   AND NOT EXISTS (SELECT 1 FROM thongbaouser u WHERE u.maTB = t.maThongBao AND u.userId = ?)";
                     $stmtInsert = $conn->prepare($sqlInsert);
                     if ($stmtInsert) {
                         $stmtInsert->bind_param('ii', $userId, $userId);
                         if(!$stmtInsert->execute()) {
                             writeLog("Lỗi Insert: " . $stmtInsert->error);
                         } else {
                             writeLog("Đã Insert thêm: " . $stmtInsert->affected_rows);
                         }
                     }
                }
            }
            $stmt->close();
        }
    }

    // Đếm số lượng chưa đọc trả về
    $stmtCount = $conn->prepare("SELECT COUNT(*) AS cnt FROM thongbaouser WHERE userId = ? AND trangThai = 0");
    $unread = 0;
    if ($stmtCount) {
        $stmtCount->bind_param('i', $userId);
        $stmtCount->execute();
        $rc = $stmtCount->get_result();
        if ($row = $rc->fetch_assoc()) { $unread = (int)$row['cnt']; }
        $stmtCount->close();
    }

    echo json_encode(['success' => true, 'unread' => $unread]);

} catch (Exception $e) {
    writeLog("CRITICAL ERROR: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>