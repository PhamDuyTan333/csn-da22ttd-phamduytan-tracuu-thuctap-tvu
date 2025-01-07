<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "tracuuttsvtt";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Corrected query to retrieve internship statistics including start and end dates
$sql = "
    SELECT 
        don_vi.ten_don_vi AS ten_don_vi,
        YEAR(dot_thuc_tap.ngay_bat_dau) AS nam_thuc_tap,
        COUNT(thuc_tap.ma_sinh_vien) AS so_luong_sinh_vien,
        AVG(thuc_tap.diem_danh_gia) AS diem_trung_binh,
        MIN(dot_thuc_tap.ngay_bat_dau) AS ngay_bat_dau,
        MAX(dot_thuc_tap.ngay_ket_thuc) AS ngay_ket_thuc
    FROM 
        thuc_tap
    JOIN 
        don_vi ON thuc_tap.ma_don_vi = don_vi.ma_don_vi
    JOIN 
        dot_thuc_tap ON thuc_tap.ma_dot_thuc_tap = dot_thuc_tap.ma_dot_thuc_tap
    GROUP BY 
        don_vi.ten_don_vi, YEAR(dot_thuc_tap.ngay_bat_dau)
    ORDER BY 
        nam_thuc_tap ASC, ten_don_vi ASC;
";

$result = $conn->query($sql);
$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê Thực tập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Nhúng thanh điều hướng -->
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center">Thống kê Thực tập Hằng năm</h1>
        <table class="table table-bordered table-striped mt-4">
            <thead class="table-primary">
                <tr>
                    <th>Tên đơn vị</th>
                    <th>Năm thực tập</th>
                    <th>Số lượng sinh viên</th>
                    <th>Điểm trung bình</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?php echo $row['ten_don_vi']; ?></td>
                    <td><?php echo $row['nam_thuc_tap']; ?></td>
                    <td><?php echo $row['so_luong_sinh_vien']; ?></td>
                    <td><?php echo number_format($row['diem_trung_binh'], 2); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row['ngay_bat_dau'])); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row['ngay_ket_thuc'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
