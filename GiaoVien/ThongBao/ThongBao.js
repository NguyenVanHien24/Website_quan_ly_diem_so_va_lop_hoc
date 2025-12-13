document.addEventListener("DOMContentLoaded", function() {
    
    // Lấy tất cả các dòng thông báo có class 'notify-row'
    function bindNotifyRows() {
        const notifyRows = document.querySelectorAll('.notify-row');
        notifyRows.forEach(row => {
            // avoid duplicate handlers
            row.removeEventListener('click', row._notifyHandler);
            row._notifyHandler = async function() {
                const d = this.dataset;
                // mark as read on server
                if (d.tbuid) {
                    try {
                        const r = await fetch(window.BASE_URL + 'Admin/QuanLyThongBao/mark_read.php', {method:'POST', credentials:'same-origin', body: new URLSearchParams({tbuId: d.tbuid})});
                        const jr = await r.json().catch(()=>null);
                        if (jr && typeof jr.unread !== 'undefined') {
                            const badge = document.getElementById('notifyBadge');
                            if (badge) {
                                if (jr.unread > 0) { badge.style.display = 'inline-block'; badge.textContent = jr.unread; } else { badge.style.display = 'none'; }
                            }
                        }
                        this.style.backgroundColor = '';
                    } catch (err) {
                        console.error('mark read failed', err);
                    }
                }
                // fill modal
                document.getElementById('v_title').value = d.title || '';
                document.getElementById('v_content').value = d.content || '';
                document.getElementById('v_date').value = d.date || '';
                // attachment
                const attachDiv = document.getElementById('v_attachment');
                attachDiv.innerHTML = '';
                if (d.attachment) {
                    const url = (window.BASE_URL || '') + 'uploads/documents/' + d.attachment;
                    const a = document.createElement('a');
                    a.href = url;
                    a.target = '_blank';
                    a.rel = 'noopener';
                    a.textContent = 'Tải tệp đính kèm';
                    attachDiv.appendChild(a);
                }
            };
            row.addEventListener('click', row._notifyHandler);
        });
    }

    // initial bind
    bindNotifyRows();

    // In case rows are loaded later, rebind when modal opens
    document.getElementById('viewDetailModal').addEventListener('show.bs.modal', bindNotifyRows);
});