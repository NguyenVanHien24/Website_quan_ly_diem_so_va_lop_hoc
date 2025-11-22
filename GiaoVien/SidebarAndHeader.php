<?php
require_once '../../config.php';
// Khởi tạo session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ==== Kiểm tra đăng nhập ====
if (!isset($_SESSION['userID'])) {
    header('Location: ../../dangnhap.php');
    exit();
}

// ==== Chỉ cho phép giáo viên ====
if ($_SESSION['vaiTro'] !== 'GiaoVien') {
    header('Location: ../../dangnhap.php');
    exit();
}

$currentPage = 'thong-tin';
// Gọi file CSS riêng
$pageCSS = ['ThongTinCaNhan.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['ThongTinCaNhan.js'];

// ====== KẾT NỐI DATABASE ======
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cdtn";

$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_set_charset($conn, "utf8");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// ==== Lấy thông tin giáo viên từ DB ====
$userID = $_SESSION['userID'];
$sql = "SELECT u.hoVaTen, u.email, u.sdt, u.gioiTinh, g.boMon
        FROM user u
        JOIN giaovien g ON u.userId = g.userId
        WHERE u.userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Màn hình chính</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tải CSS chung (từ thư mục gốc) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>GiaoVien/SidebarAndHeader.css">

    <!-- BỔ SUNG: Tải CSS CỤ THỂ CHO TỪNG TRANG -->
    <?php
    if (isset($pageCSS) && is_array($pageCSS)) {
        foreach ($pageCSS as $cssFile) {
            // In link CSS với đường dẫn tương đối
            // Trình duyệt sẽ tự hiểu file CSS này nằm cùng cấp với file PHP đang chạy
            // Ví dụ: khi chạy /Admin/QuanLyChuyenCan/QuanLyChuyenCan.php
            // nó sẽ tải file: /Admin/QuanLyChuyenCan/QuanLyChuyenCan.css
            echo '<link rel="stylesheet" href="' . htmlspecialchars($cssFile) . '">';
        }
    }
    ?>
</head>

<body>
    <!-- ========= PHẦN SIDEBAR ========= -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-mortarboard-fill"></i>
            <h5>Viện đào tạo ABC</h5>
        </div>
        <ul class="nav flex-column">
            <li>
                <a class="sidebar-menu-toggle" data-bs-toggle="collapse" href="#quanlychungCollapse" role="button" aria-expanded="true">Quản lý chung</a>
                <div class="collapse show" id="quanlychungCollapse">
                    <ul class="nav flex-column sidebar-submenu">
                        <li class="nav-item">
                            <!-- SỬA LINK: Bỏ "Admin/" khỏi link Dashboard -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'thong-tin') {
                                                    echo 'active';
                                                } ?>" href="../QuanLyChung/ThongTinCaNhan.php">
                                <i class="bi bi-house-door"></i> Thông tin cá nhân
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- SỬA LINK: Bỏ "Admin/" (Giả sử file HocSinh.php cũng ở gốc) -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'hoc-sinh') {
                                                    echo 'active';
                                                } ?>" href="../QuanLyChung/QuanLyHocSinh.php">
                                <i class="bi bi-people"></i> Học sinh
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- SỬA LINK: Bỏ "Admin/" (Giả sử file LopHoc.php cũng ở gốc) -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'lop-hoc') {
                                                    echo 'active';
                                                } ?>" href="../QuanLyChung/QuanLyLopHoc.php">
                                <i class="bi bi-easel"></i> Lớp học
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <a class="sidebar-menu-toggle collapsed" data-bs-toggle="collapse" href="#dulieuCollapse" role="button" aria-expanded="true">Quản lý dữ liệu</a>
                <div class="collapse show" id="dulieuCollapse">
                    <ul class="nav flex-column sidebar-submenu">
                        <li class="nav-item">
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'mon-hoc') {
                                                    echo 'active';
                                                } ?>" href="../QuanLyDuLieu/QuanLyMonHoc.php">
                                <i class="bi bi-journal-bookmark"></i> Môn học
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'tai-lieu') {
                                                    echo 'active';
                                                } ?>" href="../QuanLyDuLieu/QuanLyTaiLieu.php">
                                <i class="bi bi-file-earmark-text"></i> Tài liệu
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <a class="sidebar-menu-toggle collapsed" data-bs-toggle="collapse" href="#danhgiaCollapse" role="button" aria-expanded="true">Quản lý đánh giá</a>
                <div class="collapse show" id="danhgiaCollapse">
                    <ul class="nav flex-column sidebar-submenu">
                        <li class="nav-item">
                            <!-- KHÔNG ĐỔI: Link này vẫn trỏ vào Admin/QuanLyChuyenCan/ -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'chuyen-can') {
                                                    echo 'active';
                                                } ?>" href="../QuanLyDanhGia/QuanLyChuyenCan.php">
                                <i class="bi bi-pen"></i> Chuyên cần
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- KHÔNG ĐỔI: Giả sử DiemSo.php cũng nằm trong Admin -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'diem-so') {
                                                    echo 'active';
                                                } ?>" href="../QuanLyDanhGia/QuanLyDiemSo.php">
                                <i class="bi bi-clipboard-data"></i> Điểm số
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <a class="sidebar-menu-toggle collapsed" data-bs-toggle="collapse" href="#thongtinCollapse" role="button" aria-expanded="true">Quản lý thông tin</a>
                <div class="collapse show" id="thongbaoCollapse">
                    <ul class="nav flex-column sidebar-submenu">
                        <li class="nav-item">
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'thong-bao') {
                                                    echo 'active';
                                                } ?>" href="../ThongBao/ThongBao.php">
                                <i class="bi bi-bell"></i> Xem thông báo
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </nav>
    <!-- ========= KẾT THÚC SIDEBAR ========= -->


    <!-- Bắt đầu phần nội dung chính (sẽ được đóng trong footer.php) -->
    <div class="main-content">

        <!-- ========= PHẦN HEADER ========= -->
        <header class="header">
            <button class="menu-toggle"><i class="bi bi-list"></i></button>

            <form class="search-form ms-3">
                <input type="text" class="form-control" placeholder="Tìm kiếm">
                <button type="submit" class="btn btn-search"><i class="bi bi-search"></i></button>
            </form>

            <div class="header-actions ms-auto">
                <i class="bi bi-bell"></i>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle text-decoration-none user-profile" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-md-block"><?= htmlspecialchars($teacher['hoVaTen']) ?></span>
                        <i class="bi bi-chevron-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../QuanLyChung/ThongTinCaNhan.php">Thông tin cá nhân</a></li>
                        <li><a class="dropdown-item" href="#">Cài đặt</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="../../DangNhap.php">Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </header>
        <!-- ========= KẾT THÚC HEADER ========= -->