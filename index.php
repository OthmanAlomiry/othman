<?php
session_start();
error_reporting(0);

// --- بيانات السحابة الخاصة بك عثمان ---
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';

// --- إعدادات API المباريات (عثمان) ---
$FOOTBALL_API_KEY = 'd6c1b4f231cf6d72aacf0c6cfe61efa5'; 
$cache_file = "matches_v2.json"; // تم تغيير الاسم لتحديث البيانات فوراً
$cache_time = 900; 

function getTodayMatches($key, $file, $time) {
    if (file_exists($file) && (time() - filemtime($file) < $time)) {
        $cached_data = json_decode(file_get_contents($file), true);
        if (!empty($cached_data)) return $cached_data;
    }
    $date = date('Y-m-d');
    $ch = curl_init("https://v3.football.api-sports.io/fixtures?date=$date");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["x-apisports-key: " . $key]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $res = json_decode(curl_exec($ch), true);
    if (isset($res['response']) && !empty($res['response'])) {
        file_put_contents($file, json_encode($res['response']));
        return $res['response'];
    }
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

if(isset($_GET['check_notify'])) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $BIN_ID . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $API_KEY_BIN, "X-Bin-Meta: false"]);
    $res = json_decode(curl_exec($ch), true);
    $notify = $res['notification'];
    if (isset($notify['time']) && (time() - $notify['time'] > 172800)) { echo json_encode(null); } 
    else { echo json_encode($notify); }
    exit;
}

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

function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$cloud = getCloudFullData($BIN_ID, $API_KEY_BIN);
$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : [];
$active_sections = array_filter($cloud['sections'] ?: [], function($s) { return $s['status'] == 'show'; });
$news = isset($cloud['news_ticker']) ? $cloud['news_ticker'] : ['text' => '', 'status' => 'hide'];

$all_fixtures = getTodayMatches($FOOTBALL_API_KEY, $cache_file, $cache_time);
$important_leagues = [307, 2, 3, 5, 39, 140, 135, 78, 61]; 
$my_matches = array_filter($all_fixtures, function($m) use ($important_leagues) {
    return in_array($m['league']['id'], $important_leagues);
});

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
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); --blue-grad: linear-gradient(45deg, #0ea5e9, #fff); --sky: #0ea5e9; }
        
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 230px; /* زيادة الإزاحة عثمان */ color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; transition: 0.3s; }

        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; transition: max-width 0.4s; }

        /* ستايل جدول المباريات المطور عثمان */
        .matches-container { padding: 0 12px; margin-bottom: 30px; }
        .match-card { background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border); border-radius: 18px; padding: 18px 10px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .match-card:hover { border-color: var(--sky); background: rgba(14, 165, 233, 0.05); }
        .m-league { position: absolute; top: -10px; right: 15px; background: var(--sky); font-size: 9px; padding: 3px 12px; border-radius: 50px; font-weight: 900; color: #fff; box-shadow: 0 4px 8px rgba(14, 165, 233, 0.3); }
        .m-team { flex: 1.2; text-align: center; font-size: 11px; font-weight: 900; }
        .m-team img { width: 32px; height: 32px; display: block; margin: 0 auto 8px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5)); }
        .m-info { flex: 0.9; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; border-left: 1px solid rgba(255,255,255,0.08); border-right: 1px solid rgba(255,255,255,0.08); margin: 0 5px; }
        .m-score { font-size: 22px; font-weight: 900; letter-spacing: 2px; font-family: sans-serif; }
        .m-time { font-size: 14px; font-weight: 900; color: var(--sky); font-family: sans-serif; }
        .m-live { font-size: 9px; color: #22c55e; animation: blink 1s infinite; font-weight: 900; margin-top: 4px; }

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
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.98); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; transition: max-width 0.4s; }
        
        .notify-bell-btn { position: fixed; bottom: 85px; left: 25px; width: 45px; height: 45px; background: var(--main); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; z-index: 5000; box-shadow: 0 8px 20px rgba(225, 29, 72, 0.4); cursor: pointer; border: 2px solid rgba(255,255,255,0.1); }
        .notify-dot { position: absolute; top: 2px; right: 2px; width: 12px; height: 12px; background: #22c55e; border-radius: 50%; border: 2px solid var(--bg-deep); display: none; }
        
        .visitors-badge-float { position: fixed; bottom: 25px; left: 25px; width: 45px; height: 45px; background: rgba(34, 197, 94, 0.15); backdrop-filter: blur(10px); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; border: 1.5px solid #22c55e; box-shadow: 0 8px 20px rgba(34, 197, 94, 0.2); }
        .visitors-badge-float i { font-size: 14px; color: #22c55e; }
        .visitors-badge-float span { font-size: 11px; font-weight: 900; color: #fff; }

        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; flex-wrap: nowrap; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; transition: 0.3s; flex: 1; text-align: center; border: 1.5px solid #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
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
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(255, 77, 77, 0.7); } 70% { box-shadow: 0 0 0 8px rgba(255, 77, 77, 0); } 100% { box-shadow: 0 0 0 0 rgba(255, 77, 77, 0); } }

        .play-btn { width: 90%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 8px; background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; font-size: 13px; transition: 0.4s; }
        .play-btn:hover { background: var(--main); border-color: var(--main); }
        .play-btn.connected { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important; border-color: #38bdf8 !important; color: #38bdf8 !important; }

        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; background-image: url('mg/wel.GIF'); background-size: cover; background-position: center; position: relative; }
        iframe { width: 100%; height: 100%; border: none; background: #000; }

        @media screen and (orientation: landscape) {
            .main-container { max-width: 95%; } 
            .header-fixed { max-width: 95%; padding: 5px 0; }
            body { padding-top: 140px; } 
            .social-links { margin-bottom: 5px; }
            .social-btn { padding: 4px 5px; font-size: 8px; }
        }

        #notify-toast { position: fixed; top: -120px; left: 50%; transform: translateX(-50%); width: 85%; max-width: 400px; background: rgba(14, 165, 233, 0.95); backdrop-filter: blur(10px); color: white; padding: 12px 18px; border-radius: 20px; z-index: 6000; box-shadow: 0 15px 35px rgba(0,0,0,0.6); transition: 0.6s; border: 1px solid rgba(255,255,255,0.2); }
        #notify-toast.show { top: 20px; }
        #notify-panel { position: fixed; bottom: 85px; left: 50%; transform: translateX(-50%); width: 280px; max-height: 350px; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(15px); border-radius: 20px; border: 1px solid var(--glass-border); z-index: 5500; display: none; flex-direction: column; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.8); }
        .panel-header { background: var(--main); color: white; padding: 12px; font-weight: 900; font-size: 13px; display: flex; justify-content: space-between; }
        .panel-list { overflow-y: auto; padding: 10px; }
        .notify-item { background: rgba(255,255,255,0.05); padding: 8px; border-radius: 10px; margin-bottom: 6px; border-right: 3px solid #0ea5e9; font-size: 11px; }

        .bg-pattern { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        .bg-pattern::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.05; animation: movePattern 60s linear infinite; }
        footer { text-align: center; padding: 40px; font-size: 10px; opacity: 0.5; }
        @keyframes blink { 50% { opacity: 0.5; } }
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

<div class="main-container">
    <div id="notify-toast">
        <div style="display:flex; justify-content:space-between; margin-bottom:5px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:5px;">
            <b style="font-size:12px;"><i class="fas fa-bell"></i> تنبيه جديد</b>
            <small style="font-size:9px;" id="toast-time">الآن</small>
        </div>
        <div id="notify-txt"></div>
    </div>

    <div id="notify-panel">
        <div class="panel-header"><span>🔔 مركز الإشعارات</span><i class="fas fa-times" onclick="toggleNotifyPanel()" style="cursor:pointer;"></i></div>
        <div class="panel-list" id="panel-list"></div>
    </div>

    <div class="notify-bell-btn" onclick="toggleNotifyPanel()"><i class="fas fa-bell"></i><div class="notify-dot" id="n-dot"></div></div>
    <div class="visitors-badge-float">
        <i class="fas fa-users"></i>
        <span id="realtime-visitors"><?php echo $online_now; ?></span>
    </div>

    <div class="bg-pattern"></div>

    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-sn">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-tw">تويتر</a>
        </div>

        <?php if($news['status'] == 'show'): ?>
        <div class="news-ticker">
            <span class="ticker-label">تنبيهات</span>
            <div class="ticker-wrap"><div class="ticker-move">
                <span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span>
            </div></div>
        </div>
        <?php endif; ?>

        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches', this)"><img src="https://cdn-icons-png.flaticon.com/512/33/33736.png"><span>المباريات</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches" class="channel-section active">
            <div class="matches-container">
                <div style="margin-bottom: 25px; font-weight: 900; font-size: 15px; color: var(--sky); border-right: 4px solid var(--sky); padding-right: 12px;">أهم مباريات اليوم</div>
                <?php if(empty($my_matches)): ?>
                    <div style="text-align:center; padding:50px; background:var(--glass); border-radius:20px; opacity:0.3; font-size:12px;">لا توجد مباريات هامة حالياً</div>
                <?php else: foreach($my_matches as $m): 
                    $status = $m['fixture']['status']['short'];
                ?>
                <div class="match-card">
                    <div class="m-league"><?= $m['league']['name'] ?></div>
                    <div class="m-team">
                        <img src="<?= $m['teams']['home']['logo'] ?>">
                        <span><?= $m['teams']['home']['name'] ?></span>
                    </div>
                    <div class="m-info">
                        <?php if(in_array($status, ['1H','2H','HT','ET','P'])): ?>
                            <div class="m-score notranslate" style="color:var(--main)"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div>
                            <div class="m-live">مباشر</div>
                        <?php elseif($status == 'FT'): ?>
                            <div class="m-score notranslate" style="opacity:0.9"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div>
                            <div style="font-size:10px; font-weight:900; opacity:0.5;">انتهت</div>
                        <?php else: ?>
                            <div class="m-time notranslate"><?= date("H:i", $m['fixture']['timestamp']) ?></div>
                            <div style="font-size:9px; font-weight:900; opacity:0.5;">قريباً</div>
                        <?php endif; ?>
                    </div>
                    <div class="m-team">
                        <img src="<?= $m['teams']['away']['logo'] ?>">
                        <span><?= $m['teams']['away']['name'] ?></span>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section">
            <?php if(empty($channels)): ?><div style="text-align:center; padding:80px; opacity:0.3;"><p>لا توجد قنوات حالياً.</p></div><?php endif; ?>
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="c-head">
                    <div class="name-badge"><?= $ch['name'] ?></div>
                    <div class="live-badge"><div class="live-dot"></div> مباشر</div>
                </div>
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">
                    <i class="fas fa-play-circle"></i> تشغيل البث المباشر
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>
</div>

<script>
let lastNotifyId = localStorage.getItem('last_notify_id') || "";
let notifyHistory = JSON.parse(localStorage.getItem('notify_history')) || [];

function toggleNotifyPanel() {
    let panel = document.getElementById('notify-panel');
    panel.style.display = (panel.style.display === 'flex') ? 'none' : 'flex';
    document.getElementById('n-dot').style.display = 'none';
    renderHistory();
}

function renderHistory() {
    let list = document.getElementById('panel-list');
    list.innerHTML = notifyHistory.length ? "" : "<p style='text-align:center; opacity:0.5; font-size:12px;'>لا توجد إشعارات سابقة</p>";
    notifyHistory.slice().reverse().forEach(n => {
        let date = new Date(n.time * 1000).toLocaleString('ar-SA', {hour:'2-digit', minute:'2-digit'});
        list.innerHTML += `<div class="notify-item">${n.msg}<small>${date}</small></div>`;
    });
}

function checkNotifications() {
    fetch(window.location.pathname + '?check_notify=1').then(res => res.json()).then(data => {
        if(data && data.id && data.id !== lastNotifyId) {
            lastNotifyId = data.id;
            localStorage.setItem('last_notify_id', data.id);
            notifyHistory.push(data);
            if(notifyHistory.length > 10) notifyHistory.shift();
            localStorage.setItem('notify_history', JSON.stringify(notifyHistory));
            document.getElementById('notify-txt').innerText = data.msg;
            document.getElementById('notify-toast').classList.add('show');
            document.getElementById('n-dot').style.display = 'block';
            setTimeout(() => { document.getElementById('notify-toast').classList.remove('show'); }, 6000);
            renderHistory();
        }
    });
}

setInterval(checkNotifications, 10000);
window.addEventListener('load', () => { 
    setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 2000); 
    checkNotifications();
    renderHistory();
});

function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}

function startStream(boxId, file, btn) {
    let vBox = document.getElementById(boxId);
    vBox.style.backgroundImage = "none";
    vBox.innerHTML = `<iframe src="${file}?autoplay=1&muted=1" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
    btn.innerHTML = '<i class="fas fa-check-circle"></i> تم الاتصال بالبث';
    btn.classList.add('connected');
}

setInterval(() => { 
    fetch(window.location.pathname + '?fetch_visitors=1')
    .then(res => res.text())
    .then(count => { document.getElementById('realtime-visitors').innerText = count; }); 
}, 4000);
</script>
</body>
</html>
