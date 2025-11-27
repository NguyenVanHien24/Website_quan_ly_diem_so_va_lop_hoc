<?php
require_once '../../config.php';
session_start();

// ==== Kiểm tra đăng nhập ====
if (!isset($_SESSION['userID'])) {
    header('Location: ../../dangnhap.php');
    exit();
}

// ==== Chỉ cho phép giáo viên ====
if ($_SESSION['vaiTro'] !== 'GiaoVien') {
    header('Location: ../../dangnhap.php');
    exit();
}

$currentPage = 'thong-tin';
// Gọi file CSS riêng
$pageCSS = ['ThongTinCaNhan.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['ThongTinCaNhan.js'];

// ====== KẾT NỐI DATABASE ======
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cdtn";

$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_set_charset($conn, "utf8");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// ==== Lấy thông tin giáo viên từ DB ====
$userID = $_SESSION['userID'];
$sql = "SELECT u.hoVaTen, u.email, u.sdt, u.gioiTinh, g.boMon
        FROM user u
        JOIN giaovien g ON u.userId = g.userId
        WHERE u.userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();
$stmt->close();
?>

<main class="p-4">
    <h2 class="page-title mb-5">Thông tin cá nhân</h2>

    <div class="row">
        <div class="col-md-4 d-flex flex-column align-items-center text-center">
            <div class="profile-avatar mb-4">
                <i class="bi bi-person-fill"></i>
            </div>
            <h4 class="fw-bold mb-1"><?= htmlspecialchars($teacher['hoVaTen']) ?></h4>
            <p class="text-secondary"><?= htmlspecialchars($teacher['boMon']) ?></p>
        </div>

        <div class="col-md-8 ps-md-5">
            <div class="info-section mb-4">
                <h6 class="fw-bold text-dark">Giới thiệu chung:</h6>
                <p class="text-secondary">
                    <?= htmlspecialchars($teacher['hoVaTen']) ?> là một giáo viên tận tâm, có nhiều năm kinh nghiệm trong giảng dạy,
                    luôn quan tâm và khuyến khích học sinh phát triển cả kiến thức lẫn kỹ năng sống.
                </p>
            </div>

            <div class="info-section mb-4">
                <h6 class="fw-bold text-dark">Bằng cấp/Chuyên môn</h6>
                <ul class="list-unstyled text-secondary">
                    <li class="mb-1">Cử nhân Sư phạm <?= htmlspecialchars($teacher['boMon']) ?></li>
                    <li class="mb-1">Thạc sĩ Lý luận và Phương pháp dạy học <?= htmlspecialchars($teacher['boMon']) ?></li>
                    <li>Chứng chỉ Nghiệp vụ sư phạm</li>
                </ul>
            </div>

            <div class="row mb-5">
                <div class="col-6 col-md-3">
                    <h6 class="fw-bold text-dark small mb-1">Tuổi</h6>
                    <p class="text-secondary">34</p>
                </div>
                <div class="col-6 col-md-3">
                    <h6 class="fw-bold text-dark small mb-1">Giới tính</h6>
                    <p class="text-secondary"><?= htmlspecialchars($teacher['gioiTinh']) ?></p>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-4">
                <div class="contact-box">
                    <div class="icon-wrapper">
                        <i class="bi bi-telephone"></i>
                    </div>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($teacher['sdt']) ?></span>
                </div>

                <div class="contact-box">
                    <div class="icon-wrapper">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($teacher['email']) ?></span>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../footer.php'; ?>