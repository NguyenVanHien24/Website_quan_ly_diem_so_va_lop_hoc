<?php
session_start();
require_once '../../config.php';
require_once '../../csdl/db.php';
if (!isset($_SESSION["userID"])) {
    header("Location: ../../dangnhap.php");
    exit();
}

// Lấy năm học & học kỳ hiện tại
$yearNow = date('Y');
$monthNow = date('n');
if ($monthNow >= 1 && $monthNow <= 6) {
    $currentSemester = 2;
    $currentYear = ($yearNow - 1) . '-' . $yearNow;
} else {
    $currentSemester = 1;
    $currentYear = $yearNow . '-' . ($yearNow + 1);
}

// Lấy giá trị filter từ request
$selectedClass = isset($_GET['class']) ? $_GET['class'] : '';
$selectedSubject = isset($_GET['subject']) ? $_GET['subject'] : '';

// Lấy danh sách lớp từ CSDL
$classRs = $conn->query("SELECT maLop, tenLop FROM lophoc ORDER BY tenLop");
$classes = [];
while ($row = $classRs->fetch_assoc()) {
    $classes[] = $row;
}

// Lấy danh sách môn học từ CSDL
$subjectRs = $conn->query("SELECT maMon, tenMon FROM monhoc ORDER BY tenMon");
$subjects = [];
while ($row = $subjectRs->fetch_assoc()) {
    $subjects[] = $row;
}

// Query để lấy bảng điểm
$scoreQuery = "
        SELECT 
        hs.maHS,
        u.hoVaTen,
        l.tenLop,
        l.maLop,
        m.maMon,
        m.tenMon,
        d.loaiDiem,
        d.giaTriDiem
    FROM hocsinh hs
    JOIN user u ON hs.userId = u.userId
    JOIN lophoc l ON hs.maLopHienTai = l.maLop
    CROSS JOIN monhoc m
    LEFT JOIN diemso d 
        ON d.maHS = hs.maHS 
        AND d.maMonHoc = m.maMon
        AND d.namHoc = '$currentYear'
        AND d.hocKy = $currentSemester
    WHERE 1=1
";

// Thêm filter nếu có
if (!empty($selectedClass)) {
    $scoreQuery .= " AND l.maLop = " . intval($selectedClass);
}
if (!empty($selectedSubject)) {
    $scoreQuery .= " AND m.maMon = " . intval($selectedSubject);
}

$scoreQuery .= " ORDER BY l.tenLop, u.hoVaTen, m.tenMon";

$scoreRs = $conn->query($scoreQuery);
$scores = [];

while ($row = $scoreRs->fetch_assoc()) {
    $key = $row['maHS'] . '_' . $row['maMon'];

    if (!isset($scores[$key])) {
        $scores[$key] = [
            'maHS' => $row['maHS'],
            'hoVaTen' => $row['hoVaTen'],
            'tenLop' => $row['tenLop'],
            'maLop' => $row['maLop'],
            'maMon' => $row['maMon'],
            'tenMon' => $row['tenMon'],
            'HK1' => ['mouth' => null, '45m' => null, 'gk' => null, 'ck' => null],
            'HK2' => ['mouth' => null, '45m' => null, 'gk' => null, 'ck' => null],
        ];
    }

    if ($row['loaiDiem']) {
        $type = strtolower($row['loaiDiem']); // miệng, 45m, gk, ck
        $semester = ($row['hocKy'] == 1) ? 'HK1' : 'HK2';

        $scores[$key][$semester][$type] = $row['giaTriDiem'];
    }
}

$currentPage = 'diem-so';
$pageCSS = ['QuanLyDiemSo.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['QuanLyDiemSo.js'];
?>
<main>
    <h1 class="page-title">BẢNG ĐIỂM</h1>

    <form method="GET">
        <div class="row mb-4 filter-section">
            <div class="col-md-4">
                <label for="class-filter" class="form-label fw-bold">Lớp:</label>
                <select class="form-select" id="class-filter" name="class" onchange="this.form.submit()">
                    <option value="">-- Tất cả lớp --</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['maLop'] ?>" <?= ($selectedClass == $c['maLop']) ? 'selected' : '' ?>>
                            <?= $c['tenLop'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="subject-filter" class="form-label fw-bold">Môn:</label>
                <select class="form-select" id="subject-filter" name="subject" onchange="this.form.submit()">
                    <option value="">-- Tất cả môn --</option>
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?= $s['maMon'] ?>" <?= ($selectedSubject == $s['maMon']) ? 'selected' : '' ?>>
                            <?= $s['tenMon'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><input class="form-check-input" type="checkbox"></th>
                        <th>STT</th>
                        <th>Mã HS</th>
                        <th>Họ Tên</th>
                        <th>Môn học</th>
                        <th>Điểm Thi Học Kì I</th>
                        <th>Điểm Thi Học Kì II</th>
                        <th>Trung Bình Môn</th>
                        <th>Tác Vụ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    foreach ($scores as $sc): ?>
                        <tr>
                            <td><input class="form-check-input" type="checkbox"></td>
                            <td><?= $i++ ?></td>
                            <td><?= $sc['maHS'] ?></td>
                            <td><?= $sc['hoVaTen'] ?></td>
                            <td><?= $sc['tenMon'] ?></td>

                            <td><?= $sc['HK1']['ck'] ?? '' ?></td>
                            <td><?= $sc['HK2']['ck'] ?? '' ?></td>

                            <td>
                                <?php
                                $avg = (
                                    ($sc['HK1']['ck'] ?? 0) +
                                    ($sc['HK2']['ck'] ?? 0)
                                ) / 2;
                                echo $avg ?: '';
                                ?>
                            </td>

                            <td class="action-icons">
                                <a href="#" class="btn-view">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="#" class="btn-edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="#">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <span>1-4/18 mục</span>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item"><a class="page-link" href="#">‹</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">/5</a></li>
                    <li class="page-item"><a class="page-link" href="#">›</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-3 mt-4">
        <button class="btn btn-import">Import bảng điểm</button>
        <button class="btn btn-export">Xuất bảng điểm</button>
    </div>

    <!-- NHẬP ĐIỂM -->
    <div class="modal fade" id="gradeEntryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="modalActionTitle">CẬP NHẬT ĐIỂM</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="student-info-bar">
                        <span id="edit_student_name">HỌ TÊN HỌC SINH: TRẦN HOÀNG NHI</span>
                        <span id="edit_student_id">MÃ HỌC SINH: K25110386</span>
                    </div>
                    <form>
                        <div>
                            <div class="semester-title">HỌC KỲ I</div>
                            <div class="row g-4">
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM MIỆNG:</label><input type="text" class="form-control" id="edit_s1_mouth" value="9.0"></div>
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI GK:</label><input type="text" class="form-control" id="edit_s1_gk" value="8.5"></div>
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM 1 TIẾT:</label><input type="text" class="form-control" id="edit_s1_45m" value="8.0"></div>
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI CK:</label><input type="text" class="form-control" id="edit_s1_ck" value="9.0"></div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="semester-title">HỌC KỲ II</div>
                            <div class="row g-4">
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM MIỆNG:</label><input type="text" class="form-control" id="edit_s2_mouth"></div>
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI GK:</label><input type="text" class="form-control" id="edit_s2_gk"></div>
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM 1 TIẾT:</label><input type="text" class="form-control" id="edit_s2_45m"></div>
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI CK:</label><input type="text" class="form-control" id="edit_s2_ck"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-3 mt-5">
                            <button type="button" class="btn btn-custom-cancel" data-bs-dismiss="modal">HỦY</button>
                            <button type="submit" class="btn btn-custom-save">LƯU</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewGradeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title">CHI TIẾT ĐIỂM</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="student-info-bar bg-light"> <span id="view_student_name">HỌ TÊN HỌC SINH: TRẦN HOÀNG NHI</span>
                        <span id="view_student_id">MÃ HỌC SINH: K25110386</span>
                    </div>

                    <div>
                        <div class="semester-title">HỌC KỲ I</div>
                        <div class="row g-4">
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM MIỆNG:</label><input type="text" class="form-control bg-light" id="view_s1_mouth" value="9.0" readonly></div>
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI GK:</label><input type="text" class="form-control bg-light" id="view_s1_gk" value="8.5" readonly></div>
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM 1 TIẾT:</label><input type="text" class="form-control bg-light" id="view_s1_45m" value="8.0" readonly></div>
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI CK:</label><input type="text" class="form-control bg-light" id="view_s1_ck" value="9.0" readonly></div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="semester-title">HỌC KỲ II</div>
                        <div class="row g-4">
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM MIỆNG:</label><input type="text" class="form-control bg-light" id="view_s2_mouth" readonly></div>
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI GK:</label><input type="text" class="form-control bg-light" id="view_s2_gk" readonly></div>
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM 1 TIẾT:</label><input type="text" class="form-control bg-light" id="view_s2_45m" readonly></div>
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI CK:</label><input type="text" class="form-control bg-light" id="view_s2_ck" readonly></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-5">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">ĐÓNG</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Yêu cầu file footer.php để đóng các thẻ và tải JS
require_once '../../footer.php';
?>