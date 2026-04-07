<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');
$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));
$display_date = date('d / m / Y', strtotime($date_get));
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
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.1); }
        body { background: var(--bg); color: #fff; font-family: 'Tajawal', sans-serif; margin: 0; padding: 10px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; min-height: 100vh; }
        .date-picker { display: flex; align-items: center; justify-content: space-between; background: var(--glass); border: 1px solid var(--border); padding: 12px; border-radius: 20px; margin-bottom: 20px; backdrop-filter: blur(10px); }
        .date-picker a { color: #fff; text-decoration: none; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; background: var(--main); border-radius: 50%; transition: 0.3s; }
        .current-date h3 { margin: 0; font-size: 15px; font-weight: 900; color: var(--main); text-align: center;}
        .league-title { background: linear-gradient(90deg, var(--main), transparent); padding: 10px 15px; border-radius: 10px; font-size: 13px; font-weight: 900; margin: 25px 0 10px; border-right: 4px solid #fff; display: flex; align-items: center; gap: 8px; }
        .match-card { background: var(--glass); border: 1px solid var(--border); border-radius: 18px; padding: 15px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; transition: 0.3s; }
        .team { flex: 1.2; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 6px; }
        .team img { width: 35px; height: 35px; object-fit: contain; }
        .team b { font-size: 10px; color: #eee; }
        .info { flex: 1; text-align: center; display: flex; flex-direction: column; gap: 4px; }
        .score { font-size: 22px; font-weight: 900; color: #fff; letter-spacing: 2px; }
        .time { font-size: 11px; color: #38bdf8; font-weight: bold; }
        .live-tag { font-size: 9px; background: #e11d48; padding: 2px 8px; border-radius: 5px; animation: blink 1.2s infinite; width: fit-content; margin: 0 auto; }
        @keyframes blink { 50% { opacity: 0.4; } }
        #loader { text-align: center; padding: 50px; opacity: 0.6; }
    </style>
</head>
<body>

<div class="container">
    <div class="date-picker">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <div class="current-date"><h3><?= $display_date ?></h3></div>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <div id="loader"><i class="fas fa-spinner fa-spin"></i> جاري جلب المباريات المباشرة...</div>
    <div id="matches-container"></div>

    <footer style="text-align:center; padding:30px; font-size:10px; opacity:0.3;">تحديث لحظي - الخدمة الرقمية © 2026</footer>
</div>

<script>
async function loadMatches() {
    const date = '<?= $date_get ?>';
    const container = document.getElementById('matches-container');
    const loader = document.getElementById('loader');

    try {
        // نستخدم وسيط خارجي موثوق لتخطي الحماية عثمان
        const response = await fetch(`https://api.allorigins.win/get?url=${encodeURIComponent('https://ls.sport-mobi.com/api/v2/matches?date=' + date + '&timezone=3')}`);
        const rawData = await response.json();
        const data = JSON.parse(rawData.contents);
        
        loader.style.display = 'none';
        if (!data.data || data.data.length === 0) {
            container.innerHTML = '<div style="text-align:center; padding:50px; opacity:0.5;">لا توجد مباريات متاحة لهذا اليوم</div>';
            return;
        }

        // تجميع المباريات حسب الدوري عثمان
        const leagues = {};
        data.data.forEach(m => {
            const leagueName = m.league.name_ar || m.league.name;
            if (!leagues[leagueName]) leagues[leagueName] = [];
            leagues[leagueName].push(m);
        });

        for (const [name, matches] of Object.entries(leagues)) {
            let html = `<div class="league-title"><i class="fas fa-futbol"></i> ${name}</div>`;
            matches.forEach(m => {
                const isLive = m.status.type === 'live';
                const isFinished = m.status.type === 'finished';
                const time = new Date(m.start_at * 1000).toLocaleTimeString('ar-SA', {hour: '2-digit', minute:'2-digit', hour12:false});

                html += `
                <div class="match-card">
                    <div class="team">
                        <img src="${m.home_team.logo}" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                        <b>${m.home_team.name_ar || m.home_team.name}</b>
                    </div>
                    <div class="info">
                        ${isLive ? `<div class="score" style="color:#e11d48">${m.home_score} - ${m.away_score}</div><div class="live-tag">مباشر</div>` : 
                          isFinished ? `<div class="score">${m.home_score} - ${m.away_score}</div><div style="font-size:9px; opacity:0.5;">انتهت</div>` : 
                          `<div style="font-size:12px; font-weight:900; opacity:0.3;">VS</div><div class="time">${time}</div>`}
                    </div>
                    <div class="team">
                        <img src="${m.away_team.logo}" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                        <b>${m.away_team.name_ar || m.away_team.name}</b>
                    </div>
                </div>`;
            });
            container.innerHTML += html;
        }
    } catch (error) {
        loader.innerHTML = "<i class='fas fa-exclamation-triangle'></i> عذراً، جاري المحاولة مرة أخرى...";
        setTimeout(loadMatches, 3000); // إعادة محاولة تلقائية في حال الفشل عثمان
    }
}
loadMatches();
</script>
</body>
</html>
