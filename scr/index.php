<?php  
// Kết nối tới cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tracuuttsvtt";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Biến để lưu kết quả tra cứu
$results = [];
$search_query = "";
$params = [];

// Xử lý tra cứu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $ma_sinh_vien = $_POST['ma_sinh_vien'] ?? '';
    $ho_ten = $_POST['ho_ten'] ?? '';
    $lop = $_POST['lop'] ?? '';
    $khoa = $_POST['khoa'] ?? '';

    // Tạo truy vấn tìm kiếm
    $search_query = "SELECT sv.ma_sinh_vien, sv.ho_ten, sv.lop, sv.khoa, 
                            dv.ten_don_vi, tt.ten_de_tai_thuc_tap, 
                            hdt.ten AS ten_nguoi_huong_dan_truong, 
                            hddv.ten AS ten_nguoi_huong_dan_don_vi,
                            tt.nhiem_vu, tt.diem_danh_gia,
                            dv.dia_chi AS dia_chi_don_vi,
                            dv.so_dien_thoai AS so_dien_thoai_don_vi,
                            dv.email AS email_don_vi,
                            hdt.email AS email_huong_dan_truong,
                            hdt.so_dien_thoai AS so_dien_thoai_huong_dan_truong,
                            hdt.chuc_vu AS chuc_vu_huong_dan_truong,
                            hddv.email AS email_huong_dan_don_vi,
                            hddv.so_dien_thoai AS so_dien_thoai_huong_dan_don_vi,
                            hddv.chuc_vu AS chuc_vu_huong_dan_don_vi
                     FROM thuc_tap tt 
                     JOIN sinh_vien sv ON tt.ma_sinh_vien = sv.ma_sinh_vien 
                     JOIN don_vi dv ON tt.ma_don_vi = dv.ma_don_vi
                     LEFT JOIN nguoi_huong_dan hdt ON tt.ma_nguoi_huong_dan_truong = hdt.ma_nguoi_huong_dan
                     LEFT JOIN nguoi_huong_dan hddv ON tt.ma_nguoi_huong_dan_don_vi = hddv.ma_nguoi_huong_dan
                     WHERE 1=1";

    if (!empty($ma_sinh_vien)) {
        $search_query .= " AND sv.ma_sinh_vien LIKE ?";
        $params[] = "%$ma_sinh_vien%";
    }
    if (!empty($ho_ten)) {
        $search_query .= " AND sv.ho_ten LIKE ?";
        $params[] = "%$ho_ten%";
    }
    if (!empty($lop)) {
        $search_query .= " AND sv.lop LIKE ?";
        $params[] = "%$lop%";
    }
    if (!empty($khoa)) {
        $search_query .= " AND sv.khoa LIKE ?";
        $params[] = "%$khoa%";
    }

    $stmt = $conn->prepare($search_query);
    if ($stmt === false) {
        die("Lỗi truy vấn: " . $conn->error);
    }

    // Bind parameters
    if (!empty($params)) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra Cứu Thông Tin Thực Tập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        footer {
            margin-top: auto;
            padding: 20px;
            background-color: #343a40;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Nhúng thanh điều hướng -->
    <?php include('dau.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center">Tra Cứu Thông Tin Thực Tập</h1>
        <form method="POST" action="index.php" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="ma_sinh_vien" class="form-control" placeholder="Mã Sinh Viên" value="<?= htmlspecialchars($ma_sinh_vien ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" name="ho_ten" class="form-control" placeholder="Họ Tên" value="<?= htmlspecialchars($ho_ten ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" name="lop" class="form-control" placeholder="Lớp" value="<?= htmlspecialchars($lop ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" name="khoa" class="form-control" placeholder="Khóa" value="<?= htmlspecialchars($khoa ?? ''); ?>">
                </div>
            </div>
            <button type="submit" name="search" class="btn btn-primary mt-3">Tra cứu</button>
        </form>

        <?php if (!empty($results)): ?>
            <h3 class="text-center">Kết Quả Tra Cứu</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mã Sinh Viên</th>
                        <th>Họ Tên</th>
                        <th>Lớp</th>
                        <th>Khóa</th>
                        <th>Tên Đơn Vị Thực Tập</th>
                        <th>Tên Đề Tài Thực Tập</th>
                        <th>Tên Người Hướng Dẫn Trường</th>
                        <th>Tên Người Hướng Dẫn Đơn Vị</th>
                        <th>Nhiệm Vụ</th>
                        <th>Điểm Đánh Giá</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ma_sinh_vien']); ?></td>
                            <td><?= htmlspecialchars($row['ho_ten']); ?></td>
                            <td><?= htmlspecialchars($row['lop']); ?></td>
                            <td><?= htmlspecialchars($row['khoa']); ?></td>
                            <td class="clickable" data-info-type="Đơn Vị" data-info-value="<?= htmlspecialchars($row['ten_don_vi']); ?>" 
                                data-info-detail="Địa chỉ: <?= htmlspecialchars($row['dia_chi_don_vi']); ?><br>SĐT: <?= htmlspecialchars($row['so_dien_thoai_don_vi']); ?><br>Email: <?= htmlspecialchars($row['email_don_vi']); ?>">
                                <?= htmlspecialchars($row['ten_don_vi']); ?>
                            </td>
                            <td><?= htmlspecialchars($row['ten_de_tai_thuc_tap']); ?></td>
                            <td class="clickable" data-info-type="Hướng Dẫn Trường" data-info-value="<?= htmlspecialchars($row['ten_nguoi_huong_dan_truong']); ?>" 
                                data-info-detail="Email: <?= htmlspecialchars($row['email_huong_dan_truong']); ?><br>SĐT: <?= htmlspecialchars($row['so_dien_thoai_huong_dan_truong']); ?><br>Chức vụ: <?= htmlspecialchars($row['chuc_vu_huong_dan_truong']); ?>">
                                <?= htmlspecialchars($row['ten_nguoi_huong_dan_truong']); ?>
                            </td>
                            <td class="clickable" data-info-type="Hướng Dẫn Đơn Vị" data-info-value="<?= htmlspecialchars($row['ten_nguoi_huong_dan_don_vi']); ?>" 
                                data-info-detail="Email: <?= htmlspecialchars($row['email_huong_dan_don_vi']); ?><br>SĐT: <?= htmlspecialchars($row['so_dien_thoai_huong_dan_don_vi']); ?><br>Chức vụ: <?= htmlspecialchars($row['chuc_vu_huong_dan_don_vi']); ?>">
                                <?= htmlspecialchars($row['ten_nguoi_huong_dan_don_vi']); ?>
                            </td>
                            <td><?= htmlspecialchars($row['nhiem_vu']); ?></td>
                            <td><?= htmlspecialchars($row['diem_danh_gia']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="alert alert-warning" role="alert">Không tìm thấy thông tin nào cho tiêu chí tìm kiếm của bạn.</div>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">Thông Tin Chi Tiết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Nội dung sẽ được cập nhật bằng JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    
    <br>
    <!-- Chèn footer từ file cuoi.php bằng PHP -->
    <?php include('cuoi.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalContent = document.getElementById('modalContent');

            // Lắng nghe sự kiện nhấn vào các ô tên
            document.querySelectorAll('.clickable').forEach(item => {
                item.addEventListener('click', function() {
                    const infoType = this.getAttribute('data-info-type');
                    const infoValue = this.getAttribute('data-info-value');
                    const infoDetail = this.getAttribute('data-info-detail');

                    // Cập nhật nội dung modal
                    modalContent.innerHTML = `<strong>${infoType}:</strong> ${infoValue}<br>${infoDetail}`;
                    
                    // Hiển thị modal
                    var myModal = new bootstrap.Modal(document.getElementById('infoModal'));
                    myModal.show();
                });
            });
        });
    </script>
</body>
</html>