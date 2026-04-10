<?php
session_start();
error_reporting(0);

// --- بيانات السحابة الخاصة بك عثمان ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69d6f6b636566621a891e6c1';

// دالة فحص الإشعارات (AJAX)
if(isset($_GET['check_notify'])) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $BIN_ID . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $API_KEY, "X-Bin-Meta: false"]);
    $res = json_decode(curl_exec($ch), true);
    $notify = $res['notification'];
    if (isset($notify['time']) && (time() - $notify['time'] > 172800)) { echo json_encode(null); } 
    else { echo json_encode($notify); }
    exit;
}

// --- نظام عداد المتواجدين ---
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

// --- دالة جلب البيانات السحابية الكاملة ---
function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$cloud = getCloudFullData($BIN_ID, $API_KEY);
$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : [];
$active_sections = array_filter($cloud['sections'] ?: [], function($s) { return $s['status'] == 'show'; });
$news = isset($cloud['news_ticker']) ? $cloud['news_ticker'] : ['text' => '', 'status' => 'hide'];

function filterSection($channels, $sec) {
    return array_filter($channels, function($c) use ($sec) { return (isset($c['section']) && trim(strtolower($c['section'])) == trim(strtolower($sec))); });
}
date_default_timezone_set('Asia/Riyadh');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); --blue-grad: linear-gradient(45deg, #0ea5e9, #fff); }
        
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; transition: 0.3s; }

        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; transition: max-width 0.4s; }

        /* شاشة الدخول */
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e293b 0%, #050c14 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.2); }
        .loader-content { display: flex; flex-direction: column; align-items: center; }
        .intro-icon-box { width: 100px; height: 100px; background: var(--main); border-radius: 30%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 50px rgba(225, 29, 72, 0.5); animation: bounceIn 1s ease-out, glowPulse 2s infinite ease-in-out; }
        .intro-icon-box i { font-size: 50px; color: white; }
        .intro-title { margin-top: 25px; color: white; font-weight: 900; font-size: 24px; letter-spacing: 1px; text-shadow: 0 5px 15px rgba(0,0,0,0.5); }
        .loading-bar { width: 150px; height: 4px; background: rgba(255,255,255,0.1); border-radius: 10px; margin-top: 30px; overflow: hidden; position: relative; }
        .loading-bar::after { content: ""; position: absolute; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, var(--main), transparent); animation: loadingMove 1.5s infinite; }
        @keyframes glowPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        @keyframes loadingMove { 100% { left: 100%; } }
        @keyframes bounceIn { 0% { opacity: 0; transform: scale(0.3); } 50% { opacity: 1; transform: scale(1.1); } 100% { transform: scale(1); } }

        /* الهيدر الثابت */
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; transition: max-width 0.4s; }
        
        .notify-bell-btn { position: fixed; bottom: 85px; left: 25px; width: 45px; height: 45px; background: var(--main); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; z-index: 5000; box-shadow: 0 8px 20px rgba(225, 29, 72, 0.4); cursor: pointer; border: 2px solid rgba(255,255,255,0.1); }
        .visitors-badge-float { position: fixed; bottom: 25px; left: 25px; width: 45px; height: 45px; background: rgba(34, 197, 94, 0.15); backdrop-filter: blur(10px); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; border: 1.5px solid #22c55e; }

        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; flex-wrap: nowrap; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; transition: 0.3s; flex: 1; text-align: center; border: 1.5px solid #ffffff; }
        .btn-wa { background: #25d366; } .btn-tg { background: #0088cc; } .btn-sn { background: #FFFC00; color: #000 !important; } .btn-tw { background: #000; }

        .news-ticker { background: rgba(225, 29, 72, 0.15); border-top: 1px solid rgba(255, 255, 255, 0.05); border-bottom: 1px solid rgba(255, 255, 255, 0.05); height: 32px; overflow: hidden; margin-bottom: 10px; display: flex; align-items: center; position: relative; }
        .ticker-label { background: var(--main); color: #fff; padding: 0 12px; height: 100%; display: flex; align-items: center; font-size: 10px; font-weight: 900; z-index: 10; position: absolute; right: 0; }
        .ticker-wrap { flex: 1; overflow: hidden; direction: ltr; display: flex; align-items: center; }
        .ticker-move { display: flex; white-space: nowrap; animation: ticker-infinite 60s linear infinite; width: max-content; }
        .ticker-text { color: #fff; font-size: 12px; font-weight: 700; padding: 0 60px; }
        @keyframes ticker-infinite { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }

        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: scale(1.03); }
        .cat-item img { width: 26px; height: 26px; object-fit: contain; margin-bottom: 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; display: block; }

        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; animation: slideUp 0.6s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); backdrop-filter: blur(10px); margin-bottom: 20px; width: 100%; }
        .c-head { padding: 12px; background: rgba(0,0,0,0.4); display: flex; justify-content: space-between; align-items: center; }
        .name-badge { padding: 5px 12px; border-radius: 10px; font-size: 10px; font-weight: 900; color: #000; background: var(--blue-grad); }
        .live-badge { display: flex; align-items: center; gap: 5px; background: rgba(225, 29, 72, 0.2); padding: 4px 10px; border-radius: 8px; border: 1px solid var(--main); color: #ff4d4d; font-weight: 900; font-size: 10px; }
        .live-dot { width: 6px; height: 6px; background: #ff4d4d; border-radius: 50%; animation: pulse-red 1.2s infinite; }

        .play-btn { width: 90%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 8px; background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; font-size: 13px; transition: 0.4s; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; background-image: url('mg/wel.GIF'); background-size: cover; background-position: center; position: relative; }
        iframe { width: 100%; height: 100%; border: none; background: #000; }

        /* ستايل المشغل المزدوج المصغر عثمان */
        .dual-container { padding: 5px; display: flex; flex-direction: column; align-items: center; gap: 15px; }
        .dual-slot { background: rgba(0,0,0,0.3); border-radius: 15px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05); width: 85%; }
        .dual-screen-v { width: 100%; aspect-ratio: 16/9; background-color: #000; position: relative; background-size: cover; background-position: center; }
        
        /* صور خلفية المشغل المزدوج عثمان */
        #dual-screen-1 { background-image: url('mg/chn1.png'); }
        #dual-screen-2 { background-image: url('mg/chn2.png'); }

        .dual-btn-select { width: 100%; padding: 10px; background: #111827; border: none; color: #38bdf8; font-family: 'Tajawal'; font-weight: 700; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; }
        
        /* قائمة اختيار القنوات عثمان */
        .ch-picker-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); backdrop-filter: blur(15px); z-index: 7000; display: none; align-items: center; justify-content: center; }
        .ch-picker-window { width: 95%; max-width: 480px; background: #0f172a; border-radius: 25px; border: 1px solid var(--glass-border); max-height: 80vh; overflow: hidden; display: flex; flex-direction: column; }
        .ch-picker-header { padding: 20px; background: var(--main); color: white; font-weight: 900; font-size: 16px; display: flex; justify-content: space-between; align-items: center; }
        .ch-picker-list { padding: 15px; overflow-y: auto; display: grid; grid-template-columns: 1fr; gap: 10px; }
        .ch-pick-item { background: rgba(255,255,255,0.08); padding: 18px; border-radius: 15px; font-size: 14px; font-weight: 700; text-align: right; border: 1.5px solid rgba(255,255,255,0.1); cursor: pointer; color: #fff; transition: 0.2s; display: flex; align-items: center; gap: 15px; }

        .bg-pattern { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        footer { text-align: center; padding: 40px; font-size: 10px; opacity: 0.5; }
    </style>
</head>
<body>

<div id="pro-intro">
    <div class="loader-content">
        <div class="intro-icon-box"><i class="fas fa-play-circle"></i></div>
        <h2 class="intro-title">الخدمة الرقمية</h2>
        <div class="loading-bar"></div>
    </div>
</div>

<div class="ch-picker-overlay" id="ch-picker">
    <div class="ch-picker-window">
        <div class="ch-picker-header"><span>📺 قائمة القنوات</span><i class="fas fa-times" onclick="closePicker()" style="cursor:pointer;"></i></div>
        <div class="ch-picker-list">
            <?php foreach($all_channels as $ch): ?>
                <div class="ch-pick-item" onclick="confirmPick('<?= $ch['file'] ?>', '<?= $ch['name'] ?>')"><?= $ch['name'] ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="main-container">
    <div id="notify-panel" style="position:fixed; bottom:85px; left:50%; transform:translateX(-50%); width:280px; max-height:350px; background:rgba(15,23,42,0.95); border-radius:20px; z-index:5500; display:none; flex-direction:column; border:1px solid var(--glass-border);">
        <div style="padding:12px; background:var(--main); color:white; border-radius:20px 20px 0 0; display:flex; justify-content:space-between;"><span>🔔 الإشعارات</span><i class="fas fa-times" onclick="toggleNotifyPanel()"></i></div>
        <div id="panel-list" style="overflow-y:auto; padding:10px;"></div>
    </div>

    <div class="notify-bell-btn" onclick="toggleNotifyPanel()"><i class="fas fa-bell"></i><div class="notify-dot" id="n-dot"></div></div>
    <div class="visitors-badge-float"><i class="fas fa-users" style="color:#22c55e;"></i> <span id="realtime-visitors"><?php echo $online_now; ?></span></div>

    <div class="bg-pattern"></div>

    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-sn">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-tw">تويتر</a>
        </div>

        <?php if($news['status'] == 'show'): ?>
        <div class="news-ticker"><span class="ticker-label">تنبيهات</span><div class="ticker-wrap"><div class="ticker-move"><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span></div></div></div>
        <?php endif; ?>

        <div class="category-tabs">
            <?php $sections_list = array_values($active_sections); ?>
            <?php if(isset($sections_list[0])): ?>
                <div class="cat-item active" onclick="switchSection('<?= $sections_list[0]['key'] ?>', this)"><img src="<?= $sections_list[0]['img'] ?>"><span><?= $sections_list[0]['name'] ?></span></div>
            <?php endif; ?>
            <div class="cat-item" onclick="switchSection('dual_player', this)"><img src="mg/ch2.png"><span>تشغيل مباريتين</span></div>
            <?php for($i=1; $i < count($sections_list); $i++): ?>
                <div class="cat-item" onclick="switchSection('<?= $sections_list[$i]['key'] ?>', this)"><img src="<?= $sections_list[$i]['img'] ?>"><span><?= $sections_list[$i]['name'] ?></span></div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-dual_player" class="channel-section">
            <div style="background: rgba(14, 165, 233, 0.1); border: 1px dashed #0ea5e9; border-radius: 12px; padding: 10px; margin: 35px auto 15px; text-align: center; width: 85%;">
                <p style="font-size: 10px; font-weight: 700; color: #38bdf8; margin: 0;">أكتم صوت القناة بالضغط على ( <i class="fas fa-volume-mute"></i> ) لتشغيل القناة الاخرى</p>
            </div>

            <div class="dual-container">
                <div class="dual-slot">
                    <div class="dual-screen-v" id="dual-screen-1"></div>
                    <button class="dual-btn-select" onclick="openPicker(1)"><i class="fas fa-tv"></i> <span id="btn-text-1">اختر القناة الأولى</span></button>
                </div>
                <div class="dual-slot">
                    <div class="dual-screen-v" id="dual-screen-2"></div>
                    <button class="dual-btn-select" onclick="openPicker(2)"><i class="fas fa-tv"></i> <span id="btn-text-2">اختر القناة الثانية</span></button>
                </div>
            </div>
        </div>

        <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section <?= ($s['key'] == $sections_list[0]['key'] ? 'active' : '') ?>">
            <?php foreach($channels as $ch): ?>
            <div class="card"><div class="c-head"><div class="name-badge"><?= $ch['name'] ?></div><div class="live-badge"><div class="live-dot"></div> مباشر</div></div><div class="video-box" id="vid-<?= $ch['id'] ?>"></div><button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)"><i class="fas fa-play-circle"></i> تشغيل البث المباشر</button></div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>
</div>

<script>
let lastNotifyId = localStorage.getItem('last_id') || "";
let activeSlot = 0; 
function toggleNotifyPanel() { let p = document.getElementById('notify-panel'); p.style.display = (p.style.display === 'flex') ? 'none' : 'flex'; }
function openPicker(slot) { activeSlot = slot; document.getElementById('ch-picker').style.display = 'flex'; }
function closePicker() { document.getElementById('ch-picker').style.display = 'none'; }
function confirmPick(url, name) {
    if(!url) return;
    let screen = document.getElementById('dual-screen-' + activeSlot);
    screen.style.backgroundImage = "none"; // إزالة الصورة عند التشغيل
    screen.innerHTML = `<iframe src="${url}?autoplay=1&muted=1" allowfullscreen></iframe>`;
    document.getElementById('btn-text-' + activeSlot).innerText = name;
    closePicker();
}
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}
function startStream(boxId, file, btn) {
    let vBox = document.getElementById(boxId); vBox.style.backgroundImage = "none";
    vBox.innerHTML = `<iframe src="${file}?autoplay=1&muted=1" allowfullscreen></iframe>`;
    btn.innerHTML = '<i class="fas fa-check-circle"></i> تم الاتصال'; btn.style.background = "#1e293b";
}
window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1500); });
setInterval(() => { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(c => { document.getElementById('realtime-visitors').innerText = c; }); }, 5000);
</script>
</body>
</html>
