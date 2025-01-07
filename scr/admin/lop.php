<?php 
session_start();  // Bắt đầu session

$connection = new mysqli('localhost', 'root', '', 'tracuuttsvtt');

// Kiểm tra kết nối
if ($connection->connect_error) {
    die("Kết nối thất bại: " . $connection->connect_error);
}

// Thêm lớp
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_class'])) {
    $ma_lop = $_POST['ma_lop'];
    $ten_lop = $_POST['ten_lop'];

    // Tạo mã xác thực lớp tự động, ví dụ: LOP001, LOP002,...
$query = "SELECT MAX(SUBSTRING(ma_xac_thuc_lop, 4)) AS max_ma FROM lop";
$result = $connection->query($query);
$row = $result->fetch_assoc();
$max_ma = $row['max_ma'] ? (int)$row['max_ma'] : 0;  // Kiểm tra và đảm bảo rằng $max_ma là số

$new_ma = 'LOP' . str_pad($max_ma + 1, 3, '0', STR_PAD_LEFT);  // Tạo mã lớp mới

    // Thực thi câu lệnh thêm lớp
    $stmt = $connection->prepare("INSERT INTO lop (ma_lop, ten_lop, ma_xac_thuc_lop) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $ma_lop, $ten_lop, $new_ma);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Thêm lớp thành công!";
    } else {
        $_SESSION['message'] = "Lỗi khi thêm lớp!";
    }
    $stmt->close();
}

// Xóa lớp
if (isset($_GET['delete'])) {
    $ma_xac_thuc_lop = $_GET['delete'];
    $stmt = $connection->prepare("DELETE FROM lop WHERE ma_xac_thuc_lop = ?");
    $stmt->bind_param("s", $ma_xac_thuc_lop);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Xóa lớp thành công!";
    } else {
        $_SESSION['message'] = "Lỗi khi xóa lớp!";
    }
    $stmt->close();
    header("Location: lop.php");
    exit;
}

// Hiển thị danh sách lớp
$classes = [];
$result = $connection->query("SELECT * FROM lop");
if ($result) {
    $classes = $result->fetch_all(MYSQLI_ASSOC);
}

// Lấy vai trò người dùng
$vaitro = isset($_SESSION['vai_tro']) ? $_SESSION['vai_tro'] : 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Lớp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Nhúng thanh điều hướng -->
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h2>Danh sách Lớp</h2>

        <?php if (isset($_SESSION['message'])) { ?>
            <div class="alert alert-info"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php } ?>

        <!-- Ẩn nút "Thêm Sinh viên" nếu vai trò là 0 -->
        <?php if ($vaitro == 1) { ?>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addClassModal">Thêm Lớp</button>
        <?php } ?>

        <!-- Modal thêm lớp -->
        <div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addClassModalLabel">Thêm Lớp</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="lop.php" method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="ma_lop" class="form-label">Mã Lớp</label>
                                <input type="text" class="form-control" id="ma_lop" name="ma_lop" required>
                            </div>
                            <div class="mb-3">
                                <label for="ten_lop" class="form-label">Tên Lớp</label>
                                <input type="text" class="form-control" id="ten_lop" name="ten_lop" required>
                            </div>
                            <div class="mb-3">
                                <label for="ma_xac_thuc_lop" class="form-label">Mã Xác Thực Lớp</label>
                                <input type="text" class="form-control" id="ma_xac_thuc_lop" name="ma_xac_thuc_lop" value="Mã tự tạo" readonly>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" name="add_class" class="btn btn-primary">Thêm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Danh sách lớp -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã Lớp</th>
                    <th>Tên Lớp</th>
                    <th>Mã Xác Thực Lớp</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($classes) > 0) { ?>
                    <?php foreach ($classes as $class) { ?>
                        <tr>
                            <td><?= htmlspecialchars($class['ma_lop']) ?></td>
                            <td><?= htmlspecialchars($class['ten_lop']) ?></td>
                            <td><?= htmlspecialchars($class['ma_xac_thuc_lop']) ?></td>
                            <td>
                                <a href="sualop.php?edit=<?= $class['ma_xac_thuc_lop'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <!-- Ẩn nút "Thêm Sinh viên" nếu vai trò là 0 -->
                                <?php if ($vaitro == 1) { ?>
                                <a href="lop.php?delete=<?= $class['ma_xac_thuc_lop'] ?>" class="btn btn-danger btn-sm">Xóa</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4" class="text-center">Không có dữ liệu.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
