document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class-filter');
    const subjectSelect = document.getElementById('subject-filter');
    const semesterSelect = document.getElementById('semester-filter');
    const yearInput = document.getElementById('year-filter');
    const tbody = document.querySelector('#score-tbody');
    const tableRange = document.getElementById('table-range');
    
    // Store class metadata for use in save
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
                    // Store class metadata (but allow UI override)
                    if (resp.classInfo) {
                        currentClassInfo = resp.classInfo;
                        // If UI year/semester were not set, reflect class info
                        if (yearInput && (!yearInput.value || yearInput.value.trim() === '')) yearInput.value = currentClassInfo.namHoc || '2025-2026';
                        if (semesterSelect && (!semesterSelect.value || semesterSelect.value === '')) semesterSelect.value = currentClassInfo.hocKy || 1;
                    }
                        // setup pagination data
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

        // Pagination state
        let fullData = [];
        let currentPage = 1;
        let pageSize = 10;

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
            // adjust stt numbering
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

    // Modal handlers
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

        // Pagination controls
        if (prevBtn) prevBtn.addEventListener('click', function(e) { e.preventDefault(); if (currentPage>1) renderPage(currentPage-1); });
        if (nextBtn) nextBtn.addEventListener('click', function(e) { e.preventDefault(); const totalPages = Math.max(1, Math.ceil(fullData.length / pageSize)); if (currentPage<totalPages) renderPage(currentPage+1); });
        if (pageSizeSelect) pageSizeSelect.addEventListener('change', function() { pageSize = parseInt(this.value)||10; currentPage = 1; renderPage(currentPage); });

    function fetchAndPopulateModal(btn, mode) {
        const maHS = btn.dataset.mahs;
        const hoVaTen = btn.dataset.name || 'N/A';
        const maMon = subjectSelect.value;
        const maLop = classSelect.value;
        const selectedNamHoc = (yearInput && yearInput.value) ? yearInput.value : (currentClassInfo.namHoc || '2025-2026');
        
        // Set student info in modal
        if (mode === 'view') {
            document.getElementById('view_student_name').textContent = 'HỌ TÊN HỌC SINH: ' + hoVaTen.toUpperCase();
            document.getElementById('view_student_id').textContent = 'MÃ HỌC SINH: ' + maHS;
        } else {
            document.getElementById('edit_student_name').textContent = 'HỌ TÊN HỌC SINH: ' + hoVaTen.toUpperCase();
            document.getElementById('edit_student_id').textContent = 'MÃ HỌC SINH: ' + maHS;
        }
        
        // Fetch both HK1 and HK2 scores
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
            
            // Populate HK1 inputs
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
            
            // Populate HK2 inputs
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
                
                // Store student info for save
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
            }
        })
        .catch(err => {
            console.error('Error fetching scores:', err);
            alert('Lỗi nạp dữ liệu từ cơ sở dữ liệu');
        });
    }

    // Auto-detect semester from filled inputs
    function inferHocKy() {
        const s2m = document.getElementById('edit_s2_mouth');
        const s2_45 = document.getElementById('edit_s2_45m');
        const s2gk = document.getElementById('edit_s2_gk');
        const s2ck = document.getElementById('edit_s2_ck');
        
        // If any HK II input has value, return 2, else 1
        if ((s2m && s2m.value && s2m.value.trim() !== '') ||
            (s2_45 && s2_45.value && s2_45.value.trim() !== '') ||
            (s2gk && s2gk.value && s2gk.value.trim() !== '') ||
            (s2ck && s2ck.value && s2ck.value.trim() !== '')) {
            return 2;
        }
        return 1;
    }

    // Save button handler
    const saveBtn = document.querySelector('.btn-custom-save');
    if (saveBtn) {
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Auto-detect semester from filled inputs
            const detectedHocKy = inferHocKy();
            const selectedNamHoc = (yearInput && yearInput.value) ? yearInput.value : '2025-2026';

            // Find the element that stores dataset.mahs (it was set when modal was populated)
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

            // Read inputs for the detected semester
            const prefix = (detectedHocKy === 2) ? 'edit_s2_' : 'edit_s1_';
            formData.append('mouth', document.getElementById(prefix + 'mouth').value);
            formData.append('45m', document.getElementById(prefix + '45m').value);
            formData.append('gk', document.getElementById(prefix + 'gk').value);
            formData.append('ck', document.getElementById(prefix + 'ck').value);
            
            fetch('save_student_scores.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert('Lưu điểm thành công');
                        // Close modal
                        const modalEl = document.getElementById('gradeEntryModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        // Reload scores
                        loadScores();
                    } else {
                        alert('Lỗi: ' + (data.message || 'Không thể lưu điểm'));
                    }
                }).catch(err => {
                    console.error('Error saving scores:', err);
                    alert('Lỗi lưu dữ liệu');
                });
        });
    }

    // Initial load
    if (classSelect && subjectSelect) loadScores();
});
