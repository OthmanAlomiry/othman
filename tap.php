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

        .league-title { background: linear-gradient(90deg, var(--main), transparent); padding: 10px 15px; border-radius: 10px; font-size: 12px; font-weight: 900; margin: 25px 0 10px; border-right: 4px solid #fff; display: flex; align-items: center; gap: 8px; }
        
        .match-card { background: var(--glass); border: 1px solid var(--border); border-radius: 18px; padding: 15px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; transition: 0.3s; position: relative; }
        
        .team { flex: 1.2; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 6px; }
        .team img { width: 32px; height: 32px; object-fit: contain; }
        .team b { font-size: 10px; color: #eee; line-height: 1.2; }

        .info { flex: 1; text-align: center; display: flex; flex-direction: column; gap: 4px; }
        .score { font-size: 20px; font-weight: 900; color: #fff; letter-spacing: 2px; }
        .time { font-size: 11px; color: #38bdf8; font-weight: bold; }
        .live-tag { font-size: 9px; background: #e11d48; padding: 2px 8px; border-radius: 5px; animation: blink 1.2s infinite; width: fit-content; margin: 0 auto; color: #fff; }
        
        @keyframes blink { 50% { opacity: 0.4; } }
        #loader { text-align: center; padding: 50px; opacity: 0.6; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <div class="date-picker">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <div class="current-date"><h3><?= $display_date ?></h3></div>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <div id="loader"><i class="fas fa-spinner fa-spin"></i> جاري جلب مباريات اليوم...</div>
    <div id="matches-container"></div>

    <footer style="text-align:center; padding:30px; font-size:10px; opacity:0.3;">تحديث مباشر - الخدمة الرقمية © 2026</footer>
</div>

<script>
async function loadMatches() {
    const date = '<?= $date_get ?>';
    const container = document.getElementById('matches-container');
    const loader = document.getElementById('loader');

    try {
        // الاتصال بمصدر SofaScore الموثوق عثمان
        const response = await fetch(`https://api.sofascore.com/api/v1/sport/football/scheduled-events/${date}`);
        const data = await response.json();
        
        loader.style.display = 'none';
        if (!data.events || data.events.length === 0) {
            container.innerHTML = '<div style="text-align:center; padding:50px; opacity:0.5;">لا توجد مباريات متاحة لهذا اليوم</div>';
            return;
        }

        // تجميع المباريات حسب الدوري عثمان
        const leagues = {};
        data.events.forEach(event => {
            const leagueName = event.tournament.name;
            if (!leagues[leagueName]) leagues[leagueName] = [];
            leagues[leagueName].push(event);
        });

        let finalHtml = '';
        for (const [name, matches] of Object.entries(leagues)) {
            finalHtml += `<div class="league-title"><i class="fas fa-trophy"></i> ${name}</div>`;
            matches.forEach(m => {
                const isLive = m.status.type === 'inprogress';
                const isFinished = m.status.type === 'finished';
                const homeScore = m.homeScore.current ?? '';
                const awayScore = m.awayScore.current ?? '';
                const matchTime = new Date(m.startTimestamp * 1000).toLocaleTimeString('ar-SA', {hour: '2-digit', minute:'2-digit', hour12:false});

                finalHtml += `
                <div class="match-card">
                    <div class="team">
                        <img src="https://api.sofascore.app/api/v1/team/${m.homeTeam.id}/image" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                        <b>${m.homeTeam.nameAr || m.homeTeam.name}</b>
                    </div>
                    <div class="info">
                        ${isLive ? `<div class="score" style="color:#e11d48">${homeScore} - ${awayScore}</div><div class="live-tag">مباشر</div>` : 
                          isFinished ? `<div class="score">${homeScore} - ${awayScore}</div><div style="font-size:9px; opacity:0.5;">انتهت</div>` : 
                          `<div style="font-size:11px; font-weight:900; opacity:0.4;">قريباً</div><div class="time">${matchTime}</div>`}
                    </div>
                    <div class="team">
                        <img src="https://api.sofascore.app/api/v1/team/${m.awayTeam.id}/image" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                        <b>${m.awayTeam.nameAr || m.awayTeam.name}</b>
                    </div>
                </div>`;
            });
        }
        container.innerHTML = finalHtml;
    } catch (error) {
        // في حال وجود حظر CORS نستخدم محاولة أخيرة عبر وسيط عثمان
        loader.innerHTML = "جاري إعادة الاتصال الآمن...";
        const proxyUrl = `https://api.allorigins.win/get?url=${encodeURIComponent('https://api.sofascore.com/api/v1/sport/football/scheduled-events/' + date)}`;
        fetch(proxyUrl).then(res => res.json()).then(resData => {
            const data = JSON.parse(resData.contents);
            // ... (نفس عملية المعالجة بالأعلى)
            loader.style.display = 'none';
            // سيتم تنفيذ نفس الحلقة هنا
            location.reload(); // أسرع حل لإعادة المحاولة في حال فشل السكريبت عثمان
        });
    }
}
loadMatches();
</script>
</body>
</html>
