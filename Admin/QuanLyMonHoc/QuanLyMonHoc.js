document.addEventListener("DOMContentLoaded", function () {

    // 1. XỬ LÝ NÚT "THÊM MÔN HỌC"
    const btnAdd = document.querySelector('.btn-add');
    btnAdd.addEventListener('click', function () {
        // Reset form về mặc định
        document.getElementById('subjectForm').reset();

        // Lấy mã môn mới từ data attribute trên button (hoặc từ PHP nếu cần)
        const nextMaMon = this.dataset.nextId || document.getElementById('m_id').value;
        document.getElementById('m_id').value = nextMaMon;
        document.getElementById('m_id').disabled = true; // khóa ô mã môn, người dùng không sửa

        // Cập nhật giao diện theo mode "Thêm"
        document.getElementById('modalTitle').innerText = "THÊM MÔN HỌC";
        document.getElementById('btnSaveSubject').innerText = "+ Thêm mới";

        // Mặc định chọn 'Đang hoạt động'
        document.getElementById('statusActive').checked = true;
    });

    // 2. XỬ LÝ NÚT "SỬA" (Icon cây bút)
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;

            // Cập nhật giao diện theo mode "Sửa"
            document.getElementById('modalTitle').innerText = "CHỈNH SỬA MÔN HỌC";
            document.getElementById('btnSaveSubject').innerText = "Lưu thông tin";

            // Điền dữ liệu vào Form
            document.getElementById('m_year').value = d.year;
            document.getElementById('m_semester').value = d.semester;
            document.getElementById('m_id').value = d.id;
            document.getElementById('m_id').disabled = true; // khóa ô mã môn

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
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const subjectId = this.dataset.id || this.dataset.subjectId;
            const msg = `Bạn chắc chắn muốn xóa môn học ${subjectId}?`;
            document.getElementById('deleteMsg').innerText = msg;
        });
    });

    // 4. XỬ LÝ NÚT "XÓA NHIỀU MÔN HỌC" (Nút đỏ to ở ngoài bảng)
    const btnDeleteAll = document.querySelector('.btn-danger'); // Nút xóa ngoài bảng
    if (btnDeleteAll && !btnDeleteAll.closest('.modal')) {
        btnDeleteAll.addEventListener('click', function () {
            // Lấy tất cả checkbox được chọn
            const checkboxes = document.querySelectorAll('.table tbody input.form-check-input:checked');
            if (checkboxes.length === 0) {
                alert("Vui lòng chọn ít nhất một môn học để xóa.");
                return;
            }

            // Hiển thị modal xóa với thông báo số nhiều
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            document.getElementById('deleteMsg').innerText = `Bạn chắc chắn muốn xóa ${checkboxes.length} môn học đã chọn?`;
            modal.show();
        });
    }
    // 5. XỬ LÝ NÚT "THÊM MỚI / LƯU THÔNG TIN" bằng AJAX
    const btnSave = document.getElementById('btnSaveSubject');
    btnSave.addEventListener('click', function() {
        const mId = document.getElementById('m_id').value;
        const tenMon = document.getElementById('m_name').value.trim();
        const truongBoMon = document.getElementById('m_head').value.trim();
        const moTa = document.getElementById('m_note').value.trim();
        const namHoc = document.getElementById('m_year').value;
        const hocKy = document.getElementById('m_semester').value;
        const trangThai = document.querySelector('input[name="status"]:checked').value;

        if(!tenMon) {
            alert("Tên môn học không được để trống!");
            return;
        }

        // Xác định mode: thêm mới hay sửa
        const isEdit = btnSave.innerText.includes('Lưu');

        const url = isEdit ? 'suaMonHoc.php' : 'themMonHoc.php';
        const formData = new URLSearchParams();
        formData.append('maMon', mId);
        formData.append('tenMon', tenMon);
        formData.append('truongBoMon', truongBoMon);
        formData.append('moTa', moTa);
        formData.append('namHoc', namHoc);
        formData.append('hocKy', hocKy);
        formData.append('trangThai', trangThai);

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                alert(data.msg);
                location.reload(); // load lại bảng để cập nhật dữ liệu
            } else {
                alert(data.msg || "Đã có lỗi xảy ra!");
            }
        })
        .catch(err => console.error(err));
    });
});