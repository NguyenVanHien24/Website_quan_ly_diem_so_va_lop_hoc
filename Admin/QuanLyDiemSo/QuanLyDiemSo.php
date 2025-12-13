<?php
session_start();
require_once '../../config.php';
require_once '../../csdl/db.php';

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

$limit = 10; // Số dòng (Học sinh - Môn học) mỗi trang
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// 1. Xây dựng điều kiện lọc (WHERE)
$whereClause = "WHERE 1=1";
if (!empty($selectedClass)) {
    $whereClause .= " AND l.maLop = '" . $conn->real_escape_string($selectedClass) . "'";
}
if (!empty($selectedSubject)) {
    $whereClause .= " AND m.maMon = '" . $conn->real_escape_string($selectedSubject) . "'";
}

// 2. Đếm tổng số dòng (Học sinh x Môn học) để tính số trang
$countSql = "
    SELECT COUNT(*) as total
    FROM hocsinh hs
    JOIN user u ON hs.userId = u.userId
    LEFT JOIN lophoc l ON hs.maLopHienTai = l.maLop
    CROSS JOIN monhoc m
    $whereClause
";
$resCount = $conn->query($countSql);
$totalRecords = $resCount->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// 3. Truy vấn dữ liệu chính (Sử dụng Subquery để phân trang đúng)
// Bước 3a: Lấy danh sách 10 cặp (Học sinh - Môn học) cho trang hiện tại
// Bước 3b: JOIN với bảng diemso để lấy chi tiết điểm
$scoreQuery = "
    SELECT 
        T.*,
        d.loaiDiem,
        d.giaTriDiem,
        d.hocKy
    FROM (
        SELECT 
            hs.maHS,
            u.hoVaTen,
            l.tenLop,
            l.maLop,
            m.maMon,
            m.tenMon
        FROM hocsinh hs
        JOIN user u ON hs.userId = u.userId
        LEFT JOIN lophoc l ON hs.maLopHienTai = l.maLop
        CROSS JOIN monhoc m
        $whereClause
        ORDER BY l.tenLop ASC, u.hoVaTen ASC, m.tenMon ASC
        LIMIT $limit OFFSET $offset
    ) AS T
    LEFT JOIN diemso d 
        ON d.maHS = T.maHS 
        AND d.maMonHoc = T.maMon 
        AND d.namHoc = '$currentYear'
";

$scoreRs = $conn->query($scoreQuery);
$scores = [];
function mapLoaiDiem($loai)
{
    if ($loai === null || $loai === '') return '';

    $s = mb_strtolower(trim((string)$loai), 'UTF-8');
    if ($s === '') return '';

    // Check for "Điểm miệng", "miệng", etc.
    if (mb_strpos($s, 'miệng') !== false || mb_strpos($s, 'mieng') !== false) {
        return 'mouth';
    }
    // Check for "Điểm 1 tiết"
    if (
        mb_strpos($s, '1 tiết') !== false || mb_strpos($s, '1 tiet') !== false ||
        mb_strpos($s, '1tiết') !== false || mb_strpos($s, '1tiet') !== false
    ) {
        return '45m';
    }
    // Check for "Điểm giữa kỳ", "giữa kỳ", "gk", etc.
    if (
        mb_strpos($s, 'giữa') !== false || mb_strpos($s, 'giua') !== false ||
        mb_strpos($s, 'gk') !== false
    ) {
        return 'gk';
    }
    // Check for "Điểm cuối kỳ", "cuối kỳ", "ck", etc.
    if (
        mb_strpos($s, 'cuối') !== false || mb_strpos($s, 'cuoi') !== false ||
        mb_strpos($s, 'ck') !== false
    ) {
        return 'ck';
    }
    // Return empty string for unmapped types
    return '';
}

if ($scoreRs) {
    while ($row = $scoreRs->fetch_assoc()) {
        $key = $row['maHS'] . '_' . $row['maMon'];
        if (!isset($scores[$key])) {
            $scores[$key] = [
                'maHS' => $row['maHS'],
                'hoVaTen' => $row['hoVaTen'],
                'tenMon' => $row['tenMon'],
                'maMon' => $row['maMon'],
                'tenLop' => $row['tenLop'],
                'maLop' => $row['maLop'],
                'HK1' => ['mouth' => '', '45m' => '', 'gk' => '', 'ck' => ''],
                'HK2' => ['mouth' => '', '45m' => '', 'gk' => '', 'ck' => '']
            ];
        }

        // nếu không có bản ghi diemso (d.loaiDiem là null) thì chỉ giữ cấu trúc rỗng
        if ($row['loaiDiem'] === null) {
            continue;
        }

        $sem = intval($row['hocKy']) === 2 ? 2 : 1;
        $short = mapLoaiDiem($row['loaiDiem']);
        if ($short === '') continue;
        $scores[$key]['HK' . $sem][$short] = $row['giaTriDiem'] !== null ? $row['giaTriDiem'] : '';
    }
}

$pageTitle = "Quản lý điểm số";
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
                <select class="form-select" id="class-filter" name="class" onchange="location = '?class='+this.value + '&subject=' + (document.getElementById('subject-filter')?document.getElementById('subject-filter').value:'')">
                    <option value="">-- Tất cả lớp --</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?php echo htmlspecialchars($c['maLop']); ?>" <?php if ($selectedClass == $c['maLop']) echo 'selected'; ?>><?php echo htmlspecialchars($c['tenLop']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="subject-filter" class="form-label fw-bold">Môn:</label>
                <select class="form-select" id="subject-filter" name="subject" onchange="location = '?class='+ (document.getElementById('class-filter')?document.getElementById('class-filter').value:'') + '&subject=' + this.value">
                    <option value="">-- Tất cả môn --</option>
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?php echo htmlspecialchars($s['maMon']); ?>" <?php if ($selectedSubject == $s['maMon']) echo 'selected'; ?>><?php echo htmlspecialchars($s['tenMon']); ?></option>
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
                        <th>TB Học Kì I</th>
                        <th>TB Học Kì II</th>
                        <th>Trung Bình Môn</th>
                        <th>Tác Vụ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = $offset + 1;
                    foreach ($scores as $sc):
                        // BẮT ĐẦU KHỐI TÍNH TOÁN ĐIỂM (ĐƯỢC ĐƯA LÊN TRƯỚC để các cột có thể sử dụng)

                        // Tính TB HK1 (Hệ số: Miệng x1, 45m x2, GK x2, CK x3. Tổng hệ số: 1+2+2+3 = 8)
                        $hk1_total =
                            floatval($sc['HK1']['mouth'] ?? 0) * 1 +
                            floatval($sc['HK1']['45m'] ?? 0)   * 2 +
                            floatval($sc['HK1']['gk'] ?? 0)    * 2 +
                            floatval($sc['HK1']['ck'] ?? 0)    * 3;
                        $hk1_avg = $hk1_total > 0 ? round($hk1_total / 8, 2) : null;

                        // Tính TB HK2
                        $hk2_total =
                            floatval($sc['HK2']['mouth'] ?? 0) * 1 +
                            floatval($sc['HK2']['45m'] ?? 0)   * 2 +
                            floatval($sc['HK2']['gk'] ?? 0)    * 2 +
                            floatval($sc['HK2']['ck'] ?? 0)    * 3;
                        $hk2_avg = $hk2_total > 0 ? round($hk2_total / 8, 2) : null;

                        // Tính trung bình môn cả năm
                        $avg = null;
                        if ($hk1_avg && $hk2_avg) {
                            $avg = round(($hk1_avg + $hk2_avg) / 2, 2);
                        } elseif ($hk1_avg) {
                            $avg = $hk1_avg;
                        } elseif ($hk2_avg) {
                            $avg = $hk2_avg;
                        }

                        // KẾT THÚC KHỐI TÍNH TOÁN ĐIỂM
                    ?>
                        <tr>
                            <td><input class="form-check-input" type="checkbox"></td>
                            <td><?= $i++ ?></td>
                            <td><?= $sc['maHS'] ?></td>
                            <td><?= $sc['hoVaTen'] ?></td>
                            <td><?= $sc['tenMon'] ?></td>

                            <td><?= $hk1_avg ?: '' ?></td>
                            <td><?= $hk2_avg ?: '' ?></td>

                            <td>
                                <?= $avg ?: '' ?>
                            </td>

                            <td class="action-icons">

                                <a href="#"
                                    class="btn-view"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewGradeModal"

                                    data-hs="<?= $sc['maHS'] ?>"
                                    data-ten="<?= $sc['hoVaTen'] ?>"
                                    data-mon="<?= $sc['tenMon'] ?>"

                                    data-s1-mouth="<?php echo htmlspecialchars($sc['HK1']['mouth'] ?? ''); ?>"
                                    data-s1-score-45m="<?php echo htmlspecialchars($sc['HK1']['45m'] ?? ''); ?>"
                                    data-s1-gk="<?php echo htmlspecialchars($sc['HK1']['gk'] ?? ''); ?>"
                                    data-s1-ck="<?php echo htmlspecialchars($sc['HK1']['ck'] ?? ''); ?>"

                                    data-s2-mouth="<?php echo htmlspecialchars($sc['HK2']['mouth'] ?? ''); ?>"
                                    data-s2-score-45m="<?php echo htmlspecialchars($sc['HK2']['45m'] ?? ''); ?>"
                                    data-s2-gk="<?php echo htmlspecialchars($sc['HK2']['gk'] ?? ''); ?>"
                                    data-s2-ck="<?php echo htmlspecialchars($sc['HK2']['ck'] ?? ''); ?>">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="#"
                                    class="btn-edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#gradeEntryModal"

                                    data-hs="<?= $sc['maHS'] ?>"
                                    data-ten="<?= $sc['hoVaTen'] ?>"
                                    data-mon="<?= $sc['tenMon'] ?>"
                                    data-mamon="<?= $sc['maMon'] ?>"
                                    data-malop="<?= $sc['maLop'] ?>"

                                    data-s1-mouth="<?php echo htmlspecialchars($sc['HK1']['mouth'] ?? ''); ?>"
                                    data-s1-score-45m="<?php echo htmlspecialchars($sc['HK1']['45m'] ?? ''); ?>"
                                    data-s1-gk="<?php echo htmlspecialchars($sc['HK1']['gk'] ?? ''); ?>"
                                    data-s1-ck="<?php echo htmlspecialchars($sc['HK1']['ck'] ?? ''); ?>"

                                    data-s2-mouth="<?php echo htmlspecialchars($sc['HK2']['mouth'] ?? ''); ?>"
                                    data-s2-score-45m="<?php echo htmlspecialchars($sc['HK2']['45m'] ?? ''); ?>"
                                    data-s2-gk="<?php echo htmlspecialchars($sc['HK2']['gk'] ?? ''); ?>"
                                    data-s2-ck="<?php echo htmlspecialchars($sc['HK2']['ck'] ?? ''); ?>">
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
            <?php
            $startShow = ($totalRecords > 0) ? $offset + 1 : 0;
            $endShow = min($offset + $limit, $totalRecords);

            // Giữ lại các tham số filter trên URL
            $queryParams = $_GET;
            // Hàm helper tạo link
            function buildPageLink($pageNum, $params)
            {
                $params['page'] = $pageNum;
                return '?' . http_build_query($params);
            }
            ?>
            <span>Hiển thị <?= $startShow ?>-<?= $endShow ?>/<?= $totalRecords ?> mục</span>

            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page > 1) ? buildPageLink($page - 1, $queryParams) : '#' ?>">‹</a>
                        </li>

                        <?php
                        // Hiển thị tối đa 5 nút trang để tránh quá dài
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);

                        if ($startPage > 1) echo '<li class="page-item disabled"><a class="page-link">...</a></li>';

                        for ($p = $startPage; $p <= $endPage; $p++): ?>
                            <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= buildPageLink($p, $queryParams) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor;

                        if ($endPage < $totalPages) echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                        ?>

                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page < $totalPages) ? buildPageLink($page + 1, $queryParams) : '#' ?>">›</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-3 mt-4">
        <button class="btn btn-import">Import bảng điểm</button>
        <button class="btn btn-export">Xuất bảng điểm</button>
    </div>

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
                    <form method="POST" action="update_score.php">
                        <input type="hidden" name="maHS" id="edit_maHS">
                        <input type="hidden" name="maMon" id="edit_maMon">
                        <input type="hidden" name="maLop" id="edit_maLop">
                        <div>
                            <div class="semester-title">HỌC KỲ I</div>
                            <div class="row g-4">
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM MIỆNG:</label><input type="text" class="form-control" name="s1_mouth" id="edit_s1_mouth" value=""></div>
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI GK:</label><input type="text" class="form-control" name="s1_gk" id="edit_s1_gk" value=""></div>
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM 1 TIẾT:</label><input type="text" class="form-control" name="s1_45m" id="edit_s1_45m" value=""></div>
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI CK:</label><input type="text" class="form-control" name="s1_ck" id="edit_s1_ck" value=""></div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="semester-title">HỌC KỲ II</div>
                            <div class="row g-4">
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM MIỆNG:</label><input type="text" class="form-control" name="s2_mouth" id="edit_s2_mouth" value=""></div>
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI GK:</label><input type="text" class="form-control" name="s2_gk" id="edit_s2_gk" value=""></div>
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM 1 TIẾT:</label><input type="text" class="form-control" name="s2_45m" id="edit_s2_45m" value=""></div>
                                <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI CK:</label><input type="text" class="form-control" name="s2_ck" id="edit_s2_ck" value=""></div>
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
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM 1 TIẾT:</label><input type="text" class="form-control bg-light" id="view_s1_45m" value="" readonly></div>
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI CK:</label><input type="text" class="form-control bg-light" id="view_s1_ck" value="" readonly></div>
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