<?php
$connection = new mysqli('localhost', 'root', '', 'tracuuttsvtt');

// Kiểm tra kết nối
if ($connection->connect_error) {
    die("Kết nối thất bại: " . $connection->connect_error);
}

// Giả định vai trò của người dùng được lưu trong session
session_start();
$vai_tro = $_SESSION['vai_tro'] ?? 0; // Mặc định là 0 nếu không có

// Xử lý tìm kiếm
$search_term = "";
$nguoihuongdans = []; // Khởi tạo mảng kết quả mặc định

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $connection->real_escape_string($_GET['search']);

    // Dùng prepared statements để bảo vệ khỏi SQL Injection
    $stmt = $connection->prepare("SELECT * FROM nguoi_huong_dan WHERE ten LIKE ? OR ma_nguoi_huong_dan LIKE ? OR email LIKE ?");
    $search_term_like = "%" . $search_term . "%";
    $stmt->bind_param("sss", $search_term_like, $search_term_like, $search_term_like);
    $stmt->execute();
    $result = $stmt->get_result();
    $nguoihuongdans = $result->fetch_all(MYSQLI_ASSOC); // Lấy tất cả kết quả
    $stmt->close();
} else {
    // Nếu không có tìm kiếm hoặc ô tìm kiếm trống, lấy tất cả người hướng dẫn
    $result = $connection->query("SELECT * FROM nguoi_huong_dan");
    $nguoihuongdans = $result->fetch_all(MYSQLI_ASSOC);
}

// Thêm mới người hướng dẫn khi gửi form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'them') {
    // Lấy dữ liệu từ form
    $ma_nguoi_huong_dan = $connection->real_escape_string($_POST['ma_nguoi_huong_dan']);
    $ma_loai_nguoi_huong_dan = $connection->real_escape_string($_POST['ma_loai_nguoi_huong_dan']);
    $ten = $connection->real_escape_string($_POST['ten']);
    $email = $connection->real_escape_string($_POST['email']);
    $so_dien_thoai = $connection->real_escape_string($_POST['so_dien_thoai']);
    $thong_tin_tai_khoan = $connection->real_escape_string($_POST['thong_tin_tai_khoan']);
    $cccd = $connection->real_escape_string($_POST['cccd']);
    $chuc_vu = $connection->real_escape_string($_POST['chuc_vu']);

    // Kiểm tra xem mã người hướng dẫn đã tồn tại hay chưa
    $check_stmt = $connection->prepare("SELECT COUNT(*) FROM nguoi_huong_dan WHERE ma_nguoi_huong_dan = ?");
    $check_stmt->bind_param("s", $ma_nguoi_huong_dan);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result()->fetch_row();
    $check_stmt->close();

    if ($check_result[0] > 0) {
        $error_message = "Mã người hướng dẫn này đã tồn tại!";
    } else {
        // Nếu mã chưa tồn tại, thực hiện thêm mới
        $stmt = $connection->prepare("INSERT INTO nguoi_huong_dan (ma_nguoi_huong_dan, ma_loai_nguoi_huong_dan, ten, email, so_dien_thoai, thong_tin_tai_khoan, cccd, chuc_vu) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $ma_nguoi_huong_dan, $ma_loai_nguoi_huong_dan, $ten, $email, $so_dien_thoai, $thong_tin_tai_khoan, $cccd, $chuc_vu);
        if ($stmt->execute()) {
            $success_message = "Thêm người hướng dẫn thành công!";
        } else {
            $error_message = "Lỗi khi thêm người hướng dẫn!";
        }
        $stmt->close();
    }
}

// Xóa người hướng dẫn
if (isset($_GET['xoa'])) {
    $ma_nguoi_huong_dan = $connection->real_escape_string($_GET['xoa']);
    $delete_stmt = $connection->prepare("DELETE FROM nguoi_huong_dan WHERE ma_nguoi_huong_dan = ?");
    $delete_stmt->bind_param("s", $ma_nguoi_huong_dan);
    $delete_stmt->execute();
    $delete_stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']); // Refresh trang sau khi xóa
    exit;
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người Hướng Dẫn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Nhúng thanh điều hướng -->
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h2>Danh sách Người Hướng Dẫn</h2>

        <!-- Form tìm kiếm -->
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-8">
                    <input type="text" class="form-control" name="search" placeholder="Tìm kiếm người hướng dẫn..." value="<?= htmlspecialchars($search_term) ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                </div>
            </div>
        </form>

        <!-- Hiển thị thông báo lỗi nếu mã người hướng dẫn đã tồn tại -->
        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php } ?>

        <!-- Hiển thị thông báo thành công -->
        <?php if (isset($success_message)) { ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php } ?>

        <!-- Nút để mở modal thêm người hướng dẫn (chỉ hiển thị nếu vai trò khác 0) -->
        <?php if ($vai_tro != 0): ?>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#themNguoiHuongDanModal">Thêm Người Hướng Dẫn</button>
        <?php endif; ?>

        <!-- Modal Thêm Người Hướng Dẫn -->
        <div class="modal fade" id="themNguoiHuongDanModal" tabindex="-1" aria-labelledby="themNguoiHuongDanModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="themNguoiHuongDanModalLabel">Thêm Mới Người Hướng Dẫn</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="them">
                            <div class="mb-3">
                                <label for="ma_nguoi_huong_dan" class="form-label">Mã Người Hướng Dẫn</label>
                                <input type="text" class="form-control" id="ma_nguoi_huong_dan" name="ma_nguoi_huong_dan" required>
                            </div>
                            <div class="mb-3">
                                <label for="ma_loai_nguoi_huong_dan" class="form-label">Loại Người Hướng Dẫn</label>
                                <select class="form-control" id="ma_loai_nguoi_huong_dan" name="ma_loai_nguoi_huong_dan" required>
                                    <option value="1">Người hướng dẫn tại đơn vị thực tập</option>
                                    <option value="2">Người hướng dẫn tại trường</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ten" class="form-label">Tên</label>
                                <input type="text" class="form-control" id="ten" name="ten" required>
                            </div>
                            <div class="mb-3">
                                <label for="chuc_vu" class="form-label">Chức Vụ</label>
                                <input type="text" class="form-control" id="chuc_vu" name="chuc_vu" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="so_dien_thoai" class="form-label">Số Điện Thoại</label>
                                <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai" required>
                            </div>
                            <div class="mb-3">
                                <label for="thong_tin_tai_khoan" class="form-label">Thông Tin Tài Khoản Ngân Hàng</label>
                                <textarea class="form-control" id="thong_tin_tai_khoan" name="thong_tin_tai_khoan" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="cccd" class="form-label">CCCD</label>
                                <input type="text" class="form-control" id="cccd" name="cccd" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Thêm</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng danh sách người hướng dẫn -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã Người Hướng Dẫn</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Số Điện Thoại</th>
                    <th>Thông Tin Tài Khoản Ngân Hàng</th>
                    <th>CCCD</th>
                    <th>Chức Vụ</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($nguoihuongdans) > 0): ?>
                    <?php foreach ($nguoihuongdans as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ma_nguoi_huong_dan']) ?></td>
                            <td><?= htmlspecialchars($row['ten']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['so_dien_thoai']) ?></td>
                            <td><?= htmlspecialchars($row['thong_tin_tai_khoan']) ?></td>
                            <td><?= htmlspecialchars($row['cccd']) ?></td>
                            <td><?= htmlspecialchars($row['chuc_vu']) ?></td>
                            <td>
                                <a href="suanguoihuongdan.php?ma_nguoi_huong_dan=<?= htmlspecialchars($row['ma_nguoi_huong_dan']) ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <!-- Nút để mở modal thêm người hướng dẫn (chỉ hiển thị nếu vai trò khác 0) -->
                                <?php if ($vai_tro != 0): ?>
                                <a href="?xoa=<?= htmlspecialchars($row['ma_nguoi_huong_dan']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa không?')">Xóa</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Không tìm thấy người hướng dẫn.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>