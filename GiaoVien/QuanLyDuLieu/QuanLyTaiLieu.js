document.addEventListener("DOMContentLoaded", function() {
    
    // 1. XỬ LÝ NÚT "THÊM TÀI LIỆU"
    const btnAdd = document.querySelector('.btn-add-doc');
    if(btnAdd) {
        btnAdd.addEventListener('click', function() {
            // Reset form
            document.getElementById('docForm').reset();
            
            // Cập nhật giao diện theo mode "Thêm"
            document.getElementById('modalTitle').innerText = "THÊM TÀI LIỆU";
            document.getElementById('btnSaveDoc').innerText = "Thêm mới";
            
            // Mặc định chọn Công khai
            document.getElementById('statusPublic').checked = true;
        });
    }

    // 2. XỬ LÝ NÚT "SỬA" (Icon bút)
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;

            // Đổi tiêu đề và nút lưu
            document.getElementById('modalTitle').innerText = "CHỈNH SỬA TÀI LIỆU";
            document.getElementById('btnSaveDoc').innerText = "Lưu thông tin";

            // Điền dữ liệu vào form
            document.getElementById('d_title').value = d.title;
            document.getElementById('d_desc').value = d.desc;
            document.getElementById('d_subject').value = d.subject;

            // Xử lý trạng thái
            if (d.status === 'public') {
                document.getElementById('statusPublic').checked = true;
            } else {
                document.getElementById('statusPrivate').checked = true;
            }
        });
    });

    // 3. XỬ LÝ NÚT "XÓA" (Icon thùng rác - Xóa đơn)
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const docId = this.dataset.id;
            // Cập nhật thông báo xóa đơn
            const msg = `Bạn chắc chắn muốn xóa<br>tài liệu ${docId} không?`;
            document.getElementById('deleteMsg').innerHTML = msg;
        });
    });

    // 4. XỬ LÝ NÚT "XÓA TÀI LIỆU" (Nút đỏ to - Xóa nhiều)
    const btnDeleteMulti = document.getElementById('btnDeleteMulti');
    if(btnDeleteMulti) {
         btnDeleteMulti.addEventListener('click', function() {
             // Hiển thị modal xóa với thông báo số nhiều
             const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
             document.getElementById('deleteMsg').innerHTML = "Bạn chắc chắn muốn xóa<br>các tài liệu đã chọn?";
             modal.show();
         });
    }
    // XỬ LÝ CHỌN FILE UPLOAD
    // ==========================================
    const btnUpload = document.getElementById('btnUploadTrigger'); // Nút bấm
    const realFile = document.getElementById('realFileInput');     // Input ẩn
    const fileName = document.getElementById('fileNameDisplay');   // Ô hiện tên

    if (btnUpload && realFile) {
        // 1. Khi bấm nút "Tải lên" -> Kích hoạt click vào input file ẩn
        btnUpload.addEventListener('click', function() {
            realFile.click();
        });

        // 2. Khi người dùng chọn file xong -> Lấy tên file hiển thị ra ô input
        realFile.addEventListener('change', function() {
            if (realFile.files.length > 0) {
                // Lấy tên file đầu tiên được chọn
                fileName.value = realFile.files[0].name; 
            } else {
                fileName.value = "";
            }
        });
    }
});