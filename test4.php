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
            --purple-grad: linear-gradient(45deg, #7c3aed, #9ca3af); 
            --green-grad: linear-gradient(45deg, #16a34a, #facc15);
        }
        
        html { scroll-behavior: smooth; }
        /* تقليل المسافة العلوية لأقصى حد لرفع القنوات */
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 175px; overflow-x: hidden; color: #e2e8f0; }

        /* --- شاشة الدخول --- */
        #pro-cinematic-intro {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000;
            display: flex; justify-content: center; align-items: center; z-index: 1000000;
            transition: all 1.2s cubic-bezier(0.7, 0, 0.3, 1);
        }
        .loading-fill-vfx { position: absolute; width: 0%; height: 100%; background: linear-gradient(to right, transparent, var(--main), #fff); animation: proLoadingFlow 3s forwards; }
        @keyframes proLoadingFlow { 0% { width: 0%; } 100% { width: 100%; } }
        .intro-finish-vfx { opacity: 0; visibility: hidden; }

        /* --- الهيدر الملتصق --- */
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; }
        .promo-bar { background: rgba(6, 22, 38, 0.98); backdrop-filter: blur(10px); padding: 10px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .promo-text { font-size: 11px; font-weight: 700; margin-bottom: 6px; } 
        .social-links { display: flex; justify-content: center; gap: 6px; }
        .social-btn { display: flex; align-items: center; gap: 4px; padding: 5px 10px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; } 
        .btn-wa { background: var(--whatsapp); } .btn-snap { background: var(--snapchat); color: #000; } .btn-tg { background: var(--telegram); } .btn-x { background: #000; }

        /* شريط التنقل */
        .nav-shortcuts {
            background: rgba(10, 31, 51, 0.95);
            display: flex; justify-content: center; gap: 10px; padding: 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .nav-box-purple, .nav-box-green { padding: 5px 15px; border-radius: 6px; display: flex; align-items: center; gap: 5px; font-weight: 900; font-size: 11px; color: #061626; text-decoration: none; }
        .nav-box-purple { background: var(--purple-grad); }
        .nav-box-green { background: var(--green-grad); }

        /* --- الشبكة والقنوات --- */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 10px 20px; max-width: 1400px; margin: auto; }
        
        /* تقليص مساحة الفواصل لرفع القنوات */
        .section-divider {
            grid-column: 1 / -1;
            padding: 5px 0;
            font-size: 18px;
            font-weight: 900;
            display: flex; align-items: center; gap: 10px;
            margin: 0; /* إلغاء المارجن لرفعها للأعلى */
            color: #fff;
        }
        .section-divider::after { content: ""; height: 1px; flex: 1; background: rgba(255,255,255,0.1); }

        .card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border-radius: 15px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); }
        .c-head { padding: 8px 12px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
        
        /* إرجاع مستطيل Live Stream */
        .live-status-box { 
            display: flex; align-items: center; gap: 5px; 
            background: rgba(34, 197, 94, 0.15); 
            padding: 4px 10px; border-radius: 6px; 
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .live-text { font-size: 9px; font-weight: 900; color: #22c55e; text-transform: uppercase; }
        .live-dot { width: 6px; height: 6px; background-color: #22c55e; border-radius: 50%; animation: blink 1s infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

        .name-box-purple { background: var(--purple-grad); padding: 3px 10px; border-radius: 5px; }
        .name-box-green { background: var(--green-grad); padding: 3px 10px; border-radius: 5px; }
        .channel-name { display: flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 900; color: #061626; }
        .tag-4k { background: #000; color: #fff; font-size: 7px; padding: 1px 3px; border-radius: 2px; }
        
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .play-btn-premium { width: 90%; margin: 12px auto; display: flex; justify-content: center; align-items: center; gap: 8px; background: rgba(225, 29, 72, 0.1); color: #fff; border: 1px solid rgba(225, 29, 72, 0.4); padding: 10px; border-radius: 50px; font-weight: 900; font-size: 13px; cursor: pointer; transition: 0.3s; }
        .play-btn-premium:hover { background: var(--main); }

        footer { text-align: center; padding: 20px; opacity: 0.5; font-size: 10px; }
    </style>
</head>
<body>

<div id="pro-cinematic-intro">
    <div class="content-wrap">
        <div style="font-size: 50px; color: #fff; margin-bottom: 15px;"><i class="fas fa-play-circle"></i></div>
        <div style="width: 200px; height: 2px; background: rgba(255,255,255,0.1); margin: auto; position: relative; overflow: hidden;">
            <div class="loading-fill-vfx"></div>
        </div>
    </div>
</div>

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
        <i class="fas fa-trophy" style="color: #7c3aed; font-size: 16px;"></i> باقة beIN Sports
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
        <button class="play-btn-premium" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')"> ▶ تشغيل البث </button>
    </div>
    <?php endfor; ?>

    <div id="starz-section" class="section-divider">
        <i class="fas fa-star" style="color: #16a34a; font-size: 16px;"></i> باقة STARZPLAY
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
        <button class="play-btn-premium" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')"> ▶ تشغيل البث </button>
    </div>
    <?php endfor; ?>
</div>

<footer>
    <p>متجر الخدمة الرقمية - جميع الحقوق محفوظة &copy;</p>
</footer>

<script>
window.addEventListener('load', function() {
    setTimeout(function() {
        const intro = document.getElementById('pro-cinematic-intro');
        if(intro) {
            intro.classList.add('intro-finish-vfx');
            setTimeout(() => intro.remove(), 1200);
        }
    }, 2000); 
});

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
