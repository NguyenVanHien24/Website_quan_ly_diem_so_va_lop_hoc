<?php
session_start();
require_once '../../config.php';
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
$currentPage = 'hoc-sinh';
$pageCSS = ['QuanLyHocSinh.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['QuanLyHocSinh.js'];

// ==== Lấy thông tin giáo viên từ DB ====
$userID = $_SESSION['userID'];
$sql = "SELECT u.hoVaTen, u.email, u.sdt, u.gioiTinh, g.boMon
        FROM user u
        JOIN giaovien g ON u.userId = g.userId
        WHERE u.userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();
$stmt->close();

?>
<main>
    <div class="main-header d-flex justify-content-between align-items-end mb-4">
        <div>
            <h1 class="page-title">QUẢN LÝ HỌC SINH</h1>

            <div class="mt-3 d-flex align-items-center">
                <label for="classFilterHeader" class="fw-bold me-3 fs-5">Lớp:</label>
                <select class="form-select py-2 px-3" id="classFilterHeader" style="width: 300px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <option value="11A4" selected>Lớp 11A4</option>
                    <option value="10A1">Lớp 10A1</option>
                    <option value="12A1">Lớp 12A1</option>
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
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>1</td>
                        <td>K25110386</td>
                        <td>Trần Hoàng Nhi</td>
                        <td>11A4</td>
                        <td>Lớp trưởng</td>
                        <td><span class="badge-active">● Active</span></td>
                        <td class="action-icons">
                            <a href="#" class="btn-edit"
                                data-id="K25110386"
                                data-name="Trần Hoàng Nhi"
                                data-email="nhi.tran@gmail.com"
                                data-phone="0901234567"
                                data-gender="Nữ"
                                data-class="11A4"
                                data-role="Lớp trưởng"
                                data-status="active"
                                data-bs-toggle="modal" data-bs-target="#studentFormModal">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="#" class="btn-delete"
                                data-id="K25110386"
                                data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>2</td>
                        <td>K25121004</td>
                        <td>Lê Văn An</td>
                        <td>12A1</td>
                        <td>Thành viên</td>
                        <td><span class="badge-active">● Active</span></td>
                        <td class="action-icons">
                            <a href="#" class="btn-edit"
                                data-id="K25121004"
                                data-name="Lê Văn An"
                                data-email="an.le@gmail.com"
                                data-phone="0987654321"
                                data-gender="Nam"
                                data-class="12A1"
                                data-role="Thành viên"
                                data-status="active"
                                data-bs-toggle="modal" data-bs-target="#studentFormModal">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="#" class="btn-delete" data-id="K25121004" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span>1-2/18 mục</span>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                </ul>
            </nav>
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
                    <form id="studentForm">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Năm học:</label>
                                <select class="form-select" id="s_year">
                                    <option value="2024-2025">2024-2025</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Học kỳ:</label>
                                <select class="form-select" id="s_semester">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mã Học sinh:</label>
                                <input type="text" class="form-control" id="s_id" placeholder="K25...">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và Tên:</label>
                                <input type="text" class="form-control" id="s_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email:</label>
                                <input type="email" class="form-control" id="s_email">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Số Điện Thoại:</label>
                                <input type="text" class="form-control" id="s_phone">
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
                                <select class="form-select" id="s_class">
                                    <option value="10A1">10A1</option>
                                    <option value="11A4">11A4</option>
                                    <option value="12A1">12A1</option>
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

</main>
<?php
require_once '../../footer.php';
?>