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

            // SET MODE = EDIT
            document.getElementById("mode").value = "edit";

            // SET maGV
            document.getElementById("maGV").value = d.id;

            // UI
            document.getElementById('modalTitle').innerText = "CHỈNH SỬA GIÁO VIÊN";
            document.getElementById('btnSaveTeacher').innerText = "Cập nhật";

            // Fill data
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
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            document.getElementById('deleteMsg').innerText = "Bạn chắc chắn muốn xóa các giáo viên này?";
            modal.show();
        });
    }
});