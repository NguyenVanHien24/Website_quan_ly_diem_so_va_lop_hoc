function updateEditTargetControls(){
    const v = document.querySelector('input[name="target_type_edit"]:checked')?.value || 'all';
    const er = document.getElementById('e_role_select');
    const ec = document.getElementById('e_class_select');
    const eu = document.getElementById('e_users_select');
    if(er) er.style.display = (v === 'role') ? '' : 'none';
    if(ec) ec.style.display = (v === 'class') ? '' : 'none';
    if(eu) eu.style.display = (v === 'users') ? '' : 'none';
}

document.addEventListener("DOMContentLoaded", function() {
    
    // 1. XỬ LÝ NÚT "THÊM THÔNG BÁO"
    const btnAdd = document.querySelector('.btn-add-notify');
    if (btnAdd) {
        btnAdd.addEventListener('click', function() {
            document.getElementById('addForm').reset();
            // ẩn các control target
            const role = document.getElementById('a_role_select'); if (role) role.style.display = 'none';
            const cls = document.getElementById('a_class_select'); if (cls) cls.style.display = 'none';
            const users = document.getElementById('a_users_select'); if (users) users.style.display = 'none';
        });
    }

    // 2. XỬ LÝ NÚT "SỬA" (Icon bút chì)
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;
            document.getElementById('e_ma').value = d.ma || '';
            document.getElementById('e_id').value = d.id || '';
            document.getElementById('e_title').value = d.title || '';
            document.getElementById('e_content').value = d.content || '';
            if (d.send_at) {
                const dt = new Date(d.send_at);
                const local = dt.toISOString().slice(0,16);
                document.getElementById('e_date').value = local;
            } else {
                document.getElementById('e_date').value = '';
            }

            const t = d.target_type || 'all';
            if (t === 'all') document.getElementById('et_all').checked = true;
            if (t === 'role') document.getElementById('et_role').checked = true;
            if (t === 'class') document.getElementById('et_class').checked = true;
            if (t === 'users') document.getElementById('et_users').checked = true;
            updateEditTargetControls();
            const tv = d.target_value || '';
            try {
                const parsed = JSON.parse(tv);
                if (Array.isArray(parsed)) {
                    const opts = document.getElementById('e_users_select').options;
                    for (let i=0;i<opts.length;i++) opts[i].selected = parsed.includes(opts[i].value);
                } else {
                    document.getElementById('e_role_select').value = parsed || '';
                    document.getElementById('e_class_select').value = parsed || '';
                }
            } catch(e){
                document.getElementById('e_role_select').value = tv;
                document.getElementById('e_class_select').value = tv;
            }
            const att = d.attachment || '';
            const disp = document.getElementById('e_attachment_display');
            if (disp) {
                if (att) {
                    const url = (window.BASE_URL || '/') + 'uploads/documents/' + encodeURIComponent(att);
                    disp.innerHTML = '<a href="' + url + '" target="_blank">' + att + '</a>';
                } else {
                    disp.innerHTML = '<span class="text-muted">Không có tệp đính kèm</span>';
                }
            }
        });
    });

    // 3. XỬ LÝ NÚT "XEM" (Icon hộp mũi tên)
    const viewButtons = document.querySelectorAll('.btn-view');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;
            
            document.getElementById('v_id').value = d.id;
            document.getElementById('v_title').value = d.title;
            document.getElementById('v_content').value = d.content;
            document.getElementById('v_date').value = d.date;
            
            // Chọn radio button (readonly) dựa trên target_type và vai trò người nhận thực tế nếu có
            const ttype = d.target_type || 'all';
            let recRoles = [];
            if (d.recRoles) {
                try { recRoles = JSON.parse(d.recRoles); } catch(e) { recRoles = []; }
            }
            if (Array.isArray(recRoles) && recRoles.length > 0) {
                const hasAll = recRoles.includes('all');
                const hasTeacher = recRoles.includes('GiaoVien');
                const hasStudent = recRoles.includes('HocSinh');
                const hasAdmin = recRoles.includes('Admin');
                const distinctCount = (hasAll ? 1 : 0) + (hasTeacher ? 1 : 0) + (hasStudent ? 1 : 0) + (hasAdmin ? 1 : 0);
                if (hasAll || distinctCount > 1) {
                    document.getElementById('vrx1').checked = true;
                } else if (hasStudent) {
                    document.getElementById('vrx3').checked = true;
                } else if (hasTeacher) {
                    document.getElementById('vrx2').checked = true;
                } else if (hasAdmin) {
                    document.getElementById('vrx1').checked = true;
                } else {
                    if (ttype === 'all') document.getElementById('vrx1').checked = true;
                    else if (ttype === 'role') {
                        if (d.target_value === 'HocSinh') document.getElementById('vrx3').checked = true;
                        else if (d.target_value === 'GiaoVien') document.getElementById('vrx2').checked = true;
                        else document.getElementById('vrx1').checked = true;
                    } else if (ttype === 'class') document.getElementById('vrx3').checked = true;
                    else document.getElementById('vrx1').checked = true;
                }
            } else {
                if (ttype === 'all') document.getElementById('vrx1').checked = true;
                else if (ttype === 'role') {
                    if (d.target_value === 'HocSinh') document.getElementById('vrx3').checked = true;
                    else if (d.target_value === 'GiaoVien') document.getElementById('vrx2').checked = true;
                    else document.getElementById('vrx1').checked = true;
                } else if (ttype === 'class') {
                    document.getElementById('vrx3').checked = true;
                } else if (ttype === 'users') {
                    document.getElementById('vrx3').checked = true;
                }
            }

            const recipEl = document.getElementById('v_recipients');
            if (recipEl) {
                const txt = d.recipients || '';
                recipEl.innerHTML = txt ? ('<div class="small text-muted">' + txt + '</div>') : '<span class="text-muted">Chưa phân phối</span>';
            }
            const vdisp = document.getElementById('v_attachment_display');
            if (vdisp) {
                const att = d.attachment || '';
                if (att) {
                    const url = (window.BASE_URL || '/') + 'uploads/documents/' + encodeURIComponent(att);
                    vdisp.innerHTML = '<a href="' + url + '" target="_blank">' + att + '</a>';
                } else {
                    vdisp.innerHTML = '<span class="text-muted">Không có tệp đính kèm</span>';
                }
            }
        });
    });

    // 4. XỬ LÝ NÚT "XÓA" (Từng dòng)
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            // Cập nhật thông báo xóa đơn
            document.getElementById('deleteMsg').innerText = `Bạn chắc chắn muốn xóa thông báo ${id}?`;
        });
    });

    // 5. XỬ LÝ NÚT "XÓA THÔNG BÁO" (Nút đỏ lớn - Xóa nhiều)
    const btnDeleteMulti = document.getElementById('btnDeleteMulti');
    if(btnDeleteMulti) {
        btnDeleteMulti.addEventListener('click', function() {
            // Cập nhật thông báo xóa nhiều
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            document.getElementById('deleteMsg').innerText = "Bạn chắc chắn muốn xóa các thông báo này?";
            modal.show();
        });
    }
    let pendingDeleteIds = [];
    const singleDeleteButtons = document.querySelectorAll('.btn-delete');
    singleDeleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            pendingDeleteIds = [this.dataset.id];
            document.getElementById('deleteMsg').innerText = `Bạn chắc chắn muốn xóa thông báo ${this.dataset.id}?`;
        });
    });

    const btnConfirmDelete = document.getElementById('btnConfirmDelete');
    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener('click', function(){
            const checkboxes = Array.from(document.querySelectorAll('tbody input.form-check-input[type="checkbox"]'));
            const checked = checkboxes.filter(c => c.checked).map(c => c.value);
            if (checked.length > 0) pendingDeleteIds = checked;

            if (pendingDeleteIds.length === 0) { alert('Chưa chọn thông báo để xóa'); return; }

            const fd = new FormData();
            fd.append('ids', JSON.stringify(pendingDeleteIds));
            btnConfirmDelete.disabled = true;
            fetch('delete_notify.php', { method: 'POST', body: fd, credentials: 'same-origin' })
                .then(r => r.json())
                .then(resp => {
                    btnConfirmDelete.disabled = false;
                    if (resp.success) location.reload();
                    else alert(resp.message || 'Lỗi khi xóa');
                }).catch(err => { btnConfirmDelete.disabled = false; alert('Lỗi mạng'); });
        });
    }
});

// --- XỬ LÝ GỬI THÔNG BÁO TỪ MODAL ---
document.addEventListener('DOMContentLoaded', function(){
    const radios = document.querySelectorAll('input[name="target_type"]');
    const roleSel = document.getElementById('a_role_select');
    const classSel = document.getElementById('a_class_select');
    const usersSel = document.getElementById('a_users_select');

    function updateTargetControls(){
        const v = document.querySelector('input[name="target_type"]:checked')?.value || 'all';
        if(roleSel) roleSel.style.display = (v === 'role') ? '' : 'none';
        if(classSel) classSel.style.display = (v === 'class') ? '' : 'none';
        if(usersSel) usersSel.style.display = (v === 'users') ? '' : 'none';
    }
    radios.forEach(r=> r.addEventListener('change', updateTargetControls));
    updateTargetControls();

    const editRadios = document.querySelectorAll('input[name="target_type_edit"]');
    function updateEditTargetControls(){
        const v = document.querySelector('input[name="target_type_edit"]:checked')?.value || 'all';
        const er = document.getElementById('e_role_select');
        const ec = document.getElementById('e_class_select');
        const eu = document.getElementById('e_users_select');
        if(er) er.style.display = (v === 'role') ? '' : 'none';
        if(ec) ec.style.display = (v === 'class') ? '' : 'none';
        if(eu) eu.style.display = (v === 'users') ? '' : 'none';
    }
    editRadios.forEach(r=> r.addEventListener('change', updateEditTargetControls));

    const btnSend = document.getElementById('btnSendNotify');
    if (btnSend) {
        btnSend.addEventListener('click', function(){
            const title = document.getElementById('a_title')?.value || '';
            const content = document.getElementById('a_content')?.value || '';
            const sendAt = document.getElementById('a_send_at')?.value || '';
            const targetType = document.querySelector('input[name="target_type"]:checked')?.value || 'all';

            let targetValue = '';
            if (targetType === 'role') targetValue = roleSel?.value || '';
            if (targetType === 'class') targetValue = classSel?.value || '';
            if (targetType === 'users') {
                const selected = Array.from(usersSel?.selectedOptions || []).map(o => o.value);
                targetValue = JSON.stringify(selected);
            }

            if (!title.trim()) { alert('Tiêu đề không được để trống'); return; }

            const fd = new FormData();
            fd.append('title', title);
            fd.append('content', content);
            if (sendAt) fd.append('send_at', sendAt);
            fd.append('target_type', targetType);
            fd.append('target_value', targetValue);
            const aFile = document.getElementById('a_file');
            if (aFile && aFile.files && aFile.files.length > 0) fd.append('attachment', aFile.files[0]);

            btnSend.disabled = true;
            fetch('save_notify.php', { method: 'POST', body: fd, credentials: 'same-origin' })
                .then(r => r.json())
                .then(resp => {
                    btnSend.disabled = false;
                    if (resp.success) {
                        location.reload();
                    } else {
                        alert(resp.message || 'Lỗi khi gửi thông báo');
                    }
                }).catch(err => {
                    btnSend.disabled = false;
                    alert('Lỗi mạng');
                });
        });
    }

    // --- XỬ LÝ LƯU Ở MODAL CHỈNH SỬA ---
    const btnUpdate = document.getElementById('btnUpdateNotify');
    if (btnUpdate) {
        btnUpdate.addEventListener('click', function(){
            const ma = document.getElementById('e_ma')?.value || '';
            const title = document.getElementById('e_title')?.value || '';
            const content = document.getElementById('e_content')?.value || '';
            const sendAt = document.getElementById('e_date')?.value || '';
            const targetType = document.querySelector('input[name="target_type_edit"]:checked')?.value || 'all';

            let targetValue = '';
            if (targetType === 'role') targetValue = document.getElementById('e_role_select')?.value || '';
            if (targetType === 'class') targetValue = document.getElementById('e_class_select')?.value || '';
            if (targetType === 'users') {
                const selected = Array.from(document.getElementById('e_users_select')?.selectedOptions || []).map(o => o.value);
                targetValue = JSON.stringify(selected);
            }

            if (!ma) { alert('Mã thông báo không hợp lệ'); return; }

            const fd = new FormData();
            fd.append('maThongBao', ma);
            fd.append('title', title);
            fd.append('content', content);
            if (sendAt) fd.append('send_at', sendAt);
            fd.append('target_type', targetType);
            fd.append('target_value', targetValue);
            const eFile = document.getElementById('e_file');
            if (eFile && eFile.files && eFile.files.length > 0) fd.append('attachment', eFile.files[0]);

            btnUpdate.disabled = true;
            fetch('update_notify.php', { method: 'POST', body: fd, credentials: 'same-origin' })
                .then(r => r.json())
                .then(resp => {
                    btnUpdate.disabled = false;
                    if (resp.success) {
                        location.reload();
                    } else {
                        alert(resp.message || 'Lỗi khi cập nhật thông báo');
                    }
                }).catch(err => {
                    btnUpdate.disabled = false;
                    alert('Lỗi mạng');
                });
        });
    }
});