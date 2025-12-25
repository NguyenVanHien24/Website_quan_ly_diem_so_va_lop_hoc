document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('main table.table tbody') || document.querySelector('table.table tbody');
    if (!tbody) return;

    tbody.addEventListener('click', function (e) {
        const tr = e.target.closest('tr.notify-row');
        if (!tr) return;

        const d = tr.dataset;

        const titleEl = document.getElementById('v_title_text');
        const contentEl = document.getElementById('v_content');
        const senderEl = document.getElementById('v_sender');
        const dateEl = document.getElementById('v_date_text');
        const fileLink = document.getElementById('v_file_link');

        if (titleEl) titleEl.innerText = d.title || '';
        if (contentEl) contentEl.value = d.content || '';
        if (senderEl) senderEl.innerText = d.sender || 'Admin';
        if (dateEl) dateEl.innerText = d.date || '';

        if (fileLink) {
            if (d.file && d.file.trim() !== '') {
                fileLink.innerText = d.file;
                fileLink.style.display = 'inline';
                fileLink.href = window.BASE_URL + 'uploads/documents/' + d.file;
            } else {
                fileLink.style.display = 'none';
            }
        }

        const tbuId = d.tbuid;
        if (tbuId) {
            fetch(window.BASE_URL + 'HocSinh/TrangCaNhan/mark_read.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tbuId: tbuId })
            }).then(r => r.json()).then(data => {
                if (data && data.success) {
                    tr.classList.remove('unread');
                    tr.style.backgroundColor = '';
                    const badge = document.getElementById('notifBadge');
                    if (badge) badge.innerText = data.unread || '';
                }
            }).catch(() => {  });
        }
    });
});