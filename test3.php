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
            --purple-grad: linear-gradient(45deg, #7c3aed, #fff); 
            --green-grad: linear-gradient(45deg, #16a34a, #fff); 
        }
        
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 190px; overflow-x: hidden; color: #e2e8f0; }

        /* --- شاشة الدخول الاحترافية (Intro) --- */
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
        .intro-finish-vfx { transform: scale(1.1); filter: blur(20px); opacity: 0; visibility: hidden; }

        /* --- الهيدر والشبكة --- */
        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: url('https://www.transparenttextures.com/patterns/black-paper.png'), linear-gradient(135deg, var(--bg-deep) 0%, #0a1f33 100%); }
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; }
        .promo-bar { background: rgba(6, 22, 38, 0.96); backdrop-filter: blur(10px); color: #fff; padding: 15px 12px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .promo-text { font-size: 13px; font-weight: 700; opacity: 0.85; } 
        .social-links { display: flex; justify-content: center; gap: 10px; margin-top: 10px; }
        .social-btn { display: flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 11px; color: #fff; } 
        .main-portal-header { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(25px); padding: 12px 20px; text-align: center; border-bottom: 2px solid rgba(225, 29, 72, 0.5); }
        .portal-title { margin: 0; font-size: 20px; font-weight: 900; background: linear-gradient(to bottom, #ffffff 40%, #c4cfdd 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; padding: 25px; max-width: 1400px; margin: auto; }
        .card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border-radius: 20px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); }
        .c-head { padding: 10px 15px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
        .channel-name-box { padding: 3px 15px; border-radius: 6px; }
        .box-purple { background: var(--purple-grad); }
        .box-green { background: var(--green-grad); }
        .channel-name { display: flex; align-items: center; gap: 6px; font-family: 'Poppins', 'Tajawal', sans-serif; font-size: 13px; font-weight: 900; color: #061626; }
        
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        .play-btn-premium { width: 90%; margin: 20px auto; display: flex; justify-content: center; align-items: center; gap: 10px; background: rgba(225, 29, 72, 0.05); backdrop-filter: blur(5px); color: #fff; border: 1.5px solid rgba(225, 29, 72, 0.4); padding: 14px; border-radius: 50px; font-weight: 900; font-size: 15px; cursor: pointer; transition: 0.3s; }
        .play-btn-premium:hover { background: var(--main); box-shadow: 0 8px 30px rgba(225, 29, 72, 0.8); }
        
        footer { text-align: center; padding: 50px; }
        #count-num { font-size: 35px; color: #22c55e; font-weight: 900; }
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

<div class="promo-sticky-container">
    <div class="promo-bar">
        <div class="promo-text">هذه الصفحة مقدمة من <strong>متجر الخدمة الرقمية</strong></div>
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn" style="background:var(--whatsapp)"><i class="fab fa-whatsapp"></i> واتساب</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:var(--snapchat); color:#000"><i class="fab fa-snapchat"></i> سناب</a>
        </div>
    </div>
    <div class="main-portal-header"><h2 class="portal-title">بوابة الرياضة — البث المباشر</h2></div>
</div>

<div class="grid">
    <?php for($i = 1; $i <= 11; $i++): ?>
    <?php 
        $is_starz = ($i >= 10);
        $title = $is_starz ? "STARZPLAY " . ($i-9) : "beIN Sport " . $i;
        $class = $is_starz ? "box-green" : "box-purple";
    ?>
    <div class="card">
        <div class="c-head">
            <div class="channel-name-box <?php echo $class; ?>">
                <span class="channel-name"><span style="background:#000;color:#fff;padding:1px 4px;font-size:8px;border-radius:3px">4K</span> <?php echo $title; ?></span>
            </div>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline webkit-playsinline controls></video>
        <button class="play-btn-premium" onclick="playChannel('vid<?php echo $i; ?>', '<?php echo $i; ?>')"> ▶ تشغيل البث الآن</button>
    </div>
    <?php endfor; ?>
</div>

<footer><div id="count-num">0</div></footer>

<script>
// دالة التشغيل الرئيسية
function playChannel(videoId, chNum) {
    var video = document.getElementById(videoId);
    var srcFile = 'b' + chNum + '.php';

    if (chNum >= 10) {
        // لقنوات ستارز بلاي: نستخدم الرابط الذي يوفره نظام البروكسي داخل الملف
        // هذا الرابط سيعمل بـ HTTPS عبر موقعك
        var proxyUrl = window.location.origin + '/' + srcFile + '?get_stream=1';
        startStream(video, proxyUrl);
    } else {
        // قنوات beIN العادية: نقرأ الرابط النصي من الملف
        fetch(srcFile).then(r => r.text()).then(url => startStream(video, url.trim()));
    }
}

function startStream(video, url) {
    if (Hls.isSupported()) {
        var hls = new Hls({
            xhrSetup: function (xhr, url) {
                xhr.withCredentials = false;
            }
        });
        hls.loadSource(url);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => video.play());
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = url;
        video.play();
    }
}

window.addEventListener('load', () => {
    setTimeout(() => {
        const intro = document.getElementById('pro-cinematic-intro');
        if(intro) { intro.classList.add('intro-finish-vfx'); setTimeout(() => intro.remove(), 1200); }
        document.getElementById('count-num').innerText = "1,452";
    }, 3500);
});
</script>
</body>
</html>
