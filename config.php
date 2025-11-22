<?php
/*
 * File config.php chuẩn cho cả 2 máy
 */

// 1. Tự động phát hiện giao thức
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";

// 2. Tự động phát hiện host
$host = $_SERVER['HTTP_HOST'];

// 3. XỬ LÝ THÔNG MINH: Tự động chọn thư mục dựa trên cổng (port)
// Nếu thấy có cổng :3000 (máy bạn của bạn)
if (strpos($host, ':3000') !== false) {
    $project_folder = '/'; 
} 
// Nếu không (máy của bạn dùng XAMPP mặc định)
else {
    $project_folder = '/Website quan ly diem so va lop hoc/';
}

// 4. Định nghĩa hằng số BASE_URL
define('BASE_URL', $protocol . '://' . $host . $project_folder);
?>