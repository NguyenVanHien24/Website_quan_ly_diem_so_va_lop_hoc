<?php
session_start();
require_once '../../config.php';
require_once '../../CSDL/db.php';
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
$currentPage = 'tai-lieu';
$pageCSS = ['QuanLyTaiLieu.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['QuanLyTaiLieu.js'];
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
$maGV = $teacher ? (int)$teacher['maGV'] : 0;
$stmt->close();

// ==== Lấy danh sách lớp được phân công ====
$assignedClasses = [];
$assignedSubjects = [];
if ($maGV > 0) {
    $sql = "SELECT DISTINCT p.maLop, l.tenLop, p.maMon, m.tenMon
            FROM phan_cong p
            LEFT JOIN lophoc l ON l.maLop = p.maLop
            LEFT JOIN monhoc m ON m.maMon = p.maMon
            WHERE p.maGV = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $maGV);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        if (!isset($assignedClasses[$row['maLop']])) {
            $assignedClasses[$row['maLop']] = $row['tenLop'];
        }
        if (!isset($assignedSubjects[$row['maMon']])) {
            $assignedSubjects[$row['maMon']] = $row['tenMon'];
        }
    }
    $stmt->close();
}
// ==== XỬ LÝ PHÂN TRANG & LỌC DỮ LIỆU ====

// 1. Lấy tham số từ URL
$filterLop = isset($_GET['maLop']) && $_GET['maLop'] != '' ? (int)$_GET['maLop'] : null;
$filterMon = isset($_GET['maMon']) && $_GET['maMon'] != '' ? (int)$_GET['maMon'] : null;
$page      = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit     = 10; // Giới hạn 10 mục
$offset    = ($page - 1) * $limit;

// 2. Chuẩn bị câu lệnh WHERE cơ bản (chỉ lấy tài liệu của GV này)
$whereClause = "WHERE t.maGV = ?";
$params = [$maGV];
$types  = "i";

// 3. Thêm điều kiện lọc nếu có chọn Lớp hoặc Môn
if ($filterLop) {
    $whereClause .= " AND t.maLop = ?";
    $params[] = $filterLop;
    $types .= "i";
}
if ($filterMon) {
    $whereClause .= " AND t.maMon = ?";
    $params[] = $filterMon;
    $types .= "i";
}

// 4. Đếm tổng số bản ghi (để tính số trang)
$sqlCount = "SELECT COUNT(*) as total FROM tailieu t $whereClause";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->bind_param($types, ...$params);
$stmtCount->execute();
$totalRecords = $stmtCount->get_result()->fetch_assoc()['total'];
$stmtCount->close();

$totalPages = ceil($totalRecords / $limit);

// 5. Lấy dữ liệu chi tiết cho trang hiện tại
$sqlData = "SELECT t.*, m.tenMon, l.tenLop, u.hoVaTen as nguoiTao
            FROM tailieu t
            LEFT JOIN monhoc m ON t.maMon = m.maMon
            LEFT JOIN lophoc l ON t.maLop = l.maLop
            LEFT JOIN user u ON u.userId = (SELECT userId FROM giaovien WHERE maGV = t.maGV LIMIT 1)
            $whereClause
            ORDER BY t.ngayTao DESC
            LIMIT ? OFFSET ?";

// Thêm tham số limit và offset vào mảng params
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmtData = $conn->prepare($sqlData);
$stmtData->bind_param($types, ...$params);
$stmtData->execute();
$documents = $stmtData->get_result();
$stmtData->close();

?>

<main>
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="page-title mb-3">DANH SÁCH TÀI LIỆU</h1>

            <div class="d-flex gap-4 bg-light p-3 rounded-3" style="min-width: 600px;">
                <div class="flex-grow-1">
                    <label class="fw-bold mb-1">Lớp:</label>
                    <select class="form-select border-0 shadow-sm" id="class-filter">
                        <?php if (empty($assignedClasses)): ?>
                            <option>Không có lớp được phân công</option>
                        <?php else: ?>
                            <?php foreach ($assignedClasses as $id => $name): ?>
                                <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label class="fw-bold mb-1">Môn:</label>
                    <select class="form-select border-0 shadow-sm" id="subject-filter">
                        <?php if (empty($assignedSubjects)): ?>
                            <option>Không có môn được phân công</option>
                        <?php else: ?>
                            <?php foreach ($assignedSubjects as $id => $name): ?>
                                <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <button class="btn btn-primary btn-add-doc mt-3" data-bs-toggle="modal" data-bs-target="#docFormModal">
            <i class="bi bi-plus-lg me-2"></i>Thêm tài liệu
        </button>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input id="checkAll" class="form-check-input" type="checkbox"></th>
                        <th>STT</th>
                        <th style="width: 30%;">TIÊU ĐỀ</th>
                        <th style="width: 35%;">MÔ TẢ</th>
                        <th>TRẠNG THÁI</th>
                        <th>TÁC VỤ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($documents->num_rows > 0): ?>
                        <?php
                        $stt = $offset + 1;
                        while ($doc = $documents->fetch_assoc()):
                            // Xác định trạng thái hiển thị (ví dụ logic)
                            $statusText = isset($doc['trangThai']) && $doc['trangThai'] == 'private' ? 'Riêng tư' : 'Công khai';
                        ?>
                            <tr>
                                <td><input class="form-check-input rounded-circle row-checkbox doc-checkbox" type="checkbox" value="<?php echo $doc['maTaiLieu']; ?>"></td>
                                <td><?php echo $stt++; ?></td>
                                <td class="fw-bold text-primary"><?php echo htmlspecialchars($doc['tieuDe']); ?></td>
                                <td class="text-secondary text-truncate" style="max-width: 250px;">
                                    <?php echo htmlspecialchars($doc['moTa']); ?>
                                    <br>
                                    <small class="text-muted fst-italic">
                                        (Lớp: <?php echo htmlspecialchars($doc['tenLop']); ?> - GV: <?php echo htmlspecialchars($doc['nguoiTao']); ?>)
                                    </small>
                                </td>
                                <td>
                                    <span class="badge <?php echo $statusText == 'Công khai' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                        <?php echo $statusText; ?>
                                    </span>
                                </td>
                                <td class="action-icons">
                                    <a href="#" class="btn-edit"
                                        data-id="<?php echo $doc['maTaiLieu']; ?>"
                                        data-title="<?php echo htmlspecialchars($doc['tieuDe']); ?>"
                                        data-desc="<?php echo htmlspecialchars($doc['moTa']); ?>"
                                        data-class="<?php echo $doc['maLop']; ?>"
                                        data-subject="<?php echo $doc['maMon']; ?>"
                                        data-status="<?php echo isset($doc['trangThai']) ? $doc['trangThai'] : 'public'; ?>"
                                        data-bs-toggle="modal" data-bs-target="#docFormModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <a href="#" class="btn-delete"
                                        data-id="<?php echo $doc['maTaiLieu']; ?>"
                                        data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Chưa có tài liệu nào.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer d-flex justify-content-between align-items-center mt-3 px-2">
            <?php
            $startShow = ($totalRecords > 0) ? $offset + 1 : 0;
            $endShow = min($offset + $limit, $totalRecords);
            ?>
            <div id="docCount" class="text-secondary fw-bold bg-light py-1 px-3 rounded">
                Hiển thị <?= $startShow ?>-<?= $endShow ?>/<?= $totalRecords ?> mục
            </div>

            <nav id="docPagination"></nav>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button class="btn btn-danger fw-bold px-4 py-2" id="btnDeleteMulti">Xóa tài liệu</button>
    </div>


    <div class="modal fade" id="docFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title fw-bold text-uppercase" id="modalTitle">THÊM TÀI LIỆU</h2>
                </div>
                <div class="modal-body pt-4">
                    <form id="docForm">
                        <input type="hidden" id="d_id" value="">
                        <input type="hidden" id="d_maLop" value="">
                        <input type="hidden" id="d_maMon" value="">
                        <div class="row mb-4">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase fs-6">TIÊU ĐỀ</label>
                                    <input type="text" class="form-control" id="d_title">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase fs-6">MÔ TẢ</label>
                                    <input type="text" class="form-control" id="d_desc">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase fs-6">LỚP</label>
                                    <select class="form-select" id="d_class">
                                        <?php foreach ($assignedClasses as $id => $name): ?>
                                            <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-uppercase fs-6">MÔN HỌC</label>
                                    <select class="form-select" id="d_subject">
                                        <?php foreach ($assignedSubjects as $id => $name): ?>
                                            <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase fs-6 mb-3">TRẠNG THÁI</label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="statusPublic" value="public" checked>
                                            <label class="form-check-label text-uppercase" for="statusPublic">CÔNG KHAI</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="statusPrivate" value="private">
                                            <label class="form-check-label text-uppercase" for="statusPrivate">RIÊNG TƯ</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light p-4 rounded-3 mb-4">
                            <label class="form-label fw-bold text-uppercase fs-6 text-primary">FILE TÀI LIỆU</label>
                            <div class="d-flex gap-3">
                                <input type="text" class="form-control bg-white" id="fileNameDisplay" placeholder="Upload file" readonly>

                                <input type="file" id="realFileInput" style="display: none;">

                                <button type="button" class="btn btn-primary text-nowrap px-4" id="btnUploadTrigger" style="background-color: #0b1a48;">
                                    <i class="bi bi-cloud-arrow-up me-2"></i>Tải lên
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <button type="button" class="btn btn-outline-dark px-4 fw-bold" data-bs-dismiss="modal">Quay lại</button>
                            <button type="button" class="btn btn-primary px-4 fw-bold" id="btnSaveDoc" style="background-color: #0b1a48;">Thêm mới</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-none bg-transparent">
                <div class="modal-body p-0">
                    <div class="delete-box position-relative mx-auto p-4 text-center bg-white rounded-3 shadow-sm" style="max-width: 450px;">
                        <div class="question-icon">?</div>

                        <div class="bg-light rounded-3 py-4 px-4 mt-3 mb-4">
                            <h5 class="fw-bold text-dark m-0 lh-base" id="deleteMsg">Bạn chắc chắn muốn xóa<br>tài liệu này không?</h5>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-outline-dark px-5 fw-bold" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-danger px-5 fw-bold" style="background-color: #c53030;">Xóa</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const classFilter = document.getElementById('class-filter');
        const subjectFilter = document.getElementById('subject-filter');

        // Hàm xử lý reload trang với tham số
        function applyFilters() {
            const maLop = classFilter.value;
            const maMon = subjectFilter.value;
            // Reset về trang 1 khi lọc
            let url = '?page=1';

            if (maLop && maLop !== 'Không có lớp được phân công') {
                url += '&maLop=' + maLop;
            }
            if (maMon && maMon !== 'Không có môn được phân công') {
                url += '&maMon=' + maMon;
            }

            window.location.href = url;
        }

        // Gán giá trị hiện tại từ PHP vào Select box (để giữ trạng thái sau khi reload)
        <?php if ($filterLop): ?>
            classFilter.value = "<?php echo $filterLop; ?>";
        <?php endif; ?>

        <?php if ($filterMon): ?>
            subjectFilter.value = "<?php echo $filterMon; ?>";
        <?php endif; ?>

        // Bắt sự kiện change
        classFilter.addEventListener('change', applyFilters);
        subjectFilter.addEventListener('change', applyFilters);
    });
</script>
<?php require_once '../../footer.php'; ?>