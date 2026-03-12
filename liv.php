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
        :root { --main: #e11d48; --bg: #0f172a; --whatsapp: #25d366; --snapchat: #FFFC00; }
        
        /* --- الخلفية الرياضية المتحركة الاحترافية --- */
        body { 
            margin: 0; 
            font-family: 'Tajawal', sans-serif; 
            background: #0f172a; /* لون داكن أساسي */
            padding-top: 180px; 
            overflow-x: hidden;
            position: relative;
        }

        /* طبقة الأشكال المتحركة */
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(225, 29, 72, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(30, 41, 59, 0.5) 0%, transparent 40%);
            z-index: -2;
        }

        .bg-animate {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            background-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png'); /* ملمس كاربون فايبر رياضي */
            opacity: 0.3;
        }

        /* أشكال هندسية تسبح في الخلفية */
        .shape {
            position: fixed;
            background: linear-gradient(45deg, var(--main), transparent);
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
            opacity: 0.4;
            animation: moveShapes 20s infinite alternate-reverse;
        }
        
        @keyframes moveShapes {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 50px) scale(1.5); }
        }

        /* --- شاشة الدخول الاحترافية --- */
        #pro-cinematic-intro {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000000;
            overflow: hidden;
            transition: all 1.2s cubic-bezier(0.7, 0, 0.3, 1);
        }

        .video-bg-effect {
            position: absolute;
            width: 150%; height: 150%;
            background: radial-gradient(circle at center, rgba(225, 29, 72, 0.2) 0%, transparent 40%),
                        repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 0px, transparent 1px, transparent 100px);
            animation: moveVFX 15s infinite linear;
            filter: blur(50px);
        }

        .content-wrap { position: relative; z-index: 10; text-align: center; }
        .main-logo { font-size: 90px; color: #fff; margin-bottom: 20px; display: inline-block; filter: drop-shadow(0 0 25px var(--main)); animation: logoPulse 2s infinite ease-in-out; }
        .brand-title-ar { font-family: 'Tajawal', sans-serif; font-weight: 900; font-size: clamp(35px, 8vw, 60px); color: #fff; margin: 0; opacity: 0; transform: translateY(30px); animation: textRevealAr 1s 0.5s forwards; }
        .sub-title-ar { font-size: 18px; color: var(--main); font-weight: 700; margin-top: 10px; opacity: 0; animation: fadeInAr 1s 1.2s forwards; }
        .loading-frame { width: 280px; height: 3px; background: rgba(255,255,255,0.1); margin: 40px auto; position: relative; border-radius: 5px; overflow: hidden; }
        .loading-fill { position: absolute; width: 0%; height: 100%; background: linear-gradient(to right, transparent, var(--main)); box-shadow: 0 0 15px var(--main); animation: proLoading 3s cubic-bezier(0.1, 0.5, 0.5, 1) forwards; }

        @keyframes moveVFX { 0% { transform: rotate(0deg) scale(1); } 100% { transform: rotate(360deg) scale(1.2); } }
        @keyframes logoPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.08); } }
        @keyframes textRevealAr { to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInAr { to { opacity: 1; } }
        @keyframes proLoading { 0% { width: 0%; } 100% { width: 100%; } }
        .intro-finish { transform: scale(1.2); filter: blur(20px); opacity: 0; visibility: hidden; }

        /* تعديلات المحتوى ليناسب الخلفية الداكنة */
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 4px 20px rgba(0,0,0,0.5); }
        .promo-bar { background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px); color: #fff; padding: 15px 12px; text-align: center; border-bottom: 3px solid var(--main); }
        header { background: #fff; padding: 12px; text-align: center; font-size: 18px; font-weight: bold; border-bottom: 3px solid var(--main); }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; max-width: 1300px; margin: auto; position: relative; }
        .card { background: rgba(255, 255, 255, 0.95); border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); transition: 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .c-head { padding: 12px; background: #fcfcfc; display: flex; justify-content: space-between; font-weight: bold; }
        
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; }
        
        footer { text-align: center; padding: 40px 10px; margin-top: 50px; color: #fff; }
        #count-num { font-size: 40px; color: #22c55e; font-weight: bold; font-family: sans-serif; text-shadow: 0 0 10px rgba(34, 197, 94, 0.5); }
    </style>
</head>
<body>

<div class="bg-animate"></div>
<div class="shape" style="width: 300px; height: 300px; top: 10%; left: -50px;"></div>
<div class="shape" style="width: 400px; height: 400px; bottom: 10%; right: -100px; animation-delay: -5s;"></div>

<div id="pro-cinematic-intro">
    <div class="video-bg-effect"></div>
    <div class="content-wrap">
        <div class="main-logo"><i class="fas fa-play-circle"></i></div>
        <h1 class="brand-title-ar">الخدمة الرقمية</h1>
        <div class="sub-title-ar">D-SERVICE PRO</div>
        <div class="loading-frame"><div class="loading-fill"></div></div>
        <div class="status-msg">تشفير سيرفرات البث المباشر...</div>
    </div>
</div>

<div class="promo-sticky-container">
    <div class="promo-bar">
        <div class="promo-text">هذه الصفحة مقدمة مجاناً من <strong>متجر الخدمة الرقمية</strong> للاشتراك في الباقة كاملة تواصل واتساب</div>
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa"><i class="fab fa-whatsapp"></i> واتساب</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-snap"><i class="fab fa-snapchat"></i> سناب</a>
            <a href="https://x.com/d_service_pro?s=21" class="social-btn btn-x"><i class="fab fa-x-twitter"></i> تويتر X</a>
        </div>
    </div>
    <header>📺 بوابة الرياضة - مباشر</header>
</div>

<div class="grid">
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card">
        <div class="c-head"><span>beIN Sport <?php echo $i; ?></span><span style="color: #22c55e;">● مباشر</span></div>
        <video id="vid<?php echo $i; ?>" playsinline webkit-playsinline controls poster="https://via.placeholder.com/400x225/111/fff?text=beIN+Sports"></video>
        <button class="play-btn" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل البث</button>
    </div>
    <?php endfor; ?>
</div>

<footer>
    <div style="background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(5px); color:#fff; display:inline-block; padding:20px 40px; border-radius:15px; border: 1px solid rgba(255,255,255,0.1);">
        <p style="margin:0; font-size:12px; opacity:0.6;">زيارات الموقع</p>
        <div id="count-num">1,452</div>
    </div>
</footer>

<script>
window.addEventListener('load', function() {
    setTimeout(function() {
        const intro = document.getElementById('pro-cinematic-intro');
        if(intro) {
            intro.classList.add('intro-finish');
            setTimeout(() => intro.remove(), 1200);
        }
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
    if (Hls.isSupported()) {
        var hls = new Hls();
        hls.loadSource(src);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() { video.play(); });
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = src;
        video.addEventListener('loadedmetadata', function() { video.play(); });
    }
}

window.onload = updateCounter;
</script>
</body>
</html>
