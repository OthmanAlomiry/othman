<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// مفتاحك الخاص عثمان
$API_KEY = 'ef02886bbd68ecb3bdfc630f4546eb97'; 

$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));

// تعريف الدوريات بالـ ID الخاص بها لضمان الدقة عثمان
$my_leagues = [
    307 => 'الدوري السعودي',
    233 => 'الدوري المصري',
    2   => 'دوري أبطال أوروبا',
    3   => 'دوري أبطال آسيا',
    850 => 'دوري أبطال آسيا 2',
    39  => 'الدوري الإنجليزي',
    140 => 'الدوري الإسباني',
    135 => 'الدوري الإيطالي',
    78  => 'الدوري الألماني',
    61  => 'الدوري الفرنسي'
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

// فرز المباريات حسب قائمتنا عثمان
$ordered_matches = [];
foreach ($fixtures as $f) {
    $id = $f['league']['id'];
    if (isset($my_leagues[$id])) {
        $ordered_matches[$id][] = $f;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --card: rgba(255, 255, 255, 0.05); }
        body { background: var(--bg); color: #fff; font-family: 'Tajawal', sans-serif; margin: 0; padding: 15px; }
        .container { max-width: 500px; margin: auto; }
        .nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: var(--card); padding: 10px; border-radius: 15px; }
        .nav a { color: #fff; background: var(--main); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none; }
        .league-row { background: linear-gradient(90deg, var(--main), transparent); padding: 10px; border-radius: 8px; margin: 20px 0 10px; font-weight: 900; font-size: 14px; border-right: 4px solid #fff; }
        .match { background: var(--card); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 15px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; }
        .team { flex: 1; text-align: center; font-size: 11px; }
        .team img { width: 30px; height: 30px; display: block; margin: 0 auto 5px; }
        .center { flex: 1; text-align: center; }
        .score { font-size: 22px; font-weight: 900; }
        .time { font-size: 11px; color: #38bdf8; background: rgba(56,189,248,0.1); padding: 2px 8px; border-radius: 10px; }
        .live { color: #22c55e; font-size: 10px; font-weight: 900; animation: blink 1s infinite; }
        @keyframes blink { 50% { opacity: 0.5; } }
    </style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <span><?= date('Y / m / d', strtotime($date_get)) ?></span>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <?php if(empty($ordered_matches)): ?>
        <p style="text-align:center; opacity:0.5;">لا توجد مباريات هامة اليوم</p>
    <?php else: 
        foreach($my_leagues as $id => $name): 
            if(isset($ordered_matches[$id])):
    ?>
        <div class="league-row"><?= $name ?></div>
        <?php foreach($ordered_matches[$id] as $m): 
            $status = $m['fixture']['status']['short'];
            $mTime = date("H:i", $m['fixture']['timestamp']);
        ?>
        <div class="match">
            <div class="team">
                <img src="<?= $m['teams']['home']['logo'] ?>">
                <b><?= $m['teams']['home']['name'] ?></b>
            </div>
            <div class="center">
                <?php if(in_array($status, ['1H','2H','HT','ET','P'])): ?>
                    <div class="score" style="color:var(--main)"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div>
                    <div class="live">مباشر</div>
                <?php elseif($status == 'FT'): ?>
                    <div class="score"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div>
                    <div style="font-size:9px; opacity:0.5;">انتهت</div>
                <?php else: ?>
                    <div style="font-size:11px; opacity:0.2;">VS</div>
                    <div class="time"><?= $mTime ?></div>
                <?php endif; ?>
            </div>
            <div class="team">
                <img src="<?= $m['teams']['away']['logo'] ?>">
                <b><?= $m['teams']['away']['name'] ?></b>
            </div>
        </div>
    <?php endif; endforeach; endforeach; endif; ?>
</div>
</body>
</html>
