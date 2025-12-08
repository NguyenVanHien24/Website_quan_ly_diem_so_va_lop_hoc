document.addEventListener("DOMContentLoaded", function () {
    const attendanceButtons = document.querySelectorAll(".btn-attendance");
    
    attendanceButtons.forEach(btn => {
        btn.addEventListener("click", function () {
            const maHS = this.getAttribute("data-mhs");
            const date = this.getAttribute("data-date");
            const status = this.getAttribute("data-status");
            const isCurrentlyActive = this.classList.contains("active");
            
            // Nếu đã chọn rồi, bỏ chọn
            if (isCurrentlyActive) {
                this.classList.remove("active");
                updateSummary();
                return;
            }
            
            // Gửi request AJAX để lưu điểm danh
            fetch("save_attendance.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `maHS=${encodeURIComponent(maHS)}&date=${encodeURIComponent(date)}&status=${encodeURIComponent(status)}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        // Xóa active class khỏi các nút khác của học sinh này
                        const parentTd = this.parentElement;
                        const siblingButtons = parentTd.parentElement.querySelectorAll(".btn-attendance");
                        siblingButtons.forEach(btn => btn.classList.remove("active"));
                        
                        // Thêm active class vào nút được nhấn
                        this.classList.add("active");
                        
                        // Cập nhật thống kê
                        updateSummary();
                    } else {
                        alert("Lỗi: " + data.message);
                    }
                } catch (e) {
                    console.error("JSON Parse error:", e, "Response text:", text);
                    alert("Lỗi server: " + text);
                }
            })
            .catch(error => {
                console.error("Fetch error:", error);
                alert("Lỗi khi lưu điểm danh: " + error.message);
            });
        });
    });
    
    function updateSummary() {
        const present = document.querySelectorAll(".btn-present.active").length;
        const late = document.querySelectorAll(".btn-late.active").length;
        const absent = document.querySelectorAll(".btn-absent.active").length;
        const total = document.querySelectorAll(".btn-attendance").length / 3; // 3 nút mỗi học sinh
        const unchecked = total - present - late - absent;

        const countPresent = document.getElementById("count-present");
        const countLate = document.getElementById("count-late");
        const countAbsent = document.getElementById("count-absent");
        const countUnchecked = document.getElementById("count-unchecked");
        const countRate = document.getElementById("count-rate");

        if (countPresent) countPresent.textContent = present;
        if (countLate) countLate.textContent = late;
        if (countAbsent) countAbsent.textContent = absent;
        if (countUnchecked) countUnchecked.textContent = Math.ceil(unchecked);

        // Tính tỉ lệ đi học = (present / total) * 100
        let rate = 0;
        if (total > 0) {
            rate = ((present + late) / total) * 100;
        }
        if (countRate) countRate.textContent = rate.toFixed(1) + "%";
    }
    
    // Cập nhật thống kê khi trang load
    updateSummary();
});
