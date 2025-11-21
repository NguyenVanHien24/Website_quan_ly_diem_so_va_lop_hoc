document.addEventListener("DOMContentLoaded", function() {
    
    // 1. XỬ LÝ NÚT "THÊM LỚP"
    const btnAdd = document.querySelector('.btn-add');
    btnAdd.addEventListener('click', function() {
        document.getElementById('classForm').reset();
        
        // Cập nhật text theo ảnh
        document.getElementById('modalTitle').innerText = "THÊM LỚP HỌC";
        document.getElementById('btnSaveClass').innerText = "+ Thêm mới";
        
        document.getElementById('c_id').disabled = false;
        document.getElementById('statusActive').checked = true;
    });

    // 2. XỬ LÝ NÚT "SỬA"
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;

            // Cập nhật text theo ảnh
            document.getElementById('modalTitle').innerText = "CHỈNH SỬA LỚP HỌC";
            document.getElementById('btnSaveClass').innerText = "Lưu thông tin";

            // Điền dữ liệu
            document.getElementById('c_year').value = d.year;
            document.getElementById('c_semester').value = d.semester;
            document.getElementById('c_grade').value = d.grade; // Điền khối
            document.getElementById('c_id').value = d.id;
            document.getElementById('c_id').disabled = true; 
            
            document.getElementById('c_name').value = d.name;
            document.getElementById('c_teacher').value = d.teacher;
            document.getElementById('c_count').value = d.count;

            if (d.status === 'active') {
                document.getElementById('statusActive').checked = true;
            } else {
                document.getElementById('statusInactive').checked = true;
            }
        });
    });

    // 3. XỬ LÝ NÚT "XÓA"
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const classId = this.dataset.classId || this.dataset.id;
            // Cập nhật text theo ảnh
            const msg = `Bạn chắc chắn muốn xóa lớp ${classId}?`;
            document.getElementById('deleteMsg').innerText = msg;
        });
    });
        // 4. XỬ LÝ NÚT "XÓA PHÂN CÔNG" (Nút đỏ to ở ngoài)
    const btnDeleteAll = document.querySelector('.btn-danger'); // Nút xóa ngoài bảng
    if(btnDeleteAll && !btnDeleteAll.closest('.modal')) { // Tránh nhầm với nút trong modal
         btnDeleteAll.addEventListener('click', function() {
             // Hiển thị modal xóa với thông báo số nhiều
             const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
             document.getElementById('deleteMsg').innerText = "Bạn chắc chắn muốn xóa các lớp học này?";
             modal.show();
         });
    }
});