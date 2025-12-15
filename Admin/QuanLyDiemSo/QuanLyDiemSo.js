document.addEventListener("DOMContentLoaded", function () {

    let currentRow = null;

    const upperName = (str) => str ? str.toUpperCase() : "";

    // Lấy instance của Bootstrap Modal (cần thiết để đóng modal sau khi submit)
    const gradeEntryModalEl = document.getElementById("gradeEntryModal");
    let gradeEntryModal = null;
    if (gradeEntryModalEl) {
        // Khởi tạo Modal instance một lần
        gradeEntryModal = new bootstrap.Modal(gradeEntryModalEl);
    }
    
    // ==============================
    // XỬ LÝ VIEW (XEM CHI TIẾT)
    // ==============================
    document.querySelectorAll(".btn-view").forEach(btn => {
        btn.addEventListener("click", function () {
            const d = this.dataset;

            document.getElementById("view_student_name").innerText =
                "HỌ TÊN HỌC SINH: " + upperName(d.ten);

            document.getElementById("view_student_id").innerText =
                "MÃ HỌC SINH: " + d.hs;

            // HK1
            document.getElementById("view_s1_mouth").value = this.getAttribute("data-s1-mouth") || "";
            document.getElementById("view_s1_gk").value = this.getAttribute("data-s1-gk") || "";
            document.getElementById("view_s1_45m").value = this.getAttribute("data-s1-score-45m") || "";
            document.getElementById("view_s1_ck").value = this.getAttribute("data-s1-ck") || "";

            // HK2
            document.getElementById("view_s2_mouth").value = this.getAttribute("data-s2-mouth") || "";
            document.getElementById("view_s2_gk").value = this.getAttribute("data-s2-gk") || "";
            document.getElementById("view_s2_45m").value = this.getAttribute("data-s2-score-45m") || "";
            document.getElementById("view_s2_ck").value = this.getAttribute("data-s2-ck") || "";
        });
    });

    // ==============================
    // XỬ LÝ EDIT (NHẬP / SỬA)
    // ==============================
    document.querySelectorAll(".btn-edit").forEach(btn => {
        btn.addEventListener("click", function () {

            currentRow = this.closest("tr");
            const d = this.dataset;

            document.getElementById("modalActionTitle").innerText =
                (!this.getAttribute("data-s1-mouth") && !this.getAttribute("data-s1-score-45m") && !this.getAttribute("data-s1-gk") && !this.getAttribute("data-s1-ck")) ? "NHẬP ĐIỂM" : "CẬP NHẬT ĐIỂM";

            document.getElementById("edit_student_name").innerText =
                "HỌ TÊN HỌC SINH: " + upperName(this.getAttribute("data-ten"));

            document.getElementById("edit_student_id").innerText =
                "MÃ HỌC SINH: " + this.getAttribute("data-hs");

            // **********************************
            // BƯỚC 1: GÁN GIÁ TRỊ VÀO TRƯỜNG ẨN
            // **********************************
            document.getElementById("edit_maHS").value = this.getAttribute("data-hs");
            document.getElementById("edit_maMon").value = this.getAttribute("data-mamon");
            document.getElementById("edit_maLop").value = this.getAttribute("data-malop");

            // HK1
            document.getElementById("edit_s1_mouth").value = this.getAttribute("data-s1-mouth") || "";
            document.getElementById("edit_s1_gk").value = this.getAttribute("data-s1-gk") || "";
            document.getElementById("edit_s1_45m").value = this.getAttribute("data-s1-score-45m") || "";
            document.getElementById("edit_s1_ck").value = this.getAttribute("data-s1-ck") || "";

            // HK2
            document.getElementById("edit_s2_mouth").value = this.getAttribute("data-s2-mouth") || "";
            document.getElementById("edit_s2_gk").value = this.getAttribute("data-s2-gk") || "";
            document.getElementById("edit_s2_45m").value = this.getAttribute("data-s2-score-45m") || "";
            document.getElementById("edit_s2_ck").value = this.getAttribute("data-s2-ck") || "";
        });
    });

    // ==============================
    // *BƯỚC 2: XÓA LOGIC LƯU (AJAX)*
    // ==============================
    // Đã thay thế logic AJAX bằng form submit truyền thống
    // Toàn bộ khối code AJAX trước đó đã bị xóa.
    // Dữ liệu sẽ được gửi lên update_diem.php (theo cấu hình form ở file PHP)
    // Server-side (update_diem.php) sẽ xử lý việc cập nhật và redirect lại trang.
    
    // Nếu bạn muốn giữ lại hành vi đóng modal sau khi submit thành công,
    // bạn cần chuyển logic này sang file update_diem.php bằng cách 
    // gửi trả về một phản hồi và xử lý nó bằng AJAX. 
    // Tuy nhiên, form submit truyền thống là cách đơn giản và an toàn hơn.
    
    // Ví dụ, nếu bạn dùng AJAX, bạn có thể thay thế khối AJAX bị xóa bằng:
    // document.querySelector("#gradeEntryModal form").addEventListener("submit", function (e) {
    //    e.preventDefault(); // Ngăn form submit mặc định

    //    // Thực hiện FETCH (như mã đã xóa), sau đó nếu thành công:
    //    // gradeEntryModal.hide(); 
    //    // và reload lại trang hoặc cập nhật DOM.
    // });
    
    // Vì bạn đã chọn phương án form submit ở bước trước, không cần thêm code ở đây.

});

// ==============================
// IMPORT / EXPORT XLSX
// ==============================
document.addEventListener("DOMContentLoaded", function () {
    const btnExport = document.getElementById('btnExport');
    const btnImport = document.getElementById('btnImport');
    const importModalEl = document.getElementById('importModal');
    const importModalFile = document.getElementById('importModalFile');
    const importUploadBtn = document.getElementById('importUploadBtn');

    // EXPORT: only export checked rows
    if (btnExport) {
        btnExport.addEventListener('click', function (e) {
            // collect checked checkboxes in table body
            const checked = Array.from(document.querySelectorAll('table tbody input[type="checkbox"]'))
                .filter(ch => ch.checked);
            if (checked.length === 0) {
                alert('Vui lòng chọn các hàng cần xuất (tích checkbox).');
                return;
            }

            const selected = [];
            checked.forEach(ch => {
                const tr = ch.closest('tr');
                if (!tr) return;
                const mahs = tr.getAttribute('data-mahs') || '';
                const mamon = tr.getAttribute('data-mamon') || '';
                const malop = tr.getAttribute('data-malop') || '';
                const namhoc = tr.getAttribute('data-namhoc') || this.getAttribute('data-namhoc') || '';
                const hocky = tr.getAttribute('data-hocky') || this.getAttribute('data-hocky') || '';
                // encode as pipe separated
                selected.push([mahs, mamon, malop, namhoc, hocky].join('|'));
            });

            // create a form and POST selected[] to export_scores.php to trigger download
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'export_scores.php';
            form.style.display = 'none';
            selected.forEach(s => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'selected[]';
                inp.value = s;
                form.appendChild(inp);
            });
            document.body.appendChild(form);
            form.submit();
            form.remove();
        });
    }

    // SELECT ALL checkbox behavior
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const checks = document.querySelectorAll('table tbody input.row-check');
            checks.forEach(ch => ch.checked = selectAll.checked);
        });
    }

    // IMPORT: show modal with guidance
    let importModal = null;
    if (importModalEl) importModal = new bootstrap.Modal(importModalEl);
    if (btnImport && importModal) {
        btnImport.addEventListener('click', function () {
            importModal.show();
        });
    }

    if (importUploadBtn && importModalFile) {
        importUploadBtn.addEventListener('click', function () {
            const f = importModalFile.files[0];
            if (!f) { alert('Vui lòng chọn file trước khi tải lên.'); return; }

            const fd = new FormData();
            fd.append('file', f);
            const params = new URLSearchParams(window.location.search);
            fd.append('maLop', params.get('class') || '');
            fd.append('maMon', params.get('subject') || '');

            importUploadBtn.disabled = true;
            importUploadBtn.innerText = 'Đang nhập...';

            fetch('import_scores.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(resp => {
                alert('Kết quả: ' + JSON.stringify(resp));
                if (resp && (resp.inserted || resp.updated)) {
                    importModal.hide();
                    location.reload();
                }
            }).catch(err => {
                console.error(err);
                alert('Lỗi khi nhập file. Kiểm tra console.');
            }).finally(() => {
                importUploadBtn.disabled = false;
                importUploadBtn.innerText = 'Tải lên và nhập';
            });
        });
    }
});