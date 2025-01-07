<?php
// Kết nối tới cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tracuuttsvtt"; // Thay thế bằng tên cơ sở dữ liệu của bạn

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// API để lấy ngày bắt đầu và ngày kết thúc của đợt thực tập
if (isset($_GET['ma_dot_thuc_tap'])) {
    $ma_dot_thuc_tap = $_GET['ma_dot_thuc_tap'];

    $sql = "SELECT ngay_bat_dau, ngay_ket_thuc FROM dot_thuc_tap WHERE ma_dot_thuc_tap = '$ma_dot_thuc_tap'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['ngay_bat_dau' => '', 'ngay_ket_thuc' => '']);
    }
    exit;
}

// Thêm thông tin thực tập
if (isset($_POST['add'])) {
    // Lấy giá trị từ form
    $ma_sinh_vien = $_POST['ma_sinh_vien'];
    $ma_dot_thuc_tap = $_POST['ma_dot_thuc_tap'];
    $ma_don_vi = $_POST['ma_don_vi'];
    $ma_nguoi_huong_dan = $_POST['ma_nguoi_huong_dan'];
    $nhiem_vu = $_POST['nhiem_vu'];
    $ngay_bat_dau = $_POST['ngay_bat_dau'];
    $ngay_ket_thuc = $_POST['ngay_ket_thuc'];

    // Câu lệnh SQL để thêm dữ liệu vào bảng thuc_tap
    $sql = "INSERT INTO thuc_tap (ma_sinh_vien, ma_dot_thuc_tap, ma_don_vi, ma_nguoi_huong_dan, nhiem_vu, ngay_bat_dau, ngay_ket_thuc)
            VALUES ('$ma_sinh_vien', '$ma_dot_thuc_tap', '$ma_don_vi', '$ma_nguoi_huong_dan', '$nhiem_vu', '$ngay_bat_dau', '$ngay_ket_thuc')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Thêm thành công!');</script>";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

// Truy vấn để lấy danh sách sinh viên
$sql_sinh_vien = "SELECT ma_sinh_vien, ho_ten FROM sinh_vien";
$result_sinh_vien = $conn->query($sql_sinh_vien);

// Truy vấn để lấy danh sách đợt thực tập
$sql_dot_thuc_tap = "SELECT ma_dot_thuc_tap, ten_dot, ngay_bat_dau, ngay_ket_thuc FROM dot_thuc_tap";
$result_dot_thuc_tap = $conn->query($sql_dot_thuc_tap);

// Truy vấn để lấy danh sách đơn vị
$sql_don_vi = "SELECT ma_don_vi, ten_don_vi FROM don_vi";
$result_don_vi = $conn->query($sql_don_vi);

// Truy vấn để lấy danh sách người hướng dẫn tại trường (ma_loai_nguoi_huong_dan = 2)
$sql_nguoi_huong_dan_truong = "SELECT ma_nguoi_huong_dan, ten FROM nguoi_huong_dan WHERE ma_loai_nguoi_huong_dan = 2";
$result_nguoi_huong_dan_truong = $conn->query($sql_nguoi_huong_dan_truong);

// Truy vấn để lấy danh sách người hướng dẫn tại đơn vị (ma_loai_nguoi_huong_dan = 1)
$sql_nguoi_huong_dan_don_vi = "SELECT ma_nguoi_huong_dan, ten FROM nguoi_huong_dan WHERE ma_loai_nguoi_huong_dan = 1";
$result_nguoi_huong_dan_don_vi = $conn->query($sql_nguoi_huong_dan_don_vi);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Thực Tập Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Danh Sách Thực Tập Sinh Viên</h2>
        <hr>

        <!-- Nút Thêm Thực Tập (Mở Modal) -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Thêm Thực Tập</button>

        <!-- Modal Thêm Thực Tập -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Thêm Thực Tập Sinh Viên</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="thuctap.php">
                            <div class="mb-3">
                                <label for="ma_sinh_vien" class="form-label">Sinh Viên</label>
                                <select name="ma_sinh_vien" class="form-select" required>
                                    <option value="">Chọn Sinh Viên</option>
                                    <?php while ($row = $result_sinh_vien->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['ma_sinh_vien']; ?>"><?php echo $row['ho_ten']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="ma_dot_thuc_tap" class="form-label">Đợt Thực Tập</label>
                                <select name="ma_dot_thuc_tap" class="form-select" required>
                                    <option value="">Chọn Đợt Thực Tập</option>
                                    <?php while ($row = $result_dot_thuc_tap->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['ma_dot_thuc_tap']; ?>"><?php echo $row['ten_dot']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="ma_don_vi" class="form-label">Đơn Vị</label>
                                <select name="ma_don_vi" class="form-select" required>
                                    <option value="">Chọn Đơn Vị</option>
                                    <?php while ($row = $result_don_vi->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['ma_don_vi']; ?>"><?php echo $row['ten_don_vi']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="ma_nguoi_huong_dan_truong" class="form-label">Người Hướng Dẫn (Tại Trường)</label>
                                <select name="ma_nguoi_huong_dan" class="form-select" required>
                                    <option value="">Chọn Người Hướng Dẫn Tại Trường</option>
                                    <?php while ($row = $result_nguoi_huong_dan_truong->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['ma_nguoi_huong_dan']; ?>"><?php echo $row['ten']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="ma_nguoi_huong_dan_don_vi" class="form-label">Người Hướng Dẫn (Tại Đơn Vị)</label>
                                <select name="ma_nguoi_huong_dan" class="form-select" required>
                                    <option value="">Chọn Người Hướng Dẫn Tại Đơn Vị</option>
                                    <?php while ($row = $result_nguoi_huong_dan_don_vi->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['ma_nguoi_huong_dan']; ?>"><?php echo $row['ten']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="nhiem_vu" class="form-label">Nhiệm Vụ</label>
                                <textarea name="nhiem_vu" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="ngay_bat_dau" class="form-label">Ngày Bắt Đầu</label>
                                <input type="date" name="ngay_bat_dau" class="form-control" required readonly>
                            </div>

                            <div class="mb-3">
                                <label for="ngay_ket_thuc" class="form-label">Ngày Kết Thúc</label>
                                <input type="date" name="ngay_ket_thuc" class="form-control" required readonly>
                            </div>

                            <button type="submit" name="add" class="btn btn-success">Thêm</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('select[name="ma_dot_thuc_tap"]').change(function () {
                var ma_dot_thuc_tap = $(this).val();
                if (ma_dot_thuc_tap) {
                    $.ajax({
                        url: 'thuctap.php',
                        type: 'GET',
                        data: { ma_dot_thuc_tap: ma_dot_thuc_tap },
                        success: function (data) {
                            var response = JSON.parse(data);
                            $('input[name="ngay_bat_dau"]').val(response.ngay_bat_dau);
                            $('input[name="ngay_ket_thuc"]').val(response.ngay_ket_thuc);
                        },
                        error: function () {
                            alert('Không thể tải dữ liệu. Vui lòng thử lại.');
                        }
                    });
                } else {
                    $('input[name="ngay_bat_dau"]').val('');
                    $('input[name="ngay_ket_thuc"]').val('');
                }
            });
        });
    </script>
</body>
</html>
