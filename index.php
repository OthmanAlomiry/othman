<?php
session_start();
error_reporting(0);

// --- بيانات السحابة الخاصة بك عثمان ---
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';

// --- إعدادات API المباريات (عثمان) ---
$FOOTBALL_API_KEY = 'd6c1b4f231cf6d72aacf0c6cfe61efa5'; 
$cache_file = "matches_final_v3.json"; // تغيير الاسم لإجبار السيرفر على جلب بيانات جديدة
$cache_time = 600; // 10 دقائق

function getTodayMatches($key, $file, $time) {
    if (file_exists($file) && (time() - filemtime($file) < $time)) {
        $data = json_decode(file_get_contents($file), true);
        if (!empty($data)) return $data;
    }
    $date = date('Y-m-d');
    $ch = curl_init("https://v3.football.api-sports.io/fixtures?date=$date");
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

// --- باقي الأكواد كما هي تماماً عثمان ---
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

// جلب المباريات عثمان (بدون فلترة صارمة الآن للتأكد من ظهورها)
$all_fixtures = getTodayMatches($FOOTBALL_API_KEY, $cache_file, $cache_time);
$important_leagues = [307, 2, 3, 5, 39, 140, 135, 78, 61, 235, 1]; // أضفت المزيد من الدوريات
$my_matches = array_filter($all_fixtures, function($m) use ($important_leagues) {
    return in_array($m['league']['id'], $important_leagues);
});

// إذا كانت الفلترة لم تجد شيئاً، سنعرض أول 10 مباريات من أي دوري لكي لا يظهر الجدول فارغاً
if(empty($my_matches)) { $my_matches = array_slice($all_fixtures, 0, 10); }

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
        
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 230px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; transition: 0.3s; }

        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; transition: max-width 0.4s; }

        /* ستايل عثمان المطور */
        .matches-container { padding: 0 12px; margin-bottom: 30px; }
        .match-card { background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border); border-radius: 18px; padding: 18px 10px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .m-league { position: absolute; top: -10px; right: 15px; background: var(--sky); font-size: 9px; padding: 3px 12px; border-radius: 50px; font-weight: 900; color: #fff; }
        .m-team { flex: 1.2; text-align: center; font-size: 11px; font-weight: 900; }
        .m-team img { width: 32px; height: 32px; display: block; margin: 0 auto 8px; }
        .m-info { flex: 0.9; text-align: center; display: flex; flex-direction: column; align-items: center; border-left: 1px solid rgba(255,255,255,0.08); border-right: 1px solid rgba(255,255,255,0.08); margin: 0 5px; }
        .m-score { font-size: 22px; font-weight: 900; letter-spacing: 2px; }
        .m-time { font-size: 14px; font-weight: 900; color: var(--sky); }
        .m-live { font-size: 9px; color: #22c55e; animation: blink 1s infinite; font-weight: 900; }

        /* الهيدر الثابت */
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.98); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        
        /* الأزرار العائمة */
        .notify-bell-btn { position: fixed; bottom: 85px; left: 25px; width: 45px; height: 45px; background: var(--main); border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 5000; }
        .notify-dot { position: absolute; top: 2px; right: 2px; width: 12px; height: 12px; background: #22c55e; border-radius: 50%; border: 2px solid var(--bg-deep); display: none; }
        .visitors-badge-float { position: fixed; bottom: 25px; left: 25px; width: 45px; height: 45px; background: rgba(34, 197, 94, 0.15); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; border: 1.5px solid #22c55e; }
        .visitors-badge-float i { font-size: 14px; color: #22c55e; }
        .visitors-badge-float span { font-size: 11px; font-weight: 900; color: #fff; }

        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; border: 1.5px solid #ffffff; }
        .btn-wa { background: #25d366; } .btn-tg { background: #0088cc; } .btn-sn { background: #FFFC00; color: #000 !important; } .btn-tw { background: #000; }

        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 26px; height: 26px; margin-bottom: 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; display: block; }

        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); margin-bottom: 20px; }
        .play-btn { width: 90%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 8px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; background-image: url('mg/wel.GIF'); background-size: cover; background-position: center; position: relative; }
        iframe { width: 100%; height: 100%; border: none; }
        @keyframes blink { 50% { opacity: 0.5; } }
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #050c14; display: flex; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; }
        footer { text-align: center; padding: 40px; font-size: 10px; opacity: 0.5; }
    </style>
</head>
<body>

<div id="pro-intro">
    <div style="text-align:center;">
        <div style="width:80px; height:80px; background:var(--main); border-radius:20px; display:flex; align-items:center; justify-content:center; margin:auto;"><i class="fas fa-play" style="font-size:30px; color:white;"></i></div>
        <h2 style="color:white; margin-top:20px;">الخدمة الرقمية</h2>
    </div>
</div>

<div class="main-container">
    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-sn">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-tw">تويتر</a>
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
            <div class="matches-container">
                <div style="margin-bottom: 25px; font-weight: 900; font-size: 15px; color: var(--sky); border-right: 4px solid var(--sky); padding-right: 12px;">مباريات اليوم الهامة</div>
                <?php if(empty($my_matches)): ?>
                    <div style="text-align:center; padding:50px; background:var(--glass); border-radius:20px; opacity:0.3; font-size:12px;">لا توجد مباريات حالياً</div>
                <?php else: foreach($my_matches as $m): 
                    $status = $m['fixture']['status']['short'];
                ?>
                <div class="match-card">
                    <div class="m-league"><?= $m['league']['name'] ?></div>
                    <div class="m-team"><img src="<?= $m['teams']['home']['logo'] ?>"><span><?= $m['teams']['home']['name'] ?></span></div>
                    <div class="m-info">
                        <?php if(in_array($status, ['1H','2H','HT','ET','P'])): ?>
                            <div class="m-score notranslate" style="color:var(--main)"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div>
                            <div class="m-live">مباشر</div>
                        <?php elseif($status == 'FT'): ?>
                            <div class="m-score notranslate"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div>
                            <div style="font-size:9px; opacity:0.5;">انتهت</div>
                        <?php else: ?>
                            <div class="m-time notranslate"><?= date("H:i", $m['fixture']['timestamp']) ?></div>
                            <div style="font-size:9px; opacity:0.5;">قريباً</div>
                        <?php endif; ?>
                    </div>
                    <div class="m-team"><img src="<?= $m['teams']['away']['logo'] ?>"><span><?= $m['teams']['away']['name'] ?></span></div>
                </div>
                <?php endforeach; endif; ?>
            </div>
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
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}
function startStream(boxId, file, btn) {
    let vBox = document.getElementById(boxId); vBox.innerHTML = `<iframe src="${file}?autoplay=1" allowfullscreen></iframe>`;
}
</script>
</body>
</html>
