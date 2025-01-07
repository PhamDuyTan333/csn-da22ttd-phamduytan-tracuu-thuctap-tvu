<?php
session_start();
$connection = new mysqli('localhost', 'root', '', 'tracuuttsvtt');

// Kiểm tra kết nối
if ($connection->connect_error) {
    die("Kết nối thất bại: " . $connection->connect_error);
}

// Xử lý đăng ký
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['dang_ky'])) {
    $ten_quan_tri = $_POST['ten_quan_tri'];
    $email = $_POST['email'];
    $mat_khau = password_hash($_POST['mat_khau'], PASSWORD_DEFAULT);

    // Kiểm tra dữ liệu
    if (empty($ten_quan_tri) || empty($email) || empty($_POST['mat_khau'])) {
        $error = "Vui lòng điền đầy đủ thông tin!";
    } else {
        // Kiểm tra email đã tồn tại
        $stmt = $connection->prepare("SELECT * FROM quan_tri WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Email đã tồn tại!";
        } else {
            // Thêm người dùng mới
            $stmt = $connection->prepare("INSERT INTO quan_tri (ten_quan_tri, email, mat_khau) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $ten_quan_tri, $email, $mat_khau);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Đăng ký thành công! Vui lòng đăng nhập.";
                header("Location: dangnhap.php");
                exit;
            } else {
                $error = "Lỗi khi đăng ký!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Đăng Ký</h2>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php } ?>
        <form method="POST">
            <div class="mb-3">
                <label for="ten_quan_tri" class="form-label">Tên Quản trị</label>
                <input type="text" class="form-control" id="ten_quan_tri" name="ten_quan_tri" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="mat_khau" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="mat_khau" name="mat_khau" required>
            </div>
            <button type="submit" name="dang_ky" class="btn btn-primary">Đăng Ký</button>
        </form>
        <p class="mt-3">Đã có tài khoản? <a href="dangnhap.php">Đăng nhập</a></p>
    </div>
</body>
</html>