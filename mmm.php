<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محمد المصلحي | البروفايل الرقمي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background: #050505;
            height: 100vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        /* خلفية الجزيئات */
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        /* الكارت الرئيسي */
        .glass-card {
            position: relative;
            z-index: 10;
            width: 400px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(25px);
            text-align: center;
            box-shadow: 0 40px 100px rgba(0,0,0,0.8);
            transition: 0.4s ease-in-out;
        }

        .glass-card:hover {
            border: 1px solid rgba(0, 243, 255, 0.4);
            transform: translateY(-10px);
        }

        /* صورة البروفايل المتوهجة */
        .img-box {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 25px;
        }

        .img-box img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #00f3ff;
            padding: 5px;
        }

        .img-box::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            border-radius: 50%;
            background: #00f3ff;
            filter: blur(20px);
            z-index: -1;
            opacity: 0.5;
            animation: pulse 3s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.2); opacity: 0.2; }
            100% { transform: scale(1); opacity: 0.5; }
        }

        h1 {
            font-size: 28px;
            font-weight: 900;
            background: linear-gradient(to right, #fff, #00f3ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }

        .title {
            font-size: 14px;
            color: #888;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 30px;
        }

        /* أيقونات تواصل اجتماعي فائقة التوهج */
        .social-container {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-btn {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            background: #111;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-size: 22px;
            text-decoration: none;
            border: 1px solid #222;
            transition: 0.3s;
            position: relative;
        }

        /* تأثير التوهج الملون */
        .social-btn:hover {
            color: #fff;
            transform: scale(1.1);
            box-shadow: 0 0 20px var(--clr);
            border-color: var(--clr);
            text-shadow: 0 0 10px var(--clr);
        }

        .facebook { --clr: #1877f2; }
        .twitter { --clr: #1da1f2; }
        .instagram { --clr: #e4405f; }
        .snapchat { --clr: #fffc00; }

        /* زر الإتصال السريع */
        .btn-contact {
            margin-top: 35px;
            display: inline-block;
            padding: 12px 35px;
            background: #00f3ff;
            color: #000;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 10px 20px rgba(0, 243, 255, 0.3);
            transition: 0.3s;
        }

        .btn-contact:hover {
            background: #fff;
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.4);
            transform: scale(1.05);
        }
    </style>
</head>
<body>

    <div id="particles-js"></div>

    <div class="glass-card">
        <div class="img-box">
            <img src="https://ui-avatars.com/api/?name=M+M&background=00f3ff&color=000&size=200" alt="محمد المصلحي">
        </div>

        <h1><?php echo "محمد المصلحي"; ?></h1>
        <p class="title">Creative Developer & Digital Architect</p>

        <div class="social-container">
            <a href="#" class="social-btn facebook" style="--clr: #1877f2;"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-btn twitter" style="--clr: #00f3ff;"><i class="fab fa-x-twitter"></i></a>
            <a href="#" class="social-btn instagram" style="--clr: #e4405f;"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-btn snapchat" style="--clr: #fffc00;"><i class="fab fa-snapchat-ghost"></i></a>
        </div>

        <a href="mailto:info@example.com" class="btn-contact">تواصل معي الآن</a>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 80 },
                "color": { "value": "#00f3ff" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.5 },
                "size": { "value": 3 },
                "line_linked": {
                    "enable": true,
                    "distance": 150,
                    "color": "#00f3ff",
                    "opacity": 0.2,
                    "width": 1
                },
                "move": { "enable": true, "speed": 2 }
            },
            "interactivity": {
                "events": { "onhover": { "enable": true, "mode": "repulse" } }
            }
        });
    </script>
</body>
</html>
