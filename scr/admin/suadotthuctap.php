<?php
session_start();  // Bắt đầu session

$connection = new mysqli('localhost', 'root', '', 'tracuuttsvtt');

// Kiểm tra kết nối
if ($connection->connect_error) {
    die("Kết nối thất bại: " . $connection->connect_error);
}

// Lấy thông tin đợt thực tập để sửa
if (isset($_GET['edit'])) {
    $ma_dot_thuc_tap = $_GET['edit'];

    // Lấy thông tin đợt thực tập từ cơ sở dữ liệu
    $sql = "SELECT * FROM dot_thuc_tap WHERE ma_dot_thuc_tap = '$ma_dot_thuc_tap'";
    $result = $connection->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy đợt thực tập.";
        exit;
    }
}

// Cập nhật đợt thực tập
if (isset($_POST['edit'])) {
    $ma_dot_thuc_tap = $_POST['ma_dot_thuc_tap'];
    $ten_dot = $_POST['ten_dot'];
    $mo_ta = $_POST['mo_ta'];
    $ngay_bat_dau = $_POST['ngay_bat_dau'];
    $ngay_ket_thuc = $_POST['ngay_ket_thuc'];

    $sql = "UPDATE dot_thuc_tap SET 
                ten_dot = '$ten_dot', 
                mo_ta = '$mo_ta', 
                ngay_bat_dau = '$ngay_bat_dau', 
                ngay_ket_thuc = '$ngay_ket_thuc'
            WHERE ma_dot_thuc_tap = '$ma_dot_thuc_tap'";

    if ($connection->query($sql) === TRUE) {
        echo "<script>alert('Thông tin đợt thực tập đã được cập nhật thành công.'); window.location.href = 'dotthuctap.php';</script>";
    } else {
        echo "Lỗi: " . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Đợt Thực Tập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Nhúng thanh điều hướng -->
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h2>Sửa Đợt Thực Tập</h2>
        <form method="POST" action="suadotthuctap.php">
            <div class="mb-3">
                <label for="ma_dot_thuc_tap" class="form-label">Mã Đợt Thực Tập</label>
                <input type="text" class="form-control" id="ma_dot_thuc_tap" name="ma_dot_thuc_tap" value="<?php echo $row['ma_dot_thuc_tap']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="ten_dot" class="form-label">Tên Đợt</label>
                <input type="text" class="form-control" id="ten_dot" name="ten_dot" value="<?php echo $row['ten_dot']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="mo_ta" class="form-label">Mô Tả</label>
                <textarea class="form-control" id="mo_ta" name="mo_ta" rows="4" required><?php echo $row['mo_ta']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="ngay_bat_dau" class="form-label">Ngày Bắt Đầu</label>
                <input type="date" class="form-control" id="ngay_bat_dau" name="ngay_bat_dau" value="<?php echo $row['ngay_bat_dau']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="ngay_ket_thuc" class="form-label">Ngày Kết Thúc</label>
                <input type="date" class="form-control" id="ngay_ket_thuc" name="ngay_ket_thuc" value="<?php echo $row['ngay_ket_thuc']; ?>" required>
            </div>
            <button type="submit" name="edit" class="btn btn-primary">Cập Nhật</button>
        </form>
    </div>

    <!-- JS liên kết Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
