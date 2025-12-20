<?php
session_start();
require_once '../../config.php';
require_once '../../CSDL/db.php';

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

$currentPage = 'tai-lieu';
$pageCSS = ['TaiLieuHocTap.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['TaiLieuHocTap.js'];

// ==== Lấy thông tin học sinh ====
$userID = $_SESSION['userID'];
$sql = "SELECT hs.maHS, hs.maLopHienTai as maLop FROM hocsinh hs 
        JOIN user u ON u.userId = hs.userId 
        WHERE u.userId = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('SQL Error: ' . $conn->error);
}
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$maHS = $student ? (int)$student['maHS'] : 0;
$maLop = $student ? (int)$student['maLop'] : 0;
$stmt->close();

// ==== Lấy danh sách môn học của lớp học sinh ====
$subjects = [];
if ($maLop > 0) {
    $sql = "SELECT DISTINCT m.maMon, m.tenMon 
            FROM monhoc m
            JOIN phan_cong p ON p.maMon = m.maMon
            WHERE p.maLop = ?
            ORDER BY m.tenMon ASC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('SQL Error: ' . $conn->error);
    }
    $stmt->bind_param('i', $maLop);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $subjects[] = $row;
    }
    $stmt->close();
}
$subjectMap = [];
foreach ($subjects as $s) {
    $subjectMap[(int)$s['maMon']] = $s['tenMon'];
}
// ==== XỬ LÝ PHÂN TRANG & LỌC TÀI LIỆU ====
$filterMon = isset($_GET['maMon']) && $_GET['maMon'] != '' ? (int)$_GET['maMon'] : 0;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // giới hạn 10 mục
$offset = ($page - 1) * $limit;

$documents = [];
$totalRecords = 0;
$totalPages = 0;

if ($filterMon && $maLop > 0) {
    // đếm tổng số
    $countSql = "SELECT COUNT(*) as total FROM tailieu t WHERE t.maMon = ? AND t.maLop = ?";
    $cstmt = $conn->prepare($countSql);
    if ($cstmt) {
        $cstmt->bind_param('ii', $filterMon, $maLop);
        $cstmt->execute();
        $totalRecords = (int)$cstmt->get_result()->fetch_assoc()['total'];
        $cstmt->close();
    }

    $totalPages = ($limit > 0) ? (int)ceil($totalRecords / $limit) : 1;

    // lấy dữ liệu trang hiện tại
    $sqlData = "SELECT t.*, (SELECT u.hoVaTen FROM giaovien g JOIN user u ON g.userId = u.userId WHERE g.maGV = t.maGV LIMIT 1) as nguoiTao
                FROM tailieu t
                WHERE t.maMon = ? AND t.maLop = ?
                ORDER BY t.ngayTao DESC
                LIMIT ? OFFSET ?";

    $dstmt = $conn->prepare($sqlData);
    if ($dstmt) {
        $dstmt->bind_param('iiii', $filterMon, $maLop, $limit, $offset);
        $dstmt->execute();
        $res = $dstmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $documents[] = $row;
        }
        $dstmt->close();
    }
}
?>

<main>
    <h1 class="page-title mb-4">TÀI LIỆU HỌC TẬP</h1>

    <div class="mb-4" style="max-width: 400px;">
        <select class="form-select py-2" id="subjectFilter" style="border-radius: 8px; border-color: #e0e0e0;">
            <option value="">-- Chọn môn học --</option>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?php echo $subject['maMon']; ?>">
                    <?php echo htmlspecialchars($subject['tenMon']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="content-container bg-white p-0 border rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">STT</th>
                        <th style="width: 20%;">TIÊU ĐỀ</th>
                        <th style="width: 25%;">MÔ TẢ</th>
                        <th>MÔN HỌC</th>
                        <th>GV GỬI</th>
                        <th class="text-center">TÁC VỰ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$filterMon): ?>
                        <tr>
                            <td colspan="6" class="text-center text-secondary">Chọn môn học để xem tài liệu</td>
                        </tr>
                    <?php else: ?>
                        <?php if (count($documents) > 0): ?>
                            <?php $stt = $offset + 1;
                            foreach ($documents as $doc): ?>
                                <tr>
                                    <td><?php echo $stt++; ?></td>
                                    <td class="fw-bold text-primary"><?php echo htmlspecialchars($doc['tieuDe']); ?></td>
                                    <td class="text-secondary text-truncate" style="max-width: 250px;">
                                        <?php echo htmlspecialchars($doc['moTa']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($subjectMap[(int)$doc['maMon']] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($doc['nguoiTao'] ?? ''); ?></td>
                                    <td class="text-center">
                                        <a href="#" class="btn-view" data-id="<?php echo $doc['maTaiLieu']; ?>" data-title="<?php echo htmlspecialchars($doc['tieuDe']); ?>" data-desc="<?php echo htmlspecialchars($doc['moTa']); ?>" data-bs-toggle="modal" data-bs-target="#viewDocModal"><i class="bi bi-eye"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">Chưa có tài liệu cho môn này.</td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    $startShow = ($totalRecords > 0) ? $offset + 1 : 0;
    $endShow = min($offset + $limit, $totalRecords);
    ?>
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-secondary">Hiển thị <?= $startShow ?>-<?= $endShow ?>/<?= $totalRecords ?> mục</div>

        <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?maMon=<?php echo $filterMon; ?>&page=<?php echo max(1, $page - 1); ?>">&lt;</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?maMon=<?php echo $filterMon; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?maMon=<?php echo $filterMon; ?>&page=<?php echo min($totalPages, $page + 1); ?>">&gt;</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button class="btn btn-primary px-4 py-2 fw-bold" style="background-color: #0b1a48; border-radius: 6px;">Tải về tài liệu</button>
    </div>


    <div class="modal fade" id="viewDocModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content p-4 border-0">
                <div class="modal-body">
                    <h3 class="fw-bold mb-3" id="m_title">Tiêu đề tài liệu</h3>

                    <p class="text-secondary fw-bold mb-4" id="m_desc" style="text-align: justify;">
                        Nội dung mô tả...
                    </p>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Môn học:</strong> <span id="m_subject"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Giáo viên:</strong> <span id="m_teacher"></span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <button type="button" class="btn btn-outline-dark px-4 py-2" data-bs-dismiss="modal">Quay lại</button>
                        <a href="#" class="btn btn-primary px-4 py-2" id="downloadBtn" style="background-color: #0b1a48;">
                            <i class="bi bi-cloud-arrow-down me-2"></i>Tải về tài liệu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subjectFilter = document.getElementById('subjectFilter');
        if (!subjectFilter) return;

        function applyFilters() {
            const maMon = subjectFilter.value;
            let url = '?page=1';
            if (maMon) {
                url += '&maMon=' + encodeURIComponent(maMon);
            }
            window.location.href = url;
        }

        <?php if ($filterMon): ?>
            subjectFilter.value = "<?= $filterMon ?>";
        <?php endif; ?>

        subjectFilter.addEventListener('change', applyFilters);
    });
</script>
<?php require_once '../../footer.php'; ?>