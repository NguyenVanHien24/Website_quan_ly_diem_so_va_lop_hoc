<?php
require_once '../config.php';
require_once '../csdl/db.php';
$pageTitle = "Dashboard";
$currentPage = 'dashboard';
$pageCSS = ['Dashboard.css'];
require_once 'SidebarAndHeader.php';

// ===== LẤY LOG =====
$sql = "
    SELECT g.*, u.hoVaTen 
    FROM ghilog g
    LEFT JOIN user u ON g.userId = u.userId
    ORDER BY g.thoiGian DESC
    LIMIT 3
";
$result = $conn->query($sql);

// ===== LẤY TỔNG SỐ HỌC SINH =====
$hsCount = $conn->query("SELECT COUNT(*) AS total FROM hocsinh")->fetch_assoc()['total'];

// ===== LẤY TỔNG SỐ GIÁO VIÊN =====
$gvCount = $conn->query("SELECT COUNT(*) AS total FROM giaovien")->fetch_assoc()['total'];

// ===== LẤY TỔNG SỐ LỚP HỌC =====
$lopCount = $conn->query("SELECT COUNT(*) AS total FROM lophoc")->fetch_assoc()['total'];

// ===== HÀM CHUYỂN THỜI GIAN THÀNH "X giờ trước" =====
function timeAgo($datetime)
{
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) return "Vừa xong";
    if ($diff < 3600) return floor($diff / 60) . " phút trước";
    if ($diff < 86400) return floor($diff / 3600) . " giờ trước";
    if ($diff < 604800) return floor($diff / 86400) . " ngày trước";

    return date("d/m/Y", $timestamp); // nếu > 7 ngày thì hiển thị ngày
}
?>
<!-- ========= BẮT ĐẦU NỘI DUNG CHÍNH CỦA TRANG ========= -->
<main>
    <h1 class="page-title">TỔNG QUAN</h1>
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="stat-card">
                <div class="stat-card-info">
                    <h6>TỔNG HỌC SINH</h6>
                    <div class="stat-number"><?= $hsCount ?></div>
                    <p>Học sinh hoạt động trong năm nay</p>
                </div>
                <i class="bi bi-mortarboard stat-icon"></i>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="stat-card">
                <div class="stat-card-info">
                    <h6>GIÁO VIÊN</h6>
                    <div class="stat-number"><?= $gvCount ?></div>
                    <p>Cán bộ/giáo viên</p>
                </div>
                <i class="bi bi-people stat-icon"></i>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="stat-card">
                <div class="stat-card-info">
                    <h6>LỚP HỌC</h6>
                    <div class="stat-number"><?= $lopCount ?></div>
                    <p>Lớp đang vận hành</p>
                </div>
                <i class="bi bi-bank stat-icon"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="content-panel">
                <h5 class="panel-title">HOẠT ĐỘNG GẦN ĐÂY</h5>
                <div class="activity-list">
                    <div class="activity-list">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="bi bi-info-circle-fill"></i>
                                    </div>

                                    <div class="activity-text">
                                        <p>
                                            <strong><?= $row['hoVaTen'] ?? 'Hệ thống' ?></strong>
                                            → <?= $row['hanhDong'] ?>
                                            (<?= $row['doiTuongTacDong'] ?>: <?= $row['maDoiTuong'] ?>)
                                        </p>

                                        <span><?= timeAgo($row['thoiGian']) ?></span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>Chưa có hoạt động nào.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="quick-actions-panel">
                <h5 class="panel-title">CÁC TÁC VỤ NHANH</h5>

                <a href="QuanLyHocSinh/QuanLyHocSinh.php" class="btn btn-action">
                    <div class="action-icon"><i class="bi bi-person-plus"></i></div>
                    THÊM HỌC SINH
                </a>

                <a href="QuanLyGiaoVien/QuanLyGiaoVien.php" class="btn btn-action">
                    <div class="action-icon"><i class="bi bi-person-video3"></i></div>
                    THÊM GIÁO VIÊN
                </a>

                <a href="QuanLyLopHoc/QuanLyLopHoc.php" class="btn btn-action">
                    <div class="action-icon"><i class="bi bi-easel"></i></div>
                    TẠO LỚP HỌC MỚI
                </a>
            </div>
        </div>
    </div>
</main>
<!-- ========= KẾT THÚC NỘI DUNG CHÍNH CỦA TRANG ========= -->
<?php
// Yêu cầu file footer.php để đóng các thẻ và tải JS
require_once '../footer.php';
?>