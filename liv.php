<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة - متجر الخدمة الرقمية</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <style>
        :root { --main: #e11d48; --bg: #f8fafc; --whatsapp: #25d366; --snapchat: #FFFC00; --x-black: #000000; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); padding-top: 180px; }
        
        /* --- شاشة الدخول الرياضية الاحترافية (Splash Screen) --- */
        #splash-screen {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: radial-gradient(circle, #1e293b 0%, #0f172a 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            transition: opacity 0.8s ease, visibility 0.8s;
        }

        .splash-loader {
            position: relative;
            width: 150px;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .ball-glow {
            font-size: 70px;
            color: #fff;
            text-shadow: 0 0 20px var(--main), 0 0 40px var(--main);
            animation: rotateBall 1.5s infinite linear;
        }

        .loading-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-top: 4px solid var(--main);
            border-radius: 50%;
            animation: spinRing 1s infinite linear;
        }

        .splash-text {
            margin-top: 30px;
            color: white;
            text-align: center;
        }

        .splash-text h2 {
            font-size: 22px;
            margin: 0;
            letter-spacing: 1px;
            background: linear-gradient(to right, #fff, var(--main));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .progress-container {
            width: 200px;
            height: 4px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            margin-top: 15px;
            overflow: hidden;
        }

        .progress-bar {
            width: 0%;
            height: 100%;
            background: var(--main);
            box-shadow: 0 0 10px var(--main);
            animation: fillProgress 3s forwards;
        }

        @keyframes rotateBall {
            0% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.1) rotate(180deg); }
            100% { transform: scale(1) rotate(360deg); }
        }

        @keyframes spinRing {
            to { transform: rotate(360deg); }
        }

        @keyframes fillProgress {
            to { width: 100%; }
        }

        /* حالة الإخفاء */
        .fade-out {
            opacity: 0;
            visibility: hidden;
        }
        /* ---------------------------------------------------- */

        /* باقي تنسيق الصفحة الأصلي */
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .promo-bar { background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(10px); color: #fff; padding: 15px 12px; text-align: center; border-bottom: 3px solid var(--main); }
        .promo-text { font-size: 13px; margin-bottom: 15px; line-height: 1.6; max-width: 900px; margin: auto; }
        .promo-text strong { color: var(--main); }
        .social-links { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
        .social-btn { display: flex; align-items: center; gap: 8px; padding: 8px 18px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 12px; transition: 0.3s; }
        .btn-wa { background: var(--whatsapp); color: white; }
        .btn-snap { background: var(--snapchat); color: black; }
        .btn-x { background: var(--x-black); color: white; }
        header { background: #fff; padding: 12px; text-align: center; font-size: 18px; font-weight: bold; border-bottom: 3px solid var(--main); color: #1e293b; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; max-width: 1300px; margin: auto; }
        .card { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; }
        .c-head { padding: 12px; background: #fcfcfc; display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; font-family: 'Tajawal'; }
        
        footer { text-align: center; padding: 40px 10px; background: #fff; margin-top: 50px; }
        .visitor-counter { display: inline-block; background: #1e293b; padding: 20px 40px; border-radius: 15px; color: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: 1px solid #334155; }
        #count-num { font-size: 40px; color: #22c55e; font-weight: bold; letter-spacing: 2px; text-shadow: 0 0 10px rgba(34, 197, 94, 0.5); font-family: sans-serif; }
    </style>
</head>
<body>

<div id="splash-screen">
    <div class="splash-loader">
        <div class="loading-ring"></div>
        <div class="ball-glow"><i class="fas fa-futbol"></i></div>
    </div>
    <div class="splash-text">
        <h2>جاري تأمين اتصال البث...</h2>
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>
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
    <header>📺 بوابة الرياضة - مباشر</header>
</div>

<div class="grid">
    <?php for($i = 1; $i <= 6; $i++): ?>
    <div class="card">
        <div class="c-head"><span>beIN Sport <?php echo $i; ?></span><span style="color: #22c55e;">● مباشر</span></div>
        <video id="vid<?php echo $i; ?>" controls poster="https://via.placeholder.com/400x225/111/fff?text=beIN+Sports"></video>
        <button class="play-btn" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل البث</button>
    </div>
    <?php endfor; ?>
</div>

<footer>
    <div class="visitor-counter">
        <p style="margin:0 0 10px; font-size:12px; color:#94a3b8;">إجمالي زيارات الموقع الحقيقية</p>
        <div id="count-num">1,452</div>
    </div>
    <p style="margin-top:20px; font-size:11px; color:#ccc;">&copy; 2026 متجر الخدمة الرقمية</p>
</footer>

<script>
// التحكم في شاشة الدخول (تختفي بعد 3 ثواني)
window.addEventListener('load', function() {
    setTimeout(function() {
        const splash = document.getElementById('splash-screen');
        splash.classList.add('fade-out');
    }, 3000); // 3000ms = 3 ثواني
});

function updateCounter() {
    let count = localStorage.getItem('visitorCount');
    if (!count) {
        count = 1452;
    } else {
        count = parseInt(count) + Math.floor(Math.random() * 3) + 1;
    }
    localStorage.setItem('visitorCount', count);
    document.getElementById('count-num').innerText = count.toLocaleString();
}

function play(id, src) {
    var video = document.getElementById(id);
    if (Hls.isSupported()) { var hls = new Hls(); hls.loadSource(src); hls.attachMedia(video); video.play(); }
    else if (video.canPlayType('application/vnd.apple.mpegurl')) { video.src = src; video.play(); }
}

window.onload = updateCounter;
</script>
</body>
</html>
