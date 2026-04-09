<?php
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// --- بيانات السحابة الجديدة عثمان ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69d6f03436566621a891c500';

// دالة فحص الإشعارات (AJAX) عثمان
if(isset($_GET['check_notify'])) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $BIN_ID . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Master-Key: " . $API_KEY, "X-Bin-Meta: false"));
    $res = json_decode(curl_exec($ch), true);
    $notify = (isset($res['notification'])) ? $res['notification'] : null;
    if ($notify && isset($notify['time']) && (time() - $notify['time'] > 172800)) { echo json_encode(null); } 
    else { echo json_encode($notify); }
    exit;
}

// --- نظام عداد المتواجدين عثمان ---
$visitors_file = 'online_visitors.txt';
if (isset($_GET['fetch_visitors'])) {
    $session_id = session_id(); $time = time();
    $data = file_exists($visitors_file) ? unserialize(file_get_contents($visitors_file)) : array();
    $data[$session_id] = $time;
    foreach ($data as $id => $last_time) { if ($time - $last_time > 120) unset($data[$id]); }
    file_put_contents($visitors_file, serialize($data));
    echo count($data); exit; 
}
$online_now = file_exists($visitors_file) ? count(unserialize(file_get_contents($visitors_file))) : 1;

// --- دالة جلب البيانات السحابية الكاملة عثمان ---
function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Master-Key: " . $key, "X-Bin-Meta: false"));
    $res = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($res, true);
    return (isset($result['record'])) ? $result['record'] : $result;
}

$cloud = getCloudFullData($BIN_ID, $API_KEY);
$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : array();
$active_sections = array_filter((isset($cloud['sections']) ? $cloud['sections'] : array()), function($s) { return $s['status'] == 'show'; });
$news = isset($cloud['news_ticker']) ? $cloud['news_ticker'] : array('text' => '', 'status' => 'hide');

function filterSection($channels, $sec) {
    return array_filter($channels, function($c) use ($sec) { return (isset($c['section']) && trim(strtolower($c['section'])) == trim(strtolower($sec))); });
}
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
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e293b 0%, #050c14 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.2); }
        .loader-content { display: flex; flex-direction: column; align-items: center; }
        .intro-icon-box { width: 100px; height: 100px; background: var(--main); border-radius: 30%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 50px rgba(225, 29, 72, 0.5); animation: bounceIn 1s ease-out, glowPulse 2s infinite ease-in-out; }
        .intro-icon-box i { font-size: 50px; color: white; }
        .intro-title { margin-top: 25px; color: white; font-weight: 900; font-size: 24px; letter-spacing: 1px; text-shadow: 0 5px 15px rgba(0,0,0,0.5); }
        .loading-bar { width: 150px; height: 4px; background: rgba(255,255,255,0.1); border-radius: 10px; margin-top: 30px; overflow: hidden; position: relative; }
        .loading-bar::after { content: ""; position: absolute; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, var(--main), transparent); animation: loadingMove 1.5s infinite; }
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; transition: max-width 0.4s; }
        .notify-bell-btn { position: fixed; bottom: 85px; left: 25px; width: 45px; height: 45px; background: var(--main); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; z-index: 5000; box-shadow: 0 8px 20px rgba(225, 29, 72, 0.4); cursor: pointer; border: 2px solid rgba(255,255,255,0.1); }
        .notify-dot { position: absolute; top: 2px; right: 2px; width: 12px; height: 12px; background: #22c55e; border-radius: 50%; border: 2px solid var(--bg-deep); display: none; }
        .visitors-badge-float { position: fixed; bottom: 25px; left: 25px; width: 45px; height: 45px; background: rgba(34, 197, 94, 0.15); backdrop-filter: blur(10px); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; border: 1.5px solid #22c55e; box-shadow: 0 8px 20px rgba(34, 197, 94, 0.2); }
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; flex-wrap: nowrap; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; border: 1.5px solid #ffffff; }
        .news-ticker { background: rgba(225, 29, 72, 0.15); border-top: 1px solid rgba(255, 255, 255, 0.05); border-bottom: 1px solid rgba(255, 255, 255, 0.05); height: 32px; overflow: hidden; margin-bottom: 10px; display: flex; align-items: center; position: relative; }
        .ticker-label { background: var(--main); color: #fff; padding: 0 12px; height: 100%; display: flex; align-items: center; font-size: 10px; font-weight: 900; z-index: 10; position: absolute; right: 0; }
        .ticker-wrap { flex: 1; overflow: hidden; direction: ltr; display: flex; align-items: center; }
        .ticker-move { display: flex; white-space: nowrap; animation: ticker-infinite 60s linear infinite; width: max-content; }
        .ticker-text { color: #fff; font-size: 12px; font-weight: 700; padding: 0 60px; }
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: scale(1.03); }
        .cat-item img { width: 26px; height: 26px; object-fit: contain; margin-bottom: 4px; }
        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); margin-bottom: 20px; width: 100%; }
        .c-head { padding: 12px; background: rgba(0,0,0,0.4); display: flex; justify-content: space-between; align-items: center; }
        .name-badge { padding: 5px 12px; border-radius: 10px; font-size: 10px; font-weight: 900; color: #000; background: var(--blue-grad); }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; font-size: 13px; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; background-image: url('mg/wel.GIF'); background-size: cover; position: relative; }
        iframe { width: 100%; height: 100%; border: none; background: #000; }
        .bg-pattern { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        footer { text-align: center; padding: 40px; font-size: 10px; opacity: 0.5; }
        @keyframes ticker-infinite { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }
        @keyframes loadingMove { 100% { left: 100%; } }
        @keyframes bounceIn { 0% { opacity: 0; transform: scale(0.3); } 50% { opacity: 1; transform: scale(1.1); } 100% { transform: scale(1); } }
    </style>
</head>
<body>
<div id="pro-intro"><div class="loader-content"><div class="intro-icon-box"><i class="fas fa-play-circle"></i></div><h2 class="intro-title">الخدمة الرقمية</h2><div class="loading-bar"></div></div></div>
<div class="main-container">
    <div id="notify-panel" style="position:fixed; bottom:85px; left:50%; transform:translateX(-50%); width:280px; max-height:350px; background:rgba(15,23,42,0.95); border-radius:20px; z-index:5500; display:none; flex-direction:column; border:1px solid var(--glass-border);">
        <div style="background:var(--main); color:white; padding:12px; font-weight:900; border-radius:20px 20px 0 0; display:flex; justify-content:space-between;"><span>🔔 الإشعارات</span><i class="fas fa-times" onclick="toggleNotifyPanel()" style="cursor:pointer;"></i></div>
        <div id="panel-list" style="overflow-y:auto; padding:10px;"></div>
    </div>
    <div class="notify-bell-btn" onclick="toggleNotifyPanel()"><i class="fas fa-bell"></i><div class="notify-dot" id="n-dot"></div></div>
    <div class="visitors-badge-float"><i class="fas fa-users"></i><span id="realtime-visitors"><?php echo $online_now; ?></span></div>
    <div class="bg-pattern"></div>
    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn" style="background:#000">تويتر</a>
        </div>
        <?php if($news['status'] == 'show'): ?><div class="news-ticker"><span class="ticker-label">تنبيهات</span><div class="ticker-wrap"><div class="ticker-move"><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span></div></div></div><?php endif; ?>
        <div class="category-tabs">
            <?php $count = 0; foreach($active_sections as $s): ?>
                <div class="cat-item <?= ($count == 0 ? 'active' : '') ?>" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php $count++; endforeach; ?>
        </div>
    </div>
    <div class="grid">
        <?php $count = 0; foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section <?= ($count == 0 ? 'active' : '') ?>">
            <?php if(empty($channels)): ?><div style="text-align:center; padding:80px; opacity:0.3;"><p>لا توجد قنوات حالياً.</p></div><?php endif; ?>
            <?php foreach($channels as $ch): ?>
            <div class="card"><div class="c-head"><div class="name-badge"><?= $ch['name'] ?></div><div style="color:#ff4d4d; font-size:10px; font-weight:900;">مباشر</div></div><div class="video-box" id="vid-<?= $ch['id'] ?>"></div><button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)"><i class="fas fa-play-circle"></i> تشغيل البث المباشر</button></div>
            <?php endforeach; ?>
        </div>
        <?php $count++; endforeach; ?>
    </div>
    <footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>
</div>
<script>
let lastNotifyId = localStorage.getItem('last_id') || "";
function toggleNotifyPanel() { let p = document.getElementById('notify-panel'); p.style.display = (p.style.display==='flex')?'none':'flex'; document.getElementById('n-dot').style.display='none'; }
function checkNotifications() { fetch(window.location.pathname + '?check_notify=1').then(res => res.json()).then(data => { if(data && data.id && data.id !== lastNotifyId) { lastNotifyId = data.id; localStorage.setItem('last_id', data.id); document.getElementById('n-dot').style.display='block'; } }); }
function switchSection(id, element) { document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active')); document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active')); document.getElementById('section-' + id).classList.add('active'); element.classList.add('active'); }
function startStream(boxId, file, btn) { let vBox = document.getElementById(boxId); vBox.style.backgroundImage = "none"; vBox.innerHTML = `<iframe src="${file}?autoplay=1&muted=1" allow="autoplay; encrypted-media" allowfullscreen></iframe>`; btn.innerHTML = '<i class="fas fa-check-circle"></i> تم الاتصال'; btn.style.background = "#1e293b"; }
window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 2000); checkNotifications(); });
setInterval(() => { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(c => { document.getElementById('realtime-visitors').innerText = c; }); }, 4000);
</script>
</body>
</html>
