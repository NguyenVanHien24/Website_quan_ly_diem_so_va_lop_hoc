document.addEventListener("DOMContentLoaded", function() {
    
    // 1. XỬ LÝ NÚT "THÊM LỚP"
    const btnAdd = document.querySelector('.btn-add');
    btnAdd.addEventListener('click', function() {
        document.getElementById('classForm').reset();
        
        // Cập nhật text theo ảnh
        document.getElementById('modalTitle').innerText = "THÊM LỚP HỌC";
        const btnSave = document.getElementById('btnSaveClass');
        if (btnSave) { btnSave.innerText = "+ Thêm mới"; btnSave.style.display = ''; }
        
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
            const btnSave = document.getElementById('btnSaveClass');
            if (btnSave) { btnSave.innerText = "Lưu thông tin"; btnSave.style.display = ''; }

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

    // 2b. XỬ LÝ NÚT "XEM" - mở modal giống sửa nhưng ở chế độ read-only
    const viewButtons = document.querySelectorAll('.btn-view');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const d = this.dataset;

            document.getElementById('modalTitle').innerText = "XEM LỚP HỌC";
            // Hide/disable save button
            const btnSave = document.getElementById('btnSaveClass');
            if (btnSave) btnSave.style.display = 'none';

            if (document.getElementById('c_year')) document.getElementById('c_year').value = d.year || document.getElementById('c_year').value;
            if (document.getElementById('c_semester')) document.getElementById('c_semester').value = d.semester || document.getElementById('c_semester').value;
            if (document.getElementById('c_grade')) document.getElementById('c_grade').value = d.grade || document.getElementById('c_grade').value;
            if (document.getElementById('c_id')) document.getElementById('c_id').value = d.id || '';
            if (document.getElementById('c_name')) document.getElementById('c_name').value = d.name || '';
            if (document.getElementById('c_teacher')) document.getElementById('c_teacher').value = d.teacher || '';
            if (document.getElementById('c_count')) document.getElementById('c_count').value = d.count || '';

            ['c_year','c_semester','c_grade','c_id','c_name','c_teacher','c_count'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.disabled = true;
            });
            if (document.getElementById('statusActive')) document.getElementById('statusActive').disabled = true;
            if (document.getElementById('statusInactive')) document.getElementById('statusInactive').disabled = true;

            if (d.status === 'active') {
                if (document.getElementById('statusActive')) document.getElementById('statusActive').checked = true;
            } else {
                if (document.getElementById('statusInactive')) document.getElementById('statusInactive').checked = true;
            }

            try {
                const modalEl = document.getElementById('classFormModal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            } catch (e) {
                // ignore
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