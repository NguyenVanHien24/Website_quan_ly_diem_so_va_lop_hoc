<?php
require_once '../../config.php';
require_once '../../csdl/db.php';

// LẤY DANH SÁCH GIÁO VIÊN
$sql = "
    SELECT 
        gv.maGV,
        u.hoVaTen,
        u.email,
        u.gioiTinh,
        u.sdt,
        gv.boMon,
        gv.trinhDo,
        gv.phongBan,
        gv.trangThaiHoatDong
    FROM giaovien gv
    JOIN user u ON gv.userID = u.userID
    ORDER BY u.hoVaTen ASC
";

$result = $conn->query($sql);

if (!$result) {
    die("Lỗi SQL: " . $conn->error);
}

// Thêm giáo viên
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["mode"] === "add") {
    $hoVaTen = $_POST['t_name'];
    $email = $_POST['t_email'];
    $sdt = $_POST['t_phone'];
    $gioiTinh = $_POST['t_gender'];
    $boMon = $_POST['t_dept'];
    $trinhDo = $_POST['t_degree'] ?? '';
    $phongBan = $_POST['t_office'] ?? '';
    $trangThai = $_POST['status'] == "Hoạt động" ? "Hoạt động" : "Tạm dừng";
    $namHoc = $_POST['t_year'] ?? '';
    $kyHoc = intval($_POST['t_semester'] ?? 1);

    // Thêm vào user
    $stmt = $conn->prepare("INSERT INTO user (hoVaTen,email,sdt,gioiTinh,matKhau,vaiTro) VALUES (?,?,?,?, '12345678','GiaoVien')");
    if (!$stmt) {
        die("Lỗi prepare user: " . $conn->error);
    }
    $stmt->bind_param("ssss", $hoVaTen, $email, $sdt, $gioiTinh);
    $stmt->execute();
    $userId = $conn->insert_id;

    // Thêm vào giaovien
    $stmtGV = $conn->prepare("INSERT INTO giaovien (userId,boMon,trinhDo,phongBan,trangThaiHoatDong,namHoc,kyHoc) VALUES (?,?,?,?,?,?,?)");
    if (!$stmtGV) {
        die("Lỗi prepare giaovien: " . $conn->error);
    }
    $stmtGV->bind_param("isssisi", $userId, $boMon, $trinhDo, $phongBan, $trangThai, $namHoc, $kyHoc);
    $stmtGV->execute();

    header("Location: QuanLyGiaoVien.php");
    exit();
}

// Sửa giáo viên
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["mode"] === "edit") {
    $maGV = intval($_POST['maGV']);
    $hoVaTen = $_POST['t_name'];
    $email = $_POST['t_email'];
    $sdt = $_POST['t_phone'];
    $gioiTinh = $_POST['t_gender'];
    $boMon = $_POST['t_dept'];
    $trinhDo = $_POST['t_degree'] ?? '';
    $phongBan = $_POST['t_office'] ?? '';
    $trangThai = ($_POST['status'] === "Hoạt động") ? "Hoạt động" : "Tạm dừng";
    $namHoc = $_POST['t_year'] ?? '';
    $kyHoc = intval($_POST['t_semester'] ?? 1);

    // Lấy userId trước
    $res = $conn->query("SELECT userId FROM giaovien WHERE maGV=$maGV");
    if ($res && $res->num_rows > 0) {
        $userId = $res->fetch_assoc()['userId'];

        // Update user
        $stmt = $conn->prepare("UPDATE user SET hoVaTen=?, email=?, sdt=?, gioiTinh=? WHERE userId=?");
        if (!$stmt) {
            die("Lỗi prepare update user: " . $conn->error);
        }
        $stmt->bind_param("ssssi", $hoVaTen, $email, $sdt, $gioiTinh, $userId);
        $stmt->execute();

        // Update giaovien
        $stmtGV = $conn->prepare("UPDATE giaovien SET boMon=?, trinhDo=?, phongBan=?, trangThaiHoatDong=?, namHoc=?, kyHoc=? WHERE maGV=?");
        if (!$stmtGV) {
            die("Lỗi prepare update giaovien: " . $conn->error);
        }
        $stmtGV->bind_param("sssssii", $boMon, $trinhDo, $phongBan, $trangThai, $namHoc, $kyHoc, $maGV);
        $stmtGV->execute();
    }

    header("Location: QuanLyGiaoVien.php");
    exit();
}

// XỬ LÝ XÓA GIÁO VIÊN 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {

    $maGV = intval($_POST["delete_id"]);

    // Lấy userID theo maGV
    $q = $conn->query("SELECT userID FROM giaovien WHERE maGV=$maGV");
    if ($q && $q->num_rows > 0) {
        $userID = $q->fetch_assoc()["userID"];

        // Xóa bản ghi giáo viên
        $conn->query("DELETE FROM giaovien WHERE maGV=$maGV");

        // Xóa user liên quan
        $conn->query("DELETE FROM user WHERE userID=$userID");
    }

    echo "<script>location.href='QuanLyGiaoVien.php'</script>";
    exit();
}

$currentPage = 'giao-vien';
$pageCSS = ['QuanLyGiaoVien.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['QuanLyGiaoVien.js'];
?>
<main>
    <div class="main-header">
        <h1 class="page-title">QUẢN LÝ GIÁO VIÊN</h1>
        <button class="btn btn-primary btn-add" data-bs-toggle="modal" data-bs-target="#teacherFormModal">
            <i class="bi bi-plus-lg me-2"></i>Thêm Giáo Viên
        </button>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><input class="form-check-input" type="checkbox"></th>
                        <th>STT</th>
                        <th>Mã Giáo viên</th>
                        <th>Họ Tên</th>
                        <th>Giới tính</th>
                        <th>Email</th>
                        <th>Bộ môn</th>
                        <th>Trạng thái</th>
                        <th>Tác vụ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0):
                        $stt = 1;
                        while ($row = $result->fetch_assoc()):
                    ?>
                            <tr>
                                <td><input class="form-check-input" type="checkbox"></td>
                                <td><?= $stt++ ?></td>
                                <td><?= $row['maGV'] ?></td>
                                <td><?= $row['hoVaTen'] ?></td>
                                <td><?= $row['gioiTinh'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td><?= $row['boMon'] ?></td>
                                <td>
                                    <?php if ($row['trangThaiHoatDong'] == 'Hoạt động'): ?>
                                        <span class="badge-active">● Active</span>
                                    <?php else: ?>
                                        <span class="badge-inactive">● Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-icons">
                                    <a href="#"
                                        class="btn-edit"
                                        data-id="<?= $row['maGV'] ?>"
                                        data-name="<?= $row['hoVaTen'] ?>"
                                        data-email="<?= $row['email'] ?>"
                                        data-phone="<?= $row['sdt'] ?>"
                                        data-gender="<?= $row['gioiTinh'] ?>"
                                        data-dept="<?= $row['boMon'] ?>"
                                        data-office="<?= $row['phongBan'] ?>"
                                        data-degree="<?= $row['trinhDo'] ?>"
                                        data-status="<?= $row['trangThaiHoatDong'] ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#teacherFormModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <a href="#" class="btn-delete"
                                        data-id="<?= $row['maGV'] ?>"
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
                            <td colspan="9" class="text-center text-muted py-3">
                                Không có giáo viên nào trong hệ thống.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span>1-4/18 mục</span>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item"><a class="page-link" href="#">‹</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">...</a></li>
                    <li class="page-item"><a class="page-link" href="#">5</a></li>
                    <li class="page-item"><a class="page-link" href="#">›</a></li>
                </ul>
            </nav>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-4">
        <button class="btn btn-danger fw-bold px-4 py-2">Xóa giáo viên</button>
    </div>

    <!-- THÊM GIÁO VIÊN -->
    <div class="modal fade" id="teacherFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title fw-bold" id="modalTitle">THÊM GIÁO VIÊN</h2>
                </div>
                <div class="modal-body pt-4">
                    <form id="teacherForm" method="POST">
                        <input type="hidden" name="mode" id="mode" value="add">
                        <input type="hidden" name="maGV" id="maGV">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Năm học:</label>
                                <select class="form-select" id="t_year" name="t_year">
                                    <option value="2024-2025">2024-2025</option>
                                    <option value="2024-2025">2025-2026</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Học kỳ:</label>
                                <select class="form-select" id="t_semester" name="t_semester">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bộ môn:</label>
                                <select class="form-select" id="t_dept" name="t_dept">
                                    <option value="">-- Chọn bộ môn --</option>
                                    <?php
                                    // Lấy danh sách bộ môn từ CSDL
                                    $sqlBM = "SELECT DISTINCT boMon FROM giaovien ORDER BY boMon ASC";
                                    $resBM = $conn->query($sqlBM);
                                    if ($resBM && $resBM->num_rows > 0):
                                        while ($bm = $resBM->fetch_assoc()):
                                    ?>
                                            <option value="<?= htmlspecialchars($bm['boMon']) ?>"><?= htmlspecialchars($bm['boMon']) ?></option>
                                    <?php
                                        endwhile;
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và Tên*:</label>
                                <input type="text" class="form-control" id="t_name" name="t_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email*:</label>
                                <input type="email" class="form-control" id="t_email" name="t_email">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Số Điện Thoại*:</label>
                                <input type="text" class="form-control" id="t_phone" name="t_phone">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Giới tính*:</label>
                                <select class="form-select" id="t_gender" name="t_gender">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Trình độ:</label>
                                <input type="text" class="form-control" id="t_degree" name="t_degree">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Phòng ban:</label>
                                <input type="text" class="form-control" id="t_office" name="t_office">
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <div class="d-flex align-items-center gap-4 mb-2">
                                    <label class="me-3">Trạng thái*:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusActive" value="Hoạt động" checked>
                                        <label class="form-check-label" for="statusActive">Đang hoạt động</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusInactive" value="Tạm dừng">
                                        <label class="form-check-label" for="statusInactive">Tạm dừng</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary px-4" id="btnSaveTeacher">+ Thêm mới</button>
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
                            <h5 class="fw-bold text-dark m-0" id="deleteMsg">Bạn chắc chắn muốn xóa giáo viên này?</h5>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="delete_id" id="delete_id">

                            <button type="button" class="btn btn-outline-dark px-4 fw-bold" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger px-4 fw-bold">Xóa</button>
                        </form>
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