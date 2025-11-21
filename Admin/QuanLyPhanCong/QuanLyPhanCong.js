document.addEventListener("DOMContentLoaded", function() {
    
    // 1. XỬ LÝ NÚT "THÊM PHÂN CÔNG"
    const btnAdd = document.querySelector('.btn-add-assign');
    if (btnAdd) {
        btnAdd.addEventListener('click', function() {
            // Reset form
            document.getElementById('assignForm').reset();
            
            // Đổi tiêu đề thành THÊM
            document.getElementById('modalTitle').innerText = "THÊM PHÂN CÔNG";
            document.getElementById('btnSaveAssign').innerText = "LƯU";
        });
    }

    // 2. XỬ LÝ NÚT "SỬA" (Icon bút chì)
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;

            // Đổi tiêu đề thành CHỈNH SỬA
            document.getElementById('modalTitle').innerText = "CHỈNH SỬA PHÂN CÔNG";
            document.getElementById('btnSaveAssign').innerText = "LƯU";

            // Điền dữ liệu
            document.getElementById('pc_class').value = d.class;
            document.getElementById('pc_subject').value = d.subject;
            document.getElementById('pc_teacher').value = d.teacher;
        });
    });

    // 3. XỬ LÝ NÚT "XÓA" (Icon thùng rác)
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Cập nhật thông báo xóa
            const msg = "Bạn chắc chắn muốn xóa phân công này?";
            document.getElementById('deleteMsg').innerText = msg;
        });
    });
    
    // 4. XỬ LÝ NÚT "XÓA PHÂN CÔNG" (Nút đỏ to ở ngoài)
    const btnDeleteAll = document.querySelector('.btn-danger'); // Nút xóa ngoài bảng
    if(btnDeleteAll && !btnDeleteAll.closest('.modal')) { // Tránh nhầm với nút trong modal
         btnDeleteAll.addEventListener('click', function() {
             // Hiển thị modal xóa với thông báo số nhiều
             const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
             document.getElementById('deleteMsg').innerText = "Bạn chắc chắn muốn xóa các phân công này?";
             modal.show();
         });
    }
});