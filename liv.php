<?php
session_start();
$visitors_file = 'online_visitors.txt';
if (isset($_GET['fetch_visitors'])) {
    $session_id = session_id(); $time = time();
    $data = file_exists($visitors_file) ? unserialize(file_get_contents($visitors_file)) : [];
    $data[$session_id] = $time;
    foreach ($data as $id => $last_time) { if ($time - $last_time > 120) unset($data[$id]); }
    file_put_contents($visitors_file, serialize($data));
    echo count($data); exit; 
}
$online_now = file_exists($visitors_file) ? count(unserialize(file_get_contents($visitors_file))) : 1;

$manual_file = 'manual_channels.json';
$manual_channels = file_exists($manual_file) ? json_decode(file_get_contents($manual_file), true) : [];
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

function translate_name($text) {
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $response = @file_get_contents($url);
    if($response) { $result = json_decode($response, true); return $result[0][0][0] ?? $text; }
    return $text;
}

$ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $url); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $apiKey]);
$response = curl_exec($ch); curl_close($ch);
$match_data = json_decode($response, true);
date_default_timezone_set('Asia/Riyadh');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - متجر الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 310px; overflow-x: hidden; color: #e2e8f0; }
        .header-fixed-container { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 10px 0; text-align: center; }
        
        .online-count-badge { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 3px 15px; border-radius: 50px; color: #22c55e; font-size: 10px; font-weight: 900; display: inline-flex; align-items: center; gap: 5px; animation: badgePulse 2s infinite; margin-bottom: 10px; }
        @keyframes badgePulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); box-shadow: 0 0 15px rgba(34, 197, 94, 0.3); } 100% { transform: scale(1); } }
        .dot-blink { width: 7px; height: 7px; background: #22c55e; border-radius: 50%; animation: blink 1.5s infinite; }
        
        .promo-text { font-size: 11px; font-weight: 700; color: #fff; margin-bottom: 10px; }
        .social-links { display: flex; justify-content: center; gap: 8px; margin-bottom: 15px; flex-wrap: wrap; }
        .social-btn { padding: 7px 15px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; border: 1px solid rgba(255,255,255,0.15); display: flex; align-items: center; gap: 5px; }

        .category-tabs { display: flex; justify-content: flex-start; gap: 12px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 80px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 10px 5px; border-radius: 15px; cursor: pointer; transition: 0.3s; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: translateY(-3px); }
        .cat-item img { width: 40px; height: 40px; object-fit: contain; margin-bottom: 5px; }
        .cat-item span { font-size: 9px; font-weight: 900; color: #fff; display: block; }

        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        .bg-pattern-animated::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.05; animation: movePattern 60s linear infinite; }
        @keyframes movePattern { from { transform: translate(0, 0); } to { transform: translate(-50px, -50px); } }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 15px; }
        .channel-section { display: none; grid-column: 1/-1; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; }
        .channel-section.active { display: grid; animation: fadeIn 0.5s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); transition: 0.3s; }
        .c-head { padding: 15px; background: rgba(0,0,0,0.3); display: flex; justify-content: space-between; align-items: center; }
        .name-box { padding: 5px 15px; border-radius: 8px; color: #fff; font-weight: 900; font-size: 11px; }

        .play-btn { width: 90%; margin: 15px auto; display: block; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; animation: glassGlow 3s infinite; }
        @keyframes glassGlow { 0%, 100% { box-shadow: 0 0 5px rgba(255,255,255,0.05); } 50% { box-shadow: 0 0 20px rgba(255,255,255,0.15); } }
        
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .blink-live { color: #ff4d4d; animation: blink 1s infinite; font-weight: 900; font-size: 10px; }
        @keyframes blink { 50% { opacity: 0.1; } }
    </style>
</head>
<body>

<div class="bg-pattern-animated"></div>

<div class="header-fixed-container">
    <div class="online-count-badge"><div class="dot-blink"></div><span>متواجد الآن: <span id="realtime-visitors"><?php echo $online_now; ?></span></span></div>
    
    <div class="social-links">
        <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366"><i class="fab fa-whatsapp"></i> واتساب</a>
        <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc"><i class="fab fa-telegram-plane"></i> تليجرام</a>
        <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000"><i class="fab fa-snapchat"></i> سناب</a>
        <a href="https://x.com/d_service_pro" class="social-btn" style="background:#000"><i class="fab fa-x-twitter"></i> تويتر</a>
    </div>

    <div class="category-tabs">
        <div class="cat-item active" onclick="switchSection('bein', this)"><img src="mg/bein.png"><span>beIN</span></div>
        <div class="cat-item" onclick="switchSection('shahad', this)"><img src="mg/shahd.png"><span>شاهد</span></div>
        <div class="cat-item" onclick="switchSection('mbc', this)"><img src="mg/mbc.png"><span>MBC</span></div>
        <div class="cat-item" onclick="switchSection('alkas', this)"><img src="mg/alkas.png"><span>الكاس</span></div>
        <div class="cat-item" onclick="switchSection('kuwait', this)"><img src="mg/ku.png"><span>الكويت</span></div>
        <div class="cat-item" onclick="switchSection('dubai', this)"><img src="mg/du.png"><span>دبي</span></div>
    </div>
</div>

<div class="grid">
    <div id="section-alkas" class="channel-section">
        <div class="card">
            <div class="c-head"><div class="name-box" style="background: #f1c40f; color:#000">SHOOF LIVE</div><div class="blink-live">● مباشر</div></div>
            <video id="vsh" playsinline controls></video>
            <button class="play-btn" onclick="robustPlay('vsh', 'sh1.php', this)">تشغيل البث الداخلي</button>
        </div>
        </div>

    <div id="section-bein" class="channel-section active">
        <?php for($i=1; $i<=9; $i++): ?>
        <div class="card">
            <div class="c-head"><div class="name-box" style="background: #7c3aed">beIN Sport <?php echo $i; ?></div><div class="blink-live">● مباشر</div></div>
            <video id="vid<?php echo $i; ?>" playsinline controls></video>
            <button class="play-btn" onclick="robustPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', this)">تشغيل البث</button>
        </div>
        <?php endfor; ?>
    </div>
</div>

<script>
function updateRealtimeVisitors() { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(count => { if(count && !isNaN(count)) document.getElementById('realtime-visitors').innerText = count; }); }
setInterval(updateRealtimeVisitors, 4000);

function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}

function robustPlay(vId, p, btn) {
    const video = document.getElementById(vId);
    btn.innerText = "جاري التحميل...";
    if (video.hls) { video.hls.destroy(); }
    if (Hls.isSupported()) {
        const hls = new Hls({ xhrSetup: function (xhr) { xhr.withCredentials = false; } });
        hls.loadSource(p);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => { video.play(); btn.innerText = "تم التشغيل"; });
        video.hls = hls;
    } else { video.src = p; video.play(); btn.innerText = "تم التشغيل"; }
}
</script>
</body>
</html>
