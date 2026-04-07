<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// الحصول على التاريخ عثمان
$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$display_date = date('d / m / Y', strtotime($date_get));

// حساب التاريخ السابق والتالي
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --border: rgba(255, 255, 255, 0.1); }
        body { background: var(--bg); color: #fff; font-family: 'Tajawal', sans-serif; margin: 0; padding: 10px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; min-height: 100vh; }
        
        .date-picker { display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.03); border: 1px solid var(--border); padding: 12px; border-radius: 20px; margin-bottom: 20px; backdrop-filter: blur(10px); }
        .date-picker a { color: #fff; text-decoration: none; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; background: var(--main); border-radius: 50%; transition: 0.3s; }
        .current-date h3 { margin: 0; font-size: 15px; font-weight: 900; color: var(--main); }

        .league-row { background: linear-gradient(90deg, var(--main), transparent); padding: 8px 15px; border-radius: 10px; font-size: 13px; font-weight: 900; margin: 20px 0 10px; border-right: 4px solid #fff; }
        
        .match-box { background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 18px; padding: 15px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; }
        .team { flex: 1; text-align: center; font-size: 11px; font-weight: bold; }
        .m-info { flex: 1; text-align: center; }
        .m-score { font-size: 20px; font-weight: 900; letter-spacing: 2px; }
        .m-time { font-size: 11px; color: #38bdf8; }
        
        #loader { text-align: center; padding: 50px; opacity: 0.5; }
        .live-dot { color: #22c55e; font-size: 10px; animation: blink 1s infinite; }
        @keyframes blink { 50% { opacity: 0.3; } }
    </style>
</head>
<body>

<div class="container">
    <div class="date-picker">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <div class="current-date"><h3><?= $display_date ?></h3></div>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <div id="loader"><i class="fas fa-spinner fa-spin"></i> جاري جلب المباريات...</div>
    <div id="matches-list"></div>

    <footer style="text-align:center; padding:30px; font-size:10px; opacity:0.3;">الخدمة الرقمية © 2026</footer>
</div>

<script>
async function getMatches() {
    const list = document.getElementById('matches-list');
    const loader = document.getElementById('loader');
    const date = '<?= $date_get ?>';

    try {
        // سحب البيانات من مصدر مفتوح (فوت مبي) عثمان
        const res = await fetch(`https://api.allorigins.win/get?url=${encodeURIComponent('https://ls.sport-mobi.com/api/v2/matches?date=' + date + '&timezone=3')}`);
        const json = await res.json();
        const data = JSON.parse(json.contents);

        loader.style.display = 'none';
        if (!data.data || data.data.length === 0) {
            list.innerHTML = '<div style="text-align:center; padding:50px;">لا توجد مباريات جارية لهذا اليوم</div>';
            return;
        }

        const grouped = {};
        data.data.forEach(m => {
            const league = m.league.name_ar || m.league.name;
            if (!grouped[league]) grouped[league] = [];
            grouped[league].push(m);
        });

        for (const [leagueName, matches] of Object.entries(grouped)) {
            let html = `<div class="league-row">${leagueName}</div>`;
            matches.forEach(m => {
                const time = new Date(m.start_at * 1000).toLocaleTimeString('ar-SA', {hour:'2-digit', minute:'2-digit', hour12:false});
                const isLive = m.status.type === 'live';
                html += `
                <div class="match-box">
                    <div class="team">${m.home_team.name_ar || m.home_team.name}</div>
                    <div class="m-info">
                        <div class="m-score">${m.home_score} - ${m.away_score}</div>
                        <div class="m-time">${isLive ? '<span class="live-dot">● مباشر</span>' : time}</div>
                    </div>
                    <div class="team">${m.away_team.name_ar || m.away_team.name}</div>
                </div>`;
            });
            list.innerHTML += html;
        }
    } catch (e) {
        loader.innerHTML = "فشل جلب البيانات، جرب تحديث الصفحة.";
    }
}
getMatches();
</script>
</body>
</html>
