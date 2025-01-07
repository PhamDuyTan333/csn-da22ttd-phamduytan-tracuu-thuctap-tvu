<?php
session_start();  // Bắt đầu phiên làm việc

$connection = new mysqli('localhost', 'root', '', 'tracuuttsvtt');

// Kiểm tra kết nối
if ($connection->connect_error) {
    die("Kết nối thất bại: " . $connection->connect_error);
}

// Thêm đơn vị
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_unit'])) {
    $ma_don_vi = $_POST['ma_don_vi'];
    $ten_don_vi = $_POST['ten_don_vi'];
    $dia_chi = $_POST['dia_chi'];
    $so_dien_thoai = $_POST['so_dien_thoai'];
    $email = $_POST['email'];
    $loai_don_vi = $_POST['loai_don_vi'];

    // Kiểm tra dữ liệu (kiểm tra cơ bản)
    if (empty($ma_don_vi) || empty($ten_don_vi) || empty($dia_chi) || empty($so_dien_thoai) || empty($email) || empty($loai_don_vi)) {
        $_SESSION['message'] = "Vui lòng điền đầy đủ thông tin!";
    } else {
        // Chuẩn bị câu lệnh
        $stmt = $connection->prepare("INSERT INTO don_vi (ma_don_vi, ten_don_vi, dia_chi, so_dien_thoai, email, loai_don_vi) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $ma_don_vi, $ten_don_vi, $dia_chi, $so_dien_thoai, $email, $loai_don_vi);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Thêm đơn vị thành công!";
        } else {
            $_SESSION['message'] = "Lỗi khi thêm đơn vị!";
        }
        $stmt->close();
    }
}

// Xóa đơn vị
if (isset($_GET['delete'])) {
    $ma_don_vi = $_GET['delete'];
    $stmt = $connection->prepare("DELETE FROM don_vi WHERE ma_don_vi = ?");
    $stmt->bind_param("s", $ma_don_vi);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Xóa đơn vị thành công!";
    } else {
        $_SESSION['message'] = "Lỗi khi xóa đơn vị!";
    }
    $stmt->close();
    header("Location: donvi.php");
    exit;
}

// Xử lý tìm kiếm
$search_term = "";
$loai_don_vi = "";
$units = []; // Kết quả mặc định là rỗng

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $loai_don_vi = isset($_GET['loai_don_vi']) ? $_GET['loai_don_vi'] : '';

    // Tìm kiếm theo mã đơn vị hoặc tên đơn vị và loại
    $query = "SELECT * FROM don_vi WHERE (ma_don_vi LIKE ? OR ten_don_vi LIKE ?)";
    
    if (!empty($loai_don_vi)) {
        $query .= " AND loai_don_vi = ?";
    }

    $stmt = $connection->prepare($query);
    $search_param = "%" . $search_term . "%";
    
    if (!empty($loai_don_vi)) {
        $stmt->bind_param("sss", $search_param, $search_param, $loai_don_vi);
    } else {
        $stmt->bind_param("ss", $search_param, $search_param);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $units = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Lấy tất cả đơn vị nếu không có từ khóa tìm kiếm
    $result = $connection->query("SELECT * FROM don_vi");
    $units = $result->fetch_all(MYSQLI_ASSOC);
}

// Lấy vai trò người dùng
$vaitro = isset($_SESSION['vai_tro']) ? $_SESSION['vai_tro'] : 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn vị thực tập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Nhúng thanh điều hướng -->
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h2>Danh sách Đơn vị thực tập</h2>
        
        <!-- Biểu mẫu tìm kiếm -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..." value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" aria-label="Search">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="loai_don_vi">
                        <option value="">Chọn Loại Đơn vị</option>
                        <option value="Trong trường" <?= isset($_GET['loai_don_vi']) && $_GET['loai_don_vi'] == 'Trong trường' ? 'selected' : '' ?>>Trong trường</option>
                        <option value="Ngoài trường" <?= isset($_GET['loai_don_vi']) && $_GET['loai_don_vi'] == 'Ngoài trường' ? 'selected' : '' ?>>Ngoài trường</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                </div>
            </div>
        </form>

        <?php if (isset($_SESSION['message'])) { ?>
            <div class="alert alert-info"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php } ?>

        <!-- Ẩn nút "Thêm Sinh viên" nếu vai trò là 0 -->
        <?php if ($vaitro == 1) { ?>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUnitModal">Thêm Đơn vị</button>
        <?php } ?>

        <!-- Modal Thêm Đơn vị -->
        <div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUnitModalLabel">Thêm Đơn vị</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="donvi.php" method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="ma_don_vi" class="form-label">Mã Đơn vị</label>
                                <input type="text" class="form-control" id="ma_don_vi" name="ma_don_vi" required>
                            </div>
                            <div class="mb-3">
                                <label for="ten_don_vi" class="form-label">Tên Đơn vị</label>
                                <input type="text" class="form-control" id="ten_don_vi" name="ten_don_vi" required>
                            </div>
                            <div class="mb-3">
                                <label for="dia_chi" class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" id="dia_chi" name="dia_chi" required>
                            </div>
                            <div class="mb-3">
                                <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="loai_don_vi" class="form-label">Loại Đơn vị</label>
                                <select class="form-select" id="loai_don_vi" name="loai_don_vi" required>
                                    <option value="Trong trường">Trong trường</option>
                                    <option value="Ngoài trường">Ngoài trường</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" name="add_unit" class="btn btn-primary">Thêm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bảng Danh sách Đơn vị -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Mã Đơn vị</th>
                    <th>Tên Đơn vị</th>
                    <th>Địa chỉ</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Loại Đơn vị</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($units as $unit): ?>
                    <tr>
                        <td><?= $unit['ma_don_vi'] ?></td>
                        <td><?= $unit['ten_don_vi'] ?></td>
                        <td><?= $unit['dia_chi'] ?></td>
                        <td><?= $unit['so_dien_thoai'] ?></td>
                        <td><?= $unit['email'] ?></td>
                        <td><?= $unit['loai_don_vi'] ?></td>
                        <td>
                            <a href="suadonvi.php?ma_don_vi=<?= $unit['ma_don_vi'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                            <?php if ($vaitro == 1) { ?>
                            <a href="?delete=<?= $unit['ma_don_vi'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa không?')">Xóa</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
