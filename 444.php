<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عبدالله الزبيدي | الروابط الرسمية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700&family=Tajawal:wght@500;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --main-bg: #050810;
            --accent-color: #38bdf8;
            --glow-color: rgba(56, 189, 248, 0.5);
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        /* خلفية متحركة احترافية */
        body {
            font-family: 'Almarai', sans-serif;
            background: var(--main-bg);
            color: #f8fafc;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            background: var(--accent-color);
            filter: blur(150px);
            border-radius: 50%;
            z-index: -1;
            top: 10%;
            left: 10%;
            animation: move 10s infinite alternate;
        }

        @keyframes move {
            from { transform: translate(0, 0); }
            to { transform: translate(100px, 100px); }
        }

        .container {
            width: 90%;
            max-width: 450px;
            text-align: center;
            padding: 40px 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* صورة الملف الشخصي المتوهجة */
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 2px solid var(--accent-color);
            padding: 5px;
            box-shadow: 0 0 20px var(--glow-color);
            transition: 0.5s;
        }

        .profile-img:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 0 40px var(--accent-color);
        }

        h1 {
            font-family: 'Tajawal', sans-serif;
            font-size: 2rem;
            margin-top: 20px;
            text-shadow: 0 0 10px rgba(255,255,255,0.3);
        }

        .verified-badge { color: var(--accent-color); font-size: 1.2rem; }

        .bio {
            color: #94a3b8;
            font-size: 1rem;
            margin-bottom: 30px;
        }

        /* تصميم الأزرار بلمسة زجاجية وتوهج */
        .links-gap { display: flex; flex-direction: column; gap: 15px; }

        .social-item {
            position: relative;
            display: flex;
            align-items: center;
            padding: 16px 25px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            text-decoration: none;
            color: #fff;
            transition: 0.4s all ease;
            overflow: hidden;
        }

        .social-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        /* تأثير الضوء المتحرك داخل الزر */
        .social-item::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            transition: 0.5s;
        }

        .social-item:hover::after {
            left: 100%;
        }

        .social-item i { font-size: 1.4rem; width: 35px; }
        .social-item span { flex-grow: 1; font-weight: 500; }

        /* ألوان التوهج المخصصة */
        .snapchat:hover { box-shadow: 0 0 20px rgba(255, 252, 0, 0.4); border-color: #FFFC00; }
        .snapchat:hover i { color: #FFFC00; }
        
        .whatsapp:hover { box-shadow: 0 0 20px rgba(37, 211, 102, 0.4); border-color: #25D366; }
        .whatsapp:hover i { color: #25D366; }

        .instagram:hover { box-shadow: 0 0 20px rgba(228, 64, 95, 0.4); border-color: #E4405F; }
        .instagram:hover i { color: #E4405F; }

        .footer {
            margin-top: 40px;
            font-size: 0.85rem;
            color: #64748b;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-wrapper">
        <img src="https://ui-avatars.com/api/?name=Abdullah+AlZubaidi&background=38bdf8&color=fff&size=128" alt="عبدالله الزبيدي" class="profile-img">
    </div>

    <h1>عبدالله الزبيدي <i class="fas fa-check-circle verified-badge"></i></h1>
    <p class="bio">رائد أعمال | خبير في تطوير العلامات التجارية</p>

    <div class="links-gap">
        <a href="https://snapchat.com/t/chZXXEq0" class="social-item snapchat">
            <i class="fab fa-snapchat"></i>
            <span>سناب شات الرسمي</span>
            <i class="fas fa-chevron-left fa-xs"></i>
        </a>

        <a href="https://wa.me/966500000000" class="social-item whatsapp">
            <i class="fab fa-whatsapp"></i>
            <span>واتساب الأعمال</span>
            <i class="fas fa-chevron-left fa-xs"></i>
        </a>

        <a href="https://instagram.com/USERNAME" class="social-item instagram">
            <i class="fab fa-instagram"></i>
            <span>انستقرام</span>
            <i class="fas fa-chevron-left fa-xs"></i>
        </a>

        <a href="mailto:info@example.com" class="social-item">
            <i class="fas fa-envelope"></i>
            <span>البريد الإلكتروني</span>
            <i class="fas fa-chevron-left fa-xs"></i>
        </a>
    </div>

    <div class="footer">
        &copy; <?php echo date("Y"); ?> عبدالله الزبيدي
    </div>
</div>

</body>
</html>
