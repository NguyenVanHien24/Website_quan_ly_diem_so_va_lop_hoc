<?php
require_once '../../config.php';
$pageTitle = "Quản lý lớp học";
$currentPage = 'lop-hoc';
$pageCSS = ['QuanLyLopHoc.css'];
require_once '../SidebarAndHeader.php';
require_once '../../csdl/db.php';

$limit = 10; // Số dòng mỗi trang
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// 1. Đếm tổng số bản ghi
$sqlCount = "SELECT COUNT(*) as total FROM lophoc";
$resCount = $conn->query($sqlCount);
$totalRecords = $resCount->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// 2. Lấy danh sách lớp có phân trang
$sql = "
    SELECT 
        l.maLop,
        l.tenLop,
        l.khoiLop,
        l.siSo,
        l.trangThai,
        l.namHoc,
        l.kyHoc,
        u.hoVaTen AS giaoVienChuNhiem,
        gv.maGV AS maGV
    FROM lophoc l
    LEFT JOIN giaovien gv ON l.giaoVienPhuTrach = gv.maGV
    LEFT JOIN user u ON gv.userId = u.userId
    ORDER BY l.maLop DESC
    LIMIT $limit OFFSET $offset
";
$result = $conn->query($sql);

// Lấy mã lớp tiếp theo tự động
$nextLop = 1;
$resNext = $conn->query("SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lophoc'");
if ($resNext && $rowNext = $resNext->fetch_assoc()) {
    $nextLop = $rowNext['AUTO_INCREMENT'];
}
// Lấy danh sách giáo viên
$teachers = [];
$resTeachers = $conn->query("
    SELECT gv.maGV, u.hoVaTen 
    FROM giaovien gv
    JOIN user u ON gv.userId = u.userId
    ORDER BY u.hoVaTen ASC
");
if ($resTeachers) {
    while ($t = $resTeachers->fetch_assoc()) {
        $teachers[] = $t;
    }
}
$pageJS = ['QuanLyLopHoc.js'];
?>
<main>
    <div class="main-header">
        <h1 class="page-title">QUẢN LÝ LỚP HỌC</h1>
        <button class="btn btn-primary btn-add" data-bs-toggle="modal" data-bs-target="#classFormModal">
            <i class="bi bi-plus-lg me-2"></i>Thêm Lớp
        </button>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><input id="checkAll" class="form-check-input" type="checkbox"></th>
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
                    <?php
                    $stt = $offset + 1;
                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                    ?>
                            <tr>
                                <td><input class="form-check-input row-checkbox" type="checkbox" value="<?= $row['maLop'] ?>"></td>
                                <td><?= $stt++ ?></td>
                                <td><?= $row['maLop'] ?></td>
                                <td><?= $row['tenLop'] ?></td>
                                <td><?= $row['giaoVienChuNhiem'] ? $row['giaoVienChuNhiem'] : 'Chưa có' ?></td>
                                <td><?= $row['siSo'] ?></td>
                                <td>
                                    <?php if ($row['trangThai'] === 'active'): ?>
                                        <span class="badge-active">● Active</span>
                                    <?php else: ?>
                                        <span class="badge-inactive">● Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-icons">
                                    <a href="#"
                                        class="btn-edit"
                                        data-id="<?= $row['maLop'] ?>"
                                        data-name="<?= $row['tenLop'] ?>"
                                        data-teacher="<?= $row['maGV'] ?>"
                                        data-count="<?= $row['siSo'] ?>"
                                        data-year="<?= $row['namHoc'] ?>"
                                        data-semester="<?= $row['kyHoc'] ?>"
                                        data-grade="<?= $row['khoiLop'] ?>"
                                        data-status="<?= $row['trangThai'] ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#classFormModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <a href="#"
                                        class="btn-delete"
                                        data-id="<?= $row['maLop'] ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmModal">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Không có lớp học nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <?php
            $startShow = ($totalRecords > 0) ? $offset + 1 : 0;
            $endShow = min($offset + $limit, $totalRecords);
            ?>
            <span>Hiển thị <?= $startShow ?>-<?= $endShow ?>/<?= $totalRecords ?> mục</span>

            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page > 1) ? "?page=" . ($page - 1) : '#' ?>">‹</a>
                        </li>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page < $totalPages) ? "?page=" . ($page + 1) : '#' ?>">›</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button class="btn btn-danger fw-bold px-4 py-2" id="btnDeleteSelected">Xóa lớp học</button>
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
                                    <option value="2024-2025">2025-2026</option>
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
                                <input type="text" class="form-control" id="c_id" value="<?= $nextLop ?>" placeholder="auto" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tên lớp:</label>
                                <input type="text" class="form-control" id="c_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Giáo viên chủ nhiệm:</label>
                                <select class="form-select" id="c_teacher">
                                    <option value="">-- Chọn giáo viên (có thể để trống) --</option>
                                    <?php foreach ($teachers as $t): ?>
                                        <option value="<?= $t['maGV'] ?>"><?= htmlspecialchars($t['hoVaTen']) ?></option>
                                    <?php endforeach; ?>
                                </select>
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
                            <h5 class="fw-bold text-dark m-0" id="deleteMsg">Bạn chắc chắn muốn xóa lớp có mã lớp là: ?</h5>
                        </div>
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-outline-dark px-4 fw-bold" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" id="confirmDeleteBtn" class="btn btn-danger px-4 fw-bold">Xóa</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once '../../footer.php'; ?>