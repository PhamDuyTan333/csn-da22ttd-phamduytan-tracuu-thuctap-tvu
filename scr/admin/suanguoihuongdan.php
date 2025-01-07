<?php
$connection = new mysqli('localhost', 'root', '', 'tracuuttsvtt');

// Kiểm tra kết nối
if ($connection->connect_error) {
    die("Kết nối thất bại: " . $connection->connect_error);
}

// Lấy mã người hướng dẫn từ URL
if (isset($_GET['ma_nguoi_huong_dan'])) {
    $ma_nguoi_huong_dan = $_GET['ma_nguoi_huong_dan'];

    // Lấy thông tin người hướng dẫn
    $result = $connection->query("SELECT * FROM nguoi_huong_dan WHERE ma_nguoi_huong_dan = '$ma_nguoi_huong_dan'");
    if ($result->num_rows == 0) {
        die("Không tìm thấy người hướng dẫn với mã này.");
    }
    $row = $result->fetch_assoc();
}

// Cập nhật thông tin người hướng dẫn
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'sua') {
    $ma_nguoi_huong_dan = $_POST['ma_nguoi_huong_dan'];
    $ma_loai_nguoi_huong_dan = $_POST['ma_loai_nguoi_huong_dan'];
    $ten = $_POST['ten'];
    $email = $_POST['email'];
    $so_dien_thoai = $_POST['so_dien_thoai'];
    $thong_tin_tai_khoan = $_POST['thong_tin_tai_khoan'];
    $cccd = $_POST['cccd'];
    $chuc_vu = $_POST['chuc_vu'];

    // Cập nhật thông tin người hướng dẫn
    $connection->query("UPDATE nguoi_huong_dan 
                        SET ma_loai_nguoi_huong_dan = '$ma_loai_nguoi_huong_dan', 
                            ten = '$ten', 
                            email = '$email', 
                            so_dien_thoai = '$so_dien_thoai', 
                            thong_tin_tai_khoan = '$thong_tin_tai_khoan',
                            cccd = '$cccd',
                            chuc_vu = '$chuc_vu'
                        WHERE ma_nguoi_huong_dan = '$ma_nguoi_huong_dan'");

    // Thực hiện chuyển hướng về trang quản lý sau khi cập nhật thành công
    echo '<script type="text/javascript">
            alert("Thông tin người hướng dẫn đã được cập nhật thành công.");
            window.location.href = "nguoihuongdan.php";  // Đổi thành trang danh sách người hướng dẫn của bạn
          </script>';
    exit;
}

// Đóng kết nối
$connection->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Người Hướng Dẫn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Nhúng thanh điều hướng -->
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h2>Sửa Thông Tin Người Hướng Dẫn</h2>

        <!-- Hiển thị thông báo thành công -->
        <?php if (isset($success_message)) { ?>
            <div class="alert alert-success">
                <?= $success_message ?>
            </div>
        <?php } ?>

        <!-- Form sửa thông tin người hướng dẫn -->
        <form method="POST">
            <input type="hidden" name="action" value="sua">
            <input type="hidden" name="ma_nguoi_huong_dan" value="<?= $row['ma_nguoi_huong_dan'] ?>">

            <div class="mb-3">
                <label for="ma_loai_nguoi_huong_dan" class="form-label">Loại Người Hướng Dẫn</label>
                <select class="form-control" id="ma_loai_nguoi_huong_dan" name="ma_loai_nguoi_huong_dan" required>
                    <option value="1" <?= $row['ma_loai_nguoi_huong_dan'] == 1 ? 'selected' : '' ?>>Người hướng dẫn tại đơn vị thực tập</option>
                    <option value="2" <?= $row['ma_loai_nguoi_huong_dan'] == 2 ? 'selected' : '' ?>>Người hướng dẫn tại trường</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="ten" class="form-label">Tên</label>
                <input type="text" class="form-control" id="ten" name="ten" value="<?= $row['ten'] ?>" >
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $row['email'] ?>" >
            </div>

            <div class="mb-3">
                <label for="so_dien_thoai" class="form-label">Số Điện Thoại</label>
                <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai" value="<?= $row['so_dien_thoai'] ?>" >
            </div>

            <div class="mb-3">
                <label for="thong_tin_tai_khoan" class="form-label">Thông Tin Tài Khoản Ngân Hàng(Tên&STK)</label>
                <textarea class="form-control" id="thong_tin_tai_khoan" name="thong_tin_tai_khoan" ><?= $row['thong_tin_tai_khoan'] ?></textarea>
            </div>

            <div class="mb-3">
                <label for="cccd" class="form-label">CCCD</label>
                <input type="text" class="form-control" id="cccd" name="cccd" value="<?= $row['cccd'] ?>" >
            </div>

            <div class="mb-3">
                <label for="chuc_vu" class="form-label">Chức Vụ</label>
                <input type="text" class="form-control" id="chuc_vu" name="chuc_vu" value="<?= $row['chuc_vu'] ?>" >
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>

    <!-- Thư viện Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
