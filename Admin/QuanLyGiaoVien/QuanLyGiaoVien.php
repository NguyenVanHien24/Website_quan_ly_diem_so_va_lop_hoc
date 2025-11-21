<?php
require_once '../../config.php';
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
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>1</td>
                        <td>GV0001</td>
                        <td>Hoàng Văn D</td>
                        <td>Nam</td>
                        <td>DHoangVan@gmail.com</td>
                        <td>Toán Học</td>
                        <td><span class="badge-active">● Active</span></td>
                        <td class="action-icons">
                            <a href="#" class="btn-edit"
                                data-id="GV0001"
                                data-name="Hoàng Văn D"
                                data-email="DHoangVan@gmail.com"
                                data-phone="0901234567"
                                data-gender="Nam"
                                data-dept="Toán Học"
                                data-office="Phòng 101"
                                data-degree="Thạc sĩ"
                                data-status="active"
                                data-bs-toggle="modal" data-bs-target="#teacherFormModal">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <a href="#" class="btn-delete"
                                data-id="GV0001"
                                data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>2</td>
                        <td>GV0002</td>
                        <td>Trần Hoàng Văn A</td>
                        <td>Nữ</td>
                        <td>aHoangVanTran@gamil.com</td>
                        <td>Văn Học</td>
                        <td><span class="badge-active">● Active</span></td>
                        <td class="action-icons">
                            <a href="#" class="btn-edit"
                                data-id="GV0002"
                                data-name="Trần Hoàng Văn A"
                                data-email="aHoangVanTran@gamil.com"
                                data-phone="0912345678"
                                data-gender="Nữ"
                                data-dept="Văn Học"
                                data-office="Phòng 102"
                                data-degree="Tiến sĩ"
                                data-status="active"
                                data-bs-toggle="modal" data-bs-target="#teacherFormModal">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="#" class="btn-delete" data-id="GV0002" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>
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
                    <form id="teacherForm">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Năm học:</label>
                                <select class="form-select">
                                    <option>2024-2025</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Học kỳ:</label>
                                <select class="form-select">
                                    <option>1</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bộ môn:</label>
                                <select class="form-select" id="t_dept">
                                    <option value="Toán Học">Toán Học</option>
                                    <option value="Văn Học">Văn Học</option>
                                    <option value="Vật Lý">Vật Lý</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và Tên:</label>
                                <input type="text" class="form-control" id="t_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email:</label>
                                <input type="email" class="form-control" id="t_email">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Số Điện Thoại:</label>
                                <input type="text" class="form-control" id="t_phone">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Giới tính:</label>
                                <select class="form-select" id="t_gender">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Trình độ:</label>
                                <input type="text" class="form-control" id="t_degree">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Phòng ban:</label>
                                <input type="text" class="form-control" id="t_office">
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <div class="d-flex align-items-center gap-4 mb-2">
                                    <label class="me-3">Trạng thái:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusActive" value="active" checked>
                                        <label class="form-check-label" for="statusActive">Đang hoạt động</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive">
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
// Yêu cầu file footer.php để đóng các thẻ và tải JS
require_once '../../footer.php';
?>