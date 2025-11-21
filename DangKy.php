<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="DangKy.css">
</head>
<body>
    <div class="container-fluid g-0">
        <div class="row g-0 main-container">
            <div class="col-lg-7 form-section">
                <div class="form-wrapper">
                    <h1>Đăng Ký</h1>
                    <form>
                        <div class="mb-4">
                            <label class="form-label d-block mb-2" style="color: var(--light-text-color);">Đăng ký dành cho:</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="role" id="teacher" value="teacher">
                                <label class="form-check-label" for="teacher">Giáo viên</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="role" id="student" value="student" checked>
                                <label class="form-check-label" for="student">Học sinh</label>
                            </div>
                        </div>
                        <div class="input-group-custom">
                            <input type="text" id="fullName" class="form-control-custom" placeholder="Họ tên" required>
                            <i class="fa-regular fa-user input-icon"></i>
                        </div>

                        <div class="input-group-custom">
                            <input type="email" id="email" class="form-control-custom" placeholder="Email" required>
                             <i class="fa-regular fa-envelope input-icon"></i>
                        </div>

                        <div class="input-group-custom">
                            <input type="password" id="password" class="form-control-custom" placeholder="Mật khẩu" required>
                             <i class="fa-solid fa-lock input-icon"></i>
                        </div>
                        <div class="input-group-custom">
                            <input type="password" id="confirmPassword" class="form-control-custom" placeholder="Nhập lại mật khẩu" required>
                            <i class="fa-solid fa-lock input-icon"></i>
                        </div>
                        <button type="submit" class="btn-submit">Đăng ký</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-5 welcome-section">
                <h2>Welcome Back!</h2>
                <p>Bạn đã có tài khoản?</p>
                <a href="DangNhap.html" class="btn-login">Đăng Nhập</a>
            </div>

        </div>
    </div>

</body>
</html>