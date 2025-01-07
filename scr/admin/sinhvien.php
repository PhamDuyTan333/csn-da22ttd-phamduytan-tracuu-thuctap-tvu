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

// Lấy vai trò người dùng
$vaitro = isset($_SESSION['vai_tro']) ? $_SESSION['vai_tro'] : 0;

// Thêm sinh viên
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $ma_sinh_vien = $_POST['ma_sinh_vien'];
    $ho_ten = $_POST['ho_ten'];
    $lop = $_POST['lop']; // ma_lop từ dropdown
    $khoa = $_POST['khoa'];
    $email = $_POST['email'];
    $so_dien_thoai = $_POST['so_dien_thoai'];

    $stmt = $connection->prepare("INSERT INTO sinh_vien (ma_sinh_vien, ho_ten, lop, khoa, email, so_dien_thoai) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $ma_sinh_vien, $ho_ten, $lop, $khoa, $email, $so_dien_thoai);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Thêm sinh viên thành công!";
    } else {
        $_SESSION['message'] = "Lỗi khi thêm sinh viên!";
    }
    $stmt->close();
}

// Xóa sinh viên
if (isset($_GET['delete'])) {
    $ma_sinh_vien = $_GET['delete'];
    $stmt = $connection->prepare("DELETE FROM sinh_vien WHERE ma_sinh_vien = ?");
    $stmt->bind_param("s", $ma_sinh_vien);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Xóa sinh viên thành công!";
    } else {
        $_SESSION['message'] = "Lỗi khi xóa sinh viên!";
    }
    $stmt->close();
    header("Location: sinhvien.php");
    exit;
}

// Xử lý tìm kiếm và hiển thị danh sách sinh viên
$search = '';
if (isset($_GET['search'])) {
    $search = $connection->real_escape_string($_GET['search']);
}

$students = [];
$query = "
    SELECT sv.ma_sinh_vien, sv.ho_ten, sv.lop, sv.khoa, sv.email, sv.so_dien_thoai, l.ten_lop 
    FROM sinh_vien sv 
    LEFT JOIN lop l ON sv.lop = l.ma_lop
    WHERE sv.ma_sinh_vien LIKE '%$search%' 
    OR sv.ho_ten LIKE '%$search%' 
    OR sv.lop LIKE '%$search%' 
    OR sv.khoa LIKE '%$search%'
";

$result = $connection->query($query);
if ($result) {
    $students = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h2>Danh sách Sinh viên</h2>

        <?php if (isset($_SESSION['message'])) { ?>
            <div class="alert alert-info"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php } ?>

        <form action="sinhvien.php" method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm kiếm theo Mã sinh viên, Họ tên, Mã lớp, Khóa" aria-label="Tìm kiếm">
                <button class="btn btn-primary" type="submit">Tìm kiếm</button>
            </div>
        </form>

        <!-- Ẩn nút "Thêm Sinh viên" nếu vai trò là 0 -->
        <?php if ($vaitro == 1) { ?>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addStudentModal">Thêm Sinh viên</button>
        <?php } ?>

        <!-- Modal thêm sinh viên -->
        <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">Thêm Sinh viên</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="sinhvien.php" method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="ma_sinh_vien" class="form-label">Mã Sinh viên</label>
                                <input type="text" class="form-control" id="ma_sinh_vien" name="ma_sinh_vien" required>
                            </div>
                            <div class="mb-3">
                                <label for="ho_ten" class="form-label">Họ và Tên</label>
                                <input type="text" class="form-control" id="ho_ten" name="ho_ten" required>
                            </div>
                            <div class="mb-3">
                                <label for="lop" class="form-label">Lớp</label>
                                <select class="form-select" id="lop" name="lop" required>
                                    <option value="">Chọn lớp</option>
                                    <?php foreach ($classes as $class) { ?>
                                        <option value="<?= htmlspecialchars($class['ma_lop']) ?>">
                                            <?= htmlspecialchars($class['ma_lop'] . ' - ' . $class['ten_lop']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="khoa" class="form-label">Khoá</label>
                                <input type="text" class="form-control" id="khoa" name="khoa" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" name="add_student" class="btn btn-primary">Thêm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Danh sách sinh viên -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã Sinh viên</th>
                    <th>Họ và Tên</th>
                    <th>Lớp</th>
                    <th>Khoá</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) > 0) { ?>
                    <?php foreach ($students as $row) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ma_sinh_vien']) ?></td>
                            <td><?= htmlspecialchars($row['ho_ten']) ?></td>
                            <td><?= htmlspecialchars($row['lop'] . ' - ' . $row['ten_lop']) ?></td>
                            <td><?= htmlspecialchars($row['khoa']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['so_dien_thoai']) ?></td>
                            <td>
                                <a href="suasinhvien.php?edit=<?= $row['ma_sinh_vien'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <!-- Ẩn nút "Xóa" nếu vai trò là 0 -->
                                <?php if ($vaitro == 1) { ?>
                                <a href="sinhvien.php?delete=<?= $row['ma_sinh_vien'] ?>" class="btn btn-danger btn-sm">Xóa</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" class="text-center">Không có dữ liệu.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>