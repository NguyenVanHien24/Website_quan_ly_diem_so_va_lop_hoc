<?php
session_start();
require_once '../../config.php';
require_once '../../csdl/db.php';
if (!isset($_SESSION["userID"])) {
    header("Location: ../../dangnhap.php");
    exit();
}

$limit = 10; // Số lượng học sinh mỗi trang
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// 1. Đếm tổng số học sinh
$sqlCount = "SELECT COUNT(*) as total FROM hocsinh hs JOIN user u ON hs.userId = u.userId";
$resCount = $conn->query($sqlCount);
$totalRecords = $resCount->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// 2. Lấy danh sách học sinh theo trang (Thêm LIMIT và OFFSET)
$sql = "
    SELECT 
        hs.maHS,
        u.hoVaTen,
        u.email,
        u.sdt,
        u.gioiTinh,
        l.tenLop,
        hs.chucVu,
        hs.trangThaiHoatDong
    FROM hocsinh hs
    JOIN user u ON hs.userId = u.userId
    LEFT JOIN lophoc l ON hs.maLopHienTai = l.maLop
    ORDER BY u.hoVaTen
    LIMIT $limit OFFSET $offset
";

$result = $conn->query($sql);
// Lấy năm học & học kỳ hiện tại
$yearNow = date('Y');
$monthNow = date('n');

// Xác định học kỳ (ví dụ: tháng 1-6 -> HK2, 7-12 -> HK1)
if ($monthNow >= 1 && $monthNow <= 6) {
    $currentSemester = 2;
    $currentYear = ($yearNow - 1) . '-' . $yearNow;
} else {
    $currentSemester = 1;
    $currentYear = $yearNow . '-' . ($yearNow + 1);
}

// Lấy mã học sinh tự động: max(maHS)+1
$maHSResult = $conn->query("SELECT IFNULL(MAX(maHS),0)+1 AS nextMaHS FROM hocsinh");
$nextMaHS = $maHSResult->fetch_assoc()['nextMaHS'];

// Function tạo diemso cho học sinh trong lớp
function createDiemsoForStudent($conn, $maHS, $maLop, $namHoc, $kyHoc)
{
    if (!$maLop) return; // Không tạo nếu không có lớp

    // Lấy danh sách môn học của lớp trong năm học/kỳ hiện tại
    $lopMonRs = $conn->query("SELECT maMon FROM lop_monhoc WHERE maLop = $maLop AND namHoc = '$namHoc' AND hocKy = $kyHoc");

    if (!$lopMonRs || $lopMonRs->num_rows === 0) return;

    // 3 loại điểm
    $loaiDiem = ['Điểm miệng', 'Điểm 15 phút', 'Điểm 1 tiết'];

    while ($lopMon = $lopMonRs->fetch_assoc()) {
        $maMon = $lopMon['maMon'];

        // Kiểm tra xem đã tồn tại chưa để tránh trùng lặp
        $checkRs = $conn->query("SELECT COUNT(*) as cnt FROM diemso WHERE maHS = $maHS AND maMonHoc = $maMon AND namHoc = '$namHoc' AND hocKy = $kyHoc");
        $checkRow = $checkRs->fetch_assoc();

        // Chỉ tạo nếu chưa có bản ghi nào cho môn học này
        if ($checkRow['cnt'] == 0) {
            foreach ($loaiDiem as $loai) {
                $insertDiemSql = "INSERT INTO diemso (maHS, maMonHoc, maLop, loaiDiem, namHoc, hocKy) VALUES ($maHS, $maMon, $maLop, '$loai', '$namHoc', $kyHoc)";
                if (!$conn->query($insertDiemSql)) {
                    error_log("Error inserting diemso: " . $conn->error);
                }
            }
        }
    }
}

// ------------------ XỬ LÝ AJAX THÊM/SỬA/XÓA ------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $response = ['success' => false, 'error' => ''];

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $className = $_POST['class'] ?? '';
    $role = $_POST['role'] ?? 'Thành viên';
    $status = $_POST['status'] ?? 'active';

    if (!$name) {
        // Xử lý xóa trước (không cần validation)
        if ($action === 'delete') {
            $maHS = $_POST['id'] ?? 0;
            $conn->begin_transaction();
            try {
                // Lấy userId trước khi xóa hocsinh
                $rs = $conn->query("SELECT userId FROM hocsinh WHERE maHS=$maHS");
                if (!$rs || $rs->num_rows === 0) {
                    throw new Exception('Học sinh không tồn tại');
                }
                $userId = $rs->fetch_assoc()['userId'];

                // Xóa chuyên cần liên quan (nếu FK chưa có CASCADE)
                if (!$conn->query("DELETE FROM chuyencan WHERE maHS=$maHS")) {
                    throw new Exception('Lỗi xóa chuyên cần: ' . $conn->error);
                }

                // Xóa hocsinh (cascade sẽ xóa các bản ghi liên quan: diemso, bainop)
                if (!$conn->query("DELETE FROM hocsinh WHERE maHS=$maHS")) {
                    throw new Exception('Lỗi xóa hocsinh: ' . $conn->error);
                }

                // Xóa user liên quan
                if (!$conn->query("DELETE FROM user WHERE userId=$userId")) {
                    throw new Exception('Lỗi xóa user: ' . $conn->error);
                }

                $conn->commit();
                $response['success'] = true;
            } catch (Exception $e) {
                $conn->rollback();
                $response['error'] = $e->getMessage();
            }
            echo json_encode($response);
            exit();
        }
    }

    // Validation cho add/update (không cần cho delete)
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $className = $_POST['class'] ?? '';
    $role = $_POST['role'] ?? 'Thành viên';
    $status = $_POST['status'] ?? 'active';

    if (!$name) {
        $response['error'] = 'Họ tên không được để trống';
    } elseif (!$email) {
        $response['error'] = 'Email không được để trống';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['error'] = 'Email không hợp lệ';
    } elseif (!$phone) {
        $response['error'] = 'Số điện thoại không được để trống';
    } elseif (!preg_match('/^0[0-9]{9,10}$/', $phone)) {
        $response['error'] = 'Số điện thoại không hợp lệ';
    } elseif ($gender !== 'Nam' && $gender !== 'Nữ') {
        $response['error'] = 'Giới tính không hợp lệ';
    } elseif (!in_array($role, ['Thành viên', 'Lớp trưởng', 'Bí thư'])) {
        $response['error'] = 'Chức vụ không hợp lệ';
    } elseif (!in_array($status, ['active', 'inactive'])) {
        $response['error'] = 'Trạng thái không hợp lệ';
    }

    // Kiểm tra trùng số điện thoại
    if (!$response['error']) {
        $checkPhoneQuery = $conn->query("SELECT userId FROM user WHERE sdt = '$phone'");
        if ($action === 'update') {
            $maHS = $_POST['id'] ?? 0;
            $rs = $conn->query("SELECT userId FROM hocsinh WHERE maHS=$maHS");
            if ($rs->num_rows > 0) {
                $userId = $rs->fetch_assoc()['userId'];
                $checkPhoneQuery = $conn->query("SELECT userId FROM user WHERE sdt = '$phone' AND userId <> $userId");
            }
        }

        if ($checkPhoneQuery && $checkPhoneQuery->num_rows > 0) {
            $response['error'] = 'Số điện thoại đã tồn tại';
        }
    }

    if ($response['error']) {
        echo json_encode($response);
        exit();
    }

    if ($action === 'add') {
        $conn->begin_transaction();
        try {
            // Thêm user
            if (!$conn->query("INSERT INTO user (hoVaTen,email,sdt,gioiTinh,matKhau,vaiTro)
                VALUES ('$name','$email','$phone','$gender','12345678','HocSinh')")) {
                throw new Exception($conn->error);
            }
            $userId = $conn->insert_id;

            $maLop = null;
            if ($className) {
                $rs = $conn->query("SELECT maLop FROM lophoc WHERE tenLop = '$className' LIMIT 1");
                if ($rs && $rs->num_rows > 0) {
                    $maLop = $rs->fetch_assoc()['maLop'];
                } else {
                    throw new Exception("Lớp '$className' không tồn tại trong CSDL");
                }
            }

            $yearNow = date('Y');
            $monthNow = date('n');
            $currentSemester = ($monthNow >= 1 && $monthNow <= 6) ? 2 : 1;
            $currentYear = ($monthNow >= 1 && $monthNow <= 6) ? ($yearNow - 1) . '-' . $yearNow : $yearNow . '-' . ($yearNow + 1);

            $statusDb = $status === 'active' ? 'Hoạt động' : 'Inactive';

            // Cập nhật hocsinh
            $updateHocsinhSql = "UPDATE hocsinh SET maLopHienTai = " . ($maLop ?: "NULL") . ",
                trangThaiHoatDong = '$statusDb', namHoc = '$currentYear', kyHoc = $currentSemester,
                chucVu = '$role' WHERE userId = $userId";
            if (!$conn->query($updateHocsinhSql)) {
                throw new Exception('Lỗi khi cập nhật hocsinh: ' . $conn->error . ' | SQL: ' . $updateHocsinhSql);
            }

            // Lấy maHS vừa tạo
            $hsRs = $conn->query("SELECT maHS FROM hocsinh WHERE userId = $userId");
            $maHS = $hsRs->fetch_assoc()['maHS'];

            // Tạo diemso cho học sinh nếu có lớp
            if ($maLop) {
                createDiemsoForStudent($conn, $maHS, $maLop, $currentYear, $currentSemester);
            }

            // Cập nhật sĩ số lớp
            if ($maLop) {
                $conn->query("UPDATE lophoc SET siSo = siSo + 1 WHERE maLop = $maLop");
            }

            $conn->commit();
            $response['success'] = true;
        } catch (Exception $e) {
            $conn->rollback();
            $response['error'] = $e->getMessage();
        }

        echo json_encode($response);
        exit();
    }

    if ($action === 'update') {
        $maHS = $_POST['id'] ?? 0;
        $className = $_POST['class'] ?? '';
        $role = $_POST['role'] ?? 'Thành viên';
        $status = $_POST['status'] ?? 'active';

        // Lấy lớp cũ trước khi update
        $oldClassRs = $conn->query("SELECT maLopHienTai FROM hocsinh WHERE maHS = $maHS");
        $oldClass = $oldClassRs->fetch_assoc()['maLopHienTai'];

        // Lấy userId
        $rs = $conn->query("SELECT userId FROM hocsinh WHERE maHS=$maHS");
        $userId = $rs->fetch_assoc()['userId'];

        $conn->begin_transaction();
        try {
            // Lấy maLop từ tên lớp
            $maLop = null;
            if (!empty($className)) {
                $rs2 = $conn->query("SELECT maLop FROM lophoc WHERE tenLop = '" . $conn->real_escape_string($className) . "' LIMIT 1");
                if ($rs2 && $rs2->num_rows > 0) {
                    $maLop = $rs2->fetch_assoc()['maLop'];
                } else {
                    throw new Exception("Lớp '$className' không tồn tại trong CSDL");
                }
            }

            $statusDb = $status === 'active' ? 'Hoạt động' : 'Inactive';

            // UPDATE hocsinh
            $sqlUpdateHS = "
            UPDATE hocsinh 
            SET maLopHienTai = " . ($maLop !== null ? $maLop : "NULL") . ",
                chucVu='$role', trangThaiHoatDong='$statusDb'
            WHERE maHS=$maHS
        ";
            if (!$conn->query($sqlUpdateHS)) {
                throw new Exception('Lỗi cập nhật hocsinh: ' . $conn->error);
            }

            // Cập nhật sĩ số lớp
            if ($oldClass != $maLop) {
                if ($oldClass) $conn->query("UPDATE lophoc SET siSo = siSo - 1 WHERE maLop = $oldClass");
                if ($maLop) $conn->query("UPDATE lophoc SET siSo = siSo + 1 WHERE maLop = $maLop");
            }

            // Nếu thay đổi lớp, tạo diemso cho lớp mới
            if ($oldClass != $maLop && $maLop) {
                $yearNow = date('Y');
                $monthNow = date('n');
                $currentSemester = ($monthNow >= 1 && $monthNow <= 6) ? 2 : 1;
                $currentYear = ($monthNow >= 1 && $monthNow <= 6) ? ($yearNow - 1) . '-' . $yearNow : $yearNow . '-' . ($yearNow + 1);
                createDiemsoForStudent($conn, $maHS, $maLop, $currentYear, $currentSemester);
            }

            $conn->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }

    if ($action === 'delete') {
        $maHS = $_POST['id'] ?? 0;
        $conn->begin_transaction();
        try {
            // Lấy userId trước khi xóa hocsinh
            $rs = $conn->query("SELECT userId, maLopHienTai FROM hocsinh WHERE maHS=$maHS");
            $userId = $rs->fetch_assoc()['userId'];
            $oldClass = $rs->fetch_assoc()['maLopHienTai'];
            if (!$rs || $rs->num_rows === 0) {
                throw new Exception('Học sinh không tồn tại');
            }
            $userId = $rs->fetch_assoc()['userId'];

            // Xóa chuyên cần liên quan (nếu FK chưa có CASCADE)
            if (!$conn->query("DELETE FROM chuyencan WHERE maHS=$maHS")) {
                throw new Exception('Lỗi xóa chuyên cần: ' . $conn->error);
            }

            // Xóa hocsinh (cascade sẽ xóa các bản ghi liên quan: diemso, bainop)
            if (!$conn->query("DELETE FROM hocsinh WHERE maHS=$maHS")) {
                throw new Exception('Lỗi xóa hocsinh: ' . $conn->error);
            }

            // Xóa user liên quan
            if (!$conn->query("DELETE FROM user WHERE userId=$userId")) {
                throw new Exception('Lỗi xóa user: ' . $conn->error);
            }
            if ($oldClass) {
                $conn->query("UPDATE lophoc SET siSo = siSo - 1 WHERE maLop = $oldClass");
            }

            $conn->commit();
            $response['success'] = true;
        } catch (Exception $e) {
            $conn->rollback();
            $response['error'] = $e->getMessage();
        }
        echo json_encode($response);
        exit();
    }
}
$currentPage = 'hoc-sinh';
$pageCSS = ['QuanLyHocSinh.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['QuanLyHocSinh.js'];
?>
<main>
    <div class="main-header d-flex justify-content-between align-items-end mb-4">
        <div>
            <h1 class="page-title">QUẢN LÝ HỌC SINH</h1>

            <div class="mt-3 d-flex align-items-center">
                <label for="classFilterHeader" class="fw-bold me-3 fs-5">Lớp:</label>
                <select class="form-select py-2 px-3" id="classFilterHeader" style="width: 300px;">
                    <option value="">-- Tất cả lớp --</option>
                    <option value="Chưa có lớp">Chưa có lớp</option>

                    <?php
                    $lopRs = $conn->query("SELECT maLop, tenLop FROM lophoc ORDER BY tenLop");
                    while ($lop = $lopRs->fetch_assoc()):
                    ?>
                        <option value="<?= $lop['tenLop'] ?>"><?= $lop['tenLop'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <button class="btn btn-primary btn-add" data-bs-toggle="modal" data-bs-target="#studentFormModal">
            <i class="bi bi-plus-lg me-2"></i>Thêm Học Sinh
        </button>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><input class="form-check-input" type="checkbox"></th>
                        <th>STT</th>
                        <th>Mã Học Sinh</th>
                        <th>Họ Tên</th>
                        <th>Lớp</th>
                        <th>Chức vụ</th>
                        <th>Trạng thái</th>
                        <th>Tác vụ</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    if ($result->num_rows > 0):
                        $stt = $offset + 1;
                        while ($row = $result->fetch_assoc()):

                            $tenLop = $row['tenLop'] ?: 'Chưa có lớp';
                            $chucVu = $row['chucVu'] ?: 'Thành viên';
                            $status = $row['trangThaiHoatDong'] ?: 'Inactive';
                    ?>
                            <tr>
                                <td><input class="form-check-input" type="checkbox"></td>
                                <td><?= $stt++ ?></td>
                                <td><?= $row['maHS'] ?></td>
                                <td><?= $row['hoVaTen'] ?></td>
                                <td><?= $tenLop ?></td>
                                <td><?= $chucVu ?></td>

                                <td>
                                    <?php if ($status === 'Hoạt động'): ?>
                                        <span class="badge-active">● Active</span>
                                    <?php else: ?>
                                        <span class="badge-inactive">● Inactive</span>
                                    <?php endif; ?>
                                </td>

                                <td class="action-icons">
                                    <a href="#"
                                        class="btn-edit"
                                        data-id="<?= $row['maHS'] ?>"
                                        data-name="<?= htmlspecialchars($row['hoVaTen']) ?>"
                                        data-email="<?= htmlspecialchars($row['email']) ?>"
                                        data-phone="<?= htmlspecialchars($row['sdt']) ?>"
                                        data-gender="<?= $row['gioiTinh'] ?>"
                                        data-class="<?= $tenLop ?>"
                                        data-role="<?= $chucVu ?>"
                                        data-status="<?= $status ?>"
                                        data-bs-toggle="modal" data-bs-target="#studentFormModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <a href="#" class="btn-delete"
                                        data-id="<?= $row['maHS'] ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmModal">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-3 text-muted">Không có học sinh nào.</td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <?php
            $startShow = ($totalRecords > 0) ? $offset + 1 : 0;
            $endShow = min($offset + $limit, $totalRecords);
            ?>
            <span>Hiển thị <?= $startShow ?>-<?= $endShow ?>/<?= $totalRecords ?> mục</span>

            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page > 1) ? "?page=" . ($page - 1) : '#' ?>">‹</a>
                        </li>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page < $totalPages) ? "?page=" . ($page + 1) : '#' ?>">›</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-4">
        <button class="btn btn-danger fw-bold px-4 py-2">Xóa học sinh</button>
    </div>
    <!-- THÊM HỌC SINH -->
    <div class="modal fade" id="studentFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title fw-bold" id="modalTitle">THÊM HỌC SINH</h2>
                </div>
                <div class="modal-body pt-4">
                    <form id="studentForm" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Năm học:</label>
                                <input type="text" class="form-control" id="s_year" value="<?= $currentYear ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Học kỳ:</label>
                                <input type="text" class="form-control" id="s_semester" value="<?= $currentSemester ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mã Học sinh:</label>
                                <input type="text" class="form-control" id="s_id" value="<?= $nextMaHS ?>" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và Tên:</label>
                                <input type="text" class="form-control" id="s_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email:</label>
                                <input type="email" class="form-control" id="s_email" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Số Điện Thoại:</label>
                                <input type="text" class="form-control" id="s_phone" required pattern="0[0-9]{9,10}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Giới tính:</label>
                                <select class="form-select" id="s_gender">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Chức vụ:</label>
                                <select class="form-select" id="s_role">
                                    <option value="Thành viên">Thành viên</option>
                                    <option value="Lớp trưởng">Lớp trưởng</option>
                                    <option value="Bí thư">Bí thư</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Lớp:</label>
                                <select class="form-select" id="s_class" name="class">
                                    <option value="">-- Chọn lớp --</option>
                                    <?php
                                    $lopRs = $conn->query("SELECT tenLop FROM lophoc ORDER BY tenLop ASC");
                                    if ($lopRs && $lopRs->num_rows > 0) {
                                        while ($lop = $lopRs->fetch_assoc()) {
                                            echo '<option value="' . htmlspecialchars($lop['tenLop']) . '">' . htmlspecialchars($lop['tenLop']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <div class="d-flex align-items-center gap-4 mb-2">
                                    <label class="me-3">Trạng thái:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusActive" value="active" checked>
                                        <label class="form-check-label" for="statusActive">Đang học</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive">
                                        <label class="form-check-label" for="statusInactive">Đã nghỉ</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-primary px-4" id="btnSaveStudent">+ Thêm mới</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- XÓA -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-transparent">
                <div class="modal-body p-0">
                    <div class="delete-box position-relative mx-auto p-4 text-center bg-white rounded-3 shadow-sm" style="max-width: 400px;">
                        <div class="question-icon">?</div>
                        <div class="bg-light rounded-3 py-4 px-3 mt-3 mb-4">
                            <h5 class="fw-bold text-dark m-0" id="deleteMsg">Bạn chắc chắn muốn xóa học sinh này?</h5>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const classFilter = document.getElementById('classFilterHeader');
            const table = document.querySelector('.table tbody');

            classFilter.addEventListener('change', function() {
                const selectedClass = this.value;
                const rows = table.querySelectorAll('tr');

                rows.forEach(row => {
                    const classCell = row.cells[4].textContent.trim(); // cột lớp thứ 5
                    if (!selectedClass || selectedClass === classCell || (selectedClass === 'Chưa có lớp' && classCell === 'Chưa có lớp')) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
</main>
<?php
require_once '../../footer.php';
?>