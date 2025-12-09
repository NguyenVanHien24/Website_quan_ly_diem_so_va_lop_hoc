<?php
require_once '../../config.php';
require_once '../../CSDL/db.php';
// Khởi tạo session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ==== Kiểm tra đăng nhập ====
if (!isset($_SESSION['userID'])) {
    header('Location: ../../dangnhap.php');
    exit();
}

// ==== Chỉ cho phép học sinh ====
if ($_SESSION['vaiTro'] !== 'HocSinh') {
    header('Location: ../../dangnhap.php');
    exit();
}

$currentPage = 'thong-tin';
if (!isset($currentPage)) {
    $currentPage = 'thong-tin';
}


// ==== Lấy thông tin học sinh ====
$userID = $_SESSION['userID'];
$sql = "SELECT u.hoVaTen, u.email, u.sdt, u.gioiTinh, h.maLopHienTai, h.maHS
        FROM user u
        JOIN hocsinh h ON u.userId = h.userId
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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>HocSinh/SidebarAndHeader.css">

    <!-- BỔ SUNG: Tải CSS CỤ THỂ CHO TỪNG TRANG -->
    <?php
    if (isset($pageCSS) && is_array($pageCSS)) {
        $reqPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $currentDir = dirname($reqPath);
        if ($currentDir === "." || $currentDir === "\\") $currentDir = '/';

        $baseUrlPath = parse_url(BASE_URL, PHP_URL_PATH) ?: '/';
        $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/');

        foreach ($pageCSS as $cssFile) {
            if (preg_match('#^(https?:)?//#', $cssFile) || strpos($cssFile, '/') === 0) {
                $href = $cssFile;
            } else {
                $dir = rtrim($currentDir, '/');
                $candidateRel = ($dir === '' || $dir === '/') ? '/' . $cssFile : $dir . '/' . $cssFile;
                $candidateFs = $docRoot . rtrim($baseUrlPath, '/') . $candidateRel;

                if (file_exists($candidateFs)) {
                    $href = rtrim(BASE_URL, '/') . $candidateRel;
                } else {
                    $fallbackRel = '/HocSinh/TrangCaNhan/' . $cssFile;
                    $fallbackFs = $docRoot . rtrim($baseUrlPath, '/') . $fallbackRel;
                    if (file_exists($fallbackFs)) {
                        $href = rtrim(BASE_URL, '/') . $fallbackRel;
                    } else {
                        $href = rtrim(BASE_URL, '/') . '/' . $cssFile;
                    }
                }
            }

            if (function_exists('error_log')) {
                error_log('[Sidebar] pageCSS resolved: ' . $href);
            }
            echo '<link rel="stylesheet" href="' . htmlspecialchars($href) . '?v=' . time() . '">';
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
                <a class="sidebar-menu-toggle" data-bs-toggle="collapse" href="#thongtincanhanCollapse" role="button" aria-expanded="true">Thông tin cá nhân</a>
                <div class="collapse show" id="thongtincanhanCollapse">
                    <ul class="nav flex-column sidebar-submenu">
                        <li class="nav-item">
                            <!-- SỬA LINK: Bỏ "Admin/" khỏi link Dashboard -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'thong-tin') { echo 'active'; } ?>" href="<?php echo BASE_URL; ?>HocSinh/TrangCaNhan/ThongTinCaNhan.php">
                                <i class="bi bi-house-door"></i> Thông tin cá nhân
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- SỬA LINK: Bỏ "Admin/" (Giả sử file HocSinh.php cũng ở gốc) -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'thong-bao') { echo 'active'; } ?>" href="<?php echo BASE_URL; ?>HocSinh/TrangCaNhan/ThongBao.php">
                                <i class="bi bi-people"></i>Thông báo
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <a class="sidebar-menu-toggle collapsed" data-bs-toggle="collapse" href="#tracuuthongtinCollapse" role="button" aria-expanded="true">Tra cứu thông tin</a>
                <div class="collapse show" id="tracuuthongtinCollapse">
                    <ul class="nav flex-column sidebar-submenu">
                        <li class="nav-item">
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'tai-lieu') { echo 'active'; } ?>" href="<?php echo BASE_URL; ?>HocSinh/TraCuuThongTin/TaiLieuHocTap.php">
                                <i class="bi bi-journal-bookmark"></i>Tài liệu học tập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'ket-qua') { echo 'active'; } ?>" href="<?php echo BASE_URL; ?>HocSinh/TraCuuThongTin/KetQuaHocTap.php">
                                <i class="bi bi-file-earmark-text"></i>Kết quả học tập
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
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>HocSinh/TrangCaNhan/ThongTinCaNhan.php">Thông tin cá nhân</a></li>
                        <li><a class="dropdown-item" href="#">Cài đặt</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../../DangNhap.php">Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </header>
        <!-- ========= KẾT THÚC HEADER ========= -->