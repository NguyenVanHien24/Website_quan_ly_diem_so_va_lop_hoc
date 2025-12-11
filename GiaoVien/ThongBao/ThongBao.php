<?php
session_start();
require_once '../../config.php';
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
$pageTitle = "Thông báo";
$currentPage = 'thong-bao';
$pageCSS = ['ThongBao.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['ThongBao.js'];
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

<main>
    <h1 class="page-title mb-4">THÔNG BÁO</h1>

    <div class="content-container bg-white p-0 border rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3" style="width: 70%;">Tiêu đề</th>
                        <th class="pe-4 py-3 text-end" style="width: 30%;">Ngày gửi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="notify-row"
                        data-title="Hướng dẫn Quy trình tổ chức đăng ký tín chỉ và chốt dữ liệu đào tạo"
                        data-content="Nội dung chi tiết về quy trình đăng ký tín chỉ..."
                        data-date="06/08/2025"
                        data-bs-toggle="modal" data-bs-target="#viewDetailModal">
                        <td class="ps-4 py-3 fw-bold text-primary-hover cursor-pointer">
                            Hướng dẫn Quy trình tổ chức đăng ký tín chỉ và chốt dữ liệu đào tạo
                        </td>
                        <td class="pe-4 py-3 text-end text-secondary">06/08/2025</td>
                    </tr>

                    <tr class="notify-row"
                        data-title="Khảo sát về công tác hỗ trợ cho sinh viên năm học 2024-2025"
                        data-content="Nội dung khảo sát..."
                        data-date="23/05/2025"
                        data-bs-toggle="modal" data-bs-target="#viewDetailModal">
                        <td class="ps-4 py-3 fw-bold text-primary-hover cursor-pointer">
                            Khảo sát về công tác hỗ trợ cho sinh viên năm học 2024-2025
                        </td>
                        <td class="pe-4 py-3 text-end text-secondary">23/05/2025</td>
                    </tr>

                    <tr class="notify-row"
                        data-title="LIKE PAGE Trường Đại học Sư phạm Hà Nội (https://facebook.com/dhsphnhnue)"
                        data-content="Hãy like page để cập nhật tin tức mới nhất..."
                        data-date="15/04/2025"
                        data-bs-toggle="modal" data-bs-target="#viewDetailModal">
                        <td class="ps-4 py-3 fw-bold text-primary-hover cursor-pointer">
                            LIKE PAGE Trường Đại học Sư phạm Hà Nội (https://facebook.com/dhsphnhnue)
                        </td>
                        <td class="pe-4 py-3 text-end text-secondary">15/04/2025</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="viewDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-light border-0">
                <div class="modal-header border-0 pb-0 pt-4 px-5">
                    <h2 class="modal-title fw-bold text-uppercase">CHI TIẾT THÔNG BÁO</h2>
                </div>
                <div class="modal-body pt-4 px-5 pb-5">
                    <form>
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 text-secondary fs-5">Tiêu đề:</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control bg-white" id="v_title" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-md-3 text-secondary fs-5 pt-2">Nội dung:</label>
                            <div class="col-md-9">
                                <textarea class="form-control bg-white" rows="6" id="v_content" readonly></textarea>
                            </div>
                        </div>

                        <div class="row mb-5 align-items-center">
                            <label class="col-md-4 text-secondary fs-5">Thời gian gửi thông báo:</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white" id="v_date" readonly>
                                    <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-dark px-4 py-2 text-uppercase fw-bold" data-bs-dismiss="modal" style="color: #0b1a48; border-color: #0b1a48;">
                                QUAY LẠI
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>
<?php require_once '../../footer.php'; ?>