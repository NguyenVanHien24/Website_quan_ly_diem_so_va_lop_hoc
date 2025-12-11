document.addEventListener("DOMContentLoaded", function() {
    const classFilter = document.getElementById('class-filter');
    const subjectFilter = document.getElementById('subject-filter');
    const classSelect = document.getElementById('d_class');
    const tbody = document.querySelector('table tbody');
    
    function loadDocuments() {
        const maLop = classFilter.value;
        const maMon = subjectFilter.value;
        
        if (!maMon || !maLop) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Chọn lớp và môn để xem tài liệu</td></tr>';
            return;
        }
        
        fetch(`get_documents.php?maLop=${encodeURIComponent(maLop)}&maMon=${encodeURIComponent(maMon)}`)
            .then(r => r.json())
            .then(resp => {
                if (!resp.success) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">' + (resp.message || 'Lỗi') + '</td></tr>';
                    return;
                }
                renderTable(resp.data);
            })
            .catch(err => {
                console.error('Error:', err);
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">Lỗi nạp dữ liệu</td></tr>';
            });
    }
    
    function renderTable(data) {
        tbody.innerHTML = '';
        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Không có tài liệu nào</td></tr>';
            return;
        }
        
        data.forEach((doc) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input class="form-check-input rounded-circle" type="checkbox" data-id="${doc.id}"></td>
                <td>${doc.stt}</td>
                <td class="text-secondary">${doc.tieuDe || ''}</td>
                <td class="text-secondary">${doc.moTa || ''}</td>
                <td><span class="badge bg-success">Công khai</span></td>
                <td class="action-icons">
                    <a href="#" class="btn-edit" data-id="${doc.id}" data-title="${doc.tieuDe}" data-desc="${doc.moTa || ''}" data-bs-toggle="modal" data-bs-target="#docFormModal">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a href="#" class="btn-delete" data-id="${doc.id}" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                        <i class="bi bi-trash-fill"></i>
                    </a>
                </td>
            `;
            tbody.appendChild(tr);
        });
        
        // Rebind event listeners
        bindEditButtons();
        bindDeleteButtons();
    }
    
    function bindEditButtons() {
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const d = this.dataset;
                
                document.getElementById('modalTitle').innerText = "CHỈNH SỬA TÀI LIỆU";
                document.getElementById('btnSaveDoc').innerText = "Lưu thông tin";
                document.getElementById('d_id').value = d.id;
                if (classSelect) classSelect.value = classFilter.value;
                document.getElementById('d_subject').value = subjectFilter.value;
                document.getElementById('d_title').value = d.title || '';
                document.getElementById('d_desc').value = d.desc || '';
                document.getElementById('statusPublic').checked = true;
            });
        });
    }
    
    function bindDeleteButtons() {
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const docId = this.dataset.id;
                const deleteBtn = document.querySelector('#deleteConfirmModal .btn-danger');
                
                deleteBtn.onclick = function() {
                    deleteDocument(docId);
                };
            });
        });
    }
    
    function deleteDocument(docId) {
        const formData = new FormData();
        formData.append('id', docId);
        
        fetch('delete_document.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Xóa tài liệu thành công');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
                    if (modal) modal.hide();
                    loadDocuments();
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể xóa'));
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Lỗi xóa tài liệu');
            });
    }
    
    const btnAdd = document.querySelector('.btn-add-doc');
    if (btnAdd) {
        btnAdd.addEventListener('click', function() {
            document.getElementById('docForm').reset();
            document.getElementById('d_id').value = '';
            if (classSelect) classSelect.value = classFilter.value;
            document.getElementById('d_subject').value = subjectFilter.value || '';
            document.getElementById('modalTitle').innerText = "THÊM TÀI LIỆU";
            document.getElementById('btnSaveDoc').innerText = "Thêm mới";
            document.getElementById('statusPublic').checked = true;
        });
    }
    
    const btnSave = document.getElementById('btnSaveDoc');
    if (btnSave) {
        btnSave.addEventListener('click', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('id', document.getElementById('d_id').value);
            formData.append('title', document.getElementById('d_title').value);
            formData.append('desc', document.getElementById('d_desc').value);
            formData.append('maMon', document.getElementById('d_subject').value);
            formData.append('maLop', document.getElementById('d_class').value);
            if (fileInput && fileInput.files && fileInput.files.length > 0) {
                formData.append('file', fileInput.files[0]);
            } else {
                formData.append('fileName', document.getElementById('fileNameDisplay').value);
            }

            fetch('save_document.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert('Lưu thành công');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('docFormModal'));
                        if (modal) modal.hide();
                        loadDocuments();
                    } else {
                        alert('Lỗi: ' + (data.message || 'Không thể lưu'));
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Lỗi lưu dữ liệu');
                });
        });
    }
    
    const btnUpload = document.getElementById('btnUploadTrigger');
    const fileInput = document.getElementById('realFileInput');
    const fileDisplay = document.getElementById('fileNameDisplay');
    
    if (btnUpload && fileInput) {
        btnUpload.addEventListener('click', function() {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function() {
            fileDisplay.value = this.files[0]?.name || '';
        });
    }
    
    if (subjectFilter) subjectFilter.addEventListener('change', loadDocuments);
    if (classFilter) classFilter.addEventListener('change', loadDocuments);
    
    loadDocuments();
});