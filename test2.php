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
            --main-glow: rgba(225, 29, 72, 0.6);
            --bg-deep: #061626; 
            --whatsapp: #25d366; 
            --snapchat: #FFFC00; 
        }
        
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 200px; overflow-x: hidden; color: #e2e8f0; }

        /* --- الخلفية الزخرفية المتحركة --- */
        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: url('https://www.transparenttextures.com/patterns/black-paper.png'), linear-gradient(135deg, var(--bg-deep) 0%, #0a1f33 100%); background-color: var(--bg-deep); }
        .bg-pattern-animated::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.15; animation: movePattern 60s linear infinite; }
        @keyframes movePattern { from { transform: translate(0, 0); } to { transform: translate(-50px, -50px); } }
        .side-glow { position: fixed; top: 0; right: 0; width: 50%; height: 100%; background: radial-gradient(circle at right, rgba(13, 45, 68, 0.6) 0%, transparent 70%); z-index: -1; pointer-events: none; }

        /* --- شاشة الدخول (Splash) --- */
        #intro-video-wrap { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #040c16; display: flex; justify-content: center; align-items: center; z-index: 1000000; transition: 1s ease-in-out; }
        .ball-glow { font-size: 80px; color: #fff; filter: drop-shadow(0 0 20px var(--main)); animation: pulseLogo 2s infinite; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.1); }
        @keyframes pulseLogo { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }

        /* --- الهيدر الاحترافي والمطور (Sticky) --- */
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; }
        
        /* شريط التواصل العلوي */
        .promo-bar { background: rgba(6, 22, 38, 0.95); backdrop-filter: blur(10px); color: #fff; padding: 12px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .promo-text { font-size: 12px; opacity: 0.8; }
        
        .social-links { display: flex; justify-content: center; gap: 10px; margin-top: 8px; }
        .social-btn { display: flex; align-items: center; gap: 6px; padding: 6px 15px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; transition: 0.3s; }
        .btn-wa { background: var(--whatsapp); } .btn-snap { background: var(--snapchat); color: #000; } .btn-x { background: #000; }

        /* =======================================================
           قـمـة الـفـخـامـة: هـيـدر الـزجـاج الـعـمـيـق (Deep Glass Header)
           ======================================================= */
        .main-portal-header {
            /* زجاج شفاف مع تعتيم خفيف ليناسب الخلفية الفخمة */
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(25px) saturate(150%);
            -webkit-backdrop-filter: blur(25px) saturate(150%);
            padding: 18px;
            text-align: center;
            /* حافة رفيعة جداً متوهجة باللون الأحمر الملكي */
            border-bottom: 2px solid rgba(225, 29, 72, 0.5);
            box-shadow: 0 10px 40px rgba(0,0,0,0.6);
            position: relative;
            overflow: hidden;
        }

        /* إضافة تأثير "الشعاع الزجاجي" (Glass Ray) المتحرك */
        .main-portal-header::after {
            content: "";
            position: absolute;
            top: 0; left: -150%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent);
            animation: glassRay 5s infinite linear;
        }

        .portal-title {
            margin: 0;
            font-size: 24px;
            font-weight: 900; /* أقصى سمك للخط */
            letter-spacing: 1.5px;
            /* تدرج لوني معدني فخم للنص */
            background: linear-gradient(to bottom, #ffffff 40%, #c4cfdd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            text-shadow: 0 2px 10px rgba(255,255,255,0.2);
        }

        @keyframes glassRay {
            0% { left: -150%; }
            100% { left: 150%; }
        }
        /* ======================================================= */

        /* --- شبكة القنوات والكروت --- */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; padding: 25px; max-width: 1400px; margin: auto; }
        .card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.08); transition: 0.3s; }

        /* هيدر القنوات البنفسجي المستقيم الأنيق */
        .c-head { padding: 10px 15px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
        .channel-name-box { background: linear-gradient(45deg, #7c3aed, #fff); padding: 3px 15px; border-radius: 6px; border: 1px solid rgba(124, 58, 237, 0.2); }
        .channel-name { display: flex; align-items: center; gap: 6px; font-family: 'Poppins', 'Tajawal', sans-serif; font-size: 14px; font-weight: 900; color: #061626; }
        .tag-4k { background: #000; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 3px; font-weight: 900; border: 1px solid #7c3aed; }
        .live-status { display: flex; align-items: center; gap: 5px; background: rgba(0,0,0,0.4); padding: 3px 10px; border-radius: 6px; border-right: 2px solid #22c55e; }
        .live-text { font-size: 9px; font-weight: 900; color: #22c55e; }
        .live-dot { width: 5px; height: 5px; background-color: #22c55e; border-radius: 50%; animation: blink 1s infinite; }

        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }

        /* زر التشغيل الزجاجي الفخم النابض */
        .play-btn-premium { 
            width: 90%; margin: 20px auto; display: flex; justify-content: center; align-items: center; gap: 12px;
            background: rgba(225, 29, 72, 0.05); backdrop-filter: blur(5px); color: #fff; 
            border: 1.5px solid rgba(225, 29, 72, 0.4); padding: 15px; border-radius: 50px; 
            font-weight: 900; font-size: 16px; cursor: pointer; position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 15px rgba(225, 29, 72, 0.2);
            text-shadow: 0 2px 4px rgba(0,0,0,0.5); overflow: hidden; animation: borderPulse 2s infinite ease-in-out;
        }
        .play-btn-premium:hover { background: var(--main); border-color: var(--main); box-shadow: 0 8px 30px rgba(225, 29, 72, 0.8); transform: translateY(-4px) scale(1.03); }
        @keyframes borderPulse { 0%, 100% { border-color: rgba(225, 29, 72, 0.4); } 50% { border-color: rgba(225, 29, 72, 0.8); } }

        footer { text-align: center; padding: 50px; }
        .footer-counter { background: rgba(255,255,255,0.02); padding: 20px 45px; border-radius: 20px; display: inline-block; border: 1px solid rgba(255,255,255,0.05); }
        #count-num { font-size: 40px; color: #22c55e; font-weight: 900; }
    </style>
</head>
<body>

    <div class="bg-pattern-animated"></div>
    <div class="side-glow"></div>

    <div id="intro-video-wrap">
        <div style="text-align:center;">
            <i class="fas fa-futbol ball-glow"></i>
            <h1 style="color:#fff; font-size:45px; font-weight:900; margin-top:20px;">الخدمة الرقمية</h1>
        </div>
    </div>

    <div class="promo-sticky-container">
        <div class="promo-bar">
            <div class="promo-text">هذه الصفحة مقدمة من <strong>متجر الخدمة الرقمية</strong> للاشتراك في الباقة كاملة تواصل واتساب</div>
            <div class="social-links">
                <a href="https://wa.me/966505571164" class="social-btn btn-wa"><i class="fab fa-whatsapp"></i> واتساب</a>
                <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-snap"><i class="fab fa-snapchat"></i> سناب</a>
                <a href="https://x.com/d_service_pro?s=21" class="social-btn btn-x"><i class="fab fa-x-twitter"></i> تويتر X</a>
            </div>
        </div>
        <div class="main-portal-header">
            <h2 class="portal-title">بوابة الرياضة — البث المباشر</h2>
        </div>
    </div>

    <div class="grid">
        <?php for($i = 1; $i <= 9; $i++): ?>
        <div class="card">
            <div class="c-head">
                <div class="channel-name-box">
                    <span class="channel-name"><span class="tag-4k">4K</span> beIN Sport <?php echo $i; ?></span>
                </div>
                <div class="live-status">
                    <div class="live-dot"></div>
                    <span class="live-text">Live Stream</span>
                </div>
            </div>
            <video id="vid<?php echo $i; ?>" playsinline webkit-playsinline controls poster="https://via.placeholder.com/400x225/061626/fff?text=beIN+Sports"></video>
            <button class="play-btn-premium" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')"> ▶ تشغيل البث الآن</button>
        </div>
        <?php endfor; ?>
    </div>

    <footer>
        <div class="footer-counter">
            <p style="margin:0; font-size:12px; opacity:0.6;">إجمالي زيارات الموقع</p>
            <div id="count-num">0</div>
        </div>
    </footer>

    <script>
    window.addEventListener('load', () => {
        setTimeout(() => {
            const intro = document.getElementById('intro-video-wrap');
            if(intro) { intro.classList.add('intro-hide'); setTimeout(() => intro.remove(), 1000); }
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
            var hls = new Hls(); hls.loadSource(src); hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, () => video.play());
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = src; video.play();
        }
    }
    window.onload = updateCounter;
    </script>
</body>
</html>
