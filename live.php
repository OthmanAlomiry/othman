<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة -  القنوات</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --main: #e11d48; --bg: #f8fafc; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); }
        header { background: #fff; padding: 25px; text-align: center; font-size: 24px; font-weight: bold; border-bottom: 4px solid var(--main); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; max-width: 1300px; margin: auto; }
        .card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #f1f5f9; }
        .c-head { padding: 15px; background: #f8fafc; display: flex; justify-content: space-between; font-weight: bold; }
        .dot { width: 10px; height: 10px; background: #22c55e; border-radius: 50%; display: inline-block; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .btn-play { width: 92%; margin: 15px auto; display: block; padding: 13px; border: none; border-radius: 12px; background: var(--main); color: white; font-size: 16px; font-weight: bold; cursor: pointer; }
        .sch { padding: 15px; border-top: 1px solid #f1f5f9; min-height: 80px; }
        .m-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #e2e8f0; font-size: 14px; align-items: center; }
        .m-time { color: var(--main); font-weight: bold; background: #fff1f2; padding: 3px 10px; border-radius: 8px; font-size: 12px; }
        .loading-text { text-align: center; color: #94a3b8; font-size: 12px; padding: 10px; }
    </style>
</head>
<body>

<header>📺 بث مباشر - جدول مباريات اليوم</header>

<div class="grid">
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card">
        <div class="c-head">
            <span>beIN Sport <?php echo $i; ?></span>
            <span style="color:#22c55e"><span class="dot"></span> مباشر</span>
        </div>
        <video id="vid<?php echo $i; ?>" controls poster="https://via.placeholder.com/400x225/111/fff?text=beIN+Sports+<?php echo $i; ?>"></video>
        <button class="btn-play" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل البث المباشر</button>
        <div class="sch" id="sch-<?php echo $i; ?>">
            <div class="loading-text">جاري مزامنة المباريات الحقيقية...</div>
        </div>
    </div>
    <?php endfor; ?>
</div>

<script>
// دالة تشغيل الفيديو
function play(id, src) {
    var v = document.getElementById(id);
    if (Hls.isSupported()) {
        var hls = new Hls(); hls.loadSource(src); hls.attachMedia(v); v.play();
    } else { v.src = src; v.play(); }
}

// الجلب عبر المتصفح لتخطي حظر السيرفر
async function fetchMatchesRealTime() {
    const apiKey = "49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9";
    const today = new Date().toISOString().split('T')[0];

    try {
        const response = await fetch(`https://api-football-v1.p.rapidapi.com/v3/fixtures?date=${today}&timezone=Asia/Riyadh`, {
            "method": "GET",
            "headers": {
                "x-rapidapi-host": "api-football-v1.p.rapidapi.com",
                "x-rapidapi-key": apiKey
            }
        });
        
        const data = await response.json();
        const matches = data.response;

        if (matches && matches.length > 0) {
            for (let i = 1; i <= 9; i++) {
                const container = document.getElementById(`sch-${i}`);
                let html = '<strong style="font-size:12px; color:#64748b; display:block; margin-bottom:10px;">📅 جدول البث الحقيقي:</strong>';
                
                // توزيع المباريات الحقيقية على القنوات
                let m = matches[(i - 1) % matches.length];
                let time = new Date(m.fixture.date).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit', hour12: false });
                
                html += `
                    <div class="m-row">
                        <span>${m.teams.home.name} × ${m.teams.away.name}</span>
                        <span class="m-time">${time}</span>
                    </div>`;
                container.innerHTML = html;
            }
        } else {
            document.querySelectorAll('.loading-text').forEach(el => el.innerText = "لا توجد مباريات دولية منقولة حالياً");
        }
    } catch (error) {
        document.querySelectorAll('.loading-text').forEach(el => el.innerText = "حدث خطأ أثناء مزامنة البيانات");
    }
}

// تفعيل الجلب عند التحميل
window.onload = fetchMatchesRealTime;
</script>

</body>
</html>
