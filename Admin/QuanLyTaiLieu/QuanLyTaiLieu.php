<?php 
    require_once '../../config.php';
    $currentPage = 'tai-lieu'; 
    $pageCSS = ['QuanLyTaiLieu.css'];
    require_once '../SidebarAndHeader.php';
    $pageJS = ['QuanLyTaiLieu.js'];
?>

<main>
    <h1 class="page-title">DANH SÁCH TÀI LIỆU</h1>

    <div class="filter-container py-4 px-4 mb-4">
        <div class="row g-4">
            <div class="col-md-6">
                <label for="filterClass" class="form-label fw-bold fs-5">Lớp:</label>
                <select class="form-select py-2" id="filterClass">
                    <option value="">Chọn lớp...</option>
                    <option value="11A4" selected>Lớp 11A4</option>
                    <option value="12A1">Lớp 12A1</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="filterSubject" class="form-label fw-bold fs-5">Môn:</label>
                <select class="form-select py-2" id="filterSubject">
                    <option value="">Chọn môn...</option>
                    <option value="Sinh học" selected>Sinh học</option>
                    <option value="Toán học">Toán học</option>
                </select>
            </div>
        </div>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th style="width: 25%;">TIÊU ĐỀ</th>
                        <th style="width: 30%;">MÔ TẢ</th>
                        <th>MÔN HỌC</th>
                        <th>NGƯỜI TẠO</th>
                        <th>TỪ KHÓA</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center fw-bold">1</td>
                        <td class="fw-bold text-dark">Giáo án Bài 5: Quang hợp</td>
                        <td class="text-secondary">Mô tả ngắn gọn nội dung bài giảng</td>
                        <td class="text-secondary">Sinh học</td>
                        <td class="text-secondary">Nguyễn Văn A</td>
                        <td class="text-secondary">Quang hợp</td>
                    </tr>

                    <?php for($i=2; $i<=7; $i++): ?>
                    <tr>
                        <td class="text-center fw-bold"><?php echo $i; ?></td>
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
        
        <div class="table-footer d-flex justify-content-between align-items-center mt-3 px-2">
            <div class="text-muted fw-bold text-secondary">1-4/18 mục</div>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">&lt;</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1/5</a></li>
                    <li class="page-item"><a class="page-link" href="#">&gt;</a></li>
                </ul>
            </nav>
        </div>
    </div>

</main>
<?php require_once '../../footer.php'; ?>