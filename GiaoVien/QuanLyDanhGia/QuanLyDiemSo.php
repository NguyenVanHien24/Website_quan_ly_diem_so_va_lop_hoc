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
$currentPage = 'diem-so';
$pageCSS = ['QuanLyDiemSo.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['QuanLyDiemSo.js'];

$userID = $_SESSION['userID'];

// Lấy mã giáo viên
$stmt = $conn->prepare("SELECT maGV FROM giaovien WHERE userId = ?");
$stmt->bind_param('i', $userID);
$stmt->execute();
$res = $stmt->get_result();
$gv = $res->fetch_assoc();
$stmt->close();
$maGV = $gv ? (int)$gv['maGV'] : 0;

// Lấy danh sách lớp + môn được phân công
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

?>
<main>
    <h1 class="page-title">BẢNG ĐIỂM</h1>

    <div class="row mb-4 filter-section">
        <div class="col-md-4">
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
        <div class="col-md-4">
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
        <div class="col-md-2" style="display: none;">
            <label for="semester-filter" class="form-label fw-bold">Học kỳ:</label>
            <select class="form-select" id="semester-filter">
                <option value="1">Học kỳ I</option>
                <option value="2">Học kỳ II</option>
            </select>
        </div>
        <div class="col-md-2" style="display: none;">
            <label for="year-filter" class="form-label fw-bold">Năm học:</label>
            <input type="text" id="year-filter" class="form-control" value="2025-2026">
        </div>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table" id="score-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã HS</th>
                        <th>Họ Tên</th>
                        <th>Lớp</th>
                        <th>Môn học</th>
                        <th>Điểm HK I</th>
                        <th>Điểm HK II</th>
                        <th>Trung Bình</th>
                        <th>Tác Vụ</th>
                    </tr>
                </thead>
                <tbody id="score-tbody">
                    <!-- Dữ liệu được nạp bằng JS -->
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <span id="table-range">0 mục</span>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item"><a class="page-link" href="#">‹</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">/1</a></li>
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
                            <button type="button" class="btn btn-custom-save">LƯU</button>
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