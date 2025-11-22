<?php
session_start();

// KẾT NỐI DATABASE
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cdtn";

$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_set_charset($conn, "utf8");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$newPassword = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    // Kiểm tra email tồn tại
    $sql = "SELECT * FROM user WHERE email = ?";
    $stm = $conn->prepare($sql);
    $stm->bind_param("s", $email);
    $stm->execute();
    $result = $stm->get_result();

    if ($result->num_rows === 1) {

        // Tạo mật khẩu mới 8 ký tự
        $newPassword = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);

        // Mã hóa mật khẩu hoặc lưu thẳng tùy hệ thống:
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

        // Cập nhật mật khẩu vào DB
        $update = $conn->prepare("UPDATE user SET matKhau = ? WHERE email = ?");
        $update->bind_param("ss", $hashed, $email);
        $update->execute();
    } else {
        $error = "Email không tồn tại trong hệ thống!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="QuenMatKhau.css">
</head>

<body>
    <div class="container-fluid g-0">
        <div class="row g-0 main-container">
            <div class="col-lg-5 welcome-section">
                <h2>Hello, Welcome!</h2>
            </div>
            <div class="col-lg-7 form-section">
                <div class="form-wrapper">
                    <h1>Quên mật khẩu</h1>
                    <p class="subtitle">Nhập địa chỉ email để đặt lại mật khẩu</p>
                    <form method="POST" action="">
                        <div class="input-group-custom">
                            <input type="email" name="email" id="email" class="form-control-custom" placeholder="Email" required>
                            <i class="fa-regular fa-user input-icon"></i>
                        </div>
                        <button type="submit" class="btn-submit">Gửi</button>

                        <?php if (!empty($newPassword)): ?>
                            <div class="alert alert-success mt-3">
                                <p>Mật khẩu mới của bạn:</p>
                                <div class="d-flex align-items-center">
                                    <input type="text" id="newPass" value="<?php echo $newPassword; ?>" readonly class="form-control me-2" style="max-width: 200px;">
                                    <button type="button" class="btn btn-primary" onclick="copyPassword()">Copy</button>
                                </div>
                                <a href="DangNhap.php" class="btn btn-success mt-3 w-100">Quay về trang đăng nhập</a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger mt-3">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function copyPassword() {
            var copyText = document.getElementById("newPass");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // Cho mobile
            navigator.clipboard.writeText(copyText.value)
                .then(() => alert("Đã copy mật khẩu!"))
                .catch(err => alert("Không thể copy!"));
        }
    </script>
</body>

</html>