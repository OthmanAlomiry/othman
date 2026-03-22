<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>متجر خطوات الأناقة | لل أحذية العصرية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('hero-shoes.jpg');
            background-size: cover;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .shoe-card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .shoe-card:hover { transform: translateY(-10px); }
        .btn-buy { background-color: #ff4757; color: white; border-radius: 25px; }
    </style>
</head>
<body>

<section class="hero-section">
    <div class="container">
        <h1 class="display-3 fw-bold">سر في درب التميز</h1>
        <p class="lead">تشكيلة ربيع 2026 وصلت الآن بخصومات تصل إلى 30%</p>
        <a href="#products" class="btn btn-light btn-lg">تسوق الآن</a>
    </div>
</section>

<div class="container my-5" id="products">
    <h2 class="text-center mb-5">أحدث الصيحات</h2>
    <div class="row">
        <?php
        // اتصال بسيط بقاعدة البيانات
        $conn = new mysqli("localhost", "root", "", "shoe_store");
        $result = $conn->query("SELECT * FROM shoes LIMIT 6");

        while($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card shoe-card">
                    <img src="images/<?php echo $row['image_url']; ?>" class="card-img-top" alt="...">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo $row['name']; ?></h5>
                        <p class="text-muted"><?php echo $row['brand']; ?></p>
                        <h4 class="text-danger"><?php echo $row['price']; ?> ريال</h4>
                        <button class="btn btn-buy px-4">أضف للسلة</button>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
