document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.search-form');
    if (!form) return;
    const input = form.querySelector('input[name="search"]');
    if (!input) return;

    let timeout = null;

    function filterTables(q) {
        const query = String(q || '').trim().toLowerCase();
        // Find tables inside main content
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
            const did = filterTables(q);
            // if no table to filter, fall back to normal submit
            if (!did) {
                // no-op (user can press enter to submit)
            }
        }, 200);
    });

    form.addEventListener('submit', function(e) {
        // If page has at least one table and tables are marked for client-side search, prevent navigation
        const tables = document.querySelectorAll('main table.table');
        if (tables.length) {
            e.preventDefault();
            filterTables(input.value);
        }
        // otherwise allow default GET search behavior
    });
});
