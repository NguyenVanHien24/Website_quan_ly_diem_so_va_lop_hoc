document.addEventListener("DOMContentLoaded", function() {
    
    // 1. XỬ LÝ NÚT "THÊM GIÁO VIÊN"
    // --------------------------------------
    const btnAdd = document.querySelector('.btn-add');
    btnAdd.addEventListener('click', function() {
        // Reset form về rỗng
        document.getElementById('teacherForm').reset();
        
        // Đổi tiêu đề và nút thành "Thêm mới"
        document.getElementById('modalTitle').innerText = "THÊM GIÁO VIÊN";
        document.getElementById('btnSaveTeacher').innerText = "+ Thêm mới";
    });

    // 2. XỬ LÝ NÚT "SỬA GIÁO VIÊN"
    // --------------------------------------
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Lấy data từ nút bấm
            const d = this.dataset;

            // Đổi tiêu đề thành "Chỉnh sửa"
            document.getElementById('modalTitle').innerText = "CHỈNH SỬA GIÁO VIÊN";
            document.getElementById('btnSaveTeacher').innerText = "Cập nhật"; // Hoặc "+ Thêm mới" nếu muốn giữ nguyên text như ảnh

            // Điền dữ liệu vào Form
            document.getElementById('t_name').value = d.name;
            document.getElementById('t_email').value = d.email;
            document.getElementById('t_phone').value = d.phone;
            document.getElementById('t_dept').value = d.dept;
            document.getElementById('t_gender').value = d.gender;
            document.getElementById('t_degree').value = d.degree;
            document.getElementById('t_office').value = d.office;

            // Xử lý radio button trạng thái
            if (d.status === 'active') {
                document.getElementById('statusActive').checked = true;
            } else {
                document.getElementById('statusInactive').checked = true;
            }
        });
    });

    // 3. XỬ LÝ MODAL XÓA
    // --------------------------------------
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const teacherId = this.dataset.id;
            const msg = `Bạn chắc chắn muốn xóa giáo viên ${teacherId}?`;
            document.getElementById('deleteMsg').innerText = msg;
        });
    });
        // 4. XỬ LÝ NÚT "XÓA PHÂN CÔNG" (Nút đỏ to ở ngoài)
    const btnDeleteAll = document.querySelector('.btn-danger'); // Nút xóa ngoài bảng
    if(btnDeleteAll && !btnDeleteAll.closest('.modal')) { // Tránh nhầm với nút trong modal
         btnDeleteAll.addEventListener('click', function() {
             // Hiển thị modal xóa với thông báo số nhiều
             const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
             document.getElementById('deleteMsg').innerText = "Bạn chắc chắn muốn xóa các giáo viên này?";
             modal.show();
         });
    }
});