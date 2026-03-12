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
            --bg-dark: #0f172a; 
            --whatsapp: #25d366; 
            --snapchat: #FFFC00; 
        }

        /* --- التصميم العام والخلفية المتحركة --- */
        body { 
            margin: 0; 
            font-family: 'Tajawal', sans-serif; 
            background: var(--bg-dark); 
            padding-top: 180px; 
            overflow-x: hidden;
            color: #fff;
        }

        /* ملمس الكاربون فايبر الرياضي */
        .bg-pattern {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -2;
            background-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
            opacity: 0.2;
        }

        /* الأشكال الضوئية المتحركة في الخلفية */
        .animated-glow {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .glow-circle {
            position: absolute;
            background: radial-gradient(circle, var(--main) 0%, transparent 70%);
            filter: blur(80px);
            border-radius: 50%;
            opacity: 0.3;
            animation: moveGlow 20s infinite alternate;
        }

        @keyframes moveGlow {
            0% { transform: translate(-10%, -10%) scale(1); }
            100% { transform: translate(50%, 40%) scale(1.5); }
        }

        /* --- شاشة الدخول السينمائية (Intro) --- */
        #pro-cinematic-intro {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000000;
            transition: all 1.2s cubic-bezier(0.7, 0, 0.3, 1);
        }

        .intro-content { text-align: center; position: relative; z-index: 10; }
        .intro-logo { font-size: 80px; color: #fff; filter: drop-shadow(0 0 20px var(--main)); animation: pulse 2s infinite; }
        .intro-title { font-weight: 900; font-size: 45px; margin-top: 20px; color: #fff; opacity: 0; animation: fadeIn 1s 0.5s forwards; }
        
        .loading-bar-container { width: 250px; height: 3px; background: rgba(255,255,255,0.1); margin: 30px auto; overflow: hidden; border-radius: 5px; }
        .loading-bar-fill { width: 0%; height: 100%; background: var(--main); box-shadow: 0 0 15px var(--main); animation: fill 3s forwards; }

        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
        @keyframes fadeIn { to { opacity: 1; } }
        @keyframes fill { to { width: 100%; } }
        .intro-finish { transform: scale(1.2); opacity: 0; visibility: hidden; }

        /* --- الهيدر والقنوات --- */
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; }
        .promo-bar { background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px); padding: 15px; text-align: center; border-bottom: 3px solid var(--main); }
        header { background: #fff; color: #1e293b; padding: 10px; text-align: center; font-weight: bold; font-size: 18px; }

        .social-links { display: flex; justify-content: center; gap: 10px; margin-top: 10px; }
        .social-btn { padding: 8px 15px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 12px; color: #fff; }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; max-width: 1300px; margin: auto; }
        .card { background: rgba(255, 255, 255, 0.98); color: #000; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .c-head { padding: 10px; background: #f8fafc; display: flex; justify-content: space-between; font-weight: bold; border-bottom: 1px solid #eee; }
        
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; }

        /* --- العداد الزجاجي --- */
        footer { text-align: center; padding: 40px; }
        .glass-counter { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); display: inline-block; padding: 20px 50px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); }
        #count-num { font-size: 40px; color: #22c55e; font-weight: bold; text-shadow: 0 0 10px rgba(34, 197, 94, 0.5); }
    </style>
</head>
<body>

    <div class="bg-pattern"></div>
    <div class="animated-glow">
        <div class="glow-circle" style="width: 400px; height: 400px; top: -100px; left: -100px;"></div>
        <div class="glow-circle" style="width: 500px; height: 500px; bottom: -150px; right: -150px; animation-delay: -5s;"></div>
    </div>

    <div id="pro-cinematic-intro">
        <div class="intro-content">
            <i class="fas fa-play-circle intro-logo"></i>
            <div class="intro-title">الخدمة الرقمية</div>
            <div class="loading-bar-container"><div class="loading-bar-fill"></div></div>
            <div style="color: rgba(255,255,255,0.5); font-size: 10px; letter-spacing: 2px;">تشفير سيرفرات البث الحي...</div>
        </div>
    </div>

    <div class="promo-sticky-container">
        <div class="promo-bar">
            <div style="font-size: 13px;">هذه الصفحة مقدمة مجاناً من <strong>متجر الخدمة الرقمية</strong></div>
            <div class="social-links">
                <a href="https://wa.me/966505571164" class="social-btn" style="background:var(--whatsapp)">واتساب</a>
                <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:var(--snapchat); color:#000;">سناب</a>
                <a href="https://x.com/d_service_pro?s=21" class="social-btn" style="background:#000">تويتر X</a>
            </div>
        </div>
        <header>📺 بوابة الرياضة - بث مباشر</header>
    </div>

    <div class="grid">
        <?php for($i = 1; $i <= 9; $i++): ?>
        <div class="card">
            <div class="c-head"><span>beIN Sport <?php echo $i; ?></span><span style="color:#22c55e">● مباشر</span></div>
            <video id="vid<?php echo $i; ?>" playsinline webkit-playsinline controls poster="https://via.placeholder.com/400x225/111/fff?text=beIN+Sports"></video>
            <button class="play-btn" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل البث</button>
        </div>
        <?php endfor; ?>
    </div>

    <footer>
        <div class="glass-counter">
            <p style="margin:0; font-size:12px; opacity:0.6;">إجمالي زيارات الموقع</p>
            <div id="count-num">0</div>
        </div>
    </footer>

    <script>
    // إخفاء الإنترو بعد 3.5 ثانية
    window.addEventListener('load', () => {
        setTimeout(() => {
            const intro = document.getElementById('pro-cinematic-intro');
            intro.classList.add('intro-finish');
            setTimeout(() => intro.remove(), 1200);
        }, 3500);
    });

    // العداد الذكي
    function updateCounter() {
        let count = localStorage.getItem('visitorCount') || 1452;
        count = parseInt(count) + 1;
        localStorage.setItem('visitorCount', count);
        document.getElementById('count-num').innerText = count.toLocaleString();
    }

    // وظيفة التشغيل بدون تكبير تلقائي
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
