<?php
require_once '../../config.php';
// Đặt tên trang để active menu (nếu cần)
$currentPage = 'thong-bao';
$pageCSS = ['ThongBao.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['ThongBao.js'];
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
                    <?php
                    // Lấy thông báo thực từ CSDL cho user hiện tại
                    $userID = $_SESSION['userID'] ?? 0;
                    $sqlN = "SELECT tbu.id AS tbuId, tbu.trangThai, COALESCE(tb.send_at, tb.ngayGui) AS dateSend, tb.tieuDe, tb.noiDung, u.hoVaTen AS nguoiGui
                             FROM thongbaouser tbu
                             JOIN thongbao tb ON tbu.maTB = tb.maThongBao
                             LEFT JOIN `user` u ON tb.nguoiGui = u.userId
                             WHERE tbu.userId = ?
                             ORDER BY tbu.id DESC";
                    $stmtN = $conn->prepare($sqlN);
                    if ($stmtN) {
                        $stmtN->bind_param('i', $userID);
                        $stmtN->execute();
                        $rsN = $stmtN->get_result();
                        while ($r = $rsN->fetch_assoc()) {
                            $tbuId = (int)$r['tbuId'];
                            $trangThai = (int)($r['trangThai'] ?? 0);
                            $tieuDe = htmlspecialchars($r['tieuDe'] ?? '(Không tiêu đề)');
                            $noiDung = htmlspecialchars($r['noiDung'] ?? '');
                            $sender = htmlspecialchars($r['nguoiGui'] ?? 'Quản trị viên');
                            $dateRaw = $r['dateSend'] ?? null;
                            $dateDisp = $dateRaw ? date('d/m/Y H:i', strtotime($dateRaw)) : '';
                            $rowClass = $trangThai === 0 ? 'notify-row unread' : 'notify-row';
                            $bgStyle = $trangThai === 0 ? 'style="background-color:#eef6ff;"' : '';

                            echo '<tr class="' . $rowClass . '" ' . $bgStyle . ' data-tbuid="' . $tbuId . '" data-title="' . $tieuDe . '" data-content="' . $noiDung . '" data-date="' . $dateDisp . '" data-sender="' . $sender . '" data-file="" data-bs-toggle="modal" data-bs-target="#viewDetailModal">';
                            echo '<td class="ps-4 py-3 fw-bold text-primary-hover cursor-pointer">' . $tieuDe . '</td>';
                            echo '<td class="pe-4 py-3 text-end text-secondary">' . $dateDisp . '</td>';
                            echo '</tr>';
                        }
                        $stmtN->close();
                    } else {
                        echo '<tr><td colspan="2" class="text-center text-muted py-4">Không thể tải thông báo</td></tr>';
                    }
                    ?>
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
                                <div class="form-control-plaintext text-dark fw-bold fs-5" id="v_title_text">Tiêu đề 1</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-md-3 text-secondary fs-5 pt-2">Nội dung:</label>
                            <div class="col-md-9">
                                <textarea class="form-control bg-white" rows="5" id="v_content" readonly></textarea>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 text-secondary fs-5">Người gửi:</label>
                            <div class="col-md-9">
                                <div class="form-control-plaintext text-dark fs-5" id="v_sender">Admin1</div>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-4 text-secondary fs-5">Thời gian gửi thông báo:</label>
                            <div class="col-md-8">
                                <div class="form-control-plaintext text-dark fs-5" id="v_date_text">15/10/2025</div>
                            </div>
                        </div>

                        <div class="row mb-5 align-items-center">
                            <label class="col-md-3 text-secondary fs-5">Tệp đính kèm:</label>
                            <div class="col-md-9">
                                <a href="#" class="text-decoration-underline text-dark fs-5" id="v_file_link">BaiGiang_Chuong5.docx</a>
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