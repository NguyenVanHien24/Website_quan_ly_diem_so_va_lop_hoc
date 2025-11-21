<?php
/*
 * File này định nghĩa đường dẫn gốc cho website.
 * Mọi link và file include sẽ dùng hằng số 'BASE_URL' này.
 * Đặt file này ở thư mục gốc của dự án.
 */

// Tự động phát hiện giao thức (http hoặc https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";

// Tự động phát hiện tên máy chủ (ví dụ: localhost)
$host = $_SERVER['HTTP_HOST'];

// Đường dẫn thư mục gốc của dự án (bắt đầu bằng dấu /)
// Dựa trên đường dẫn của bạn, nó sẽ là:
$project_folder = '/Website quan ly diem so va lop hoc/';

// Định nghĩa hằng số BASE_URL
define('BASE_URL', $protocol . '://' . $host . $project_folder);
?>