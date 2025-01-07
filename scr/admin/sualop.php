<?php 
session_start();  // Bắt đầu session

$connection = new mysqli('localhost', 'root', '', 'tracuuttsvtt');

// Kiểm tra kết nối
if ($connection->connect_error) {
    die("Kết nối thất bại: " . $connection->connect_error);
}

// Lấy mã xác thực lớp từ URL (nếu có)
if (isset($_GET['edit'])) {
    $ma_xac_thuc_lop = $_GET['edit'];

    // Lấy thông tin lớp từ cơ sở dữ liệu
    $stmt = $connection->prepare("SELECT * FROM lop WHERE ma_xac_thuc_lop = ?");
    $stmt->bind_param("s", $ma_xac_thuc_lop);
    $stmt->execute();
    $result = $stmt->get_result();
    $class = $result->fetch_assoc();

    if (!$class) {
        $_SESSION['message'] = "Lớp không tồn tại.";
        header("Location: lop.php");
        exit;
    }
    $stmt->close();
} else {
    $_SESSION['message'] = "Không có lớp để sửa.";
    header("Location: lop.php");
    exit;
}

// Cập nhật thông tin lớp
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_class'])) {
    $ma_lop = $_POST['ma_lop'];
    $ten_lop = $_POST['ten_lop'];

    // Cập nhật thông tin lớp vào cơ sở dữ liệu
    $stmt = $connection->prepare("UPDATE lop SET ma_lop = ?, ten_lop = ? WHERE ma_xac_thuc_lop = ?");
    $stmt->bind_param("sss", $ma_lop, $ten_lop, $ma_xac_thuc_lop);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Cập nhật lớp thành công!";
        header("Location: lop.php");
        exit;
    } else {
        $_SESSION['message'] = "Lỗi khi cập nhật lớp!";
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Thông Tin Lớp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Nhúng thanh điều hướng -->
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h2>Sửa Thông Tin Lớp</h2>

        <!-- Hiển thị thông báo -->
        <?php if (isset($_SESSION['message'])) { ?>
            <div class="alert alert-info"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php } ?>

        <!-- Form sửa lớp -->
        <form action="sualop.php?edit=<?= $class['ma_xac_thuc_lop'] ?>" method="POST">
            <div class="mb-3">
                <label for="ma_lop" class="form-label">Mã Lớp</label>
                <input type="text" class="form-control" id="ma_lop" name="ma_lop" value="<?= htmlspecialchars($class['ma_lop']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="ten_lop" class="form-label">Tên Lớp</label>
                <input type="text" class="form-control" id="ten_lop" name="ten_lop" value="<?= htmlspecialchars($class['ten_lop']) ?>" required>
            </div>

            <button type="submit" name="update_class" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
</body>
</html>
