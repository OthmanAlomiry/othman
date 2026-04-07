<?php
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// --- بيانات السحابة والـ API عثمان ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$FOOTBALL_API_KEY = 'ef02886bbd68ecb3bdfc630f4546eb97';

// --- دوال جلب المباريات عثمان ---
function getFixtures($date, $key) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://v3.football.api-sports.io/fixtures?date=$date",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array("x-apisports-key: $key"),
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);
    return $data['response'] ?: array();
}

function translateText($text) {
    if(empty($text)) return $text;
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $res = @file_get_contents($url);
    if($res){ $res = json_decode($res, true); return $res[0][0][0] ?: $text; }
    return $text;
}

// --- نظام جلب الجدول حسب التاريخ عثمان (AJAX) ---
if(isset($_GET['fetch_schedule'])) {
    $target_date = $_GET['fetch_schedule'];
    $fixtures = getFixtures($target_date, $FOOTBALL_API_KEY);
    $my_leagues = array(307 => 'الدوري السعودي', 2 => 'أبطال أوروبا', 3 => 'الدوري الأوروبي', 39 => 'الدوري الإنجليزي', 140 => 'الدوري الإسباني', 135 => 'الدوري الإيطالي');
    
    $grouped = array();
    foreach($fixtures as $f) { if(isset($my_leagues[$f['league']['id']])) $grouped[$my_leagues[$f['league']['id']]][] = $f; }
    
    if(empty($grouped)) {
        echo "<p style='text-align:center; padding:50px; opacity:0.5; font-size:12px;'>لا توجد مباريات هامة لهذا التاريخ</p>";
    } else {
        foreach($grouped as $league => $matches) {
            echo '<div class="league-title">'.$league.'</div>';
            foreach($matches as $m) {
                echo '<div class="match-row">
                    <div class="m-team"><img src="'.$m['teams']['home']['logo'].'">'.translateText($m['teams']['home']['name']).'</div>
                    <div style="text-align:center; flex:0.5; color:#fff;"><b style="font-size:14px;">'.date("H:i", $m['fixture']['timestamp']).'</b><br><small style="font-size:8px; opacity:0.5;">مكة</small></div>
                    <div class="m-team"><img src="'.$m['teams']['away']['logo'].'">'.translateText($m['teams']['away']['name']).'</div>
                </div>';
            }
        }
    }
    exit;
}

// دالة فحص الإشعارات (AJAX)
if(isset($_GET['check_notify'])) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $BIN_ID . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Master-Key: " . $API_KEY, "X-Bin-Meta: false"));
    $res = json_decode(curl_exec($ch), true);
    $notify = $res['notification'];
    echo json_encode($notify);
    exit;
}

// نظام عداد المتواجدين
$visitors_file = 'online_visitors.txt';
if (isset($_GET['fetch_visitors'])) {
    $session_id = session_id(); $time = time();
    $data = file_exists($visitors_file) ? unserialize(file_get_contents($visitors_file)) : array();
    $data[$session_id] = $time;
    foreach ($data as $id => $last_time) { if ($time - $last_time > 120) unset($data[$id]); }
    file_put_contents($visitors_file, serialize($data));
    echo count($data); exit; 
}

// جلب بيانات السحابة
function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Master-Key: " . $key, "X-Bin-Meta: false"));
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}
$cloud = getCloudFullData($BIN_ID, $API_KEY);
$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : array();
$active_sections = array_filter($cloud['sections'] ?: array(), function($s) { return $s['status'] == 'show'; });
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
    <title>الخدمة الرقمية - بث ومباريات</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); --blue-grad: linear-gradient(45deg, #0ea5e9, #fff); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }

        /* أزرار عثمان الجانبية */
        .schedule-btn { position: fixed; bottom: 145px; left: 25px; width: 45px; height: 45px; background: #0ea5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; z-index: 5000; cursor: pointer; border: 2px solid rgba(255,255,255,0.1); }
        
        /* نافذة الجدول عثمان مع شريط التاريخ */
        #schedule-panel { position: fixed; bottom: 85px; left: 50%; transform: translateX(-50%); width: 92%; max-width: 400px; max-height: 75vh; background: #0f172a; border-radius: 20px; border: 1px solid var(--glass-border); z-index: 5500; display: none; flex-direction: column; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.8); }
        .date-nav { display: flex; align-items: center; justify-content: space-between; padding: 12px; background: rgba(255,255,255,0.05); border-bottom: 1px solid var(--glass-border); }
        .date-nav b { font-size: 13px; color: #0ea5e9; }
        .date-nav button { background: var(--main); border: none; color: #fff; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; }

        .league-title { background: linear-gradient(90deg, var(--main), transparent); padding: 8px 12px; font-size: 12px; font-weight: 900; margin: 15px 0 5px; border-right: 3px solid #fff; color: #fff; }
        .match-row { background: rgba(255,255,255,0.03); margin: 5px 10px; padding: 10px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(255,255,255,0.05); }
        .m-team { flex: 1; text-align: center; font-size: 10px; font-weight: 700; color: #fff; }
        .m-team img { width: 25px; height: 25px; display: block; margin: 0 auto 5px; object-fit: contain; }

        /* ستايلاتك الأساسية عثمان */
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #050c14; display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; }
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .notify-bell-btn { position: fixed; bottom: 85px; left: 25px; width: 45px; height: 45px; background: var(--main); border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 5000; cursor: pointer; }
        .visitors-badge-float { position: fixed; bottom: 25px; left: 25px; width: 45px; height: 45px; background: rgba(34, 197, 94, 0.15); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; border: 1.5px solid #22c55e; }
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; }
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; padding: 5px 0; }
        .cat-item { min-width: 65px; background: var(--glass); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 26px; height: 26px; display: block; margin: 0 auto 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); margin-bottom: 20px; }
        .c-head { padding: 12px; background: rgba(0,0,0,0.4); display: flex; justify-content: space-between; align-items: center; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; }
        iframe { width: 100%; height: 100%; border: none; }
        footer { text-align: center; padding: 40px; font-size: 10px; opacity: 0.5; }
    </style>
</head>
<body>

<div id="pro-intro">
    <div class="intro-icon-box" style="width:80px; height:80px; background:var(--main); border-radius:20px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-play" style="color:#fff; font-size:30px;"></i></div>
    <h2 style="color:#fff; margin-top:20px;">الخدمة الرقمية</h2>
</div>

<div class="main-container">
    <div class="schedule-btn" onclick="toggleSchedule()"><i class="fas fa-calendar-alt"></i></div>
    <div class="notify-bell-btn" onclick="toggleNotifyPanel()"><i class="fas fa-bell"></i></div>
    <div class="visitors-badge-float"><i class="fas fa-users" style="color:#22c55e; font-size:12px;"></i><span id="realtime-visitors" style="font-size:10px;"><?php echo $online_now; ?></span></div>

    <div id="schedule-panel">
        <div class="date-nav">
            <button onclick="changeDate(-1)"><i class="fas fa-chevron-right"></i></button>
            <b id="current-date-display"><?= date('Y-m-d') ?></b>
            <button onclick="changeDate(1)"><i class="fas fa-chevron-left"></i></button>
            <i class="fas fa-times" onclick="toggleSchedule()" style="cursor:pointer; margin-right:10px; opacity:0.5;"></i>
        </div>
        <div class="panel-list" id="schedule-content" style="overflow-y:auto; flex:1; padding-bottom:20px;">
            </div>
    </div>

    <div class="header-fixed">
        <div class="social-links">
            <a href="#" class="social-btn" style="background:#25d366;">واتساب</a>
            <a href="#" class="social-btn" style="background:#0088cc;">تليجرام</a>
            <a href="#" class="social-btn" style="background:#FFFC00; color:#000;">سناب</a>
            <a href="#" class="social-btn" style="background:#000;">تويتر</a>
        </div>
        <div class="category-tabs">
            <?php $count = 0; foreach($active_sections as $s): ?>
                <div class="cat-item <?= ($count == 0 ? 'active' : '') ?>" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php $count++; endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <?php $count = 0; foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section <?= ($count == 0 ? 'active' : '') ?>">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="c-head"><div style="background:var(--blue-grad); color:#000; padding:4px 10px; border-radius:8px; font-size:10px; font-weight:900;"><?= $ch['name'] ?></div><div style="color:#ff4d4d; font-size:10px; font-weight:900;">مباشر</div></div>
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">تشغيل البث</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php $count++; endforeach; ?>
    </div>
    <footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>
</div>

<script>
let currentScheduleDate = new Date().toISOString().split('T')[0];

function toggleSchedule() {
    let panel = document.getElementById('schedule-panel');
    if(panel.style.display === 'flex') {
        panel.style.display = 'none';
    } else {
        panel.style.display = 'flex';
        loadSchedule(currentScheduleDate);
    }
}

function changeDate(days) {
    let date = new Date(currentScheduleDate);
    date.setDate(date.getDate() + days);
    currentScheduleDate = date.toISOString().split('T')[0];
    document.getElementById('current-date-display').innerText = currentScheduleDate;
    loadSchedule(currentScheduleDate);
}

function loadSchedule(date) {
    let content = document.getElementById('schedule-content');
    content.innerHTML = '<p style="text-align:center; padding:50px; opacity:0.5;">جاري التحميل...</p>';
    fetch(window.location.pathname + '?fetch_schedule=' + date)
    .then(res => res.text())
    .then(data => { content.innerHTML = data; });
}

function startStream(boxId, file, btn) {
    document.getElementById(boxId).innerHTML = `<iframe src="${file}?autoplay=1" allowfullscreen></iframe>`;
    btn.style.display = 'none';
}

function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}

window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1500); });
setInterval(() => { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(count => { document.getElementById('realtime-visitors').innerText = count; }); }, 5000);
</script>
</body>
</html>
