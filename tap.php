<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>جدول المباريات - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --sky: #0ea5e9; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; padding: 20px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; }
        .title-header { margin-bottom: 25px; font-weight: 900; font-size: 17px; color: var(--sky); border-right: 4px solid var(--sky); padding-right: 12px; display: flex; align-items: center; gap: 10px; }
        .match-card { background: var(--glass); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 18px 10px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; transition: 0.3s; }
        .m-league { position: absolute; top: -10px; right: 20px; background: var(--sky); font-size: 9px; padding: 3px 12px; border-radius: 50px; font-weight: 900; color: #fff; box-shadow: 0 4px 10px rgba(14, 165, 233, 0.3); }
        .team { flex: 1.2; text-align: center; font-size: 11px; font-weight: 900; }
        .team img { width: 35px; height: 35px; display: block; margin: 0 auto 8px; filter: drop-shadow(0 2px 5px rgba(0,0,0,0.5)); }
        .info { flex: 0.9; text-align: center; border-left: 1px solid rgba(255,255,255,0.08); border-right: 1px solid rgba(255,255,255,0.08); margin: 0 5px; }
        .score { font-size: 22px; font-weight: 900; font-family: sans-serif; letter-spacing: 2px; }
        .time { font-size: 13px; color: var(--sky); font-weight: 900; }
        .live-tag { color: #22c55e; animation: blink 1s infinite; font-size: 9px; font-weight: 900; display: block; margin-top: 4px; }
        #loader { text-align: center; padding: 60px; opacity: 0.5; }
        @keyframes blink { 50% { opacity: 0.4; } }
    </style>
</head>
<body>

<div class="container">
    <div class="title-header"><i class="fas fa-trophy"></i> مباريات اليوم (تشمل الدوري السعودي)</div>
    <div id="matches-list">
        <div id="loader"><i class="fas fa-spinner fa-spin fa-2x"></i><p>جاري تحديث البيانات...</p></div>
    </div>
</div>

<script>
// العودة لمفتاحك القوي في API-Football لضمان الدوري السعودي عثمان
const API_KEY = '895397d292e24b08cf4b107b68f52524';

async function fetchMatches() {
    const list = document.getElementById('matches-list');
    const today = new Date().toISOString().split('T')[0];
    
    try {
        const response = await fetch(`https://v3.football.api-sports.io/fixtures?date=${today}&timezone=Asia/Riyadh`, {
            "method": "GET",
            "headers": { "x-apisports-key": API_KEY }
        });
        const data = await response.json();
        const matches = data.response || [];

        if (matches.length === 0) {
            list.innerHTML = '<div style="text-align:center; padding:40px; opacity:0.5;">لا توجد مباريات مسجلة حالياً.</div>';
            return;
        }

        list.innerHTML = '';
        
        // الدوريات المستهدفة: السعودي (307)، أبطال آسيا (281)، والدوريات الكبرى
        const targetLeagues = [307, 281, 2, 3, 39, 140, 135, 78, 61, 1];
        
        // ترتيب المباريات: نجعل الدوري السعودي يظهر أولاً عثمان
        matches.sort((a, b) => (a.league.id === 307 ? -1 : 1));

        let count = 0;
        matches.forEach(m => {
            if (targetLeagues.includes(m.league.id) || count < 10) {
                const time = new Date(m.fixture.timestamp * 1000).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit', hour12: false });
                const status = m.fixture.status.short;
                const isLive = ['1H', '2H', 'HT', 'ET', 'P'].includes(status);
                
                list.innerHTML += `
                    <div class="match-card">
                        <div class="m-league">${m.league.name}</div>
                        <div class="team"><img src="${m.teams.home.logo}"><span class="notranslate">${m.teams.home.name}</span></div>
                        <div class="info">
                            <div class="score notranslate" style="${isLive ? 'color:var(--main)' : ''}">${m.goals.home ?? 0} - ${m.goals.away ?? 0}</div>
                            <div class="time">${isLive ? '<span class="live-tag">مباشر الآن</span>' : (status === 'FT' ? 'انتهت' : time)}</div>
                        </div>
                        <div class="team"><img src="${m.teams.away.logo}"><span class="notranslate">${m.teams.away.name}</span></div>
                    </div>
                `;
                count++;
            }
        });
    } catch (e) {
        list.innerHTML = '<div style="text-align:center; padding:40px;">حدث خطأ في جلب البيانات.</div>';
    }
}

fetchMatches();
setInterval(fetchMatches, 120000);
</script>
</body>
</html>
