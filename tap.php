<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة المباريات المباشرة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --sky: #0ea5e9; }
        
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; padding: 20px; display: flex; justify-content: center; min-height: 100vh; }
        .container { width: 100%; max-width: 480px; }
        
        /* الهيدر عثمان */
        .title-header { margin-bottom: 30px; font-weight: 900; font-size: 18px; color: var(--sky); border-right: 5px solid var(--sky); padding-right: 15px; display: flex; align-items: center; gap: 10px; }
        
        /* كرت المباراة المطور */
        .match-card { background: var(--glass); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 22px; padding: 20px 15px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; position: relative; transition: 0.3s; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .match-card:hover { border-color: var(--sky); transform: translateY(-3px); }
        
        .m-league { position: absolute; top: -12px; right: 25px; background: var(--sky); font-size: 10px; padding: 4px 14px; border-radius: 50px; font-weight: 900; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4); color: #fff; }
        
        .team { flex: 1.2; text-align: center; font-size: 12px; font-weight: 900; }
        .team img { width: 40px; height: 40px; display: block; margin: 0 auto 10px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5)); object-fit: contain; }
        
        .info { flex: 0.8; text-align: center; border-left: 1px solid rgba(255,255,255,0.05); border-right: 1px solid rgba(255,255,255,0.05); margin: 0 8px; }
        .score { font-size: 24px; font-weight: 900; font-family: 'Tajawal', sans-serif; letter-spacing: 3px; margin-bottom: 5px; }
        .time { font-size: 13px; color: var(--sky); font-weight: 900; }
        
        /* علامة المباشر عثمان */
        .live-tag { color: #22c55e; animation: blink 1s infinite; font-size: 10px; font-weight: 900; background: rgba(34, 197, 94, 0.1); padding: 2px 8px; border-radius: 5px; }
        
        #loader { text-align: center; padding: 80px 20px; opacity: 0.6; }
        @keyframes blink { 50% { opacity: 0.3; } }
        
        .notranslate { font-family: sans-serif !important; }
    </style>
</head>
<body>

<div class="container">
    <div class="title-header">
        <i class="fas fa-calendar-day"></i>
        <span>أهم مباريات اليوم</span>
    </div>
    
    <div id="matches-list">
        <div id="loader">
            <i class="fas fa-circle-notch fa-spin fa-3x" style="color:var(--sky); margin-bottom: 20px;"></i>
            <p>جاري جلب جدول المباريات المباشر...</p>
        </div>
    </div>
</div>

<script>
// الإعدادات الخاصة بك عثمان
const API_KEY = '895397d292e24b08cf4b107b68f52524';
const today = new Date().toISOString().split('T')[0];

async function fetchMatches() {
    const list = document.getElementById('matches-list');
    try {
        // جلب البيانات بتوقيت الرياض لضمان الدقة
        const response = await fetch(`https://v3.football.api-sports.io/fixtures?date=${today}&timezone=Asia/Riyadh`, {
            "method": "GET",
            "headers": { "x-apisports-key": API_KEY }
        });
        
        const data = await response.json();
        const matches = data.response || [];

        if (matches.length === 0) {
            list.innerHTML = '<div style="text-align:center; padding:50px; background:var(--glass); border-radius:20px;">لا توجد مباريات هامة مجدولة حالياً.</div>';
            return;
        }

        list.innerHTML = '';
        
        // فلترة الدوريات الكبرى (السعودي، أبطال أوروبا، الدوريات الخمس الكبرى) عثمان
        const importantLeagues = [307, 2, 3, 39, 140, 135, 78, 61, 5, 1];
        let filteredMatches = matches.filter(m => importantLeagues.includes(m.league.id));
        
        // إذا لم تتوفر مباريات كبرى، اعرض أول 15 مباراة متاحة من أي دوري
        if (filteredMatches.length === 0) filteredMatches = matches.slice(0, 15);

        filteredMatches.forEach(m => {
            const time = new Date(m.fixture.timestamp * 1000).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit', hour12: false });
            const status = m.fixture.status.short;
            const isLive = ['1H', '2H', 'HT', 'ET', 'P'].includes(status);
            
            list.innerHTML += `
                <div class="match-card">
                    <div class="m-league">${m.league.name}</div>
                    <div class="team">
                        <img src="${m.teams.home.logo}" alt="logo">
                        <span>${m.teams.home.name}</span>
                    </div>
                    <div class="info">
                        <div class="score notranslate" style="${isLive ? 'color:var(--main)' : ''}">
                            ${m.goals.home ?? 0} - ${m.goals.away ?? 0}
                        </div>
                        <div class="time">
                            ${isLive ? '<span class="live-tag">مباشر الآن</span>' : (status === 'FT' ? 'انتهت' : time)}
                        </div>
                    </div>
                    <div class="team">
                        <img src="${m.teams.away.logo}" alt="logo">
                        <span>${m.teams.away.name}</span>
                    </div>
                </div>
            `;
        });
    } catch (e) {
        list.innerHTML = '<div style="text-align:center; padding:50px;">فشل الاتصال بالمصدر. تأكد من إعدادات الـ API.</div>';
    }
}

// تشغيل الوظيفة وتحديثها تلقائياً عثمان
fetchMatches();
setInterval(fetchMatches, 120000); // تحديث كل دقيقتين
</script>

</body>
</html>
