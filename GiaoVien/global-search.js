document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.search-form');
    if (!form) return;
    // Prefer named input, fallback to first text input in the form
    const input = form.querySelector('input[name="search"]') || form.querySelector('input[type="text"]');
    if (!input) return;

    let timeout = null;

    function filterTables(q) {
        const query = String(q || '').trim().toLowerCase();
        const tables = document.querySelectorAll('main table.table');
        if (!tables.length) return false;
        tables.forEach(table => {
            const tbody = table.tBodies[0];
            if (!tbody) return;
            const rows = Array.from(tbody.rows);
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = (query === '' || text.indexOf(query) !== -1) ? '' : 'none';
            });
        });
        return true;
    }

    input.addEventListener('input', function() {
        clearTimeout(timeout);
        const q = this.value;
        timeout = setTimeout(() => {
            const handled = filterTables(q);
            if (!handled) {
                // no table found, let the form submit normally on Enter
            }
        }, 200);
    });

    form.addEventListener('submit', function(e) {
        const tables = document.querySelectorAll('main table.table');
        if (tables.length) {
            e.preventDefault();
            filterTables(input.value);
        }
    });
});
