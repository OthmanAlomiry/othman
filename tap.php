<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// المفتاح الخاص بك عثمان
$API_KEY = 'ef02886bbd68ecb3bdfc630f4546eb97'; 

$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));
$display_date = date('Y / m / d', strtotime($date_get));

// قائمة الدوريات المحددة عثمان (الأسماء كما تأتي من الـ API)
$allowed_leagues = [
    'Premier League',           // الإنجليزي
    'La Liga',                 // الإسباني
    'Serie A',                 // الإيطالي
    'Bundesliga',              // الألماني
    'Ligue 1',                 // الفرنسي
    'UEFA Champions League',    // أبطال أوروبا
    'Saudi Pro League',         // السعودي
    'Egyptian Premier League',  // المصري
    'AFC Champions League',     // أبطال آسيا
    'AFC Champions League Two'  // آسيا 2
];

function getFixtures($date, $key) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://v3.football.api-sports.io/fixtures?date=$date",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["x-apisports-key: $key"],
        CURLOPT_TIMEOUT => 15
    ]);
    $response = curl_exec($curl);
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['response'] ?: [];
}

$fixtures = getFixtures($date_get, $API_KEY);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --border: rgba(255, 255, 255, 0.1); }
        body { margin: 0; padding: 10px; background-color: var(--bg); font-family: 'Tajawal', sans-serif; color: #fff; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; }
        .date-nav { display: flex; align-items: center; justify-content: space-between; background: var(--glass); border: 1px solid var(--border); padding: 12px; border-radius: 20px; margin-bottom: 20px; }
        .date-nav a { width: 35px; height: 35px; background: var(--main); color: #fff; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none; }
        .league-title { background: linear-gradient(90deg, var(--main), transparent); padding: 10px 15px; border-radius: 12px; font-size: 13px; font-weight: 900; margin: 25px 0 12px; border-right: 4px solid #fff; display: flex; align-items: center; gap: 10px; }
        .card { background: var(--glass); border: 1px solid var(--border); border-radius: 20px; padding: 15px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; }
        .team { flex: 1.2; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 8px; }
        .team img { width: 35px; height: 35px; object-fit: contain; }
        .team b { font-size: 10px; color: #eee; }
        .info { flex: 1; text-align: center; }
        .score { font-size: 22px; font-weight: 900; letter-spacing: 2px; }
        .time { font-size: 11px; color: #38bdf8; font-weight: bold; }
        .live { color: #e11d48; font-size: 10px; font-weight: 900; animation: blink 1s infinite; }
        @keyframes blink { 50% { opacity: 0.5; } }
    </style>
</head>
<body>
<div class="container">
    <div class="date-nav">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <h3><?= $display_date ?></h3>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <?php 
    $found = false;
    $grouped = [];
    foreach($fixtures as $f) {
        // فحص إذا كان الدوري ضمن القائمة المسموحة عثمان
        if (in_array($f['league']['name'], $allowed_leagues)) {
            $grouped[$f['league']['name']][] = $f;
            $found = true;
        }
    }

    if(!$found): ?>
        <div style="text-align:center; padding:80px; opacity:0.4;">لا توجد مباريات للدوريات المحددة اليوم</div>
    <?php else: 
        foreach($grouped as $leagueName => $matches):
    ?>
        <div class="league-title"><i class="fas fa-trophy"></i> <?= $leagueName ?></div>
        <?php foreach($matches as $m): 
            $status = $m['fixture']['status']['short'];
            $mTime = date("H:i", $m['fixture']['timestamp']);
        ?>
        <div class="card">
            <div class="team">
                <img src="<?= $m['teams']['home']['logo'] ?>">
                <b><?= $m['teams']['home']['name'] ?></b>
            </div>
            <div class="info">
                <?php if(in_array($status, ['1H','2H','HT','ET','P'])): ?>
                    <div class="score" style="color:var(--main)"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div>
                    <div class="live">مباشر <?= $m['fixture']['status']['elapsed'] ?>'</div>
                <?php elseif($status == 'FT'): ?>
                    <div class="score"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div>
                    <div style="font-size:9px; opacity:0.5;">انتهت</div>
                <?php else: ?>
                    <div style="font-size:12px; opacity:0.2;">VS</div>
                    <div class="time"><?= $mTime ?></div>
                <?php endif; ?>
            </div>
            <div class="team">
                <img src="<?= $m['teams']['away']['logo'] ?>">
                <b><?= $m['teams']['away']['name'] ?></b>
            </div>
        </div>
        <?php endforeach; endforeach; endif; ?>
</div>
</body>
</html>
