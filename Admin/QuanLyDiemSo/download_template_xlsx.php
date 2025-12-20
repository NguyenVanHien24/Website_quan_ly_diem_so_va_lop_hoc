<?php
session_start();
require_once '../../config.php';
require_once '../../CSDL/db.php';
require_once '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['userID'])) {
    http_response_code(403);
    echo 'Unauthorized';
    exit();
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Template-DiemSo');
$headers = ['maHS','maMon','loaiDiem','giaTriDiem','namHoc','hocKy'];
$col = 1;
foreach ($headers as $h) {
    $sheet->setCellValueByColumnAndRow($col++, 1, $h);
}

$sample = [
    ['1','1','Điểm miệng',8.5,'2025-2026',1],
    ['2','1','Điểm 1 tiết',7.0,'2025-2026',1],
    ['1','1','Điểm giữa kỳ',8.0,'2025-2026',1],
    ['1','1','Điểm cuối kỳ',8.5,'2025-2026',1],
];
$rowNum = 2;
foreach ($sample as $r) {
    $col = 1;
    foreach ($r as $c) {
        $sheet->setCellValueByColumnAndRow($col++, $rowNum, $c);
    }
    $rowNum++;
}

$filename = 'diemso_template.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
