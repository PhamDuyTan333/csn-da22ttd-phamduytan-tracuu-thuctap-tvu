<?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$database = "tracuuttsvtt";

$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn số liệu thống kê
$sqlSinhVien = "SELECT COUNT(*) AS total FROM sinh_vien";
$sqlDonVi = "SELECT COUNT(*) AS total FROM don_vi";
$sqlHuongDan = "SELECT COUNT(*) AS total FROM nguoi_huong_dan";

$resultSinhVien = $conn->query($sqlSinhVien);
$resultDonVi = $conn->query($sqlDonVi);
$resultHuongDan = $conn->query($sqlHuongDan);

$totalSinhVien = $resultSinhVien->fetch_assoc()['total'] ?? 0;
$totalDonVi = $resultDonVi->fetch_assoc()['total'] ?? 0;
$totalHuongDan = $resultHuongDan->fetch_assoc()['total'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ Quản lý Thực tập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Nhúng thanh điều hướng -->
    <?php include('dau.php'); ?>

    <!-- Main Content -->
    <main class="container my-5">
        <h1 class="text-center mb-4">Tổng quan Hệ thống</h1>
        <div class="row">
            <div class="col-lg-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Tổng số Sinh viên</h5>
                        <p class="card-text display-4"><?php echo $totalSinhVien; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Tổng số Đơn vị Thực tập</h5>
                        <p class="card-text display-4"><?php echo $totalDonVi; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Tổng số Người Hướng dẫn</h5>
                        <p class="card-text display-4"><?php echo $totalHuongDan; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
