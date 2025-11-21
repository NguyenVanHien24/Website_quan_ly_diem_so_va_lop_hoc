<?php 
    require_once '../../config.php';
    $currentPage = 'diem-so'; 
    $pageCSS = ['QuanLyDiemSo.css'];
    require_once '../SidebarAndHeader.php';
    $pageJS = ['QuanLyDiemSo.js'];
?>
<main>
    <h1 class="page-title">BẢNG ĐIỂM</h1>

    <div class="row mb-4 filter-section">
        <div class="col-md-4">
            <label for="class-filter" class="form-label fw-bold">Lớp:</label>
            <select class="form-select" id="class-filter">
                <option value="10A1">Lớp 10A1</option>
                <option value="11A4" selected>Lớp 11A4</option>
                <option value="12A1">Lớp 12A1</option>
            </select>
        </div>
            <div class="col-md-4">
            <label for="subject-filter" class="form-label fw-bold">Môn:</label>
            <select class="form-select" id="subject-filter">
                <option value="math">Toán</option>
                <option value="physics">Vật Lý</option>
                <option value="biology" selected>Sinh học</option>
            </select>
        </div>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><input class="form-check-input" type="checkbox"></th>
                        <th>STT</th>
                        <th>Mã HS</th>
                        <th>Họ Tên</th>
                        <th>Môn học</th>
                        <th>Điểm miệng</th>
                        <th>Điểm 1 Tiết</th>
                        <th>Điểm Thi Học Kì I</th>
                        <th>Điểm Thi Học Kì II</th>
                        <th>Trung Bình Môn</th>
                        <th>Tác Vụ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>1</td>
                        <td class="student-id">K25110386</td>
                        <td class="student-name">Trần Hoàng Nhi</td>
                        <td>Sinh học</td>
                        <td class="score-mouth">9.0</td>
                        <td class="score-45m">8.0</td>
                        <td class="score-hk1">8.5</td>
                        <td class="score-hk2">9.0</td>
                        <td class="score-avg">9.0</td> <td class="action-icons">
                            <a href="#" class="btn-view" 
                            data-id="K25110386" 
                            data-name="Trần Hoàng Nhi" 
                            data-s1-mouth="9.0" data-s1-45m="8.0" data-s1-gk="8.5" data-s1-ck="9.0"
                            data-s2-mouth="9.0" data-s2-45m="" data-s2-gk="" data-s2-ck=""
                            data-bs-toggle="modal" data-bs-target="#viewGradeModal">
                                <i class="bi bi-eye"></i>
                            </a> 
                            
                            <a href="#" class="btn-edit" 
                            data-id="K25110386" 
                            data-name="Trần Hoàng Nhi" 
                            data-s1-mouth="9.0" data-s1-45m="8.0" data-s1-gk="8.5" data-s1-ck="9.0"
                            data-s2-mouth="9.0" data-s2-45m="" data-s2-gk="" data-s2-ck=""
                            data-bs-toggle="modal" data-bs-target="#gradeEntryModal">
                                <i class="bi bi-pencil-square"></i>
                            </a> 
                            <a href="#"><i class="bi bi-printer"></i></a> 
                        </td>
                    </tr>

                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>2</td>
                        <td class="student-id">K25999999</td>
                        <td class="student-name">Nguyễn Văn A</td>
                        <td>Sinh học</td>
                        <td class="score-mouth"></td>
                        <td class="score-45m"></td>
                        <td class="score-hk1"></td>
                        <td class="score-hk2"></td>
                        <td class="score-avg"></td>
                        
                        <td class="action-icons">
                            <a href="#" class="btn-view" 
                            data-id="K25999999" data-name="Nguyễn Văn A" 
                            data-s1-mouth="" data-s1-45m="" data-s1-gk="" data-s1-ck=""
                            data-s2-mouth="" data-s2-45m="" data-s2-gk="" data-s2-ck=""
                            data-bs-toggle="modal" data-bs-target="#viewGradeModal">
                                <i class="bi bi-eye"></i>
                            </a>
                            
                            <a href="#" class="btn-edit" 
                            data-id="K25999999" data-name="Nguyễn Văn A" 
                            data-s1-mouth="" data-s1-45m="" data-s1-gk="" data-s1-ck=""
                            data-s2-mouth="" data-s2-45m="" data-s2-gk="" data-s2-ck=""
                            data-bs-toggle="modal" data-bs-target="#gradeEntryModal">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="#"><i class="bi bi-printer"></i></a>
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
    
    <div class="d-flex justify-content-end gap-3 mt-4">
        <button class="btn btn-import">Import bảng điểm</button>
        <button class="btn btn-export">Xuất bảng điểm</button>
    </div>

    <!-- NHẬP ĐIỂM -->
<div class="modal fade" id="gradeEntryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="modalActionTitle">CẬP NHẬT ĐIỂM</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="student-info-bar">
                    <span id="edit_student_name">HỌ TÊN HỌC SINH: TRẦN HOÀNG NHI</span>
                    <span id="edit_student_id">MÃ HỌC SINH: K25110386</span>
                </div>
                <form>
                    <div>
                        <div class="semester-title">HỌC KỲ I</div>
                        <div class="row g-4">
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM MIỆNG:</label><input type="text" class="form-control" id="edit_s1_mouth" value="9.0"></div>
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI GK:</label><input type="text" class="form-control" id="edit_s1_gk" value="8.5"></div>
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM 1 TIẾT:</label><input type="text" class="form-control" id="edit_s1_45m" value="8.0"></div>
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI CK:</label><input type="text" class="form-control" id="edit_s1_ck" value="9.0"></div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="semester-title">HỌC KỲ II</div>
                        <div class="row g-4">
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM MIỆNG:</label><input type="text" class="form-control" id="edit_s2_mouth"></div>
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI GK:</label><input type="text" class="form-control" id="edit_s2_gk"></div>
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM 1 TIẾT:</label><input type="text" class="form-control" id="edit_s2_45m"></div>
                            <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI CK:</label><input type="text" class="form-control" id="edit_s2_ck"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-3 mt-5">
                        <button type="button" class="btn btn-custom-cancel" data-bs-dismiss="modal">HỦY</button>
                        <button type="submit" class="btn btn-custom-save">LƯU</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewGradeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">CHI TIẾT ĐIỂM</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="student-info-bar bg-light"> <span id="view_student_name">HỌ TÊN HỌC SINH: TRẦN HOÀNG NHI</span>
                    <span id="view_student_id">MÃ HỌC SINH: K25110386</span>
                </div>
                
                <div>
                    <div class="semester-title">HỌC KỲ I</div>
                    <div class="row g-4">
                        <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM MIỆNG:</label><input type="text" class="form-control bg-light" id="view_s1_mouth" value="9.0" readonly></div>
                        <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI GK:</label><input type="text" class="form-control bg-light" id="view_s1_gk" value="8.5" readonly></div>
                        <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM 1 TIẾT:</label><input type="text" class="form-control bg-light" id="view_s1_45m" value="8.0" readonly></div>
                        <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI CK:</label><input type="text" class="form-control bg-light" id="view_s1_ck" value="9.0" readonly></div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="semester-title">HỌC KỲ II</div>
                    <div class="row g-4">
                        <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM MIỆNG:</label><input type="text" class="form-control bg-light" id="view_s2_mouth" readonly></div>
                        <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI GK:</label><input type="text" class="form-control bg-light" id="view_s2_gk" readonly></div>
                        <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM 1 TIẾT:</label><input type="text" class="form-control bg-light" id="view_s2_45m" readonly></div>
                        <div class="col-md-6 d-flex align-items-center"><label class="col-4 form-label mb-0">ĐIỂM THI CK:</label><input type="text" class="form-control bg-light" id="view_s2_ck" readonly></div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-5">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">ĐÓNG</button>
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