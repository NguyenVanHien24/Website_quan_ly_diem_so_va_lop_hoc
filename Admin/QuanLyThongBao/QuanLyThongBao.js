document.addEventListener("DOMContentLoaded", function() {
    
    // 1. XỬ LÝ NÚT "THÊM THÔNG BÁO"
    const btnAdd = document.querySelector('.btn-add-notify');
    if (btnAdd) {
        btnAdd.addEventListener('click', function() {
            document.getElementById('addForm').reset();
        });
    }

    // 2. XỬ LÝ NÚT "SỬA" (Icon bút chì)
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;
            
            document.getElementById('e_id').value = d.id;
            document.getElementById('e_title').value = d.title;
            document.getElementById('e_content').value = d.content;
            document.getElementById('e_date').value = d.date;
            
            // Chọn radio button dựa trên data-receiver
            if(d.receiver === 'all') document.getElementById('erx1').checked = true;
            if(d.receiver === 'teacher') document.getElementById('erx2').checked = true;
            if(d.receiver === 'student') document.getElementById('erx3').checked = true;
        });
    });

    // 3. XỬ LÝ NÚT "XEM" (Icon hộp mũi tên)
    const viewButtons = document.querySelectorAll('.btn-view');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;
            
            document.getElementById('v_id').value = d.id;
            document.getElementById('v_title').value = d.title;
            document.getElementById('v_content').value = d.content;
            document.getElementById('v_date').value = d.date;
            
            // Chọn radio button (readonly)
            if(d.receiver === 'all') document.getElementById('vrx1').checked = true;
            if(d.receiver === 'teacher') document.getElementById('vrx2').checked = true;
            if(d.receiver === 'student') document.getElementById('vrx3').checked = true;
        });
    });

    // 4. XỬ LÝ NÚT "XÓA" (Từng dòng)
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            // Cập nhật thông báo xóa đơn
            document.getElementById('deleteMsg').innerText = `Bạn chắc chắn muốn xóa thông báo ${id}?`;
        });
    });

    // 5. XỬ LÝ NÚT "XÓA THÔNG BÁO" (Nút đỏ lớn - Xóa nhiều)
    const btnDeleteMulti = document.getElementById('btnDeleteMulti');
    if(btnDeleteMulti) {
        btnDeleteMulti.addEventListener('click', function() {
            // Cập nhật thông báo xóa nhiều
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            document.getElementById('deleteMsg').innerText = "Bạn chắc chắn muốn xóa các thông báo này?";
            modal.show();
        });
    }
});