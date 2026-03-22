<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عقيل المصلحي | الصفحة الشخصية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00d2ff;
            --secondary-color: #3a7bd5;
            --bg-dark: #0f0c29;
            --text-white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            color: var(--text-white);
        }

        /* حاوية البطاقة التعريفية */
        .profile-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            transform: translateY(50px);
            opacity: 0;
            animation: slideUp 1s ease forwards;
            max-width: 400px;
            width: 90%;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid var(--primary-color);
            margin-bottom: 20px;
            transition: transform 0.5s ease;
        }

        .profile-img:hover {
            transform: rotate(360px) scale(1.1);
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        p {
            font-size: 16px;
            color: #ccc;
            margin-bottom: 30px;
        }

        /* أيقونات التواصل الاجتماعي */
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .social-icons a {
            text-decoration: none;
            color: var(--text-white);
            font-size: 24px;
            width: 50px;
            height: 50px;
            line-height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            display: inline-block;
            position: relative;
        }

        .social-icons a:hover {
            background: var(--primary-color);
            transform: translateY(-10px);
            box-shadow: 0 0 20px var(--primary-color);
        }

        /* تأثير جزيئات الخلفية البسيطة */
        .background-animate {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
        }
    </style>
</head>
<body>

    <div class="background-animate" id="particles-js"></div>

    <div class="profile-card">
        <img src="https://via.placeholder.com/150" alt="عقيل المصلحي" class="profile-img">
        
        <h1>عقيل المصلحي</h1>
        <p>مطور محتوى | صانع تجارب رقمية</p>

        <div class="social-icons">
            <a href="#" title="X (Twitter)"><i class="fab fa-x-twitter"></i></a>
            <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" title="Snapchat"><i class="fab fa-snapchat"></i></a>
            <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS('particles-js', {
            "particles": {
                "number": { "value": 80 },
                "color": { "value": "#ffffff" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.5 },
                "size": { "value": 3 },
                "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 },
                "move": { "enable": true, "speed": 4 }
            }
        });
    </script>
</body>
</html>
