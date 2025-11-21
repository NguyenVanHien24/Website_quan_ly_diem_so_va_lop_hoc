</div> <!-- ĐÓNG THẺ <div class="main-content"> TỪ SidebarAndHeader.php -->

<!-- Tải các tệp JS ở cuối trang -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php
if (isset($pageJS) && is_array($pageJS)) {
    foreach ($pageJS as $jsFile) {
        // Thêm time() để tự động xóa cache khi sửa code
        echo '<script src="' . htmlspecialchars($jsFile) . '?v=' . time() . '"></script>';
    }
}
?>

</body>

</html>