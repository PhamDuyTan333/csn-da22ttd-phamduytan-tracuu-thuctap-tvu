<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Tra Cứu Thông Tin Thực Tập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
        }

        .hero-section {
            position: relative;
            background: url('image/nen-TVU.jpg') no-repeat center center/cover;
            height: 300px;
            color: white;
            display: flex;
            align-items: center; 
            justify-content: center; 
            text-align: center; 
            flex-direction: column; 
            padding: 20px;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5); 
            z-index: 1;
        }

        .hero-section h1 {
            position: relative;
            z-index: 2;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
            margin: 0;
        }

        .btntracuu-section {
            display: flex;
            justify-content: center;
            margin-top: 20px; 
            padding: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3; 
        }

    </style>
</head>
<body>
    <header class="bg-dark text-white p-3">
        <div class="container d-flex align-items-center">
            <img src="image/Logo-TVU.png" alt="Logo Trường Đại Học Trà Vinh" style="height: 50px; margin-right: 10px;">
            <h2 class="mb-0">Hệ Thống Tra Cứu Thực Tập Sinh Viên TVU</h2>
        </div>
    </header>

    <section class="hero-section">
        <h1>Chào mừng đến với Hệ Thống Tra Cứu Thông Tin Thực Tập Của Sinh Viên <br> Tại Trường Đại Học Trà Vinh</h1>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>