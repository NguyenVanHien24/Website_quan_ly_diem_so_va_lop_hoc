document.addEventListener("DOMContentLoaded", function() {
    
    // Lấy tất cả các dòng thông báo có class 'notify-row'
    const notifyRows = document.querySelectorAll('.notify-row');

    notifyRows.forEach(row => {
        row.addEventListener('click', function() {
            // Lấy dữ liệu từ attributes data-...
            const d = this.dataset;

            // Điền vào Modal Chi tiết
            document.getElementById('v_title').value = d.title;
            document.getElementById('v_content').value = d.content;
            document.getElementById('v_date').value = d.date;
        });
    });
});