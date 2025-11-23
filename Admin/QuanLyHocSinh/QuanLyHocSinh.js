document.addEventListener("DOMContentLoaded", function() {
    // 1. XỬ LÝ NÚT "THÊM HỌC SINH"
    const btnAdd = document.querySelector('.btn-add');
    
    btnAdd.addEventListener('click', function() {
        // Reset form về rỗng
        document.getElementById('studentForm').reset();
        
        // Cập nhật giao diện theo mode "Thêm"
        document.getElementById('modalTitle').innerText = "THÊM HỌC SINH";
        document.getElementById('btnSaveStudent').innerText = "+ Thêm mới";
        
        // Mở khóa ô Mã HS
        document.getElementById('s_id').disabled = false;
        document.getElementById('statusActive').checked = true;
    });

    // 2. XỬ LÝ NÚT "SỬA" (Icon cây bút)
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;

            document.getElementById('modalTitle').innerText = "CHỈNH SỬA HỌC SINH";
            document.getElementById('btnSaveStudent').innerText = "Lưu thông tin";

            document.getElementById('s_id').value = d.id;
            document.getElementById('s_id').disabled = true;
            document.getElementById('s_name').value = d.name;
            document.getElementById('s_email').value = d.email;
            document.getElementById('s_phone').value = d.phone;
            document.getElementById('s_gender').value = d.gender;
            document.getElementById('s_class').value = d.class;
            document.getElementById('s_role').value = d.role;

            if (d.status === 'Hoạt động') {
                document.getElementById('statusActive').checked = true;
            } else {
                document.getElementById('statusInactive').checked = true;
            }
        });
    });

    // 3. XỬ LÝ AJAX THÊM/SỬA
    document.getElementById('btnSaveStudent').addEventListener('click', function() {
        // const id = document.getElementById('s_id').value;
        const name = document.getElementById('s_name').value;
        const email = document.getElementById('s_email').value;
        const phone = document.getElementById('s_phone').value;
        const gender = document.getElementById('s_gender').value;
        const role = document.getElementById('s_role').value;
        const className = document.getElementById('s_class').value;
        const status = document.querySelector('input[name="status"]:checked').value;

        const action = this.innerText.includes('Thêm') ? 'add' : 'update';
        const id = action === 'update' ? document.getElementById('s_id').value : '';

        fetch('QuanLyHocSinh.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=${action}&id=${id}&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}&gender=${encodeURIComponent(gender)}&role=${encodeURIComponent(role)}&class=${encodeURIComponent(className)}&status=${encodeURIComponent(status)}`
        }).then(res => res.json()).then(res => {
            if(res.success) location.reload();
            else alert(res.error || 'Lỗi!');
        });
    });

    // 4. XỬ LÝ NÚT "XÓA"
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const studentId = this.dataset.id;
            document.getElementById('deleteMsg').innerText = `Bạn chắc chắn muốn xóa học sinh có mã học sinh là ${studentId}?`;

            const confirmBtn = document.querySelector('#deleteConfirmModal .btn-danger');
            confirmBtn.onclick = function() {
                fetch('QuanLyHocSinh.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=delete&id=${studentId}`
                }).then(res => res.json()).then(res => {
                    if(res.success) location.reload();
                    else alert('Xóa thất bại!');
                });
            };
        });
    });

    // 5. XỬ LÝ NÚT "XÓA NHIỀU" (nút ngoài bảng)
    const btnDeleteAll = document.querySelector('.btn-danger'); 
    if(btnDeleteAll && !btnDeleteAll.closest('.modal')) {
         btnDeleteAll.addEventListener('click', function() {
             const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
             document.getElementById('deleteMsg').innerText = "Bạn chắc chắn muốn xóa các học sinh này?";
             modal.show();

             const confirmBtn = document.querySelector('#deleteConfirmModal .btn-danger');
             confirmBtn.onclick = function() {
                 const selected = Array.from(document.querySelectorAll('tbody input[type=checkbox]:checked'))
                                       .map(cb => cb.closest('tr').querySelector('.btn-delete').dataset.id);

                 if(selected.length === 0) {
                     alert('Chưa chọn học sinh nào!');
                     return;
                 }

                 selected.forEach(id => {
                     fetch('QuanLyHocSinh.php', {
                         method: 'POST',
                         headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                         body: `action=delete&id=${id}`
                     }).then(res => res.json()).then(res => {
                         if(res.success) location.reload();
                     });
                 });
             };
         });
    }
});
