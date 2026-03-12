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
        :root { 
            --main: #e11d48; 
            --bg-soft: #1a2332; /* لون مريح للعين - رمادي مزرق */
            --card-bg: #242f41; /* لون الكروت متناسق مع الخلفية */
            --whatsapp: #25d366; 
            --snapchat: #FFFC00; 
        }
        
        body { 
            margin: 0; 
            font-family: 'Tajawal', sans-serif; 
            background-color: var(--bg-soft);
            padding-top: 180px; 
            overflow-x: hidden;
            color: #e2e8f0;
        }

        /* --- محرك الخلفية الرياضية (ألوان مريحة ومتحركة) --- */
        .bg-vfx-soft {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, #1a2332 0%, #111827 100%);
            overflow: hidden;
        }

        /* تأثير الشبكة الهادئة */
        .bg-vfx-soft::after {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* موجات ضوئية خافتة جداً ومتحركة */
        .soft-glow {
            position: absolute;
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(225, 29, 72, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(100px);
            animation: drift 15s infinite alternate ease-in-out;
        }

        @keyframes drift {
            0% { transform: translate(-10%, -10%); opacity: 0.5; }
            100% { transform: translate(20%, 30%); opacity: 1; }
        }

        /* --- شاشة الدخول (Splash) --- */
        #intro-video-wrap {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #0f172a;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000000;
            transition: 1s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .ball-glow { font-size: 90px; color: #fff; filter: drop-shadow(0 0 20px var(--main)); animation: pulse 2s infinite; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.1); }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }

        /* --- الهيدر الاحترافي --- */
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; }
        .promo-bar { background: rgba(30, 41, 59, 0.9); backdrop-filter: blur(15px); color: #fff; padding: 15px; text-align: center; border-bottom: 2px solid var(--main); }
        .promo-text { font-size: 13px; font-weight: 400; max-width: 900px; margin: auto; line-height: 1.5; }
        
        .social-links { display: flex; justify-content: center; gap: 10px; margin-top: 12px; }
        .social-btn { display: flex; align-items: center; gap: 8px; padding: 8px 18px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 11px; color: #fff; transition: 0.3s; }
        .btn-wa { background: var(--whatsapp); }
        .btn-snap { background: var(--snapchat); color: #000; }
        .btn-x { background: #000; }
        .social-btn:hover { transform: translateY(-2px); filter: brightness(1.1); }

        header { background: #fff; padding: 12px; text-align: center; font-size: 18px; font-weight: 900; color: #1e293b; }

        /* --- القنوات والكروت --- */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; max-width: 1400px; margin: auto; }
        .card { background: var(--card-bg); border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.05); }
        .c-head { padding: 12px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; font-weight: bold; color: #cbd5e1; font-size: 14px; }
        
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 13px; border-radius: 12px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .play-btn:hover { background: #be123c; }

        footer { text-align: center; padding: 50px; }
        .footer-counter { background: rgba(255,255,255,0.03); padding: 20px 45px; border-radius: 20px; display: inline-block; border: 1px solid rgba(255,255,255,0.05); }
        #count-num { font-size: 40px; color: #22c55e; font-weight: 900; text-shadow: 0 0 10px rgba(34, 197, 94, 0.3); }
    </style>
</head>
<body>

    <div class="bg-vfx-soft">
        <div class="soft-glow" style="top: -10%; left: -10%;"></div>
        <div class="soft-glow" style="bottom: -10%; right: -20%; animation-delay: -7s;"></div>
    </div>

    <div id="intro-video-wrap">
        <div style="text-align:center;">
            <i class="fas fa-futbol ball-glow"></i>
            <h1 style="color:#fff; font-size:45px; font-weight:900; margin-top:20px; letter-spacing: -1px;">الخدمة الرقمية</h1>
        </div>
    </div>

    <div class="promo-sticky-container">
        <div class="promo-bar">
            <div class="promo-text">هذه الصفحة مقدمة مجاناً من <strong>متجر الخدمة الرقمية</strong> للاشتراك في الباقة كاملة يدعم جميع القنوات الرياضة ومكتبة الأفلام والمسلسلات تواصل واتساب</div>
            <div class="social-links">
                <a href="https://wa.me/966505571164" class="social-btn btn-wa"><i class="fab fa-whatsapp"></i> واتساب</a>
                <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-snap"><i class="fab fa-snapchat"></i> سناب شات</a>
                <a href="https://x.com/d_service_pro?s=21" class="social-btn btn-x"><i class="fab fa-x-twitter"></i> تابعنا على X</a>
            </div>
        </div>
        <header>📺 بوابة الرياضة - البث المباشر</header>
    </div>

    <div class="grid">
        <?php for($i = 1; $i <= 9; $i++): ?>
        <div class="card">
            <div class="c-head">
                <span>beIN Sport <?php echo $i; ?></span>
                <span style="color: #22c55e;"><i class="fas fa-circle" style="font-size: 8px;"></i> مباشر</span>
            </div>
            <video id="vid<?php echo $i; ?>" playsinline webkit-playsinline controls poster="https://via.placeholder.com/400x225/1a2332/fff?text=beIN+Sports"></video>
            <button class="play-btn" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل البث الآن</button>
        </div>
        <?php endfor; ?>
    </div>

    <footer>
        <div class="footer-counter">
            <p style="margin:0; font-size:12px; color: #94a3b8;">إجمالي زيارات الموقع</p>
            <div id="count-num">0</div>
        </div>
    </footer>

    <script>
    // إخفاء الانترو
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
