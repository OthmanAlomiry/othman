<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الأستاذ محمد الزبيدي | معلم تربوي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
            --text-color: #333;
            --bg-color: #f4f7f6;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .profile-card {
            background: #fff;
            width: 350px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            text-align: center;
            padding: 30px;
            transition: transform 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-10px);
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--accent-color);
            margin-bottom: 20px;
        }

        h1 {
            font-size: 24px;
            color: var(--primary-color);
            margin: 10px 0;
        }

        .title {
            color: var(--accent-color);
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 15px;
            display: block;
        }

        .bio {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-links a {
            text-decoration: none;
            width: 40px;
            height: 40px;
            line-height: 40px;
            background: var(--primary-color);
            color: #fff;
            border-radius: 50%;
            transition: 0.3s;
        }

        .social-links a:hover {
            background: var(--accent-color);
            transform: scale(1.2);
        }

        .contact-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 25px;
            background-color: var(--accent-color);
            color: white;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .contact-btn:hover {
            background-color: var(--primary-color);
        }
    </style>
</head>
<body>

    <?php
        // بيانات الأستاذ - يمكنك تعديلها بسهولة من هنا
        $name = "محمد الزبيدي";
        $job_title = "معلم تربوي وخبير تعليمي";
        $bio = "شغوف بنقل المعرفة وبناء الأجيال. متخصص في طرق التدريس الحديثة وتطوير المهارات التربوية للطلاب.";
        
        // روابط التواصل الاجتماعي
        $social_media = [
            "twitter" => "https://twitter.com/username",
            "facebook" => "https://facebook.com/username",
            "whatsapp" => "https://wa.me/966500000000",
            "linkedin" => "https://linkedin.com/in/username"
        ];
    ?>

    <div class="profile-card">
        <img src="https://via.placeholder.com/150" alt="صورة الأستاذ محمد الزبيدي" class="profile-img">
        
        <h1><?php echo $name; ?></h1>
        <span class="title"><?php echo $job_title; ?></span>
        
        <p class="bio"><?php echo $bio; ?></p>

        <div class="social-links">
            <a href="<?php echo $social_media['twitter']; ?>" title="تويتر"><i class="fab fa-x-twitter"></i></a>
            <a href="<?php echo $social_media['facebook']; ?>" title="فيسبوك"><i class="fab fa-facebook-f"></i></a>
            <a href="<?php echo $social_media['whatsapp']; ?>" title="واتساب"><i class="fab fa-whatsapp"></i></a>
            <a href="<?php echo $social_media['linkedin']; ?>" title="لينكد إن"><i class="fab fa-linkedin-in"></i></a>
        </div>

        <a href="mailto:info@example.com" class="contact-btn">تواصل معي مباشرة</a>
    </div>

</body>
</html>
