<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة - متجر الخدمة الرقمية</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <style>
        :root { 
            --main: #e11d48; 
            --bg: #f8fafc; 
            --whatsapp: #25d366; 
            --snapchat: #FFFC00; 
            --x-black: #000000;
        }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); padding-top: 140px; /* تعويض مساحة القسم الثابت */ }
        
        /* جعل قسم الإعلان ثابتاً في الأعلى */
        .promo-sticky-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000; /* لضمان ظهوره فوق كل شيء */
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .promo-bar {
            background: rgba(30, 41, 59, 0.98); /* لون داكن مع شفافية بسيطة */
            backdrop-filter: blur(5px); /* تأثير زجاجي */
            color: #fff;
            padding: 15px 10px;
            text-align: center;
            border-bottom: 3px solid var(--main);
        }
        
        .promo-text { font-size: 13px; margin-bottom: 10px; line-height: 1.4; }
        .promo-text strong { color: var(--main); }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .social-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 50px; /* أزرار دائرية */
            text-decoration: none;
            font-weight: bold;
            font-size: 12px;
            transition: 0.3s;
        }
        
        .btn-wa { background: var(--whatsapp); color: white; }
        .btn-snap { background: var(--snapchat); color: black; }
        .btn-x { background: var(--x-black); color: white; }

        .social-btn:hover { transform: scale(1.05); }

        header { background: #fff; padding: 15px; text-align: center; font-size: 20px; font-weight: bold; border-bottom: 4px solid var(--main); }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; max-width: 1200px; margin: auto; }
        .card { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; }
        .c-head { padding: 12px; background: #fcfcfc; display: flex; justify-content: space-between; font-weight: bold; }
        .live-tag { color: #22c55e; font-size: 13px; display: flex; align-items: center; gap: 5px; }
        .dot { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; animation: blink 1s infinite; }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.2; } 100% { opacity: 1; } }
        
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; }
        
        .sch { padding: 12px; border-top: 1px solid #eee; min-height: 70px; }
        .m-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eee; font-size: 13px; align-items: center; }
        .m-time { color: var(--main); font-weight: bold; font-size: 11px; background: #fff1f2; padding: 2px 5px; border-radius: 4px; }
    </style>
</head>
<body>

<div class="promo-sticky-container">
    <div class="promo-bar">
        <div class="promo-text">
            مقدم مجاناً من <strong>متجر الخدمة الرقمية</strong> | للاشتراك بالباقة الكاملة:
        </div>
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">
                <i class="fab fa-whatsapp"></i> واتساب
            </a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-snap">
                <i class="fab fa-snapchat"></i> سناب
            </a>
            <a href="https://x.com/d_service_pro?s=21" class="social-btn btn-x">
                <i class="fab fa-x-twitter"></i> X
            </a>
        </div>
    </div>
    <header>📺 بوابة الرياضة</header>
</div>

<div class="grid">
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card">
        <div class="c-head">
            <span>beIN Sport <?php echo $i; ?></span>
            <span class="live-tag"><span class="dot"></span> مباشر</span>
        </div>
        <video id="vid<?php echo $i; ?>" controls poster="https://via.placeholder.com/400x225/111/fff?text=beIN+Sports+<?php echo $i; ?>"></video>
        <button class="play-btn" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل الآن</button>
        <div class="sch" id="sch-<?php echo $i; ?>">
            <div style="text-align:center; color:#999; font-size:12px; padding:10px;">جاري تحديث الجدول...</div>
        </div>
    </div>
    <?php endfor; ?>
</div>

<script>
function play(id, s) {
    var v = document.getElementById(id);
    if (Hls.isSupported()) {
        var hls = new Hls(); hls.loadSource(s); hls.attachMedia(v); v.play();
    } else { v.src = s; v.play(); }
}

async function fetchMatches() {
    try {
        const response = await fetch('https://api.scorebat.com/video-api/v3/');
        const data = await response.json();
        const matches = data.response;
        for (let i = 1; i <= 9; i++) {
            const container = document.getElementById(`sch-${i}`);
            let m = matches[i - 1]; 
            if (m) {
                container.innerHTML = `<div class="m-row"><span>${m.title}</span><span class="m-time">LIVE</span></div>`;
            } else {
                container.innerHTML = '<div style="text-align:center; color:#ccc; font-size:11px;">لا توجد مباريات حالياً</div>';
            }
        }
    } catch (e) { console.log("Error"); }
}
window.onload = fetchMatches;
</script>
</body>
</html>
