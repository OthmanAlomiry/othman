<?php
session_start();
error_reporting(E_ALL); // تم تفعيل الأخطاء داخلياً للفحص
ini_set('display_errors', 0); // عدم إظهارها للزائر عثمان

// --- إعدادات السحابة عثمان ---
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$FOOTBALL_API_KEY = 'd6c1b4f231cf6d72aacf0c6cfe61efa5'; 

// --- نظام الإشعارات والزوار عثمان ---
if(isset($_GET['check_notify'])) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $BIN_ID . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $API_KEY_BIN, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    header('Content-Type: application/json');
    echo $res; exit;
}

if (isset($_GET['fetch_visitors'])) {
    $visitors_file = 'online_visitors.txt';
    $session_id = session_id(); $time = time();
    $data = file_exists($visitors_file) ? unserialize(file_get_contents($visitors_file)) : [];
    $data[$session_id] = $time;
    foreach ($data as $id => $last_time) { if ($time - $last_time > 120) unset($data[$id]); }
    file_put_contents($visitors_file, serialize($data));
    echo count($data); exit; 
}

// --- جلب بيانات القنوات من السحابة عثمان ---
function getCloudData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$cloud = getCloudData($BIN_ID, $API_KEY_BIN);
$active_sections = isset($cloud['sections']) ? array_filter($cloud['sections'], function($s) { return $s['status'] == 'show'; }) : [];
$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : [];

function filterSection($channels, $sec) {
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
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); padding-top: 220px; color: #fff; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; }
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.98); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.1); padding: 15px 0; text-align: center; }
        .category-tabs { display: flex; gap: 10px; width: 95%; margin: 0 auto; overflow-x: auto; padding: 5px 0; scrollbar-width: none; }
        .cat-item { min-width: 75px; flex-shrink: 0; background: var(--glass); padding: 10px 5px; border-radius: 15px; cursor: pointer; text-align: center; border: 1px solid transparent; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 26px; height: 26px; display: block; margin: 0 auto 5px; }
        .cat-item span { font-size: 9px; font-weight: 900; }
        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; }
        .match-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 18px; padding: 15px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; }
        .m-league { position: absolute; top: -10px; right: 15px; background: var(--sky); font-size: 8px; padding: 3px 10px; border-radius: 50px; font-weight: 900; }
        .m-team { flex: 1.2; text-align: center; font-size: 11px; font-weight: 900; }
        .m-team img { width: 30px; height: 30px; display: block; margin: 0 auto 5px; }
        .m-info { flex: 0.8; text-align: center; }
        .m-score { font-size: 22px; font-weight: 900; }
        .m-time { font-size: 13px; color: var(--sky); font-weight: 900; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.1); }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; position: relative; }
        iframe { width: 100%; height: 100%; border: none; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: white; border: none; padding: 12px; border-radius: 50px; font-weight: 900; cursor: pointer; }
    </style>
</head>
<body>

<div class="main-container">
    <div class="header-fixed">
        <div style="display:flex; justify-content:space-around; margin-bottom:15px; padding:0 10px;">
            <a href="https://wa.me/966505571164" style="background:#25d366; color:white; padding:6px 15px; border-radius:20px; font-size:10px; text-decoration:none; font-weight:bold;">واتساب</a>
            <a href="https://t.me/d_s_pro" style="background:#0088cc; color:white; padding:6px 15px; border-radius:20px; font-size:10px; text-decoration:none; font-weight:bold;">تليجرام</a>
        </div>
        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches', this)">
                <img src="https://cdn-icons-png.flaticon.com/512/33/33736.png"><span>المباريات</span>
            </div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)">
                    <img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches" class="channel-section active">
            <div id="matches-list">
                <div style="text-align:center; padding:50px; opacity:0.5;">جاري تحميل مباريات اليوم...</div>
            </div>
        </div>

        <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>')">تشغيل البث المباشر</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
const FOOTBALL_KEY = '<?= $FOOTBALL_API_KEY ?>';

async function loadMatches() {
    const list = document.getElementById('matches-list');
    const today = new Date().toISOString().split('T')[0];
    try {
        const res = await fetch(`https://v3.football.api-sports.io/fixtures?date=${today}&timezone=Asia/Riyadh`, {
            headers: { 'x-apisports-key': FOOTBALL_KEY }
        });
        const data = await res.json();
        const matches = data.response || [];
        
        if (matches.length === 0) {
            list.innerHTML = '<div style="text-align:center; padding:50px; opacity:0.5;">لا توجد مباريات هامة اليوم.</div>';
            return;
        }

        list.innerHTML = '<div style="margin-bottom:15px; font-weight:900; color:var(--sky); padding-right:10px; border-right:3px solid var(--sky);">أهم مباريات اليوم</div>';
        
        // إظهار أهم 15 مباراة عثمان
        matches.slice(0, 15).forEach(m => {
            const time = new Date(m.fixture.timestamp * 1000).toLocaleTimeString('ar-SA', { hour:'2-digit', minute:'2-digit', hour12:false });
            const isLive = ['1H','2H','HT','ET','P'].includes(m.fixture.status.short);
            list.innerHTML += `
                <div class="match-card">
                    <div class="m-league">${m.league.name}</div>
                    <div class="m-team"><img src="${m.teams.home.logo}"><span>${m.teams.home.name}</span></div>
                    <div class="m-info">
                        <div class="m-score" style="${isLive ? 'color:var(--main)' : ''}">${m.goals.home ?? ''} - ${m.goals.away ?? ''}</div>
                        <div class="m-time">${isLive ? '<span style="color:#22c55e;">مباشر</span>' : time}</div>
                    </div>
                    <div class="m-team"><img src="${m.teams.away.logo}"><span>${m.teams.away.name}</span></div>
                </div>
            `;
        });
    } catch(e) {
        list.innerHTML = '<div style="text-align:center; padding:50px;">فشل تحديث البيانات.</div>';
    }
}

function switchSection(id, el) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    el.classList.add('active');
}

function startStream(boxId, file) {
    document.getElementById(boxId).innerHTML = `<iframe src="${file}?autoplay=1" allowfullscreen></iframe>`;
}

loadMatches();
</script>
</body>
</html>
