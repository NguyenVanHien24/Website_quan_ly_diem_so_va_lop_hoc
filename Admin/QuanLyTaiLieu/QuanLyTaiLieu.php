<?php
require_once '../../config.php';
$pageTitle = "Quản lý tài liệu";
$currentPage = 'tai-lieu';
$pageCSS = ['QuanLyTaiLieu.css'];
require_once '../SidebarAndHeader.php';
$pageJS = ['QuanLyTaiLieu.js'];
require_once '../../CSDL/db.php';
?>

<main>
    <h1 class="page-title">DANH SÁCH TÀI LIỆU</h1>

    <?php
    // Lấy danh sách lớp và môn để populate dropdown
    $selectedLop = isset($_GET['maLop']) ? (int)$_GET['maLop'] : 0;
    $selectedMon = isset($_GET['maMon']) ? (int)$_GET['maMon'] : 0;

    $classRs = $conn->query("SELECT maLop, tenLop FROM lophoc WHERE trangThai = 'active' ORDER BY tenLop");
    $subjectRs = $conn->query("SELECT maMon, tenMon FROM monhoc ORDER BY tenMon");
    ?>

    <div class="filter-container py-4 px-4 mb-4">
        <div class="row g-4">
            <div class="col-md-6">
                <label for="filterClass" class="form-label fw-bold fs-5">Lớp:</label>
                <select class="form-select py-2" id="filterClass">
                    <option value="">Chọn lớp...</option>
                    <?php if ($classRs) while ($c = $classRs->fetch_assoc()): ?>
                        <option value="<?php echo (int)$c['maLop']; ?>" <?php if ($selectedLop && $selectedLop == (int)$c['maLop']) echo 'selected'; ?>><?php echo htmlspecialchars($c['tenLop']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="filterSubject" class="form-label fw-bold fs-5">Môn:</label>
                <select class="form-select py-2" id="filterSubject">
                    <option value="">Chọn môn...</option>
                    <?php if ($subjectRs) while ($s = $subjectRs->fetch_assoc()): ?>
                        <option value="<?php echo (int)$s['maMon']; ?>" <?php if ($selectedMon && $selectedMon == (int)$s['maMon']) echo 'selected'; ?>><?php echo htmlspecialchars($s['tenMon']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="content-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th style="width: 25%;">TIÊU ĐỀ</th>
                        <th style="width: 30%;">MÔ TẢ</th>
                        <th>MÔN HỌC</th>
                        <th>NGƯỜI TẠO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Lấy danh sách tài liệu, có hỗ trợ lọc theo lớp và môn
                    $limit = 10; // Số tài liệu mỗi trang
                    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                    $offset = ($page - 1) * $limit;

                    // Xử lý bộ lọc
                    $where = [];
                    if ($selectedLop > 0) {
                        $where[] = "t.maLop = '" . $conn->real_escape_string($selectedLop) . "'";
                    }
                    if ($selectedMon > 0) {
                        $where[] = "t.maMon = '" . $conn->real_escape_string($selectedMon) . "'";
                    }
                    $whereSql = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

                    // 1. Đếm tổng số bản ghi (để tính số trang)
                    $sqlCount = "SELECT COUNT(*) as total FROM tailieu t" . $whereSql;
                    $resCount = $conn->query($sqlCount);
                    $totalRecords = $resCount->fetch_assoc()['total'];
                    $totalPages = ceil($totalRecords / $limit);

                    // 2. Lấy danh sách tài liệu theo trang
                    $sql = "SELECT t.maTaiLieu, t.tieuDe, t.moTa, t.fileTL, t.ngayTao, t.hanNop, t.maLop,
                                    m.tenMon AS tenMon,
                                    g.maGV, u.hoVaTen AS giaoVien
                                FROM tailieu t
                                LEFT JOIN monhoc m ON m.maMon = t.maMon
                                LEFT JOIN giaovien g ON t.maGV = g.maGV
                                LEFT JOIN `user` u ON g.userId = u.userId"
                        . $whereSql .
                        " ORDER BY t.ngayTao DESC 
                                LIMIT $limit OFFSET $offset"; // Thêm LIMIT và OFFSET

                    $res = $conn->query($sql);
                    $stt = $offset + 1;
                    if ($res && $res->num_rows > 0) {
                        while ($row = $res->fetch_assoc()) {
                            $tieuDe = htmlspecialchars($row['tieuDe'] ?? '');
                            $moTa = htmlspecialchars($row['moTa'] ?? '');
                            $mon = htmlspecialchars($row['tenMon'] ?? '');
                            $lop = htmlspecialchars($row['maLop'] ?? '');
                            $gv = htmlspecialchars($row['giaoVien'] ?? '---');
                            echo "<tr>";
                            echo "<td class=\"text-center fw-bold\">" . $stt++ . "</td>";
                            echo "<td class=\"fw-bold text-dark\">" . $tieuDe . "</td>";
                            echo "<td class=\"text-secondary\">" . $moTa . "</td>";
                            echo "<td class=\"text-secondary\">" . ($mon ?: '&nbsp;') . "</td>";
                            echo "<td class=\"text-secondary\">" . $gv . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo '<tr><td colspan="5" class="text-center text-secondary">Không có tài liệu nào</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer d-flex justify-content-between align-items-center mt-3 px-2">
            <?php
            $startShow = ($totalRecords > 0) ? $offset + 1 : 0;
            $endShow = min($offset + $limit, $totalRecords);

            // Tạo chuỗi query string để giữ bộ lọc khi chuyển trang
            $queryParams = [];
            if ($selectedLop) $queryParams['maLop'] = $selectedLop;
            if ($selectedMon) $queryParams['maMon'] = $selectedMon;

            // Hàm tạo link
            function createPageLink($p, $params)
            {
                $params['page'] = $p;
                return '?' . http_build_query($params);
            }
            ?>
            <div class="text-muted fw-bold text-secondary">
                Hiển thị <?= $startShow ?>-<?= $endShow ?>/<?= $totalRecords ?> mục
            </div>

            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page > 1) ? createPageLink($page - 1, $queryParams) : '#' ?>">&lt;</a>
                        </li>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= createPageLink($i, $queryParams) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page < $totalPages) ? createPageLink($page + 1, $queryParams) : '#' ?>">&gt;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

</main>
<?php require_once '../../footer.php'; ?>