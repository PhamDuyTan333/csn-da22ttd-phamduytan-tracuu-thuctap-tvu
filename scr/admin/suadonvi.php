<?php
session_start(); // Bắt đầu phiên làm việc

$connection = new mysqli('localhost', 'root', '', 'tracuuttsvtt');

// Kiểm tra kết nối
if ($connection->connect_error) {
    die("Kết nối thất bại: " . $connection->connect_error);
}

// Lấy thông tin đơn vị để chỉnh sửa
if (isset($_GET['ma_don_vi'])) {
    $ma_don_vi = $_GET['ma_don_vi'];
    $stmt = $connection->prepare("SELECT * FROM don_vi WHERE ma_don_vi = ?");
    $stmt->bind_param("s", $ma_don_vi);
    $stmt->execute();
    $result = $stmt->get_result();
    $unit = $result->fetch_assoc();
    $stmt->close();
}

// Cập nhật thông tin đơn vị
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_unit'])) {
    $ma_don_vi = $_POST['ma_don_vi'];
    $ten_don_vi = $_POST['ten_don_vi'];
    $dia_chi = $_POST['dia_chi'];
    $so_dien_thoai = $_POST['so_dien_thoai'];
    $email = $_POST['email'];
    $loai_don_vi = $_POST['loai_don_vi'];

    // Kiểm tra thông tin đầu vào
    if (empty($ma_don_vi) || empty($ten_don_vi) || empty($dia_chi) || empty($so_dien_thoai) || empty($email) || empty($loai_don_vi)) {
        $_SESSION['message'] = "Vui lòng điền đầy đủ thông tin!";
    } else {
        // Chuẩn bị câu lệnh cập nhật
        $stmt = $connection->prepare("UPDATE don_vi SET ten_don_vi = ?, dia_chi = ?, so_dien_thoai = ?, email = ?, loai_don_vi = ? WHERE ma_don_vi = ?");
        $stmt->bind_param("ssssss", $ten_don_vi, $dia_chi, $so_dien_thoai, $email, $loai_don_vi, $ma_don_vi);

        if ($stmt->execute()) {
            // Hiển thị thông báo thành công và chuyển hướng về trang quản lý
            echo '<script type="text/javascript">
                    alert("Thông tin đơn vị đã được cập nhật thành công.");
                    window.location.href = "donvi.php";  // Đổi thành trang danh sách đơn vị của bạn
                  </script>';
            exit;
        } else {
            $_SESSION['message'] = "Lỗi khi cập nhật đơn vị!";
        }
        $stmt->close();
    }
}

// Đóng kết nối
$connection->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật Đơn vị thực tập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h2>Cập nhật Đơn vị thực tập</h2>

        <?php if (isset($_SESSION['message'])) { ?>
            <div class="alert alert-info"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php } ?>

        <!-- Form chỉnh sửa đơn vị -->
        <form action="suadonvi.php?ma_don_vi=<?= $unit['ma_don_vi'] ?>" method="POST">
            <div class="mb-3">
                <label for="ma_don_vi" class="form-label">Mã Đơn vị</label>
                <input type="text" class="form-control" id="ma_don_vi" name="ma_don_vi" value="<?= $unit['ma_don_vi'] ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="ten_don_vi" class="form-label">Tên Đơn vị</label>
                <input type="text" class="form-control" id="ten_don_vi" name="ten_don_vi" value="<?= $unit['ten_don_vi'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="dia_chi" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control" id="dia_chi" name="dia_chi" value="<?= $unit['dia_chi'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai" value="<?= $unit['so_dien_thoai'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $unit['email'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="loai_don_vi" class="form-label">Loại Đơn vị</label>
                <select class="form-select" id="loai_don_vi" name="loai_don_vi" required>
                    <option value="Trong trường" <?= $unit['loai_don_vi'] == 'Trong trường' ? 'selected' : '' ?>>Trong trường</option>
                    <option value="Ngoài trường" <?= $unit['loai_don_vi'] == 'Ngoài trường' ? 'selected' : '' ?>>Ngoài trường</option>
                </select>
            </div>

            <button type="submit" name="update_unit" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
