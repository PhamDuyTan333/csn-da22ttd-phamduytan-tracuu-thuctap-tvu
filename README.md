# csn-da22ttd-phamduytan-tracuu-thuctap-tvu

Thiết kế Ứng dụng Web Tra cứu Thông tin Thực tập

Mô tả
Dự án "Thiết kế ứng dụng web phục vụ tra cứu thông tin thực tập của sinh viên tại Trường Đại học Trà Vinh" là một ứng dụng web cho phép sinh viên, giảng viên và quản lý dễ dàng tra cứu thông tin liên quan đến quá trình thực tập của sinh viên. Ứng dụng hỗ trợ tìm kiếm thông tin sinh viên và kỳ thực tập dựa trên các tiêu chí như họ tên, mã số sinh viên, lớp và khóa học.

Ứng dụng hiển thị chi tiết thông tin sinh viên, đề tài thực tập, điểm số và thông tin của các giảng viên hướng dẫn, báo cáo. Ngoài ra, thông tin chi tiết về giảng viên có thể được xem trực tiếp từ kết quả tìm kiếm.

Tính năng
- Tra cứu thông tin thực tập của sinh viên.
- Tìm kiếm theo tên sinh viên, mã số sinh viên, lớp, và khóa học.
- Hiển thị thông tin chi tiết về sinh viên, đề tài thực tập, giảng viên hướng dẫn và giảng viên báo cáo.
- Hiển thị chi tiết thông tin giảng viên: họ tên, số điện thoại, địa chỉ liên hệ, số tài khoản ngân hàng, và chi nhánh ngân hàng (nếu không phải là giảng viên trường).

Công nghệ sử dụng
- Giao diện: HTML, CSS, BOOSTRAP
- chức năng: PHP
- Cơ sở dữ liệu: MySQL

Cài đặt
Yêu cầu:
- Máy chủ web Apache hoặc Nginx (có PHP 7.x trở lên)
- MySQL 5.x trở lên
- Trình duyệt web hỗ trợ HTML và CSS

Bước 1: Sao chép dự án về máy
git clone https://github.com/PhamDuyTan333/csn-da22ttd-phamduytan-tracuu-thuctap-tvu.git

Bước 2: Cấu hình cơ sở dữ liệu
1. Tạo cơ sở dữ liệu MySQL:
sql
CREATE DATABASE dacsn;


2. Chạy các lệnh SQL để tạo bảng và thêm dữ liệu mẫu (có trong file "tracuuttsvtt.sql`):
sql
USE dacsn;
-- Chạy các lệnh tạo bảng và thêm dữ liệu mẫu

Bước 3: Cấu hình kết nối cơ sở dữ liệu
- Mở file "index.php" và điều chỉnh thông tin kết nối cơ sở dữ liệu:
php
$servername = "localhost"; 
$username = "root";       
$password = "";          
$dbname = "tracuuttsvtt";        

Bước 4: Chạy ứng dụng
- Mở trình duyệt và truy cập địa chỉ:

http://localhost81/tracuuttsvtt/


Sử dụng
1. Nhập một trong các thông tin sau để tìm kiếm:
    - Mã sinh viên
    - Họ tên
    - Lớp
    - Khóa học

2. Nhấn nút "Tìm kiếm" để xem kết quả.

3. Xem thông tin chi tiết giảng viên bằng cách nhấn vào liên kết "Xem chi tiết" trong kết quả tìm kiếm.

Cấu trúc thư mục
tracuuttsvtt/

             

Ghi chú
- Mã nguồn này chỉ sử dụng cho mục đích học tập.
- Đảm bảo bảo mật thông tin kết nối cơ sở dữ liệu trong môi trường thực tế.

Liên hệ
Tác giả: Phạm Duy Tân, 
GV.Hướng dẫn: Nguyễn Ngọc Đan Thanh
Khoa Kỹ thuật và Công nghệ, Trường Đại học Trà Vinh.
