document.addEventListener("DOMContentLoaded", function() {
    
    const filterClass = document.getElementById('filterClass');
    const filterSubject = document.getElementById('filterSubject');

    // Hàm xử lý khi thay đổi bộ lọc
    function handleFilterChange() {
        const selectedClass = filterClass.value;
        const selectedSubject = filterSubject.value;

        console.log(`Đang lọc tài liệu: Lớp ${selectedClass} - Môn ${selectedSubject}`);
        // Tại đây bạn có thể thêm logic gọi API hoặc reload trang với tham số GET
        // Ví dụ: window.location.href = `?lop=${selectedClass}&mon=${selectedSubject}`;
    }

    // Gán sự kiện change
    if(filterClass) filterClass.addEventListener('change', handleFilterChange);
    if(filterSubject) filterSubject.addEventListener('change', handleFilterChange);

});