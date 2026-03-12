<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة - متجر الخدمة الرقمية</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <style>
        :root { --main: #e11d48; --bg: #0f172a; --whatsapp: #25d366; --snapchat: #FFFC00; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: #f1f5f9; padding-top: 180px; overflow-x: hidden; }

        /* --- شاشة الدخول الاحترافية: فيديو واقعي عربي --- */
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

        /* تأثير الدخان والضوء المتحرك (VFX) */
        .video-bg-effect {
            position: absolute;
            width: 150%; height: 150%;
            background: radial-gradient(circle at center, rgba(225, 29, 72, 0.2) 0%, transparent 40%),
                        repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 0px, transparent 1px, transparent 100px);
            animation: moveVFX 15s infinite linear;
            filter: blur(50px);
        }

        .content-wrap {
            position: relative;
            z-index: 10;
            text-align: center;
        }

        /* شعار متوهج */
        .main-logo {
            font-size: 90px;
            color: #fff;
            margin-bottom: 20px;
            display: inline-block;
            filter: drop-shadow(0 0 25px var(--main));
            animation: logoPulse 2s infinite ease-in-out;
        }

        /* النص العربي السينمائي */
        .brand-title-ar {
            font-family: 'Tajawal', sans-serif;
            font-weight: 900;
            font-size: clamp(35px, 8vw, 60px);
            color: #fff;
            margin: 0;
            letter-spacing: -1px;
            opacity: 0;
            transform: translateY(30px);
            animation: textRevealAr 1s 0.5s forwards;
            text-shadow: 0 5px 15px rgba(0,0,0,0.5);
        }

        .sub-title-ar {
            font-size: 18px;
            color: var(--main);
            font-weight: 700;
            margin-top: 10px;
            letter-spacing: 5px;
            opacity: 0;
            animation: fadeInAr 1s 1.2s forwards;
        }

        /* شريط التحميل الرقمي */
        .loading-frame {
            width: 280px;
            height: 3px;
            background: rgba(255,255,255,0.1);
            margin: 40px auto;
            position: relative;
            border-radius: 5px;
            overflow: hidden;
        }

        .loading-fill {
            position: absolute;
            width: 0%; height: 100%;
            background: linear-gradient(to right, transparent, var(--main));
            box-shadow: 0 0 15px var(--main);
            animation: proLoading 3s cubic-bezier(0.1, 0.5, 0.5, 1) forwards;
        }

        .status-msg {
            font-size: 11px;
            color: rgba(255,255,255,0.4);
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* --- الحركات (Animations) --- */
        @keyframes moveVFX {
            0% { transform: rotate(0deg) scale(1); }
            100% { transform: rotate(360deg) scale(1.2); }
        }

        @keyframes logoPulse {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 20px var(--main)); }
            50% { transform: scale(1.08); filter: drop-shadow(0 0 45px var(--main)); }
        }

        @keyframes textRevealAr {
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInAr {
            to { opacity: 1; letter-spacing: 2px; }
        }

        @keyframes proLoading {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        /* تأثير الخروج السينمائي */
        .intro-finish {
            transform: scale(1.2);
            filter: blur(20px);
            opacity: 0;
            visibility: hidden;
        }

        /* ------------------------------------------- */

        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .promo-bar { background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(10px); color: #fff; padding: 15px 12px; text-align: center; border-bottom: 3px solid var(--main); }
        .promo-text { font-size: 13px; line-height: 1.6; max-width: 900px; margin: auto; }
        .social-links { display: flex; justify-content: center; gap: 10px; margin-top: 15px; }
        .social-btn { display: flex; align-items: center; gap: 8px; padding: 8px 18px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 12px; transition: 0.3s; }
        .btn-wa { background: var(--whatsapp); color: white; }
        .btn-snap { background: var(--snapchat); color: black; }
        .btn-x { background: #000; color: white; }
        header { background: #fff; padding: 12px; text-align: center; font-size: 18px; font-weight: bold; border-bottom: 3px solid var(--main); }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; max-width: 1300px; margin: auto; }
        .card { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; }
        .c-head { padding: 12px; background: #fcfcfc; display: flex; justify-content: space-between; font-weight: bold; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; }
        
        footer { text-align: center; padding: 40px 10px; background: #fff; margin-top: 50px; }
        #count-num { font-size: 40px; color: #22c55e; font-weight: bold; font-family: sans-serif; }
    </style>
</head>
<body>

<div id="pro-cinematic-intro">
    <div class="video-bg-effect"></div>
    <div class="content-wrap">
        <div class="main-logo">
            <i class="fas fa-play-circle"></i>
        </div>
        <h1 class="brand-title-ar">الخدمة الرقمية</h1>
        <div class="sub-title-ar">D-SERVICE PRO</div>
        
        <div class="loading-frame">
            <div class="loading-fill"></div>
        </div>
        
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
        <p style="margin:0; font-size:12px; opacity:0.6;">زيارات الموقع</p>
        <div id="count-num">1,452</div>
    </div>
</footer>

<script>
// التحكم في شاشة الدخول (3 ثواني)
window.addEventListener('load', function() {
    setTimeout(function() {
        const intro = document.getElementById('pro-cinematic-intro');
        intro.classList.add('intro-finish');
        setTimeout(() => intro.remove(), 1200);
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
}

window.onload = updateCounter;
</script>
</body>
</html>
