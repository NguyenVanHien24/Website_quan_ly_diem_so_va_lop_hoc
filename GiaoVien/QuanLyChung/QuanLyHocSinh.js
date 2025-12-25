document.addEventListener("DOMContentLoaded", function() {
    // 1. XỬ LÝ NÚT "THÊM HỌC SINH"
    const btnAdd = document.querySelector('.btn-add');
    
    btnAdd.addEventListener('click', function() {
        // Reset form về rỗng
        document.getElementById('studentForm').reset();
        
        // Cập nhật giao diện theo mode "Thêm"
        document.getElementById('modalTitle').innerText = "THÊM HỌC SINH";
        document.getElementById('btnSaveStudent').innerText = "+ Thêm mới";
        
        // Mở khóa ô Mã HS (vì thêm mới được nhập mã)
        document.getElementById('s_id').disabled = false;
        // Mặc định chọn 'Đang học'
        document.getElementById('statusActive').checked = true;
        
    });

    // 2. XỬ LÝ NÚT "SỬA" (Icon cây bút)
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;

            // Cập nhật giao diện theo mode "Sửa"
            document.getElementById('modalTitle').innerText = "CHỈNH SỬA HỌC SINH";
            document.getElementById('btnSaveStudent').innerText = "Lưu thông tin";

            // Điền dữ liệu vào Form
            document.getElementById('s_id').value = d.id;
            document.getElementById('s_id').disabled = true; // Không cho sửa Mã HS
            
            document.getElementById('s_name').value = d.name;
            document.getElementById('s_email').value = d.email;
            document.getElementById('s_phone').value = d.phone;
            document.getElementById('s_gender').value = d.gender;
            document.getElementById('s_class').value = d.class;
            document.getElementById('s_role').value = d.role;

            // Xử lý radio button trạng thái
            if (d.status === 'active') {
                document.getElementById('statusActive').checked = true;
            } else {
                document.getElementById('statusInactive').checked = true;
            }
        });
    });

    // 3. XỬ LÝ NÚT "XÓA" (Icon thùng rác)
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const studentId = this.dataset.id;
            // Cập nhật nội dung câu hỏi xóa
            const msg = `Bạn chắc chắn muốn xóa học sinh ${studentId}?`;
            document.getElementById('deleteMsg').innerText = msg;
        });
    });

        // 4. XỬ LÝ NÚT "XÓA PHÂN CÔNG" (Nút đỏ to ở ngoài)
    const btnDeleteAll = document.querySelector('.btn-danger'); // Nút xóa ngoài bảng
    if(btnDeleteAll && !btnDeleteAll.closest('.modal')) { // Tránh nhầm với nút trong modal
         btnDeleteAll.addEventListener('click', function() {
             // Hiển thị modal xóa với thông báo số nhiều
             const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
             document.getElementById('deleteMsg').innerText = "Bạn chắc chắn muốn xóa các học sinh này?";
             modal.show();
         });
    }
});