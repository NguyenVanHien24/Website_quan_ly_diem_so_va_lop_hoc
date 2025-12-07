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

    // 5. Khi thay đổi môn, lấy danh sách giáo viên dạy môn đó
    const subjectSelect = document.getElementById('pc_subject');
    const teacherSelect = document.getElementById('pc_teacher');
    const btnSave = document.getElementById('btnSaveAssign');

    function clearTeachers() {
        if (!teacherSelect) return;
        teacherSelect.innerHTML = '<option value="">Chọn giáo viên...</option>';
    }

    if (subjectSelect) {
        subjectSelect.addEventListener('change', function() {
            const maMon = this.value;
            if (!maMon) { clearTeachers(); return; }
            fetch('get_teachers.php?maMon=' + encodeURIComponent(maMon))
                .then(r => r.json())
                .then(data => {
                    if (!teacherSelect) return;
                    clearTeachers();
                    if (data.success && Array.isArray(data.data)) {
                        data.data.forEach(t => {
                            const opt = document.createElement('option');
                            opt.value = t.maGV;
                            opt.textContent = t.hoVaTen;
                            teacherSelect.appendChild(opt);
                        });
                    } else {
                        console.error('get_teachers error', data.message);
                    }
                })
                .catch(e => console.error('Fetch teachers error', e));
        });
    }

    // 6. Lưu phân công
    if (btnSave) {
        btnSave.addEventListener('click', function() {
            const maLop = document.getElementById('pc_class') ? document.getElementById('pc_class').value : '';
            const maMon = document.getElementById('pc_subject') ? document.getElementById('pc_subject').value : '';
            const maGV = document.getElementById('pc_teacher') ? document.getElementById('pc_teacher').value : '';
            if (!maLop || !maMon || !maGV) {
                alert('Vui lòng chọn đầy đủ lớp, môn và giáo viên');
                return;
            }
            fetch('save_assignment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `maLop=${encodeURIComponent(maLop)}&maMon=${encodeURIComponent(maMon)}&maGV=${encodeURIComponent(maGV)}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    try { var modal = bootstrap.Modal.getInstance(document.getElementById('assignFormModal')); if (modal) modal.hide(); } catch(e) {}
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(e => { console.error(e); alert('Lỗi khi lưu phân công'); });
        });
    }
});