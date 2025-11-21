<?php
require_once '../../config.php';
$currentPage = 'thong-bao';
$pageCSS = ['QuanLyThongBao.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['QuanLyThongBao.js'];
?>

<main>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="page-title">THÔNG BÁO</h1>
        <button class="btn btn-primary btn-add-notify" data-bs-toggle="modal" data-bs-target="#addNotifyModal">
            THÊM THÔNG BÁO
        </button>
    </div>

    <div class="notify-tabs mb-3">
        <a href="#" class="tab-item active">Tất cả (1234)</a>
        <a href="#" class="tab-item">Đã gửi (1200)</a>
        <a href="#" class="tab-item">Đã lên lịch (34)</a>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th><input class="form-check-input" type="checkbox"></th>
                        <th>STT</th>
                        <th>MÃ TB</th>
                        <th>TIÊU ĐỀ</th>
                        <th>NGƯỜI GỬI</th>
                        <th>NGƯỜI NHẬN</th>
                        <th>TRẠNG THÁI</th>
                        <th>TÁC VỤ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>1</td>
                        <td>TB00001</td>
                        <td>Thông báo nghỉ lễ</td>
                        <td>Admin</td>
                        <td>Giáo viên</td>
                        <td><span class="text-secondary fw-bold">ĐÃ GỬI</span></td>
                        <td class="action-icons">
                            <a href="#" class="btn-view"
                                data-id="TB00001" data-title="Thông báo nghỉ lễ" data-content="Nội dung chi tiết..."
                                data-date="15/10/2025" data-receiver="teacher"
                                data-bs-toggle="modal" data-bs-target="#viewNotifyModal">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            <a href="#" class="btn-edit"
                                data-id="TB00001" data-title="Thông báo nghỉ lễ" data-content="Nội dung chi tiết..."
                                data-date="15/10/2025" data-receiver="teacher"
                                data-bs-toggle="modal" data-bs-target="#editNotifyModal">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="#" class="btn-delete"
                                data-id="TB00001"
                                data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>2</td>
                        <td>TB00002</td>
                        <td>Lịch thi học kỳ 1</td>
                        <td>Admin</td>
                        <td>Học sinh</td>
                        <td><span class="text-secondary fw-bold">ĐÃ GỬI</span></td>
                        <td class="action-icons">
                            <a href="#" class="btn-view"><i class="bi bi-box-arrow-up-right"></i></a>
                            <a href="#" class="btn-edit"><i class="bi bi-pencil-square"></i></a>
                            <a href="#" class="btn-delete"><i class="bi bi-trash-fill"></i></a>
                        </td>
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
        <button class="btn btn-danger fw-bold px-4 py-2" id="btnDeleteMulti">Xóa thông báo</button>
    </div>


    <div class="modal fade" id="addNotifyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-4">
                <h2 class="modal-title fw-bold mb-4">THÊM THÔNG BÁO</h2>
                <form id="addForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tiêu đề:</label>
                        <input type="text" class="form-control" placeholder="Tiêu đề 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung:</label>
                        <textarea class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Thời gian gửi thông báo:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="dd/mm/yyyy">
                            <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold me-3">Người nhận:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="receiverAdd" id="rx1" value="all">
                            <label class="form-check-label" for="rx1">Toàn hệ thống</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="receiverAdd" id="rx2" value="teacher" checked>
                            <label class="form-check-label" for="rx2">Giáo viên</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="receiverAdd" id="rx3" value="student">
                            <label class="form-check-label" for="rx3">Học sinh</label>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Đính kèm tệp (Tùy chọn):</label>
                        <div class="d-flex align-items-center">
                            <label class="btn btn-light border me-2">Chọn tệp <input type="file" hidden></label>
                            <span class="text-muted fst-italic">Không tệp nào được chọn</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">HỦY</button>
                        <button type="button" class="btn btn-primary px-4" style="background-color: #0b1a48;">GỬI THÔNG BÁO</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editNotifyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-4 bg-light">
                <h2 class="modal-title fw-bold mb-4">CHỈNH SỬA THÔNG BÁO</h2>
                <form id="editForm">
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 text-secondary">Mã thông báo:</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="e_id" disabled>
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 text-secondary">Tiêu đề:</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="e_title">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-3 text-secondary pt-2">Nội dung:</label>
                        <div class="col-md-9">
                            <textarea class="form-control" rows="4" id="e_content"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 text-secondary">Thời gian gửi thông báo:</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <input type="text" class="form-control" id="e_date">
                                <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4 align-items-center">
                        <label class="col-md-3 text-secondary">Người nhận:</label>
                        <div class="col-md-9">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="receiverEdit" id="erx1" value="all">
                                <label class="form-check-label">Toàn hệ thống</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="receiverEdit" id="erx2" value="teacher">
                                <label class="form-check-label">Giáo viên</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="receiverEdit" id="erx3" value="student">
                                <label class="form-check-label">Học sinh</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">HỦY</button>
                        <button type="button" class="btn btn-success px-4 text-white fw-bold">LƯU</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewNotifyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-4 bg-light">
                <h2 class="modal-title fw-bold mb-4">CHI TIẾT THÔNG BÁO</h2>
                <form>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 text-secondary">Mã thông báo:</label>
                        <div class="col-md-9"><input type="text" class="form-control bg-white" id="v_id" readonly></div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 text-secondary">Tiêu đề:</label>
                        <div class="col-md-9"><input type="text" class="form-control bg-white" id="v_title" readonly></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-3 text-secondary pt-2">Nội dung:</label>
                        <div class="col-md-9"><textarea class="form-control bg-white" rows="4" id="v_content" readonly></textarea></div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 text-secondary">Thời gian gửi thông báo:</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <input type="text" class="form-control bg-white" id="v_date" readonly>
                                <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4 align-items-center">
                        <label class="col-md-3 text-secondary">Người nhận:</label>
                        <div class="col-md-9">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="receiverView" id="vrx1" value="all" disabled>
                                <label class="form-check-label">Toàn hệ thống</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="receiverView" id="vrx2" value="teacher" disabled>
                                <label class="form-check-label">Giáo viên</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="receiverView" id="vrx3" value="student" disabled>
                                <label class="form-check-label">Học sinh</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-dark px-4" data-bs-dismiss="modal">QUAY LẠI</button>
                    </div>
                </form>
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
                            <h5 class="fw-bold text-dark m-0" id="deleteMsg">Bạn chắc chắn muốn xóa thông báo?</h5>
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