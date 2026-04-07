<?php
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// --- بيانات السحابة والـ API عثمان ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$FOOTBALL_API_KEY = 'ef02886bbd68ecb3bdfc630f4546eb97';

// --- دوال جلب البيانات عثمان ---
function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Master-Key: " . $key, "X-Bin-Meta: false"));
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

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
    $res = json_decode($res, true);
    return $res[0][0][0] ?: $text;
}

// جلب البيانات
$cloud = getCloudFullData($BIN_ID, $API_KEY);
$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : array();
$active_sections = array_filter($cloud['sections'] ?: array(), function($s) { return $s['status'] == 'show'; });
$fixtures = getFixtures(date('Y-m-d'), $FOOTBALL_API_KEY);
$my_leagues = array(307 => 'الدوري السعودي', 2 => 'أبطال أوروبا', 3 => 'الدوري الأوروبي', 39 => 'الدوري الإنجليزي', 140 => 'الدوري الإسباني', 135 => 'الدوري الإيطالي');

// نظام عداد المتواجدين
$visitors_file = 'online_visitors.txt';
if (isset($_GET['fetch_visitors'])) {
    $session_id = session_id(); $time = time();
    $data = file_exists($visitors_file) ? unserialize(file_get_contents($visitors_file)) : array();
    $data[$session_id] = $time;
    foreach ($data as $id => $lt) { if ($time - $lt > 120) unset($data[$id]); }
    file_put_contents($visitors_file, serialize($data));
    echo count($data); exit; 
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>الخدمة الرقمية - بث وجدول</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 150px; color: #fff; padding-bottom: 80px; }
        .main-container { width: 100%; max-width: 500px; margin: auto; }
        
        /* الهيدر الثابت عثمان */
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 10px auto 0; overflow-x: auto; scrollbar-width: none; }
        .cat-item { min-width: 80px; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px; border-radius: 12px; cursor: pointer; text-align: center; font-size: 10px; font-weight: 900; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }

        /* استايل الجدول الاحترافي عثمان */
        .league-title { background: linear-gradient(90deg, var(--main), transparent); padding: 8px 15px; border-radius: 10px; font-size: 13px; font-weight: 900; margin: 20px 0 10px; border-right: 4px solid #fff; }
        .match-card { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 20px; padding: 15px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; }
        .team { flex: 1; text-align: center; font-size: 11px; font-weight: 700; }
        .team img { width: 35px; height: 35px; display: block; margin: 0 auto 5px; object-fit: contain; }
        .m-info { flex: 1; text-align: center; }
        .score { font-size: 22px; font-weight: 900; }

        /* شريط سفلي عثمان */
        .bottom-nav { position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 500px; height: 70px; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(15px); display: flex; justify-content: space-around; align-items: center; border-top: 1px solid var(--glass-border); z-index: 9000; }
        .nav-item { display: flex; flex-direction: column; align-items: center; color: #94a3b8; text-decoration: none; font-size: 11px; cursor: pointer; }
        .nav-item.active { color: var(--main); }
        .nav-item i { font-size: 22px; margin-bottom: 4px; }

        .section { display: none; padding: 10px; }
        .section.active { display: block; }
        .card-ch { background: var(--glass); border-radius: 20px; border: 1px solid var(--glass-border); margin-bottom: 15px; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

<div class="main-container">
    <div class="header-fixed">
        <div style="font-weight:900; font-size:18px; color:var(--main); margin-bottom:10px;">الخدمة الرقمية</div>
        <div class="category-tabs" id="top-tabs">
            <div class="cat-item active" onclick="switchMain('channels', this)">القنوات</div>
            <div class="cat-item" onclick="switchMain('schedule', this)">الجدول</div>
            <div class="cat-item" onclick="switchMain('about', this)">من نحن</div>
        </div>
    </div>

    <div id="sec-channels" class="section active">
        <?php foreach($all_channels as $ch): ?>
        <div class="card-ch">
            <span style="font-weight:900;"><?= $ch['name'] ?></span>
            <button onclick="location.href='<?= $ch['file'] ?>'" style="background:var(--main); border:none; color:#fff; padding:8px 20px; border-radius:12px; font-weight:900;">بث مباشر</button>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="sec-schedule" class="section">
        <?php 
        $grouped = array();
        foreach($fixtures as $f) { if(isset($my_leagues[$f['league']['id']])) $grouped[$my_leagues[$f['league']['id']]][] = $f; }
        if(empty($grouped)) echo "<p style='text-align:center; padding:50px; opacity:0.5;'>لا توجد مباريات هامة اليوم</p>";
        foreach($grouped as $league => $matches): ?>
            <div class="league-title"><?= $league ?></div>
            <?php foreach($matches as $m): ?>
            <div class="match-card">
                <div class="team"><img src="<?= $m['teams']['home']['logo'] ?>"><?= translateText($m['teams']['home']['name']) ?></div>
                <div class="m-info">
                    <div class="score"><?= date("H:i", $m['fixture']['timestamp']) ?></div>
                    <div style="font-size:9px; opacity:0.4;">بتوقيت مكة</div>
                </div>
                <div class="team"><img src="<?= $m['teams']['away']['logo'] ?>"><?= translateText($m['teams']['away']['name']) ?></div>
            </div>
            <?php endforeach; endforeach; ?>
    </div>

    <div id="sec-about" class="section">
        <div style="background:var(--glass); padding:30px; border-radius:25px; text-align:center; border:1px solid var(--glass-border);">
            <i class="fas fa-info-circle" style="font-size:50px; color:var(--main); margin-bottom:20px;"></i>
            <h2 style="margin:0;">من نحن</h2>
            <p style="opacity:0.7; line-height:1.8;">متجر الخدمة الرقمية يوفر لكم أحدث نتائج المباريات وجداول الدوريات الكبرى مع خدمة بث مميزة.</p>
        </div>
    </div>

    <div class="bottom-nav">
        <div class="nav-item active" onclick="switchMain('channels', this)"><i class="fas fa-tv"></i><span>القنوات</span></div>
        <div class="nav-item" onclick="switchMain('schedule', this)"><i class="far fa-calendar-alt"></i><span>الجدول</span></div>
        <div class="nav-item" onclick="switchMain('about', this)"><i class="fas fa-user-circle"></i><span>من نحن</span></div>
    </div>
</div>

<script>
function switchMain(id, el) {
    // إخفاء كل الأقسام
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    // إظهار القسم المختار
    document.getElementById('sec-' + id).classList.add('active');
    
    // تحديث التبويب العلوي والسفلي
    document.querySelectorAll('.nav-item, .cat-item').forEach(i => i.classList.remove('active'));
    // تفعيل العنصر الذي تم الضغط عليه
    el.classList.add('active');
    window.scrollTo(0,0);
}

// تحديث عداد الزوار
setInterval(() => { 
    fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(count => {}); 
}, 5000);
</script>
</body>
</html>
