document.addEventListener("DOMContentLoaded", function() {
    
    const notifyRows = document.querySelectorAll('.notify-row');

    notifyRows.forEach(row => {
        row.addEventListener('click', function() {
            const d = this.dataset;

            // Điền dữ liệu vào Modal
            
            // 1. Tiêu đề (Dạng text, không phải input)
            document.getElementById('v_title_text').innerText = d.title;
            
            // 2. Nội dung (Textarea)
            document.getElementById('v_content').value = d.content;
            
            // 3. Người gửi
            document.getElementById('v_sender').innerText = d.sender || "Admin"; // Mặc định Admin nếu thiếu
            
            // 4. Ngày gửi
            document.getElementById('v_date_text').innerText = d.date;
            
            // 5. Tệp đính kèm
            const fileLink = document.getElementById('v_file_link');
            if (d.file) {
                fileLink.innerText = d.file;
                fileLink.style.display = "inline"; // Hiện nếu có file
                // fileLink.href = "/uploads/" + d.file; // Link tải file thực tế
            } else {
                fileLink.style.display = "none"; // Ẩn nếu không có file
            }
        });
    });
});