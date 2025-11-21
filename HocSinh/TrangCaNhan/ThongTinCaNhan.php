<?php 
    require_once '../../config.php';
    $currentPage = 'thong-tin'; 
    $pageCSS = ['ThongTinCaNhan.css'];
    require_once '../SidebarAndHeader.php'; 
    $pageJS = ['ThongTinCaNhan.js'];
?>

<main>
    <div class="container-fluid p-4">
        <h2 class="page-title mb-5">Thông tin cá nhân</h2>

        <div class="row align-items-start">
            <div class="col-md-4 d-flex justify-content-center mb-4 mb-md-0">
                <div class="avatar-container">
                    <i class="bi bi-person-fill"></i>
                </div>
            </div>

            <div class="col-md-8 ps-md-5">
                <div class="info-list">
                    <div class="row mb-3">
                        <div class="col-4 col-sm-3 fw-bold text-dark">Mã học sinh:</div>
                        <div class="col-8 col-sm-9 text-secondary">K25101207</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4 col-sm-3 fw-bold text-dark">Họ và tên:</div>
                        <div class="col-8 col-sm-9 text-secondary">Hoàng Thảo Nhi</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4 col-sm-3 fw-bold text-dark">Lớp:</div>
                        <div class="col-8 col-sm-9 text-secondary">10A1</div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-4 col-sm-3 fw-bold text-dark">Chức vụ:</div>
                        <div class="col-8 col-sm-9 text-secondary">Học viên</div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-6 col-sm-3">
                            <span class="fw-bold text-dark me-2">Tuổi:</span>
                            <span class="text-secondary">34</span>
                        </div>
                        <div class="col-6 col-sm-4">
                            <span class="fw-bold text-dark me-2">Giới tính:</span>
                            <span class="text-secondary">Nữ</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-4">
                        <div class="contact-box">
                            <div class="icon-wrap">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <span class="fw-bold text-dark">0988888888</span>
                        </div>

                        <div class="contact-box">
                            <div class="icon-wrap">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <span class="fw-bold text-dark">nva01@gmail.com</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../footer.php'; ?>