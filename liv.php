<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة - جلب تلقائي نهائي</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <style>
        :root { --main: #e11d48; --bg: #f8fafc; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); }
        header { background: #fff; padding: 20px; text-align: center; font-size: 22px; font-weight: bold; border-bottom: 4px solid var(--main); box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 15px; max-width: 1200px; margin: auto; }
        .card { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; transition: 0.3s; }
        .c-head { padding: 12px; background: #fcfcfc; display: flex; justify-content: space-between; font-weight: bold; }
        .live-tag { color: #22c55e; font-size: 13px; display: flex; align-items: center; gap: 5px; }
        .dot { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; animation: blink 1s infinite; }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.2; } 100% { opacity: 1; } }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; }
        .sch { padding: 12px; border-top: 1px solid #eee; min-height: 90px; }
        .m-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eee; font-size: 13px; align-items: center; }
        .m-time { color: var(--main); font-weight: bold; font-size: 11px; background: #fff1f2; padding: 2px 5px; border-radius: 4px; }
        .loading { text-align: center; color: #999; font-size: 12px; padding: 20px; }
    </style>
</head>
<body>

<header>📺 بوابة الرياضة - جدول المباريات الذكي</header>

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
            <div class="loading">يتم الآن جلب جدول القناة...</div>
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

// دالة الجلب التلقائي الذكي من مصدر عالمي مفتوح
async function fetchMatches() {
    try {
        const response = await fetch('https://api.scorebat.com/video-api/v3/');
        const data = await response.json();
        const matches = data.response;

        for (let i = 1; i <= 9; i++) {
            const container = document.getElementById(`sch-${i}`);
            let html = '<strong style="font-size:11px; color:#888; display:block; margin-bottom:5px;">📅 مباريات منقولة اليوم:</strong>';
            
            // توزيع المباريات: كل قناة تأخذ مباراة مختلفة من القائمة المجلوبة
            // إذا كانت القائمة تحتوي على مباريات كافية، سيتم عرضها
            let m1 = matches[i - 1]; 
            let m2 = matches[i + 8]; 

            if (m1) {
                html += `<div class="m-row"><span>${m1.title}</span><span class="m-time">LIVE</span></div>`;
            }
            if (m2) {
                html += `<div class="m-row"><span>${m2.title}</span><span class="m-time">بث مباشر</span></div>`;
            }
            
            if (!m1 && !m2) {
                html += '<div class="loading">لا توجد مباريات مسجلة حالياً</div>';
            }
            
            container.innerHTML = html;
        }
    } catch (e) {
        console.log("Fetch Error");
        // في حال فشل الجلب، نعرض رسالة ثابتة بدلاً من التحميل اللانهائي
        for (let i = 1; i <= 9; i++) {
            document.getElementById(`sch-${i}`).innerHTML = '<div class="loading">المباريات قيد التحديث...</div>';
        }
    }
}

// البدء بالجلب بمجرد تحميل الصفحة
window.onload = fetchMatches;
</script>
</body>
</html>
