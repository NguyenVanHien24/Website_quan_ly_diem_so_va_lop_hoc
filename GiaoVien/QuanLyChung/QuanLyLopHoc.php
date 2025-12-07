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
$currentPage = 'lop-hoc';
$pageCSS = ['QuanLyLopHoc.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['QuanLyLopHoc.js'];
// ==== Lấy thông tin giáo viên từ DB ====
$userID = $_SESSION['userID'];
$sql = "SELECT u.hoVaTen, u.email, u.sdt, u.gioiTinh, g.boMon, g.maGV
    FROM user u
    JOIN giaovien g ON u.userId = g.userId
    WHERE u.userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();
$stmt->close();

// Lấy danh sách lớp mà giáo viên này được phân công
$assignedClasses = [];
if (!empty($teacher['maGV'])) {
    $maGV = (int)$teacher['maGV'];
    $sqlc = "SELECT DISTINCT l.maLop, l.tenLop, l.khoiLop, l.giaoVienPhuTrach, l.siSo, l.trangThai, l.namHoc, l.kyHoc,
                     gv.maGV AS gv_maGV, u2.hoVaTen AS gvName
             FROM phan_cong pc
             JOIN lophoc l ON l.maLop = pc.maLop
             LEFT JOIN giaovien gv ON gv.maGV = l.giaoVienPhuTrach
             LEFT JOIN `user` u2 ON u2.userId = gv.userId
             WHERE pc.maGV = $maGV
             ORDER BY l.tenLop";
    $rs = $conn->query($sqlc);
    if ($rs) {
        while ($r = $rs->fetch_assoc()) {
            $assignedClasses[] = $r;
        }
    }
}

?>
<main>
    <div class="main-header">
        <h1 class="page-title">QUẢN LÝ LỚP HỌC</h1>
        <button class="btn btn-primary btn-add" data-bs-toggle="modal" data-bs-target="#classFormModal" style="display: none;">
            <i class="bi bi-plus-lg me-2"></i>Thêm Lớp
        </button>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><input class="form-check-input" type="checkbox"></th>
                        <th>STT</th>
                        <th>Mã Lớp</th>
                        <th>Tên Lớp</th>
                        <th>Giáo viên chủ nhiệm</th>
                        <th>Sĩ số</th>
                        <th>Trạng thái</th>
                        <th>Tác vụ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($assignedClasses) > 0): $i = 1; ?>
                        <?php foreach ($assignedClasses as $c): ?>
                            <tr>
                                <td><input class="form-check-input" type="checkbox" value="<?= htmlspecialchars($c['maLop']) ?>"></td>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($c['maLop']) ?></td>
                                <td><?= htmlspecialchars($c['tenLop']) ?></td>
                                <td><?= htmlspecialchars($c['gvName'] ?? '') ?></td>
                                <td><?= htmlspecialchars($c['siSo'] ?? '') ?></td>
                                <td><?= htmlspecialchars($c['trangThai'] ?? '') ? '<span class="badge-active">● Active</span>' : '<span class="badge-inactive">● Inactive</span>' ?></td>
                                <td class="action-icons">
                                    <a href="#" class="btn-view" data-id="<?= htmlspecialchars($c['maLop']) ?>" data-name="<?= htmlspecialchars($c['tenLop']) ?>" data-teacher="<?= htmlspecialchars($c['gvName'] ?? '') ?>" data-count="<?= htmlspecialchars($c['siSo'] ?? '') ?>" data-year="<?= htmlspecialchars($c['namHoc'] ?? '') ?>" data-semester="<?= htmlspecialchars($c['kyHoc'] ?? '') ?>" data-grade="<?= htmlspecialchars($c['khoiLop'] ?? '') ?>" data-status="<?= htmlspecialchars($c['trangThai'] ?? '') ?>" data-bs-toggle="modal" data-bs-target="#classFormModal">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted">Bạn chưa được phân công lớp nào</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span>1-1/18 mục</span>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4" style="display: none;">
        <button class="btn btn-danger fw-bold px-4 py-2">Xóa lớp học</button>
    </div>

    <div class="modal fade" id="classFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-4">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title fw-bold text-uppercase" id="modalTitle">THÊM LỚP HỌC</h2>
                </div>
                <div class="modal-body pt-4">
                    <form id="classForm">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Năm học:</label>
                                <select class="form-select" id="c_year">
                                    <option value="2024-2025">2024-2025</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Học kỳ:</label>
                                <select class="form-select" id="c_semester">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Khối:</label>
                                <select class="form-select" id="c_grade">
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mã lớp:</label>
                                <input type="text" class="form-control" id="c_id" placeholder="251104">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tên lớp:</label>
                                <input type="text" class="form-control" id="c_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Giáo viên chủ nhiệm:</label>
                                <input type="text" class="form-control" id="c_teacher">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Sĩ số:</label>
                                <input type="number" class="form-control" id="c_count">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="d-flex align-items-center gap-4 mb-2">
                                    <label class="me-3">Trạng thái:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusActive" value="active" checked>
                                        <label class="form-check-label" for="statusActive">Đang hoạt động</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive">
                                        <label class="form-check-label" for="statusInactive">Tạm dừng</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-primary px-4" id="btnSaveClass">+ Thêm mới</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-transparent">
                <div class="modal-body p-0">
                    <div class="delete-box position-relative mx-auto p-4 text-center bg-white rounded-3 shadow-sm" style="max-width: 400px;">
                        <div class="question-icon">?</div>
                        <div class="bg-light rounded-3 py-4 px-3 mt-3 mb-4">
                            <h5 class="fw-bold text-dark m-0" id="deleteMsg">Bạn chắc chắn muốn xóa lớp 251104?</h5>
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
<?php require_once '../../footer.php'; ?>