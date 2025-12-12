<?php
require_once '../../config.php';
require_once '../../csdl/db.php';
// Đặt tên trang để active menu bên sidebar
$currentPage = 'phan-cong';
// Tải CSS riêng cho trang này
$pageCSS = ['QuanLyPhanCong.css'];
require_once '../SidebarAndHeader.php';
// Tải JS xử lý logic
$pageJS = ['QuanLyPhanCong.js'];
?>

<main>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">PHÂN CÔNG</h1>
        <button class="btn btn-primary btn-add-assign" data-bs-toggle="modal" data-bs-target="#assignFormModal">
            THÊM PHÂN CÔNG
        </button>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th><input class="form-check-input" type="checkbox"></th>
                        <th>STT</th>
                        <th>LỚP</th>
                        <th>KHỐI</th>
                        <th>MÔN HỌC</th>
                        <th>GIÁO VIÊN PHỤ TRÁCH</th>
                        <th>TÁC VỤ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // --- BẮT ĐẦU LOGIC PHÂN TRANG ---
                    $limit = 10; // Số dòng mỗi trang
                    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                    $offset = ($page - 1) * $limit;

                    // 1. Đếm tổng số bản ghi (để tính số trang)
                    $sqlCount = "SELECT COUNT(*) as total 
                                 FROM phan_cong pc
                                 JOIN lophoc l ON l.maLop = pc.maLop
                                 JOIN monhoc m ON m.maMon = pc.maMon
                                 JOIN giaovien g ON g.maGV = pc.maGV
                                 JOIN `user` u ON u.userId = g.userId";
                    $resCount = $conn->query($sqlCount);
                    $totalRecords = $resCount->fetch_assoc()['total'];
                    $totalPages = ceil($totalRecords / $limit);

                    // 2. Lấy danh sách phân công theo trang (Thêm LIMIT và OFFSET)
                    $assignmentSql = "SELECT pc.id, l.tenLop, l.khoiLop, m.tenMon, m.maMon, u.hoVaTen, g.maGV, pc.maLop 
                                      FROM phan_cong pc
                                      JOIN lophoc l ON l.maLop = pc.maLop
                                      JOIN monhoc m ON m.maMon = pc.maMon
                                      JOIN giaovien g ON g.maGV = pc.maGV
                                      JOIN `user` u ON u.userId = g.userId
                                      ORDER BY l.tenLop ASC, m.tenMon ASC
                                      LIMIT $limit OFFSET $offset";

                    $assignmentRs = $conn->query($assignmentSql);

                    // Cập nhật biến STT theo trang hiện tại
                    $i = $offset + 1;

                    if ($assignmentRs && $assignmentRs->num_rows > 0) {
                        while ($row = $assignmentRs->fetch_assoc()):
                    ?>
                            <tr>
                                <td><input class="form-check-input" type="checkbox" value="<?= htmlspecialchars($row['id']) ?>"></td>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($row['tenLop']) ?></td>
                                <td><?= htmlspecialchars($row['khoiLop']) ?></td>
                                <td><?= htmlspecialchars($row['tenMon']) ?></td>
                                <td><?= htmlspecialchars($row['hoVaTen']) ?></td>
                                <td class="action-icons">
                                    <a href="#" class="btn-edit"
                                        data-id="<?= htmlspecialchars($row['id']) ?>"
                                        data-maLop="<?= htmlspecialchars($row['maLop']) ?>"
                                        data-maMon="<?= htmlspecialchars($row['maMon']) ?>"
                                        data-maGV="<?= htmlspecialchars($row['maGV']) ?>"
                                        data-class="<?= htmlspecialchars($row['tenLop']) ?>"
                                        data-subject="<?= htmlspecialchars($row['tenMon']) ?>"
                                        data-teacher="<?= htmlspecialchars($row['hoVaTen']) ?>"
                                        data-bs-toggle="modal" data-bs-target="#assignFormModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="#" class="btn-delete"
                                        data-id="<?= htmlspecialchars($row['id']) ?>"
                                        data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                    <?php
                        endwhile;
                    } else {
                        echo '<tr><td colspan="7" class="text-center text-muted">Chưa có phân công nào</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer d-flex justify-content-between align-items-center mt-3">
            <?php
            $startShow = ($totalRecords > 0) ? $offset + 1 : 0;
            $endShow = min($offset + $limit, $totalRecords);
            ?>
            <div class="text-muted">Hiển thị <?= $startShow ?>-<?= $endShow ?>/<?= $totalRecords ?> mục</div>

            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page > 1) ? "?page=" . ($page - 1) : '#' ?>">&lt;</a>
                        </li>

                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page < $totalPages) ? "?page=" . ($page + 1) : '#' ?>">&gt;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button class="btn btn-danger fw-bold px-4 py-2">Xóa phân công</button>
    </div>
    <!-- THÊM PHÂN CÔNG -->

    <div class="modal fade" id="assignFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-light border-0">
                <div class="modal-header border-0 pb-0 pt-4 px-5">
                    <h2 class="modal-title fw-bold" id="modalTitle">THÊM PHÂN CÔNG</h2>
                </div>
                <div class="modal-body pt-4 px-5 pb-5">
                    <form id="assignForm">
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 fw-bold text-uppercase text-secondary">LỚP:</label>
                            <div class="col-md-9">
                                <select class="form-select py-2" id="pc_class">
                                    <option value="">Chọn lớp...</option>
                                    <?php
                                    // Lấy danh sách lớp từ CSDL
                                    $classes = [];
                                    $rs = $conn->query("SELECT maLop, tenLop FROM lophoc WHERE trangThai = 'active' ORDER BY tenLop");
                                    if ($rs) {
                                        while ($r = $rs->fetch_assoc()) {
                                            $classes[] = $r;
                                        }
                                    }
                                    foreach ($classes as $c): ?>
                                        <option value="<?= htmlspecialchars($c['maLop']) ?>"><?= htmlspecialchars($c['tenLop']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 fw-bold text-uppercase text-secondary">MÔN:</label>
                            <div class="col-md-9">
                                <select class="form-select py-2" id="pc_subject">
                                    <option value="">Chọn môn...</option>
                                    <?php
                                    // Lấy danh sách môn từ CSDL
                                    $subjects = [];
                                    $rs2 = $conn->query("SELECT maMon, tenMon FROM monhoc WHERE trangThai = 'active' ORDER BY tenMon");
                                    if ($rs2) {
                                        while ($r = $rs2->fetch_assoc()) {
                                            $subjects[] = $r;
                                        }
                                    }
                                    foreach ($subjects as $s): ?>
                                        <option value="<?= htmlspecialchars($s['maMon']) ?>"><?= htmlspecialchars($s['tenMon']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-5 align-items-center">
                            <label class="col-md-3 fw-bold text-uppercase text-secondary">GIÁO VIÊN:</label>
                            <div class="col-md-9">
                                <select class="form-select py-2" id="pc_teacher">
                                    <option value="">Chọn giáo viên...</option>
                                    <?php
                                    $rs3 = $conn->query("SELECT g.maGV, u.hoVaTen FROM giaovien g JOIN `user` u ON u.userId = g.userId WHERE g.trangThaiHoatDong = 'Hoạt động' ORDER BY u.hoVaTen");
                                    if ($rs3) {
                                        while ($r = $rs3->fetch_assoc()) {
                                            echo '<option value="' . htmlspecialchars($r['maGV']) . '">' . htmlspecialchars($r['hoVaTen']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 fw-bold text-uppercase" data-bs-dismiss="modal" style="border-color: #ccc; color: #333;">HỦY</button>
                            <button type="button" class="btn btn-primary px-4 py-2 fw-bold text-uppercase" id="btnSaveAssign" style="background-color: #0b1a48;">LƯU</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- XÓA PHÂN CÔNG -->

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-transparent">
                <div class="modal-body p-0">
                    <div class="delete-box position-relative mx-auto p-4 text-center bg-white rounded-3 shadow-sm" style="max-width: 400px;">
                        <div class="question-icon">?</div>
                        <div class="bg-light rounded-3 py-4 px-3 mt-3 mb-4">
                            <h5 class="fw-bold text-dark m-0" id="deleteMsg">Bạn chắc chắn muốn xóa phân công này?</h5>
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