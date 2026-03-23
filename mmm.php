<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محمد المصلحي | الصفحة الشخصية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-glow: #00d2ff;
            --secondary-glow: #9d00ff;
            --bg-color: #0f0c29;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #000;
        }

        /* خلفية متحركة احترافية */
        .background {
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #0f0c29, #302b63, #24243e);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            z-index: -1;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* حاوية المحتوى */
        .profile-card {
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            padding: 50px;
            border-radius: 20px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            max-width: 400px;
            width: 90%;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid var(--primary-glow);
            box-shadow: 0 0 20px var(--primary-glow);
            margin-bottom: 20px;
            transition: 0.5s;
        }

        .profile-img:hover {
            transform: scale(1.05) rotate(5deg);
            box-shadow: 0 0 30px var(--secondary-glow);
            border-color: var(--secondary-glow);
        }

        h1 {
            color: white;
            margin: 10px 0;
            letter-spacing: 2px;
            font-size: 2rem;
            text-shadow: 0 0 10px rgba(255,255,255,0.3);
        }

        p {
            color: #ccc;
            margin-bottom: 30px;
        }

        /* أيقونات التواصل الاجتماعي المتوهجة */
        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .social-links a {
            position: relative;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            color: white;
            font-size: 24px;
            text-decoration: none;
            transition: 0.5s;
            border: 1px solid rgba(255,255,255,0.1);
            overflow: hidden;
        }

        .social-links a:hover {
            color: #fff;
            transform: translateY(-10px);
        }

        /* تأثير التوهج اللوني لكل أيقونة */
        .social-links a.facebook:hover { background: #1877F2; box-shadow: 0 0 20px #1877F2; }
        .social-links a.twitter:hover { background: #1DA1F2; box-shadow: 0 0 20px #1DA1F2; }
        .social-links a.linkedin:hover { background: #0077B5; box-shadow: 0 0 20px #0077B5; }
        .social-links a.instagram:hover { background: #E4405F; box-shadow: 0 0 20px #E4405F; }

    </style>
</head>
<body>

    <div class="background"></div>

    <div class="profile-card">
        <img src="https://via.placeholder.com/150" alt="محمد المصلحي" class="profile-img">
        
        <h1><?php echo "محمد المصلحي"; ?></h1>
        <p>مطور برمجيات | رائد أعمال | مصمم حلول رقمية</p>

        <div class="social-links">
            <a href="#" class="facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" class="linkedin"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" class="instagram"><i class="fab fa-instagram"></i></a>
        </div>
    </div>

</body>
</html>
