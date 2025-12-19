<?php
require_once __DIR__ . '/../../CSDL/db.php';
$tb = isset($argv[1]) ? intval($argv[1]) : 11;
$rs = $conn->query("SELECT COUNT(*) AS c FROM thongbaouser WHERE maTB = $tb");
$r = $rs->fetch_assoc();
$rs2 = $conn->query("SELECT send_at, ngayGui FROM thongbao WHERE maThongBao = $tb");
$r2 = $rs2->fetch_assoc();
echo "maThongBao=$tb\n";
echo "thongbaouser_count=" . ($r['c'] ?? 0) . "\n";
echo "send_at=" . ($r2['send_at'] ?? 'NULL') . "\n";
echo "ngayGui=" . ($r2['ngayGui'] ?? 'NULL') . "\n";
