<?php
require_once '../../config.php';

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
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Viện đào tạo ABC'; ?></title>
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
                <a class="sidebar-menu-toggle" data-bs-toggle="collapse" href="#quanlychungCollapse" role="button" aria-expanded="true">Quản lý chung</a>
                <div class="collapse show" id="quanlychungCollapse">
                    <ul class="nav flex-column sidebar-submenu">
                        <li class="nav-item">
                            <!-- SỬA LINK: Bỏ "Admin/" khỏi link Dashboard -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'thong-tin') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>GiaoVien/QuanLyChung/ThongTinCaNhan.php">
                                <i class="bi bi-house-door"></i> Thông tin cá nhân
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- SỬA LINK: Bỏ "Admin/" (Giả sử file HocSinh.php cũng ở gốc) -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'hoc-sinh') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>GiaoVien/QuanLyChung/QuanLyHocSinh.php">
                                <i class="bi bi-people"></i> Học sinh
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- SỬA LINK: Bỏ "Admin/" (Giả sử file LopHoc.php cũng ở gốc) -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'lop-hoc') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>GiaoVien/QuanLyChung/QuanLyLopHoc.php">
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
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'tai-lieu') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>GiaoVien/QuanLyDuLieu/QuanLyTaiLieu.php">
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
                                                } ?>" href="<?php echo BASE_URL; ?>GiaoVien/QuanLyDanhGia/QuanLyChuyenCan.php">
                                <i class="bi bi-pen"></i> Chuyên cần
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- KHÔNG ĐỔI: Giả sử DiemSo.php cũng nằm trong Admin -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'diem-so') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>GiaoVien/QuanLyDanhGia/QuanLyDiemSo.php">
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
                                                } ?>" href="<?php echo BASE_URL; ?>GiaoVien/ThongBao/ThongBao.php">
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
                <div class="dropdown me-3">
                    <a href="#" id="notifyToggle" class="text-decoration-none position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell" style="font-size:1.25rem"></i>
                        <span id="notifyBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;">0</span>
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
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>GiaoVien/QuanLyChung/ThongTinCaNhan.php">Thông tin cá nhân</a></li>
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
                        <a href="<?php echo BASE_URL; ?>GiaoVien/ThongBao/ThongBao.php" class="btn btn-link">Xem chi tiết</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const notifyList = document.getElementById('notifyList');
                const notifyBadge = document.getElementById('notifyBadge');
                const notifyToggle = document.getElementById('notifyToggle');

                // Modal elements
                const notifyModalElem = document.getElementById('notifyModal');
                const notifyModal = new bootstrap.Modal(notifyModalElem);
                const modalTitle = document.getElementById('notifyModalTitle');
                const modalBody = document.getElementById('notifyModalBody');
                const modalMeta = document.getElementById('notifyModalMeta');

                let isDropdownOpen = false;

                // Theo dõi trạng thái dropdown để tránh reload khi đang đọc
                notifyToggle.addEventListener('show.bs.dropdown', () => {
                    isDropdownOpen = true;
                });
                notifyToggle.addEventListener('hide.bs.dropdown', () => {
                    isDropdownOpen = false;
                });

                async function loadNotifies() {
                    try {
                        // Không xóa innerHTML ở đây để tránh bị nháy trắng
                        const res = await fetch(window.BASE_URL + 'GiaoVien/ThongBao/get_notifications.php');
                        const data = await res.json();

                        if (!data.success) return;

                        // Cập nhật số lượng chưa đọc (Badge)
                        updateBadge(data.unread);

                        // Nếu dropdown đang mở, ta có thể cân nhắc không render lại để tránh trôi bài
                        // Hoặc vẫn render nhưng khéo léo hơn. Ở đây ta render lại toàn bộ nội dung HTML mới.

                        let htmlContent = '';

                        if (!data.notifications || data.notifications.length === 0) {
                            htmlContent = '<li class="text-center text-muted small py-2">Không có thông báo mới</li>';
                        } else {
                            data.notifications.forEach(n => {
                                // Logic style
                                const isUnread = parseInt(n.trangThai) === 0;
                                const bgStyle = isUnread ? 'background-color: #eef6ff;' : '';
                                const fwClass = isUnread ? 'fw-bold' : '';

                                // Lưu ý: Dùng dataset để lưu dữ liệu, tránh hardcode onclick
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

                        // Footer
                        htmlContent += `
                    <li class="p-2 text-center bg-light sticky-bottom">
                        <div class="d-flex justify-content-between align-items-center small">
                            <a href="#" id="markAllReadBtn" class="text-decoration-none">Đánh dấu tất cả đã đọc</a>
                            <a href="${window.BASE_URL}GiaoVien/ThongBao/ThongBao.php" class="text-decoration-none">Xem tất cả</a>
                        </div>
                    </li>
                `;

                        notifyList.innerHTML = htmlContent;

                        // Gán lại sự kiện click sau khi render HTML mới
                        attachClickEvents();

                    } catch (err) {
                        console.error('Lỗi tải thông báo:', err);
                    }
                }

                function attachClickEvents() {
                    // Sự kiện click từng thông báo
                    document.querySelectorAll('.notify-item').forEach(item => {
                        item.addEventListener('click', async function() {
                            const tbuId = this.dataset.id;
                            const status = parseInt(this.dataset.status);

                            // Hiển thị Modal
                            modalTitle.textContent = this.dataset.title;
                            modalBody.textContent = this.dataset.content;
                            modalMeta.textContent = `Thời gian: ${this.dataset.time}`;
                            notifyModal.show();

                            // Nếu chưa đọc thì gọi API đánh dấu
                            if (status === 0) {
                                this.style.backgroundColor = 'transparent';
                                this.classList.remove('fw-bold');
                                this.dataset.status = 1; // Cập nhật lại data attribute

                                try {
                                    const formData = new FormData();
                                    formData.append('tbuId', tbuId);
                                    const r = await fetch(window.BASE_URL + 'GiaoVien/ThongBao/mark_read.php', {
                                        method: 'POST',
                                        body: formData
                                    });
                                    const resp = await r.json();
                                    if (resp.success) updateBadge(resp.unread);
                                } catch (e) {
                                    console.error(e);
                                }
                            }
                        });
                    });

                    // Sự kiện click "Đánh dấu tất cả"
                    const btnAll = document.getElementById('markAllReadBtn');
                    if (btnAll) {
                        btnAll.addEventListener('click', async function(e) {
                            e.preventDefault();
                            try {
                                const formData = new FormData();
                                formData.append('all', '1'); // PHP nhận qua $_POST['all']

                                const r = await fetch(window.BASE_URL + 'GiaoVien/ThongBao/mark_read.php', {
                                    method: 'POST',
                                    body: formData
                                });
                                const resp = await r.json();

                                if (resp.success) {
                                    // Cập nhật giao diện ngay lập tức
                                    document.querySelectorAll('.notify-item').forEach(item => {
                                        item.style.backgroundColor = 'transparent';
                                        item.classList.remove('fw-bold');
                                        item.dataset.status = 1;
                                    });
                                    updateBadge(0);
                                }
                            } catch (e) {
                                console.error(e);
                            }
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

                // Khởi chạy
                notifyToggle.addEventListener('click', loadNotifies);
                loadNotifies(); // Tải lần đầu
                setInterval(() => {
                    // Chỉ tải lại ngầm nếu dropdown KHÔNG mở để tránh làm phiền người dùng
                    if (!isDropdownOpen) {
                        loadNotifies();
                    }
                }, 60000);
            });
        </script>
        <!-- Global search enhancement (client-side table filtering) -->
        <script src="<?php echo BASE_URL; ?>GiaoVien/global-search.js"></script>