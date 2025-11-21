<?php
require_once '../../config.php';
// Đặt tên trang để active menu bên sidebar
$currentPage = 'phan-cong';
// Tải CSS riêng cho trang này
$pageCSS = ['QuanLyPhanCong.css'];
require_once '../SidebarAndHeader.php';
// Tải JS xử lý logic
$pageJS = ['QuanLyPhanCong.js'];
?>

<main>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">PHÂN CÔNG</h1>
        <button class="btn btn-primary btn-add-assign" data-bs-toggle="modal" data-bs-target="#assignFormModal">
            THÊM PHÂN CÔNG
        </button>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th><input class="form-check-input" type="checkbox"></th>
                        <th>STT</th>
                        <th>LỚP</th>
                        <th>KHỐI</th>
                        <th>MÔN HỌC</th>
                        <th>GIÁO VIÊN PHỤ TRÁCH</th>
                        <th>TÁC VỤ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>1</td>
                        <td>10A1</td>
                        <td>10</td>
                        <td>Toán học</td>
                        <td>Nguyễn Văn A</td>
                        <td class="action-icons">
                            <a href="#" class="btn-edit"
                                data-id="1"
                                data-class="10A1"
                                data-subject="Toán học"
                                data-teacher="Nguyễn Văn A"
                                data-bs-toggle="modal" data-bs-target="#assignFormModal">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="#" class="btn-delete"
                                data-id="1"
                                data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>2</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="action-icons"></td>
                    </tr>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>3</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="action-icons"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">1-4/18 mục</div>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">&lt;</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1/5</a></li>
                    <li class="page-item"><a class="page-link" href="#">&gt;</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button class="btn btn-danger fw-bold px-4 py-2">Xóa phân công</button>
    </div>
    <!-- THÊM PHÂN CÔNG -->

    <div class="modal fade" id="assignFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-light border-0">
                <div class="modal-header border-0 pb-0 pt-4 px-5">
                    <h2 class="modal-title fw-bold" id="modalTitle">THÊM PHÂN CÔNG</h2>
                </div>
                <div class="modal-body pt-4 px-5 pb-5">
                    <form id="assignForm">
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 fw-bold text-uppercase text-secondary">LỚP:</label>
                            <div class="col-md-9">
                                <select class="form-select py-2" id="pc_class">
                                    <option value="">Chọn lớp...</option>
                                    <option value="10A1">10A1</option>
                                    <option value="11A2">11A2</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 fw-bold text-uppercase text-secondary">MÔN:</label>
                            <div class="col-md-9">
                                <select class="form-select py-2" id="pc_subject">
                                    <option value="">Chọn môn...</option>
                                    <option value="Toán học">Toán học</option>
                                    <option value="Vật lý">Vật lý</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-5 align-items-center">
                            <label class="col-md-3 fw-bold text-uppercase text-secondary">GIÁO VIÊN:</label>
                            <div class="col-md-9">
                                <select class="form-select py-2" id="pc_teacher">
                                    <option value="">Chọn giáo viên...</option>
                                    <option value="Nguyễn Văn A">Nguyễn Văn A</option>
                                    <option value="Trần Thị B">Trần Thị B</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 fw-bold text-uppercase" data-bs-dismiss="modal" style="border-color: #ccc; color: #333;">HỦY</button>
                            <button type="button" class="btn btn-primary px-4 py-2 fw-bold text-uppercase" id="btnSaveAssign" style="background-color: #0b1a48;">LƯU</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- XÓA PHÂN CÔNG -->

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-transparent">
                <div class="modal-body p-0">
                    <div class="delete-box position-relative mx-auto p-4 text-center bg-white rounded-3 shadow-sm" style="max-width: 400px;">
                        <div class="question-icon">?</div>
                        <div class="bg-light rounded-3 py-4 px-3 mt-3 mb-4">
                            <h5 class="fw-bold text-dark m-0" id="deleteMsg">Bạn chắc chắn muốn xóa phân công này?</h5>
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
<?php require_once '../../footer.php'; ?>