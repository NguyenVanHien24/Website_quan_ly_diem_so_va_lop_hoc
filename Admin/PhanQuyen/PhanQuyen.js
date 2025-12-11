document.addEventListener("DOMContentLoaded", function() {
    
    const btnSave = document.querySelector('.btn-save');
    const btnCancel = document.querySelector('.btn-cancel');

    // Xử lý nút LƯU
    if(btnSave) {
        btnSave.addEventListener('click', async function() {
            const originalText = this.innerText;
            this.innerText = "Đang lưu...";
            this.disabled = true;

            const userId = document.getElementById('userId') ? document.getElementById('userId').value : 0;
            // read radio selection
            const sel = document.querySelector('input[name="role"]:checked');
            const role = sel ? sel.value : '';

            if (!userId || !role) {
                alert('Vui lòng chọn người dùng và vai trò hợp lệ.');
                this.innerText = originalText; this.disabled = false; return;
            }

            try {
                const fd = new FormData(); fd.append('userId', userId); fd.append('role', role);
                const res = await fetch(window.BASE_URL + 'Admin/PhanQuyen/save_permission.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    alert(data.message || 'Lưu thành công');
                    // Redirect to user list
                    window.location.href = window.BASE_URL + 'Admin/PhanQuyen/PhanQuyen.php';
                } else {
                    alert(data.message || 'Lưu thất bại');
                    this.innerText = originalText; this.disabled = false;
                }
            } catch (e) {
                console.error(e);
                alert('Có lỗi xảy ra, vui lòng thử lại.');
                this.innerText = originalText; this.disabled = false;
            }
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