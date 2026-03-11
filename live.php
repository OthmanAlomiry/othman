<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة - متجر الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --main: #e11d48; --bg: #f8fafc; --whatsapp: #25d366; --snapchat: #FFFC00; --x-black: #000000; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); padding-top: 180px; }
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .promo-bar { background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(10px); color: #fff; padding: 15px 12px; text-align: center; border-bottom: 3px solid var(--main); }
        .promo-text { font-size: 13px; margin-bottom: 15px; line-height: 1.6; max-width: 900px; margin: auto; }
        .social-links { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
        .social-btn { display: flex; align-items: center; gap: 8px; padding: 8px 18px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 12px; transition: 0.3s; }
        .btn-wa { background: var(--whatsapp); color: white; }
        .btn-snap { background: var(--snapchat); color: black; }
        .btn-x { background: var(--x-black); color: white; }
        header { background: #fff; padding: 12px; text-align: center; font-size: 18px; font-weight: bold; border-bottom: 3px solid var(--main); color: #1e293b; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; max-width: 1300px; margin: auto; }
        .card { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; }
        .c-head { padding: 12px; background: #fcfcfc; display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; border-bottom: 1px solid #eee; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; }
        footer { text-align: center; padding: 40px 10px; background: #fff; border-top: 1px solid #eee; margin-top: 50px; }
        .visitor-counter { display: inline-block; background: #1e293b; padding: 15px 30px; border-radius: 15px; color: #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        #count-num { font-family: 'Orbitron', sans-serif; font-size: 32px; color: #22c55e; text-shadow: 0 0 10px rgba(34, 197, 94, 0.5); font-weight: bold; }
    </style>
</head>
<body>

<div class="promo-sticky-container">
    <div class="promo-bar">
        <div class="promo-text">هذه الصفحة مقدمة مجاناً من <strong>متجر الخدمة الرقمية</strong> للاشتراك في الباقة كاملة يدعم جميع القنوات الرياضة ومكتبة الأفلام والمسلسلات على شاشة التلفزون والجوال تواصل واتساب</div>
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa"><i class="fab fa-whatsapp"></i> تواصل واتساب</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-snap"><i class="fab fa-snapchat"></i> سناب شات</a>
            <a href="https://x.com/d_service_pro?s=21" class="social-btn btn-x"><i class="fab fa-x-twitter"></i> تابعنا على X</a>
        </div>
    </div>
    <header>📺 بوابة الرياضة - مباريات اليوم المباشرة</header>
</div>

<div class="grid">
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card">
        <div class="c-head"><span>beIN Sport <?php echo $i; ?></span><span style="color: #22c55e; font-size: 12px;">● مباشر</span></div>
        <video id="vid<?php echo $i; ?>" controls poster="https://via.placeholder.com/400x225/111/fff?text=beIN+Sports"></video>
        <button class="play-btn" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل البث الآن</button>
        <div class="sch-box" id="sch-<?php echo $i; ?>" style="padding:10px; font-size:12px; color:#444;">جاري جلب الجدول...</div>
    </div>
    <?php endfor; ?>
</div>

<footer>
    <div class="visitor-counter"><p style="margin:0 0 10px; font-size:12px; color:#94a3b8;">إجمالي زيارات الموقع الحقيقية</p><div id="count-num">0</div></div>
</footer>

<script>
// --- كود العداد الفعلي ---
const FIREBASE_URL = "https://scothman-42a6e.firebaseio.com/visits.json";

async function updateVisitCount() {
    try {
        const response = await fetch(FIREBASE_URL);
        const data = await response.json();
        
        // التحقق الفعلي: إذا كان null نجعله 0
        let currentCount = (data !== null) ? parseInt(data) : 0;
        let newCount = currentCount + 1;
        
        document.getElementById('count-num').innerText = newCount.toLocaleString();
        
        await fetch(FIREBASE_URL, {
            method: 'PUT',
            body: JSON.stringify(newCount)
        });
    } catch (e) { document.getElementById('count-num').innerText = "1"; }
}

function play(id, src) {
    var video = document.getElementById(id);
    if (Hls.isSupported()) { var hls = new Hls(); hls.loadSource(src); hls.attachMedia(video); video.play(); }
    else if (video.canPlayType('application/vnd.apple.mpegurl')) { video.src = src; video.play(); }
}

async function fetchMatches() {
    try {
        const response = await fetch('https://api.scorebat.com/video-api/v3/');
        const data = await response.json();
        const matches = data.response;
        for (let i = 1; i <= 9; i++) {
            const container = document.getElementById(`sch-${i}`);
            let m = matches[i - 1]; 
            container.innerHTML = m ? `<div style="display:flex; justify-content:space-between;"><strong>${m.title}</strong><span style="color:#e11d48;font-weight:bold;">LIVE</span></div>` : "بث مباشر قريباً";
        }
    } catch (e) { console.log("Match Fetch Error"); }
}

window.onload = function() {
    updateVisitCount();
    fetchMatches();
};
</script>
</body>
</html>
