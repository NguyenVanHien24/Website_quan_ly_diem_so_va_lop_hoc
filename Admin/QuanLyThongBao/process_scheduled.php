<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: application/json; charset=utf-8');

require_once '../../config.php';
require_once '../../CSDL/db.php';

$processed = [];

$sql = "SELECT tb.maThongBao, tb.target_type, tb.target_value FROM thongbao tb
        WHERE tb.send_at IS NOT NULL AND tb.send_at <= NOW()
        AND NOT EXISTS (SELECT 1 FROM thongbaouser tbu WHERE tbu.maTB = tb.maThongBao)";
$rs = $conn->query($sql);
if ($rs) {
    while ($row = $rs->fetch_assoc()) {
        $ma = (int)$row['maThongBao'];
        $target_type = $row['target_type'] ?? 'all';
        $target_value = $row['target_value'] ?? '';

        $userIds = [];
        if ($target_type === 'all') {
            $r2 = $conn->query("SELECT userId FROM `user`");
            if ($r2) while ($u = $r2->fetch_assoc()) $userIds[] = (int)$u['userId'];
        } elseif ($target_type === 'role') {
            $role = $conn->real_escape_string($target_value);
            $r2 = $conn->query("SELECT userId FROM `user` WHERE vaiTro = '".$role."'");
            if ($r2) while ($u = $r2->fetch_assoc()) $userIds[] = (int)$u['userId'];
        } elseif ($target_type === 'class') {
            $maLop = (int)$target_value;
            $r2 = $conn->query("SELECT u.userId FROM `user` u JOIN hocsinh hs ON u.userId = hs.userId WHERE hs.maLopHienTai = '".$maLop."'");
            if ($r2) while ($u = $r2->fetch_assoc()) $userIds[] = (int)$u['userId'];
        } elseif ($target_type === 'users') {
            $arr = json_decode($target_value, true);
            if (is_array($arr)) foreach ($arr as $v) $userIds[] = (int)$v;
        }

        $inserted = 0; $errors = [];
        if (!empty($userIds)) {
            $conn->begin_transaction();
            $stmt = $conn->prepare("INSERT INTO thongbaouser (maTB, userId, trangThai) VALUES (?, ?, 0)");
            if ($stmt) {
                foreach ($userIds as $uid) {
                    $uid = (int)$uid;
                    if ($stmt->bind_param('ii', $ma, $uid) && $stmt->execute()) {
                        $inserted++;
                    } else {
                        $errors[] = ['userId'=>$uid, 'error'=>$stmt->error ?: $conn->error];
                    }
                }
                $stmt->close();
            } else {
                $errors[] = ['prepare_error' => $conn->error];
            }
            // If we successfully inserted recipients, mark the notification as sent
            if ($inserted > 0) {
                $conn->query("UPDATE thongbao SET send_at = NULL, ngayGui = NOW() WHERE maThongBao = " . $ma);
            }
            $conn->commit();
        }

        $conn->query("UPDATE thongbao SET ngayGui = NOW() WHERE maThongBao = " . $ma . " AND (ngayGui IS NULL OR ngayGui = '')");

        $processed[] = ['maThongBao' => $ma, 'inserted' => $inserted, 'errors' => $errors];
    }
}

echo json_encode(['success' => true, 'processed' => $processed]);
exit();

?>