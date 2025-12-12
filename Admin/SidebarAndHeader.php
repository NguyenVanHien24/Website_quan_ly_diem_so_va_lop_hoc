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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>Admin/SidebarAndHeader.css">

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
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'dashboard') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>Admin/Dashboard.php">
                                <i class="bi bi-house-door"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- SỬA LINK: Bỏ "Admin/" (Giả sử file GiaoVien.php cũng ở gốc) -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'giao-vien') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>Admin/QuanLyGiaoVien/QuanLyGiaoVien.php">
                                <i class="bi bi-person-video3"></i> Giáo viên
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- SỬA LINK: Bỏ "Admin/" (Giả sử file HocSinh.php cũng ở gốc) -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'hoc-sinh') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>Admin/QuanLyHocSinh/QuanLyHocSinh.php">
                                <i class="bi bi-people"></i> Học sinh
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- SỬA LINK: Bỏ "Admin/" (Giả sử file LopHoc.php cũng ở gốc) -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'lop-hoc') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>Admin/QuanLyLopHoc/QuanLyLopHoc.php">
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
                                                } ?>" href="<?php echo BASE_URL; ?>Admin/QuanLyMonHoc/QuanLyMonHoc.php">
                                <i class="bi bi-journal-bookmark"></i> Môn học
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'tai-lieu') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>Admin/QuanLyTaiLieu/QuanLyTaiLieu.php">
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
                                                } ?>" href="<?php echo BASE_URL; ?>Admin/QuanLyChuyenCan/QuanLyChuyenCan.php">
                                <i class="bi bi-pen"></i> Chuyên cần
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- KHÔNG ĐỔI: Giả sử DiemSo.php cũng nằm trong Admin -->
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'diem-so') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>Admin/QuanLyDiemSo/QuanLyDiemSo.php">
                                <i class="bi bi-clipboard-data"></i> Điểm số
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <a class="sidebar-menu-toggle collapsed" data-bs-toggle="collapse" href="#thongtinCollapse" role="button" aria-expanded="true">Quản lý thông tin</a>
                <div class="collapse show" id="thongtinCollapse">
                    <ul class="nav flex-column sidebar-submenu">
                        <li class="nav-item">
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'thong-bao') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>Admin/QuanLyThongBao/QuanLyThongBao.php">
                                <i class="bi bi-bell"></i> Thông báo
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'su-kien') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>SuKien.php">
                                <i class="bi bi-calendar-event"></i> Sự kiện
                            </a>
                        </li> -->
                    </ul>
                </div>
            </li>
            <li>
                <a class="sidebar-menu-toggle collapsed" data-bs-toggle="collapse" href="#taikhoanCollapse" role="button" aria-expanded="true">Quản lý tài khoản</a>
                <div class="collapse show" id="taikhoanCollapse">
                    <ul class="nav flex-column sidebar-submenu">
                        <li class="nav-item">
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'phan-cong') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>Admin/QuanLyPhanCong/QuanLyPhanCong.php">
                                <i class="bi bi-person-lines-fill"></i> Phân công tài khoản
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if (isset($currentPage) && $currentPage == 'phan-quyen') {
                                                    echo 'active';
                                                } ?>" href="<?php echo BASE_URL; ?>Admin/PhanQuyen/PhanQuyen.php">
                                <i class="bi bi-shield-lock"></i> Phân quyền
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

            <form class="search-form ms-3" method="GET" action="">
                <?php
                // Giữ lại các tham số filter khác nếu có (ví dụ: status, class, subject)
                // Loại bỏ 'search' và 'page' để tránh trùng lặp hoặc lỗi phân trang khi search mới
                $queryParams = $_GET;
                unset($queryParams['search']);
                unset($queryParams['page']);

                foreach ($queryParams as $key => $value) {
                    echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
                }
                ?>

                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..."
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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
                        <span class="d-none d-md-block">Quản trị viên</span>
                        <i class="bi bi-chevron-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- <li><a class="dropdown-item" href="#">Thông tin cá nhân</a></li> -->
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

        <!-- Notification modal -->
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
                        <a href="<?php echo BASE_URL; ?>Admin/QuanLyThongBao/QuanLyThongBao.php" class="btn btn-link">Xem chi tiết</a>
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

                // Hàm tải thông báo
                async function loadNotifies() {
                    try {
                        const res = await fetch(window.BASE_URL + 'Admin/QuanLyThongBao/get_notifications.php');
                        let data;
                        try {
                            data = await res.json();
                        } catch (err) {
                            const txt = await res.text().catch(() => '');
                            console.error('Invalid JSON from get_notifications.php:', txt || err);
                            notifyList.innerHTML = '<li class="text-center text-muted small py-2">Lỗi tải thông báo</li>';
                            return;
                        }

                        notifyList.innerHTML = ''; // Xóa danh sách cũ

                        if (!data.success) {
                            notifyList.innerHTML = '<li class="text-center text-muted small py-2">Lỗi tải thông báo</li>';
                            return;
                        }

                        // Cập nhật số lượng chưa đọc (Badge)
                        updateBadge(data.unread);

                        if (!data.notifications || data.notifications.length === 0) {
                            notifyList.innerHTML = '<li class="text-center text-muted small py-2">Không có thông báo mới</li>';
                        } else {
                            // Render danh sách thông báo
                            data.notifications.forEach(n => {
                                const li = document.createElement('li');
                                li.className = 'd-flex align-items-start gap-2 p-2 border-bottom notify-item';
                                li.style.cursor = 'pointer';
                                // Style cho tin chưa đọc
                                if (parseInt(n.trangThai) === 0) {
                                    li.style.backgroundColor = '#eef6ff';
                                    li.classList.add('fw-bold'); // Thêm đậm nếu chưa đọc
                                }

                                li.innerHTML = `
                            <div class="flex-grow-1">
                                <div class="notify-title">${n.tieuDe || '(Không tiêu đề)'}</div>
                                <div class="text-muted small mt-1">${n.ngayGui || ''}</div>
                            </div>
                        `;

                                // --- SỰ KIỆN CLICK VÀO TỪNG THÔNG BÁO ---
                                li.addEventListener('click', async function() {
                                    // 1. Mở Modal hiển thị nội dung
                                    modalTitle.textContent = n.tieuDe;
                                    modalBody.textContent = n.noiDung;
                                    modalMeta.textContent = `Thời gian: ${n.ngayGui}`;
                                    notifyModal.show();

                                    // 2. Nếu tin chưa đọc -> Gọi API đánh dấu đã đọc
                                    if (parseInt(n.trangThai) === 0) {
                                        try {
                                            // Đổi giao diện ngay lập tức (Optimistic UI)
                                            li.style.backgroundColor = 'transparent';
                                            li.classList.remove('fw-bold');
                                            n.trangThai = 1; // Cập nhật biến cục bộ

                                            // Gọi Backend
                                            const formData = new FormData();
                                            formData.append('tbuId', n.tbuId);

                                            const r = await fetch(window.BASE_URL + 'Admin/QuanLyThongBao/mark_read.php', {
                                                method: 'POST',
                                                body: formData
                                            });
                                            const resp = await r.json();

                                            // Cập nhật lại số trên badge từ server trả về
                                            if (resp.success) {
                                                updateBadge(resp.unread);
                                            }
                                        } catch (e) {
                                            console.error('Lỗi đánh dấu đã đọc:', e);
                                        }
                                    }
                                });

                                notifyList.appendChild(li);
                            });
                        }

                        // Thêm Footer cho dropdown (Nút Đánh dấu tất cả)
                        const liFooter = document.createElement('li');
                        liFooter.className = 'p-2 text-center bg-light sticky-bottom';
                        liFooter.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center small">
                        <a href="#" id="markAllReadBtn" class="text-decoration-none">Đánh dấu tất cả đã đọc</a>
                        <a href="${window.BASE_URL}Admin/QuanLyThongBao/QuanLyThongBao.php" class="text-decoration-none">Xem tất cả</a>
                    </div>
                `;
                        notifyList.appendChild(liFooter);

                        // --- SỰ KIỆN CLICK "ĐÁNH DẤU TẤT CẢ" ---
                        document.getElementById('markAllReadBtn').addEventListener('click', async function(e) {
                            e.preventDefault();
                            e.stopPropagation(); // Ngăn dropdown đóng lại nếu muốn

                            try {
                                // Gọi API
                                const formData = new FormData();
                                formData.append('all', '1');

                                const r = await fetch(window.BASE_URL + 'Admin/QuanLyThongBao/mark_read.php', {
                                    method: 'POST',
                                    body: formData
                                });
                                const resp = await r.json();

                                if (resp.success) {
                                    // Cập nhật giao diện: Xóa màu nền tất cả item & ẩn badge
                                    document.querySelectorAll('.notify-item').forEach(item => {
                                        item.style.backgroundColor = 'transparent';
                                        item.classList.remove('fw-bold');
                                    });
                                    updateBadge(0);
                                }
                            } catch (e) {
                                console.error('Lỗi đánh dấu tất cả:', e);
                                alert('Có lỗi xảy ra khi xử lý.');
                            }
                        });

                    } catch (err) {
                        console.error('Lỗi loadNotifies:', err);
                    }
                }

                // Helper: Cập nhật số trên chuông
                function updateBadge(count) {
                    const num = parseInt(count);
                    if (num > 0) {
                        notifyBadge.style.display = 'inline-block';
                        notifyBadge.textContent = num > 99 ? '99+' : num;
                    } else {
                        notifyBadge.style.display = 'none';
                    }
                }

                // Tải thông báo khi click vào chuông
                notifyToggle.addEventListener('click', loadNotifies);

                // Tự động tải lần đầu
                loadNotifies();

                // Tự động refresh sau mỗi 60s
                setInterval(loadNotifies, 60000);
            });
        </script>

        <!-- Global search enhancement (client-side table filtering) -->
        <script src="<?php echo BASE_URL; ?>Admin/global-search.js"></script>