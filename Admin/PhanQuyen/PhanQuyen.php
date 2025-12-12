<?php
require_once '../../config.php';
require_once '../../CSDL/db.php';
// Đặt tên trang (giả sử thuộc nhóm quản lý tài khoản)
$currentPage = 'phan-quyen';
$pageCSS = ['PhanQuyen.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['PhanQuyen.js'];

// Lấy userId từ query string
$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
$user = null;
if ($userId > 0) {
    $stmt = $conn->prepare("SELECT u.userId, u.hoVaTen, u.email, u.sdt, u.vaiTro, g.maGV FROM `user` u LEFT JOIN giaovien g ON u.userId = g.userId WHERE u.userId = ? LIMIT 1");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows) {
        $user = $res->fetch_assoc();
    }
    $stmt->close();
}
?>

<main>
    <div class="content-wrapper">
        <?php if ($userId <= 0): ?>
            <h2 class="section-title">DANH SÁCH TÀI KHOẢN</h2>

            <?php
            // --- BẮT ĐẦU XỬ LÝ PHÂN TRANG ---
            $limit = 10; // Số tài khoản hiển thị trên mỗi trang
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Trang hiện tại, mặc định là 1
            $offset = ($page - 1) * $limit; // Vị trí bắt đầu lấy dữ liệu

            // 1. Đếm tổng số bản ghi
            $countQuery = $conn->query("SELECT COUNT(*) as total FROM `user`");
            $totalRows = $countQuery->fetch_assoc()['total'];
            $totalPages = ceil($totalRows / $limit);

            // 2. Lấy dữ liệu phân trang (Thêm LIMIT và OFFSET)
            // Lưu ý: Đã sửa câu lệnh SQL để thêm LIMIT
            $q = $conn->query("SELECT userId, hoVaTen, email, sdt, vaiTro FROM `user` ORDER BY userId DESC LIMIT $limit OFFSET $offset");
            // --- KẾT THÚC XỬ LÝ PHÂN TRANG ---
            ?>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:80px">STT</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>Vai trò</th>
                            <th style="width:160px">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($q && $q->num_rows):
                            // Tính STT dựa trên trang hiện tại: (Trang 1 bắt đầu từ 1, Trang 2 bắt đầu từ 11...)
                            $idx = $offset + 1;
                            while ($row = $q->fetch_assoc()):
                        ?>
                                <tr>
                                    <td><?php echo $idx++; ?></td>
                                    <td><?php echo htmlspecialchars($row['hoVaTen']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['sdt']); ?></td>
                                    <td><?php echo htmlspecialchars($row['vaiTro']); ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary" href="?userId=<?php echo intval($row['userId']); ?>">Phân quyền</a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Không có tài khoản</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-end mt-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="text-end text-muted small">
                    Hiển thị <?php echo ($offset + 1); ?> - <?php echo min($offset + $limit, $totalRows); ?> trên tổng số <?php echo $totalRows; ?> tài khoản
                </div>
            <?php endif; ?>
        <?php else: ?>
            <h2 class="section-title">THÔNG TIN TÀI KHOẢN</h2>

            <div class="form-container mb-5">
                <div class="row mb-4 align-items-center">
                    <div class="col-md-6">
                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label fw-bold text-end-md">Email đăng nhập:</label>
                            <div class="col-sm-8">
                                <input type="text" id="inpEmail" class="form-control" value="<?php echo $user ? htmlspecialchars($user['email']) : ''; ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label fw-bold text-end-md">Tên hiển thị:</label>
                            <div class="col-sm-8">
                                <input type="text" id="inpName" class="form-control" value="<?php echo $user ? htmlspecialchars($user['hoVaTen']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label fw-bold text-end-md">Mã user:</label>
                            <div class="col-sm-8">
                                <input type="text" id="inpCode" class="form-control" value="<?php echo $user && isset($user['maGV']) ? htmlspecialchars($user['maGV']) : ''; ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label fw-bold text-end-md">Số điện thoại:</label>
                            <div class="col-sm-8">
                                <input type="text" id="inpPhone" class="form-control" value="<?php echo $user ? htmlspecialchars($user['sdt']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="section-title">PHÂN QUYỀN</h2>

            <div class="form-container permission-box mb-5">
                <div class="row mb-3 align-items-center">
                    <label class="col-6 col-sm-4 fw-bold ps-4">Vai trò:</label>
                    <div class="col-6 col-sm-8">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="role" id="roleAdmin" value="Admin" <?php echo ($user && $user['vaiTro'] === 'Admin') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="roleAdmin">Admin</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="role" id="roleTeacher" value="GiaoVien" <?php echo ($user && $user['vaiTro'] === 'GiaoVien') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="roleTeacher">Giáo viên</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="role" id="roleStudent" value="HocSinh" <?php echo ($user && $user['vaiTro'] === 'HocSinh') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="roleStudent">Học sinh</label>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="userId" value="<?php echo $user ? intval($user['userId']) : 0; ?>">
            <div class="d-flex justify-content-end gap-3 mt-5">
                <a href="PhanQuyen.php" class="btn btn-cancel">Quay lại</a>
                <button type="button" class="btn btn-save">Lưu thông tin</button>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../../footer.php'; ?>