document.addEventListener("DOMContentLoaded", function () {

    const btnSave = document.getElementById("btnSaveClass");
    let isEditMode = false;
    let editMaLop = null;

    // 1. NÚT "THÊM LỚP"
    document.querySelector(".btn-add").addEventListener("click", function () {

        document.getElementById("classForm").reset();

        document.getElementById("modalTitle").innerText = "THÊM LỚP HỌC";
        btnSave.innerText = "+ Thêm mới";

        document.getElementById("c_id").disabled = false;
        document.getElementById("statusActive").checked = true;

        isEditMode = false;
        editMaLop = null;
    });

    // 2. NÚT "SỬA"
    document.querySelectorAll(".btn-edit").forEach(btn => {
        btn.addEventListener("click", function () {

            const d = this.dataset;

            document.getElementById('modalTitle').innerText = "CHỈNH SỬA LỚP HỌC";
            btnSave.innerText = "Lưu thông tin";

            document.getElementById('c_year').value = d.year;
            document.getElementById('c_semester').value = d.semester;
            document.getElementById('c_grade').value = d.grade;

            document.getElementById('c_id').value = d.id;
            document.getElementById('c_id').disabled = true;

            document.getElementById('c_name').value = d.name;
            document.getElementById('c_teacher').value = d.teacher;
            document.getElementById('c_count').value = d.count;

            if (d.status === "active") {
                document.getElementById("statusActive").checked = true;
            } else {
                document.getElementById("statusInactive").checked = true;
            }

            isEditMode = true;
            editMaLop = d.id;
        });
    });

    // 3. LƯU (THÊM / SỬA)
    btnSave.addEventListener("click", function () {

        const data = {
            tenLop: document.getElementById("c_name").value,
            khoiLop: document.getElementById("c_grade").value,
            siSo: document.getElementById("c_count").value,
            trangThai: document.querySelector("input[name='status']:checked").value,
            namHoc: document.getElementById("c_year").value,
            kyHoc: document.getElementById("c_semester").value,
            giaoVien: document.getElementById("c_teacher").value
        };

        if (isEditMode) {
            data.maLop = editMaLop; // chỉ gửi khi sửa
        }

        let api = isEditMode ? "suaLopHoc.php" : "themLopHoc.php";

        fetch(api, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams(data)
        })
            .then(res => res.json())
            .then(res => {

                if (res.status === "success") {
                    alert(isEditMode ? "Cập nhật lớp thành công!" : "Thêm lớp mới thành công!");
                    location.reload();
                } else {
                    alert("Lỗi: " + res.msg);
                }
            })
            .catch(err => alert("Lỗi hệ thống: " + err));
    });

    // 4. XÓA 1 LỚP
    document.querySelectorAll(".btn-delete").forEach(btn => {
        btn.addEventListener("click", function () {

            const maLop = this.dataset.id;

            document.getElementById("deleteMsg").innerText =
                `Bạn chắc chắn muốn xóa lớp có mã lớp là: ${maLop}?`;

            // Gán sự kiện cho nút XÓA trong modal
            document.getElementById("confirmDeleteBtn").onclick = function () {

                fetch("xoaLopHoc.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "ids[]=" + maLop
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === "success") {
                            alert("Đã xóa lớp!");
                            location.reload();
                        } else {
                            alert("Lỗi: " + res.msg);
                        }
                    })
                    .catch(err => alert("Lỗi hệ thống: " + err));
            };
        });
    });

    // 5. XÓA NHIỀU
    const btnDeleteAll = document.querySelector(".btn-danger:not(.modal .btn-danger)");

    if (btnDeleteAll) {

        btnDeleteAll.addEventListener("click", function () {

            const checked = document.querySelectorAll("tbody input[type='checkbox']:checked");

            if (checked.length === 0) {
                alert("Hãy chọn ít nhất 1 lớp để xóa!");
                return;
            }

            document.getElementById("deleteMsg").innerText =
                `Bạn chắc chắn muốn xóa ${checked.length} lớp đã chọn?`;

            const modal = new bootstrap.Modal(document.getElementById("deleteConfirmModal"));
            modal.show();

            document.getElementById("confirmDeleteBtn").onclick = function () {

                let formData = new URLSearchParams();

                checked.forEach(c => formData.append("ids[]", c.closest("tr").querySelector(".btn-delete").dataset.id));

                fetch("xoaLopHoc.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: formData
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === "success") {
                            alert("Đã xóa thành công!");
                            location.reload();
                        } else {
                            alert("Lỗi: " + res.msg);
                        }
                    })
                    .catch(err => alert("Lỗi hệ thống: " + err));
            };
        });
    }
});