<?php
session_start();
error_reporting(0);

// --- إعدادات السحابة عثمان ---
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$FOOTBALL_API_KEY = '895397d292e24b08cf4b107b68f52524'; 

// جلب بيانات القنوات عثمان
function getCloudData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $res = curl_exec($ch);
    return json_decode($res, true);
}

$cloud = getCloudData($BIN_ID, $API_KEY_BIN);
$active_sections = $cloud['sections'] ? array_filter($cloud['sections'], function($s) { return $s['status'] == 'show'; }) : [];
$all_channels = $cloud['custom_channels'] ?: [];

function filterCh($channels, $sec) {
    return array_filter($channels, function($c) use ($sec) { 
        return (isset($c['section']) && trim(strtolower($c['section'])) == trim(strtolower($sec))); 
    });
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --sky: #0ea5e9; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); padding-top: 180px; color: #fff; display: flex; justify-content: center; overflow-x: hidden; }
        .main-container { width: 100%; max-width: 500px; padding: 15px; }
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.98); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.1); padding: 15px 0; text-align: center; }
        .category-tabs { display: flex; gap: 10px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; }
        .cat-item { min-width: 75px; flex-shrink: 0; background: var(--glass); padding: 10px 5px; border-radius: 15px; cursor: pointer; text-align: center; font-size: 9px; font-weight: 900; border: 1px solid transparent; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 24px; height: 24px; display: block; margin: 0 auto 5px; }
        .grid { width: 100%; }
        .channel-section { display: none; }
        .channel-section.active { display: block; animation: fadeIn 0.5s; }
        .match-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 18px; padding: 15px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; }
        .m-league { position: absolute; top: -10px; right: 15px; background: var(--sky); font-size: 8px; padding: 2px 10px; border-radius: 50px; font-weight: 900; }
        .team { flex: 1; text-align: center; font-size: 11px; font-weight: 900; }
        .team img { width: 30px; height: 30px; display: block; margin: 0 auto 5px; }
        .info { flex: 0.8; text-align: center; }
        .score { font-size: 20px; font-weight: 900; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.1); }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: white; border: none; padding: 12px; border-radius: 50px; font-weight: 900; cursor: pointer; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>

<div class="main-container">
    <div class="header-fixed">
        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSec('matches', this)"><img src="https://cdn-icons-png.flaticon.com/512/33/33736.png"><span>المباريات</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSec('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="sec-matches" class="channel-section active">
            <div id="matches-list"><p style="text-align:center; padding:50px; opacity:0.5;">جاري جلب المباريات...</p></div>
        </div>

        <?php foreach($active_sections as $s): $channels = filterCh($all_channels, $s['key']); ?>
        <div id="sec-<?= $s['key'] ?>" class="channel-section">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="playS('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>')">تشغيل البث</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
const FB_KEY = '<?= $FOOTBALL_API_KEY ?>';
async function loadM() {
    const list = document.getElementById('matches-list');
    const day = new Date().toISOString().split('T')[0];
    try {
        const res = await fetch(`https://v3.football.api-sports.io/fixtures?date=${day}&timezone=Asia/Riyadh`, {
            headers: { 'x-apisports-key': FB_KEY }
        });
        const d = await res.json();
        const matches = d.response || [];
        if (matches.length === 0) {
            list.innerHTML = '<p style="text-align:center; padding:50px;">لا توجد مباريات حالياً.</p>';
            return;
        }
        list.innerHTML = '<h3 style="color:var(--sky); font-size:14px; margin-bottom:15px;">مباريات اليوم</h3>';
        matches.slice(0, 15).forEach(m => {
            const time = new Date(m.fixture.timestamp * 1000).toLocaleTimeString('ar-SA', { hour:'2-digit', minute:'2-digit', hour12:false });
            list.innerHTML += `<div class="match-card">
                <div class="m-league">${m.league.name}</div>
                <div class="team"><img src="${m.teams.home.logo}"><span>${m.teams.home.name}</span></div>
                <div class="info"><div class="score">${m.goals.home ?? 0} - ${m.goals.away ?? 0}</div><div style="font-size:11px;">${time}</div></div>
                <div class="team"><img src="${m.teams.away.logo}"><span>${m.teams.away.name}</span></div>
            </div>`;
        });
    } catch(e) { list.innerHTML = '<p style="text-align:center;">خطأ في الاتصال.</p>'; }
}
function switchSec(id, el) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('sec-' + id).classList.add('active'); el.classList.add('active');
}
function playS(id, f) { document.getElementById(id).innerHTML = `<iframe src="${f}?autoplay=1" style="width:100%; height:100%; border:none;" allowfullscreen></iframe>`; }
loadM();
</script>
</body>
</html>
