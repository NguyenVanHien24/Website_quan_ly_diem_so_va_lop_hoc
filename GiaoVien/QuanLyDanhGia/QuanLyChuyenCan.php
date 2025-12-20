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
$currentPage = 'chuyen-can';
$pageCSS = ['QuanLyChuyenCan.css'];
require_once '../SidebarAndHeader.php';

$userID = $_SESSION['userID'];
// Lấy mã giáo viên
$stmt = $conn->prepare("SELECT maGV FROM giaovien WHERE userId = ?");
$stmt->bind_param('i', $userID);
$stmt->execute();
$res = $stmt->get_result();
$gv = $res->fetch_assoc();
$stmt->close();
$maGV = $gv ? (int)$gv['maGV'] : 0;

// Lấy danh sách phân công (lớp + môn) của giáo viên
$assignedClasses = [];
$assignedSubjects = [];
if ($maGV > 0) {
    $sql = "SELECT DISTINCT p.maLop, l.tenLop, p.maMon, m.tenMon
            FROM phan_cong p
            LEFT JOIN lophoc l ON l.maLop = p.maLop
            LEFT JOIN monhoc m ON m.maMon = p.maMon
            WHERE p.maGV = '" . $conn->real_escape_string($maGV) . "'";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        if (!isset($assignedClasses[$row['maLop']])) {
            $assignedClasses[$row['maLop']] = $row['tenLop'];
        }
        if (!isset($assignedSubjects[$row['maMon']])) {
            $assignedSubjects[$row['maMon']] = $row['tenMon'];
        }
    }
}

$today = date('Y-m-d');
?>
<main>
    <h1 class="page-title">ĐIỂM DANH HỌC SINH</h1>

    <div class="row mb-4 filter-section">
        <div class="col-md-3">
            <label for="attendance-date" class="form-label fw-bold">Ngày:</label>
            <input type="date" class="form-control" id="attendance-date" value="<?php echo $today; ?>" max="<?php echo $today; ?>">
            <div class="form-text">Không cho phép chọn Chủ nhật hoặc ngày tương lai.</div>
        </div>
        <div class="col-md-3">
            <label for="class-filter" class="form-label fw-bold">Lớp:</label>
            <select class="form-select" id="class-filter">
                <?php if (empty($assignedClasses)) : ?>
                    <option>Không có lớp được phân công</option>
                <?php else: ?>
                    <?php foreach ($assignedClasses as $id => $name) : ?>
                        <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="subject-filter" class="form-label fw-bold">Môn:</label>
            <select class="form-select" id="subject-filter">
                <?php if (empty($assignedSubjects)) : ?>
                    <option>Không có môn được phân công</option>
                <?php else: ?>
                    <?php foreach ($assignedSubjects as $id => $name) : ?>
                        <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="content-container">
                <div class="table-responsive">
                    <table class="table" id="attendance-table">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Lớp</th>
                                <th>Họ và tên</th>
                                <th class="text-center">Tác vụ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dữ liệu sẽ được nạp bằng JS -->
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>

        <div class="col-lg-4">
            <div class="summary-panel">
                <h5 class="panel-title">Tổng quan điểm danh</h5>
                <div class="summary-item summary-present">
                    <div><i class="bi bi-check-circle-fill icon me-2"></i> Có mặt</div>
                    <div class="count" id="count-present">0</div>
                </div>
                <div class="summary-item summary-late">
                    <div><i class="bi bi-exclamation-triangle-fill icon me-2"></i> Đến muộn</div>
                    <div class="count" id="count-late">0</div>
                </div>
                <div class="summary-item summary-absent">
                    <div><i class="bi bi-x-circle-fill icon me-2"></i> Vắng mặt</div>
                    <div class="count" id="count-absent">0</div>
                </div>
                <div class="summary-item summary-rate">
                    <div>Tỉ lệ đi học:</div>
                    <div class="count" id="count-rate">0%</div>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- ========= KẾT THÚC NỘI DUNG CHÍNH CỦA TRANG ========= -->

<?php
require_once dirname(dirname(__DIR__)) . '/footer.php';
?>
<script src="QuanLyChuyenCan.js"></script>