<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user_name'])) {
    header("Location: dangnhap.php"); // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
    exit;
}

// Lấy vai trò từ session
$vai_tro = isset($_SESSION['vai_tro']) ? $_SESSION['vai_tro'] : 0; // Mặc định là 0 nếu không có
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Thực tập Sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        /* Định dạng thanh sidebar */
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
            transition: 0.3s;
            z-index: 999; /* Đảm bảo sidebar luôn hiển thị trên các phần tử khác */
        }

        /* Ẩn sidebar khi có class 'closed' */
        .sidebar.closed {
            width: 0;
            padding-top: 0;
            visibility: hidden; /* Ẩn hoàn toàn nội dung trong sidebar */
        }

        /* Định dạng các liên kết trong sidebar */
        .sidebar a {
            color: white;
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            display: block;
            transition: 0.3s;
        }

        /* Hiệu ứng hover cho các liên kết trong sidebar */
        .sidebar a:hover {
            background-color: #575757;
        }

        /* Định dạng nút toggle */
        .toggle-btn {
            font-size: 20px;
            cursor: pointer;
            position: absolute;
            top: 20px;
            left: 250px; /* Nút nằm trong thanh sidebar khi mở */
            background-color: #C0C0C0;
            color: white;
            padding: 15px;
            border-radius: 0;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s, transform 0.3s, left 0.3s;
        }

        /* Hiệu ứng hover cho nút toggle */
        .toggle-btn:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }

        /* Loại bỏ outline khi nhấn nút toggle */
        .toggle-btn:focus {
            outline: none;
        }

        /* Di chuyển nút toggle ra ngoài khi thanh sidebar mở */
        .toggle-btn.sidebar-open {
            left: 0; /* Nút di chuyển ra ngoài khi thanh sidebar mở */
        }
    </style>
</head>
<body>

    <!-- Thanh sidebar -->
    <div class="sidebar" id="sidebar">
        <h2 class="text-center text-white">Quản lý Thực tập Sinh viên</h2>
        <a href="index.php">Trang chủ</a>
        <a href="sinhvien.php">Quản lý Sinh viên</a>
        <a href="lop.php">Quản lý Lớp</a>
        <a href="dotthuctap.php">Quản lý Đợt thực tập</a>
        <a href="donvi.php">Quản lý Đơn vị thực tập</a>
        <a href="nguoihuongdan.php">Quản lý Người hướng dẫn</a>
        <a href="thuctap.php">Quản lý Thông tin thực tập</a>
        <a href="thongke.php">Thống kê</a>

        <!-- Hiển thị liên kết "Quản lý quản trị viên" chỉ cho người dùng có vai trò 1 -->
        <?php if ($vai_tro == 1) { ?>
            <a href="quantri.php">Quản lý quản trị viên</a>
        <?php } ?>

        <h5 class="text-center text-white" style="text-shadow: 1px 1px 0 #ff0000, -1px -1px 0 #ff0000, 1px -1px 0 #ff0000, -1px 1px 0 #ff0000;">
            Xin chào <?= htmlspecialchars($_SESSION['user_name']) ?> !
        </h5> <!-- Hiển thị tên người dùng -->
        <a href="dangxuat.php" class="text-danger">Đăng xuất</a> <!-- Nút đăng xuất -->
    </div>

    <!-- Nút toggle để ẩn/hiển thị sidebar -->
    <button class="toggle-btn" id="toggle-btn" onclick="toggleSidebar()">&#9776;</button>

    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script để điều khiển ẩn/hiển thị sidebar -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggle-btn');
            sidebar.classList.toggle('closed'); // Thêm hoặc xóa class 'closed' để ẩn/hiển thị sidebar
            toggleBtn.classList.toggle('sidebar-open'); // Di chuyển nút khi thanh sidebar mở
        }
    </script>
</body>
</html>