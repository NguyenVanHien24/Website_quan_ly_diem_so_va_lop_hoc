document.addEventListener("DOMContentLoaded", function () {

    // 1. XỬ LÝ NÚT "THÊM MÔN HỌC"
    const btnAdd = document.querySelector('.btn-add');
    btnAdd.addEventListener('click', function () {
        document.getElementById('subjectForm').reset();

        // Lấy mã môn mới
        const nextMaMon = this.dataset.nextId || document.getElementById('m_id').value;
        document.getElementById('m_id').value = nextMaMon;
        document.getElementById('m_id').disabled = true;

        document.getElementById('modalTitle').innerText = "THÊM MÔN HỌC";
        document.getElementById('btnSaveSubject').innerText = "+ Thêm mới";

        document.getElementById('statusActive').checked = true;

        // RESET dropdown trưởng bộ môn
        const dropdown = document.getElementById('m_head');
        dropdown.innerHTML = `<option value="">-- Chọn giáo viên --</option>`;
        // Khi thêm mới, để dropdown trống (hoặc có thể load sau khi nhập tên môn)
    });

    // 2. XỬ LÝ NÚT "SỬA" (ICON CÂY BÚT)
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;

            document.getElementById('modalTitle').innerText = "CHỈNH SỬA MÔN HỌC";
            document.getElementById('btnSaveSubject').innerText = "Lưu thông tin";

            document.getElementById('m_year').value = d.year;
            document.getElementById('m_semester').value = d.semester;
            document.getElementById('m_id').value = d.id;
            document.getElementById('m_id').disabled = true;

            document.getElementById('m_name').value = d.name;
            document.getElementById('m_note').value = d.note;

            if (d.status === 'active') {
                document.getElementById('statusActive').checked = true;
            } else {
                document.getElementById('statusInactive').checked = true;
            }

            const dropdown = document.getElementById('m_head');
            dropdown.innerHTML = `<option value="">-- Chọn giáo viên --</option>`;

            // Gọi API lấy giáo viên theo môn
            fetch('suaMonHoc.php?action=getTeachersBySubject', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `maMon=${d.id}`
            })
                .then(res => res.json())
                .then(data => {
                    dropdown.innerHTML = `<option value="">-- Chọn giáo viên --</option>`;

                    if (data.length === 0) {
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = '(Không có giáo viên)';
                        dropdown.appendChild(opt);
                    } else {
                        data.forEach(gv => {
                            const opt = document.createElement('option');
                            opt.value = gv.hoVaTen;
                            opt.textContent = gv.hoVaTen;
                            if (gv.hoVaTen === d.head) opt.selected = true;
                            dropdown.appendChild(opt);
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = '(Lỗi tải giáo viên)';
                    dropdown.appendChild(opt);
                });
        });
    });

    // 3. XÓA MỘT MÔN
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const maMon = this.dataset.id;
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            document.getElementById('deleteMsg').innerText =
                `Bạn chắc chắn muốn xóa môn học có mã môn là: ${maMon}?`;
            modal.show();

            // Khi xác nhận xóa
            const confirmBtn = document.querySelector('#deleteConfirmModal .btn-danger');
            confirmBtn.onclick = function () {
                fetch('xoaMonHoc.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `maMon=${maMon}`
                })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.msg);
                        if (data.status === 'success') location.reload();
                    })
                    .catch(err => {
                        alert("Có lỗi xảy ra khi xóa!");
                        console.error(err);
                    });
            }
        });
    });

    // 4. XÓA NHIỀU MÔN
    const btnDeleteAll = document.querySelector('.btn-danger');
    if (btnDeleteAll && !btnDeleteAll.closest('.modal')) {
        btnDeleteAll.addEventListener('click', function () {
            const checks = document.querySelectorAll('.table tbody .row-checkbox:checked');

            if (checks.length === 0) {
                alert("Vui lòng chọn ít nhất một môn học để xóa.");
                return;
            }

            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            document.getElementById('deleteMsg').innerText =
                `Bạn chắc chắn muốn xóa ${checks.length} môn học đã chọn?`;
            modal.show();

            const confirmBtn = document.querySelector('#deleteConfirmModal .btn-danger');
            confirmBtn.onclick = function () {
                const promises = Array.from(checks).map(cb => {
                    const maMon = cb.dataset.id || cb.value;
                    return fetch('xoaMonHoc.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `maMon=${maMon}`
                    }).then(res => res.json());
                });

                Promise.all(promises)
                    .then(results => {
                        alert("Xóa thành công " + results.filter(r => r.status === 'success').length + " môn học");
                        location.reload();
                    })
                    .catch(err => {
                        alert("Có lỗi xảy ra khi xóa các môn học!");
                        console.error(err);
                    });
            }
        });
    }

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

    // 5. LƯU DỮ LIỆU (THÊM / SỬA)
    const btnSave = document.getElementById('btnSaveSubject');
    btnSave.addEventListener('click', function () {
        const mId = document.getElementById('m_id').value;
        const tenMon = document.getElementById('m_name').value.trim();
        const truongBoMon = document.getElementById('m_head').value.trim();
        const moTa = document.getElementById('m_note').value.trim();
        const namHoc = document.getElementById('m_year').value;
        const hocKy = document.getElementById('m_semester').value;
        const trangThai = document.querySelector('input[name="status"]:checked').value;

        if (!tenMon) {
            alert("Tên môn học không được để trống!");
            return;
        }

        const isEdit = btnSave.innerText.includes("Lưu");
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
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.msg);
                    location.reload();
                } else {
                    alert(data.msg || "Đã có lỗi xảy ra!");
                }
            });
    });
});