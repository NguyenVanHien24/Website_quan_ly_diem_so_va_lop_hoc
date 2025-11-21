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
                    
                    <form>
                        <div class="input-group-custom">
                            <input type="email" id="email" class="form-control-custom" placeholder="Email" required>
                             <i class="fa-regular fa-user input-icon"></i>
                        </div>
                        <div class="input-group-custom">
                            <input type="password" id="password" class="form-control-custom" placeholder="Mật khẩu" required>
                             <i class="fa-solid fa-lock input-icon"></i>
                        </div>
                        <a href="QuenMatKhau.html" class="forgot-password-link">Quên mật khẩu?</a>
                        <button type="submit" class="btn-submit">Đăng nhập</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</body>
</html>