<script>
const FOOTBALL_KEY = '<?= $FOOTBALL_API_KEY ?>';

async function loadMatches() {
    const list = document.getElementById('matches-list');
    // جلب تاريخ اليوم وتاريخ الغد عثمان لضمان ظهور بيانات
    const today = new Date().toISOString().split('T')[0];
    
    try {
        // محاولة جلب مباريات اليوم
        const res = await fetch(`https://v3.football.api-sports.io/fixtures?date=${today}&timezone=Asia/Riyadh`, {
            headers: { 'x-apisports-key': FOOTBALL_KEY }
        });
        const data = await res.json();
        let matches = data.response || [];
        
        // إذا كانت القائمة فارغة، سنحاول جلب أي مباريات قادمة عثمان
        if (matches.length === 0) {
            const nextRes = await fetch(`https://v3.football.api-sports.io/fixtures?next=15&timezone=Asia/Riyadh`, {
                headers: { 'x-apisports-key': FOOTBALL_KEY }
            });
            const nextData = await nextRes.json();
            matches = nextData.response || [];
        }

        if (matches.length === 0) {
            list.innerHTML = '<div style="text-align:center; padding:50px; opacity:0.5;">لا توجد مباريات حالياً في المصدر.</div>';
            return;
        }

        list.innerHTML = '<div style="margin-bottom:15px; font-weight:900; color:var(--sky); padding-right:10px; border-right:3px solid var(--sky);">جدول المباريات المتاحة</div>';
        
        matches.forEach(m => {
            const time = new Date(m.fixture.timestamp * 1000).toLocaleTimeString('ar-SA', { hour:'2-digit', minute:'2-digit', hour12:false });
            const isLive = ['1H','2H','HT','ET','P'].includes(m.fixture.status.short);
            const statusDesc = isLive ? '<span style="color:#22c55e;">مباشر الآن</span>' : (m.fixture.status.short === 'FT' ? 'انتهت' : time);
            
            list.innerHTML += `
                <div class="match-card">
                    <div class="m-league">${m.league.name}</div>
                    <div class="m-team"><img src="${m.teams.home.logo}"><span>${m.teams.home.name}</span></div>
                    <div class="m-info">
                        <div class="m-score" style="${isLive ? 'color:var(--main)' : ''}">${m.goals.home ?? 0} - ${m.goals.away ?? 0}</div>
                        <div class="m-time">${statusDesc}</div>
                    </div>
                    <div class="m-team"><img src="${m.teams.away.logo}"><span>${m.teams.away.name}</span></div>
                </div>
            `;
        });
    } catch(e) {
        list.innerHTML = '<div style="text-align:center; padding:50px;">تأكد من مفتاح API أو جودة الاتصال.</div>';
    }
}

// استدعاء الوظائف عثمان
loadMatches();
</script>
