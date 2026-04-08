<?php
session_start();
error_reporting(0);

// --- بيانات السحابة والـ API عثمان ---
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$FOOTBALL_API_KEY = 'd6c1b4f231cf6d72aacf0c6cfe61efa5'; 

// إعدادات التخزين المؤقت عثمان
$cache_file = "matches_master_cache.json";
$cache_time = 900; // تحديث كل 15 دقيقة لتوفير الـ 100 طلب

function getTodayMatches($key, $file, $time) {
    if (file_exists($file) && (time() - filemtime($file) < $time)) {
        $cached = json_decode(file_get_contents($file), true);
        if (!empty($cached)) return $cached;
    }
    $date = date('Y-m-d');
    $ch = curl_init("https://v3.football.api-sports.io/fixtures?date=$date&timezone=Asia/Riyadh");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["x-apisports-key: " . $key]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = json_decode(curl_exec($ch), true);
    if (isset($res['response']) && !empty($res['response'])) {
        file_put_contents($file, json_encode($res['response']));
        return $res['response'];
    }
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

// دالة فحص الإشعارات (AJAX)
if(isset($_GET['check_notify'])) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $BIN_ID . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $API_KEY_BIN, "X-Bin-Meta: false"]);
    $res = json_decode(curl_exec($ch), true);
    echo json_encode($res['notification']);
    exit;
}

// نظام المتواجدين
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

// جلب بيانات القنوات
function getCloudData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    return json_decode(curl_exec($ch), true);
}

$cloud = getCloudData($BIN_ID, $API_KEY_BIN);
$all_channels = $cloud['custom_channels'] ?: [];
$active_sections = array_filter($cloud['sections'] ?: [], function($s) { return $s['status'] == 'show'; });

// جلب ومعالجة المباريات عثمان
$all_fixtures = getTodayMatches($FOOTBALL_API_KEY, $cache_file, $cache_time);
$important_leagues = [307, 2, 3, 5, 39, 140, 135, 78, 61, 1]; 
$my_matches = array_filter($all_fixtures, function($m) use ($important_leagues) {
    return in_array($m['league']['id'], $important_leagues);
});
if(empty($my_matches)) { $my_matches = array_slice($all_fixtures, 0, 12); }

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
    <title>الخدمة الرقمية - بث مباشر ومباريات</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); --sky: #0ea5e9; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 210px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.98); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: scale(1.03); }
        .cat-item img { width: 26px; height: 26px; object-fit: contain; margin-bottom: 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; display: block; }
        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; animation: slideUp 0.5s ease-out; }
        .match-card { background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border); border-radius: 18px; padding: 15px 10px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; }
        .m-league { position: absolute; top: -10px; right: 15px; background: var(--sky); font-size: 9px; padding: 3px 10px; border-radius: 50px; font-weight: 900; }
        .m-team { flex: 1.2; text-align: center; font-size: 11px; font-weight: 900; }
        .m-team img { width: 30px; height: 30px; display: block; margin: 0 auto 8px; }
        .m-info { flex: 0.8; text-align: center; border-left: 1px solid rgba(255,255,255,0.08); border-right: 1px solid rgba(255,255,255,0.08); margin: 0 5px; }
        .m-score { font-size: 20px; font-weight: 900; letter-spacing: 2px; }
        .m-time { font-size: 13px; font-weight: 900; color: var(--sky); }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); margin-bottom: 20px; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; background-image: url('mg/wel.GIF'); background-size: cover; position: relative; }
        iframe { width: 100%; height: 100%; border: none; }
        .play-btn { width: 90%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 8px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; }
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #050c14; z-index: 9999; display: flex; justify-content: center; align-items: center; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; }
        @keyframes blink { 50% { opacity: 0.5; } }
        .m-live { color: #22c55e; animation: blink 1s infinite; font-size: 9px; font-weight: 900; }
        footer { text-align: center; padding: 40px; font-size: 10px; opacity: 0.5; }
    </style>
</head>
<body>

<div id="pro-intro"><h2 style="color:white; font-weight:900;">الخدمة الرقمية</h2></div>

<div class="main-container">
    <div class="header-fixed">
        <div style="display:flex; justify-content:space-around; margin-bottom:15px; padding:0 10px;">
            <a href="https://wa.me/966505571164" style="background:#25d366; color:white; padding:6px 15px; border-radius:20px; font-size:10px; text-decoration:none; font-weight:bold;">واتساب</a>
            <a href="https://t.me/d_s_pro" style="background:#0088cc; color:white; padding:6px 15px; border-radius:20px; font-size:10px; text-decoration:none; font-weight:bold;">تليجرام</a>
        </div>
        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches', this)"><img src="https://cdn-icons-png.flaticon.com/512/33/33736.png"><span>المباريات</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches" class="channel-section active">
            <div style="margin-bottom: 25px; font-weight: 900; font-size: 15px; color: var(--sky); border-right: 4px solid var(--sky); padding-right: 12px;">أهم مباريات اليوم</div>
            <?php if(empty($my_matches)): ?>
                <div style="text-align:center; padding:50px; opacity:0.3;">لا توجد مباريات متاحة الآن.</div>
            <?php else: foreach($my_matches as $m): $status = $m['fixture']['status']['short']; ?>
                <div class="match-card">
                    <div class="m-league"><?= $m['league']['name'] ?></div>
                    <div class="m-team"><img src="<?= $m['teams']['home']['logo'] ?>"><span class="notranslate"><?= $m['teams']['home']['name'] ?></span></div>
                    <div class="m-info">
                        <?php if(in_array($status, ['1H','2H','HT','ET','P'])): ?>
                            <div class="m-score notranslate" style="color:var(--main)"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div><div class="m-live">مباشر</div>
                        <?php elseif($status == 'FT'): ?>
                            <div class="m-score notranslate"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div><div style="font-size:9px; opacity:0.5;">انتهت</div>
                        <?php else: ?>
                            <div class="m-time notranslate"><?= date("H:i", $m['fixture']['timestamp']) ?></div><div style="font-size:9px; opacity:0.5;">قريباً</div>
                        <?php endif; ?>
                    </div>
                    <div class="m-team"><img src="<?= $m['teams']['away']['logo'] ?>"><span class="notranslate"><?= $m['teams']['away']['name'] ?></span></div>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">تشغيل البث المباشر</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <footer>جميع الحقوق محفوظة © 2026</footer>
</div>

<script>
window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1500); });
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active'); element.classList.add('active');
}
function startStream(boxId, file, btn) {
    let vBox = document.getElementById(boxId); vBox.style.backgroundImage = "none";
    vBox.innerHTML = `<iframe src="${file}?autoplay=1&muted=1" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
}
</script>
</body>
</html>
