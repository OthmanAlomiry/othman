<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة المباريات - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg: #050c14; --sky: #0ea5e9; --glass: rgba(255, 255, 255, 0.05); --main: #e11d48; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; padding: 20px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; }
        .header { margin-bottom: 25px; font-weight: 900; font-size: 18px; color: var(--sky); border-right: 5px solid var(--sky); padding-right: 15px; }
        .match-card { background: var(--glass); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 18px 10px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; }
        .league-label { position: absolute; top: -10px; right: 20px; background: var(--sky); font-size: 9px; padding: 3px 12px; border-radius: 50px; font-weight: 900; }
        .team { flex: 1.2; text-align: center; font-size: 11px; font-weight: 900; }
        .team img { width: 35px; height: 35px; display: block; margin: 0 auto 8px; filter: drop-shadow(0 2px 5px rgba(0,0,0,0.5)); }
        .info { flex: 0.9; text-align: center; border-left: 1px solid rgba(255,255,255,0.08); border-right: 1px solid rgba(255,255,255,0.08); margin: 0 5px; }
        .score { font-size: 22px; font-weight: 900; }
        .time { font-size: 13px; color: var(--sky); font-weight: 900; }
        #debug-info { background: #1e293b; padding: 10px; border-radius: 10px; font-size: 10px; color: #94a3b8; margin-top: 20px; display: none; direction: ltr; text-align: left; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">مباريات اليوم</div>
    <div id="matches-display">
        <div style="text-align:center; padding:50px; opacity:0.5;">جاري فحص الاتصال بالمصدر...</div>
    </div>
    <div id="debug-info"></div>
</div>

<script>
const API_KEY = '895397d292e24b08cf4b107b68f52524';

async function checkApi() {
    const display = document.getElementById('matches-display');
    const debug = document.getElementById('debug-info');
    const today = new Date().toISOString().split('T')[0];

    try {
        const response = await fetch(`https://v3.football.api-sports.io/fixtures?date=${today}&timezone=Asia/Riyadh`, {
            method: 'GET',
            headers: { 'x-apisports-key': API_KEY }
        });
        
        const data = await response.json();
        
        // إظهار معلومات الديباج إذا كان هناك خطأ عثمان
        if (data.errors && Object.keys(data.errors).length > 0) {
            debug.style.display = 'block';
            debug.innerText = "Error from API: " + JSON.stringify(data.errors);
            display.innerHTML = '<div style="text-align:center; padding:30px;">خطأ في إعدادات الـ API. راجع رسالة الديباج في الأسفل.</div>';
            return;
        }

        const matches = data.response || [];
        if (matches.length === 0) {
            display.innerHTML = '<div style="text-align:center; padding:40px; opacity:0.5;">لا توجد مباريات مسجلة اليوم في باقتك.</div>';
            return;
        }

        display.innerHTML = '';
        matches.sort((a, b) => (a.league.id === 307 ? -1 : 1));

        matches.slice(0, 20).forEach(m => {
            const time = new Date(m.fixture.timestamp * 1000).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit', hour12: false });
            const isLive = ['1H', '2H', 'HT', 'ET', 'P'].includes(m.fixture.status.short);
            
            display.innerHTML += `
                <div class="match-card">
                    <div class="league-label">${m.league.name}</div>
                    <div class="team"><img src="${m.teams.home.logo}"><span>${m.teams.home.name}</span></div>
                    <div class="info">
                        <div class="score" style="${isLive ? 'color:var(--main)' : ''}">${m.goals.home ?? 0} - ${m.goals.away ?? 0}</div>
                        <div class="time">${isLive ? 'مباشر' : (m.fixture.status.short === 'FT' ? 'انتهت' : time)}</div>
                    </div>
                    <div class="team"><img src="${m.teams.away.logo}"><span>${m.teams.away.name}</span></div>
                </div>`;
        });

    } catch (err) {
        display.innerHTML = '<div style="text-align:center; padding:30px;">فشل الاتصال تماماً. جرب فتح الصفحة من متصفح آخر.</div>';
    }
}

checkApi();
</script>
</body>
</html>
