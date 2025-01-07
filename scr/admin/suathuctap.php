<?php  
// Kết nối tới cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tracuuttsvtt";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy mã thực tập từ URL
if (isset($_GET['edit'])) {
    $ma_thuc_tap = $_GET['edit'];
    
    // Truy vấn thông tin thực tập
    $sql = "SELECT * FROM thuc_tap WHERE ma_thuc_tap = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $ma_thuc_tap);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $thuc_tap = $result->fetch_assoc();
    } else {
        die("Không tìm thấy thông tin thực tập.");
    }
    $stmt->close();
}

// Cập nhật thông tin thực tập
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $ma_thuc_tap = $_POST['ma_thuc_tap'];
    $ma_don_vi = $_POST['ma_don_vi'];
    $ten_de_tai_thuc_tap = $_POST['ten_de_tai_thuc_tap'];
    $ma_nguoi_huong_dan_truong = $_POST['ma_nguoi_huong_dan_truong'];
    $ma_nguoi_huong_dan_don_vi = $_POST['ma_nguoi_huong_dan_don_vi'];
    $nhiem_vu = $_POST['nhiem_vu'];
    $diem_danh_gia = $_POST['diem_danh_gia'];

    $sql_update = "UPDATE thuc_tap SET 
                    ma_don_vi = ?, 
                    ten_de_tai_thuc_tap = ?, 
                    ma_nguoi_huong_dan_truong = ?, 
                    ma_nguoi_huong_dan_don_vi = ?, 
                    nhiem_vu = ?, 
                    diem_danh_gia = ? 
                    WHERE ma_thuc_tap = ?";
                    
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('sssssss', $ma_don_vi, $ten_de_tai_thuc_tap, 
                             $ma_nguoi_huong_dan_truong, $ma_nguoi_huong_dan_don_vi, 
                             $nhiem_vu, $diem_danh_gia, $ma_thuc_tap);

    if ($stmt_update->execute()) {
        header("Location: thuctap.php?success=1");
        exit;
    } else {
        echo "Lỗi: " . $stmt_update->error;
    }
    $stmt_update->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Thông Tin Thực Tập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Nhúng thanh điều hướng -->
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center">Sửa Thông Tin Thực Tập</h1>
        <form method="POST" action="suathuctap.php">
            <input type="hidden" name="ma_thuc_tap" value="<?= htmlspecialchars($thuc_tap['ma_thuc_tap']); ?>">
            
            <div class="mb-3">
                <label for="ma_don_vi" class="form-label">Đơn Vị Thực Tập</label>
                <select name="ma_don_vi" class="form-control" required>
                    <option value="">Chọn đơn vị thực tập</option>
                    <?php
                    $result_don_vi = $conn->query("SELECT * FROM don_vi");
                    while ($row = $result_don_vi->fetch_assoc()) {
                        $selected = ($row['ma_don_vi'] == $thuc_tap['ma_don_vi']) ? 'selected' : '';
                        echo "<option value=\"" . htmlspecialchars($row['ma_don_vi']) . "\" $selected>" . htmlspecialchars($row['ten_don_vi']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="ten_de_tai_thuc_tap" class="form-label">Tên Đề Tài Thực Tập</label>
                <input type="text" name="ten_de_tai_thuc_tap" class="form-control" value="<?= htmlspecialchars($thuc_tap['ten_de_tai_thuc_tap']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="ma_nguoi_huong_dan_truong" class="form-label">Người Hướng Dẫn Trường</label>
                <select name="ma_nguoi_huong_dan_truong" class="form-control">
                    <option value="">Chọn người hướng dẫn trường</option>
                    <?php
                    $result_nguoi_huong_dan_truong = $conn->query("SELECT * FROM nguoi_huong_dan WHERE ma_loai_nguoi_huong_dan = 2");
                    while ($row = $result_nguoi_huong_dan_truong->fetch_assoc()) {
                        $selected = ($row['ma_nguoi_huong_dan'] == $thuc_tap['ma_nguoi_huong_dan_truong']) ? 'selected' : '';
                        echo "<option value=\"" . htmlspecialchars($row['ma_nguoi_huong_dan']) . "\" $selected>" . htmlspecialchars($row['ten']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="ma_nguoi_huong_dan_don_vi" class="form-label">Người Hướng Dẫn Đơn Vị</label>
                <select name="ma_nguoi_huong_dan_don_vi" class="form-control">
                    <option value="">Chọn người hướng dẫn đơn vị</option>
                    <?php
                    $result_nguoi_huong_dan_don_vi = $conn->query("SELECT * FROM nguoi_huong_dan WHERE ma_loai_nguoi_huong_dan = 1");
                    while ($row = $result_nguoi_huong_dan_don_vi->fetch_assoc()) {
                        $selected = ($row['ma_nguoi_huong_dan'] == $thuc_tap['ma_nguoi_huong_dan_don_vi']) ? 'selected' : '';
                        echo "<option value=\"" . htmlspecialchars($row['ma_nguoi_huong_dan']) . "\" $selected>" . htmlspecialchars($row['ten']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="nhiem_vu" class="form-label">Nhiệm Vụ</label>
                <input type="text" name="nhiem_vu" class="form-control" value="<?= htmlspecialchars($thuc_tap['nhiem_vu']); ?>">
            </div>

            <div class="mb-3">
                <label for="diem_danh_gia" class="form-label">Điểm Đánh Giá</label>
                <input type="number" name="diem_danh_gia" class="form-control" step="0.1" min="0" max="10" value="<?= htmlspecialchars($thuc_tap['diem_danh_gia']); ?>">
            </div>

            <button type="submit" name="update" class="btn btn-primary">Cập Nhật</button>
            <a href="thuctap.php" class="btn btn-secondary">Hủy</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>