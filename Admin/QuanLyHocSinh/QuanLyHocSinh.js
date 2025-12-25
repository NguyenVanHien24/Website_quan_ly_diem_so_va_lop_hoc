document.addEventListener("DOMContentLoaded", function () {
    // 1. XỬ LÝ NÚT "THÊM HỌC SINH"
    const btnAdd = document.querySelector('.btn-add');
    btnAdd.addEventListener('click', function () {
        document.getElementById('studentForm').reset();
        document.getElementById('modalTitle').innerText = "THÊM HỌC SINH";
        document.getElementById('btnSaveStudent').innerText = "+ Thêm mới";
        document.getElementById('s_id').disabled = false;
        document.getElementById('statusActive').checked = true;
    });

    // 2. XỬ LÝ NÚT "SỬA"
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function () {
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
    document.getElementById('btnSaveStudent').addEventListener('click', function () {
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
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    alert(action === 'add' ? 'Thêm học sinh thành công!' : 'Cập nhật thành công!');
                    location.reload();
                } else {
                    alert(res.error || 'Có lỗi xảy ra!');
                }
            })
            .catch(err => alert('Lỗi mạng hoặc server: ' + err));
    });

    // 4. XỬ LÝ NÚT "XÓA"
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const studentId = this.dataset.id;
            document.getElementById('deleteMsg').innerText = `Bạn chắc chắn muốn xóa học sinh có mã học sinh là ${studentId}?`;

            const confirmBtn = document.querySelector('#deleteConfirmModal .btn-danger');
            confirmBtn.onclick = function () {
                fetch('QuanLyHocSinh.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=delete&id=${studentId}`
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            alert('Xóa học sinh thành công!');
                            location.reload();
                        } else {
                            alert(res.error || 'Xóa thất bại!');
                        }
                    });
            };
        });
    });

    // 5. XỬ LÝ NÚT "XÓA NHIỀU"
    const btnDeleteAll = document.querySelector('.btn-danger');
    if (btnDeleteAll && !btnDeleteAll.closest('.modal')) {
        btnDeleteAll.addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            document.getElementById('deleteMsg').innerText = "Bạn chắc chắn muốn xóa các học sinh này?";
            modal.show();

            const confirmBtn = document.querySelector('#deleteConfirmModal .btn-danger');
            confirmBtn.onclick = function () {
                const selected = Array.from(document.querySelectorAll('tbody input[type=checkbox]:checked'))
                    .map(cb => cb.closest('tr').querySelector('.btn-delete').dataset.id);

                if (selected.length === 0) {
                    alert('Chưa chọn học sinh nào!');
                    return;
                }

                // Xóa tuần tự từng học sinh rồi reload
                (async () => {
                    for (const id of selected) {
                        const res = await fetch('QuanLyHocSinh.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `action=delete&id=${id}`
                        }).then(r => r.json());
                        if (!res.success) {
                            alert(`Xóa học sinh ${id} thất bại: ${res.error || ''}`);
                        } else {
                            alert('Xóa học sinh thành công!');
                            location.reload();
                        }
                    }
                    location.reload();
                })();
            };
        });

    // 6. CHECKBOXES
    const checkAll = document.getElementById('checkAll');
    const rowCheckboxes = Array.from(document.querySelectorAll('.row-checkbox'));
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = checkAll.checked);
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (!this.checked && checkAll && checkAll.checked) checkAll.checked = false;
            if (checkAll) {
                const allChecked = rowCheckboxes.every(r => r.checked);
                checkAll.checked = allChecked;
            }
        });
    });
    }
});
