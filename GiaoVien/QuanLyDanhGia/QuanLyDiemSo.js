document.addEventListener("DOMContentLoaded", function() {
    // Biến lưu dòng đang được chỉnh sửa
    let currentRow = null;

    // ==========================================
    // XỬ LÝ MODAL XEM CHI TIẾT (VIEW)
    // ==========================================
    const viewButtons = document.querySelectorAll('.btn-view');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;

            // Điền thông tin học sinh
            document.getElementById('view_student_name').innerText = "HỌ TÊN HỌC SINH: " + d.name.uppercase();
            document.getElementById('view_student_id').innerText = "MÃ HỌC SINH: " + d.id;
            
            // --- ĐIỀN ĐIỂM HỌC KỲ 1 ---
            document.getElementById('view_s1_mouth').value = d.s1Mouth;
            document.getElementById('view_s1_gk').value = d.s1Gk;
            // SỬA: Thêm số 1 vào (s145m) để khớp với HTML data-s1-45m
            document.getElementById('view_s1_45m').value = d.s145m; 
            document.getElementById('view_s1_ck').value = d.s1Ck;
            
            // --- ĐIỀN ĐIỂM HỌC KỲ 2 ---
            document.getElementById('view_s2_mouth').value = d.s2Mouth;
            document.getElementById('view_s2_gk').value = d.s2Gk;
            // SỬA: Thêm số 2 vào (s245m) để khớp với HTML data-s2-45m
            document.getElementById('view_s2_45m').value = d.s245m; 
            document.getElementById('view_s2_ck').value = d.s2Ck;
        });
    });

    // ==========================================
    // XỬ LÝ MODAL NHẬP / SỬA (EDIT)
    // ==========================================
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            currentRow = this.closest('tr'); 
            const d = this.dataset;

            const titleElement = document.getElementById('modalActionTitle');
            if (d.s1Mouth === "") {
                titleElement.innerText = "NHẬP ĐIỂM";
            } else {
                titleElement.innerText = "CẬP NHẬT ĐIỂM";
            }

            document.getElementById('edit_student_name').innerText = "HỌ TÊN HỌC SINH: " +  d.name.uppercase();
            document.getElementById('edit_student_id').innerText =  "MÃ HỌC SINH: " + d.id;

            // --- ĐIỀN FORM HỌC KỲ 1 ---
            document.getElementById('edit_s1_mouth').value = d.s1Mouth;
            document.getElementById('edit_s1_gk').value = d.s1Gk;
            // SỬA: s145m
            document.getElementById('edit_s1_45m').value = d.s145m;
            document.getElementById('edit_s1_ck').value = d.s1Ck;
            
            // --- ĐIỀN FORM HỌC KỲ 2 ---
            document.getElementById('edit_s2_mouth').value = d.s2Mouth;
            document.getElementById('edit_s2_gk').value = d.s2Gk;
            // SỬA: s245m
            document.getElementById('edit_s2_45m').value = d.s245m;
            document.getElementById('edit_s2_ck').value = d.s2Ck;
        });
    });

    // ==========================================
    // XỬ LÝ KHI BẤM LƯU (SAVE)
    // ==========================================
    const saveBtn = document.querySelector('.btn-custom-save');
    saveBtn.addEventListener('click', function(e) {
        e.preventDefault(); 

        if (currentRow) {
            // 1. Lấy giá trị mới từ Input (HK1)
            const newMouth = document.getElementById('edit_s1_mouth').value;
            const new45m = document.getElementById('edit_s1_45m').value;
            const newGK = document.getElementById('edit_s1_gk').value;
            const newCK = document.getElementById('edit_s1_ck').value;
            
            // (Lấy thêm giá trị HK2 nếu cần cập nhật hiển thị...)

            // 2. Cập nhật hiển thị trên Bảng
            currentRow.querySelector('.score-mouth').innerText = newMouth;
            // Chắc chắn class trong HTML là .score-45m
            currentRow.querySelector('.score-45m').innerText = new45m; 
            currentRow.querySelector('.score-hk1').innerText = newGK;
            // currentRow.querySelector('.score-hk2').innerText = newCK;
            
            // 3. Cập nhật lại data-attribute cho nút Edit/View
            const btnEdit = currentRow.querySelector('.btn-edit');
            const btnView = currentRow.querySelector('.btn-view');

            const updateData = (btn) => {
                btn.dataset.s1Mouth = newMouth;
                // SỬA: Cập nhật vào đúng biến s145m
                btn.dataset.s145m = new45m; 
                btn.dataset.s1Gk = newGK;
                btn.dataset.s1Ck = newCK;
                
                // Nếu muốn lưu cả HK2 vào data thì thêm dòng cập nhật s2... ở đây
            };

            updateData(btnEdit);
            updateData(btnView);

            // 4. Đóng Modal
            const modalEl = document.getElementById('gradeEntryModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            alert("Đã cập nhật điểm thành công trên giao diện!");
        }
    });
});