document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class-filter');
    const subjectSelect = document.getElementById('subject-filter');
    const semesterSelect = document.getElementById('semester-filter');
    const yearInput = document.getElementById('year-filter');
    const tbody = document.querySelector('#score-tbody');
    const tableRange = document.getElementById('table-range');
    
    let currentClassInfo = { namHoc: '', hocKy: 1 };

    classSelect.addEventListener('change', loadScores);
    subjectSelect.addEventListener('change', loadScores);
    if (semesterSelect) semesterSelect.addEventListener('change', loadScores);
    if (yearInput) yearInput.addEventListener('change', loadScores);

    function loadScores() {
        const maLop = classSelect.value;
        const maMon = subjectSelect.value;
        if (!maLop || !maMon) {
            tbody.innerHTML = '<tr><td colspan="9">Chọn lớp và môn để xem điểm</td></tr>';
            return;
        }
        const hocKyParam = (semesterSelect && semesterSelect.value) ? `&hocKy=${encodeURIComponent(semesterSelect.value)}` : '';
        const namHocParam = (yearInput && yearInput.value) ? `&namHoc=${encodeURIComponent(yearInput.value)}` : '';
        fetch(`get_scores.php?maLop=${encodeURIComponent(maLop)}&maMon=${encodeURIComponent(maMon)}` + hocKyParam + namHocParam)
            .then(r => r.text())
            .then(text => {
                try {
                    const resp = JSON.parse(text);
                    if (!resp.success) {
                        tbody.innerHTML = '<tr><td colspan="9">' + (resp.message||'Lỗi') + '</td></tr>';
                        return;
                    }
                    if (resp.classInfo) {
                        currentClassInfo = resp.classInfo;
                        if (yearInput && (!yearInput.value || yearInput.value.trim() === '')) yearInput.value = currentClassInfo.namHoc || '2025-2026';
                        if (semesterSelect && (!semesterSelect.value || semesterSelect.value === '')) semesterSelect.value = currentClassInfo.hocKy || 1;
                    }
                        fullData = resp.data || [];
                        currentPage = 1;
                        pageSize = parseInt(pageSizeSelect ? pageSizeSelect.value : 10) || 10;
                        renderPage(currentPage);
                } catch (e) {
                    console.error('JSON parse error:', text);
                    tbody.innerHTML = '<tr><td colspan="9">Lỗi phân tích dữ liệu: ' + text.substring(0, 100) + '</td></tr>';
                }
            }).catch(err => {
                tbody.innerHTML = '<tr><td colspan="9">Lỗi nạp dữ liệu</td></tr>';
                console.error(err);
            });
    }

        let fullData = [];
        let currentPage = 1;
        let pageSize = 10;
        // Track which semester inputs the user is actively editing inside the modal
        let modalEditingHocKy = 1;

        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');
        const pageInfo = document.getElementById('page-info');
        const pageSizeSelect = document.getElementById('page-size');

        function renderTableRows(rows) {
            tbody.innerHTML = '';
            if (!rows || rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9">Không có học sinh</td></tr>';
                return;
            }
            rows.forEach((row) => {
                const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><input class="form-check-input row-check" type="checkbox"></td>
                        <td>${row.stt}</td>
                        <td>${row.maHS}</td>
                        <td>${row.hoVaTen}</td>
                        <td>${row.tenLop}</td>
                        <td>${subjectSelect.options[subjectSelect.selectedIndex].text}</td>
                        <td>${row.avgHK1 || '-'}</td>
                        <td>${row.avgHK2 || '-'}</td>
                        <td>${row.avg || '-'}</td>
                        <td class="action-icons">
                            <a href="#" class="btn-view" data-mahs="${row.maHS}" data-name="${row.hoVaTen}" data-mouth="${row.mouth}" data-45m="${row['45m']}" data-gk="${row.gk}" data-ck="${row.ck}" data-bs-toggle="modal" data-bs-target="#viewGradeModal">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="#" class="btn-edit" data-mahs="${row.maHS}" data-name="${row.hoVaTen}" data-mouth="${row.mouth}" data-45m="${row['45m']}" data-gk="${row.gk}" data-ck="${row.ck}" data-bs-toggle="modal" data-bs-target="#gradeEntryModal">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </td>
                    `;
                tbody.appendChild(tr);
            });
        }

        function updatePaginationControls() {
            const total = fullData.length;
            const totalPages = Math.max(1, Math.ceil(total / pageSize));
            if (currentPage > totalPages) currentPage = totalPages;
            pageInfo.textContent = currentPage + '/' + totalPages;
            const start = (currentPage - 1) * pageSize + 1;
            const end = Math.min(total, currentPage * pageSize);
            tableRange.textContent = (total === 0 ? '0 mục' : (`${start}-${end} / ${total} mục`));
            prevBtn.disabled = currentPage <= 1;
            nextBtn.disabled = currentPage >= totalPages;
        }

        function renderPage(page) {
            currentPage = page || 1;
            const start = (currentPage - 1) * pageSize;
            const pageRows = fullData.slice(start, start + pageSize);
            pageRows.forEach((r, idx) => r.stt = start + idx + 1);
            renderTableRows(pageRows);
            updatePaginationControls();
        }

    function renderTable(data) {
        tbody.innerHTML = '';
        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9">Không có học sinh</td></tr>';
            tableRange.textContent = '0 mục';
            return;
        }
        data.forEach((row) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${row.stt}</td>
                <td>${row.maHS}</td>
                <td>${row.hoVaTen}</td>
                <td>${row.tenLop}</td>
                <td>${subjectSelect.options[subjectSelect.selectedIndex].text}</td>
                <td>${row.avgHK1 || '-'}</td>
                <td>${row.avgHK2 || '-'}</td>
                <td>${row.avg || '-'}</td>
                <td class="action-icons">
                    <a href="#" class="btn-view" data-mahs="${row.maHS}" data-name="${row.hoVaTen}" data-mouth="${row.mouth}" data-45m="${row['45m']}" data-gk="${row.gk}" data-ck="${row.ck}" data-bs-toggle="modal" data-bs-target="#viewGradeModal">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="#" class="btn-edit" data-mahs="${row.maHS}" data-name="${row.hoVaTen}" data-mouth="${row.mouth}" data-45m="${row['45m']}" data-gk="${row.gk}" data-ck="${row.ck}" data-bs-toggle="modal" data-bs-target="#gradeEntryModal">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                </td>
            `;
            tbody.appendChild(tr);
        });
        tableRange.textContent = data.length + ' mục';
    }

    tbody.addEventListener('click', function(e) {
        const btnView = e.target.closest('.btn-view');
        const btnEdit = e.target.closest('.btn-edit');
        if (btnView) {
            e.preventDefault();
            fetchAndPopulateModal(btnView, 'view');
        }
        if (btnEdit) {
            e.preventDefault();
            fetchAndPopulateModal(btnEdit, 'edit');
        }
    });

        if (prevBtn) prevBtn.addEventListener('click', function(e) { e.preventDefault(); if (currentPage>1) renderPage(currentPage-1); });
        if (nextBtn) nextBtn.addEventListener('click', function(e) { e.preventDefault(); const totalPages = Math.max(1, Math.ceil(fullData.length / pageSize)); if (currentPage<totalPages) renderPage(currentPage+1); });
        if (pageSizeSelect) pageSizeSelect.addEventListener('change', function() { pageSize = parseInt(this.value)||10; currentPage = 1; renderPage(currentPage); });

    function fetchAndPopulateModal(btn, mode) {
        const maHS = btn.dataset.mahs;
        const hoVaTen = btn.dataset.name || 'N/A';
        const maMon = subjectSelect.value;
        const maLop = classSelect.value;
        const selectedNamHoc = (yearInput && yearInput.value) ? yearInput.value : (currentClassInfo.namHoc || '2025-2026');
        
        if (mode === 'view') {
            document.getElementById('view_student_name').textContent = 'HỌ TÊN HỌC SINH: ' + hoVaTen.toUpperCase();
            document.getElementById('view_student_id').textContent = 'MÃ HỌC SINH: ' + maHS;
        } else {
            document.getElementById('edit_student_name').textContent = 'HỌ TÊN HỌC SINH: ' + hoVaTen.toUpperCase();
            document.getElementById('edit_student_id').textContent = 'MÃ HỌC SINH: ' + maHS;
        }
        
        Promise.all([
            fetch('get_student_scores.php', { 
                method: 'POST', 
                body: new URLSearchParams({
                    maHS: maHS,
                    maMon: maMon,
                    maLop: maLop,
                    namHoc: selectedNamHoc,
                    hocKy: 1
                })
            }).then(r => r.json()),
            fetch('get_student_scores.php', { 
                method: 'POST', 
                body: new URLSearchParams({
                    maHS: maHS,
                    maMon: maMon,
                    maLop: maLop,
                    namHoc: selectedNamHoc,
                    hocKy: 2
                })
            }).then(r => r.json())
        ])
        .then(([data1, data2]) => {
            if (!data1.success || !data2.success) {
                alert('Lỗi: Không thể lấy dữ liệu');
                return;
            }
            
            const prefixView1 = 'view_s1_';
            const prefixEdit1 = 'edit_s1_';
            if (mode === 'view') {
                document.getElementById(prefixView1 + 'mouth').value = data1.scores.mouth || '';
                document.getElementById(prefixView1 + '45m').value = data1.scores['45m'] || '';
                document.getElementById(prefixView1 + 'gk').value = data1.scores.gk || '';
                document.getElementById(prefixView1 + 'ck').value = data1.scores.ck || '';
            } else {
                document.getElementById(prefixEdit1 + 'mouth').value = data1.scores.mouth || '';
                document.getElementById(prefixEdit1 + '45m').value = data1.scores['45m'] || '';
                document.getElementById(prefixEdit1 + 'gk').value = data1.scores.gk || '';
                document.getElementById(prefixEdit1 + 'ck').value = data1.scores.ck || '';
            }
            
            const prefixView2 = 'view_s2_';
            const prefixEdit2 = 'edit_s2_';
            if (mode === 'view') {
                document.getElementById(prefixView2 + 'mouth').value = data2.scores.mouth || '';
                document.getElementById(prefixView2 + '45m').value = data2.scores['45m'] || '';
                document.getElementById(prefixView2 + 'gk').value = data2.scores.gk || '';
                document.getElementById(prefixView2 + 'ck').value = data2.scores.ck || '';
            } else {
                document.getElementById(prefixEdit2 + 'mouth').value = data2.scores.mouth || '';
                document.getElementById(prefixEdit2 + '45m').value = data2.scores['45m'] || '';
                document.getElementById(prefixEdit2 + 'gk').value = data2.scores.gk || '';
                document.getElementById(prefixEdit2 + 'ck').value = data2.scores.ck || '';

                const saveElem1 = document.getElementById(prefixEdit1 + 'mouth');
                const saveElem2 = document.getElementById(prefixEdit2 + 'mouth');
                if (saveElem1) {
                    saveElem1.dataset.mahs = maHS;
                    saveElem1.dataset.mamon = maMon;
                }
                if (saveElem2) {
                    saveElem2.dataset.mahs = maHS;
                    saveElem2.dataset.mamon = maMon;
                }
                // Determine default editing semester: prefer the semester with at least one non-empty score if s1 empty
                const s1Has = (data1.scores.mouth||data1.scores['45m']||data1.scores.gk||data1.scores.ck) && (data1.scores.mouth!==''||data1.scores['45m']!==''||data1.scores.gk!==''||data1.scores.ck!=='');
                const s2Has = (data2.scores.mouth||data2.scores['45m']||data2.scores.gk||data2.scores.ck) && (data2.scores.mouth!==''||data2.scores['45m']!==''||data2.scores.gk!==''||data2.scores.ck!=='');
                modalEditingHocKy = s1Has ? 1 : (s2Has ? 2 : 1);

                // Add focus listeners so when user focuses inputs we know which semester they're editing
                ['mouth','45m','gk','ck'].forEach(k => {
                    const e1 = document.getElementById('edit_s1_' + k);
                    const e2 = document.getElementById('edit_s2_' + k);
                    if (e1) e1.addEventListener('focus', () => { modalEditingHocKy = 1; });
                    if (e2) e2.addEventListener('focus', () => { modalEditingHocKy = 2; });
                });
            }
        })
        .catch(err => {
            console.error('Error fetching scores:', err);
            alert('Lỗi nạp dữ liệu từ cơ sở dữ liệu');
        });
    }

    function inferHocKy() {
        const s2m = document.getElementById('edit_s2_mouth');
        const s2_45 = document.getElementById('edit_s2_45m');
        const s2gk = document.getElementById('edit_s2_gk');
        const s2ck = document.getElementById('edit_s2_ck');
        
        if ((s2m && s2m.value && s2m.value.trim() !== '') ||
            (s2_45 && s2_45.value && s2_45.value.trim() !== '') ||
            (s2gk && s2gk.value && s2gk.value.trim() !== '') ||
            (s2ck && s2ck.value && s2ck.value.trim() !== '')) {
            return 2;
        }
        return 1;
    }

    const saveBtn = document.querySelector('.btn-custom-save');
    if (saveBtn) {
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const detectedHocKy = modalEditingHocKy || inferHocKy();
            const selectedNamHoc = (yearInput && yearInput.value) ? yearInput.value : '2025-2026';

            const s1Elem = document.getElementById('edit_s1_mouth');
            const s2Elem = document.getElementById('edit_s2_mouth');
            const maHS = (s1Elem && s1Elem.dataset.mahs) ? s1Elem.dataset.mahs : (s2Elem && s2Elem.dataset.mahs ? s2Elem.dataset.mahs : null);
            const maMon = (s1Elem && s1Elem.dataset.mamon) ? s1Elem.dataset.mamon : (s2Elem && s2Elem.dataset.mamon ? s2Elem.dataset.mamon : null);

            if (!maHS || !maMon) {
                alert('Lỗi: Không xác định được học sinh hoặc môn');
                return;
            }

            const formData = new FormData();
            formData.append('maHS', maHS);
            formData.append('maMon', maMon);
            formData.append('maLop', classSelect.value);
            formData.append('namHoc', selectedNamHoc);
            formData.append('hocKy', detectedHocKy);

            const prefix = (detectedHocKy === 2) ? 'edit_s2_' : 'edit_s1_';
            formData.append('mouth', document.getElementById(prefix + 'mouth').value);
            formData.append('45m', document.getElementById(prefix + '45m').value);
            formData.append('gk', document.getElementById(prefix + 'gk').value);
            formData.append('ck', document.getElementById(prefix + 'ck').value);
            
            fetch('save_student_scores.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert('Lưu điểm thành công\n' + JSON.stringify(data));
                        console.info('Save response:', data);
                        const modalEl = document.getElementById('gradeEntryModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        try {
                            if (semesterSelect) semesterSelect.value = detectedHocKy;
                            if (yearInput) yearInput.value = selectedNamHoc;
                        } catch (e) { console.warn('Lỗi', e); }
                        loadScores();
                    } else {
                        alert('Lỗi: ' + (data.message || 'Không thể lưu điểm') + '\n' + JSON.stringify(data));
                        console.error('Save error response:', data);
                    }
                }).catch(err => {
                    console.error('Error saving scores:', err);
                    alert('Lỗi lưu dữ liệu');
                });
        });
    }

    if (classSelect && subjectSelect) loadScores();

    // Import / Export 
    const btnExport = document.getElementById('btnExport');
    const btnImport = document.getElementById('btnImport');
    const importModalEl = document.getElementById('importModal');
    const importModalFile = document.getElementById('importModalFile');
    const importUploadBtn = document.getElementById('importUploadBtn');
    const selectAll = document.getElementById('selectAll');

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const checks = document.querySelectorAll('#score-tbody input.row-check');
            checks.forEach(ch => ch.checked = selectAll.checked);
        });
    }

    if (btnExport) {
        btnExport.addEventListener('click', function () {
            const checked = Array.from(document.querySelectorAll('#score-tbody input.row-check'))
                .filter(ch => ch.checked);
            if (checked.length === 0) { alert('Vui lòng chọn hàng để xuất'); return; }

            const selected = [];
            checked.forEach(ch => {
                const tr = ch.closest('tr');
                if (!tr) return;
                const mahs = tr.querySelector('td:nth-child(3)') ? tr.querySelector('td:nth-child(3)').innerText.trim() : '';
                const mamon = subjectSelect.value || '';
                const malop = classSelect.value || '';
                const namhoc = yearInput && yearInput.value ? yearInput.value : '';
                const hocky = semesterSelect && semesterSelect.value ? semesterSelect.value : '';
                selected.push([mahs, mamon, malop, namhoc, hocky].join('|'));
            });

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../Admin/QuanLyDiemSo/export_scores.php';
            form.style.display = 'none';
            selected.forEach(s => {
                const inp = document.createElement('input'); inp.type = 'hidden'; inp.name = 'selected[]'; inp.value = s; form.appendChild(inp);
            });
            document.body.appendChild(form); form.submit(); form.remove();
        });
    }

    let importModal = null;
    if (importModalEl) importModal = new bootstrap.Modal(importModalEl);
    if (btnImport && importModal) btnImport.addEventListener('click', () => importModal.show());

    if (importUploadBtn && importModalFile) {
        importUploadBtn.addEventListener('click', function () {
            const f = importModalFile.files[0]; if (!f) { alert('Vui lòng chọn file'); return; }
            const fd = new FormData(); fd.append('file', f);
            fd.append('maLop', classSelect.value || ''); fd.append('maMon', subjectSelect.value || '');

            importUploadBtn.disabled = true; importUploadBtn.innerText = 'Đang nhập...';
            fetch('../../Admin/QuanLyDiemSo/import_scores.php', { method: 'POST', body: fd })
            .then(r => r.json()).then(resp => { alert('Kết quả: ' + JSON.stringify(resp)); if (resp && (resp.inserted || resp.updated)) { importModal.hide(); loadScores(); } })
            .catch(err => { console.error(err); alert('Lỗi khi nhập'); })
            .finally(() => { importUploadBtn.disabled = false; importUploadBtn.innerText = 'Tải lên và nhập'; });
        });
    }
});
