<?php 
    require_once '../../config.php';
    $currentPage = 'tai-lieu'; 
    $pageCSS = ['TaiLieuHocTap.css'];
    require_once '../SidebarAndHeader.php';
    $pageJS = ['TaiLieuHocTap.js'];
?>

<main>
    <h1 class="page-title mb-4">TÀI LIỆU HỌC TẬP</h1>

    <div class="mb-4" style="max-width: 400px;">
        <select class="form-select py-2" id="subjectFilter" style="border-radius: 8px; border-color: #e0e0e0;">
            <option selected>Chọn môn học</option>
            <option value="Toan">Toán học</option>
            <option value="Van">Ngữ văn</option>
            <option value="Anh">Tiếng Anh</option>
        </select>
    </div>

    <div class="content-container bg-white p-0 border rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center"><i class="bi bi-dash-circle text-primary fs-5"></i></th>
                        <th style="width: 50px;">STT</th>
                        <th style="width: 20%;">TIÊU ĐỀ</th>
                        <th style="width: 25%;">MÔ TẢ</th>
                        <th>MÔN HỌC</th>
                        <th>GV GỬI</th>
                        <th class="text-center">TÁC VỤ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center"><input class="form-check-input rounded-circle" type="checkbox"></td>
                        <td>1</td>
                        <td class="fw-bold text-secondary">Tiêu đề 1</td>
                        <td class="text-secondary text-truncate" style="max-width: 200px;">Mô tả 1</td>
                        <td class="text-secondary">Môn học 1</td>
                        <td class="text-secondary">Nguyễn Văn A</td>
                        <td class="text-center action-icons">
                            <a href="#" class="btn-view-doc" 
                               data-title="Chương I – Bài 4: Tiêu đề bài 4"
                               data-desc="Nội dung đi sâu vào chuyên đề tính diện tích bề mặt của các hình khối không gian và ứng dụng trong các bài toán tối ưu hóa thực tế. Tập trung vào việc xác định lượng vật liệu cần thiết để tạo ra các vật thể như bể chứa, hộp đựng, hay các công trình kiến trúc."
                               data-img="https://img.freepik.com/free-vector/math-worksheet-template_1308-22392.jpg" 
                               data-bs-toggle="modal" data-bs-target="#viewDocModal">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </td>
                    </tr>

                    <?php for($i=2; $i<=8; $i++): ?>
                    <tr>
                        <td class="text-center"><input class="form-check-input rounded-circle" type="checkbox"></td>
                        <td><?php echo $i; ?></td>
                        <td></td><td></td><td></td><td></td><td></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button class="btn btn-primary px-4 py-2 fw-bold" style="background-color: #0b1a48; border-radius: 6px;">Tải về tài liệu</button>
    </div>


    <div class="modal fade" id="viewDocModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered"> <div class="modal-content p-4 border-0">
                
                <div class="modal-body">
                    <h3 class="fw-bold mb-3" id="m_title">Tiêu đề tài liệu</h3>
                    
                    <p class="text-secondary fw-bold mb-4" id="m_desc" style="text-align: justify;">
                        Nội dung mô tả...
                    </p>

                    <div class="preview-container bg-light p-3 rounded text-center border mb-4">
                        <img src="" id="m_image" class="img-fluid shadow-sm" style="max-height: 500px;" alt="Document Preview">
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <button type="button" class="btn btn-outline-dark px-4 py-2" data-bs-dismiss="modal">Quay lại</button>
                        <button type="button" class="btn btn-primary px-4 py-2" style="background-color: #0b1a48;">Tải về tài liệu</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

</main>
<?php require_once '../../footer.php'; ?>