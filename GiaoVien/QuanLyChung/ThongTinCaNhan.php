<?php 
    require_once '../../config.php';
    $currentPage = 'thong-tin'; 
    // Gọi file CSS riêng
    $pageCSS = ['ThongTinCaNhan.css'];
    require_once '../SidebarAndHeader.php';
    $pageJS = ['ThongTinCaNhan.js'];
?>

<main class="p-4">
    <h2 class="page-title mb-5">Thông tin cá nhân</h2>

    <div class="row">
        <div class="col-md-4 d-flex flex-column align-items-center text-center">
            <div class="profile-avatar mb-4">
                <i class="bi bi-person-fill"></i>
            </div>
            <h4 class="fw-bold mb-1">Nguyễn Văn A</h4>
            <p class="text-secondary">Bộ môn Toán</p>
        </div>

        <div class="col-md-8 ps-md-5">
            <div class="info-section mb-4">
                <h6 class="fw-bold text-dark">Giới thiệu chung:</h6>
                <p class="text-secondary">
                    Thầy là một giáo viên tận tâm, có nhiều năm kinh nghiệm trong giảng dạy, 
                    luôn quan tâm và khuyến khích học sinh phát triển cả kiến thức lẫn kỹ năng sống.
                </p>
            </div>

            <div class="info-section mb-4">
                <h6 class="fw-bold text-dark">Bằng cấp/Chuyên môn</h6>
                <ul class="list-unstyled text-secondary">
                    <li class="mb-1">Cử nhân Sư phạm Toán học</li>
                    <li class="mb-1">Thạc sĩ Lý luận và Phương pháp dạy học Toán</li>
                    <li>Chứng chỉ Nghiệp vụ sư phạm</li>
                </ul>
            </div>

            <div class="row mb-5">
                <div class="col-6 col-md-3">
                    <h6 class="fw-bold text-dark small mb-1">Tuổi</h6>
                    <p class="text-secondary">34</p>
                </div>
                <div class="col-6 col-md-3">
                    <h6 class="fw-bold text-dark small mb-1">Giới tính</h6>
                    <p class="text-secondary">Nam</p>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-4">
                <div class="contact-box">
                    <div class="icon-wrapper">
                        <i class="bi bi-telephone"></i>
                    </div>
                    <span class="fw-bold text-dark">0988888888</span>
                </div>
                
                <div class="contact-box">
                    <div class="icon-wrapper">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <span class="fw-bold text-dark">nva01@gmail.com</span>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../footer.php'; ?>