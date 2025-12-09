<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();

require_once '../../config.php';
require_once '../../CSDL/db.php';

if (!isset($_SESSION['userID']) || $_SESSION['vaiTro'] !== 'HocSinh') {
    header('HTTP/1.0 403 Forbidden');
    exit('Unauthorized');
}

$userID = $_SESSION['userID'];
$maTaiLieu = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($maTaiLieu <= 0) {
    header('HTTP/1.0 400 Bad Request');
    exit('Invalid document');
}

// Get student's class
$stmt = $conn->prepare("SELECT maLopHienTai as maLop FROM hocsinh WHERE userId = ?");
if (!$stmt) {
    header('HTTP/1.0 500 Server Error');
    exit('Database error');
}
$stmt->bind_param('i', $userID);
$stmt->execute();
$res = $stmt->get_result();
$student = $res->fetch_assoc();
$stmt->close();

if (!$student) {
    header('HTTP/1.0 404 Not Found');
    exit('Student not found');
}

$maLop = $student['maLop'];

// If tailieu has maLop, enforce class match; otherwise fetch by id only
$colRes = $conn->query("SHOW COLUMNS FROM tailieu LIKE 'maLop'");
$hasMaLop = ($colRes && $colRes->num_rows > 0);

if ($hasMaLop) {
    $sql = "SELECT fileTL, tieuDe FROM tailieu WHERE maTaiLieu = ? AND maLop = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header('HTTP/1.0 500 Server Error');
        exit('Database error');
    }
    $stmt->bind_param('ii', $maTaiLieu, $maLop);
} else {
    $sql = "SELECT fileTL, tieuDe FROM tailieu WHERE maTaiLieu = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header('HTTP/1.0 500 Server Error');
        exit('Database error');
    }
    $stmt->bind_param('i', $maTaiLieu);
}
$stmt->execute();
$res = $stmt->get_result();
$document = $res->fetch_assoc();
$stmt->close();

if (!$document || !$document['fileTL']) {
    header('HTTP/1.0 404 Not Found');
    exit('Document not found');
}

// Build absolute uploads path using __DIR__ to avoid relative-path issues
$uploadsDir = realpath(__DIR__ . '/../../uploads/documents');
$fileName = basename($document['fileTL']);

// Log helper (temporary) — write minimal debug info to system temp for troubleshooting
$logFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'download_document.log';
file_put_contents($logFile, "[" . date('c') . "] request id={$maTaiLieu} file={$fileName} uploadsDir=" . ($uploadsDir ?: 'NULL') . "\n", FILE_APPEND);

if (!$uploadsDir) {
    header('HTTP/1.0 500 Server Error');
    file_put_contents($logFile, "uploadsDir not found\n", FILE_APPEND);
    exit('Server configuration error (uploads directory missing)');
}

$filePath = $uploadsDir . DIRECTORY_SEPARATOR . $fileName;
$realPath = realpath($filePath);

// Security check: ensure resolved file path is inside uploads directory
if (!$realPath || strpos($realPath, $uploadsDir) !== 0) {
    header('HTTP/1.0 403 Forbidden');
    file_put_contents($logFile, "forbidden: realPath={$realPath}\n", FILE_APPEND);
    exit('Invalid file path');
}

if (!is_readable($realPath) || !file_exists($realPath)) {
    header('HTTP/1.0 404 Not Found');
    file_put_contents($logFile, "not found or not readable: {$realPath}\n", FILE_APPEND);
    exit('File not found');
}

// Serve file with correct headers
$mime = 'application/octet-stream';
if (function_exists('mime_content_type')) {
    $detected = mime_content_type($realPath);
    if ($detected) $mime = $detected;
}

header('Content-Description: File Transfer');
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Length: ' . filesize($realPath));
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

// Clear output buffers then readfile
while (ob_get_level()) ob_end_clean();
readfile($realPath);
exit();
exit();
?>
