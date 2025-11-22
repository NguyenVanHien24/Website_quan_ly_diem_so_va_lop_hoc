<?php
session_start();

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

// ====== XỬ LÝ ĐĂNG NHẬP ======
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $matkhau = trim($_POST["password"]);

    // Lấy user theo email
    $sql = "SELECT * FROM user WHERE email = ?";
    $stm = $conn->prepare($sql);
    $stm->bind_param("s", $email);
    $stm->execute();
    $result = $stm->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Nếu mật khẩu đã mã hoá bằng password_hash
        if (password_verify($matkhau, $user["matKhau"]) || $matkhau === $user["matKhau"]) {

            // Lưu session
            $_SESSION["userID"] = $user["userId"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["vaiTro"] = $user["vaiTro"];

            // Điều hướng theo vai trò
            if ($user["vaiTro"] === "Admin") {
                header("Location: ../Admin/Dashboard.php");
            } elseif ($user["vaiTro"] === "GiaoVien") {
                header("Location: ../GiaoVien/QuanLyChung/ThongTinCaNhan.php");
            } elseif ($user["vaiTro"] === "HocSinh") {
                header("Location: ../HocSinh/TrangCaNhan/ThongTinCaNhan.php");
            } else {
                $error = "Vai trò không hợp lệ!";
            }
            exit();
        } else {
            $error = "Sai mật khẩu!";
        }
    } else {
        $error = "Email không tồn tại!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="DangNhap.css">
</head>

<body>
    <div class="container-fluid g-0">
        <div class="row g-0 main-container">
            <div class="col-lg-5 welcome-section">
                <h2>Hello, Welcome!</h2>
            </div>

            <div class="col-lg-7 form-section">
                <div class="form-wrapper">
                    <h1>Đăng Nhập</h1>
                    <form method="POST" action="">
                        <div class="input-group-custom">
                            <input type="email" name="email" id="email" class="form-control-custom" placeholder="Email" required>
                            <i class="fa-regular fa-user input-icon"></i>
                        </div>

                        <div class="input-group-custom">
                            <input type="password" name="password" id="password" class="form-control-custom" placeholder="Mật khẩu" required>
                            <i class="fa-solid fa-lock input-icon"></i>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger mt-2"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <a href="QuenMatKhau.php" class="forgot-password-link">Quên mật khẩu?</a>
                        <button type="submit" class="btn-submit">Đăng nhập</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>