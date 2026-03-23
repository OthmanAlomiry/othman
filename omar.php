<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عمر الزبيدي | الصفحة الشخصية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: #0a0a0a;
            color: #fff;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* الخلفية المتحركة (الدوائر العائمة) */
        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(45deg, #0f2027, #203a43, #2c5364);
            overflow: hidden;
        }

        .circles {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .circles li {
            position: absolute;
            display: block;
            list-style: none;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.1);
            animation: animate 25s linear infinite;
            bottom: -150px;
        }

        /* توزيع الدوائر المتحركة */
        .circles li:nth-child(1) { left: 25%; width: 80px; height: 80px; animation-delay: 0s; }
        .circles li:nth-child(2) { left: 10%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
        .circles li:nth-child(3) { left: 70%; width: 20px; height: 20px; animation-delay: 4s; }
        .circles li:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-delay: 0s; animation-duration: 18s; }
        .circles li:nth-child(5) { left: 65%; width: 20px; height: 20px; animation-delay: 0s; }

        @keyframes animate {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; border-radius: 0; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; border-radius: 50%; }
        }

        /* حاوية المحتوى الرئيسي */
        .card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            max-width: 400px;
            width: 90%;
            z-index: 1;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #00d2ff;
            margin-bottom: 20px;
            transition: 0.3s;
        }

        .profile-img:hover {
            transform: scale(1.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        p {
            font-size: 14px;
            color: #ccc;
            margin-bottom: 30px;
        }

        /* أزرار التواصل الاجتماعي */
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .social-links a {
            text-decoration: none;
            color: #fff;
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 20px;
            transition: 0.3s ease;
        }

        .social-links a:hover {
            background: #00d2ff;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 210, 255, 0.4);
        }

    </style>
</head>
<body>

    <div class="background">
        <ul class="circles">
            <li></li><li></li><li></li><li></li><li></li><li></li>
        </ul>
    </div>

    <div class="card">
        <img src="https://via.placeholder.com/150" alt="عمر الزبيدي" class="profile-img">
        
        <h1>عمر الزبيدي</h1>
        <p>مطور برمجيات | مهتم بالتكنولوجيا والتصميم الرقمي</p>

        <div class="social-links">
            <a href="https://twitter.com/your-username" target="_blank"><i class="fab fa-x-twitter"></i></a>
            <a href="https://instagram.com/your-username" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://snapchat.com/add/your-username" target="_blank"><i class="fab fa-snapchat"></i></a>
            <a href="https://linkedin.com/in/your-username" target="_blank"><i class="fab fa-linkedin"></i></a>
            <a href="mailto:your-email@example.com"><i class="fas fa-envelope"></i></a>
        </div>
    </div>

</body>
</html>
