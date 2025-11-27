<?php
require_once '../../config.php';
// Đặt tên trang (giả sử thuộc nhóm quản lý tài khoản)
$currentPage = 'phan-quyen';
$pageCSS = ['PhanQuyen.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['PhanQuyen.js'];
?>

<main>
    <div class="content-wrapper">
        <h2 class="section-title">THÔNG TIN TÀI KHOẢN</h2>

        <div class="form-container mb-5">
            <div class="row mb-4 align-items-center">
                <div class="col-md-6">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label fw-bold text-end-md">Email đăng nhập:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="DHoangVan@gmail.com" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label fw-bold text-end-md">Tên hiển thị:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="Hoàng Văn D">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label fw-bold text-end-md">Mã giáo viên:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="GV0001" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label fw-bold text-end-md">Số điện thoại:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="section-title">PHÂN QUYỀN</h2>

        <div class="form-container permission-box mb-5">
            <div class="row mb-3 align-items-center">
                <label class="col-6 col-sm-4 fw-bold ps-4">Admin hệ thống:</label>
                <div class="col-6 col-sm-8">
                    <input class="form-check-input custom-checkbox" type="checkbox" id="roleAdmin">
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label class="col-6 col-sm-4 fw-bold ps-4">Giáo viên:</label>
                <div class="col-6 col-sm-8">
                    <input class="form-check-input custom-checkbox" type="checkbox" id="roleTeacher" checked>
                </div>
            </div>

            <div class="row align-items-center">
                <label class="col-6 col-sm-4 fw-bold ps-4">Học sinh:</label>
                <div class="col-6 col-sm-8">
                    <input class="form-check-input custom-checkbox" type="checkbox" id="roleStudent">
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-5">
            <button type="button" class="btn btn-cancel">Hủy</button>
            <button type="button" class="btn btn-save">Lưu thông tin</button>
        </div>

    </div>
</main>

<?php require_once '../../footer.php'; ?>