<?php
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// --- بيانات الربط عثمان ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69d6f03436566621a891c500';
$FOOTBALL_KEY = '2fa3bae3383927c794a34cd72089383cef96aec2af30afaa8b7b093f64a91142';

// --- وظيفة جلب البيانات من السحابة عثمان ---
function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array("X-Master-Key: " . $key, "X-Bin-Meta: false"),
        CURLOPT_TIMEOUT => 10
    ));
    $res = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($res, true);
    return (isset($data['record'])) ? $data['record'] : $data;
}

// --- وظيفة جلب المباريات عثمان ---
function getFixtures($key) {
    $date = date('Y-m-d');
    $ch = curl_init("https://v3.football.api-sports.io/fixtures?date=$date");
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array("x-apisports-key: " . $key),
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false
    ));
    $res = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($res, true);
    return (isset($data['response'])) ? $data['response'] : array();
}

// --- وظيفة الترجمة التلقائية عثمان ---
function translateText($text) {
    if(empty($text)) return $text;
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $res = @file_get_contents($url);
    if($res){ $res = json_decode($res, true); return $res[0][0][0] ?: $text; }
    return $text;
}

// جلب كافة البيانات
$cloud = getCloudFullData($BIN_ID, $API_KEY);
$fixtures = getFixtures($FOOTBALL_KEY);
$allowed_leagues = array(307, 2, 3, 39, 140, 135, 78, 61);

$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : array();
$active_sections = array_filter(isset($cloud['sections']) ? $cloud['sections'] : array(), function($s) { return $s['status'] == 'show'; });
$news = isset($cloud['news_ticker']) ? $cloud['news_ticker'] : array('text' => '', 'status' => 'hide');

// نظام الزوار
if (isset($_GET['fetch_visitors'])) {
    $file = 'online_visitors.txt';
    $data = file_exists($file) ? unserialize(file_get_contents($file)) : array();
    $data[session_id()] = time();
    foreach ($data as $id => $lt) { if (time() - $lt > 120) unset($data[$id]); }
    file_put_contents($file, serialize($data));
    echo count($data); exit; 
}

// فحص الإشعارات (AJAX)
if(isset($_GET['check_notify'])) { echo json_encode($cloud['notification']); exit; }

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
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #fff; overflow-x: hidden; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #050c14; z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; }
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .match-slider { display: flex; gap: 10px; overflow-x: auto; padding: 10px 15px; scrollbar-width: none; background: rgba(255,255,255,0.02); margin-bottom: 5px; }
        .mini-card { min-width: 130px; background: var(--glass); border: 1px solid var(--glass-border); border-radius: 12px; padding: 6px; text-align: center; flex-shrink: 0; }
        .mini-teams img { width: 18px; height: 18px; object-fit: contain; }
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 10px auto 0; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 26px; height: 26px; margin-bottom: 4px; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); margin: 0 15px 20px; }
        .c-head { padding: 12px; background: rgba(0,0,0,0.4); display: flex; justify-content: space-between; align-items: center; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; }
        iframe { width: 100%; height: 100%; border: none; }
        .notify-bell-btn { position: fixed; bottom: 85px; left: 25px; width: 45px; height: 45px; background: var(--main); border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 5000; }
        .visitors-badge { position: fixed; bottom: 25px; left: 25px; width: 45px; height: 45px; background: rgba(34, 197, 94, 0.15); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; border: 1.5px solid #22c55e; }
        #notify-toast { position: fixed; top: -100px; left: 50%; transform: translateX(-50%); width: 80%; background: #0ea5e9; padding: 15px; border-radius: 15px; z-index: 6000; transition: 0.5s; text-align: center; }
        #notify-toast.show { top: 20px; }
    </style>
</head>
<body>

<div id="pro-intro"><h2>الخدمة الرقمية</h2></div>
<div id="notify-toast"><b>تنبيه جديد:</b> <span id="notif-text"></span></div>

<div class="main-container">
    <div class="notify-bell-btn"><i class="fas fa-bell"></i></div>
    <div class="visitors-badge"><i class="fas fa-users" style="color:#22c55e; font-size:12px;"></i><span id="vc" style="font-size:10px;">1</span></div>

    <div class="header-fixed">
        <div class="social-links" style="display:flex; justify-content:space-between; padding:0 10px; margin-bottom:10px;">
            <a href="https://wa.me/966505571164" style="background:#25d366; flex:1; margin:2px; padding:5px; border-radius:50px; text-decoration:none; font-size:9px; color:#fff;">واتساب</a>
            <a href="https://t.me/d_s_pro" style="background:#0088cc; flex:1; margin:2px; padding:5px; border-radius:50px; text-decoration:none; font-size:9px; color:#fff;">تليجرام</a>
        </div>

        <?php if($news['status'] == 'show'): ?><marquee style="color:#fff; font-size:11px;"><?= $news['text'] ?></marquee><?php endif; ?>

        <div class="match-slider">
            <?php 
            foreach($fixtures as $f) {
                if(in_array((int)$f['league']['id'], $allowed_leagues)) {
                    echo '<div class="mini-card">
                            <div style="font-size:7px; opacity:0.5;">'.translateText($f['league']['name']).'</div>
                            <div style="display:flex; align-items:center; justify-content:center; gap:5px; margin-top:4px;">
                                <img src="'.$f['teams']['home']['logo'].'" style="width:18px;">
                                <span style="font-size:10px; font-weight:900; color:#0ea5e9;">'.date("H:i", $f['fixture']['timestamp']).'</span>
                                <img src="'.$f['teams']['away']['logo'].'" style="width:18px;">
                            </div>
                          </div>';
                }
            }
            ?>
        </div>

        <div class="category-tabs">
            <?php $count = 0; foreach($active_sections as $s): ?>
                <div class="cat-item <?= ($count == 0 ? 'active' : '') ?>" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><br><?= $s['name'] ?></span></div>
            <?php $count++; endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <?php $count = 0; foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="chan-sec" style="<?= ($count == 0 ? 'display:block;' : 'display:none;') ?>">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="c-head"><b><?= $ch['name'] ?></b><span style="color:#ff4d4d; font-size:10px;">● مباشر</span></div>
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">تشغيل البث</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php $count++; endforeach; ?>
    </div>
</div>

<script>
let lastId = "";
function switchSection(id, el) {
    document.querySelectorAll('.chan-sec').forEach(s => s.style.display = 'none');
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).style.display = 'block';
    el.classList.add('active');
}
function startStream(id, file, btn) {
    document.getElementById(id).innerHTML = `<iframe src="${file}?autoplay=1&muted=1" allowfullscreen></iframe>`;
    btn.style.display = 'none';
}
function checkNotif() {
    fetch(window.location.pathname + '?check_notify=1').then(res => res.json()).then(data => {
        if(data && data.id && data.id !== lastId) {
            lastId = data.id;
            document.getElementById('notif-text').innerText = data.msg;
            document.getElementById('notify-toast').classList.add('show');
            setTimeout(() => { document.getElementById('notify-toast').classList.remove('show'); }, 5000);
        }
    });
}
window.addEventListener('load', () => { 
    setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1500); 
    setInterval(checkNotif, 10000);
});
setInterval(() => { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(c => { document.getElementById('vc').innerText = c; }); }, 5000);
</script>
</body>
</html>
