document.addEventListener("DOMContentLoaded", function() {
    
    // Lấy tất cả nút xem tài liệu
    const viewButtons = document.querySelectorAll('.btn-view-doc');

    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Lấy dữ liệu từ data attribute của nút vừa bấm
            const d = this.dataset;

            // 1. Cập nhật Tiêu đề
            const titleEl = document.getElementById('m_title');
            if(titleEl) titleEl.innerText = d.title;

            // 2. Cập nhật Mô tả
            const descEl = document.getElementById('m_desc');
            if(descEl) descEl.innerText = d.desc;

            // 3. Cập nhật Hình ảnh preview
            const imgEl = document.getElementById('m_image');
            if(imgEl) {
                // Nếu có link ảnh thì hiện, không thì hiện ảnh placeholder hoặc ẩn
                if(d.img) {
                    imgEl.src = d.img;
                    imgEl.style.display = 'inline-block';
                } else {
                    imgEl.style.display = 'none';
                }
            }
        });
    });
});