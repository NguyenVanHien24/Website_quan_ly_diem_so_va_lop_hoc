document.addEventListener("DOMContentLoaded", function() {
    
    const filterClass = document.getElementById('filterClass');
    const filterSubject = document.getElementById('filterSubject');

    // Hàm xử lý khi thay đổi bộ lọc
    function handleFilterChange() {
        const selectedClass = filterClass ? filterClass.value : '';
        const selectedSubject = filterSubject ? filterSubject.value : '';

        // Build query params and reload page to apply server-side filter
        const params = new URLSearchParams();
        if (selectedClass) params.set('maLop', selectedClass);
        if (selectedSubject) params.set('maMon', selectedSubject);

        const query = params.toString();
        const base = window.location.pathname;
        window.location.href = query ? base + '?' + query : base;
    }

    // Gán sự kiện change
    if(filterClass) filterClass.addEventListener('change', handleFilterChange);
    if(filterSubject) filterSubject.addEventListener('change', handleFilterChange);

});