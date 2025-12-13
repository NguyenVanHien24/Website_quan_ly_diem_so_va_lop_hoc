document.addEventListener('DOMContentLoaded', function() {
    const selYear = document.getElementById('selYear');
    const selSemester = document.getElementById('selSemester');
    const btnLoad = document.getElementById('btnLoadScores');
    const tbody = document.querySelector('main table.table tbody');
    const btnExport = document.getElementById('btnExport');
    const paginationInfo = document.getElementById('paginationInfo');
    const btnPrevPage = document.getElementById('btnPrevPage');
    const btnNextPage = document.getElementById('btnNextPage');
    const pageNumber = document.getElementById('pageNumber');

    // pagination state
    let fullData = [];
    let currentPage = 1;
    const pageSize = 10; // enforced

    async function loadYearsAndDefault() {
        try {
            const res = await fetch(window.BASE_URL + 'HocSinh/TraCuuThongTin/get_student_overview.php');
            const data = await res.json();
            if (!data.success) return;
            selYear.innerHTML = '';
            data.years.forEach(y => {
                const opt = document.createElement('option'); opt.value = y; opt.text = y; selYear.appendChild(opt);
            });
            if (data.years.length) selYear.value = data.years[0];
        } catch (e) { console.error(e); }
    }

    async function loadOverview() {
        const year = selYear.value || '';
        const hk = parseInt(selSemester.value) || 0;
        try {
            const res = await fetch(window.BASE_URL + `HocSinh/TraCuuThongTin/get_student_overview.php?namHoc=${encodeURIComponent(year)}&hocKy=${encodeURIComponent(hk)}`);
            const data = await res.json();
            if (!data.success) return;
            fullData = data.data || [];
            renderTablePage(1);
        } catch (e) { console.error(e); }
    }

    function renderTablePage(page) {
        tbody.innerHTML = '';
        const total = fullData.length;
        if (!fullData || total === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-3">Không có dữ liệu</td></tr>';
            updatePagination(0, 0, 0);
            return;
        }
        const totalPages = Math.max(1, Math.ceil(total / pageSize));
        if (page < 1) page = 1; if (page > totalPages) page = totalPages;
        currentPage = page;
        const start = (currentPage - 1) * pageSize;
        const end = Math.min(total, start + pageSize);
        for (let idx = start; idx < end; idx++) {
            const r = fullData[idx];
            const tr = document.createElement('tr');
            tr.className = 'notify-row';
            const globalIndex = idx + 1;
            tr.innerHTML = `
                <td class="text-center"><input class="form-check-input custom-checkbox" type="checkbox"></td>
                <td class="text-center fw-bold">${globalIndex}</td>
                <td class="text-secondary"></td>
                <td class="text-secondary">${escapeHtml(r.tenMon || '')}</td>
                <td class="text-center text-secondary">${r.avg_gk !== null ? r.avg_gk : ''}</td>
                <td class="text-center text-secondary">${r.avg_ck !== null ? r.avg_ck : ''}</td>
                <td class="text-center text-secondary">${r.avgScore !== null ? r.avgScore : ''}</td>
                <td class="text-center action-icons">
                    <a href="#" class="me-3 btn-view" data-mamon="${r.maMon}" title="Xem chi tiết"><i class="bi bi-box-arrow-up-right"></i></a>
                    <a href="#" class="icon-download btn-export-subject" data-mamon="${r.maMon}" title="Tải xuống"><i class="bi bi-download"></i></a>
                </td>`;
            tbody.appendChild(tr);
        }
        attachRowEvents();
        updatePagination(start + 1, end, total);
    }

    function attachRowEvents() {
        document.querySelectorAll('.btn-view').forEach(a => {
            a.addEventListener('click', async function(e) {
                e.preventDefault();
                const maMon = this.dataset.mamon;
                const year = selYear.value || '';
                const hk = parseInt(selSemester.value) || 0;
                try {
                    const fd = new FormData(); fd.append('maMon', maMon); fd.append('namHoc', year); fd.append('hocKy', hk);
                    const r = await fetch(window.BASE_URL + 'HocSinh/TraCuuThongTin/get_student_scores.php', { method: 'POST', body: fd });
                    const data = await r.json();
                    if (!data.success) return;
                    // Show simple modal with detail (reuse existing viewDetailModal if present)
                    const modalTitle = document.getElementById('v_title_text');
                    const elMieng = document.getElementById('v_diem_mieng');
                    const el1Tiet = document.getElementById('v_diem_1tiet');
                    const elGK = document.getElementById('v_diem_gk');
                    const elCK = document.getElementById('v_diem_ck');
                    if (modalTitle) modalTitle.innerText = this.closest('tr').querySelector('td:nth-child(4)').innerText;
                    // Normalize and group scores by type
                    const mouth = [], tiet = [], gk = [], ck = [];
                    (data.scores || []).forEach(s => {
                        const t = String(s.loaiDiem || '').toLowerCase();
                        const val = s.giaTriDiem;
                        if (t.includes('miệng') || t.includes('mieng')) mouth.push(val);
                        else if ((t.includes('1') && (t.includes('tiết') || t.includes('tiet') || t.includes('45'))) || t.includes('1tiết') || t.includes('1tiet')) tiet.push(val);
                        else if (t.includes('gk') || t.includes('giữa') || t.includes('giua') || t.includes('giữa kỳ') || t.includes('giua ky')) gk.push(val);
                        else if (t.includes('ck') || t.includes('cuối') || t.includes('cuoi')) ck.push(val);
                        else {
                            // fallback: try common keywords
                            if (t.includes('giua') || t.includes('gk')) gk.push(val);
                            else if (t.includes('cuoi') || t.includes('ck')) ck.push(val);
                            else tiet.push(val);
                        }
                    });
                    if (elMieng) elMieng.value = mouth.join(', ');
                    if (el1Tiet) el1Tiet.value = tiet.join(', ');
                    if (elGK) elGK.value = gk.join(', ');
                    if (elCK) elCK.value = ck.join(', ');
                    // Show modal
                    const modalElem = document.getElementById('viewDetailModal');
                    if (modalElem) new bootstrap.Modal(modalElem).show();
                } catch (e) { console.error(e); }
            });
        });

        document.querySelectorAll('.btn-export-subject').forEach(a => {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                const maMon = this.dataset.mamon;
                const tenMon = this.closest('tr').querySelector('td:nth-child(4)').innerText.trim();
                exportSubjectExcel(maMon, tenMon);
            });
        });
    }

    async function exportSubjectExcel(maMon, tenMon) {
        const year = selYear.value || '';
        const hk = parseInt(selSemester.value) || 0;
        const fd = new FormData(); fd.append('maMon', maMon); fd.append('namHoc', year); fd.append('hocKy', hk);
        try {
            const r = await fetch(window.BASE_URL + 'HocSinh/TraCuuThongTin/get_student_scores.php', { method: 'POST', body: fd });
            const data = await r.json();
            if (!data.success) return;
            const rows = [];
            rows.push(['Năm học', year]);
            rows.push(['Học kỳ', hk]);
            rows.push([]);
            rows.push(['Môn', tenMon]);
            rows.push([]);
            rows.push(['Loại điểm','Giá trị','Ngày ghi nhận']);
            data.scores.forEach(s => rows.push([s.loaiDiem, s.giaTriDiem, s.ngayGhiNhan]));
            const filename = `scores_${maMon}_${year}_hk${hk}.xlsx`;
            if (window.XLSX && typeof XLSX.utils !== 'undefined') {
                const ws = XLSX.utils.aoa_to_sheet(rows);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
                XLSX.writeFile(wb, filename);
            } else {
                downloadExcelFromRows(rows, filename.replace('.xlsx', '.xls'));
            }
        } catch (e) { console.error(e); }
    }

    function downloadExcelFromRows(rows, filename) {
        let html = '<table>';
        rows.forEach(r => {
            html += '<tr>' + r.map(c => '<td>' + (c !== undefined && c !== null ? escapeHtml(String(c)) : '') + '</td>').join('') + '</tr>';
        });
        html += '</table>';
        const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = filename; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
    }

    function escapeHtml(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    btnLoad.addEventListener('click', loadOverview);
    if (btnPrevPage) btnPrevPage.addEventListener('click', function(){ if (currentPage>1) renderTablePage(currentPage-1); });
    if (btnNextPage) btnNextPage.addEventListener('click', function(){ const totalPages = Math.max(1, Math.ceil(fullData.length / pageSize)); if (currentPage<totalPages) renderTablePage(currentPage+1); });

    function updatePagination(start, end, total) {
        const totalPages = Math.max(1, Math.ceil(total / pageSize));
        const displayStart = total === 0 ? 0 : start;
        const displayEnd = total === 0 ? 0 : end;
        if (paginationInfo) paginationInfo.innerText = `${displayStart}-${displayEnd}/${total} mục`;
        if (pageNumber) pageNumber.innerText = `${currentPage}/${totalPages}`;
        if (btnPrevPage) btnPrevPage.disabled = (currentPage <= 1 || total === 0);
        if (btnNextPage) btnNextPage.disabled = (currentPage >= totalPages || total === 0);
    }
    btnExport.addEventListener('click', function() {
        const rows = [];
        // metadata
        rows.push(['Năm học', selYear.value || '']);
        rows.push(['Học kỳ', selSemester.value || '']);
        rows.push([]);
        // table header
        document.querySelectorAll('main table.table thead tr').forEach(tr => {
            const cols = Array.from(tr.querySelectorAll('th')).map(th => th.innerText.trim()); rows.push(cols);
        });
        // table body
        document.querySelectorAll('main table.table tbody tr').forEach(tr => {
            const cols = Array.from(tr.querySelectorAll('td')).map(td => td.innerText.trim()); rows.push(cols);
        });
        const filename = `bangdiem_${selYear.value}_hk${selSemester.value}.xlsx`;
        if (window.XLSX && typeof XLSX.utils !== 'undefined') {
            const ws = XLSX.utils.aoa_to_sheet(rows);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
            XLSX.writeFile(wb, filename);
        } else {
            downloadExcelFromRows(rows, filename.replace('.xlsx', '.xls'));
        }
    });

    // init
    loadYearsAndDefault().then(() => loadOverview());
});