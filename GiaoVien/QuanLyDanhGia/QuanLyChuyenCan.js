document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('attendance-date');
    const classSelect = document.getElementById('class-filter');
    const subjectSelect = document.getElementById('subject-filter');
    const tbody = document.querySelector('#attendance-table tbody');
    const countPresent = document.getElementById('count-present');
    const countLate = document.getElementById('count-late');
    const countAbsent = document.getElementById('count-absent');
    const countRate = document.getElementById('count-rate');

    dateInput.addEventListener('change', function() {
        const d = new Date(this.value + 'T00:00:00Z');
        if (isNaN(d.getTime())) return;
        const day = d.getUTCDay(); // 0 = Sunday
        if (day === 0) {
            alert('Không thể chọn Chủ nhật để điểm danh');
            this.value = new Date().toISOString().slice(0,10);
            return;
        }
        loadStudents();
    });

    classSelect.addEventListener('change', loadStudents);
    subjectSelect.addEventListener('change', loadStudents);

    function loadStudents() {
        const maLop = classSelect.value;
        const maMon = subjectSelect.value;
        const date = dateInput.value;
        if (!maLop || !maMon) {
            tbody.innerHTML = '<tr><td colspan="4">Chọn lớp và môn để nạp danh sách</td></tr>';
            return;
        }
        fetch(`get_attendance.php?maLop=${encodeURIComponent(maLop)}&maMon=${encodeURIComponent(maMon)}&date=${encodeURIComponent(date)}`)
            .then(r => r.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (!data.success) {
                        tbody.innerHTML = '<tr><td colspan="4">' + (data.message||'Lỗi') + '</td></tr>';
                        return;
                    }
                    renderTable(data.students, data.attendance);
                } catch (e) {
                    console.error('JSON parse error:', text);
                    tbody.innerHTML = '<tr><td colspan="4">Lỗi phân tích phản hồi: ' + text.substring(0, 100) + '</td></tr>';
                }
            }).catch(err => {
                tbody.innerHTML = '<tr><td colspan="4">Lỗi nạp dữ liệu</td></tr>';
                console.error(err);
            });
    }

    function renderTable(students, attendance) {
        tbody.innerHTML = '';
        let p = 0, l = 0, a = 0;
        if (!students || students.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4">Không có học sinh</td></tr>';
            updateSummary(0,0,0);
            return;
        }
        students.forEach((s, idx) => {
            const tr = document.createElement('tr');
            const tdIdx = document.createElement('td'); tdIdx.textContent = idx+1;
            const tdClass = document.createElement('td'); tdClass.textContent = s.tenLop || '';
            const tdName = document.createElement('td'); tdName.textContent = s.hoVaTen || '';
            const tdAct = document.createElement('td'); tdAct.className = 'text-center';

            const btnPresent = document.createElement('button'); btnPresent.className = 'btn-attendance btn-present'; btnPresent.textContent = 'Có mặt';
            const btnLate = document.createElement('button'); btnLate.className = 'btn-attendance btn-late'; btnLate.textContent = 'Đến muộn';
            const btnAbsent = document.createElement('button'); btnAbsent.className = 'btn-attendance btn-absent'; btnAbsent.textContent = 'Vắng mặt';

            const existing = attendance && attendance[s.maHS] !== undefined ? attendance[s.maHS] : null;
            if (existing !== null) {
                if (existing == '1') { btnPresent.classList.add('active'); p++; }
                else if (existing == '2') { btnLate.classList.add('active'); l++; }
                else { btnAbsent.classList.add('active'); a++; }
            }

            [btnPresent, btnLate, btnAbsent].forEach(btn => {
                btn.style.margin = '0 4px';
                btn.dataset.mahs = s.maHS;
                btn.dataset.malop = classSelect.value;
                btn.dataset.mamon = subjectSelect.value;
                btn.dataset.date = dateInput.value;
                btn.addEventListener('click', onToggleAttendance);
            });

            tdAct.appendChild(btnPresent);
            tdAct.appendChild(btnLate);
            tdAct.appendChild(btnAbsent);

            tr.appendChild(tdIdx);
            tr.appendChild(tdClass);
            tr.appendChild(tdName);
            tr.appendChild(tdAct);
            tbody.appendChild(tr);
        });
        updateSummary(p,l,a);
    }

    function onToggleAttendance(e) {
        const btn = e.currentTarget;
        const tr = btn.closest('tr');
        const buttons = tr.querySelectorAll('.btn-attendance');
        const wasActive = btn.classList.contains('active');
        buttons.forEach(b => b.classList.remove('active'));
        let newStatus = null;
        if (!wasActive) {
            btn.classList.add('active');
            if (btn.classList.contains('btn-present')) newStatus = '1';
            else if (btn.classList.contains('btn-late')) newStatus = '2';
            else newStatus = '0';
        }
        const mahs = btn.dataset.mahs;
        const malop = btn.dataset.malop;
        const mamon = btn.dataset.mamon;
        const date = btn.dataset.date;

        // send to server
        saveAttendance(mahs, malop, mamon, date, newStatus).then(resp => {
            if (!resp.success) {
                alert(resp.message || 'Lỗi lưu điểm danh');
                loadStudents();
            } else {
                recalcSummary();
            }
        }).catch(err => {
            console.error(err);
            alert('Lỗi mạng khi lưu');
            loadStudents();
        });
    }

    function saveAttendance(mahs, malop, mamon, date, status) {
        const form = new FormData();
        form.append('maHS', mahs);
        form.append('maLop', malop);
        form.append('maMon', mamon);
        form.append('date', date);
        if (status !== null) form.append('status', status);
        else form.append('status', '');

        return fetch('save_attendance.php', { method: 'POST', body: form })
            .then(r => r.text())
            .then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Save JSON parse error:', text);
                    return {success: false, message: 'Lỗi phân tích phản hồi: ' + text.substring(0, 100)};
                }
            });
    }

    function recalcSummary() {
        const rows = tbody.querySelectorAll('tr');
        let p = 0, l = 0, a = 0;
        rows.forEach(r => {
            const btnP = r.querySelector('.btn-present');
            const btnL = r.querySelector('.btn-late');
            const btnA = r.querySelector('.btn-absent');
            if (btnP && btnP.classList.contains('active')) p++;
            else if (btnL && btnL.classList.contains('active')) l++;
            else if (btnA && btnA.classList.contains('active')) a++;
        });
        updateSummary(p,l,a);
    }

    function updateSummary(p,l,a) {
        countPresent.textContent = p;
        countLate.textContent = l;
        countAbsent.textContent = a;
        const total = p + l + a;
        let rate = '0%';
        if (total > 0) {
            rate = (((p+l)/total)*100).toFixed(1) + '%';
        }
        countRate.textContent = rate;
    }

    if (classSelect && subjectSelect) loadStudents();
});
