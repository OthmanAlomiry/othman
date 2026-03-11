<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة - مباريات اليوم الحقيقية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --main: #e11d48; --bg: #f8fafc; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); }
        header { background: #fff; padding: 20px; text-align: center; font-size: 22px; font-weight: bold; border-bottom: 4px solid var(--main); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 15px; max-width: 1200px; margin: auto; }
        .card { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #eee; }
        .c-head { padding: 12px; background: #fcfcfc; display: flex; justify-content: space-between; font-weight: bold; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .btn-play { width: 90%; margin: 12px auto; display: block; background: var(--main); color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; }
        .sch { padding: 12px; border-top: 1px solid #eee; min-height: 60px; }
        .m-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eee; font-size: 14px; align-items: center; }
        .m-time { color: var(--main); font-weight: bold; background: #fff1f2; padding: 2px 8px; border-radius: 5px; font-size: 11px; }
        .status { text-align: center; font-size: 12px; color: #999; padding: 10px; }
    </style>
</head>
<body>

<header>📺 بث مباشر - جدول مباريات اليوم</header>

<div class="grid">
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card">
        <div class="c-head">
            <span>beIN Sport <?php echo $i; ?></span>
            <span style="color:#22c55e">● مباشر</span>
        </div>
        <video id="vid<?php echo $i; ?>" controls></video>
        <button class="btn-play" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل البث</button>
        <div class="sch" id="sch-<?php echo $i; ?>">
            <div class="status">جاري البحث عن مباريات اليوم...</div>
        </div>
    </div>
    <?php endfor; ?>
</div>

<script>
function play(id, src) {
    var v = document.getElementById(id);
    if (Hls.isSupported()) {
        var hls = new Hls(); hls.loadSource(src); hls.attachMedia(v); v.play();
    } else { v.src = src; v.play(); }
}

async function getTodayMatches() {
    const apiKey = "49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9";
    
    // الحصول على تاريخ اليوم بتنسيق YYYY-MM-DD بدقة
    const now = new Date();
    const offset = now.getTimezoneOffset() * 60000;
    const localISODate = new Date(now - offset).toISOString().split('T')[0];

    try {
        const response = await fetch(`https://api-football-v1.p.rapidapi.com/v3/fixtures?date=${localISODate}&timezone=Asia/Riyadh`, {
            "method": "GET",
            "headers": {
                "x-rapidapi-host": "api-football-v1.p.rapidapi.com",
                "x-rapidapi-key": apiKey
            }
        });

        const result = await response.json();
        const matches = result.response;

        if (matches && matches.length > 0) {
            // فرز المباريات حسب التوقيت لتبدأ بالأقرب
            matches.sort((a, b) => new Date(a.fixture.date) - new Date(b.fixture.date));

            for (let i = 1; i <= 9; i++) {
                const container = document.getElementById(`sch-${i}`);
                // توزيع المباريات بالتوالي على القنوات لضمان التنوع
                const m = matches[(i - 1) % matches.length];
                
                const time = new Date(m.fixture.date).toLocaleTimeString('ar-SA', { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    hour12: false 
                });

                container.innerHTML = `
                    <div class="m-row">
                        <span>${m.teams.home.name} × ${m.teams.away.name}</span>
                        <span class="m-time">${time}</span>
                    </div>
                `;
            }
        } else {
            document.querySelectorAll('.status').forEach(el => el.innerHTML = "لم يتم العثور على مباريات مسجلة لهذا التاريخ");
        }
    } catch (error) {
        document.querySelectorAll('.status').forEach(el => el.innerHTML = "خطأ في الاتصال بالبيانات");
    }
}

window.onload = getTodayMatches;
</script>
</body>
</html>
