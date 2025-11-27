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
                        <th class="text-center">ĐIỂM THI<br>HỌC KÌ I</th>
                        <th class="text-center">ĐIỂM THI<br>HỌC KÌ II</th>
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
            <div class="text-secondary ps-3 fw-bold fs-6">1-4/18 mục</div>
            <nav>
                <ul class="pagination mb-0 pe-3 gap-1">
                    <li class="page-item"><button class="page-link custom-page-btn"><i class="bi bi-chevron-left"></i></button></li>
                    <li class="page-item"><span class="page-link custom-page-number">1/5</span></li>
                    <li class="page-item"><button class="page-link custom-page-btn"><i class="bi bi-chevron-right"></i></button></li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button class="btn btn-success-custom" id="btnExport">
            Xuất bảng điểm
        </button>
    </div>

</main>
<?php require_once '../../footer.php'; ?>