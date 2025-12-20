document.addEventListener("DOMContentLoaded", function () {

    // 1. XỬ LÝ NÚT "THÊM GIÁO VIÊN"
    const btnAdd = document.querySelector('.btn-add');
    btnAdd.addEventListener('click', function () {

        document.getElementById('teacherForm').reset();

        // Reset hidden fields
        document.getElementById('maGV').value = "";
        document.getElementById('mode').value = "add";

        // UI
        document.getElementById('modalTitle').innerText = "THÊM GIÁO VIÊN";
        document.getElementById('btnSaveTeacher').innerText = "+ Thêm mới";
    });


    // 2. XỬ LÝ NÚT "SỬA GIÁO VIÊN"
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function () {

            const d = this.dataset;

            document.getElementById("mode").value = "edit";

            document.getElementById("maGV").value = d.id;

            document.getElementById('modalTitle').innerText = "CHỈNH SỬA GIÁO VIÊN";
            document.getElementById('btnSaveTeacher').innerText = "Cập nhật";

            document.getElementById('t_name').value = d.name;
            document.getElementById('t_email').value = d.email;
            document.getElementById('t_phone').value = d.phone;
            document.getElementById('t_dept').value = d.dept;
            document.getElementById('t_gender').value = d.gender;
            document.getElementById('t_degree').value = d.degree;
            document.getElementById('t_office').value = d.office;

            // Trạng thái
            if (d.status === 'Hoạt động') {
                document.getElementById('statusActive').checked = true;
            } else {
                document.getElementById('statusInactive').checked = true;
            }
        });
    });


    // 3. XỬ LÝ MODAL XÓA
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const teacherId = this.dataset.id;

            document.getElementById('deleteMsg').innerText =
                `Bạn chắc chắn muốn xóa giáo viên có mã ${teacherId}?`;

            document.getElementById('delete_id').value = teacherId;
        });
    });


    // 4. XÓA NHIỀU
    const btnDeleteAll = document.querySelector('.btn-danger');
    if (btnDeleteAll && !btnDeleteAll.closest('.modal')) {
        btnDeleteAll.addEventListener('click', function () {
            const selected = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
            if (selected.length === 0) {
                alert('Vui lòng chọn ít nhất một giáo viên để xóa.');
                return;
            }

            document.getElementById('delete_id').value = selected.join(',');
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            document.getElementById('deleteMsg').innerText = "Bạn chắc chắn muốn xóa các giáo viên này?";
            modal.show();
        });
    }

    // 5. CHECKBOXES
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
});