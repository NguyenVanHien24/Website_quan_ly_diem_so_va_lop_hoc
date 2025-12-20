<?php
session_start();
require_once '../../config.php';
require_once '../../csdl/db.php';

if (!isset($_SESSION["userID"])) {
    header("Location: ../../dangnhap.php");
    exit();
}

// Lấy ngày hiện tại từ request hoặc dùng hôm nay
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$selectedClass = isset($_GET['class']) ? $_GET['class'] : '';

// Kiểm tra ngày được chọn
$dateObj = DateTime::createFromFormat('Y-m-d', $selectedDate);
$dayOfWeek = $dateObj->format('N'); // 1=Mon, 7=Sun
$isToday = ($selectedDate === date('Y-m-d'));
$isFuture = strtotime($selectedDate) > strtotime(date('Y-m-d'));
$isSunday = ($dayOfWeek == 7);

// Thông báo lỗi nếu có
$error = '';
if ($isSunday) {
    $error = 'Không thể điểm danh vào ngày Chủ nhật';
}
if ($isFuture) {
    $error = 'Không thể điểm danh ngày tương lai';
}

// Lấy danh sách lớp
$classQuery = "SELECT DISTINCT l.maLop, l.tenLop FROM lophoc l ORDER BY l.tenLop";
$classRs = $conn->query($classQuery);
$classes = [];
while ($row = $classRs->fetch_assoc()) {
    $classes[] = $row;
}

// Lấy danh sách học sinh
$studentQuery = "SELECT hs.maHS, u.hoVaTen, l.maLop, l.tenLop 
                 FROM hocsinh hs
                 JOIN user u ON hs.userId = u.userId
                 LEFT JOIN lophoc l ON hs.maLopHienTai = l.maLop
                 WHERE 1=1";

if (!empty($selectedClass)) {
    $studentQuery .= " AND l.maLop = '" . $conn->real_escape_string($selectedClass) . "'";
}

$studentQuery .= " ORDER BY l.tenLop, u.hoVaTen";
$studentRs = $conn->query($studentQuery);
if (!$studentRs) {
    die("Lỗi query học sinh: " . $conn->error);
}
$students = [];
while ($row = $studentRs->fetch_assoc()) {
    $students[] = $row;
}

// Lấy bản ghi điểm danh đã có cho ngày này
$attendanceData = [];
$attendanceQuery = "SELECT maHS, trangThai FROM chuyencan WHERE ngayDiemDanh = '" . $conn->real_escape_string($selectedDate) . "'";
$attendanceRs = $conn->query($attendanceQuery);
if ($attendanceRs) {
    // Map số thành string
    $statusMap = [0 => 'absent', 1 => 'present', 2 => 'late'];
    while ($row = $attendanceRs->fetch_assoc()) {
        $trangThaiNum = (int)$row['trangThai'];
        $attendanceData[$row['maHS']] = $statusMap[$trangThaiNum] ?? null;
    }
}

$currentPage = 'chuyen-can';
$pageCSS = ['QuanLyChuyenCan.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['QuanLyChuyenCan.js'];
?>
<main>
    <h1 class="page-title">ĐIỂM DANH HỌC SINH</h1>

    <?php if ($error): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>⚠️ Lưu ý:</strong> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="GET" class="mb-4">
        <div class="row filter-section">
            <div class="col-md-3">
                <label for="attendance-date" class="form-label fw-bold">Ngày:</label>
                <input type="date" class="form-control" id="attendance-date" name="date" value="<?= htmlspecialchars($selectedDate) ?>" max="<?= date('Y-m-d') ?>">
                <small class="text-muted">Max: <?= date('Y-m-d') ?> (hôm nay)</small>
            </div>
            <div class="col-md-3">
                <label for="class-filter" class="form-label fw-bold">Lớp:</label>
                <select class="form-select" id="class-filter" name="class" onchange="this.form.submit()">
                    <option value="">-- Tất cả lớp --</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= htmlspecialchars($c['maLop']) ?>" <?php if ($selectedClass == $c['maLop']) echo 'selected'; ?>>
                            <?= htmlspecialchars($c['tenLop']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-lg-8">
            <div class="content-container">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <!-- <th><input class="form-check-input" type="checkbox"></th> -->
                                <th>STT</th>
                                <th>Lớp</th>
                                <th>Họ và tên</th>
                                <th colspan="3" class="text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($students as $student): 
                                $status = $attendanceData[$student['maHS']] ?? null;
                            ?>
                            <tr>
                                <!-- <td><input class="form-check-input" type="checkbox"></td> -->
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($student['tenLop']) ?></td>
                                <td><?= htmlspecialchars($student['hoVaTen']) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn-attendance btn-present <?= ($status === 'present') ? 'active' : '' ?>" 
                                            data-mhs="<?= htmlspecialchars($student['maHS']) ?>" 
                                            data-date="<?= htmlspecialchars($selectedDate) ?>" 
                                            data-status="present"
                                            <?= ($error) ? 'disabled' : '' ?>>
                                        Có mặt
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn-attendance btn-late <?= ($status === 'late') ? 'active' : '' ?>" 
                                            data-mhs="<?= htmlspecialchars($student['maHS']) ?>" 
                                            data-date="<?= htmlspecialchars($selectedDate) ?>" 
                                            data-status="late"
                                            <?= ($error) ? 'disabled' : '' ?>>
                                        Đến muộn
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn-attendance btn-absent <?= ($status === 'absent') ? 'active' : '' ?>" 
                                            data-mhs="<?= htmlspecialchars($student['maHS']) ?>" 
                                            data-date="<?= htmlspecialchars($selectedDate) ?>" 
                                            data-status="absent"
                                            <?= ($error) ? 'disabled' : '' ?>>
                                        Vắng mặt
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
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
                    <div id="count-present" class="count">0</div>
                </div>
                <div class="summary-item summary-late">
                    <div><i class="bi bi-exclamation-triangle-fill icon me-2"></i> Đến muộn</div>
                    <div id="count-late" class="count">0</div>
                </div>
                <div class="summary-item summary-absent">
                    <div><i class="bi bi-x-circle-fill icon me-2"></i> Vắng mặt</div>
                    <div id="count-absent" class="count">0</div>
                </div>
                <div class="summary-item summary-rate">
                    <div>Tỉ lệ đi học:</div>
                    <div id="count-rate" class="count">0%</div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once '../../footer.php';
?>