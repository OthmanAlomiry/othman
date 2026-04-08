<?php
session_start();
error_reporting(0);

// --- بيانات السحابة الخاصة بك عثمان ---
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';

// --- إعدادات API المباريات (عثمان) ---
$FOOTBALL_API_KEY = 'd6c1b4f231cf6d72aacf0c6cfe61efa5'; 
$cache_file = "cache_final_v7.json"; // تغيير الاسم لتحديث البيانات فوراً
$cache_time = 900; 

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

$cloud = getCloudFullData($BIN_ID, $API_KEY_BIN);
$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : [];
$active_sections = array_filter($cloud['sections'] ?: [], function($s) { return $s['status'] == 'show'; });
$news = isset($cloud['news_ticker']) ? $cloud['news_ticker'] : ['text' => '', 'status' => 'hide'];

// جلب المباريات عثمان
$all_fixtures = getTodayMatches($FOOTBALL_API_KEY, $cache_file, $cache_time);
$my_matches = !empty($all_fixtures) ? array_slice($all_fixtures, 0, 15) : [];

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
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); --sky: #0ea5e9; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 230px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; }
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.98); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .cat-item { min-width: 65px; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item span { font-size: 8px; font-weight: 900; display: block; margin-top: 4px; }
        .cat-item img { width: 26px; height: 26px; }
        .match-card { background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border); border-radius: 18px; padding: 18px 10px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; }
        .m-league { position: absolute; top: -10px; right: 15px; background: var(--sky); font-size: 9px; padding: 3px 12px; border-radius: 50px; font-weight: 900; color: #fff; }
        .m-team { flex: 1.2; text-align: center; font-size: 11px; font-weight: 900; }
        .m-team img { width: 32px; height: 32px; display: block; margin: 0 auto 8px; }
        .m-info { flex: 0.9; text-align: center; border-left: 1px solid rgba(255,255,255,0.08); border-right: 1px solid rgba(255,255,255,0.08); margin: 0 5px; }
        .m-score { font-size: 22px; font-weight: 900; letter-spacing: 2px; }
        .m-time { font-size: 14px; font-weight: 900; color: var(--sky); }
        .channel-section { display: none; padding: 15px; }
        .channel-section.active { display: block; }
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #050c14; z-index: 9999; display: flex; justify-content: center; align-items: center; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; }
        footer { text-align: center; padding: 40px; font-size: 10px; opacity: 0.5; }
        @keyframes blink { 50% { opacity: 0.5; } }
    </style>
</head>
<body>
<div id="pro-intro"><h2 style="color:white;">الخدمة الرقمية...</h2></div>
<div class="main-container">
    <div class="header-fixed">
        <div class="social-links" style="display:flex; justify-content:space-around; margin-bottom:15px;">
            <a href="#" style="background:#25d366; padding:5px 15px; border-radius:20px; font-size:10px; color:white; text-decoration:none;">واتساب</a>
            <a href="#" style="background:#0088cc; padding:5px 15px; border-radius:20px; font-size:10px; color:white; text-decoration:none;">تليجرام</a>
        </div>
        <div class="category-tabs" style="display:flex; gap:8px; width:95%; margin:0 auto; overflow-x:auto;">
            <div class="cat-item active" onclick="switchSection('matches', this)"><img src="https://cdn-icons-png.flaticon.com/512/33/33736.png"><span>المباريات</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches" class="channel-section active">
            <div style="margin-bottom: 25px; font-weight: 900; font-size: 15px; color: var(--sky); border-right: 4px solid var(--sky); padding-right: 12px;">مباريات اليوم الهامة</div>
            <?php if(empty($my_matches)): ?>
                <div style="text-align:center; padding:50px; opacity:0.3;">جاري تحديث البيانات... (تأكد من رصيد API)</div>
            <?php else: foreach($my_matches as $m): $status = $m['fixture']['status']['short']; ?>
                <div class="match-card">
                    <div class="m-league"><?= $m['league']['name'] ?></div>
                    <div class="m-team"><img src="<?= $m['teams']['home']['logo'] ?>"><span class="notranslate"><?= $m['teams']['home']['name'] ?></span></div>
                    <div class="m-info">
                        <?php if(in_array($status, ['1H','2H','HT','ET','P'])): ?>
                            <div class="m-score notranslate" style="color:var(--main)"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div><div style="color:#22c55e; font-size:9px;">مباشر</div>
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
        </div>
</div>
<script>
window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1000); });
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active'); if(element) element.classList.add('active');
}
</script>
</body>
</html>
