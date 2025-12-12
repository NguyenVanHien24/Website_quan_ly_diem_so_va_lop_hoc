<?php
session_start();
require_once '../../config.php';
require_once '../../csdl/db.php';
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
$currentPage = 'hoc-sinh';
$pageCSS = ['QuanLyHocSinh.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['QuanLyHocSinh.js'];

// ==== Lấy thông tin giáo viên từ DB ====
$userID = $_SESSION['userID'];
$sql = "SELECT u.hoVaTen, u.email, u.sdt, u.gioiTinh, g.maGV, g.boMon
        FROM user u
        JOIN giaovien g ON u.userId = g.userId
        WHERE u.userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();
$stmt->close();

// ==== Lấy danh sách lớp mà giáo viên được phân công ====
$assignedClasses = [];
$classListSql = "SELECT DISTINCT l.maLop, l.tenLop 
                 FROM phan_cong pc
                 JOIN lophoc l ON l.maLop = pc.maLop
                 WHERE pc.maGV = " . (int)$teacher['maGV'] . "
                 ORDER BY l.tenLop";
$classRs = $conn->query($classListSql);
if ($classRs) {
    while ($c = $classRs->fetch_assoc()) {
        $assignedClasses[] = $c;
    }
}

// ==== Xác định lớp được chọn ====
$selectedClass = isset($_GET['class']) ? (int)$_GET['class'] : (isset($assignedClasses[0]) ? $assignedClasses[0]['maLop'] : 0);

// ==== Lấy danh sách học sinh của lớp được chọn ====
$limit = 10; // Số học sinh mỗi trang
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Điều kiện lọc lớp
$whereClass = "WHERE hs.maLopHienTai = " . (int)$selectedClass;

// 1. Đếm tổng số học sinh trong lớp (để tính số trang)
$countSql = "SELECT COUNT(*) as total 
             FROM hocsinh hs 
             $whereClass";
$resCount = $conn->query($countSql);
$totalRecords = $resCount->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// 2. Lấy danh sách học sinh theo trang
$students = [];
$studentSql = "SELECT hs.maHS, u.hoVaTen, u.email, u.sdt, u.gioiTinh, l.tenLop, hs.chucVu, hs.trangThaiHoatDong
               FROM hocsinh hs
               JOIN user u ON u.userId = hs.userId
               JOIN lophoc l ON l.maLop = hs.maLopHienTai
               $whereClass
               ORDER BY u.hoVaTen
               LIMIT $limit OFFSET $offset";

$studentRs = $conn->query($studentSql);
if ($studentRs) {
    while ($s = $studentRs->fetch_assoc()) {
        $students[] = $s;
    }
}

?>
<main>
    <div class="main-header d-flex justify-content-between align-items-end mb-4">
        <div>
            <h1 class="page-title">QUẢN LÝ HỌC SINH</h1>

            <div class="mt-3 d-flex align-items-center">
                <label for="classFilterHeader" class="fw-bold me-3 fs-5">Lớp:</label>
                <select class="form-select py-2 px-3" id="classFilterHeader" style="width: 300px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <option value="">Chọn lớp...</option>
                    <?php foreach ($assignedClasses as $c): ?>
                        <option value="<?= htmlspecialchars($c['maLop']) ?>" <?= ($c['maLop'] == $selectedClass) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['tenLop']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button class="btn btn-primary btn-add" data-bs-toggle="modal" data-bs-target="#studentFormModal">
            <i class="bi bi-plus-lg me-2"></i>Thêm Học Sinh
        </button>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><input class="form-check-input" type="checkbox"></th>
                        <th>STT</th>
                        <th>Mã Học Sinh</th>
                        <th>Họ Tên</th>
                        <th>Lớp</th>
                        <th>Chức vụ</th>
                        <th>Trạng thái</th>
                        <th>Tác vụ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($students) > 0) {
                        $i = $offset + 1;
                        foreach ($students as $student):
                            $statusBadge = ($student['trangThaiHoatDong'] === 'Hoạt động') ? '<span class="badge-active">● Active</span>' : '<span class="badge-inactive">● Inactive</span>';
                    ?>
                            <tr>
                                <td><input class="form-check-input" type="checkbox" value="<?= htmlspecialchars($student['maHS']) ?>"></td>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($student['maHS']) ?></td>
                                <td><?= htmlspecialchars($student['hoVaTen']) ?></td>
                                <td><?= htmlspecialchars($student['tenLop']) ?></td>
                                <td><?= htmlspecialchars($student['chucVu'] ?? 'Thành viên') ?></td>
                                <td><?= $statusBadge ?></td>
                                <td class="action-icons">
                                    <a href="#" class="btn-edit"
                                        data-id="<?= htmlspecialchars($student['maHS']) ?>"
                                        data-name="<?= htmlspecialchars($student['hoVaTen']) ?>"
                                        data-email="<?= htmlspecialchars($student['email'] ?? '') ?>"
                                        data-phone="<?= htmlspecialchars($student['sdt'] ?? '') ?>"
                                        data-gender="<?= htmlspecialchars($student['gioiTinh'] ?? '') ?>"
                                        data-class="<?= htmlspecialchars($student['tenLop']) ?>"
                                        data-role="<?= htmlspecialchars($student['chucVu'] ?? 'Thành viên') ?>"
                                        data-status="<?= htmlspecialchars($student['trangThaiHoatDong'] ?? 'Hoạt động') ?>"
                                        data-bs-toggle="modal" data-bs-target="#studentFormModal">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                    <?php
                        endforeach;
                    } else {
                        echo '<tr><td colspan="8" class="text-center text-muted py-4">Chưa có học sinh trong lớp này</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <?php
            $startShow = ($totalRecords > 0) ? $offset + 1 : 0;
            $endShow = min($offset + $limit, $totalRecords);

            // Hàm tạo link giữ nguyên tham số lớp (class)
            function createPageLink($p, $cls)
            {
                $query = [];
                if ($cls) $query['class'] = $cls;
                $query['page'] = $p;
                return '?' . http_build_query($query);
            }
            ?>
            <span>Hiển thị <?= $startShow ?>-<?= $endShow ?>/<?= $totalRecords ?> học sinh</span>

            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page > 1) ? createPageLink($page - 1, $selectedClass) : '#' ?>">‹</a>
                        </li>

                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= createPageLink($p, $selectedClass) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page < $totalPages) ? createPageLink($page + 1, $selectedClass) : '#' ?>">›</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-4">
        <button class="btn btn-danger fw-bold px-4 py-2" style="display: none;">Xóa học sinh</button>
    </div>
    <!-- THÊM HỌC SINH -->
    <div class="modal fade" id="studentFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title fw-bold" id="modalTitle">XEM CHI TIẾT HỌC SINH</h2>
                </div>
                <div class="modal-body pt-4">
                    <form id="studentForm">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Năm học:</label>
                                <select class="form-select" id="s_year" disabled>
                                    <option value="2024-2025">2024-2025</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Học kỳ:</label>
                                <select class="form-select" id="s_semester" disabled>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mã Học sinh:</label>
                                <input type="text" class="form-control" id="s_id" placeholder="K25..." disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và Tên:</label>
                                <input type="text" class="form-control" id="s_name" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email:</label>
                                <input type="email" class="form-control" id="s_email" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Số Điện Thoại:</label>
                                <input type="text" class="form-control" id="s_phone" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Giới tính:</label>
                                <select class="form-select" id="s_gender" disabled>
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Chức vụ:</label>
                                <select class="form-select" id="s_role" disabled>
                                    <option value="Thành viên">Thành viên</option>
                                    <option value="Lớp trưởng">Lớp trưởng</option>
                                    <option value="Bí thư">Bí thư</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Lớp:</label>
                                <select class="form-select" id="s_class" disabled>
                                    <option value="10A1">10A1</option>
                                    <option value="11A4">11A4</option>
                                    <option value="12A1">12A1</option>
                                </select>
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <div class="d-flex align-items-center gap-4 mb-2">
                                    <label class="me-3">Trạng thái:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusActive" value="active" checked disabled>
                                        <label class="form-check-label" for="statusActive">Đang học</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive" disabled>
                                        <label class="form-check-label" for="statusInactive">Đã nghỉ</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-primary px-4" id="btnSaveStudent" style="display: none;">+ Thêm mới</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- XÓA -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-transparent">
                <div class="modal-body p-0">
                    <div class="delete-box position-relative mx-auto p-4 text-center bg-white rounded-3 shadow-sm" style="max-width: 400px;">
                        <div class="question-icon">?</div>
                        <div class="bg-light rounded-3 py-4 px-3 mt-3 mb-4">
                            <h5 class="fw-bold text-dark m-0" id="deleteMsg">Bạn chắc chắn muốn xóa học sinh này?</h5>
                        </div>
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-outline-dark px-4 fw-bold" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-danger px-4 fw-bold">Xóa</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
<?php
require_once '../../footer.php';
?>