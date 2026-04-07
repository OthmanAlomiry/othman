<?php
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// --- بيانات السحابة والـ API عثمان ---
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$FOOTBALL_API_KEY = 'ef02886bbd68ecb3bdfc630f4546eb97';

// دالة جلب بيانات القنوات عثمان - مصححة
function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Master-Key: " . $key, "X-Bin-Meta: false"));
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

// دالة جلب المباريات عثمان - مصححة
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

// دالة الترجمة عثمان
function translateText($text) {
    if(empty($text)) return $text;
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $res = @file_get_contents($url);
    $res = json_decode($res, true);
    return $res[0][0][0] ?: $text;
}

// جلب البيانات
$cloud = getCloudFullData($BIN_ID, $API_KEY_BIN);
$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : array();
$fixtures = getFixtures(date('Y-m-d'), $FOOTBALL_API_KEY);

$my_leagues = array(307 => 'الدوري السعودي', 2 => 'أبطال أوروبا', 3 => 'الدوري الأوروبي', 39 => 'الدوري الإنجليزي', 140 => 'الدوري الإسباني', 135 => 'الدوري الإيطالي');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --card: rgba(255, 255, 255, 0.05); --border: rgba(255, 255, 255, 0.1); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; padding-bottom: 80px; }
        .container { width: 100%; max-width: 500px; margin: auto; }
        .header { text-align: center; padding: 20px 0; background: rgba(5, 12, 20, 0.95); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 1000; backdrop-filter: blur(10px); }
        .page { display: none; padding: 15px; animation: fadeIn 0.3s ease; }
        .page.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .bottom-nav { position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 500px; height: 70px; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(15px); display: flex; justify-content: space-around; align-items: center; border-top: 1px solid var(--border); z-index: 9000; }
        .nav-item { display: flex; flex-direction: column; align-items: center; color: #94a3b8; text-decoration: none; font-size: 11px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .nav-item.active { color: var(--main); }
        .nav-item i { font-size: 22px; margin-bottom: 4px; }
        .league-title { background: linear-gradient(90deg, var(--main), transparent); padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 900; margin: 20px 0 10px; border-right: 4px solid #fff; }
        .match-card { background: var(--card); border: 1px solid var(--border); border-radius: 15px; padding: 15px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; }
        .m-team { flex: 1; text-align: center; font-size: 11px; }
        .m-team img { width: 30px; height: 30px; display: block; margin: 0 auto 5px; object-fit: contain; }
        .btn-play { background: var(--main); border: none; color: #fff; padding: 8px 15px; border-radius: 10px; font-family: inherit; font-weight: 900; cursor: pointer; font-size: 12px; }
        .about-box { background: var(--card); padding: 30px; border-radius: 20px; border: 1px solid var(--border); text-align: center; line-height: 1.6; }
    </style>
</head>
<body>

<div class="container">
    <div class="header"><h1 id="page-title" style="margin:0; font-size:18px;">القنوات المباشرة</h1></div>

    <div id="page-channels" class="page active">
        <?php foreach($all_channels as $ch): ?>
        <div class="match-card">
            <span style="font-weight:900;"><?= $ch['name'] ?></span>
            <button class="btn-play" onclick="location.href='<?= $ch['file'] ?>'"><i class="fas fa-play-circle"></i> تشغيل</button>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="page-schedule" class="page">
        <?php 
        $grouped = array();
        foreach($fixtures as $f) { if(isset($my_leagues[$f['league']['id']])) $grouped[$my_leagues[$f['league']['id']]][] = $f; }
        if(empty($grouped)) echo "<p style='text-align:center; padding:50px; opacity:0.5;'>لا توجد مباريات هامة اليوم</p>";
        foreach($grouped as $league => $matches): ?>
            <div class="league-title"><?= $league ?></div>
            <?php foreach($matches as $m): ?>
            <div class="match-card">
                <div class="m-team"><img src="<?= $m['teams']['home']['logo'] ?>"><?= translateText($m['teams']['home']['name']) ?></div>
                <div style="text-align:center; flex:1;">
                    <div style="font-size:18px; font-weight:900;"><?= date("H:i", $m['fixture']['timestamp']) ?></div>
                    <div style="font-size:9px; opacity:0.4;">بتوقيت مكة</div>
                </div>
                <div class="m-team"><img src="<?= $m['teams']['away']['logo'] ?>"><?= translateText($m['teams']['away']['name']) ?></div>
            </div>
            <?php endforeach; endforeach; ?>
    </div>

    <div id="page-news" class="page">
        <div class="about-box"><i class="fas fa-bullhorn" style="font-size:40px; color:var(--main); margin-bottom:15px;"></i><h2>قسم الأخبار</h2><p>قريباً.. تغطية حصرية لأهم الأخبار الرياضية العالمية والمحلية.</p></div>
    </div>

    <div id="page-about" class="page">
        <div class="about-box">
            <i class="fas fa-info-circle" style="font-size:40px; color:var(--main); margin-bottom:15px;"></i>
            <h2>عن متجر الخدمة الرقمية</h2>
            <p>منصة رياضية متكاملة تقدم أحدث النتائج المباشرة وخدمات البث بأعلى جودة. نحن نسعى لتوفير تجربة مستخدم فريدة وسريعة لمتابعينا.</p>
        </div>
    </div>

    <div class="bottom-nav">
        <div class="nav-item active" onclick="showP('channels', 'القنوات المباشرة', this)"><i class="fas fa-tv"></i><span>القنوات</span></div>
        <div class="nav-item" onclick="showP('schedule', 'جدول المباريات', this)"><i class="far fa-calendar-alt"></i><span>الجدول</span></div>
        <div class="nav-item" onclick="showP('news', 'الأخبار', this)"><i class="fas fa-newspaper"></i><span>الأخبار</span></div>
        <div class="nav-item" onclick="showP('about', 'من نحن', this)"><i class="fas fa-user-circle"></i><span>من نحن</span></div>
    </div>
</div>

<script>
function showP(id, title, el) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.getElementById('page-' + id).classList.add('active');
    document.getElementById('page-title').innerText = title;
    document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
    window.scrollTo(0,0);
}
</script>
</body>
</html>
