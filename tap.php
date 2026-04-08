<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>جدول مباريات اليوم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --sky: #0ea5e9; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; padding: 15px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; }
        .title-header { margin-bottom: 20px; font-weight: 900; font-size: 16px; color: var(--sky); border-right: 4px solid var(--sky); padding-right: 12px; }
        .match-card { background: var(--glass); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 18px; padding: 15px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; position: relative; transition: 0.3s; }
        .m-league { position: absolute; top: -10px; right: 15px; background: var(--sky); font-size: 8px; padding: 2px 10px; border-radius: 50px; font-weight: 900; }
        .m-team { flex: 1.2; text-align: center; font-size: 11px; font-weight: 900; }
        .m-team img { width: 30px; height: 30px; display: block; margin: 0 auto 5px; }
        .m-info { flex: 0.8; text-align: center; }
        .m-score { font-size: 20px; font-weight: 900; font-family: sans-serif; margin-bottom: 3px; }
        .m-time { font-size: 12px; color: var(--sky); font-weight: 900; }
        .m-live { color: #22c55e; animation: blink 1s infinite; font-size: 9px; }
        #loader { text-align: center; padding: 50px; opacity: 0.5; font-size: 14px; }
        @keyframes blink { 50% { opacity: 0.5; } }
    </style>
</head>
<body>

<div class="container">
    <div class="title-header">جدول مباريات اليوم</div>
    <div id="matches-list">
        <div id="loader"><i class="fas fa-spinner fa-spin"></i> جاري جلب البيانات...</div>
    </div>
</div>

<script>
// إعدادات الـ API الخاصة بك يا عثمان
const API_KEY = 'd6c1b4f231cf6d72aacf0c6cfe61efa5';
const today = new Date().toISOString().split('T')[0];

async function fetchMatches() {
    const list = document.getElementById('matches-list');
    try {
        // محاولة جلب مباريات اليوم بتوقيت الرياض
        const response = await fetch(`https://v3.football.api-sports.io/fixtures?date=${today}&timezone=Asia/Riyadh`, {
            "method": "GET",
            "headers": { "x-apisports-key": API_KEY }
        });
        const data = await response.json();
        const matches = data.response || [];

        if (matches.length === 0) {
            list.innerHTML = '<div style="text-align:center; padding:30px;">لا توجد مباريات هامة مسجلة حالياً.</div>';
            return;
        }

        list.innerHTML = '';
        
        // فلترة أهم الدوريات (أبطال أوروبا، الإنجليزي، الإسباني، السعودي)
        const importantIDs = [307, 2, 3, 39, 140, 135, 78, 61, 5, 1];
        let filtered = matches.filter(m => importantIDs.includes(m.league.id));
        
        // إذا لم تكن هناك مباريات كبرى، اعرض أول 15 مباراة متاحة
        if (filtered.length === 0) filtered = matches.slice(0, 15);

        filtered.forEach(m => {
            const time = new Date(m.fixture.timestamp * 1000).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit', hour12: false });
            const status = m.fixture.status.short;
            const isLive = ['1H', '2H', 'HT', 'ET', 'P'].includes(status);
            
            list.innerHTML += `
                <div class="match-card">
                    <div class="m-league">${m.league.name}</div>
                    <div class="m-team"><img src="${m.teams.home.logo}"><span>${m.teams.home.name}</span></div>
                    <div class="m-info">
                        <div class="m-score" style="${isLive ? 'color:var(--main)' : ''}">${m.goals.home ?? ''} - ${m.goals.away ?? ''}</div>
                        <div class="m-time">${isLive ? '<span class="m-live">مباشر</span>' : (status === 'FT' ? 'انتهت' : time)}</div>
                    </div>
                    <div class="m-team"><img src="${m.teams.away.logo}"><span>${m.teams.away.name}</span></div>
                </div>
            `;
        });
    } catch (error) {
        list.innerHTML = '<div style="text-align:center; padding:30px;">حدث خطأ أثناء جلب البيانات، تأكد من اتصال الإنترنت.</div>';
    }
}

fetchMatches();
</script>

</body>
</html>
