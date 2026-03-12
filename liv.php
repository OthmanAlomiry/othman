<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - متجر الخدمة الرقمية</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <style>
        :root { --main: #e11d48; --bg-light: #f1f5f9; --whatsapp: #25d366; --snapchat: #FFFC00; }
        
        body { 
            margin: 0; 
            font-family: 'Tajawal', sans-serif; 
            background-color: var(--bg-light);
            padding-top: 180px; 
            overflow-x: hidden;
            color: #1e293b;
        }

        /* --- محرك الخلفية الرياضية المتحركة (نسخة فاتحة واحترافية) --- */
        .bg-vfx-light {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            overflow: hidden;
        }

        /* تأثير الشبكة البيضاء الناعمة */
        .bg-vfx-light::after {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image: 
                linear-gradient(rgba(225, 29, 72, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(225, 29, 72, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        /* بقع ضوئية متحركة (بدون بريق) */
        .light-glow {
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(225, 29, 72, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(80px);
            animation: moveLight 12s infinite alternate ease-in-out;
        }

        @keyframes moveLight {
            0% { transform: translate(-10%, -10%); }
            100% { transform: translate(30%, 20%); }
        }

        /* --- شاشة الدخول السينمائية --- */
        #intro-video-wrap {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #0f172a;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000000;
            transition: 1s ease-in-out;
        }
        .ball-glow { font-size: 90px; color: #fff; filter: drop-shadow(0 0 20px var(--main)); animation: logoPulse 2s infinite; }
        .intro-hide { opacity: 0; visibility: hidden; transform: translateY(-100%); }
        @keyframes logoPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }

        /* --- الهيدر والبار العلوي --- */
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .promo-bar { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); color: #1e293b; padding: 15px; text-align: center; border-bottom: 3px solid var(--main); }
        .promo-text { font-size: 13px; font-weight: 700; max-width: 900px; margin: auto; }
        .social-links { display: flex; justify-content: center; gap: 10px; margin-top: 12px; }
        .social-btn { display: flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 11px; color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .btn-wa { background: var(--whatsapp); }
        .btn-snap { background: var(--snapchat); color: #000; }
        .btn-x { background: #000; }
        header { background: #fff; padding: 12px; text-align: center; font-size: 18px; font-weight: 900; color: #0f172a; }

        /* --- الشبكة والكروت --- */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; max-width: 1400px; margin: auto; }
        .card { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .c-head { padding: 12px; background: #f8fafc; display: flex; justify-content: space-between; font-weight: bold; color: #334155; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; }

        footer { text-align: center; padding: 50px; }
        .footer-counter { background: #fff; padding: 20px 40px; border-radius: 20px; display: inline-block; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        #count-num { font-size: 40px; color: #16a34a; font-weight: 900; }
    </style>
</head>
<body>

    <div class="bg-vfx-light">
        <div class="light-glow" style="top: 0; left: 0;"></div>
        <div class="light-glow" style="bottom: 0; right: 0; animation-delay: -6s;"></div>
    </div>

    <div id="intro-video-wrap">
        <div style="text-align:center;">
            <i class="fas fa-futbol ball-glow"></i>
            <h1 style="color:#fff; font-size:45px; font-weight:900; margin-top:20px;">الخدمة الرقمية</h1>
        </div>
    </div>

    <div class="promo-sticky-container">
        <div class="promo-bar">
            <div class="promo-text">هذه الصفحة مقدمة مجاناً من <strong>متجر الخدمة الرقمية</strong> للاشتراك في الباقة كاملة يدعم جميع القنوات الرياضة ومكتبة الأفلام والمسلسلات تواصل واتساب</div>
            <div class="social-links">
                <a href="https://wa.me/966505571164" class="social-btn btn-wa"><i class="fab fa-whatsapp"></i> تواصل واتساب</a>
                <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-snap"><i class="fab fa-snapchat"></i> سناب شات</a>
                <a href="https://x.com/d_service_pro?s=21" class="social-btn btn-x"><i class="fab fa-x-twitter"></i> تابعنا على X</a>
            </div>
        </div>
        <header>📺 بوابة الرياضة - مباريات اليوم المباشرة</header>
    </div>

    <div class="grid">
        <?php for($i = 1; $i <= 9; $i++): ?>
        <div class="card">
            <div class="c-head"><span>beIN Sport <?php echo $i; ?></span><span style="color: #16a34a;">● مباشر</span></div>
            <video id="vid<?php echo $i; ?>" playsinline webkit-playsinline controls poster="https://via.placeholder.com/400x225/f8fafc/1e293b?text=beIN+Sports"></video>
            <button class="play-btn" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل البث الآن</button>
        </div>
        <?php endfor; ?>
    </div>

    <footer>
        <div class="footer-counter">
            <p style="margin:0; font-size:12px; color: #64748b;">إجمالي زيارات الموقع الحقيقية</p>
            <div id="count-num">0</div>
        </div>
    </footer>

    <script>
    // التحكم في الانترو
    window.addEventListener('load', () => {
        setTimeout(() => {
            const intro = document.getElementById('intro-video-wrap');
            if(intro) {
                intro.classList.add('intro-hide');
                setTimeout(() => intro.remove(), 1000);
            }
        }, 3000);
    });

    // العداد
    function updateCounter() {
        let count = localStorage.getItem('vCount') || 1452;
        count = parseInt(count) + 1;
        localStorage.setItem('vCount', count);
        document.getElementById('count-num').innerText = count.toLocaleString();
    }

    // تشغيل الفيديو
    function play(id, src) {
        var video = document.getElementById(id);
        if (Hls.isSupported()) {
            var hls = new Hls();
            hls.loadSource(src);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, () => video.play());
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = src;
            video.play();
        }
    }

    window.onload = updateCounter;
    </script>
</body>
</html>
