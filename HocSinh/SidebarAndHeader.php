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
    <script>
        window.BASE_URL = <?php echo json_encode(rtrim(BASE_URL, '/') . '/'); ?>;
    </script>
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
                <div class="dropdown me-3">
                    <a href="#" id="notifyToggle" class="text-decoration-none position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell" style="font-size:1.25rem"></i>
                        <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;">0</span>
                    </a>
                    <ul id="notifyList" class="dropdown-menu dropdown-menu-end p-2" style="min-width:320px; max-width:420px;">
                        <li class="text-center text-muted small">Đang tải...</li>
                    </ul>
                </div>

                <div class="dropdown">
                    <a href="#" class="dropdown-toggle text-decoration-none user-profile" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-md-block"><?= htmlspecialchars($teacher['hoVaTen']) ?></span>
                        <i class="bi bi-chevron-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>HocSinh/TrangCaNhan/ThongTinCaNhan.php">Thông tin cá nhân</a></li>
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
        <div class="modal fade" id="notifyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title">Chi tiết thông báo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="notifyModalTitle" class="fw-bold mb-2"></div>
                        <div id="notifyModalBody" class="mb-3"></div>
                        <div id="notifyModalMeta" class="text-muted small"></div>
                    </div>
                    <div class="modal-footer">
                        <a href="<?php echo BASE_URL; ?>HocSinh/TrangCaNhan/ThongBao.php" class="btn btn-link">Xem chi tiết</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const notifyList = document.getElementById('notifyList');
                const notifyBadge = document.getElementById('notifBadge');
                const notifyToggle = document.getElementById('notifyToggle');

                // Modal elements
                const notifyModalElem = document.getElementById('notifyModal');
                const notifyModal = new bootstrap.Modal(notifyModalElem);
                const modalTitle = document.getElementById('notifyModalTitle');
                const modalBody = document.getElementById('notifyModalBody');
                const modalMeta = document.getElementById('notifyModalMeta');

                let isDropdownOpen = false;

                notifyToggle.addEventListener('show.bs.dropdown', () => { isDropdownOpen = true; });
                notifyToggle.addEventListener('hide.bs.dropdown', () => { isDropdownOpen = false; });

                async function loadNotifies() {
                    try {
                        const res = await fetch(window.BASE_URL + 'HocSinh/TrangCaNhan/get_notifications.php');
                        let data;
                        try { data = await res.json(); } catch (e) { data = { success:false }; }
                        if (!data.success) return;

                        updateBadge(data.unread);

                        let htmlContent = '';
                        if (!data.notifications || data.notifications.length === 0) {
                            htmlContent = '<li class="text-center text-muted small py-2">Không có thông báo mới</li>';
                        } else {
                            data.notifications.forEach(n => {
                                const isUnread = parseInt(n.trangThai) === 0;
                                const bgStyle = isUnread ? 'background-color: #eef6ff;' : '';
                                const fwClass = isUnread ? 'fw-bold' : '';
                                htmlContent += `
                                    <li class="d-flex align-items-start gap-2 p-2 border-bottom notify-item ${fwClass}" 
                                        style="cursor: pointer; ${bgStyle}"
                                        data-id="${n.tbuId}"
                                        data-title="${n.tieuDe || ''}"
                                        data-content="${n.noiDung || ''}"
                                        data-time="${n.ngayGui}"
                                        data-status="${n.trangThai}">
                                        <div class="flex-grow-1">
                                            <div class="notify-title">${n.tieuDe || '(Không tiêu đề)'}</div>
                                            <div class="text-muted small mt-1">${n.ngayGui}</div>
                                        </div>
                                    </li>
                                `;
                            });
                        }

                        htmlContent += `
                            <li class="p-2 text-center bg-light sticky-bottom">
                                <div class="d-flex justify-content-between align-items-center small">
                                    <a href="#" id="markAllReadBtn" class="text-decoration-none">Đánh dấu tất cả đã đọc</a>
                                    <a href="${window.BASE_URL}HocSinh/TrangCaNhan/ThongBao.php" class="text-decoration-none">Xem tất cả</a>
                                </div>
                            </li>
                        `;

                        notifyList.innerHTML = htmlContent;
                        attachClickEvents();
                    } catch (err) {
                        console.error('Lỗi tải thông báo:', err);
                    }
                }

                function attachClickEvents() {
                    document.querySelectorAll('.notify-item').forEach(item => {
                        item.addEventListener('click', async function() {
                            const tbuId = this.dataset.id;
                            const status = parseInt(this.dataset.status);

                            modalTitle.textContent = this.dataset.title;
                            modalBody.textContent = this.dataset.content;
                            modalMeta.textContent = `Thời gian: ${this.dataset.time}`;
                            notifyModal.show();

                            if (status === 0) {
                                this.style.backgroundColor = 'transparent';
                                this.classList.remove('fw-bold');
                                this.dataset.status = 1;
                                try {
                                    const formData = new FormData();
                                    formData.append('tbuId', tbuId);
                                    const r = await fetch(window.BASE_URL + 'HocSinh/TrangCaNhan/mark_read.php', { method: 'POST', body: formData });
                                    const resp = await r.json();
                                    if (resp.success) updateBadge(resp.unread);
                                } catch (e) { console.error(e); }
                            }
                        });
                    });

                    const btnAll = document.getElementById('markAllReadBtn');
                    if (btnAll) {
                        btnAll.addEventListener('click', async function(e) {
                            e.preventDefault();
                            try {
                                const formData = new FormData();
                                formData.append('all', '1');
                                const r = await fetch(window.BASE_URL + 'HocSinh/TrangCaNhan/mark_read.php', { method: 'POST', body: formData });
                                const resp = await r.json();
                                if (resp.success) {
                                    document.querySelectorAll('.notify-item').forEach(item => {
                                        item.style.backgroundColor = 'transparent';
                                        item.classList.remove('fw-bold');
                                        item.dataset.status = 1;
                                    });
                                    updateBadge(0);
                                }
                            } catch (e) { console.error(e); }
                        });
                    }
                }

                function updateBadge(count) {
                    const num = parseInt(count);
                    if (num > 0) {
                        notifyBadge.style.display = 'inline-block';
                        notifyBadge.textContent = num > 99 ? '99+' : num;
                    } else {
                        notifyBadge.style.display = 'none';
                    }
                }

                notifyToggle.addEventListener('click', loadNotifies);
                loadNotifies();
                setInterval(() => { if (!isDropdownOpen) loadNotifies(); }, 60000);
            });
        </script>
        <!-- Global search enhancement (client-side table filtering) -->
        <script src="<?php echo BASE_URL; ?>HocSinh/global-search.js"></script>