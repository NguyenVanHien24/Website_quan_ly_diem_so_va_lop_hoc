document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.search-form');
    if (!form) return;
    const input = form.querySelector('input');
    if (!input) return;

    function normalize(s){ return String(s||'').toLowerCase().normalize('NFC'); }
    function debounce(fn, wait){ let t; return function(...args){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,args), wait); }; }

    const doFilter = () => {
        const q = normalize(input.value.trim());
        const tbody = document.querySelector('main table.table tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr'));
        if (!q) {
            rows.forEach(r => r.style.display = '');
            return;
        }
        rows.forEach(r => {
            const text = normalize(r.innerText);
            const matched = text.indexOf(q) !== -1;
            r.style.display = matched ? '' : 'none';
        });
    };

    const debounced = debounce(doFilter, 200);
    input.addEventListener('input', debounced);
    // also handle submit to prevent navigation
    form.addEventListener('submit', function(e){ e.preventDefault(); doFilter(); });
});
