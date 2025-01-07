<?php
session_start();
$connection = new mysqli('localhost', 'root', '', 'tracuuttsvtt');

// Kiểm tra kết nối
if ($connection->connect_error) {
    die("Kết nối thất bại: " . $connection->connect_error);
}

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user_name'])) {
    header("Location: dangnhap.php"); // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
    exit;
}

// Lấy vai trò từ session
$vai_tro = isset($_SESSION['vai_tro']) ? $_SESSION['vai_tro'] : 0; // Mặc định là 0 nếu không có
if ($vai_tro == 0) {
    header("Location: index.php"); // Chuyển hướng về trang chính nếu vai trò là 0
    exit;
}


// Xử lý thêm quản trị viên
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $ten_quan_tri = $_POST['ten_quan_tri'];
    $email = $_POST['email'];
    $mat_khau = password_hash($_POST['mat_khau'], PASSWORD_DEFAULT); // Mã hóa mật khẩu
    $vai_tro = 0; // Thiết lập vai trò mặc định là 0

    // Kiểm tra xem email đã tồn tại chưa
    $stmt = $connection->prepare("SELECT * FROM quan_tri WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['message'] = "Email đã tồn tại! Vui lòng sử dụng email khác.";
    } else {
        // Thêm vào cơ sở dữ liệu
        $stmt = $connection->prepare("INSERT INTO quan_tri (ten_quan_tri, email, mat_khau, vai_tro) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $ten_quan_tri, $email, $mat_khau, $vai_tro); // Thêm vai trò vào câu lệnh

        if ($stmt->execute()) {
            $_SESSION['message'] = "Thêm quản trị viên thành công!";
        } else {
            $_SESSION['message'] = "Thêm không thành công!";
        }
    }

    // Chuyển hướng để tránh thêm lại khi tải lại trang
    header("Location: quantri.php");
    exit; // Dừng thực hiện mã sau khi chuyển hướng
}

// Xử lý cập nhật quản trị viên
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $user_id = $_POST['user_id'];
    $ten_quan_tri = $_POST['ten_quan_tri'];
    $email = $_POST['email'];
    $mat_khau = $_POST['mat_khau'] ? password_hash($_POST['mat_khau'], PASSWORD_DEFAULT) : null; // Mã hóa mật khẩu nếu có
    $vai_tro = $_POST['vai_tro']; // Lấy vai trò từ form

    // Cập nhật thông tin
    if ($mat_khau) {
        $stmt = $connection->prepare("UPDATE quan_tri SET ten_quan_tri = ?, email = ?, mat_khau = ?, vai_tro = ? WHERE id = ?");
        $stmt->bind_param("sssii", $ten_quan_tri, $email, $mat_khau, $vai_tro, $user_id);
    } else {
        $stmt = $connection->prepare("UPDATE quan_tri SET ten_quan_tri = ?, email = ?, vai_tro = ? WHERE id = ?");
        $stmt->bind_param("ssii", $ten_quan_tri, $email, $vai_tro, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Cập nhật quản trị viên thành công!";
    } else {
        $_SESSION['message'] = "Cập nhật không thành công!";
    }
}

// Xử lý xóa quản trị viên
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Xóa quản trị viên
    $stmt = $connection->prepare("DELETE FROM quan_tri WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Xóa quản trị viên thành công!";
    } else {
        $_SESSION['message'] = "Xóa không thành công!";
    }
}

// Lấy danh sách quản trị viên
$stmt = $connection->prepare("SELECT * FROM quan_tri");
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Quản trị viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Nhúng thanh điều hướng -->
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h2>Quản lý Quản trị viên</h2>
        
        <?php if (isset($_SESSION['message'])) { ?>
            <div class="alert alert-info"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php } ?>

        <!-- Nút mở modal thêm quản trị viên -->
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addAdminModal">Thêm Quản trị viên</button>

        <!-- Danh sách quản trị viên -->
        <h4>Danh sách Quản trị viên</h4>
        <table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên Quản trị</th>
            <th>Email</th>
            <th>Vai trò</th> <!-- Thêm cột Vai trò -->
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['ten_quan_tri'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['vai_tro'] == 1 ? 'adminc' : 'adminp' ?></td> <!-- Hiển thị vai trò -->
                <td>
                    <!-- Nút sửa -->
                    <button class="btn btn-warning btn-sm" onclick="showEditForm(<?= $row['id'] ?>, '<?= $row['ten_quan_tri'] ?>', '<?= $row['email'] ?>')">Sửa</button>
                    <!-- Nút xóa -->
                    <a href="?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa không?');">Xóa</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
        </table>

        <!-- Form sửa quản trị viên (ẩn ban đầu) -->
        <h4>Sửa Quản trị viên</h4>
<form id="editForm" method="POST" style="display:none;">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="user_id" id="user_id">
    <div class="mb-3">
        <label for="edit_ten_quan_tri" class="form-label">Tên Quản trị</label>
        <input type="text" class="form-control" id="edit_ten_quan_tri" name="ten_quan_tri" required>
    </div>
    <div class="mb-3">
        <label for="edit_email" class="form-label">Email</label>
        <input type="email" class="form-control" id="edit_email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="edit_mat_khau" class="form-label">Mật khẩu mới</label>
        <input type="password" class="form-control" id="edit_mat_khau" name="mat_khau" placeholder="Nhập mật khẩu mới (nếu có)">
    </div>
    <div class="mb-3">
        <label for="edit_vai_tro" class="form-label">Vai trò</label>
        <select class="form-control" id="edit_vai_tro" name="vai_tro">
            <option value="0">adminp</option>
            <option value="1">adminc</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Cập nhật</button>
    <button type="button" class="btn btn-secondary" onclick="hideEditForm()">Hủy</button>
</form>
    </div>

    <!-- Modal thêm quản trị viên -->
    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAdminModalLabel">Thêm Quản trị viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="ten_quan_tri" class="form-label">Tên Quản trị</label>
                            <input type="text" class="form-control" id="ten_quan_tri" name="ten_quan_tri" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="mat_khau" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="mat_khau" name="mat_khau" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Thêm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showEditForm(id, ten, email) {
            document.getElementById('editForm').style.display = 'block';
            document.getElementById('user_id').value = id;
            document.getElementById('edit_ten_quan_tri').value = ten;
            document.getElementById('edit_email').value = email;
        }

        function hideEditForm() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>
</html>