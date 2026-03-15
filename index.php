<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - متجر الخدمة الرقمية</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <style>
        :root { 
            --main: #e11d48; 
            --bg-deep: #061626; 
            --whatsapp: #25d366; 
            --snapchat: #FFFC00; 
            --telegram: #0088cc;
            --purple-grad: linear-gradient(45deg, #7c3aed, #fff); 
            --green-grad: linear-gradient(45deg, #16a34a, #fff);
        }
        
        html { scroll-behavior: smooth; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 175px; overflow-x: hidden; color: #e2e8f0; }

        /* --- شاشة الدخول الاحترافية (VFX Intro) --- */
        #pro-cinematic-intro {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000;
            display: flex; justify-content: center; align-items: center; z-index: 1000000;
            overflow: hidden; transition: all 1.2s cubic-bezier(0.7, 0, 0.3, 1);
        }
        .video-bg-effect {
            position: absolute; width: 150%; height: 150%;
            background: radial-gradient(circle at center, rgba(225, 29, 72, 0.2) 0%, transparent 40%),
                        repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 0px, transparent 1px, transparent 100px);
            animation: moveVFX 15s infinite linear; filter: blur(50px);
        }
        .content-wrap { position: relative; z-index: 10; text-align: center; }
        .main-logo-vfx { font-size: 80px; color: #fff; margin-bottom: 20px; display: inline-block; filter: drop-shadow(0 0 25px var(--main)); animation: logoPulsePro 2s infinite ease-in-out; }
        .brand-title-ar { font-weight: 900; font-size: clamp(30px, 7vw, 55px); color: #fff; margin: 0; opacity: 0; transform: translateY(30px); animation: textRevealAr 1s 0.5s forwards; }
        .loading-frame { width: 250px; height: 3px; background: rgba(255,255,255,0.1); margin: 35px auto; position: relative; border-radius: 5px; overflow: hidden; }
        .loading-fill-vfx { position: absolute; width: 0%; height: 100%; background: linear-gradient(to right, transparent, var(--main), #fff); box-shadow: 0 0 15px var(--main); animation: proLoadingFlow 3s cubic-bezier(0.1, 0.5, 0.5, 1) forwards; }
        @keyframes moveVFX { 0% { transform: rotate(0deg) scale(1); } 100% { transform: rotate(360deg) scale(1.2); } }
        @keyframes logoPulsePro { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        @keyframes textRevealAr { to { opacity: 1; transform: translateY(0); } }
        @keyframes proLoadingFlow { 0% { width: 0%; } 100% { width: 100%; } }
        .intro-finish-vfx { transform: scale(1.2); filter: blur(20px); opacity: 0; visibility: hidden; }

        /* --- الخلفية الاحترافية المتحركة --- */
        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: url('https://www.transparenttextures.com/patterns/black-paper.png'), linear-gradient(135deg, var(--bg-deep) 0%, #0a1f33 100%); }
        .bg-pattern-animated::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.15; animation: movePattern 60s linear infinite; }
        @keyframes movePattern { from { transform: translate(0, 0); } to { transform: translate(-50px, -50px); } }
        .side-glow { position: fixed; top: 0; right: 0; width: 50%; height: 100%; background: radial-gradient(circle at right, rgba(13, 45, 68, 0.6) 0%, transparent 70%); z-index: -1; pointer-events: none; }

        /* --- الهيدر الزجاجي الملتصق --- */
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 10px 40px rgba(0,0,0,0.6); }
        .promo-bar { background: rgba(6, 22, 38, 0.96); backdrop-filter: blur(15px); color: #fff; padding: 10px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .promo-text { font-size: 11px; font-weight: 700; margin-bottom: 8px; opacity: 0.9; } 
        .social-links { display: flex; justify-content: center; gap: 8px; }
        .social-btn { display: flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; transition: 0.3s; } 
        .social-btn:hover { transform: translateY(-3px); }
        .btn-wa { background: var(--whatsapp); } .btn-snap { background: var(--snapchat); color: #000; } .btn-tg { background: var(--telegram); } .btn-x { background: #000; }

        /* شريط التنقل الزجاجي */
        .nav-shortcuts {
            background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(25px);
            display: flex; justify-content: center; gap: 12px; padding: 10px;
            border-bottom: 1px solid rgba(225, 29, 72, 0.3);
        }
        .nav-box-purple, .nav-box-green { padding: 6px 18px; border-radius: 8px; display: flex; align-items: center; gap: 6px; font-weight: 900; font-size: 12px; color: #061626; text-decoration: none; transition: 0.3s; }
        .nav-box-purple { background: var(--purple-grad); box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3); }
        .nav-box-green { background: var(--green-grad); box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3); }

        /* --- شبكة القنوات الزجاجية --- */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 10px 25px; max-width: 1400px; margin: auto; }
        .section-divider {
            grid-column: 1 / -1; padding: 10px 0; font-size: 20px; font-weight: 900; display: flex; align-items: center; gap: 10px; margin: 0; color: #fff;
        }
        .section-divider::after { content: ""; height: 1px; flex: 1; background: linear-gradient(to left, transparent, rgba(255,255,255,0.1)); }

        .card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border-radius: 20px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); transition: 0.3s; }
        .card:hover { transform: translateY(-5px); border-color: rgba(225, 29, 72, 0.4); }
        .c-head { padding: 10px 15px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
        
        /* مستطيل Live Stream المعاد */
        .live-status-box { 
            display: flex; align-items: center; gap: 5px; 
            background: rgba(34, 197, 94, 0.1); 
            padding: 5px 12px; border-radius: 8px; 
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .live-text { font-size: 9px; font-weight: 900; color: #22c55e; }
        .live-dot { width: 6px; height: 6px; background-color: #22c55e; border-radius: 50%; animation: blinkStatus 1s infinite; }
        @keyframes blinkStatus { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(0.8); } }

        .name-box-purple { background: var(--purple-grad); padding: 4px 12px; border-radius: 6px; }
        .name-box-green { background: var(--green-grad); padding: 4px 12px; border-radius: 6px; }
        .channel-name { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 900; color: #061626; }
        .tag-4k { background: #000; color: #fff; font-size: 7px; padding: 1px 4px; border-radius: 3px; }
        
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        .play-btn-premium { width: 92%; margin: 15px auto; display: flex; justify-content: center; align-items: center; gap: 10px; background: rgba(225, 29, 72, 0.05); color: #fff; border: 1.5px solid rgba(225, 29, 72, 0.4); padding: 12px; border-radius: 50px; font-weight: 900; font-size: 14px; cursor: pointer; transition: 0.3s; }
        .play-btn-premium:hover { background: var(--main); box-shadow: 0 0 20px rgba(225, 29, 72, 0.6); }

        footer { text-align: center; padding: 40px; }
        #count-num { font-size: 30px; color: #22c55e; font-weight: 900; }
    </style>
</head>
<body>

<div id="pro-cinematic-intro">
    <div class="video-bg-effect"></div>
    <div class="content-wrap">
        <div class="main-logo-vfx"><i class="fas fa-play-circle"></i></div>
        <h1 class="brand-title-ar">الخدمة الرقمية</h1>
        <div class="loading-frame"><div class="loading-fill-vfx"></div></div>
    </div>
</div>

<div class="bg-pattern-animated"></div>
<div class="side-glow"></div>

<div class="promo-sticky-container">
    <div class="promo-bar">
        <div class="promo-text">هذه الصفحة مقدمة من <strong>متجر الخدمة الرقمية</strong> للاشتراك تواصل معنا:</div>
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa"><i class="fab fa-whatsapp"></i> واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg"><i class="fab fa-telegram-plane"></i> تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-snap"><i class="fab fa-snapchat"></i> سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-x"><i class="fab fa-x-twitter"></i> تويتر</a>
        </div>
    </div>
    <div class="nav-shortcuts">
        <a href="#bein-section" class="nav-box-purple"><i class="fas fa-satellite-dish"></i> beIN Sport</a>
        <a href="#starz-section" class="nav-box-green"><i class="fas fa-play"></i> STARZPLAY</a>
    </div>
</div>

<div class="grid">
    <div id="bein-section" class="section-divider">
        <i class="fas fa-trophy" style="color: #7c3aed;"></i> باقة beIN Sports
    </div>
    
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card">
        <div class="c-head">
            <div class="name-box-purple">
                <span class="channel-name"><span class="tag-4k">4K</span> beIN Sport <?php echo $i; ?></span>
            </div>
            <div class="live-status-box">
                <div class="live-dot"></div>
                <span class="live-text">Live Stream</span>
            </div>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline webkit-playsinline controls poster="https://via.placeholder.com/400x225/061626/fff?text=beIN+Sports"></video>
        <button class="play-btn-premium" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')"> ▶ تشغيل البث الآن </button>
    </div>
    <?php endfor; ?>

    <div id="starz-section" class="section-divider">
        <i class="fas fa-star" style="color: #16a34a;"></i> باقة STARZPLAY
    </div>

    <?php for($i = 10; $i <= 11; $i++): ?>
    <div class="card">
        <div class="c-head">
            <div class="name-box-green">
                <span class="channel-name"><span class="tag-4k">4K</span> STARZPLAY <?php echo ($i-9); ?></span>
            </div>
            <div class="live-status-box">
                <div class="live-dot"></div>
                <span class="live-text">Live Stream</span>
            </div>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline webkit-playsinline controls poster="https://via.placeholder.com/400x225/061626/fff?text=STARZPLAY"></video>
        <button class="play-btn-premium" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')"> ▶ تشغيل البث الآن </button>
    </div>
    <?php endfor; ?>
</div>

<footer>
    <div id="count-num">0</div>
    <p style="opacity: 0.5; font-size: 11px;">متجر الخدمة الرقمية - جميع الحقوق محفوظة</p>
</footer>

<script>
window.addEventListener('load', function() {
    setTimeout(function() {
        const intro = document.getElementById('pro-cinematic-intro');
        if(intro) {
            intro.classList.add('intro-finish-vfx');
            setTimeout(() => intro.remove(), 1200);
            updateCounter();
        }
    }, 3500); 
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
        var hls = new Hls(); hls.loadSource(src); hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => video.play());
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = src; video.play();
    }
}
</script>
</body>
</html>
