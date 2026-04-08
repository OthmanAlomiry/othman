<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة المباريات - الباقة المجانية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg: #050c14; --sky: #0ea5e9; --glass: rgba(255, 255, 255, 0.05); --main: #e11d48; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; padding: 20px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; }
        .header { margin-bottom: 25px; font-weight: 900; font-size: 18px; color: var(--sky); border-right: 5px solid var(--sky); padding-right: 15px; }
        .match-card { background: var(--glass); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 18px 10px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; }
        .league-label { position: absolute; top: -10px; right: 20px; background: var(--sky); font-size: 9px; padding: 3px 12px; border-radius: 50px; font-weight: 900; color: #fff; }
        .team { flex: 1.2; text-align: center; font-size: 11px; font-weight: 900; }
        .team img { width: 35px; height: 35px; display: block; margin: 0 auto 8px; }
        .info { flex: 0.9; text-align: center; border-left: 1px solid rgba(255,255,255,0.08); border-right: 1px solid rgba(255,255,255,0.08); margin: 0 5px; }
        .score { font-size: 22px; font-weight: 900; letter-spacing: 2px; }
        .time { font-size: 12px; color: var(--sky); font-weight: 900; }
        .no-data { text-align: center; padding: 40px; opacity: 0.6; background: var(--glass); border-radius: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">مباريات اليوم (الباقة المجانية)</div>
    <div id="matches-display">
        <div style="text-align:center; padding:50px;">جاري تحديث جدول اليوم...</div>
    </div>
</div>

<script>
// المفتاح الجديد الخاص بك عثمان
const API_KEY = '1e8894c7e946b851b36fea7f3a3c4d98';

async function loadMatches() {
    const display = document.getElementById('matches-display');
    // الحصول على تاريخ اليوم حصراً لتجنب رفض الباقة المجانية عثمان
    const today = new Date().toISOString().split('T')[0];

    try {
        const response = await fetch(`https://v3.football.api-sports.io/fixtures?date=${today}&timezone=Asia/Riyadh`, {
            method: 'GET',
            headers: { 'x-apisports-key': API_KEY }
        });
        
        const data = await response.json();
        const matches = data.response || [];

        if (matches.length === 0) {
            display.innerHTML = '<div class="no-data">لا توجد مباريات مجدولة لهذا التاريخ في الباقة المجانية.</div>';
            return;
        }

        display.innerHTML = '';
        // ترتيب الدوري السعودي (307) في المقدمة عثمان
        matches.sort((a, b) => (a.league.id === 307 ? -1 : 1));

        matches.forEach(m => {
            const time = new Date(m.fixture.timestamp * 1000).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit', hour12: false });
            const isLive = ['1H', '2H', 'HT', 'ET', 'P'].includes(m.fixture.status.short);
            
            display.innerHTML += `
                <div class="match-card">
                    <div class="league-label">${m.league.name}</div>
                    <div class="team"><img src="${m.teams.home.logo}"><span>${m.teams.home.name}</span></div>
                    <div class="info">
                        <div class="score" style="${isLive ? 'color:var(--main)' : ''}">${m.goals.home ?? 0} - ${m.goals.away ?? 0}</div>
                        <div class="time">${isLive ? 'مباشر' : time}</div>
                    </div>
                    <div class="team"><img src="${m.teams.away.logo}"><span>${m.teams.away.name}</span></div>
                </div>`;
        });
    } catch (err) {
        display.innerHTML = '<div class="no-data">حدث خطأ في جلب البيانات.</div>';
    }
}

loadMatches();
</script>
</body>
</html>
