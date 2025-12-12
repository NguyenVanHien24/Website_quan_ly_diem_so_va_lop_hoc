<?php
require_once '../../config.php';
$currentPage = 'ket-qua';
$pageCSS = ['KetQuaHocTap.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['KetQuaHocTap.js'];
?>

<main>
    <h1 class="page-title mb-4">BẢNG ĐIỂM</h1>
    <div class="content-container bg-white p-0 border rounded-3 overflow-hidden">
        <div class="p-3 border-bottom bg-light d-flex gap-3 align-items-center">
            <label class="mb-0 fw-bold">Năm học</label>
            <select id="selYear" class="form-select form-select-sm" style="width:180px"></select>
            <label class="mb-0 fw-bold ms-3">Học kỳ</label>
            <select id="selSemester" class="form-select form-select-sm" style="width:120px">
                <option value="1">Học kỳ 1</option>
                <option value="2">Học kỳ 2</option>
            </select>
            <button id="btnLoadScores" class="btn btn-primary btn-sm ms-3">Lọc</button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">
                            <i class="bi bi-dash-square text-primary fs-5" style="cursor: pointer;"></i>
                        </th>
                        <th class="text-center" style="width: 60px;">STT</th>
                        <th>MÃ HS</th>
                        <th>MÔN HỌC</th>
                        <th class="text-center">ĐIỂM GIỮA KỲ</th>
                        <th class="text-center">ĐIỂM CUỐI KỲ</th>
                        <th class="text-center">TRUNG BÌNH<br>MÔN</th>
                        <th class="text-center">TÁC VỤ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center"><input class="form-check-input custom-checkbox" type="checkbox"></td>
                        <td class="text-center fw-bold">1</td>
                        <td class="text-secondary">K25110386</td>
                        <td class="text-secondary">Sinh học</td>
                        <td class="text-center text-secondary">8.5</td>
                        <td class="text-center text-secondary">9.0</td>
                        <td class="text-center text-secondary">9.0</td>
                        <td class="text-center action-icons">
                            <a href="#" class="me-3" title="Xem chi tiết">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            <a href="#" class="icon-download" title="Tải xuống">
                                <i class="bi bi-download"></i>
                            </a>
                        </td>
                    </tr>

                    <?php for ($i = 2; $i <= 8; $i++): ?>
                        <tr>
                            <td class="text-center"><input class="form-check-input custom-checkbox" type="checkbox"></td>
                            <td class="text-center fw-bold"><?php echo $i; ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer d-flex justify-content-between align-items-center bg-light p-2 border-top">
            <div id="paginationInfo" class="text-secondary ps-3 fw-bold fs-6">0-0/0 mục</div>
            <nav>
                <ul class="pagination mb-0 pe-3 gap-1">
                    <li class="page-item"><button id="btnPrevPage" class="page-link custom-page-btn" title="Trang trước"><i class="bi bi-chevron-left"></i></button></li>
                    <li class="page-item"><span id="pageNumber" class="page-link custom-page-number">0/0</span></li>
                    <li class="page-item"><button id="btnNextPage" class="page-link custom-page-btn" title="Trang sau"><i class="bi bi-chevron-right"></i></button></li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button class="btn btn-success-custom" id="btnExport">
            Xuất bảng điểm
        </button>
    </div>
    
    <!-- Modal chi tiết điểm môn -->
    <div class="modal fade" id="viewDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-light border-0">
                <div class="modal-header border-0 pb-0 pt-4 px-5">
                    <h2 class="modal-title fw-bold text-uppercase">CHI TIẾT ĐIỂM MÔN</h2>
                </div>
                <div class="modal-body pt-4 px-5 pb-5">
                    <form>
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 text-secondary fs-5">Môn học:</label>
                            <div class="col-md-9">
                                <div class="form-control-plaintext text-dark fw-bold fs-5" id="v_title_text"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-md-3 text-secondary fs-5 pt-2">Chi tiết điểm:</label>
                            <div class="col-md-9">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary">Điểm miệng</label>
                                        <input type="text" id="v_diem_mieng" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary">Điểm thi GK</label>
                                        <input type="text" id="v_diem_gk" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary">Điểm 1 tiết</label>
                                        <input type="text" id="v_diem_1tiet" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary">Điểm thi CK</label>
                                        <input type="text" id="v_diem_ck" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-dark px-4 py-2 text-uppercase fw-bold" data-bs-dismiss="modal" style="color: #0b1a48; border-color: #0b1a48;">
                                ĐÓNG
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>
    <!-- SheetJS for .xlsx export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?php require_once '../../footer.php'; ?>