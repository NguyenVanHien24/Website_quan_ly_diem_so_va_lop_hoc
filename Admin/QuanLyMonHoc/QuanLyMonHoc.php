<?php
require_once '../../config.php';
require_once '../../csdl/db.php'; // kết nối DB
$limit = 10; // Số lượng môn học mỗi trang
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// 1. Đếm tổng số môn học
$sqlCount = "SELECT COUNT(*) as total FROM monhoc";
$resCount = $conn->query($sqlCount);
$totalRecords = $resCount->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// 2. Lấy danh sách môn học theo trang (Thêm LIMIT và OFFSET)
$sqlSubjects = "SELECT * FROM monhoc ORDER BY maMon ASC LIMIT $limit OFFSET $offset";
$resultSubjects = $conn->query($sqlSubjects);


$pageTitle = "Quản lý môn học";

// Lấy mã môn tiếp theo tự động
$nextMaMon = 1;
$resNext = $conn->query("SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'monhoc'");
if ($resNext && $rowNext = $resNext->fetch_assoc()) {
    $nextMaMon = $rowNext['AUTO_INCREMENT'];
}


$currentPage = 'mon-hoc';
$pageCSS = ['QuanLyMonHoc.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['QuanLyMonHoc.js'];
?>
<main>
    <div class="main-header">
        <h1 class="page-title">QUẢN LÝ MÔN HỌC</h1>
        <button class="btn btn-primary btn-add" data-bs-toggle="modal" data-bs-target="#subjectFormModal">
            <i class="bi bi-plus-lg me-2"></i>Thêm Môn Học
        </button>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><input id="checkAll" class="form-check-input" type="checkbox"></th>
                        <th>STT</th>
                        <th>Mã Môn</th>
                        <th>Tên Môn</th>
                        <th>Trưởng Bộ Môn</th>
                        <th>Ghi Chú</th>
                        <th>Trạng thái</th>
                        <th>Tác vụ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stt = $offset + 1;
                    if ($resultSubjects && $resultSubjects->num_rows > 0):
                        while ($row = $resultSubjects->fetch_assoc()):
                    ?>
                            <tr>
                                <td><input class="form-check-input row-checkbox" type="checkbox" data-id="<?= $row['maMon'] ?>" value="<?= $row['maMon'] ?>"></td>
                                <td><?= $stt++ ?></td>
                                <td><?= $row['maMon'] ?></td>
                                <td><?= $row['tenMon'] ?></td>
                                <td><?= $row['truongBoMon'] ? $row['truongBoMon'] : 'Chưa có' ?></td>
                                <td><?= $row['moTa'] ?></td>
                                <td>
                                    <?php if (strtolower($row['trangThai']) === 'active'): ?>
                                        <span class="badge-active">● Active</span>
                                    <?php else: ?>
                                        <span class="badge-inactive">● Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-icons">
                                    <a href="#"
                                        class="btn-edit"
                                        data-id="<?= $row['maMon'] ?>"
                                        data-name="<?= $row['tenMon'] ?>"
                                        data-head="<?= $row['truongBoMon'] ?>"
                                        data-note="<?= $row['moTa'] ?>"
                                        data-year="<?= $row['namHoc'] ?>"
                                        data-semester="<?= $row['hocKy'] ?>"
                                        data-status="<?= strtolower($row['trangThai']) ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#subjectFormModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <a href="#"
                                        class="btn-delete"
                                        data-id="<?= $row['maMon'] ?>"
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
                            <td colspan="8" class="text-center text-muted">Không có môn học nào.</td>
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
        <button class="btn btn-danger fw-bold px-4 py-2">Xóa môn học</button>
    </div>

    <div class="modal fade" id="subjectFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-4">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title fw-bold text-uppercase" id="modalTitle">THÊM MÔN HỌC</h2>
                </div>
                <div class="modal-body pt-4">
                    <form id="subjectForm">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Năm học:</label>
                                <select class="form-select" id="m_year">
                                    <option value="2024-2025">2024-2025</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Học kỳ:</label>
                                <select class="form-select" id="m_semester">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mã môn:</label>
                                <input type="text" class="form-control" id="m_id" value="<?= $nextMaMon ?>" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tên môn:</label>
                                <input type="text" class="form-control" id="m_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Trưởng bộ môn:</label>
                                <select class="form-select" id="m_head">
                                    <option value="">-- Chọn giáo viên --</option>

                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Ghi chú:</label>
                                <textarea class="form-control" id="m_note" rows="3"></textarea>
                            </div>
                            <div class="col-md-6 d-flex align-items-center pt-4">
                                <div class="d-flex align-items-center gap-4">
                                    <label class="me-3 text-nowrap">Trạng thái:</label>
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
                            <button type="button" class="btn btn-primary px-4" id="btnSaveSubject">+ Thêm mới</button>
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
                            <h5 class="fw-bold text-dark m-0" id="deleteMsg">Bạn chắc chắn muốn xóa môn học này?</h5>
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