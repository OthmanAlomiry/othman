<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة - متجر الخدمة الرقمية</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&family=Orbitron:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <style>
        :root { --main: #e11d48; --bg: #0f172a; --whatsapp: #25d366; --snapchat: #FFFC00; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: #f1f5f9; padding-top: 180px; }

        /* --- شاشة الدخول السينمائية (Cinematic Video Intro) --- */
        #cinematic-intro {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 100000;
            overflow: hidden;
            transition: all 1s cubic-bezier(0.9, 0, 0.1, 1);
        }

        /* خلفية ضوئية متحركة تشبه الفيديو */
        #cinematic-intro::before {
            content: '';
            position: absolute;
            width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(225, 29, 72, 0.15) 0%, transparent 50%);
            animation: moveLight 8s infinite alternate;
        }

        .intro-content {
            position: relative;
            text-align: center;
            z-index: 2;
        }

        .logo-box {
            position: relative;
            animation: logoEntrance 1.5s ease-out forwards;
        }

        .logo-box i {
            font-size: 100px;
            color: #fff;
            filter: drop-shadow(0 0 30px var(--main));
            animation: pulseGlow 2s infinite ease-in-out;
        }

        .brand-name {
            margin-top: 25px;
            font-family: 'Orbitron', sans-serif;
            font-size: 35px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 15px; /* حروف متباعدة جداً كالأفلام */
            opacity: 0;
            animation: textReveal 2s 0.5s forwards;
        }

        .loading-line-container {
            width: 300px;
            height: 2px;
            background: rgba(255,255,255,0.1);
            margin: 30px auto;
            position: relative;
            overflow: hidden;
        }

        .loading-line {
            position: absolute;
            width: 0%; height: 100%;
            background: var(--main);
            box-shadow: 0 0 15px var(--main);
            animation: lineFill 3s ease-in-out forwards;
        }

        .scanning-text {
            color: rgba(255,255,255,0.5);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 10px;
        }

        /* حركات الأنيميشن */
        @keyframes moveLight {
            from { transform: translate(-20%, -20%); }
            to { transform: translate(10%, 10%); }
        }

        @keyframes logoEntrance {
            0% { transform: scale(0.5); opacity: 0; filter: blur(20px); }
            100% { transform: scale(1); opacity: 1; filter: blur(0); }
        }

        @keyframes pulseGlow {
            0%, 100% { filter: drop-shadow(0 0 20px var(--main)); transform: scale(1); }
            50% { filter: drop-shadow(0 0 50px var(--main)); transform: scale(1.05); }
        }

        @keyframes textReveal {
            to { opacity: 1; letter-spacing: 5px; }
        }

        @keyframes lineFill {
            to { width: 100%; }
        }

        /* خروج السينما */
        .intro-fade-out {
            transform: scale(1.5);
            opacity: 0;
            visibility: hidden;
        }

        /* ------------------------------------------------ */

        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .promo-bar { background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(10px); color: #fff; padding: 15px 12px; text-align: center; border-bottom: 3px solid var(--main); }
        .promo-text { font-size: 13px; margin-bottom: 15px; line-height: 1.6; max-width: 900px; margin: auto; }
        .social-links { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
        .social-btn { display: flex; align-items: center; gap: 8px; padding: 8px 18px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 12px; transition: 0.3s; }
        .btn-wa { background: var(--whatsapp); color: white; }
        .btn-snap { background: var(--snapchat); color: black; }
        .btn-x { background: #000; color: white; }
        header { background: #fff; padding: 12px; text-align: center; font-size: 18px; font-weight: bold; border-bottom: 3px solid var(--main); color: #1e293b; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; max-width: 1300px; margin: auto; }
        .card { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; }
        .c-head { padding: 12px; background: #fcfcfc; display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; }
        
        footer { text-align: center; padding: 40px 10px; background: #fff; margin-top: 50px; }
        #count-num { font-size: 40px; color: #22c55e; font-weight: bold; font-family: sans-serif; }
    </style>
</head>
<body>

<div id="cinematic-intro">
    <div class="intro-content">
        <div class="logo-box">
            <i class="fas fa-futbol"></i>
        </div>
        <div class="brand-name">D-SERVICE PRO</div>
        <div class="loading-line-container">
            <div class="loading-line"></div>
        </div>
        <div class="scanning-text">Encrypting Live Stream Servers...</div>
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
    <div style="background: #1e293b; color:#fff; display:inline-block; padding:20px 40px; border-radius:15px;">
        <p style="margin:0; font-size:12px; opacity:0.6;">إجمالي زيارات الموقع</p>
        <div id="count-num">1,452</div>
    </div>
</footer>

<script>
// التحكم في الانترو السينمائي (3 ثواني)
window.addEventListener('load', function() {
    setTimeout(function() {
        const intro = document.getElementById('cinematic-intro');
        intro.classList.add('intro-fade-out');
        // إزالة العنصر تماماً من المتصفح بعد انتهاء الحركة لتسريع الموقع
        setTimeout(() => intro.remove(), 1000);
    }, 3500); 
});

function updateCounter() {
    let count = localStorage.getItem('visitorCount') || 1452;
    count = parseInt(count) + 1;
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
