<?php  
// Hằng số cho kết nối cơ sở dữ liệu
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'tracuuttsvtt');

// Tạo kết nối
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Khởi tạo biến thông báo
$message = "";

// Khởi tạo biến tìm kiếm
$search_query = "";

// Giả định vai trò của người dùng được lưu trong session
session_start();
$vai_tro = $_SESSION['vai_tro'] ?? 0; // Mặc định là 0 nếu không có

// Thêm thông tin thực tập
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $ma_sinh_vien = $_POST['ma_sinh_vien'];
    $ma_dot_thuc_tap = $_POST['ma_dot_thuc_tap'];
    $ma_don_vi = $_POST['ma_don_vi'];
    $ma_nguoi_huong_dan_truong = $_POST['ma_nguoi_huong_dan_truong'];
    $ma_nguoi_huong_dan_don_vi = $_POST['ma_nguoi_huong_dan_don_vi'];
    $nhiem_vu = $_POST['nhiem_vu'];
    $diem_danh_gia = $_POST['diem_danh_gia'];
    $ten_de_tai_thuc_tap = $_POST['ten_de_tai_thuc_tap'];

    $sql = "INSERT INTO thuc_tap (ma_sinh_vien, ma_dot_thuc_tap, ma_don_vi, ma_nguoi_huong_dan_truong, ma_nguoi_huong_dan_don_vi, nhiem_vu, diem_danh_gia, ten_de_tai_thuc_tap)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssss', $ma_sinh_vien, $ma_dot_thuc_tap, $ma_don_vi, $ma_nguoi_huong_dan_truong, $ma_nguoi_huong_dan_don_vi, $nhiem_vu, $diem_danh_gia, $ten_de_tai_thuc_tap);

    if ($stmt->execute()) {
        $message = "Thêm thông tin thực tập thành công!";
    } else {
        $message = "Lỗi: " . $stmt->error;
    }
    $stmt->close();
}

// Xóa thông tin thực tập
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    $ma_thuc_tap = $_GET['ma_thuc_tap'];
    $sql_delete = "DELETE FROM thuc_tap WHERE ma_thuc_tap = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param('s', $ma_thuc_tap);

    if ($stmt->execute()) {
        $message = "Xóa thông tin thực tập thành công!";
    } else {
        $message = "Lỗi khi xóa: " . $stmt->error;
    }
    $stmt->close();
    header('Location: thuctap.php?message=' . urlencode($message));
    exit;
}

// Chức năng tìm kiếm
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Lấy thông tin thực tập với chức năng tìm kiếm
$sql_thuc_tap = "SELECT tt.ma_thuc_tap, sv.ho_ten, dt.ten_dot, dv.ten_don_vi, 
                        t1.ten AS ten_nguoi_huong_dan_truong, t2.ten AS ten_nguoi_huong_dan_don_vi, 
                        tt.nhiem_vu, tt.diem_danh_gia, tt.ten_de_tai_thuc_tap
                FROM thuc_tap tt
                JOIN sinh_vien sv ON tt.ma_sinh_vien = sv.ma_sinh_vien
                JOIN dot_thuc_tap dt ON tt.ma_dot_thuc_tap = dt.ma_dot_thuc_tap
                JOIN don_vi dv ON tt.ma_don_vi = dv.ma_don_vi
                LEFT JOIN nguoi_huong_dan t1 ON tt.ma_nguoi_huong_dan_truong = t1.ma_nguoi_huong_dan
                LEFT JOIN nguoi_huong_dan t2 ON tt.ma_nguoi_huong_dan_don_vi = t2.ma_nguoi_huong_dan
                WHERE sv.ho_ten LIKE ? OR tt.ten_de_tai_thuc_tap LIKE ?";
$stmt = $conn->prepare($sql_thuc_tap);
$search_param = "%" . $search_query . "%";
$stmt->bind_param('ss', $search_param, $search_param);
$stmt->execute();
$result_thuc_tap = $stmt->get_result();

// Lấy danh sách cho dropdown
$result_dot_thuc_tap = $conn->query("SELECT * FROM dot_thuc_tap");
$result_sinh_vien = $conn->query("SELECT * FROM sinh_vien");

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thực tập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center">Quản lý thông tin thực tập</h1>
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Form tìm kiếm -->
        <form method="GET" action="thuctap.php" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên sinh viên hoặc đề tài" value="<?= htmlspecialchars($search_query); ?>">
                <button class="btn btn-primary" type="submit">Tìm kiếm</button>
            </div>
        </form>

        <!-- Nút để mở modal thêm người hướng dẫn (chỉ hiển thị nếu vai trò khác 0) -->
        <?php if ($vai_tro != 0): ?>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Thêm Thực Tập</button>
        <?php endif; ?>

        <!-- Modal Thêm Thực Tập -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="thuctap.php">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Thêm Thông Tin Thực Tập</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="ma_sinh_vien" class="form-label">Sinh Viên</label>
                                <select name="ma_sinh_vien" class="form-control" required>
                                    <option value="">Chọn sinh viên</option>
                                    <?php while ($row = $result_sinh_vien->fetch_assoc()) { ?>
                                        <option value="<?= htmlspecialchars($row['ma_sinh_vien']); ?>"><?= htmlspecialchars($row['ho_ten']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ma_dot_thuc_tap" class="form-label">Đợt Thực Tập</label>
                                <select name="ma_dot_thuc_tap" class="form-control" required>
                                    <option value="">Chọn đợt thực tập</option>
                                    <?php while ($row = $result_dot_thuc_tap->fetch_assoc()) { ?>
                                        <option value="<?= htmlspecialchars($row['ma_dot_thuc_tap']); ?>"><?= htmlspecialchars($row['ten_dot']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ten_de_tai_thuc_tap" class="form-label">Tên Đề Tài Thực Tập</label>
                                <input type="text" name="ten_de_tai_thuc_tap" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="ma_don_vi" class="form-label">Đơn Vị Thực Tập</label>
                                <select name="ma_don_vi" class="form-control">
                                    <option value="">Chọn đơn vị thực tập</option>
                                    <?php
                                    $result_don_vi = $conn->query("SELECT * FROM don_vi");
                                    while ($row = $result_don_vi->fetch_assoc()) {
                                        echo "<option value=\"" . htmlspecialchars($row['ma_don_vi']) . "\">" . htmlspecialchars($row['ten_don_vi']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ma_nguoi_huong_dan_truong" class="form-label">Người Hướng Dẫn Trường</label>
                                <select name="ma_nguoi_huong_dan_truong" class="form-control">
                                    <option value="">Chọn người hướng dẫn trường</option>
                                    <?php
                                    $result_nguoi_huong_dan_truong = $conn->query("SELECT * FROM nguoi_huong_dan WHERE ma_loai_nguoi_huong_dan = 2");
                                    while ($row = $result_nguoi_huong_dan_truong->fetch_assoc()) {
                                        echo "<option value=\"" . htmlspecialchars($row['ma_nguoi_huong_dan']) . "\">" . htmlspecialchars($row['ten']) . "</option>";
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
                                        echo "<option value=\"" . htmlspecialchars($row['ma_nguoi_huong_dan']) . "\">" . htmlspecialchars($row['ten']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nhiem_vu" class="form-label">Nhiệm Vụ</label>
                                <input type="text" name="nhiem_vu" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="diem_danh_gia" class="form-label">Điểm Đánh Giá</label>
                                <input type="number" name="diem_danh_gia" class="form-control" step="0.1" min="0" max="10">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" name="add" class="btn btn-primary">Thêm</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Họ tên sinh viên</th>
                    <th>Đợt thực tập</th>
                    <th>Đơn vị thực tập</th>
                    <th>Tên đề tài thực tập</th>
                    <th>Người hướng dẫn trường</th>
                    <th>Người hướng dẫn đơn vị</th>
                    <th>Nhiệm vụ</th>
                    <th>Điểm đánh giá</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_thuc_tap->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['ho_ten']); ?></td>
                        <td><?= htmlspecialchars($row['ten_dot']); ?></td>
                        <td><?= htmlspecialchars($row['ten_don_vi']); ?></td>
                        <td><?= htmlspecialchars($row['ten_de_tai_thuc_tap']); ?></td>
                        <td><?= htmlspecialchars($row['ten_nguoi_huong_dan_truong']); ?></td>
                        <td><?= htmlspecialchars($row['ten_nguoi_huong_dan_don_vi']); ?></td>
                        <td><?= htmlspecialchars($row['nhiem_vu']); ?></td>
                        <td><?= htmlspecialchars($row['diem_danh_gia']); ?></td>
                        <td>
                            <!-- Nút để mở modal thêm người hướng dẫn (chỉ hiển thị nếu vai trò khác 0) -->
                            <?php if ($vai_tro != 0): ?>
                            <a href="thuctap.php?delete=1&ma_thuc_tap=<?= urlencode($row['ma_thuc_tap']); ?>" class="btn btn-danger btn-sm">Xóa</a>
                            <?php endif; ?>
                            <a href="suathuctap.php?edit=<?= urlencode($row['ma_thuc_tap']) ?>" class="btn btn-warning btn-sm">Sửa</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>