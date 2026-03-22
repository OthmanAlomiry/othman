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
            --main-bg: #0f172a; /* لون ليلي فاخر */
            --card-bg: rgba(255, 255, 255, 0.05);
            --text-color: #f8fafc;
            --accent-color: #38bdf8; /* لون سماوي احترافي */
        }

        body {
            font-family: 'Almarai', sans-serif;
            background-color: var(--main-bg);
            background-image: radial-gradient(circle at top right, #1e293b, #0f172a);
            color: var(--text-color);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 480px;
            text-align: center;
        }

        /* صورة الملف الشخصي */
        .profile-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }

        .profile-img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 3px solid var(--accent-color);
            padding: 5px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .profile-img:hover {
            transform: rotate(360px) scale(1.1);
        }

        h1 {
            font-family: 'Tajawal', sans-serif;
            font-size: 1.8rem;
            margin: 10px 0;
            letter-spacing: 1px;
        }

        .bio {
            font-size: 0.95rem;
            color: #94a3b8;
            margin-bottom: 35px;
            line-height: 1.6;
        }

        /* تنسيق الروابط */
        .links-gap {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .social-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            text-decoration: none;
            color: var(--text-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .social-item:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(-8px);
            border-color: var(--accent-color);
        }

        .social-item i {
            font-size: 1.5rem;
            width: 40px;
            text-align: center;
        }

        .social-item span {
            flex-grow: 1;
            font-weight: 500;
            font-size: 1rem;
        }

        /* ألوان مخصصة عند التحويم */
        .whatsapp:hover i { color: #25D366; }
        .snapchat:hover i { color: #FFFC00; }
        .instagram:hover i { color: #E4405F; }
        .x-twitter:hover i { color: #ffffff; }
        .linkedin:hover i { color: #0A66C2; }

        .footer {
            margin-top: 40px;
            font-size: 0.8rem;
            color: #64748b;
        }

        /* علامة التوثيق */
        .verified-badge {
            color: var(--accent-color);
            font-size: 1.2rem;
            margin-right: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-wrapper">
        <img src="https://ui-avatars.com/api/?name=Abdullah+AlZubaidi&background=38bdf8&color=fff&size=128" alt="عبدالله الزبيدي" class="profile-img">
    </div>

    <h1>عبدالله الزبيدي <i class="fas fa-check-circle verified-badge"></i></h1>
    <p class="bio">رائد أعمال | خبير في تطوير العلامات التجارية <br> مرحباً بك في عالمي الرقمي</p>

    <div class="links-gap">
        <a href="https://wa.me/966500000000" class="social-item whatsapp">
            <i class="fab fa-whatsapp"></i>
            <span>تواصل عبر واتساب</span>
            <i class="fas fa-external-link-alt fa-xs"></i>
        </a>

        <a href="https://snapchat.com/add/USERNAME" class="social-item snapchat">
            <i class="fab fa-snapchat"></i>
            <span>سناب شات</span>
            <i class="fas fa-external-link-alt fa-xs"></i>
        </a>

        <a href="https://instagram.com/USERNAME" class="social-item instagram">
            <i class="fab fa-instagram"></i>
            <span>انستقرام</span>
            <i class="fas fa-external-link-alt fa-xs"></i>
        </a>

        <a href="https://x.com/USERNAME" class="social-item x-twitter">
            <i class="fab fa-x-twitter"></i>
            <span>منصة X</span>
            <i class="fas fa-external-link-alt fa-xs"></i>
        </a>

        <a href="https://linkedin.com/in/USERNAME" class="social-item linkedin">
            <i class="fab fa-linkedin-in"></i>
            <span>لينكد إن</span>
            <i class="fas fa-external-link-alt fa-xs"></i>
        </a>

        <a href="mailto:info@example.com" class="social-item">
            <i class="fas fa-envelope"></i>
            <span>البريد الإلكتروني</span>
            <i class="fas fa-external-link-alt fa-xs"></i>
        </a>
    </div>

    <div class="footer">
        &copy; <?php echo date("Y"); ?> عبدالله الزبيدي. جميع الحقوق محفوظة.
    </div>
</div>

</body>
</html>
