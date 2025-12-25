<?php
require_once '../../config.php';
$pageTitle = "Quản lý thông báo";
$currentPage = 'thong-bao';
$pageCSS = ['QuanLyThongBao.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['QuanLyThongBao.js'];
require_once '../../CSDL/db.php';

// Lấy danh sách lớp và người dùng để populate UI
$classList = [];
$classRs = $conn->query("SELECT maLop, tenLop FROM lophoc WHERE trangThai = 'active' ORDER BY tenLop");
if ($classRs) while ($r = $classRs->fetch_assoc()) $classList[] = $r;

$userList = [];
$userRs = $conn->query("SELECT userId, hoVaTen, vaiTro FROM `user` ORDER BY hoVaTen");
if ($userRs) while ($r = $userRs->fetch_assoc()) $userList[] = $r;
// Tính số lượng thông báo: tổng, đã gửi và đã lên lịch
$countTotal = 0;
$countSent = 0;
$countScheduled = 0;
$sqlCounts = "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN tb.send_at IS NOT NULL AND tb.send_at > NOW() THEN 1 ELSE 0 END) AS scheduled,
    SUM(CASE WHEN (tb.send_at IS NULL OR tb.send_at <= NOW()) AND EXISTS(SELECT 1 FROM `user` uu WHERE uu.userId = tb.nguoiGui AND uu.vaiTro = 'Admin') THEN 1 ELSE 0 END) AS sent
    FROM thongbao tb";
$cntRs = $conn->query($sqlCounts);
if ($cntRs) {
    $c = $cntRs->fetch_assoc();
    $countTotal = (int)($c['total'] ?? 0);
    $countScheduled = (int)($c['scheduled'] ?? 0);
    $countSent = (int)($c['sent'] ?? 0);
}

$status = isset($_GET['status']) ? trim($_GET['status']) : 'all';
?>

<main>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="page-title">THÔNG BÁO</h1>
        <button class="btn btn-primary btn-add-notify" data-bs-toggle="modal" data-bs-target="#addNotifyModal">
            THÊM THÔNG BÁO
        </button>
    </div>

    <div class="notify-tabs mb-3">
        <a href="?status=all" class="<?php echo ($status === 'all' ? 'tab-item active' : 'tab-item'); ?>">Tất cả (<?php echo $countTotal; ?>)</a>
        <a href="?status=sent" class="<?php echo ($status === 'sent' ? 'tab-item active' : 'tab-item'); ?>">Đã gửi (<?php echo $countSent; ?>)</a>
        <a href="?status=scheduled" class="<?php echo ($status === 'scheduled' ? 'tab-item active' : 'tab-item'); ?>">Đã lên lịch (<?php echo $countScheduled; ?>)</a>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th><input id="chkAll" class="form-check-input" type="checkbox"></th>
                        <th>STT</th>
                        <th>MÃ TB</th>
                        <th>TIÊU ĐỀ</th>
                        <th>NGƯỜI GỬI</th>
                        <th>NGƯỜI NHẬN</th>
                        <th>TRẠNG THÁI</th>
                        <th>TÁC VỤ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $limit = 10; // Số thông báo mỗi trang
                    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                    $offset = ($page - 1) * $limit;

                    $where = '';
                    if ($status === 'sent') {
                        $where = "WHERE (tb.send_at IS NULL OR tb.send_at <= NOW()) AND EXISTS(SELECT 1 FROM `user` uu WHERE uu.userId = tb.nguoiGui AND uu.vaiTro = 'Admin')";
                    } elseif ($status === 'scheduled') {
                        $where = "WHERE tb.send_at IS NOT NULL AND tb.send_at > NOW()";
                    }

                    // 1. Đếm tổng số bản ghi theo bộ lọc hiện tại (để tính số trang)
                    $sqlCount = "SELECT COUNT(*) as total FROM thongbao tb " . $where;
                    $resCount = $conn->query($sqlCount);
                    $totalRecords = $resCount->fetch_assoc()['total'];
                    $totalPages = ceil($totalRecords / $limit);

                    // 2. Truy vấn lấy dữ liệu có phân trang
                    $sql = "SELECT tb.maThongBao, tb.tieuDe, tb.noiDung, tb.ngayGui, u.hoVaTen AS nguoiGui,
                                   tb.target_type, tb.target_value, tb.send_at, tb.attachment
                            FROM thongbao tb
                            LEFT JOIN `user` u ON tb.nguoiGui = u.userId
                            " . $where . "
                            ORDER BY tb.ngayGui DESC
                            LIMIT $limit OFFSET $offset";

                    $rs = $conn->query($sql);
                    if ($rs && $rs->num_rows > 0) {
                        $i = $offset + 1;
                        while ($row = $rs->fetch_assoc()) {
                            $id = (int)$row['maThongBao'];
                            $code = 'TB' . str_pad($id, 5, '0', STR_PAD_LEFT);
                            $title = htmlspecialchars($row['tieuDe'] ?? '');
                            $content = htmlspecialchars($row['noiDung'] ?? '');
                            $date = htmlspecialchars($row['ngayGui'] ?? '');
                            $sender = htmlspecialchars($row['nguoiGui'] ?? '---');

                            $sendAtRaw = $row['send_at'] ?? null;

                            $realCnt = 0;
                            $countRs = $conn->query("SELECT COUNT(*) AS cnt FROM thongbaouser WHERE maTB = " . $id);
                            if ($countRs) {
                                $c = $countRs->fetch_assoc();
                                $realCnt = (int)($c['cnt'] ?? 0);
                            }

                            $predictedCnt = 0;
                            if ($realCnt === 0) {
                                $tt = $row['target_type'] ?? 'all';
                                $tv = $row['target_value'] ?? '';
                                if ($tt === 'all') {
                                    $r2 = $conn->query("SELECT COUNT(*) AS cnt FROM `user`");
                                    if ($r2) {
                                        $c2 = $r2->fetch_assoc();
                                        $predictedCnt = (int)($c2['cnt'] ?? 0);
                                    }
                                } elseif ($tt === 'role') {
                                    $role = $conn->real_escape_string($tv);
                                    $r2 = $conn->query("SELECT COUNT(*) AS cnt FROM `user` WHERE vaiTro = '" . $role . "'");
                                    if ($r2) {
                                        $c2 = $r2->fetch_assoc();
                                        $predictedCnt = (int)($c2['cnt'] ?? 0);
                                    }
                                } elseif ($tt === 'class') {
                                    $maLop = (int)$tv;
                                    $r2 = $conn->query("SELECT COUNT(*) AS cnt FROM `user` u JOIN hocsinh hs ON u.userId = hs.userId WHERE hs.maLopHienTai = '" . $maLop . "'");
                                    if ($r2) {
                                        $c2 = $r2->fetch_assoc();
                                        $predictedCnt = (int)($c2['cnt'] ?? 0);
                                    }
                                } elseif ($tt === 'users') {
                                    $arr = json_decode($tv, true);
                                    if (is_array($arr)) $predictedCnt = count($arr);
                                }
                            }

                            $isScheduled = false;
                            if ($sendAtRaw !== null && $sendAtRaw !== '' && strtotime($sendAtRaw) > time() && $realCnt === 0) $isScheduled = true;

                            echo '<tr>';
                            echo '<td><input class="form-check-input row-chk" type="checkbox" value="' . $id . '"></td>';
                            echo '<td>' . $i++ . '</td>';
                            echo '<td>' . $code . '</td>';
                            echo '<td>' . $title . '</td>';
                            echo '<td>' . $sender . '</td>';
                            if ($isScheduled) {
                                echo '<td>' . ($predictedCnt > 0 ? ($predictedCnt . ' người dự kiến') : 'Chưa phân phối') . '</td>';
                                echo '<td><span class="text-warning fw-bold">ĐÃ LÊN LỊCH</span></td>';
                            } else {
                                echo '<td>' . ($realCnt > 0 ? ($realCnt . ' người') : ($predictedCnt > 0 ? ($predictedCnt . " người") : 'Chưa phân phối')) . '</td>';
                                echo '<td><span class="text-secondary fw-bold">ĐÃ GỬI</span></td>';
                            }

                            $receiverLabel = '';
                            $tt = $row['target_type'] ?? 'all';
                            $tv = $row['target_value'] ?? '';
                            if ($isScheduled) {
                                if ($tt === 'all') {
                                    $receiverLabel = 'Toàn hệ thống';
                                    $receiverRoles = ['all'];
                                } elseif ($tt === 'role') {
                                    $map = ['GiaoVien' => 'Giáo viên', 'HocSinh' => 'Học sinh', 'Admin' => 'Admin'];
                                    $receiverLabel = $map[$tv] ?? $tv;
                                    $receiverRoles = [$tv];
                                } elseif ($tt === 'class') {
                                    $maLop = (int)$tv;
                                    $r2 = $conn->query("SELECT tenLop FROM lophoc WHERE maLop = " . $maLop . " LIMIT 1");
                                    if ($r2 && $r2->num_rows > 0) {
                                        $receiverLabel = $r2->fetch_assoc()['tenLop'];
                                    } else {
                                        $receiverLabel = 'Lớp #' . $maLop;
                                    }
                                    $receiverRoles = ['HocSinh'];
                                } elseif ($tt === 'users') {
                                    $arr = json_decode($tv, true);
                                    if (is_array($arr) && count($arr) > 0) {

                                        $ids = array_map('intval', $arr);
                                        $in = implode(',', $ids);
                                        $rsn = $conn->query("SELECT hoVaTen, vaiTro FROM `user` WHERE userId IN (" . $in . ") LIMIT 10");
                                        $names = [];
                                        $rolesTmp = [];
                                        if ($rsn) while ($rrr = $rsn->fetch_assoc()) {
                                            $names[] = $rrr['hoVaTen'];
                                            if (!empty($rrr['vaiTro'])) $rolesTmp[] = $rrr['vaiTro'];
                                        }
                                        $receiverLabel = implode(', ', $names);
                                        $receiverRoles = array_values(array_unique($rolesTmp));
                                    } else {
                                        $receiverLabel = 'Chọn người nhận cụ thể';
                                        $receiverRoles = [];
                                    }
                                }
                            } else {
                                $rsr = $conn->query("SELECT u.hoVaTen, u.vaiTro FROM thongbaouser tbu JOIN `user` u ON tbu.userId = u.userId WHERE tbu.maTB = " . $id . " LIMIT 50");
                                $names = [];
                                $roles = [];
                                if ($rsr) {
                                    while ($rr = $rsr->fetch_assoc()) {
                                        $names[] = $rr['hoVaTen'];
                                        if (!empty($rr['vaiTro'])) $roles[] = $rr['vaiTro'];
                                    }
                                }
                                if (!empty($names)) {
                                    $receiverLabel = implode(', ', $names);
                                    $receiverRoles = array_values(array_unique($roles));
                                } else {
                                    if ($tt === 'all') {
                                        $receiverLabel = 'Toàn hệ thống';
                                        $receiverRoles = ['all'];
                                    } elseif ($tt === 'role') {
                                        $map = ['GiaoVien' => 'Giáo viên', 'HocSinh' => 'Học sinh', 'Admin' => 'Admin'];
                                        $receiverLabel = $map[$tv] ?? $tv;
                                        $receiverRoles = [$tv];
                                    } elseif ($tt === 'class') {
                                        $receiverLabel = 'Lớp #' . (int)$tv;
                                        $receiverRoles = ['HocSinh'];
                                    } else {
                                        $receiverLabel = 'Chưa phân phối';
                                        $receiverRoles = [];
                                    }
                                }
                            }

                            $dataReceiver = ($realCnt > 0) ? $realCnt : $predictedCnt;
                            $dataAttrs = 'data-ma="' . $id . '" data-id="' . $code . '" data-title="' . $title . '" data-content="' . $content . '" data-date="' . $date . '" data-receiver="' . $dataReceiver . '"';
                            $dataAttrs .= ' data-target_type="' . htmlspecialchars($row['target_type'] ?? 'all') . '"';
                            $dataAttrs .= ' data-target_value="' . htmlspecialchars($row['target_value'] ?? '') . '"';
                            $dataAttrs .= ' data-send_at="' . htmlspecialchars($row['send_at'] ?? '') . '"';
                            $dataAttrs .= ' data-attachment="' . htmlspecialchars($row['attachment'] ?? '') . '"';
                            $dataAttrs .= ' data-recipients="' . htmlspecialchars($receiverLabel) . '"';
                            $dataAttrs .= ' data-rec-roles="' . htmlspecialchars(json_encode($receiverRoles), ENT_QUOTES) . '"';
                            echo '<td class="action-icons">';
                            if ($isScheduled) {
                                echo '<a href="#" class="btn-send-now" data-ma="' . $id . '" title="Gửi ngay"><i class="bi bi-send-fill"></i></a> ';
                            }
                            echo '<a href="#" class="btn-view" ' . $dataAttrs . ' data-bs-toggle="modal" data-bs-target="#viewNotifyModal"><i class="bi bi-box-arrow-up-right"></i></a> ';
                            echo '<a href="#" class="btn-edit" ' . $dataAttrs . ' data-bs-toggle="modal" data-bs-target="#editNotifyModal"><i class="bi bi-pencil-square"></i></a> ';
                            echo '<a href="#" class="btn-delete" data-id="' . $id . '" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"><i class="bi bi-trash-fill"></i></a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="8" class="text-center text-muted py-4">Chưa có thông báo</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer d-flex justify-content-between align-items-center mt-3">
            <?php
            $startShow = ($totalRecords > 0) ? $offset + 1 : 0;
            $endShow = min($offset + $limit, $totalRecords);

            function createPageLink($p, $s)
            {
                return "?status=" . htmlspecialchars($s) . "&page=" . $p;
            }
            ?>
            <div class="text-muted">Hiển thị <?= $startShow ?>-<?= $endShow ?>/<?= $totalRecords ?> mục</div>

            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page > 1) ? createPageLink($page - 1, $status) : '#' ?>">&lt;</a>
                        </li>

                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= createPageLink($p, $status) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page < $totalPages) ? createPageLink($page + 1, $status) : '#' ?>">&gt;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button class="btn btn-danger fw-bold px-4 py-2" id="btnDeleteMulti">Xóa thông báo</button>
    </div>


    <div class="modal fade" id="addNotifyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-4">
                <h2 class="modal-title fw-bold mb-4">THÊM THÔNG BÁO</h2>
                <form id="addForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tiêu đề:</label>
                        <input type="text" class="form-control" id="a_title" name="title" placeholder="Tiêu đề 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung:</label>
                        <textarea class="form-control" id="a_content" name="content" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Thời gian gửi thông báo (tùy chọn):</label>
                        <div class="input-group">
                            <input type="datetime-local" class="form-control" id="a_send_at" name="send_at">
                            <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold me-3">Người nhận:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="target_type" id="t_all" value="all" checked>
                            <label class="form-check-label" for="t_all">Toàn hệ thống</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="target_type" id="t_role" value="role">
                            <label class="form-check-label" for="t_role">Theo vai trò</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="target_type" id="t_class" value="class">
                            <label class="form-check-label" for="t_class">Theo lớp</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="target_type" id="t_users" value="users">
                            <label class="form-check-label" for="t_users">Chọn người nhận cụ thể</label>
                        </div>
                        <div class="mt-3" id="targetControls">
                            <select class="form-select mb-2" id="a_role_select" style="display:none;">
                                <option value="">Chọn vai trò...</option>
                                <option value="GiaoVien">Giáo viên</option>
                                <option value="HocSinh">Học sinh</option>
                                <option value="Admin">Admin</option>
                            </select>
                            <select class="form-select mb-2" id="a_class_select" style="display:none;">
                                <option value="">Chọn lớp...</option>
                                <?php foreach ($classList as $c): ?>
                                    <option value="<?php echo (int)$c['maLop']; ?>"><?php echo htmlspecialchars($c['tenLop']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-select mb-2" id="a_users_select" multiple style="display:none; height:140px;">
                                <?php foreach ($userList as $u): ?>
                                    <option value="<?php echo (int)$u['userId']; ?>"><?php echo htmlspecialchars(($u['hoVaTen'] ?: 'User') . ' [' . $u['vaiTro'] . ']'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Đính kèm tệp (Tùy chọn):</label>
                        <div class="d-flex align-items-center">
                            <input type="file" id="a_file" name="attachment" class="form-control me-2">
                            <span class="text-muted fst-italic" id="a_file_label">Không tệp nào được chọn</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">HỦY</button>
                        <button type="button" class="btn btn-primary px-4" id="btnSendNotify" style="background-color: #0b1a48;">GỬI THÔNG BÁO</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editNotifyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-4 bg-light">
                <h2 class="modal-title fw-bold mb-4">CHỈNH SỬA THÔNG BÁO</h2>
                <form id="editForm">
                    <input type="hidden" id="e_ma" name="maThongBao">
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 text-secondary">Mã thông báo:</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="e_id" disabled>
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 text-secondary">Tiêu đề:</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="e_title">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-3 text-secondary pt-2">Nội dung:</label>
                        <div class="col-md-9">
                            <textarea class="form-control" rows="4" id="e_content"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 text-secondary">Thời gian gửi thông báo:</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <input type="datetime-local" class="form-control" id="e_date" name="send_at">
                                <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4 align-items-center">
                        <label class="col-md-3 text-secondary">Người nhận:</label>
                        <div class="col-md-9">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="target_type_edit" id="et_all" value="all">
                                <label class="form-check-label" for="et_all">Toàn hệ thống</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="target_type_edit" id="et_role" value="role">
                                <label class="form-check-label" for="et_role">Theo vai trò</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="target_type_edit" id="et_class" value="class">
                                <label class="form-check-label" for="et_class">Theo lớp</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="target_type_edit" id="et_users" value="users">
                                <label class="form-check-label" for="et_users">Chọn người nhận cụ thể</label>
                            </div>
                            <div class="mt-3" id="e_targetControls">
                                <select class="form-select mb-2" id="e_role_select" style="display:none;">
                                    <option value="">Chọn vai trò...</option>
                                    <option value="GiaoVien">Giáo viên</option>
                                    <option value="HocSinh">Học sinh</option>
                                    <option value="Admin">Admin</option>
                                </select>
                                <select class="form-select mb-2" id="e_class_select" style="display:none;">
                                    <option value="">Chọn lớp...</option>
                                    <?php foreach ($classList as $c): ?>
                                        <option value="<?php echo (int)$c['maLop']; ?>"><?php echo htmlspecialchars($c['tenLop']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select class="form-select mb-2" id="e_users_select" multiple style="display:none; height:140px;">
                                    <?php foreach ($userList as $u): ?>
                                        <option value="<?php echo (int)$u['userId']; ?>"><?php echo htmlspecialchars(($u['hoVaTen'] ?: 'User') . ' [' . $u['vaiTro'] . ']'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <div class="mb-4 w-100">
                            <label class="form-label fw-bold">Đính kèm tệp (Tùy chọn):</label>
                            <input type="file" id="e_file" name="attachment" class="form-control">
                            <div id="e_attachment_display" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">HỦY</button>
                        <button type="button" class="btn btn-success px-4 text-white fw-bold" id="btnUpdateNotify">LƯU</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewNotifyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-4 bg-light">
                <h2 class="modal-title fw-bold mb-4">CHI TIẾT THÔNG BÁO</h2>
                <form>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 text-secondary">Mã thông báo:</label>
                        <div class="col-md-9"><input type="text" class="form-control bg-white" id="v_id" readonly></div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 text-secondary">Tiêu đề:</label>
                        <div class="col-md-9"><input type="text" class="form-control bg-white" id="v_title" readonly></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-3 text-secondary pt-2">Nội dung:</label>
                        <div class="col-md-9"><textarea class="form-control bg-white" rows="4" id="v_content" readonly></textarea></div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 text-secondary">Thời gian tạo thông báo:</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <input type="text" class="form-control bg-white" id="v_date" readonly>
                                <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 text-secondary">Tệp đính kèm:</label>
                        <div class="col-md-9">
                            <div id="v_attachment_display"></div>
                        </div>
                    </div>
                    <div class="row mb-4 align-items-center">
                        <label class="col-md-3 text-secondary">Người nhận:</label>
                        <div class="col-md-9">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="receiverView" id="vrx1" value="all" disabled>
                                <label class="form-check-label">Toàn hệ thống</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="receiverView" id="vrx2" value="teacher" disabled>
                                <label class="form-check-label">Giáo viên</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="receiverView" id="vrx3" value="student" disabled>
                                <label class="form-check-label">Học sinh</label>
                            </div>
                            <!-- <div id="v_recipients" class="mt-2"></div> -->
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-dark px-4" data-bs-dismiss="modal">QUAY LẠI</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-transparent">
                <div class="modal-body p-0">
                    <div class="delete-box position-relative mx-auto p-4 text-center bg-white rounded-3 shadow-sm" style="max-width: 400px;">
                        <div class="question-icon">?</div>
                        <div class="bg-light rounded-3 py-4 px-3 mt-3 mb-4">
                            <h5 class="fw-bold text-dark m-0" id="deleteMsg">Bạn chắc chắn muốn xóa thông báo?</h5>
                        </div>
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-outline-dark px-4 fw-bold" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-danger px-4 fw-bold" id="btnConfirmDelete">Xóa</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
<script>
    window.BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<?php require_once '../../footer.php'; ?>