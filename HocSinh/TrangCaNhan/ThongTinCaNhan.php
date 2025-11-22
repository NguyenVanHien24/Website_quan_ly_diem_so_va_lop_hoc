<?php
require_once '../../config.php';
session_start();

// ==== Kiểm tra đăng nhập ====
if (!isset($_SESSION['userID'])) {
    header('Location: ../dangnhap.php');
    exit();
}

// ==== Chỉ cho phép giáo viên ====
if ($_SESSION['vaiTro'] !== 'HocSinh') {
    header('Location: ../dangnhap.php');
    exit();
}

$currentPage = 'thong-tin';
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

// ==== Lấy thông tin học sinh ====
$userID = $_SESSION['userID'];
$sql = "SELECT u.hoVaTen, u.email, u.sdt, u.gioiTinh, h.maLopHienTai, h.maHS
        FROM user u
        JOIN hocsinh h ON u.userId = h.userId
        WHERE u.userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();
$stmt->close();
?>

<main>
    <div class="container-fluid p-4">
        <h2 class="page-title mb-5">Thông tin cá nhân</h2>

        <div class="row align-items-start">
            <div class="col-md-4 d-flex justify-content-center mb-4 mb-md-0">
                <div class="avatar-container">
                    <i class="bi bi-person-fill"></i>
                </div>
            </div>

            <div class="col-md-8 ps-md-5">
                <div class="info-list">
                    <div class="row mb-3">
                        <div class="col-4 col-sm-3 fw-bold text-dark">Mã học sinh:</div>
                        <div class="col-8 col-sm-9 text-secondary"><?= htmlspecialchars($teacher['maHS']) ?></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4 col-sm-3 fw-bold text-dark">Họ và tên:</div>
                        <div class="col-8 col-sm-9 text-secondary"><?= htmlspecialchars($teacher['hoVaTen']) ?></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4 col-sm-3 fw-bold text-dark">Lớp:</div>
                        <div class="col-8 col-sm-9 text-secondary"><?= htmlspecialchars($teacher['maLopHienTai']) ?></div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-4 col-sm-3 fw-bold text-dark">Chức vụ:</div>
                        <div class="col-8 col-sm-9 text-secondary">Học viên</div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-6 col-sm-3">
                            <span class="fw-bold text-dark me-2">Tuổi:</span>
                            <span class="text-secondary">16</span>
                        </div>
                        <div class="col-6 col-sm-4">
                            <span class="fw-bold text-dark me-2">Giới tính:</span>
                            <span class="text-secondary"><?= htmlspecialchars($teacher['gioiTinh']) ?></span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-4">
                        <div class="contact-box">
                            <div class="icon-wrap">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($teacher['sdt']) ?></span>
                        </div>

                        <div class="contact-box">
                            <div class="icon-wrap">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($teacher['email']) ?></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../footer.php'; ?>