<?php
session_start();  // Bắt đầu session

$connection = new mysqli('localhost', 'root', '', 'tracuuttsvtt');

// Kiểm tra kết nối
if ($connection->connect_error) {
    die("Kết nối thất bại: " . $connection->connect_error);
}

// Thêm đợt thực tập
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $ma_dot_thuc_tap = $_POST['ma_dot_thuc_tap'];
    $ten_dot = $_POST['ten_dot'];
    $mo_ta = $_POST['mo_ta'];
    $ngay_bat_dau = $_POST['ngay_bat_dau'];
    $ngay_ket_thuc = $_POST['ngay_ket_thuc'];

    // Thêm đợt thực tập vào cơ sở dữ liệu
    $stmt = $connection->prepare("INSERT INTO dot_thuc_tap (ma_dot_thuc_tap, ten_dot, mo_ta, ngay_bat_dau, ngay_ket_thuc) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $ma_dot_thuc_tap, $ten_dot, $mo_ta, $ngay_bat_dau, $ngay_ket_thuc);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Thêm đợt thực tập thành công!";
        header("Location: dotthuctap.php");
        exit;
    } else {
        $_SESSION['message'] = "Lỗi khi thêm đợt thực tập!";
    }

    $stmt->close();
}

// Xóa đợt thực tập
if (isset($_GET['delete'])) {
    $ma_dot_thuc_tap = $_GET['delete'];

    // Sử dụng câu lệnh chuẩn bị để xóa
    $stmt = $connection->prepare("DELETE FROM dot_thuc_tap WHERE ma_dot_thuc_tap = ?");
    $stmt->bind_param("s", $ma_dot_thuc_tap);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Xóa đợt thực tập thành công!";
    } else {
        $_SESSION['message'] = "Lỗi khi xóa đợt thực tập!";
    }

    $stmt->close();
    header("Location: dotthuctap.php");
    exit;
}

// Lấy danh sách đợt thực tập
$sql = "SELECT * FROM dot_thuc_tap";
$result = $connection->query($sql);

// Lấy vai trò người dùng
$vaitro = isset($_SESSION['vai_tro']) ? $_SESSION['vai_tro'] : 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đợt Thực Tập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Nhúng thanh điều hướng -->
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h2>Danh Sách Đợt Thực Tập</h2>

        <!-- Ẩn nút "Thêm Sinh viên" nếu vai trò là 0 -->
        <?php if ($vaitro == 1) { ?>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAddDotThucTap">Thêm Đợt Thực Tập</button>
        <?php } ?>

        <!-- Hiển thị thông báo -->
        <?php if (isset($_SESSION['message'])) { ?>
            <div class="alert alert-info"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php } ?>

        <!-- Bảng danh sách đợt thực tập -->
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Mã Đợt</th>
                    <th>Tên Đợt</th>
                    <th>Mô Tả</th>
                    <th>Ngày Bắt Đầu</th>
                    <th>Ngày Kết Thúc</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['ma_dot_thuc_tap']; ?></td>
                        <td><?php echo $row['ten_dot']; ?></td>
                        <td><?php echo $row['mo_ta']; ?></td>
                        <td><?php echo $row['ngay_bat_dau']; ?></td>
                        <td><?php echo $row['ngay_ket_thuc']; ?></td>
                        <td>
                            <a href="suadotthuctap.php?edit=<?php echo $row['ma_dot_thuc_tap']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                            <!-- Ẩn nút "Thêm Sinh viên" nếu vai trò là 0 -->
                            <?php if ($vaitro == 1) { ?>
                            <a href="dotthuctap.php?delete=<?php echo $row['ma_dot_thuc_tap']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Thêm Đợt Thực Tập -->
    <div class="modal fade" id="modalAddDotThucTap" tabindex="-1" aria-labelledby="modalAddDotThucTapLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddDotThucTapLabel">Thêm Đợt Thực Tập</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="dotthuctap.php">
                        <div class="mb-3">
                            <label for="ma_dot_thuc_tap" class="form-label">Mã Đợt Thực Tập</label>
                            <input type="text" class="form-control" id="ma_dot_thuc_tap" name="ma_dot_thuc_tap" required>
                        </div>
                        <div class="mb-3">
                            <label for="ten_dot" class="form-label">Tên Đợt</label>
                            <input type="text" class="form-control" id="ten_dot" name="ten_dot" required>
                        </div>
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô Tả</label>
                            <textarea class="form-control" id="mo_ta" name="mo_ta" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="ngay_bat_dau" class="form-label">Ngày Bắt Đầu</label>
                            <input type="date" class="form-control" id="ngay_bat_dau" name="ngay_bat_dau" required>
                        </div>
                        <div class="mb-3">
                            <label for="ngay_ket_thuc" class="form-label">Ngày Kết Thúc</label>
                            <input type="date" class="form-control" id="ngay_ket_thuc" name="ngay_ket_thuc" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" name="add" class="btn btn-primary">Thêm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JS liên kết Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
