document.addEventListener("DOMContentLoaded", function() {
    const subjectFilter = document.getElementById('subjectFilter');
    const tbody = document.querySelector('table tbody');
    
    // Load documents when subject changes
    function loadDocuments() {
        const maMon = subjectFilter.value;
        
        if (!maMon) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-secondary">Chọn môn học để xem tài liệu</td></tr>';
            return;
        }
        
        fetch(`get_documents_student.php?maMon=${encodeURIComponent(maMon)}`)
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
        
        data.forEach((doc, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td class="fw-bold text-secondary">${doc.tieuDe || ''}</td>
                <td class="text-secondary text-truncate" style="max-width: 200px;">${doc.moTa || ''}</td>
                <td class="text-secondary">${doc.tenMon || ''}</td>
                <td class="text-secondary">${doc.hoVaTen || ''}</td>
                <td class="text-center action-icons">
                    <a href="#" class="btn-view-doc" data-id="${doc.id}" data-bs-toggle="modal" data-bs-target="#viewDocModal">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </td>
            `;
            tbody.appendChild(tr);
        });
        
        // Rebind event listeners
        bindViewButtons();
    }
    
    function bindViewButtons() {
        document.querySelectorAll('.btn-view-doc').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const docId = this.dataset.id;
                
                // Fetch document details
                fetch(`get_document_detail.php?id=${encodeURIComponent(docId)}`)
                    .then(r => r.json())
                    .then(resp => {
                        if (resp.success && resp.data) {
                            const doc = resp.data;
                            document.getElementById('m_title').innerText = doc.tieuDe || '';
                            document.getElementById('m_desc').innerText = doc.moTa || '';
                            document.getElementById('m_subject').innerText = doc.tenMon || '';
                            document.getElementById('m_teacher').innerText = doc.hoVaTen || '';
                            
                            const downloadBtn = document.getElementById('downloadBtn');
                            downloadBtn.href = `download_document.php?id=${encodeURIComponent(docId)}`;
                            downloadBtn.download = doc.fileTL || 'document';
                        } else {
                            alert('Không thể tải chi tiết tài liệu');
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        alert('Lỗi tải chi tiết tài liệu');
                    });
            });
        });
    }
    
    // Event listener
    if (subjectFilter) subjectFilter.addEventListener('change', loadDocuments);
});