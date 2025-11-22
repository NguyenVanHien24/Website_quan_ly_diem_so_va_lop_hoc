<?php
// Nếu đã có kết nối thì không tạo lại
if (!isset($conn)) {

    // THÔNG TIN KẾT NỐI DATABASE — chỉnh theo config của bạn
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "cdtn";

    // Tạo kết nối
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Nếu lỗi → dừng chương trình
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // UTF8 để hiển thị tiếng Việt
    $conn->set_charset("utf8mb4");
}
?>
