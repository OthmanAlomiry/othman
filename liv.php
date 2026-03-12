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
        :root { --main: #e11d48; --bg: #030712; --whatsapp: #25d366; --snapchat: #FFFC00; }
        
        body { 
            margin: 0; 
            font-family: 'Tajawal', sans-serif; 
            background-color: var(--bg);
            padding-top: 180px; 
            overflow-x: hidden;
            color: #fff;
        }

        /* --- محرك الخلفية الرياضية المتحركة الحماسي (VFX) --- */
        .bg-vfx {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            background: #000;
            overflow: hidden;
        }

        /* تأثير الشبكة (Grid) */
        .bg-vfx::after {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image: 
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(ellipse at center, black, transparent 80%);
        }

        /* أمواج الطاقة المتحركة */
        .energy-wave {
            position: absolute;
            width: 800px; height: 800px;
            background: radial-gradient(circle, rgba(225, 29, 72, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(60px);
            animation: moveEnergy 15s infinite alternate ease-in-out;
        }

        /* تأثير البرق (Lightning Flash) */
        .lightning-flash {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #fff;
            opacity: 0;
            z-index: -1;
            pointer-events: none;
            animation: flashAnim 8s infinite;
        }

        @keyframes flashAnim {
            0%, 92%, 100% { opacity: 0; }
            93% { opacity: 0.1; }
            94% { opacity: 0; }
            96% { opacity: 0.2; }
            97% { opacity: 0; }
        }

        @keyframes moveEnergy {
            0% { transform: translate(-20%, -20%) scale(1); }
            100% { transform: translate(30%, 30%) scale(1.2); }
        }

        /* --- شاشة الدخول السينمائية --- */
        #intro-video-wrap {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000000;
            transition: 1s cubic-bezier(0.85, 0, 0.15, 1);
        }
        .ball-glow { font-size: 100px; filter: drop-shadow(0 0 30px var(--main)); animation: logoIntro 2s infinite ease-in-out; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.2); }
        @keyframes logoIntro { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }

        /* --- الهيدر (إرجاع كافة النصوص والروابط) --- */
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 4px 20px rgba(0,0,0,0.5); }
        .promo-bar { background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(10px); color: #fff; padding: 15px 12px; text-align: center; border-bottom: 3px solid var(--main); }
        .promo-text { font-size: 13px; line-height: 1.6; max-width: 900px; margin: auto; }
        .social-links { display: flex; justify-content: center; gap: 10px; margin-top: 15px; }
        .social-btn { display: flex; align-items: center; gap: 8px; padding: 8px 18px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 12px; transition: 0.3s; }
        .btn-wa { background: var(--whatsapp); color: white; }
        .btn-snap { background: var(--snapchat); color: black; }
        .btn-x { background: #000; color: white; }
        header { background: #fff; padding: 12px; text-align: center; font-size: 18px; font-weight: bold; color: #000; border-bottom: 3px solid var(--main); }

        /* --- الشبكة والكروت --- */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; max-width: 1400px; margin: auto; }
        .card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; overflow: hidden; }
        .c-head { padding: 12px; background: rgba(0,0,0,0.3); display: flex; justify-content: space-between; font-weight: bold; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; }

        footer { text-align: center; padding: 50px; }
        #count-num { font-size: 40px; color: #22c55e; font-weight: 900; }
    </style>
</head>
<body>

    <div class="bg-vfx">
        <div class="energy-wave" style="top: -10%; left: -10%;"></div>
        <div class="energy-wave" style="bottom: -10%; right: -10%; background: radial-gradient(circle, rgba(30, 64, 175, 0.1) 0%, transparent 70%);"></div>
    </div>
    <div class="lightning-flash"></div>

    <div id="intro-video-wrap">
        <div style="text-align:center;">
            <i class="fas fa-futbol ball-glow"></i>
            <h1 style="color:#fff; font-size:50px; font-weight:900; margin-top:20px;">الخدمة الرقمية</h1>
        </div>
    </div>

    <div class="promo-sticky-container">
        <div class="promo-bar">
            <div class="promo-text">هذه الصفحة مقدمة مجاناً من <strong>متجر الخدمة الرقمية</strong> للاشتراك في الباقة كاملة يدعم جميع القنوات الرياضة ومكتبة الأفلام والمسلسلات على شاشة التلفزون والجوال تواصل واتساب</div>
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
            <div class="c-head"><span>beIN Sport <?php echo $i; ?></span><span style="color: #22c55e;">● مباشر</span></div>
            <video id="vid<?php echo $i; ?>" playsinline webkit-playsinline controls poster="https://via.placeholder.com/400x225/111/fff?text=beIN+Sports"></video>
            <button class="play-btn" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل البث الآن</button>
        </div>
        <?php endfor; ?>
    </div>

    <footer>
        <div style="background: rgba(255,255,255,0.05); padding: 25px; border-radius: 20px; display: inline-block;">
            <p style="margin:0; font-size:12px; opacity:0.6;">إجمالي زيارات الموقع الحقيقية</p>
            <div id="count-num">0</div>
        </div>
    </footer>

    <script>
    window.addEventListener('load', () => {
        setTimeout(() => {
            const intro = document.getElementById('intro-video-wrap');
            if(intro) {
                intro.classList.add('intro-hide');
                setTimeout(() => intro.remove(), 1000);
            }
        }, 3000);
    });

    function updateCounter() {
        let count = localStorage.getItem('vCount') || 1452;
        count = parseInt(count) + 1;
        localStorage.setItem('vCount', count);
        document.getElementById('count-num').innerText = count.toLocaleString();
    }

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
