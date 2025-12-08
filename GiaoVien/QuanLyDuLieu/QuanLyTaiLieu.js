document.addEventListener("DOMContentLoaded", function() {
    const classFilter = document.getElementById('class-filter');
    const subjectFilter = document.getElementById('subject-filter');
    const tbody = document.querySelector('table tbody');
    
    // Load documents when subject changes
    function loadDocuments() {
        const maMon = subjectFilter.value;
        if (!maMon) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Chọn môn học để xem tài liệu</td></tr>';
            return;
        }
        
        fetch(`get_documents.php?maMon=${encodeURIComponent(maMon)}`)
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
                document.getElementById('d_class').value = classFilter.value;
                document.getElementById('d_maMon').value = subjectFilter.value;
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
        // Implement delete if needed
        alert('Xóa tài liệu ID: ' + docId);
        loadDocuments();
    }
    
    // Add button handler
    const btnAdd = document.querySelector('.btn-add-doc');
    if (btnAdd) {
        btnAdd.addEventListener('click', function() {
            document.getElementById('docForm').reset();
            document.getElementById('d_id').value = '';
            document.getElementById('d_class').value = classFilter.value;
            document.getElementById('d_maMon').value = subjectFilter.value;
            document.getElementById('d_subject').value = subjectFilter.value;
            document.getElementById('modalTitle').innerText = "THÊM TÀI LIỆU";
            document.getElementById('btnSaveDoc').innerText = "Thêm mới";
            document.getElementById('statusPublic').checked = true;
        });
    }
    
    // Save button handler
    const btnSave = document.getElementById('btnSaveDoc');
    if (btnSave) {
        btnSave.addEventListener('click', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('id', document.getElementById('d_id').value);
            formData.append('title', document.getElementById('d_title').value);
            formData.append('desc', document.getElementById('d_desc').value);
            formData.append('maMon', subjectFilter.value);
            formData.append('maLop', classFilter.value);
            formData.append('fileName', document.getElementById('fileNameDisplay').value);
            
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
    
    // File upload handler
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
    
    // Event listeners
    if (subjectFilter) subjectFilter.addEventListener('change', loadDocuments);
    
    // Initial load
    loadDocuments();
});