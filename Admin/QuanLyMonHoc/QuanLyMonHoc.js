document.addEventListener("DOMContentLoaded", function() {
    
    // 1. XỬ LÝ NÚT "THÊM MÔN HỌC"
    const btnAdd = document.querySelector('.btn-add');
    btnAdd.addEventListener('click', function() {
        // Reset form về mặc định
        document.getElementById('subjectForm').reset();
        
        // Cập nhật giao diện theo mode "Thêm"
        document.getElementById('modalTitle').innerText = "THÊM MÔN HỌC";
        document.getElementById('btnSaveSubject').innerText = "+ Thêm mới";
        
        // Mở khóa ô Mã Môn
        document.getElementById('m_id').disabled = false;
        // Mặc định chọn 'Đang hoạt động'
        document.getElementById('statusActive').checked = true;
    });

    // 2. XỬ LÝ NÚT "SỬA" (Icon cây bút)
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Lấy dữ liệu từ các thuộc tính data
            const d = this.dataset;

            // Cập nhật giao diện theo mode "Sửa"
            document.getElementById('modalTitle').innerText = "CHỈNH SỬA MÔN HỌC";
            document.getElementById('btnSaveSubject').innerText = "Lưu thông tin";

            // Điền dữ liệu vào Form
            document.getElementById('m_year').value = d.year;
            document.getElementById('m_semester').value = d.semester;
            document.getElementById('m_id').value = d.id;
            document.getElementById('m_id').disabled = true; // Khóa ô mã môn
            
            document.getElementById('m_name').value = d.name;
            document.getElementById('m_head').value = d.head;
            document.getElementById('m_note').value = d.note;

            // Xử lý radio button trạng thái
            if (d.status === 'active') {
                document.getElementById('statusActive').checked = true;
            } else {
                document.getElementById('statusInactive').checked = true;
            }
        });
    });

    // 3. XỬ LÝ NÚT "XÓA" (Icon thùng rác)
    const deleteButtons = document.querySelectorAll('.delete-btn'); // Lưu ý class trong HTML là delete-btn
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const subjectId = this.dataset.id || this.dataset.subjectId;
            
            // Cập nhật nội dung thông báo xóa
            const msg = `Bạn chắc chắn muốn xóa môn học ${subjectId}?`;
            document.getElementById('deleteMsg').innerText = msg;
        });
    });
        // 4. XỬ LÝ NÚT "XÓA PHÂN CÔNG" (Nút đỏ to ở ngoài)
    const btnDeleteAll = document.querySelector('.btn-danger'); // Nút xóa ngoài bảng
    if(btnDeleteAll && !btnDeleteAll.closest('.modal')) { // Tránh nhầm với nút trong modal
         btnDeleteAll.addEventListener('click', function() {
             // Hiển thị modal xóa với thông báo số nhiều
             const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
             document.getElementById('deleteMsg').innerText = "Bạn chắc chắn muốn xóa các môn học này?";
             modal.show();
         });
    }
});