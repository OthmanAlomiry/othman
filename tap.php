<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// المفتاح الخاص بك عثمان
$API_KEY = 'ef02886bbd68ecb3bdfc630f4546eb97'; 

$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));
$display_date = date('Y / m / d', strtotime($date_get));

// قائمة الدوريات المختارة مع أسمائها بالعربي عثمان
$allowed_leagues = [
    'Premier League' => 'الدوري الإنجليزي',
    'La Liga' => 'الدوري الإسباني',
    'Serie A' => 'الدوري الإيطالي',
    'Bundesliga' => 'الدوري الألماني',
    'Ligue 1' => 'الدوري الفرنسي',
    'UEFA Champions League' => 'دوري أبطال أوروبا',
    'Saudi Pro League' => 'الدوري السعودي',
    'Egyptian Premier League' => 'الدوري المصري',
    'AFC Champions League' => 'دوري أبطال آسيا',
    'AFC Champions League Two' => 'دوري أبطال آسيا 2'
];

function getFixtures($date, $key) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://v3.football.api-sports.io/fixtures?date=$date",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["x-apisports-key: $key"],
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);
    return $data['response'] ?: [];
}

$fixtures = getFixtures($date_get, $API_KEY);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>مباريات اليوم - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --border: rgba(255, 255, 255, 0.1); }
        body { margin: 0; padding: 10px; background-color: var(--bg); font-family: 'Tajawal', sans-serif; color: #fff; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; min-height: 100vh; }
        .date-nav { display: flex; align-items: center; justify-content: space-between; background: var(--glass); border: 1px solid var(--border); padding: 12px; border-radius: 20px; margin-bottom: 20px; backdrop-filter: blur(10px); }
        .date-nav a { width: 35px; height: 35px; background: var(--main); color: #fff; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none; box-shadow: 0 5px 15px rgba(225, 29, 72, 0.3); }
        .league-title { background: linear-gradient(90deg, var(--main), transparent); padding: 10px 15px; border-radius: 12px; font-size: 13px; font-weight: 900; margin: 25px 0 12px; border-right: 4px solid #fff; display: flex; align-items: center; gap: 10px; }
        .match-card { background: var(--glass); border: 1px solid var(--border); border-radius: 20px; padding: 15px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; backdrop-filter: blur(5px); }
        .team { flex: 1.2; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 8px; }
        .team img { width: 35px; height: 35px; object-fit: contain; }
        .team b { font-size: 10px; color: #eee; line-height: 1.2; }
        .info { flex: 1; text-align: center; display: flex; flex-direction: column; gap: 5px; }
        .score { font-size: 24px; font-weight: 900; color: #fff; letter-spacing: 2px; }
        .time { font-size: 11px; color: #38bdf8; font-weight: bold; background: rgba(56, 189, 248, 0.1); padding: 2px 10px; border-radius: 50px; display: inline-block; margin: 0 auto; }
        .live-tag { font-size: 9px; background: #e11d48; color: #fff; padding: 2px 8px; border-radius: 5px; animation: blink 1s infinite; font-weight: 900; width: fit-content; margin: 0 auto; }
        @keyframes blink { 50% { opacity: 0.5; } }
        .no-matches { text-align: center; padding: 80px 20px; opacity: 0.4; border: 1px dashed var(--border); border-radius: 20px; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <div class="date-nav">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <h3 style="margin:0; font-size:15px;"><?= $display_date ?></h3>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <?php 
    $grouped = [];
    foreach($fixtures as $f) {
        $leagueName = $f['league']['name'];
        if (array_key_exists($leagueName, $allowed_leagues)) {
            $grouped[$allowed_leagues[$leagueName]][] = $f;
        }
    }

    if(empty($grouped)): ?>
        <div class="no-matches">لا توجد مباريات للدوريات المختارة لهذا التاريخ</div>
    <?php else: 
        foreach($grouped as $leagueAr => $matches):
    ?>
        <div class="league-title"><i class="fas fa-trophy"></i> <?= $leagueAr ?></div>
        <?php foreach($matches as $m): 
            $status = $m['fixture']['status']['short'];
            $time = date("H:i", $m['fixture']['timestamp']);
        ?>
        <div class="match-card">
            <div class="team">
                <img src="<?= $m['teams']['home']['logo'] ?>">
                <b><?= $m['teams']['home']['name'] ?></b>
            </div>
            <div class="info">
                <?php if(in_array($status, ['1H', '2H', 'HT', 'ET', 'P'])): ?>
                    <div class="score" style="color:var(--main)"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div>
                    <div class="live-tag">مباشر <?= $m['fixture']['status']['elapsed'] ?>'</div>
                <?php elseif($status == 'FT'): ?>
                    <div class="score"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div>
                    <div style="font-size:9px; opacity:0.5;">انتهت</div>
                <?php else: ?>
                    <div style="font-size:12px; opacity:0.2;">VS</div>
                    <div class="time"><?= $time ?></div>
                <?php endif; ?>
            </div>
            <div class="team">
                <img src="<?= $m['teams']['away']['logo'] ?>">
                <b><?= $m['teams']['away']['name'] ?></b>
            </div>
        </div>
        <?php endforeach; endforeach; endif; ?>

    <footer style="text-align:center; padding:30px; font-size:10px; opacity:0.3;">الخدمة الرقمية © 2026</footer>
</div>

</body>
</html>
