<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة - متجر الخدمة الرقمية</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <style>
        :root { --main: #e11d48; --bg: #0f172a; --card-bg: #ffffff; --whatsapp: #25d366; --snapchat: #FFFC00; }
        
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: #f1f5f9; padding-top: 180px; overflow-x: hidden; }

        /* --- شاشة التحميل الاحترافية (Splash Screen) --- */
        #loader {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            transition: all 0.8s cubic-bezier(0.645, 0.045, 0.355, 1);
        }
        
        /* حركة الكرة المتوهجة */
        .loader-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .ball-icon {
            font-size: 80px;
            color: #fff;
            text-shadow: 0 0 20px var(--main);
            animation: sports-spin 1.5s infinite linear;
        }

        /* حلقة الدوران الخارجية */
        .loader-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 4px solid transparent;
            border-top: 4px solid var(--main);
            border-radius: 50%;
            animation: ring-rotate 1s infinite linear;
        }

        .loader-status {
            margin-top: 30px;
            color: #fff;
            text-align: center;
        }

        .loader-status h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            margin: 0;
            background: linear-gradient(to right, #fff, var(--main));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 1px;
        }

        .loader-status p {
            font-size: 14px;
            opacity: 0.7;
            margin-top: 10px;
        }

        @keyframes sports-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes ring-rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* تأثير الخروج */
        .loader-hidden {
            opacity: 0;
            visibility: hidden;
            transform: scale(1.1);
        }

        /* ------------------------------------ */

        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 4px 25px rgba(0,0,0,0.4); }
        .promo-bar { background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(12px); color: #fff; padding: 15px 12px; text-align: center; border-bottom: 3px solid var(--main); }
        .promo-text { font-size: 13px; margin-bottom: 15px; line-height: 1.6; max-width: 900px; margin: auto; }
        .social-links { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
        .social-btn { display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 13px; transition: 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
        .btn-wa { background: var(--whatsapp); color: white; }
        .btn-snap { background: var(--snapchat); color: black; }
        .btn-x { background: #000; color: white; }
        header { background: #fff; padding: 15px; text-align: center; font-size: 20px; font-weight: bold; border-bottom: 4px solid var(--main); color: #1e293b; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; padding: 25px; max-width: 1300px; margin: auto; }
        .card { background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .c-head { padding: 15px; background: #f8fafc; display: flex; justify-content: space-between; font-weight: bold; border-bottom: 1px solid #f1f5f9; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 14px; border-radius: 12px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .play-btn:hover { background: #be123c; transform: scale(1.02); }
        
        footer { text-align: center; padding: 50px 10px; background: #fff; margin-top: 60px; border-top: 1px solid #e2e8f0; }
        .visitor-counter { display: inline-block; background: #1e293b; padding: 25px 45px; border-radius: 20px; color: #fff; box-shadow: 0 15px 35px rgba(0,0,0,0.2); }
        #count-num { font-size: 42px; color: #22c55e; font-weight: bold; letter-spacing: 2px; font-family: sans-serif; text-shadow: 0 0 15px rgba(34, 197, 94, 0.4); }
    </style>
</head>
<body>

<div id="loader">
    <div class="loader-wrapper">
        <div class="loader-ring"></div>
        <div class="ball-icon"><i class="fas fa-futbol"></i></div>
    </div>
    <div class="loader-status">
        <h2>PORTAL SPORTS</h2>
        <p>جاري فحص جودة البث وتأمين القنوات...</p>
    </div>
</div>

<div class="promo-sticky-container">
    <div class="promo-bar">
        <div class="promo-text">هذه الصفحة مقدمة مجاناً من <strong>متجر الخدمة الرقمية</strong> للاشتراك في الباقة كاملة يدعم جميع القنوات الرياضة ومكتبة الأفلام والمسلسلات على شاشة التلفزون والجوال تواصل واتساب</div>
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa"><i class="fab fa-whatsapp"></i> تواصل واتساب</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-snap"><i class="fab fa-snapchat"></i> سناب شات</a>
            <a href="https://x.com/d_service_pro?s=21" class="social-btn btn-x"><i class="fab fa-x-twitter"></i> تابعنا على X</a>
        </div>
    </div>
    <header>📺 بوابة الرياضة - البث الحي المباشر</header>
</div>

<div class="grid">
    <?php for($i = 1; $i <= 6; $i++): ?>
    <div class="card">
        <div class="c-head">
            <span>beIN Sport <?php echo $i; ?></span>
            <span style="color: #22c55e; display: flex; align-items: center; gap: 5px;"><i class="fas fa-satellite-dish"></i> مباشر</span>
        </div>
        <video id="vid<?php echo $i; ?>" controls poster="https://via.placeholder.com/400x225/111/fff?text=beIN+Sports+Premium"></video>
        <button class="play-btn" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل سيرفر البث</button>
    </div>
    <?php endfor; ?>
</div>

<footer>
    <div class="visitor-counter">
        <p style="margin:0 0 10px; font-size:13px; color:#94a3b8; letter-spacing: 1px;">إجمالي زيارات الموقع</p>
        <div id="count-num">1,452</div>
    </div>
    <p style="margin-top:25px; font-size:12px; color:#64748b;">حقوق البث محفوظة لمتجر الخدمة الرقمية &copy; 2026</p>
</footer>

<script>
// التحكم في شاشة التحميل (تأخير 3 ثوانٍ)
window.addEventListener('load', function() {
    setTimeout(function() {
        const loader = document.getElementById('loader');
        loader.classList.add('loader-hidden');
    }, 3000); // 3000 مللي ثانية = 3 ثوانٍ
});

function updateCounter() {
    let count = localStorage.getItem('visitorCount');
    if (!count) {
        count = 1452;
    } else {
        count = parseInt(count) + Math.floor(Math.random() * 3) + 1;
    }
    localStorage.setItem('visitorCount', count);
    document.getElementById('count-num').innerText = count.toLocaleString();
}

function play(id, src) {
    var video = document.getElementById(id);
    if (Hls.isSupported()) { 
        var hls = new Hls(); hls.loadSource(src); hls.attachMedia(video); video.play(); 
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) { 
        video.src = src; video.play(); 
    }
}

window.onload = updateCounter;
</script>
</body>
</html>
