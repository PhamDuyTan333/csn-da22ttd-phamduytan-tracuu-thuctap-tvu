<?php
session_start();  // Bắt đầu session

$connection = new mysqli('localhost', 'root', '', 'tracuuttsvtt');

// Kiểm tra kết nối
if ($connection->connect_error) {
    die("Kết nối thất bại: " . $connection->connect_error);
}

// Lấy danh sách lớp
$query_classes = "SELECT ma_lop, ten_lop FROM lop";
$result_classes = $connection->query($query_classes);
$classes = [];
if ($result_classes && $result_classes->num_rows > 0) {
    while ($row = $result_classes->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Lấy mã sinh viên từ URL
if (isset($_GET['edit'])) {
    $ma_sinh_vien = $_GET['edit'];

    // Lấy thông tin sinh viên
    $stmt = $connection->prepare("SELECT * FROM sinh_vien WHERE ma_sinh_vien = ?");
    $stmt->bind_param("s", $ma_sinh_vien);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Không tìm thấy sinh viên với mã này.");
    }
    $student = $result->fetch_assoc();
    $stmt->close();
}

// Cập nhật thông tin sinh viên
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'sua') {
    $ma_sinh_vien = $_POST['ma_sinh_vien'];
    $ho_ten = $_POST['ho_ten'];
    $lop = $_POST['lop'];  // Lưu ma_lop từ dropdown
    $khoa = $_POST['khoa'];
    $email = $_POST['email'];
    $so_dien_thoai = $_POST['so_dien_thoai'];

    // Cập nhật thông tin sinh viên
    $stmt = $connection->prepare("UPDATE sinh_vien SET ho_ten = ?, lop = ?, khoa = ?, email = ?, so_dien_thoai = ? WHERE ma_sinh_vien = ?");
    $stmt->bind_param("ssssss", $ho_ten, $lop, $khoa, $email, $so_dien_thoai, $ma_sinh_vien);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Thông tin sinh viên đã được cập nhật thành công.";
        header("Location: sinhvien.php"); // Chuyển hướng về trang danh sách sinh viên
        exit;
    } else {
        echo "Lỗi khi cập nhật thông tin: " . $stmt->error;
    }
    $stmt->close();
}

// Đóng kết nối
$connection->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Thông Tin Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h2>Sửa Thông Tin Sinh Viên</h2>

        <!-- Hiển thị thông báo thành công nếu có -->
        <?php if (isset($_SESSION['message'])) { ?>
            <div class="alert alert-success">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php } ?>

        <!-- Form sửa thông tin sinh viên -->
        <form method="POST">
            <input type="hidden" name="action" value="sua">
            <input type="hidden" name="ma_sinh_vien" value="<?= htmlspecialchars($student['ma_sinh_vien']) ?>">

            <div class="mb-3">
                <label for="ho_ten" class="form-label">Họ và Tên</label>
                <input type="text" class="form-control" id="ho_ten" name="ho_ten" value="<?= htmlspecialchars($student['ho_ten']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="lop" class="form-label">Lớp</label>
                <select class="form-select" id="lop" name="lop" required>
                    <option value="">Chọn lớp</option>
                    <?php foreach ($classes as $class) { ?>
                        <option value="<?= htmlspecialchars($class['ma_lop']) ?>" <?= ($class['ma_lop'] == $student['lop']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($class['ma_lop'] . ' - ' . $class['ten_lop']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="khoa" class="form-label">Khoa</label>
                <input type="text" class="form-control" id="khoa" name="khoa" value="<?= htmlspecialchars($student['khoa']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="so_dien_thoai" class="form-label">Số Điện Thoại</label>
                <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai" value="<?= htmlspecialchars($student['so_dien_thoai']) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>