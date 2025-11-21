document.addEventListener("DOMContentLoaded", function() {
    
    // Xử lý nút Xuất bảng điểm
    const btnExport = document.getElementById('btnExport');
    
    if(btnExport) {
        btnExport.addEventListener('click', function() {
            // Giả lập hành động xuất file
            const originalText = this.innerText;
            this.innerText = "Đang xử lý...";
            this.disabled = true;

            setTimeout(() => {
                alert("Đã xuất bảng điểm thành công!");
                this.innerText = originalText;
                this.disabled = false;
            }, 1000);
        });
    }
});