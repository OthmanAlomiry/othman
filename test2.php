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
        }
        
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 190px; overflow-x: hidden; color: #e2e8f0; }

        /* --- شاشة الدخول السينمائية الفائقة (VFX Intro) --- */
        #ultra-pro-intro {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: radial-gradient(circle at center, #0a1f33 0%, #040c16 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000000;
            overflow: hidden;
            transition: opacity 1s cubic-bezier(0.4, 0, 0.2, 1), visibility 1s;
        }

        /* دخان رقمي خلفي متحرك */
        .intro-bg-vfx {
            position: absolute;
            width: 150%; height: 150%;
            background: url('https://www.transparenttextures.com/patterns/asfalt-dark.png');
            opacity: 0.2;
            animation: bgZoom 20s infinite linear;
        }

        .intro-container {
            position: relative;
            z-index: 10;
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        /* كرة القدم النيونية المتوهجة */
        .football-loader {
            font-size: 100px;
            color: #fff;
            filter: drop-shadow(0 0 20px var(--main)) drop-shadow(0 0 40px var(--main));
            animation: ballAction 2.5s infinite cubic-bezier(0.68, -0.55, 0.27, 1.55);
            display: inline-block;
            margin-bottom: 30px;
        }

        .intro-brand-name {
            font-size: 50px;
            font-weight: 900;
            letter-spacing: -2px;
            margin: 0;
            background: linear-gradient(to bottom, #fff 50%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 20px rgba(0,0,0,0.5);
        }

        .intro-sub-text {
            color: var(--main);
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            letter-spacing: 6px;
            font-size: 12px;
            margin-top: 5px;
            opacity: 0.8;
        }

        /* نظام شريط التحميل الاحترافي */
        .loader-wrapper {
            margin-top: 50px;
            padding: 0 40px;
        }

        .loader-bar-bg {
            width: 100%;
            height: 4px;
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.5);
        }

        .loader-bar-fill {
            position: absolute;
            top: 0; left: 0;
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, transparent, var(--main), #fff);
            box-shadow: 0 0 15px var(--main);
            border-radius: 10px;
        }

        .loader-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-size: 10px;
            font-weight: 700;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        #loading-msg { color: var(--main); }

        /* الحركات (Animations) */
        @keyframes ballAction {
            0% { transform: scale(0.8) rotate(0deg); opacity: 0.5; }
            50% { transform: scale(1.1) rotate(180deg); opacity: 1; filter: drop-shadow(0 0 50px var(--main)); }
            100% { transform: scale(0.8) rotate(360deg); opacity: 0.5; }
        }

        @keyframes bgZoom {
            from { transform: scale(1); }
            to { transform: scale(1.2); }
        }

        /* حالة انتهاء التحميل */
        .intro-finished {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        /* --- باقي تنسيقات الموقع (بدون تغيير) --- */
        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: url('https://www.transparenttextures.com/patterns/black-paper.png'), linear-gradient(135deg, var(--bg-deep) 0%, #0a1f33 100%); background-color: var(--bg-deep); }
        .bg-pattern-animated::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.15; animation: movePattern 60s linear infinite; }
        @keyframes movePattern { from { transform: translate(0, 0); } to { transform: translate(-50px, -50px); } }
        .side-glow { position: fixed; top: 0; right: 0; width: 50%; height: 100%; background: radial-gradient(circle at right, rgba(13, 45, 68, 0.6) 0%, transparent 70%); z-index: -1; pointer-events: none; }
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; }
        .promo-bar { background: rgba(6, 22, 38, 0.96); backdrop-filter: blur(10px); color: #fff; padding: 15px 12px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .promo-text { font-size: 13px; font-weight: 700; opacity: 0.85; } 
        .social-links { display: flex; justify-content: center; gap: 10px; margin-top: 10px; }
        .social-btn { display: flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 11px; color: #fff; } 
        .btn-wa { background: var(--whatsapp); } .btn-snap { background: var(--snapchat); color: #000; } .btn-x { background: #000; }
        .main-portal-header { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(25px); padding: 12px 20px; text-align: center; border-bottom: 2px solid rgba(225, 29, 72, 0.5); box-shadow: 0 10px 40px rgba(0,0,0,0.6); }
        .portal-title { margin: 0; font-size: 20px; font-weight: 900; letter-spacing: 0.5px; background: linear-gradient(to bottom, #ffffff 40%, #c4cfdd 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; padding: 25px; max-width: 1400px; margin: auto; }
        .card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.08); }
        .c-head { padding: 10px 15px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
        .channel-name-box { background: linear-gradient(45deg, #7c3aed, #fff); padding: 3px 15px; border-radius: 6px; }
        .channel-name { display: flex; align-items: center; gap: 6px; font-family: 'Poppins', 'Tajawal', sans-serif; font-size: 13px; font-weight: 900; color: #061626; }
        .tag-4k { background: #000; color: #fff; font-size: 8px; padding: 1px 4px; border-radius: 3px; font-weight: 900; }
        .live-status { display: flex; align-items: center; gap: 5px; background: rgba(0,0,0,0.4); padding: 3px 10px; border-radius: 6px; border-right: 2px solid #22c55e; }
        .live-text { font-size: 8px; font-weight: 900; color: #22c55e; }
        .live-dot { width: 5px; height: 5px; background-color: #22c55e; border-radius: 50%; animation: blinkStatus 1s infinite; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        .play-btn-premium { width: 90%; margin: 20px auto; display: flex; justify-content: center; align-items: center; gap: 12px; background: rgba(225, 29, 72, 0.05); backdrop-filter: blur(5px); color: #fff; border: 1.5px solid rgba(225, 29, 72, 0.4); padding: 14px; border-radius: 50px; font-weight: 900; font-size: 15px; cursor: pointer; transition: 0.3s; animation: borderPulse 2s infinite ease-in-out; }
        .play-btn-premium:hover { background: var(--main); box-shadow: 0 8px 30px rgba(225, 29, 72, 0.8); }
        @keyframes borderPulse { 0%, 100% { border-color: rgba(225, 29, 72, 0.4); box-shadow: 0 4px 15px rgba(225, 29, 72, 0.2); } 50% { border-color: rgba(225, 29, 72, 0.8); box-shadow: 0 4px 20px rgba(225, 29, 72, 0.5); } }
        footer { text-align: center; padding: 50px; }
        #count-num { font-size: 35px; color: #22c55e; font-weight: 900; }
    </style>
</head>
<body>

    <div id="ultra-pro-intro">
        <div class="intro-bg-vfx"></div>
        <div class="intro-container">
            <i class="fas fa-futbol football-loader"></i>
            <h1 class="intro-brand-name">الخدمة الرقمية</h1>
            <div class="intro-sub-text">D-SERVICE PREMIUM</div>
            
            <div class="loader-wrapper">
                <div class="loader-bar-bg">
                    <div id="loading-bar" class="loader-bar-fill"></div>
                </div>
                <div class="loader-stats">
                    <span id="loading-msg">جاري الاتصال بالسيرفرات...</span>
                    <span id="loading-perc">0%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-pattern-animated"></div>
    <div class="side-glow"></div>

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
        <div style="background: rgba(255,255,255,0.02); padding: 15px 40px; border-radius: 20px; display: inline-block; border: 1px solid rgba(255,255,255,0.05);">
            <p style="margin:0; font-size:11px; opacity:0.6;">إجمالي زيارات الموقع</p>
            <div id="count-num">0</div>
        </div>
    </footer>

    <script>
    // نظام التحميل الاحترافي المتقدم
    function startIntro() {
        const bar = document.getElementById('loading-bar');
        const perc = document.getElementById('loading-perc');
        const msg = document.getElementById('loading-msg');
        const intro = document.getElementById('ultra-pro-intro');
        
        const steps = [
            { p: 15, m: "تجهيز بيئة الاتصال..." },
            { p: 45, m: "فك تشفير السيرفرات الرياضية..." },
            { p: 70, m: "تحميل قنوات 4K المباشرة..." },
            { p: 90, m: "تأمين جودة البث..." },
            { p: 100, m: "تم الاتصال بنجاح!" }
        ];

        let currentStep = 0;
        let progress = 0;

        const interval = setInterval(() => {
            if (progress < steps[currentStep].p) {
                progress += Math.floor(Math.random() * 3) + 1;
                if (progress > 100) progress = 100;
                bar.style.width = progress + '%';
                perc.innerText = progress + '%';
                msg.innerText = steps[currentStep].m;
            } else {
                if (currentStep < steps.length - 1) {
                    currentStep++;
                } else if (progress >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        intro.classList.add('intro-finished');
                        updateCounter();
                    }, 800);
                }
            }
        }, 40);
    }

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

    // تشغيل الانترو فور تحميل الصفحة
    window.onload = startIntro;
    </script>
</body>
</html>
