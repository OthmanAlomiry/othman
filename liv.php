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
        :root { --main: #e11d48; --bg: #030712; --whatsapp: #25d366; }
        
        body { 
            margin: 0; 
            font-family: 'Tajawal', sans-serif; 
            background-color: var(--bg);
            padding-top: 180px; 
            overflow-x: hidden;
            color: #fff;
        }

        /* --- محرك الخلفية الرياضية المتحركة (VFX Background) --- */
        .bg-vfx {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            background: #000;
            overflow: hidden;
        }

        /* تأثير الشبكة الرياضية (Grid) */
        .bg-vfx::after {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image: 
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            perspective: 500px;
            mask-image: radial-gradient(ellipse at center, black, transparent 80%);
        }

        /* أضواء النيون المتحركة (Animated Energy Waves) */
        .energy-wave {
            position: absolute;
            width: 800px; height: 800px;
            background: radial-gradient(circle, rgba(225, 29, 72, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(60px);
            animation: moveEnergy 15s infinite alternate ease-in-out;
        }

        @keyframes moveEnergy {
            0% { transform: translate(-20%, -20%) scale(1); }
            50% { transform: translate(40%, 10%) scale(1.2); }
            100% { transform: translate(-10%, 40%) scale(0.9); }
        }

        /* --- شاشة الدخول السينمائية الواقعية --- */
        #intro-video-wrap {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000000;
            transition: 1s cubic-bezier(0.85, 0, 0.15, 1);
        }

        .intro-ui { text-align: center; }
        .ball-glow {
            font-size: 100px;
            color: #fff;
            filter: drop-shadow(0 0 30px var(--main));
            animation: logoIntro 2s infinite ease-in-out;
        }
        
        .intro-title-ar {
            font-size: 50px; font-weight: 900; color: #fff; margin-top: 20px;
            background: linear-gradient(to bottom, #fff 50%, #94a3b8 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        @keyframes logoIntro {
            0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.8; }
            50% { transform: scale(1.1) rotate(10deg); opacity: 1; filter: drop-shadow(0 0 50px var(--main)); }
        }

        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.2); }

        /* --- تنسيق المحتوى (Glassmorphism) --- */
        .promo-bar { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(15px); border-bottom: 2px solid var(--main); padding: 15px; text-align: center; }
        header { background: #fff; color: #000; padding: 12px; text-align: center; font-weight: 900; }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; padding: 25px; max-width: 1400px; margin: auto; }
        
        /* كروت شفافة باحترافية */
        .card { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px; 
            overflow: hidden; 
            transition: 0.4s;
        }
        .card:hover { transform: translateY(-10px); border-color: var(--main); box-shadow: 0 0 30px rgba(225, 29, 72, 0.2); }
        
        .c-head { padding: 12px; background: rgba(0,0,0,0.3); display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; color: #ddd; }
        
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        
        .play-btn { 
            width: 90%; margin: 15px auto; display: block; 
            background: linear-gradient(45deg, var(--main), #9f1239); 
            color: #fff; border: none; padding: 14px; border-radius: 12px; 
            font-weight: 900; cursor: pointer; transition: 0.3s;
        }

        footer { text-align: center; padding: 60px 20px; }
        #count-num { font-size: 45px; color: #22c55e; font-weight: 900; text-shadow: 0 0 20px rgba(34, 197, 94, 0.4); }
    </style>
</head>
<body>

    <div class="bg-vfx">
        <div class="energy-wave" style="top: -10%; left: -10%;"></div>
        <div class="energy-wave" style="bottom: -10%; right: -10%; animation-delay: -7s; background: radial-gradient(circle, rgba(30, 64, 175, 0.15) 0%, transparent 70%);"></div>
    </div>

    <div id="intro-video-wrap">
        <div class="intro-ui">
            <i class="fas fa-futbol ball-glow"></i>
            <h1 class="intro-title-ar">الخدمة الرقمية</h1>
            <div style="color:var(--main); letter-spacing: 5px; font-weight: bold; margin-top: 10px;">D-SERVICE PRO</div>
        </div>
    </div>

    <div style="position:fixed; top:0; width:100%; z-index:1000;">
        <div class="promo-bar">
            <div style="font-size: 14px; font-weight: bold;">بث حصري مجاني من متجر الخدمة الرقمية</div>
            <div style="display:flex; justify-content:center; gap:15px; margin-top:10px;">
                <a href="https://wa.me/966505571164" style="color:#fff; text-decoration:none; background:var(--whatsapp); padding:5px 15px; border-radius:50px; font-size:12px;"><i class="fab fa-whatsapp"></i> واتساب</a>
                <a href="https://snapchat.com/t/4DVEkM5k" style="color:#000; text-decoration:none; background:#FFFC00; padding:5px 15px; border-radius:50px; font-size:12px;"><i class="fab fa-snapchat"></i> سناب</a>
            </div>
        </div>
        <header>المباريات المباشرة - جودة عالية 4K</header>
    </div>

    <div class="grid">
        <?php for($i = 1; $i <= 9; $i++): ?>
        <div class="card">
            <div class="c-head">
                <span>beIN Sport <?php echo $i; ?></span>
                <span style="color: #22c55e;"><i class="fas fa-circle" style="font-size: 8px;"></i> مباشر</span>
            </div>
            <video id="vid<?php echo $i; ?>" playsinline webkit-playsinline controls poster="https://via.placeholder.com/400x225/111/fff?text=beIN+Sports"></video>
            <button class="play-btn" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">تفعيل البث المباشر</button>
        </div>
        <?php endfor; ?>
    </div>

    <footer>
        <div style="background: rgba(255,255,255,0.05); padding: 25px; border-radius: 20px; display: inline-block; border: 1px solid rgba(255,255,255,0.1);">
            <p style="margin:0; font-size:12px; opacity:0.6;">إجمالي المشاهدات الحقيقية</p>
            <div id="count-num">0</div>
        </div>
    </footer>

    <script>
    // إخفاء الانترو بعد 3 ثواني
    window.addEventListener('load', () => {
        setTimeout(() => {
            const intro = document.getElementById('intro-video-wrap');
            intro.classList.add('intro-hide');
            setTimeout(() => intro.remove(), 1000);
        }, 3000);
    });

    // عداد الزيارات
    function updateCounter() {
        let count = localStorage.getItem('vCount') || 2840;
        count = parseInt(count) + 1;
        localStorage.setItem('vCount', count);
        document.getElementById('count-num').innerText = count.toLocaleString();
    }

    // تشغيل الفيديو بدون تكبير
    function play(id, src) {
        var video = document.getElementById(id);
        if (Hls.isSupported()) {
            var hls = new Hls();
            hls.loadSource(src);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, () => video.play());
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = src;
            video.play();
        }
    }

    window.onload = updateCounter;
    </script>
</body>
</html>
