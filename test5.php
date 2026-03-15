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
            --main: #e11d48; --bg-deep: #061626; --whatsapp: #25d366; --snapchat: #FFFC00; --telegram: #0088cc;
            --purple-grad: linear-gradient(45deg, #7c3aed, #fff); --green-grad: linear-gradient(45deg, #16a34a, #fff);
        }
        html { scroll-behavior: smooth; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 175px; overflow-x: hidden; color: #e2e8f0; }

        /* --- شاشة الدخول --- */
        #pro-cinematic-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; display: flex; justify-content: center; align-items: center; z-index: 1000000; overflow: hidden; transition: all 1.2s cubic-bezier(0.7, 0, 0.3, 1); }
        .video-bg-effect { position: absolute; width: 150%; height: 150%; background: radial-gradient(circle at center, rgba(225, 29, 72, 0.2) 0%, transparent 40%), repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 0px, transparent 1px, transparent 100px); animation: moveVFX 15s infinite linear; filter: blur(50px); }
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

        /* --- الهيدر والقنوات --- */
        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: url('https://www.transparenttextures.com/patterns/black-paper.png'), linear-gradient(135deg, var(--bg-deep) 0%, #0a1f33 100%); }
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 10px 40px rgba(0,0,0,0.6); }
        .promo-bar { background: rgba(6, 22, 38, 0.96); backdrop-filter: blur(15px); color: #fff; padding: 10px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .social-links { display: flex; justify-content: center; gap: 8px; }
        .social-btn { display: flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; } 
        .btn-wa { background: var(--whatsapp); } .btn-snap { background: var(--snapchat); color: #000; } .btn-tg { background: var(--telegram); } .btn-x { background: #000; }
        .nav-shortcuts { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(25px); display: flex; justify-content: center; gap: 12px; padding: 10px; border-bottom: 1px solid rgba(225, 29, 72, 0.3); }
        .nav-box-purple, .nav-box-green { padding: 6px 18px; border-radius: 8px; display: flex; align-items: center; gap: 6px; font-weight: 900; font-size: 12px; color: #061626; text-decoration: none; }
        .nav-box-purple { background: var(--purple-grad); } .nav-box-green { background: var(--green-grad); }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 10px 25px; max-width: 1400px; margin: auto; }
        .section-divider { grid-column: 1 / -1; padding: 10px 0; font-size: 20px; font-weight: 900; display: flex; align-items: center; gap: 10px; color: #fff; }
        .card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border-radius: 20px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); }
        .c-head { padding: 10px 15px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
        .live-status-box { display: flex; align-items: center; gap: 5px; background: rgba(34, 197, 94, 0.1); padding: 5px 12px; border-radius: 8px; border: 1px solid rgba(34, 197, 94, 0.3); }
        .live-dot { width: 6px; height: 6px; background-color: #22c55e; border-radius: 50%; animation: blinkStatus 1s infinite; }
        @keyframes blinkStatus { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        .play-btn-premium { width: 92%; margin: 18px auto; display: flex; justify-content: center; align-items: center; gap: 12px; background: linear-gradient(135deg, rgba(225, 29, 72, 0.2) 0%, rgba(225, 29, 72, 0.1) 100%); backdrop-filter: blur(10px); color: #fff; border: 1px solid rgba(225, 29, 72, 0.5); padding: 14px; border-radius: 50px; font-weight: 900; font-size: 15px; cursor: pointer; transition: 0.4s; }
        .play-btn-premium:hover { background: var(--main); }
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

<div class="promo-sticky-container">
    <div class="promo-bar">
        <div class="promo-text" style="font-size: 11px; margin-bottom: 8px;">هذه الصفحة مقدمة من <strong>متجر الخدمة الرقمية</strong></div>
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
    <div id="bein-section" class="section-divider">باقة beIN Sports</div>
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card">
        <div class="c-head">
            <div style="background:var(--purple-grad); padding:4px 12px; border-radius:6px; color:#000; font-weight:900; font-size:12px;">beIN Sport <?php echo $i; ?></div>
            <div class="live-status-box"><div class="live-dot"></div><span style="font-size:9px;color:#22c55e;font-weight:900;">LIVE STREAM</span></div>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline controls poster="https://via.placeholder.com/400x225/061626/fff?text=beIN+Sports"></video>
        <button class="play-btn-premium" onclick="smartPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', 'bs<?php echo $i; ?>.php')"> 
            <i class="fas fa-play"></i> <span>بدء البث المباشر</span>
        </button>
    </div>
    <?php endfor; ?>

    <div id="starz-section" class="section-divider">باقة STARZPLAY</div>
    <?php for($i = 10; $i <= 11; $i++): ?>
    <div class="card">
        <div class="c-head">
            <div style="background:var(--green-grad); padding:4px 12px; border-radius:6px; color:#000; font-weight:900; font-size:12px;">STARZPLAY <?php echo ($i-9); ?></div>
            <div class="live-status-box"><div class="live-dot"></div><span style="font-size:9px;color:#22c55e;font-weight:900;">LIVE STREAM</span></div>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline controls poster="https://via.placeholder.com/400x225/061626/fff?text=STARZPLAY"></video>
        <button class="play-btn-premium" onclick="smartPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', 'bs<?php echo $i; ?>.php')"> 
            <i class="fas fa-play"></i> <span>بدء البث المباشر</span>
        </button>
    </div>
    <?php endfor; ?>
</div>

<script>
window.addEventListener('load', function() {
    setTimeout(function() {
        const intro = document.getElementById('pro-cinematic-intro');
        if(intro) { intro.classList.add('intro-finish-vfx'); setTimeout(() => intro.remove(), 1200); }
    }, 3500); 
});

async function smartPlay(videoId, primary, backup) {
    const video = document.getElementById(videoId);
    const btn = event.currentTarget;
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-sync fa-spin"></i> <span>جاري تهيئة البث...</span>';
    btn.style.pointerEvents = 'none';

    if (video.hls) { video.hls.destroy(); delete video.hls; }

    try {
        const response = await fetch(primary);
        const url = await response.text();
        const finalUrl = url.trim();

        if (finalUrl && finalUrl.startsWith('http')) {
            runHls(video, finalUrl);
            let playCheck = false;
            video.onplaying = () => { 
                playCheck = true; 
                btn.innerHTML = '<i class="fas fa-check-circle"></i> <span>بث مباشر يعمل</span>';
                setTimeout(() => { btn.innerHTML = originalText; btn.style.pointerEvents = 'auto'; }, 2000);
            };
            setTimeout(() => { if (!playCheck) triggerBackup(video, backup, btn, originalText); }, 10000);
        } else { triggerBackup(video, backup, btn, originalText); }
    } catch (e) { triggerBackup(video, backup, btn, originalText); }
}

function triggerBackup(video, backupFile, btn, originalText) {
    btn.innerHTML = '<i class="fas fa-shield-alt"></i> <span>تفعيل الاحتياطي...</span>';
    fetch(backupFile).then(r => r.text()).then(url => {
        runHls(video, url.trim());
        setTimeout(() => { btn.innerHTML = originalText; btn.style.pointerEvents = 'auto'; }, 3000);
    }).catch(() => {
        btn.innerHTML = 'خطأ في المصدر';
        setTimeout(() => { btn.innerHTML = originalText; btn.style.pointerEvents = 'auto'; }, 3000);
    });
}

function runHls(video, url) {
    if (Hls.isSupported()) {
        const hls = new Hls({ manifestLoadingTimeOut: 15000, enableWorker: true });
        hls.loadSource(url); hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => video.play());
        video.hls = hls;
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = url; video.play();
    }
}
</script>
</body>
</html>
