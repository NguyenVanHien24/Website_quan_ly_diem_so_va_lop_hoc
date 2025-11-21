<?php 
    require_once '../config.php';
    $currentPage = 'dashboard'; 
    $pageCSS = ['Dashboard.css'];
    require_once 'SidebarAndHeader.php'; 
?>
    <!-- ========= BẮT ĐẦU NỘI DUNG CHÍNH CỦA TRANG ========= -->
    <main>
        <h1 class="page-title">TỔNG QUAN</h1>
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="stat-card">
                    <div class="stat-card-info">
                        <h6>TỔNG HỌC SINH</h6>
                        <div class="stat-number">1.465</div>
                        <p>Học sinh hoạt động trong năm nay</p>
                    </div>
                    <i class="bi bi-mortarboard stat-icon"></i>
                </div>
            </div>
                <div class="col-lg-4 mb-4">
                <div class="stat-card">
                    <div class="stat-card-info">
                        <h6>GIÁO VIÊN</h6>
                        <div class="stat-number">162</div>
                        <p>Cán bộ/giáo viên</p>
                    </div>
                        <i class="bi bi-people stat-icon"></i>
                </div>
            </div>
                <div class="col-lg-4 mb-4">
                <div class="stat-card">
                    <div class="stat-card-info">
                        <h6>LỚP HỌC</h6>
                        <div class="stat-number">38</div>
                        <p>Lớp đang vận hành</p>
                    </div>
                    <i class="bi bi-bank stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="content-panel">
                    <h5 class="panel-title">HOẠT ĐỘNG GẦN ĐÂY</h5>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon"><i class="bi bi-person-plus-fill"></i></div>
                            <div class="activity-text">
                                <p>Học sinh Nguyễn Văn A được thêm vào lớp 10A1.</p>
                                <span>2 tiếng trước</span>
                            </div>
                        </div>
                            <div class="activity-item">
                            <div class="activity-icon"><i class="bi bi-pencil-square"></i></div>
                            <div class="activity-text">
                                <p>Giáo viên Bùi Thị B đã nhập điểm giữa kì cho lớp 11A2.</p>
                                <span>3 tiếng trước</span>
                            </div>
                        </div>
                            <div class="activity-item">
                            <div class="activity-icon"><i class="bi bi-info-circle-fill"></i></div>
                            <div class="activity-text">
                                <p>Thông tin lớp 12A3 đã được chỉnh sửa.</p>
                                <span>1 ngày trước</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5 mb-4">
                <div class="quick-actions-panel">
                    <h5 class="panel-title">CÁC TÁC VỤ NHANH</h5>
                    <button class="btn btn-action">
                        <div class="action-icon"><i class="bi bi-person-plus"></i></div>
                        THÊM HỌC SINH
                    </button>
                    <button class="btn btn-action">
                        <div class="action-icon"><i class="bi bi-person-video3"></i></div>
                            THÊM GIÁO VIÊN
                    </button>
                    <button class="btn btn-action">
                        <div class="action-icon"><i class="bi bi-easel"></i></div>
                        TẠO LỚP HỌC MỚI
                    </button>
                </div>
            </div>
        </div>
    </main>
    <!-- ========= KẾT THÚC NỘI DUNG CHÍNH CỦA TRANG ========= -->
<?php 
    // Yêu cầu file footer.php để đóng các thẻ và tải JS
    require_once '../footer.php'; 
?>