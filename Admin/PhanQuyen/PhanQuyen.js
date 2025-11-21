document.addEventListener("DOMContentLoaded", function() {
    
    const btnSave = document.querySelector('.btn-save');
    const btnCancel = document.querySelector('.btn-cancel');

    // Xử lý nút LƯU
    if(btnSave) {
        btnSave.addEventListener('click', function() {
            // Giả lập loading
            const originalText = this.innerText;
            this.innerText = "Đang lưu...";
            this.disabled = true;

            setTimeout(() => {
                alert("Đã cập nhật thông tin và phân quyền thành công!");
                this.innerText = originalText;
                this.disabled = false;
            }, 800);
        });
    }

    // Xử lý nút HỦY
    if(btnCancel) {
        btnCancel.addEventListener('click', function() {
            if(confirm("Bạn có chắc muốn hủy thay đổi?")) {
                window.location.reload(); // Tải lại trang để reset form
            }
        });
    }
});