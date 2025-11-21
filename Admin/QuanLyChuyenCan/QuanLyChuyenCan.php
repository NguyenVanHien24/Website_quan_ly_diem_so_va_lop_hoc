<?php 
    require_once '../../config.php';
    $currentPage = 'chuyen-can'; 
    $pageCSS = ['QuanLyChuyenCan.css'];
    require_once '../SidebarAndHeader.php';
?>
<main>
    <h1 class="page-title">ĐIỂM DANH HỌC SINH</h1>
    
        <div class="row mb-4 filter-section">
            <div class="col-md-3">
                <label for="attendance-date" class="form-label fw-bold">Ngày:</label>
                <input type="date" class="form-control" id="attendance-date" value="2025-09-29">
            </div>
            <div class="col-md-3">
                <label for="class-filter" class="form-label fw-bold">Lớp:</label>
                <select class="form-select" id="class-filter">
                    <option selected>Tất cả các lớp</option>
                    <option value="10A1">10A1</option>
                    <option value="10A2">10A2</option>
                </select>
            </div>
                <div class="col-md-3">
                <label for="subject-filter" class="form-label fw-bold">Môn:</label>
                <select class="form-select" id="subject-filter">
                    <option selected>Tất cả các môn</option>
                    <option value="math">Toán</option>
                    <option value="physics">Lý</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="content-container">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><input class="form-check-input" type="checkbox"></th>
                                    <th>STT</th>
                                    <th>Lớp</th>
                                    <th>Họ và tên</th>
                                    <th class="text-center">Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input class="form-check-input" type="checkbox"></td>
                                    <td>1</td>
                                    <td>10A1</td>
                                    <td>Phạm Thu A</td>
                                    <td class="text-center">
                                        <button class="btn-attendance btn-present">Có mặt</button>
                                        <button class="btn-attendance btn-late">Đến muộn</button>
                                        <button class="btn-attendance btn-absent">Vắng mặt</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input class="form-check-input" type="checkbox"></td>
                                    <td>2</td>
                                    <td>10A1</td>
                                    <td>Bùi Trần B</td>
                                    <td class="text-center">
                                        <button class="btn-attendance btn-present">Có mặt</button>
                                        <button class="btn-attendance btn-late">Đến muộn</button>
                                        <button class="btn-attendance btn-absent">Vắng mặt</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input class="form-check-input" type="checkbox"></td>
                                    <td>3</td>
                                    <td>10A1</td>
                                    <td>Nguyễn Vỹ C</td>
                                    <td class="text-center">
                                        <button class="btn-attendance btn-present">Có mặt</button>
                                        <button class="btn-attendance btn-late">Đến muộn</button>
                                        <button class="btn-attendance btn-absent">Vắng mặt</button>
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
                                <li class="page-item"><a class="page-link" href="#">/5</a></li>
                                <li class="page-item"><a class="page-link" href="#">›</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-panel">
                    <h5 class="panel-title">Tổng quan điểm danh</h5>
                    <div class="summary-item summary-present">
                        <div><i class="bi bi-check-circle-fill icon me-2"></i> Có mặt</div>
                        <div class="count">1403</div>
                    </div>
                    <div class="summary-item summary-late">
                        <div><i class="bi bi-exclamation-triangle-fill icon me-2"></i> Đến muộn</div>
                        <div class="count">24</div>
                    </div>
                    <div class="summary-item summary-absent">
                        <div><i class="bi bi-x-circle-fill icon me-2"></i> Vắng mặt</div>
                        <div class="count">38</div>
                    </div>
                    <div class="summary-item summary-rate">
                        <div>Tỉ lệ đi học:</div>
                        <div class="count">95,7%</div>
                    </div>
                </div>
            </div>
        </div>
</main>
    <!-- ========= KẾT THÚC NỘI DUNG CHÍNH CỦA TRANG ========= -->
<?php 
    // Yêu cầu file footer.php để đóng các thẻ và tải JS
    require_once dirname(dirname(__DIR__)) . '/footer.php'; 
?>