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

$errors = [];

// Thêm/sửa giáo viên
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["mode"])) {
    $mode = $_POST["mode"];
    $errors = [];

    $hoVaTen = trim($_POST['t_name'] ?? '');
    $email = trim($_POST['t_email'] ?? '');
    $sdt = trim($_POST['t_phone'] ?? '');
    $gioiTinh = $_POST['t_gender'] ?? '';
    $boMon = $_POST['t_dept'] ?? '';
    $trinhDo = $_POST['t_degree'] ?? '';
    $phongBan = $_POST['t_office'] ?? '';
    $trangThai = ($_POST['status'] ?? '') === "Hoạt động" ? "Hoạt động" : "Tạm dừng";
    $namHoc = $_POST['t_year'] ?? '';
    $kyHoc = intval($_POST['t_semester'] ?? 1);

    if (!$hoVaTen) $errors[] = "Họ và Tên không được để trống.";
    if (!$email) $errors[] = "Email không được để trống.";
    if (!$sdt) $errors[] = "Số điện thoại không được để trống.";

    if ($mode === "add") {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email không hợp lệ.";
        }

        // Validate phone format: Vietnamese numbers like 0xxxxxxxxx or +84xxxxxxxxx
        if (!preg_match('/^(\+84|0)\d{9}$/', $sdt)) {
            $errors[] = "Số điện thoại không hợp lệ. (Ví dụ: 0987654321 hoặc +84987654321)";
        }

        // Kiểm tra trùng số điện thoại
        $check = $conn->prepare("SELECT userId FROM user WHERE sdt = ?");
        $check->bind_param("s", $sdt);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) $errors[] = "Số điện thoại đã tồn tại.";
        $check->close();

        // Kiểm tra trùng email
        $check2 = $conn->prepare("SELECT userId FROM user WHERE email = ?");
        $check2->bind_param("s", $email);
        $check2->execute();
        $check2->store_result();
        if ($check2->num_rows > 0) $errors[] = "Email đã tồn tại.";
        $check2->close();

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => implode("\n", $errors)]);
            exit();
        }

        // Tạo user (trigger có thể tự sinh row trong `giaovien`)
        $stmt = $conn->prepare("INSERT INTO user (hoVaTen,email,sdt,gioiTinh,matKhau,vaiTro) VALUES (?,?,?,?, '12345678','GiaoVien')");
        $stmt->bind_param("ssss", $hoVaTen, $email, $sdt, $gioiTinh);
        $stmt->execute();
        $userId = $conn->insert_id;

        // Cập nhật thông tin giaovien đã sinh bởi trigger (nếu trigger tồn tại),
        // nếu không có bản ghi (trigger bị tắt) sẽ chèn mới.
        $stmtGV = $conn->prepare("UPDATE giaovien SET boMon=?, trinhDo=?, phongBan=?, trangThaiHoatDong=?, namHoc=?, kyHoc=? WHERE userId=?");
        if ($stmtGV) {
            $stmtGV->bind_param("sssssii", $boMon, $trinhDo, $phongBan, $trangThai, $namHoc, $kyHoc, $userId);
            $stmtGV->execute();
        }

        // Nếu update không ảnh hưởng (không có bản ghi), chèn mới
        if ($stmtGV && $stmtGV->affected_rows === 0) {
            $insGV = $conn->prepare("INSERT INTO giaovien (userId,boMon,trinhDo,phongBan,trangThaiHoatDong,namHoc,kyHoc) VALUES (?,?,?,?,?,?,?)");
            if ($insGV) {
                $insGV->bind_param("isssssi", $userId, $boMon, $trinhDo, $phongBan, $trangThai, $namHoc, $kyHoc);
                $insGV->execute();
                $insGV->close();
            }
        }

        // Lấy mã giáo viên (có thể do trigger hoặc insert trả về)
        $qmgv = $conn->prepare("SELECT maGV FROM giaovien WHERE userId = ? LIMIT 1");
        $maGV = 0;
        if ($qmgv) {
            $qmgv->bind_param("i", $userId);
            $qmgv->execute();
            $qmgv->bind_result($maGV);
            $qmgv->fetch();
            $qmgv->close();
        }

        // Đồng bộ phân công môn: xóa cũ (nếu có) -> thêm mới
        if ($maGV) {
            $del = $conn->prepare("DELETE FROM giaovien_monhoc WHERE idGV = ?");
            if ($del) {
                $del->bind_param("i", $maGV);
                $del->execute();
                $del->close();
            }

            if (!empty($boMon)) {
                $p = $conn->prepare("SELECT maMon FROM monhoc WHERE tenMon = ? AND namHoc = ? AND hocKy = ?");
                if ($p) {
                    $p->bind_param("ssi", $boMon, $namHoc, $kyHoc);
                    $p->execute();
                    $p->bind_result($maMon);
                    $found = false;
                    while ($p->fetch()) {
                        $found = true;
                        $ins = $conn->prepare("INSERT INTO giaovien_monhoc (idGV, idMon) VALUES (?, ?)");
                        if ($ins) {
                            $ins->bind_param("ii", $maGV, $maMon);
                            $ins->execute();
                            $ins->close();
                        }
                    }
                    $p->close();

                    if (!$found) {
                        $p2 = $conn->prepare("SELECT maMon FROM monhoc WHERE tenMon = ? LIMIT 1");
                        if ($p2) {
                            $p2->bind_param("s", $boMon);
                            $p2->execute();
                            $p2->bind_result($maMon);
                            if ($p2->fetch()) {
                                $ins2 = $conn->prepare("INSERT INTO giaovien_monhoc (idGV, idMon) VALUES (?, ?)");
                                if ($ins2) {
                                    $ins2->bind_param("ii", $maGV, $maMon);
                                    $ins2->execute();
                                    $ins2->close();
                                }
                            }
                            $p2->close();
                        }
                    }
                }
            }
        }

        echo json_encode(['success' => true]);
        exit();
    }

    if ($mode === "edit") {
        $maGV = intval($_POST['maGV'] ?? 0);
        $res = $conn->query("SELECT userId FROM giaovien WHERE maGV=$maGV");
        $userId = $res->fetch_assoc()['userId'] ?? 0;

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email không hợp lệ.";
        }

        // Validate phone format
        if (!preg_match('/^(\+84|0)\d{9}$/', $sdt)) {
            $errors[] = "Số điện thoại không hợp lệ. (Ví dụ: 0987654321 hoặc +84987654321)";
        }

        // Kiểm tra trùng số điện thoại (ngoại trừ user hiện tại)
        $check = $conn->prepare("SELECT userId FROM user WHERE sdt = ? AND userId != ?");
        $check->bind_param("si", $sdt, $userId);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) $errors[] = "Số điện thoại đã tồn tại.";
        $check->close();

        // Kiểm tra trùng email (ngoại trừ user hiện tại)
        $check2 = $conn->prepare("SELECT userId FROM user WHERE email = ? AND userId != ?");
        $check2->bind_param("si", $email, $userId);
        $check2->execute();
        $check2->store_result();
        if ($check2->num_rows > 0) $errors[] = "Email đã tồn tại.";
        $check2->close();

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => implode("\n", $errors)]);
            exit();
        }

        $stmt = $conn->prepare("UPDATE user SET hoVaTen=?, email=?, sdt=?, gioiTinh=? WHERE userId=?");
        $stmt->bind_param("ssssi", $hoVaTen, $email, $sdt, $gioiTinh, $userId);
        $stmt->execute();

        $stmtGV = $conn->prepare("UPDATE giaovien SET boMon=?, trinhDo=?, phongBan=?, trangThaiHoatDong=?, namHoc=?, kyHoc=? WHERE maGV=?");
        $stmtGV->bind_param("sssssii", $boMon, $trinhDo, $phongBan, $trangThai, $namHoc, $kyHoc, $maGV);
        $stmtGV->execute();

        // Đồng bộ lại phân công môn trong giaovien_monhoc: xóa cũ -> thêm mới
        $del = $conn->prepare("DELETE FROM giaovien_monhoc WHERE idGV = ?");
        if ($del) {
            $del->bind_param("i", $maGV);
            $del->execute();
            $del->close();
        }

        if (!empty($boMon)) {
            $p = $conn->prepare("SELECT maMon FROM monhoc WHERE tenMon = ? AND namHoc = ? AND hocKy = ?");
            if ($p) {
                $p->bind_param("ssi", $boMon, $namHoc, $kyHoc);
                $p->execute();
                $p->bind_result($maMon);
                $found = false;
                while ($p->fetch()) {
                    $found = true;
                    $ins = $conn->prepare("INSERT INTO giaovien_monhoc (idGV, idMon) VALUES (?, ?)");
                    if ($ins) {
                        $ins->bind_param("ii", $maGV, $maMon);
                        $ins->execute();
                        $ins->close();
                    }
                }
                $p->close();

                if (!$found) {
                    $p2 = $conn->prepare("SELECT maMon FROM monhoc WHERE tenMon = ? LIMIT 1");
                    if ($p2) {
                        $p2->bind_param("s", $boMon);
                        $p2->execute();
                        $p2->bind_result($maMon);
                        if ($p2->fetch()) {
                            $ins2 = $conn->prepare("INSERT INTO giaovien_monhoc (idGV, idMon) VALUES (?, ?)");
                            if ($ins2) {
                                $ins2->bind_param("ii", $maGV, $maMon);
                                $ins2->execute();
                                $ins2->close();
                            }
                        }
                        $p2->close();
                    }
                }
            }
        }

        echo json_encode(['success' => true]);
        exit();
    }
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
                                    $sqlBM = "SELECT DISTINCT tenMon FROM monhoc ORDER BY tenMon ASC";
                                    $resBM = $conn->query($sqlBM);
                                    if ($resBM && $resBM->num_rows > 0):
                                        while ($bm = $resBM->fetch_assoc()):
                                            echo "<option value='" . htmlspecialchars($bm['tenMon']) . "'>" . htmlspecialchars($bm['tenMon']) . "</option>";
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
<script>
    document.getElementById('teacherForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        fetch('<?= basename(__FILE__) ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Cập nhật giáo viên thành công!");
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(err => alert('Lỗi server'));
    });
</script>

<?php
// Yêu cầu file footer.php để đóng các thẻ và tải JS
require_once '../../footer.php';
?>