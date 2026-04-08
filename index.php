<?php
session_start();
error_reporting(0); // سنعطله مؤقتاً إذا لم يعمل الكود

// --- بيانات السحابة (JSONBin) عثمان ---
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';

// --- إعدادات API المباريات عثمان ---
$FOOTBALL_API_KEY = 'd6c1b4f231cf6d72aacf0c6cfe61efa5'; 
$cache_file = "matches_debug_v1.json"; 
$cache_time = 300; // 5 دقائق عثمان لسرعة الفحص

function getTodayMatches($key, $file, $time) {
    if (file_exists($file) && (time() - filemtime($file) < $time)) {
        $data = json_decode(file_get_contents($file), true);
        if (!empty($data)) return $data;
    }
    
    $date = date('Y-m-d');
    $ch = curl_init("https://v3.football.api-sports.io/fixtures?date=$date&timezone=Asia/Riyadh");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["x-apisports-key: " . $key]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $res = json_decode($response, true);
    
    if (isset($res['response']) && !empty($res['response'])) {
        file_put_contents($file, json_encode($res['response']));
        return $res['response'];
    }
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

// جلب بيانات القنوات عثمان
function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    return json_decode($res, true);
}

$cloud = getCloudFullData($BIN_ID, $API_KEY_BIN);
$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : [];
$active_sections = array_filter($cloud['sections'] ?: [], function($s) { return $s['status'] == 'show'; });

// جلب المباريات
$all_fixtures = getTodayMatches($FOOTBALL_API_KEY, $cache_file, $cache_time);

// سنعرض أي مباراة تصلنا الآن يا عثمان لنتأكد أن الكود يعمل
$my_matches = array_slice($all_fixtures, 0, 20); 

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
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 230px; color: #e2e8f0; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; }
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.98); backdrop-filter: blur(20px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; padding: 5px 0; }
        .cat-item { min-width: 65px; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; text-align: center; cursor: pointer; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item span { font-size: 8px; font-weight: 900; display: block; margin-top: 4px; }
        .cat-item img { width: 26px; height: 26px; }
        .match-card { background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border); border-radius: 18px; padding: 18px 10px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; }
        .m-league { position: absolute; top: -10px; right: 15px; background: var(--sky); font-size: 9px; padding: 3px 12px; border-radius: 50px; font-weight: 900; }
        .m-team { flex: 1.2; text-align: center; font-size: 11px; font-weight: 900; }
        .m-team img { width: 32px; height: 32px; display: block; margin: 0 auto 8px; }
        .m-info { flex: 0.9; text-align: center; border-left: 1px solid rgba(255,255,255,0.08); border-right: 1px solid rgba(255,255,255,0.08); margin: 0 5px; }
        .m-score { font-size: 22px; font-weight: 900; letter-spacing: 2px; }
        .m-time { font-size: 14px; font-weight: 900; color: var(--sky); }
        .channel-section { display: none; padding: 15px; }
        .channel-section.active { display: block; }
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #050c14; z-index: 9999; display: flex; justify-content: center; align-items: center; transition: 0.5s; }
        .intro-hide { opacity: 0; visibility: hidden; }
    </style>
</head>
<body>
<div id="pro-intro"><h2 style="color:white;">جاري التحميل...</h2></div>
<div class="main-container">
    <div class="header-fixed">
        <div style="display:flex; justify-content:space-around; margin-bottom:15px; padding:0 10px;">
            <a href="#" style="color:white; text-decoration:none; font-size:10px; background:#25d366; padding:5px 10px; border-radius:20px;">واتساب</a>
            <a href="#" style="color:white; text-decoration:none; font-size:10px; background:#0088cc; padding:5px 10px; border-radius:20px;">تليجرام</a>
        </div>
        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches', this)"><img src="https://cdn-icons-png.flaticon.com/512/33/33736.png"><span>المباريات</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="section-matches" class="channel-section active">
        <div style="margin-bottom: 20px; font-weight: 900; font-size: 15px; color: var(--sky); border-right: 4px solid var(--sky); padding-right: 12px;">مباريات اليوم</div>
        <?php if(empty($my_matches)): ?>
            <div style="text-align:center; padding:50px; opacity:0.3;">لم يتم استلام أي مباريات. تأكد من إعدادات الـ API</div>
        <?php else: foreach($my_matches as $m): $status = $m['fixture']['status']['short']; ?>
            <div class="match-card">
                <div class="m-league"><?= $m['league']['name'] ?></div>
                <div class="m-team"><img src="<?= $m['teams']['home']['logo'] ?>"><span><?= $m['teams']['home']['name'] ?></span></div>
                <div class="m-info">
                    <?php if(in_array($status, ['1H','2H','HT','ET','P'])): ?>
                        <div class="m-score" style="color:var(--main)"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div><div style="color:#22c55e; font-size:9px;">مباشر</div>
                    <?php elseif($status == 'FT'): ?>
                        <div class="m-score"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div><div style="font-size:9px; opacity:0.5;">انتهت</div>
                    <?php else: ?>
                        <div class="m-time"><?= date("H:i", $m['fixture']['timestamp']) ?></div><div style="font-size:9px; opacity:0.5;">قريباً</div>
                    <?php endif; ?>
                </div>
                <div class="m-team"><img src="<?= $m['teams']['away']['logo'] ?>"><span><?= $m['teams']['away']['name'] ?></span></div>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
    <div id="section-<?= $s['key'] ?>" class="channel-section">
        <p style="text-align:center; opacity:0.5;">قسم القنوات المباشرة</p>
    </div>
    <?php endforeach; ?>
</div>

<script>
window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1000); });
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active'); element.classList.add('active');
}
</script>
</body>
</html>
