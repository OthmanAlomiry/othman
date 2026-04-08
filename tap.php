<script>
const FB_KEY = '895397d292e24b08cf4b107b68f52524';

async function loadM() {
    const list = document.getElementById('matches-list');
    const day = new Date().toISOString().split('T')[0];
    
    try {
        // 1. محاولة جلب مباريات اليوم
        let res = await fetch(`https://v3.football.api-sports.io/fixtures?date=${day}&timezone=Asia/Riyadh`, {
            headers: { 'x-apisports-key': FB_KEY }
        });
        let d = await res.json();
        let matches = d.response || [];

        // 2. إذا لم يجد مباريات اليوم، سيجلب أقرب 15 مباراة قادمة عثمان
        if (matches.length === 0) {
            res = await fetch(`https://v3.football.api-sports.io/fixtures?next=15&timezone=Asia/Riyadh`, {
                headers: { 'x-apisports-key': FB_KEY }
            });
            d = await res.json();
            matches = d.response || [];
        }

        if (matches.length === 0) {
            list.innerHTML = '<p style="text-align:center; padding:50px; opacity:0.5;">لا توجد مباريات مسجلة حالياً في المصدر.</p>';
            return;
        }

        list.innerHTML = '<h3 style="color:var(--sky); font-size:14px; margin-bottom:15px; border-right:3px solid var(--sky); padding-right:10px;">جدول المباريات المتاحة</h3>';
        
        // ترتيب الدوري السعودي (307) ليكون في الأعلى دائماً عثمان
        matches.sort((a, b) => (a.league.id === 307 ? -1 : 1));

        matches.forEach(m => {
            const time = new Date(m.fixture.timestamp * 1000).toLocaleTimeString('ar-SA', { hour:'2-digit', minute:'2-digit', hour12:false });
            const isLive = ['1H','2H','HT','ET','P'].includes(m.fixture.status.short);
            
            list.innerHTML += `
                <div class="match-card">
                    <div class="m-league">${m.league.name}</div>
                    <div class="team"><img src="${m.teams.home.logo}" onerror="this.src='https://cdn-icons-png.flaticon.com/512/33/33736.png'"><span>${m.teams.home.name}</span></div>
                    <div class="info">
                        <div class="score" style="${isLive ? 'color:var(--main)' : ''}">${m.goals.home ?? 0} - ${m.goals.away ?? 0}</div>
                        <div style="font-size:11px; font-weight:900;">${isLive ? '<span style="color:#22c55e;">مباشر</span>' : time}</div>
                    </div>
                    <div class="team"><img src="${m.teams.away.logo}" onerror="this.src='https://cdn-icons-png.flaticon.com/512/33/33736.png'"><span>${m.teams.away.name}</span></div>
                </div>`;
        });
    } catch(e) { 
        list.innerHTML = '<p style="text-align:center;">خطأ في الاتصال بالمصدر.</p>'; 
    }
}

function switchSec(id, el) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('sec-' + id).classList.add('active');
    el.classList.add('active');
}

function playS(id, f) { 
    document.getElementById(id).innerHTML = `<iframe src="${f}?autoplay=1" style="width:100%; height:100%; border:none;" allowfullscreen></iframe>`; 
}

loadM();
</script>
